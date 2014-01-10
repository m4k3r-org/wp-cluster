<?php

namespace optparse;

require_once __DIR__ . '/option.php';
require_once __DIR__ . '/option_parser.php';

abstract class HelpFormatter {
    
    const NO_DEFAULT_VALUE = 'none';
    
    var $parser;
    
    var $option_strings,
        $width,
        $short_first,
        $help_width,
        $help_position,
        $max_help_position,
        $current_indent,
        $indent_increment,
        $level;
    
    var
        $default_tag,
        $short_opt_fmt,
        $long_opt_fmt;
    
    function __construct($indent_increment, $max_help_position, $width, $short_first) {
        $this->parser = null;
        $this->indent_increment = $indent_increment;
        $this->help_position = $max_help_position;
        $this->max_help_position = $max_help_position;
        if ($width === null) {
            $w = getenv('COLUMNS');
            $width = false === $w ? 80 : $w;
            $width -= 2;
        }
        $this->width = $width;
        $this->current_indent = 0;
        $this->level = 0;
        $this->help_width = null;
        $this->short_first = $short_first;
        $this->option_strings = array();
        $this->default_tag = '%default';
        $this->short_opt_fmt = '%s %s';
        $this->long_opt_fmt = '%s=%s';
    }
    
    function setParser(OptionParser $parser) {
        $this->parser = $parser;
    }
    
    function setShortOptDelimiter($delim) {
        if (!in_array($delim, array('', ' ')))
            throw new \Exception(
                "invalid metavar delimiter for short options: {$delim}");
        $this->short_opt_fmt = "%s{$delim}%s";
    }
    
    function setLongOptDelimiter($delim) {
        if (!in_array($delim, array('', ' ')))
            throw new \Exception(
                "invalid metavar delimiter for long options: {$delim}");
        $this->long_opt_fmt = "%s{$delim}%s";
    }
    
    function indent() {
        $this->current_indent += $this->indent_increment;
        $this->level++;
    }
    
    function dedent() {
        $this->current_indent -= $this->indent_increment;
        $this->level--;
    }
    
    function formatText($text) {
        $text_width = $this->width - $this->current_indent;
        $indent = str_repeat(' ', $this->current_indent);
        return textwrap_fill($text, $text_width, $indent);
    }
    
    function formatDescription($description) {
        if ($description)
            return $this->formatText($description) . "\n";
        return '';
    }
    
    function formatEpilog($epilog) {
        if ($epilog)
            return "\n" . $this->formatText($epilog) . "\n";
        return '';
    }
    
    function formatOption($option) {
        $result = array();
        $opts = $this->option_strings[strval($option)];
        $opt_width = $this->help_position - $this->current_indent - 2;
        $indent = str_repeat(' ', $this->current_indent);
        if (strlen($opts) > $opt_width) {
            $opts = $opts . "\n";
            $indent_first = $this->help_position;
        } else {
            $opts = str_pad($opts, $opt_width) . "  ";
            $indent_first = 0;
        }
        $result[] = $indent . $opts;
        $help = $option->help;
        if (!empty($help)) {
            $help = $this->expandDefault($option);
            $help_lines = textwrap_wrap($help, $this->help_width);
            $result[] = str_repeat(' ', $indent_first) . $help_lines[0] . "\n";
            for ($i=1; $i < count($help_lines); $i++) { 
                $result[] = str_repeat(' ', $this->help_position) . $help_lines[$i] . "\n";
            }
        } else if (substr($opts, -1) != "\n") {
            $result[] = "\n";
        }
        
        return implode('', $result);
    }
    
    function formatOptionStrings($option) {
        if ($option->takesValue()) {
            $metavar = $option->metavar ?: strtoupper($option->dest);
            
            $fmt = $this->short_opt_fmt;
            $short_opts = array_map(function($sopt) use ($fmt, $metavar) {
                return sprintf($fmt, $sopt, $metavar);
            }, $option->short_opts);
            
            $fmt = $this->long_opt_fmt;
            $long_opts = array_map(function($lopt) use ($fmt, $metavar) {
                return sprintf($fmt, $lopt, $metavar);
            }, $option->long_opts);
        } else {
            $short_opts = $option->short_opts;
            $long_opts = $option->long_opts;
        }
        
        if ($this->short_first)
            $opts = array_merge($short_opts, $long_opts);
        else
            $opts = array_merge($long_opts, $short_opts);
        
        return implode(', ', $opts);
    }
    
    abstract function formatUsage($usage);
    abstract function formatHeading($heading);
    
    function expandDefault($option) {
        if ($this->parser === null || !$this->default_tag)
            return $option->help;
        $default_value = isset($this->parser->defaults[$option->dest])
            ? $this->parser->defaults[$option->dest]
            : null;
        if ($default_value === Option::NO_DEFAULT || $default_value === null)
            $default_value = self::NO_DEFAULT_VALUE;
        return str_replace($this->default_tag, print_r($default_value, true), $option->help);
    }
    
    function storeOptionStrings($parser) {
        $this->indent();
        $max_len = 0;
        foreach ($parser->option_list as $opt) {
            $strings = $this->formatOptionStrings($opt);
            $this->option_strings[strval($opt)] = $strings;
            $max_len = max($max_len, strlen($strings) + $this->current_indent);
        }
        $this->indent();
        foreach ($parser->option_groups as $group) {
            foreach ($group->option_list as $opt) {
                $strings = $this->formatOptionStrings($opt);
                $this->option_strings[strval($opt)] = $strings;
                $max_len = max($max_len, strlen($strings) + $this->current_indent);
            }
        }
        $this->dedent();
        $this->dedent();
        $this->help_position = min($max_len + 2, $this->max_help_position);
        $this->help_width = $this->width - $this->help_position;
    }
}

class IndentedHelpFormatter extends HelpFormatter {
    function __construct($indent_increment=2, $max_help_position=24,
                         $width=null, $short_first=true)
    {
        parent::__construct($indent_increment, $max_help_position,
                            $width, $short_first);
    }
    
    function formatUsage($usage) {
        return "Usage: {$usage}\n";
    }
    
    function formatHeading($heading) {
        return str_repeat(' ', $this->current_indent) . $heading . ":\n";
    }
}

/*
    props ju1ius:
        <http://php.net/manual/en/function.wordwrap.php#107570>
*/
function utf8_wordwrap($string, $width=75, $break="\n", $cut=true) {
    if ($cut) {
        // Match anything 1 to $width chars long followed by whitespace or EOS,
        // otherwise match anything $width chars long
        $search = "/(.{1,{$width}})(?:\s|$)|(.{{$width}})/uS";
        $replace = "$1$2{$break}";
    } else {
        // Anchor the beginning of the pattern with a lookahead
        // to avoid crazy backtracking when words are longer than $width
        $search = "/(?=\s)(.{1,{$width}})(?:\s|$)/uS";
        $replace = "$1{$break}";
    }
    return preg_replace($search, $replace, $string);
}

function textwrap_wrap($text, $text_width, $indent='') {
    return explode("\n", wordwrap($text, $text_width));
}

function textwrap_fill($text, $text_width, $indent='') {
    return implode("\n", array_map(function($line) use ($indent) {
        return $indent . $line;
    }, textwrap_wrap($text, $text_width, $indent)));
}

