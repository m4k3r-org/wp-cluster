<?php

namespace {

final class pprint {
    
    public static function dump($value, $max_levels=2, $_level=0) {
        if (is_array($value))
        {
            if ($_level < $max_levels) {
                $indent = str_repeat('    ', $_level);
                $line_indent = str_repeat('    ', $_level + 1);
                $lines = array("Array");
                $lines[] = "{$indent}(";
                foreach ($value as $k => $v) {
                    $v = self::dump($v, $max_levels, $_level + 1);
                    $lines[] = "{$line_indent}[{$k}] => {$v}";
                }
                $lines[] = "{$indent})";
                $value = implode("\n", $lines);
            } else {
                $value = 'Array (...) [' . count($value) . ']';
            }
        }
        else if (is_object($value))
        {
            $class = get_class($value);
            $props = get_object_vars($value);
            $dump = self::dump($props, $max_levels, $_level);
            $dump = $class . mb_substr($dump, strlen('Array'));
            $value = "Object => {$dump}";
        }
        else if (is_bool($value))
        {
            $value = $value ? 'true' : 'false';
        }
        else if (is_null($value))
        {
            $value = 'null';
        }
        else if (is_string($value))
        {
            $value = "'{$value}'";
        }
        else
        {
            $value = print_r($value, true);
        }
        return $value;
    }
}

}
