<?php

/**
* Functions for reversing a regular expression (used in reverse URL resolving).
* Used internally by Django and not intended for external use.
* 
* This is not, and is not intended to be, a complete reg-exp decompiler. It
* should be good enough for a large class of URLS, however.
*/

namespace bjork\utils {

final class regex_helper {
    public static function normalize($pattern) {
        return regex_helper\normalize($pattern);
    }
}

}

namespace bjork\utils\regex_helper {

use strutils;

use bjork\utils\datastructures\List_,
    bjork\utils\datastructures\IndexError;

class NotImplementedError extends \Exception {}
class StopIteration extends IndexError {}

/**
* Used to represent multiple possibilities at this point in a pattern string.
* We use a distinguished type, rather than a list, so that the usage in the
* code is clear.
*/
class Choice extends List_ {}
const ChoiceClass = 'bjork\utils\regex_helper\Choice';

/**
* Used to represent a capturing group in the pattern string.
*/
class Group extends List_ {}
const GroupClass = 'bjork\utils\regex_helper\Group';

/**
* Used to represent a non-capturing group in the pattern string.
*/
class NonCapture extends List_ {}
const NonCaptureClass = 'bjork\utils\regex_helper\NonCapture';

/**
* Given a reg-exp pattern, normalizes it to a list of forms that suffice for
* reverse matching. This does the following:
* 
* (1) For any repeating sections, keeps the minimum number of occurrences
*     permitted (this means zero for optional groups).
* (2) If an optional group includes parameters, include one occurrence of
*     that group (along with the zero occurrence case from step (1)).
* (3) Select the first (essentially an arbitrary) element from any character
*     class. Select an arbitrary character for any unordered class (e.g. '.'
*     or '\w') in the pattern.
* (5) Ignore comments and any of the reg-exp flags that won't change
*     what we construct ("iLmsu"). "(?x)" is an error, however.
* (6) Raise an error on all other non-capturing (?...) forms (e.g.
*     look-ahead and look-behind matches) and any disjunctive ('|')
*     constructs.
* 
* Django's URLs for forward resolving are either all positional arguments or
* all keyword arguments. That is assumed here, as well. Although reverse
* resolving can be done using positional args when keyword args are
* specified, the two cannot be mixed in the same reverse() call.
*/
function normalize($pattern) {
    // Do a linear scan to work out the special features of this pattern.
    // The idea is that we scan once here and collect all the information
    // we need to make future decisions.
    $result = new List_();
    $non_capturing_groups = new List_();
    $consume_next = true;
    $pattern_iter = new RegexWalker(strutils::explode($pattern));
    $num_args = 0;
    
    $__empty_result = array(array('', array()));
    
    // A "while" loop is used here because later on we need to be able to peek
    // at the next character and possibly go around without consuming another
    // one at the top of the loop.
    try {
        list($ch, $escaped) = $pattern_iter->next();
    } catch (StopIteration $e) {
        return $__empty_result;
    }
    
    try {
        while (true) {
            if ($escaped) {
                $result->append($ch);
            } else if ($ch == '.') {
                // Replace "any character" with an arbitrary representative.
                $result->append('.');
            } else if ($ch == '|') {
                throw new NotImplementedError();
            } else if ($ch == '^') {
                // pass
            } else if ($ch == '$') {
                break;
            } else if ($ch == ')') {
                // This can only be the end of a non-capturing group, since all
                // other unescaped parentheses are handled by the grouping
                // section later (and the full group is handled there).
                // 
                // We regroup everything inside the capturing group so that it
                // can be quantified, if necessary.
                $start = $non_capturing_groups->pop();
                $inner = new NonCapture($result->slice($start));
                $r = $result->slice(0, $start)->toArray();
                $r[] = $inner;
                $result = new List_($r);
            } else if ($ch == '[') {
                // Replace ranges with the first character in the range.
                list($ch, $escaped) = $pattern_iter->next();
                $result->append($ch);
                list($ch, $escaped) = $pattern_iter->next();
                while ($escaped || $ch != ']')
                    list($ch, $escaped) = $pattern_iter->next();
            } else if ($ch == '(') {
                // Some kind of group.
                list($ch, $escaped) = $pattern_iter->next();
                if ($escaped || $ch != '?') {
                    // A positional group
                    $name = "_{$num_args}";
                    $num_args++;
                    $result->append(new Group(array("%({$name})s", $name)));
                    walk_to_end($ch, $pattern_iter);
                } else {
                    list($ch, $escaped) = $pattern_iter->next();
                    if (in_array($ch, strutils::explode('imsxUXJ#'))) {
                        // All of these are ignorable. Walk to the end of the
                        // group.
                        walk_to_end($ch, $pattern_iter);
                    } else if ($ch == ':') {
                        // Non-capturing group
                        $non_capturing_groups->append(count($result));
                    } else if ($ch != 'P') {
                        // Anything else, other than a named group, is something
                        // we cannot reverse.
                        throw new \Exception("Non-reversible reg-exp portion: '(?{$ch}'");
                    } else {
                        list($ch, $escaped) = $pattern_iter->next();
                        if ($ch != '<')
                            throw new \Exception("Non-reversible reg-exp portion: '(?P{$ch}'");
                        // We are in a named capturing group. Extract the name
                        // and then skip to the end.
                        $name = array();
                        list($ch, $escaped) = $pattern_iter->next();
                        while ($ch != '>') {
                            $name[] = $ch;
                            list($ch, $escaped) = $pattern_iter->next();
                        }
                        $param = implode('', $name);
                        $result->append(new Group(array("%({$param})s", $param)));
                        walk_to_end($ch, $pattern_iter);
                    }
                }
            } else if (in_array($ch, strutils::explode('*?+{'))) {
                // Quanitifers affect the previous item in the result list.
                list($count, $ch) = get_quantifier($ch, $pattern_iter);
                if ($ch)
                    // We had to look ahead, but it wasn't needed to compute
                    // the quanitifer, so use this character next time around
                    // the main loop.
                    $consume_next = false;
                
                $last_index = count($result) - 1;
                if ($count === 0) {
                    if (contains($result[$last_index], GroupClass)) {
                        // If we are quantifying a capturing group (or
                        // something containing such a group) and the minimum is
                        // zero, we must also handle the case of one occurrence
                        // being present. All the quantifiers (except {0,0},
                        // which we conveniently ignore) that have a 0 minimum
                        // also allow a single occurrence.
                        $result[$last_index] = new Choice(array(null, $result[$last_index]));
                    } else {
                        $result->pop();
                    }
                } else if ($count > 1) {
                    $item = $result[$last_index];
                    $items = new List_(array_fill(0, $count - 1, $item));
                    $result->extend($items);
                }
            } else {
                // Anything else is a literal
                $result->append($ch);
            }
            
            if ($consume_next)
                list($ch, $escaped) = $pattern_iter->next();
            else
                $consume_next = true;
        }
    } catch (StopIteration $e) {
        // pass
    } catch (NotImplementedError $e) {
        // A case of using the disjunctive form. No results for you!
        return $__empty_result;
    }
    
    list($k, $v) = flatten_result($result);
    $out = array_map(null, $k, $v);
    
    return $out;
}

/**
* An iterator that yields the next character from "pattern_iter", respecting
* escape sequences. An escaped character is replaced by a representative of
* its class (e.g. \w -> "x"). If the escaped character is one that is
* skipped, it is not returned (the next character is returned instead).
* 
* Yields the next character, along with a boolean indicating whether it is a
* raw (unescaped) character or not.
*/
class RegexWalker {
    /**
    * Mapping of an escape character to a representative of that class. So, e.g.,
    * "\w" is replaced by "x" in a reverse URL. A value of None means to ignore
    * this sequence. Any missing key is mapped to itself.
    */
    static $escapeMappings = array(
        'A' => null,
        'b' => null,
        'B' => null,
        'd' => '0',
        'D' => 'x',
        's' => ' ',
        'S' => 'x',
        'w' => 'x',
        'W' => '!',
        'Z' => null,
    );
    
    private $position, $array;  
    
    function __construct(array $input) {
        $this->position = -1;
        $this->array = $input;
    }
    
    function next() {
        ++$this->position;
        if (!isset($this->array[$this->position]))
            throw new StopIteration();
        $ch = $this->array[$this->position];
        if ($ch != '\\')
            return array($ch, false);
        list($ch, $escaped) = $this->next();
        $representative = self::getRepresentative($ch, $ch);
        if (is_null($representative))
            return $this->next();
        return array($representative, true);
    }
    
    static function getRepresentative($ch, $default=null) {
        if (isset(self::$escapeMappings[$ch]))
            return self::$escapeMappings[$ch];
        return $default;
    }
}

/**
* The iterator is currently inside a capturing group. We want to walk to the
* close of this group, skipping over any nested groups and handling escaped
* parentheses correctly.
*/
function walk_to_end($ch, RegexWalker $input_iter) {
    if ($ch == '(')
        $nesting = 1;
    else
        $nesting = 0;
    while (true) {
        list($ch, $escaped) = $input_iter->next();
        if ($escaped)
            continue;
        else if ($ch == '(')
            $nesting++;
        else if ($ch == ')') {
            if (!$nesting)
                return;
            $nesting--;
        }
    }
}

/**
* Parse a quantifier from the input, where "ch" is the first character in the
* quantifier.
* 
* Returns the minimum number of occurences permitted by the quantifier and
* either None or the next character from the input_iter if the next character
* is not part of the quantifier.
*/
function get_quantifier($ch, RegexWalker $input_iter) {
    if (in_array($ch, strutils::explode('*?+'))) {
        try {
            list($ch2, $escaped) = $input_iter->next();
        } catch (StopIteration $e) {
            $ch2 = null;
        }
        if ($ch2 === '?')
            $ch2 = null;
        if ($ch === '+')
            return array(1, $ch2);
        return array(0, $ch2);
    }
    
    $quant = array();
    while ($ch !== '}') {
        list($ch, $escaped) = $input_iter->next();
        $quant[] = $ch;
    }
    array_pop($quant);
    $values = explode(',', implode('', $quant));
    
    // Consume the trailing '?', if necessary.
    try {
        list($ch, $escaped) = $input_iter->next();
    } catch (StopIteration $e) {
        $ch = null;
    }
    if ($ch === '?')
        $ch = null;
    return array(intval($values[0], 10), $ch);
}

/**
* Returns True if the "source" contains an instance of "inst". False,
* otherwise.
*/
function contains($source, $inst) {
    if (is_a($source, $inst))
        return true;
    if ($source instanceof NonCapture) {
        foreach ($source as $elt) {
            if (contains($elt, $inst))
                return true;
        }
    }
    return false;
}

/**
* Turns the given source sequence into a list of reg-exp possibilities and
* their arguments. Returns a list of strings and a list of argument lists.
* Each of the two lists will be of the same length.
*/
function flatten_result($source) {
    $__empty_result = array(
        array(''),
        array(array()),
    );
    
    if (is_null($source))
        return $__empty_result;
    
    if ($source instanceof Group) {
        if (is_null($source[1]))
            $params = array();
        else
            $params = array($source[1]);
        return array(
            array($source[0]),
            array($params),
        );
    }
    $result = array('');
    $result_args = array(array());
    $pos = 0;
    $last = 0;
    for ($pos = 0; $pos < count($source); $pos++) {
        $elt = $source[$pos];
        if (is_string($elt))
            continue;
        $piece = implode('', $source->slice($last, $pos)->toArray());
        if ($elt instanceof Group) {
            $piece .= $elt[0];
            $param = $elt[1];
        } else {
            $param = null;
        }
        $last = $pos + 1;
        for ($i = 0; $i < count($result); $i++) {
            $result[$i] .= $piece;
            if ($param)
                $result_args[$i][] = $param;
        }
        if ($elt instanceof Choice || $elt instanceof NonCapture) {
            if ($elt instanceof NonCapture)
                $elt = array($elt);
            $inner_result = array();
            $inner_args = array();
            foreach ($elt as $item) {
                list($res, $args) = flatten_result($item);
                array_splice($inner_result, count($inner_result), 0, $res);
                array_splice($inner_args, count($inner_args), 0, $args);
            }
            $new_result = array();
            $new_args = array();
            foreach (array_combine($result, $result_args) as $item => $args) {
                foreach (array_combine($inner_result, $inner_args) as $i_item => $i_args) {
                    $new_result[] = $item . $i_item;
                    $args_ = array_merge(array(), $args);
                    $new_args[] = array_merge($args, $i_args);
                }
            }
            $result = $new_result;
            $result_args = $new_args;
        }
    }
    if ($pos >= $last) {
        $piece = implode('', $source->slice($last)->toArray());
        for ($i = 0; $i < count($result); $i++)
            $result[$i] .= $piece;
    }
    return array($result, $result_args);
}

}
