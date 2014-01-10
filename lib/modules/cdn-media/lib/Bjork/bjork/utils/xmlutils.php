<?php

namespace bjork\utils\xmlutils;

// props: <http://www.php.net/manual/en/ref.xmlwriter.php#104240>
class SimplerXMLGenerator extends \XMLWriter {
    private $encoding; 
    
    function __construct($encoding, $indent=false, $indentstr='  ') {
        $this->encoding = $encoding;
        
        $this->openMemory();
        $this->setIndent($indent);
        $this->setIndentString($indentstr);
    }
    
    function startDocument($version="1.0", $encoding=null, $standalone=null) {
        if (is_null($encoding))
            $encoding = $this->encoding;
        // this is a hack to have the document element generated even on
        // machines that have libxml built without iconv support.
        // see: <http://www.php.net/manual/en/function.xmlwriter-start-document.php#89957>
        parent::startDocument(sprintf('%s" encoding="%s', $version, $encoding), null, $standalone);
    }
    
    function startElement($name, array $attrs=null) {
        if (is_null($attrs))
            $attrs = array();
        parent::startElement($name);
        foreach ($attrs as $k => $v)
            $this->writeAttribute($k, $v);
    }
    
    /**
    * Convenience method for adding an element with no children.
    */
    function addQuickElement($name, $contents=null, array $attrs=null) {
        $this->startElement($name, $attrs);
        if (!is_null($contents))
            $this->text($contents);
        $this->endElement();
    }
}
