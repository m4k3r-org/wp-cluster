<?php

namespace {

final class timeit {
    const FORMAT_SECONDS = 1;
    const FORMAT_MILISECONDS = 2;
    const FORMAT_MICROSECONDS = 4;
    
    protected
        $markers,
        $times,
        $level;
    
    private
        $children,
        $activeInstance;
    
    private static $instance = null;
    
    public static function getInstance($label=null) {
        if (is_null(self::$instance))
            self::$instance = new self($label);
        return self::$instance;
    }
    
    private function __construct($label='') {
        $this->markers = array();
        $this->times = array();
        $this->level = -1;
        
        $this->children = array();
        
        $this->push($label);
    }
    
    function push($label) {
        $this->level++;
        $this->markers[] = array(
            'time' => self::getMicrotime(),
            'label' => $label,
            'level' => $this->level);
    }
    
    function peek($format=null) {
        $time = self::elapsedMicrotime(
            $this->markers[$this->level]['time'],
            self::getMicrotime());
        if (!$format)
            return $time;
        return self::formatTime($time, $format);
    }
    
    function pop() {
        $this->level--;
        return array_pop($this->markers);
    }
    
    function pull() {
        $time = $this->peek();
        $marker = $this->markers[$this->level];
        $marker['elapsed'] = $time;
        $this->times[] = $marker;
        $this->pop();
        return $time;
    }
    
    function collect() {
        while ($this->level > 0)
            $this->pull();
        return $this;
    }
    
    function getTimes() {
        return $this->times;
    }
    
    function toString() {
        $format = '%s (%s ms total time)';
        $marker = $this->markers[0];
        $elapsed = self::elapsedMicrotime(
            $marker['time'], self::getMicrotime());
        return sprintf($format,
            $marker['label'],
            self::formatTime($elapsed,
                self::FORMAT_MILISECONDS));
    }
    
    function toHtml() {
        $total = $this->toString();
        $containerFormat = "\n<dl>\n<dt>%s</dt>\n<dd>\n%s</dd>\n</dl>\n";
        $format = "\t<li>%s (%s ms)</li>\n";
        $rs = "";
        
        $markers = $this->getTimes();
        foreach ($markers as $marker) {
            $rs .= sprintf($format,
                $marker['label'],
                self::formatTime($marker['elapsed'],
                    self::FORMAT_MILISECONDS));
        }
        
        return sprintf($containerFormat,
            $total,
            "<ul>\n".$rs."</ul>\n");
    }
    
    static function formatTime($time, $format=TimedSection::FORMAT_SECONDS) {
        if ($format === self::FORMAT_SECONDS)
            return round($time, 4);
        else if ($format === self::FORMAT_MILISECONDS)
            return round($time*1000, 2);
        else if ($format === self::FORMAT_MICROSECONDS)
            return round($time*1000000, 2);
        else
            return $time;
    }
    
    static function getMicrotime() {
        return microtime();
    }
    
    // Props Edor Faus.
    // <http://edorfaus.xepher.net/div/convert-method-test.php>
    static function elapsedMicrotime($before, $after) {
        return (substr($after, 11) - substr($before, 11))
             + (substr($after, 0, 9) - substr($before, 0, 9));
    }
}

}
