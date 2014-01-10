<?php

namespace bjork\utils\paginator;

class InvalidPage extends \Exception {}
class PageNotAnInteger extends InvalidPage {}
class EmptyPage extends InvalidPage {}

class Paginator {
    
    protected
        $_allow_empty_first_page,
        $_object_list_orig,
        $_object_list,
        $_per_page,
        $_num_pages,
        $_orphans,
        $_count;
    
    function __construct($object_list, $per_page, $orphans=0, $allow_empty_first_page=true) {
        $this->_object_list_orig = $object_list;
        $this->_object_list = null;
        $this->_per_page = $per_page;
        $this->_orphans = $orphans;
        $this->_allow_empty_first_page = $allow_empty_first_page;
        $this->_num_pages = null;
        $this->_count = null;
    }
    
    public function getObjects() {
        return $this->_object_list_orig;
    }
    
    public function getObjectList() {
        if (is_null($this->_object_list))
            $this->_object_list = (array)$this->_object_list_orig;
        return $this->_object_list;
    }
    
    public function getObjectsPerPage() {
        return $this->_per_page;
    }
    
    public function getAllowedOrphansCount() {
        return $this->_orphans;
    }
    
    public function isEmptyFirstPageAllowed() {
        return $this->_allow_empty_first_page;
    }
    
    /**
    * Returns a Page object for the given 1-based page number.
    */
    public function getPage($number) {
        $number = $this->validateNumber($number);
        $bottom = ($number - 1) * $this->_per_page;
        $top = $bottom + $this->_per_page;
        if ($top + $this->_orphans >= $this->getObjectCount())
            $top = $this->getObjectCount();
        $object_array = $this->getObjectList();
        return new Page(array_slice($object_array, $bottom, $top - $bottom), $number, $this);
    }
    
    /**
    * Returns the total number of objects, across all pages.
    */
    public function getObjectCount() {
        if (is_null($this->_count)) {
            if (is_object($this->_object_list_orig) &&
                method_exists($this->_object_list_orig, 'count'))
            {
                $this->_count = $this->_object_list_orig->count();
            } else {
                $this->_count = count($this->getObjectList());
            }
        }
        return $this->_count;
    }
    
    /**
    * Returns the total number of pages.
    */
    public function getPageCount() {
        if (is_null($this->_num_pages)) {
            if ($this->getObjectCount() === 0 && !$this->isEmptyFirstPageAllowed())
                $this->_num_pages = 0;
            else {
                $hits = max(1, $this->getObjectCount() - $this->_orphans);
                $this->_num_pages = intval(ceil($hits / intval($this->_per_page)));
            }
        }
        return $this->_num_pages;
    }
    
    /**
    * Returns a 1-based range of pages for iterating through within
    * a template for loop.
    */
    public function getPageRange() {
        return range(1, $this->getPageCount());
    }
    
    /**
    * Validates the given 1-based page number.
    */
    protected function validateNumber($number) {
        if (!is_integer($number))
            throw new PageNotAnInteger('That page number is not an integer');
        if ($number < 1)
            throw new EmptyPage('That page number is less than 1');
        if ($number > $this->getPageCount()) {
            if ($number === 1 && $this->isEmptyFirstPageAllowed()) {
                // pass
            } else {
                throw new EmptyPage('That page contains no results');
            }
        }
        return $number;
    }
}

class Page implements \IteratorAggregate, \Countable, \ArrayAccess {
    
    protected
        $_object_list_orig,
        $_object_list,
        $_number,
        $_paginator;
    
    public function __construct($object_list, $number, $paginator) {
        $this->_object_list_orig = $object_list;
        $this->_object_list = null;
        $this->_number = $number;
        $this->_paginator = $paginator;
    }
    
    function __toString() {
        return sprintf('<Page %s of %s>',
            $this->_number,
            $this->_paginator->getPageCount());
    }
    
    public function getObjects() {
        return $this->_object_list_orig;
    }
    
    public function getObjectList() {
        if (is_null($this->_object_list))
            $this->_object_list = (array)$this->_object_list_orig;
        return $this->_object_list;
    }
    
    public function getIterator() {
        if (is_object($this->_object_list_orig) &&
            method_exists($this->_object_list_orig, 'getIterator'))
        {
            return $this->_object_list_orig->getIterator();
        } else {
            return new \ArrayIterator($this->getObjectList());
        }
    }
    
    public function count() {
        if (is_object($this->_object_list_orig) &&
            method_exists($this->_object_list_orig, 'count'))
        {
            return $this->_object_list_orig->count();
        } else {
            return count($this->getObjectList());
        }
    }
    
    public function offsetExists($offset) {
        $list = $this->getObjectList();
        return isset($list[$offset]);
    }
    
    public function offsetGet($offset) {
        $list = $this->getObjectList();
        return $list[$offset];
    }
    
    public function offsetSet($offset, $value) {
        throw new \Exception(__CLASS__ . ' object is immutable');
    }
    
    public function offsetUnset($offset) {
        throw new \Exception(__CLASS__ . ' object is immutable');
    }
    
    function hasNext() {
        return $this->_number < $this->_paginator->getPageCount();
    }
    
    function hasPrevious() {
        return $this->_number > 1;
    }
    
    function hasOtherPages() {
        return $this->hasPrevious() || $this->hasNext();
    }
    
    function getPageNumber() {
        return $this->_number;
    }
    
    function getNextPageNumber() {
        return $this->_number + 1;
    }
    
    function getPreviousPageNumber() {
        return $this->_number - 1;
    }
    
    /**
    * Returns the 1-based index of the first object on this page,
    * relative to total objects in the paginator.
    */
    function getStartIndex() {
        // Special case, return zero if no items.
        if ($this->_paginator->getObjectCount() === 0)
            return 0;
        return ($this->_paginator->getObjectsPerPage() * ($this->_number - 1)) + 1;
    }
    
    /**
    * Returns the 1-based index of the last object on this page,
    * relative to total objects found (hits).
    */
    function getEndIndex() {
        // Special case for the last page because there can be orphans.
        if ($this->_number == $this->_paginator->getPageCount())
            return $this->_paginator->getObjectCount();
        return $this->_number * $this->_paginator->getObjectsPerPage();
    }
}
