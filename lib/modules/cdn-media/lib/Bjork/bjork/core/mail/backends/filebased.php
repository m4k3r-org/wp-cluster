<?php

namespace bjork\core\mail\backends\filebased;

use strutils;

use bjork\conf\settings,
    bjork\core\exceptions\ImproperlyConfigured,
    bjork\core\mail\backends\streambased\EmailBackend as BaseEmailBackend;

function get_random_number($length=12) {
    $str = '';
    $chars = strutils::explode('0123456789');
    foreach (range(1, $length) as $i)
        $str .= $chars[array_rand($chars, 1)];
    return $str;
}

/**
* Email backend that writes messages to a file.
*/
class EmailBackend extends BaseEmailBackend {
    
    var $file_path,
        $file_name;
    
    function __construct(array $options=null, $fail_silently=false) {
        if (!$options)
            $options = array();
        
        $this->file_name = null;
        
        $this->file_path = isset($options['file_path'])
            ? $options['file_path']
            : settings::get('EMAIL_FILE_PATH', null);
        
        // Make sure that self.file_path is a directory if it exists.
        if (@file_exists($this->file_path) && !is_dir($this->file_path)) {
            throw new ImproperlyConfigured(sprintf(
                'Path for saving email messages exists, but is not a directory: %s',
                $this->file_path));
        // Try to create it, if it doesn't exist.
        } else if (!(@file_exists($this->file_path))) {
            try {
                mkdir($this->file_path, 0777, true);
            } catch (\ErrorException $e) {
                throw new ImproperlyConfigured(sprintf(
                    'Could not create directory for saving email messages: %s (%s)',
                    $this->file_path, $e->getMessage()));
            }
        }
        
        // can't canonicalise the path earlier as it might contain
        // a non-existent component
        $this->file_path = realpath($this->file_path);
        
        // Make sure that self.file_path is writable.
        if (!(@is_writable($this->file_path)))
            throw new ImproperlyConfigured(sprintf(
                'Could not write to directory: %s',
                $this->file_path));
        
        // Since we're using the stream-based backend as a base,
        // force the stream to be None, so we don't default to stdout
        $options['stream'] = null;
        parent::__construct($options, $fail_silently);
    }
    
    function open() {
        if (null === $this->stream) {
            $this->stream = fopen($this->getFilename(), 'ab');
            return true;
        }
        return false;
    }
    
    function close() {
        if (null !== $this->stream)
            fclose($this->stream);
        $this->stream = null;
    }
    
    function lock() {
        if (null !== $this->stream)
            flock($this->stream, \LOCK_EX);
    }
    
    function unlock() {
        if (null !== $this->stream)
            flock($this->stream, \LOCK_UN);
    }
    
    // Return a unique file name.
    function getFilename() {
        if (null === $this->file_name) {
            $ts = \DateTime::createFromFormat('U', time())->format('Ymd-His');
            $id = get_random_number();
            $fn = "{$ts}-{$id}.log";
            $this->file_name = $this->file_path . DIRECTORY_SEPARATOR . $fn;
        }
        return $this->file_name;
    }
}
