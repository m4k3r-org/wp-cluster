<?php

namespace email\generator;

use email\header\Header;

const UNDERSCORE = '_';
const NL = "\n";
const FCRE = '/^From /m';

function make_boundary($text=null) {
    static $_maxint = null, $_width, $_fmt;
    
    if ($_maxint === null) {
        $_maxint = PHP_INT_MAX;
        $_width = strlen($_maxint-1);
        $_fmt = "%0{$_width}d";
    }
    
    $token = mt_rand();
    $boundary = str_repeat('=', 15) . sprintf($_fmt, $token) . '==';
    if (null === $text)
        return $boundary;
    $b = $boundary;
    $counter = 0;
    while (true) {
        $cre = '/^--'.preg_quote($b).'(--)?$/m';
        if (!preg_match($cre, $text))
            break;
        $b = "{$boundary}.{$counter}";
        $counter++;
    }
    return $b;
}

class Generator {
    
    protected
        $fp,
        $mangle_from,
        $maxheaderlen;
    
    function __construct(&$outfp, $mangle_from=true, $maxheaderlen=78) {
        $this->fp =& $outfp;
        $this->mangle_from = $mangle_from;
        $this->maxheaderlen = $maxheaderlen;
    }
    
    function getClone(&$fp) {
        return new static($fp, $this->mangle_from, $this->maxheaderlen);
    }
    
    public function write($s) {
        $this->fp .= strval($s);
    }
    
    public function flatten($msg, $unixfrom=false) {
        if ($unixfrom) {
            // @todo
        }
        $this->_write($msg);
    }
    
    function _write($msg) {
        $oldfp =& $this->fp;
        $sfp = '';
        $this->fp =& $sfp;
        $this->_dispatch($msg);
        $this->fp =& $oldfp;
        
        $meth = '_write_headers';
        if (method_exists($msg, $meth))
            $msg->$meth($this);
        else
            $this->_write_headers($msg);
        $this->write($sfp);
    }
    
    function _dispatch($msg) {
        $main = $msg->getContentMainType();
        $sub = $msg->getContentSubType();
        $specific = str_replace('-', '_', $main . UNDERSCORE . $sub);
        $meth = '_handle_' . $specific;
        if (!method_exists($this, $meth)) {
            $generic = str_replace('-', '_', $main);
            $meth = '_handle_' . $generic;
            if (!method_exists($this, $meth))
                $meth = '_write_body';
        }
        $this->$meth($msg);
    }
    
    function _write_headers($msg) {
        foreach ($msg->items() as $pair) {
            list($h, $v) = $pair;
            $this->write("{$h}: ");
            if ($this->maxheaderlen === 0)
                // Explicit no-wrapping
                $this->write(strval($v) . NL);
            else if ($v instanceof Header)
                $this->write($v->encode() . NL);
            // @TODO
            // else if (is8bitstring($v))
            //     $this->write($v . NL);
            else {
                $header = new Header($v, null, $this->maxheaderlen, $h);
                $this->write($header->encode() . NL);
            }
        }
        $this->write(NL);
    }
    
    function _write_body($msg) {
        $this->_handle_text($msg);
    }
    
    function _handle_text($msg) {
        $payload = $msg->getPayload();
        if (null === $payload)
            return;
        if (!is_string($payload))
            throw new \Exception('string payload expected: '.gettype($payload));
        if ($this->mangle_from) {
            // todo
        }
        $this->write($payload);
    }
    
    function _handle_multipart($msg) {
        $msgtexts = array();
        $subparts = $msg->getPayload();
        if (null === $subparts)
            $subparts = array();
        else if (is_string($subparts)) {
            $this->write($subparts);
            return;
        } else if (!is_array($subparts)) {
            $subparts = array($subparts);
        }
        
        foreach ($subparts as $part) {
            $s = '';
            $g = $this->getClone($s);
            $g->flatten($part, false);
            $msgtexts[] = $s;
        }
        $boundary = $msg->getBoundary();
        if (!$boundary) {
            $alltext = implode(NL, $msgtexts);
            $boundary = make_boundary($alltext);
            $msg->setBoundary($boundary);
        }
        
        // @todo: if (msg->getPreample()) ...
        
        $this->write('--' . $boundary . NL);
        
        if ($msgtexts) {
            $t = array_shift($msgtexts);
            $this->write($t);
        }
        
        foreach ($msgtexts as $body_part) {
            $this->write(NL . '--' . $boundary . NL);
            $this->write($body_part);
        }
        $this->write(NL . '--' . $boundary . '--' . NL);
    }
}
