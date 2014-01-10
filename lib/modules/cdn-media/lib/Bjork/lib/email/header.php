<?php

namespace email\header;

use email\charset\Charset;

const NL = "\n";
const SPACE = ' ';
const SPACE8 = '        ';
const EMPTYSTRING = '';

const MAXLINELEN = 76;

const EMBEDEDHEADER_RE = '/\n[^ \t]+:/';

const USASCII = 'us-ascii';
const UTF8 = 'utf-8';

function get_charset($charset) {
    static $charsets = array();
    if (!array_key_exists($charset, $charsets)) {
        $c = new Charset($charset);
        $charsets[$charset] = $c;
    }
    return $charsets[$charset];
}

class Header {
    
    protected
        $charset,
        $continuation_ws,
        $chunks,
        $firstlinelen,
        $maxlinelen;
    
    function __construct($s=null, $charset=null, $maxlinelen=null,
                         $header_name=null, $continuation_ws=' ')
    {
        if (null === $charset)
            $charset = get_charset(USASCII);
        if (!($charset instanceof Charset))
            $charset = new Charset($charset);
        $this->charset = $charset;
        $this->continuation_ws = $continuation_ws;
        $cws_expanded_len = mb_strlen(str_replace("\t", SPACE8, $continuation_ws));
        $this->chunks = array();
        if (null !== $s)
            $this->append($s, $charset);
        if (null === $maxlinelen)
            $maxlinelen = MAXLINELEN;
        if (null === $header_name)
            $this->firstlinelen = $maxlinelen;
        else
            $this->firstlinelen = $maxlinelen - mb_strlen($header_name) - 2;
        $this->maxlinelen = $maxlinelen - $cws_expanded_len;
    }
    
    function __toString() {
        return $this->encode();
    }
    
    public function append($s, $charset) {
        if (null === $charset)
            $charset = $this->charset;
        else if (!($charset instanceof Charset))
            $charset = new Charset($charset);
        if (strval($charset) != '8bit') {
            $valid = false;
            foreach (array(get_charset(USASCII), $charset, get_charset(UTF8)) as $charset) {
                $outcodec = $charset->getOutputCodec() ?: 'us-ascii';
                if (mb_check_encoding($s, $outcodec)) {
                    $s = mb_convert_encoding($s, $outcodec);
                    $valid = true;
                    break;
                }
            }
            if (!$valid)
                throw new \Exception('string could not be converted to output charset');
        }
        
        $this->chunks[] = array($s, $charset);
    }
    
    public function encode() {
        $chunks = array();
        
        foreach ($this->chunks as $chunk) {
            list($header, $charset) = $chunk;
            if (!$header)
                continue;
            if (null === $charset || null === $charset->getHeaderEncoding())
                $s = $header;
            else
                $s = $charset->headerEncode($header);
            $chunks[] = $s;
        }
        
        $value = implode(NL . $this->continuation_ws, $chunks);
        if (preg_match(EMBEDEDHEADER_RE, $value))
            throw new \Exception('header value appears to contain an embedded header');
        
        return $value;
    }
}
