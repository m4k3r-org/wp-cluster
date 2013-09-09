<?php

namespace bjork\contrib\sessions\backends\base;

use strutils;

use bjork\conf\settings,
    bjork\core\exceptions\SuspiciousOperation,
    bjork\utils\crypto,
    bjork\utils\http;

/**
* Used internally as a consistent exception type to catch from save.
* (see the docstring for SessionBase.save() for details).
*/
class CreateError extends \Exception {}

/**
* Base class for all Session classes.
*/
abstract class SessionBase implements \ArrayAccess {
    const MAX_SESSION_KEY = 18446744073709551616; // 2<<63
    const TEST_COOKIE_NAME = 'testcookie';
    const TEST_COOKIE_VALUE = 'worked';
    const SESSION_LOADED_SENTINEL = -987654321;
    
    protected
        $session_key,
        $session_cache,
        $accessed,
        $modified;
    
    function __construct($session_key=null) {
        $this->session_cache = self::SESSION_LOADED_SENTINEL;
        $this->session_key = $session_key;
        $this->accessed = false;
        $this->modified = false;
    }
    
    //-- ArrayAccess interface ----------------------------------------------
    
    function offsetExists($key) {
        return array_key_exists($key, $this->getSessionData());
    }
    
    function offsetGet($key) {
        $data = $this->getSessionData();
        if (!array_key_exists($key, $data))
            throw new \OutOfBoundsException($key);
        return $data[$key];
    }
    
    function offsetSet($key, $value) {
        $data = $this->getSessionData();
        $data[$key] = $value;
        $this->setSessionData($data);
        $this->modified = true;
    }
    
    function offsetUnset($key) {
        $data = $this->getSessionData();
        unset($data[$key]);
        $this->setSessionData($data);
        $this->modified = true;
    }
    
    //-- Public API ---------------------------------------------------------
    
    public function get($key, $default=null) {
        if ($this->offsetExists($key))
            return $this->offsetGet($key);
        return $default;
    }
    
    public function set($key, $value) {
        $this->offsetSet($key, $value);
    }
    
    public function setDefault($key, $value) {
        if ($this->offsetExists($key))
            return $this->offsetGet($key);
        $this->offsetSet($key, $value);
        return $value;
    }
    
    public function hasKey($key) {
        return $this->offsetExists($key);
    }
    
    public function update(array $dict) {
        // we don't iterate over the $dict and offsetSet()
        // as this could be costly on some backends
        $data = array_merge($this->getSessionData(), $dict);
        $this->setSessionData($data);
        $this->modified = true;
    }
    
    public function pop($key /*, $default */) {
        $args = func_get_args();
        $data = $this->getSessionData();
        $this->modified = $this->modified || isset($data[$key]);
        if (isset($data[$key])) {
            unset($data[$key]);
            $this->setSessionData($data);
        } else {
            if (count($args) > 1)
                return $args[1];
            throw new \Exception("".__CLASS__." object has no property '{$k}'");
        }
    }
    
    public function clear() {
        // To avoid unnecessary persistent storage accesses, we set up the
        // internals directly (loading data wastes time, since we are going
        // to set it to an empty dict anyway).
        $this->session_cache = array();
        $this->accessed = true;
        $this->modified = true;
    }
    
    /**
    * Removes the current session data from the database and regenerates
    * the key.
    */
    public function flush() {
        $this->clear();
        $this->delete();
        $this->create();
    }
    
    /**
    * Creates a new session key, whilst retaining the current session data.
    */
    public function cycleKey() {
        $data = $this->session_cache;
        $key = $this->session_key;
        $this->create();
        $this->session_cache = $data;
        $this->delete($key);
    }
    
    public function isAccessed() {
        return $this->accessed;
    }
    
    public function isModified() {
        return $this->modified;
    }
    
    /**
    * Get the session's expiry date (as a DateTime object).
    */
    public function getExpiryDate() {
        $expiry = $this->get('_session_expiry');
        if ($expiry instanceof \DateTime)
            return $expiry;
        if (!$expiry) // checks both null and 0 cases
            $expiry = settings::get('SESSION_COOKIE_AGE');
        $now = new \DateTime();
        return $now->add(\DateInterval::createFromDateString($expiry.' seconds'));
    }
    
    /**
    * Get the number of seconds until the session expires.
    */
    public function getExpiryAge() {
        $expiry = $this->get('_session_expiry');
        if (!$expiry) // checks both null and 0 cases
            return settings::get('SESSION_COOKIE_AGE');
        if (!($expiry instanceof \DateTime))
            return $expiry;
        $delta = $expiry->diff(new \DateTime());
        return $delta->days * 86400 + $delta->h * 24 + $delta->m * 60 + $delta->s;
    }
    
    /**
    * Sets a custom expiration for the session. ``value`` can be an integer,
    * a ``DateTime`` or ``DateInterval`` object or ``null``.
    *
    * If ``value`` is an integer, the session will expire after that many
    * seconds of inactivity. If set to ``0`` then the session will expire on
    * browser close.
    *
    * If ``value`` is a ``DateTime`` or ``DateInterval`` object, the session
    * will expire at that specific future time.
    *
    * If ``value`` is ``null``, the session uses the global session expiry
    * policy.
    */
    public function setExpiry($value) {
        if (null === $value) {
            // Remove any custom expiration for this session.
            $this->offsetUnset('_session_expiry');
            return;
        }
        if ($value instanceof \DateInterval) {
            $now = new \DateTime();
            $value = $now->add($value);
        } else if (is_int($value)) { // timestamp
            $value = new \DateTime('@' . strval($value));
        }
        $this->offsetSet('_session_expiry', $value);
    }
    
    /**
    * Returns ``true`` if the session is set to expire when the browser
    * closes, and ``false`` if there's an expiry date. Use ``getExpiryDate()``
    * or ``getExpiryAge()`` to find the actual expiry date/age, if there
    * is one.
    */
    public function expiresAtBrowserClose() {
        if (null === $this->get('_session_expiry'))
            return settings::get('SESSION_EXPIRE_AT_BROWSER_CLOSE');
        return $this->get('_session_expiry') === 0;
    }
    
    public function getSessionKey() {
        if (!empty($this->session_key))
            return $this->session_key;
        $this->session_key = $this->getNewSessionKey();
        return $this->session_key;
    }
    
    public function setSessionKey($key) {
        $this->session_key = $key;
    }
    
    //-- Test cookie --------------------------------------------------------
    
    public function setTestCookie() {
        $this->offsetSet(self::TEST_COOKIE_NAME, self::TEST_COOKIE_VALUE);
    }
    
    public function testCookieWorked() {
        return $this->get(self::TEST_COOKIE_NAME) === self::TEST_COOKIE_VALUE;
    }
    
    public function deleteTestCookie() {
        $this->offsetUnset(self::TEST_COOKIE_NAME);
    }
    
    //-- Methods subclasses can override ------------------------------------
    
    /**
    * Lazily loads session from storage (unless "no_load" is True, when only
    * an empty dict is stored) and stores it in the current instance.
    */
    protected function getSessionData($no_load=false) {
        $this->accessed = true;
        if ($this->session_cache === self::SESSION_LOADED_SENTINEL) {
            if (null === $this->session_key || $no_load)
                $this->session_cache = array();
            else
                $this->session_cache = $this->load();
        }
        return $this->session_cache;
    }
    
    private function setSessionData(array $data) {
        $this->session_cache = $data;
    }
    
    /**
    * Returns a session key that isn't being used.
    */
    protected function getNewSessionKey() {
        $pid = getmypid();
        while (true) {
            $session_key = md5(sprintf('%s%s%s%s',
                mt_rand(0, self::MAX_SESSION_KEY - 1),
                $pid,
                time(),
                settings::get('SECRET_KEY')
            ));
            if (!$this->exists($session_key))
                break;
        }
        return $session_key;
    }
    
    protected function hash($value) {
        $key_salt = 'bjork.contrib.sessions.' . get_called_class();
        return crypto::salted_hmac($key_salt, $value);
    }
    
    /**
    * Returns the given session dictionary pickled and encoded as a string.
    */
    protected function encode(array $session_dict) {
        $serialized = serialize($session_dict);
        $hash = $this->hash($serialized);
        return http::b64encode($hash . ':' . $serialized);
    }
    
    protected function decode($session_data) {
        $encoded_data = http::b64decode($session_data);
        try {
            list($hash, $serialized) = strutils::split($encoded_data, ':', 1);
            $expected_hash = $this->hash($serialized);
            if (!crypto::constant_time_compare($hash, $expected_hash))
                throw new SuspiciousOperation('Session data corrupted');
            return unserialize($serialized);
        } catch (\Exception $e) {
            // \BjorkException while unserializing, SuspiciousOperation.
            // We catch these and return an empty array.
            return array();
        }
    }
    
    //-- Methods subclasses must implement -----------------------------------
    
    /**
    * Returns True if the given session_key already exists.
    */
    abstract function exists($session_key);
    
    /**
    * Creates a new session instance. Guaranteed to create a new object with
    * a unique key and will have saved the result once (with empty data)
    * before the method returns.
    */
    abstract function create();
    
    /**
    * Saves the session data. If 'must_create' is True, a new session object
    * is created (otherwise a CreateError exception is raised). Otherwise,
    * save() can update an existing object with the same key.
    */
    abstract function save($must_create=false);
    
    /**
    * Deletes the session data under this key. If the key is None, the
    * current session key value is used.
    */
    abstract function delete($session_key=null);
    
    /**
    * Loads the session data and returns a dictionary.
    */
    abstract function load();
}
