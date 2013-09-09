<?php

namespace bjork\contrib\messages\storage\base;

use bjork\conf\settings,
    bjork\contrib\messages;

/**
* Represents an actual message that can be stored in any of the supported
* storage classes (typically session- or cookie-based) and rendered in a view
* or template.
*/
class Message {
    
    protected $level, $message, $extra_tags;
    
    function __construct($level, $message, $extra_tags=null) {
        $this->level = intval($level);
        $this->message = $message;
        $this->extra_tags = $extra_tags;
    }
    
    function __toString() {
        return $this->getMessage();
    }
    
    public function isEqualTo($other) {
        return $other instanceof Message &&
            $this->getLevel() === $other->getLevel() &&
            $this->getMessage() === $other->getMessage();
    }
    
    public function getLevel() {
        return $this->level;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getTags() {
        $level_tags = messages::get_level_tags();
        $label_tag = $level_tags->get($this->getLevel(), '');
        $extra_tags = $this->extra_tags;
        if ($extra_tags && $level_tags)
            return "{$extra_tags} {$label_tag}";
        else if ($extra_tags)
            return $extra_tags;
        else if ($label_tag)
            return $label_tag;
        return '';
    }
    
    /**
    * Prepares the message for serialization by forcing the ``message``
    * and ``extra_tags`` to unicode in case they are lazy translations.
    * 
    * Known "safe" types (None, int, etc.) are not converted (see Django's
    * ``force_unicode`` implementation for details).
    */
    function prepare() {
        $this->message = strval($this->message);
        $this->extra_tags = strval($this->extra_tags);
    }
}

/**
* This is the base backend for temporary message storage.
* 
* This is not a complete class; to be a usable storage backend, it must be
* subclassed and the two methods ``_get`` and ``_store`` overridden.
*/
abstract class BaseStorage implements \Countable, \IteratorAggregate {
    
    protected
        $request,
        $loaded_data,
        $queued_messages,
        $used,
        $added_new,
        $level;
    
    function __construct($request, array $options=null) {
        if (null === $options)
            $options = array();
        $this->request = $request;
        $this->loaded_data = null;
        $this->queued_messages = null;
        $this->used = false;
        $this->added_new = false;
        $this->level = null;
    }
    
    function count() {
        return count($this->getLoadedMessages()) +
               count($this->getQueuedMessages());
    }
    
    function getIterator() {
        $this->used = true;
        $queued_messages = $this->getQueuedMessages();
        if (!empty($queued_messages)) {
            $this->setLoadedMessages(array_merge(
                $this->getLoadedMessages(),
                $queued_messages));
            $this->resetQueuedMessages();
        }
        return new \ArrayIterator($this->getLoadedMessages());
    }
    
    public function contains(Message $message) {
        foreach (array_merge(
                $this->getLoadedMessages(),
                $this->getQueuedMessages()) as $msg) {
            if ($msg->isEqualTo($message))
                return true;
        }
        return false;
    }
    
    /**
    * Returns a list of loaded messages, retrieving them first if they have
    * not been loaded yet.
    */
    protected function getLoadedMessages() {
        if (null === $this->loaded_data) {
            list($messages, $all_retrieved) = $this->get();
            $this->loaded_data = empty($messages) ? array() : $messages;
        }
        return $this->loaded_data;
    }
    
    private function setLoadedMessages(array $messages) {
        $this->loaded_data = $messages;
    }
    
    protected function getQueuedMessages() {
        if (null === $this->queued_messages)
            $this->queued_messages = array();
        return $this->queued_messages;
    }
    
    private function resetQueuedMessages() {
        $this->queued_messages = null;
    }
    
    private function queueMessage(Message $message) {
        if (null === $this->queued_messages)
            $this->queued_messages = array();
        $this->queued_messages[] = $message;
    }
    
    /**
    * Retrieves a list of stored messages. Returns a tuple of the messages
    * and a flag indicating whether or not all the messages originally
    * intended to be stored in this storage were, in fact, stored and
    * retrieved; e.g., ``array($messages, $all_retrieved)``.
    * 
    * If it is possible to tell if the backend was not used (as opposed to
    * just containing no messages) then ``None`` should be returned in
    * place of ``messages``.
    */
    abstract protected function get();
    
    /**
    * Stores a list of messages, returning a list of any messages which could
    * not be stored.
    * 
    * One type of object must be able to be stored, ``Message``.
    */
    abstract protected function store(array $messages, $response, array $options=null);
    
    /**
    * Prepares a list of messages for storage.
    */
    protected function prepareMessages(array $messages) {
        foreach ($messages as $msg) 
            $msg->prepare();
    }
    
    /**
    * Stores all unread messages.
    * 
    * If the backend has yet to be iterated, previously stored messages will
    * be stored again. Otherwise, only messages added after the last
    * iteration will be stored.
    */
    public function update($response) {
        $this->prepareMessages($this->getQueuedMessages());
        if ($this->used)
            return $this->store($this->getQueuedMessages(), $response);
        else if ($this->added_new) {
            $messages = array_merge($this->getLoadedMessages(),
                                    $this->getQueuedMessages());
            return $this->store($messages, $response);
        }
    }
    
    /**
    * Queues a message to be stored.
    *
    * The message is only queued if it contained something and its level is
    * not less than the recording level (``self.level``).
    */
    public function add($level, $message, $extra_tags='') {
        if (empty($message))
            return;
        // Check that the message level is not less than the recording level.
        if ($level < $this->getLevel())
            return;
        // Add the message.
        $this->added_new = true;
        $message = new Message($level, $message, $extra_tags);
        $this->queueMessage($message);
    }
    
    /**
    * Returns the minimum recorded level.
    *
    * The default level is the ``MESSAGE_LEVEL`` setting. If this is
    * not found, the ``INFO`` level is used.
    */
    public function getLevel() {
        if (null === $this->level)
            $this->level = settings::get('MESSAGE_LEVEL', messages::INFO);
        return $this->level;
    }
    
    /**
    * Sets a custom minimum recorded level.
    *
    * If set to ``None``, the default level will be used (see the
    * ``_get_level`` method).
    */
    public function setLevel($value=null) {
        $this->level = $value;
    }
}
