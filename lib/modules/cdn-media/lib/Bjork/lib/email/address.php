<?php

namespace email\address;

function len($str) { return mb_strlen($str); }
function index($str, $i) { return mb_substr($str, $i, 1); }
function instring($haystack, $needle) { return false !== mb_strpos($haystack, $needle); }

class AddressParser {
    
    const SPECIALS = '()<>@,:;."[]';
    const LWS = " \t";
    const CR = "\r\n";
    
    protected
        $pos,
        $atomends,
        $phraseends,
        $field,
        $commentlist;
    
    function __construct($field) {
        // `field' is an unparsed address header field, containing
        // one or more addresses.
        $this->pos = 0;
        $this->atomends = self::SPECIALS . self::LWS . self::CR;
        $this->phraseends = str_replace('.', '', $this->atomends);
        $this->field = $field;
        $this->commentlist = array();
    }
    
    function __toString() {
        $addresses = array();
        foreach ($this->getAddressList() as $pair)
            $addresses[] = self::dumpAddressPair($pair);
        return implode(', ', $addresses);
    }
    
    /**
    * Dump a (name, address) pair in a canonicalized form.
    */
    public static function dumpAddressPair($pair) {
        if (isset($pair[0]))
            return vsprintf('"%s" <%s>', $pair);
        return $pair[1];
    }
    
    /**
    * Parse all addresses.
    *
    * Returns a list containing all of the addresses.
    */
    public function getAddressList() {
        $result = array();
        $ad = $this->getAddress();
        while ($ad) {
            $result = array_merge($result, $ad);
            $ad = $this->getAddress();
        }
        return $result;
    }
    
    // parser helpers
    
    // Parse the next address.
    function getAddress() {
        $this->commentlist = array();
        $this->gotoNext();
        
        $oldpos = $this->pos;
        $oldcl = $this->commentlist;
        $plist = $this->getPhraseList();
        
        $this->gotoNext();
        $returnlist = array();
        
        if ($this->pos >= len($this->field))
        {
            // Bad email address technically, no domain.
            if ($plist)
                $returnlist = array(
                    array(implode(' ', $this->commentlist), $plist[0]));
        }
        else if (instring('.@', index($this->field, $this->pos)))
        {
            // email address is just an addrspec
            // this isn't very efficient since we start over
            $this->pos = $oldpos;
            $this->commentlist = $oldcl;
            $addrspec = $this->getAddrSpec();
            $returnlist = array(
                array(implode(' ', $this->commentlist), $addrspec));
        }
        else if (index($this->field, $this->pos) == ':')
        {
            // address is a group
            $returnlist = array();
            
            $fieldlen = len($this->field);
            $this->pos += 1;
            while ($this->pos < len($this->field)) {
                $this->gotoNext();
                if ($this->pos < $fieldlen && index($this->field, $this->pos) == ';') {
                    $this->pos += 1;
                    break;
                }
                $returnlist = array_merge($returnlist, $this->getAddress());
            }
        }
        else if (index($this->field, $this->pos) == '<')
        {
            // Address is a phrase then a route addr
            $routeaddr = $this->getRouteAddr();
            
            if ($this->commentlist)
                $returnlist = array(
                    array(implode(' ', $plist).' ('.implode(' ', $this->commentlist).')',
                        $routeaddr));
            else
                $returnlist = array(
                    array(implode(' ', $plist), $routeaddr));
        }
        else
        {
            if ($plist)
                $returnlist = array(
                    array(implode(' ', $this->commentlist), $plist[0]));
            else if (instring(self::SPECIALS, index($this->field, $this->pos)))
                $this->pos += 1;
        }
        
        $this->gotoNext();
        if ($this->pos < len($this->field) && index($this->field, $this->pos) == ',')
            $this->pos += 1;
        return $returnlist;
    }
    
    // Parse a route address (Return-path value).
    // This method just skips all the route stuff and returns the addrspec.
    function getRouteAddr() {
        if (index($this->field, $this->pos) != '<')
            return;
        
        $expectroute = false;
        $this->pos += 1;
        $this->gotoNext();
        $adlist = '';
        
        while ($this->pos < len($this->field)) {
            if ($expectroute) {
                $this->getDomain();
                $expectroute = false;
            } else if (index($this->field, $this->pos) == '>') {
                $this->pos += 1;
                break;
            } else if (index($this->field, $this->pos) == '@') {
                $this->pos += 1;
                $expectroute = true;
            } else if (index($this->field, $this->pos) == ':') {
                $this->pos += 1;
            } else {
                $adlist = $this->getAddrSpec();
                $this->pos += 1;
                break;
            }
            $this->gotoNext();
        }
        
        return $adlist;
    }
    
    // Parse an RFC 2822 addr-spec.
    function getAddrSpec() {
        $aslist = array();
        
        $this->gotoNext();
        while ($this->pos < len($this->field)) {
            if (index($this->field, $this->pos) == '.') {
                $aslist[] = '.';
                $this->pos += 1;
            } else if (index($this->field, $this->pos) == '"') {
                $aslist[] = "\"{$this->getQuote()}\"";
            } else if (instring($this->atomends, index($this->field, $this->pos))) {
                break;
            } else {
                $aslist[] = $this->getAtom();
            }
            $this->gotoNext();
        }
        
        if ($this->pos >= len($this->field) || index($this->field, $this->pos) != '@')
            return implode('', $aslist);
        
        $aslist[] = '@';
        $this->pos += 1;
        $this->gotoNext();
        return implode('', $aslist) . $this->getDomain();
    }
    
    // Get the complete domain name from an address.
    function getDomain() {
        $sdlist = array();
        while ($this->pos < len($this->field)) {
            if (instring(self::LWS, index($this->field, $this->pos))) {
                $this->pos += 1;
            } else if (index($this->field, $this->pos) == '(') {
                $this->commentlist[] = $this->getComment();
            } else if (index($this->field, $this->pos) == '[') {
                $sdlist[] = $this->getDomainLiteral();
            } else if (index($this->field, $this->pos) == '.') {
                $this->pos += 1;
                $sdlist[] = '.';
            } else if (instring($this->atomends, index($this->field, $this->pos))) {
                break;
            } else {
                $sdlist[] = $this->getAtom();
            }
        }
        return implode('', $sdlist);
    }
    
    // Get a parenthesis-delimited fragment from self's field.
    function getComment() {
        return $this->getDelimited('(', ")\r", true);
    }
    
    // Parse a header fragment delimited by special characters.
    // 
    // `beginchar' is the start character for the fragment.  If self is not
    // looking at an instance of `beginchar' then getdelimited returns the
    // empty string.
    // 
    // `endchars' is a sequence of allowable end-delimiting characters.
    // Parsing stops when one of these is encountered.
    // 
    // If `allowcomments' is non-zero, embedded RFC 2822 comments are allowed
    // within the parsed fragment.
    function getDelimited($beginchar, $endchars, $allowcomments=true) {
        if (index($this->field, $this->pos) != $beginchar)
            return '';
        
        $slist = array();
        $quote = false;
        $this->pos += 1;
        
        while ($this->pos < len($this->field)) {
            if ($quote) {
                $slist[] = index($this->field, $this->pos);
                $quote = false;
            } else if (instring($endchars, index($this->field, $this->pos))) {
                $this->pos += 1;
                break;
            } else if ($allowcomments && index($this->field, $this->pos) == '(') {
                $slist[] = $this->getComment();
                continue; // have already advanced pos from getcomment
            } else if (index($this->field, $this->pos) == '\\') {
                $quote = true;
            } else {
                $slist[] = index($this->field, $this->pos);
            }
            $this->pos += 1;
        }
        
        return implode('', $slist);
    }
    
    // Parse an RFC 2822 domain-literal.
    function getDomainLiteral() {
        return '['.$this->getDelimited('[', "]\r", false).']';
    }
    
    // Parse a sequence of RFC 2822 phrases.
    // 
    // A phrase is a sequence of words, which are in turn either RFC 2822
    // atoms or quoted-strings.  Phrases are canonicalized by squeezing all
    // runs of continuous whitespace into one space.
    function getPhraseList() {
        $plist = array();
        
        while ($this->pos < len($this->field)) {
            if (instring(self::LWS, index($this->field, $this->pos))) {
                $this->pos += 1;
            } else if (index($this->field, $this->pos) == '"') {
                $plist[] = $this->getQuote();
            } else if (index($this->field, $this->pos) == '(') {
                $this->commentlist[] = $this->getComment();
            } else if (instring($this->phraseends, index($this->field, $this->pos))) {
                break;
            } else {
                $plist[] = $this->getAtom($this->phraseends);
            }
        }
        
        return $plist;
    }
    
    // Get a quote-delimited fragment from self's field.
    function getQuote() {
        return $this->getDelimited('"', "\"\r", false);
    }
    
    // Parse an RFC 2822 atom.
    // 
    // Optional atomends specifies a different set of end token delimiters
    // (the default is to use self.atomends).  This is used e.g. in
    // getphraselist() since phrase endings must not include the `.' (which
    // is legal in phrases).
    function getAtom($atomends=null) {
        $atomlist = array('');
        if (null === $atomends)
            $atomends = $this->atomends;
        
        while ($this->pos < len($this->field)) {
            if (instring($atomends, index($this->field, $this->pos)))
                break;
            else
                $atomlist[] = index($this->field, $this->pos);
            $this->pos += 1;
        }
        
        return implode('', $atomlist);
    }
    
    // Parse up to the start of the next address.
    function gotoNext() {
        while ($this->pos < len($this->field)) {
            $c = index($this->field, $this->pos);
            if (instring(self::LWS . "\n\r", $c))
                $this->pos += 1;
            else if ($c == '(')
                $this->commentlist[] = $this->getComment();
            else
                break;
        }
    }
}
