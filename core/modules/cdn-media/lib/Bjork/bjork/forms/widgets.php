<?php

namespace bjork\forms;

require_once __DIR__.'/../utils/html.php';
require_once __DIR__.'/../utils/translation/__init.php';

use bjork\utils\html,
    bjork\utils\translation;

abstract class Widget {
    protected
        $isHidden = false,
        $isRequired = false,
        $needsMultipartForm = false;
    
    var $attrs;
    
    function __construct($attrs=null) {
        $this->attrs = $attrs;
        if (is_null($attrs))
            $this->attrs = array();
    }
    
    // API
    
    public function isHidden() {
        return $this->isHidden;
    }
    
    public function isRequired() {
        return $this->isRequired;
    }
    
    public function needsMultipartForm() {
        return $this->needsMultipartForm;
    }
    
    public function getIdForLabel($id) {
        return $id;
    }
    
    // Subclassing API
    
    function render($name, $value, $attrs=null) {
        throw new \Exception("Subclasses must implement");
    }
    
    function valueFromDataDict($data, $files, $name) {
        if (isset($data[$name]))
            return $data[$name];
        return null;
    }
    
    // Protected
    
    function hasChanged($initial, $data) {
        if (is_null($data))
            $dataValue = '';
        else
            $dataValue = $data;
        if (is_null($initial))
            $initialValue = '';
        else
            $initialValue = $initial;
        return $initialValue != $dataValue;
    }
    
    function buildAttrs(array $extra_attrs=null, array $more_attrs=null) {
        if (is_null($more_attrs))
            $more_attrs = array();
        $attrs = array_merge($this->attrs, $more_attrs);
        if (!is_null($extra_attrs))
            $attrs = array_merge($attrs, $extra_attrs);
        return $attrs;
    }
    
    function setRequired($required) {
        $this->isRequired = $required;
    }
}

abstract class Input extends Widget {
    protected
        $inputType = null;
    
    function render($name, $value, $attrs=null) {
        if (is_null($value))
            $value = "";
        $finalAttrs = $this->buildAttrs($attrs, array(
            "type" => $this->inputType,
            "name" => $name));
        if ($value !== "")
            $finalAttrs["value"] = $value;
        return html::mark_safe(sprintf('<input%s />', flatatt($finalAttrs)));
    }
}

class TextInput extends Input {
    protected
        $inputType = "text";
}

class PasswordInput extends Input {
    protected
        $inputType = "password",
        $renderValue = false;
    
    function __construct($attrs=null, $renderValue=false) {
        parent::__construct($attrs);
        $this->renderValue = $renderValue;
    }
    
    function render($name, $value, $attrs=null) {
        if (!$this->renderValue)
            $value = null;
        return parent::render($name, $value, $attrs);
    }
}

class HiddenInput extends Input {
    protected
        $isHidden = true,
        $inputType = "hidden";
}

class MultipleHiddenInput extends HiddenInput {
    protected $choices;
    
    function __construct($attrs=null, array $choices=null) {
        parent::__construct($attrs);
        if (is_null($choices))
            $choices = array();
        $this->choices = $choices;
    }
    
    function render($name, $value, $attrs=null, array $choices=null) {
        if (is_null($value))
            $value = array();
        $finalAttrs = $this->buildAttrs($attrs, array('type' => $this->inputType, 'name' => "{$name}[]"));
        $id = isset($finalAttrs['id']) ? $finalAttrs['id'] : null;
        $inputs = array();
        $i = 0; foreach ($value as $v) {
            $inputAttrs = array_merge(array('value' => $v), $finalAttrs);
            if (!is_null($id))
                $inputAttrs['id'] = "{$id}_{$i}";
            $inputs[] = sprintf('<input%s />', flatatt($inputAttrs));
            $i++;
        }
        return html::mark_safe(implode("\n", $inputs));
    }
    
    function valueFromDataDict($data, $files, $name) {
        if (is_a($data, 'bjork\utils\datastructures\MultiValueDict'))
            return $data->getList($name);
        if (isset($data[$name]))
            return $data[$name];
        return null;
    }
}

class FileInput extends Input {
    protected
        $needsMultipartForm = true,
        $inputType = "file";
    
    function render($name, $value, $attrs=null) {
        return parent::render($name, null, $attrs);
    }
    
    function valueFromDataDict($data, $files, $name) {
        if (isset($files[$name]))
            return $files[$name];
        return null;
    }
}

class Textarea extends Widget {
    function __construct($attrs=null) {
        $defaultAttrs = array("cols" => "40", "rows" => "10");
        if (!empty($attrs))
            $defaultAttrs = array_merge($defaultAttrs, $attrs);
        parent::__construct($defaultAttrs);
    }
    
    function render($name, $value, $attrs=null) {
        if (is_null($value))
            $value = "";
        $finalAttrs = $this->buildAttrs($attrs, array("name" => $name));
        return html::mark_safe(sprintf('<textarea%s>%s</textarea>',
            flatatt($finalAttrs),
            html::conditional_escape($value)));
    }
}

class CheckboxInput extends Widget {
    var $checkTest = null;
    
    function __construct($attrs=null, $checkTest=null) {
        parent::__construct($attrs);
        if (is_null($checkTest)) {
            $checkTest = function($val) {
                return (bool)$val;
            };
        }
        $this->checkTest = $checkTest;
    }
    
    function render($name, $value, $attrs=null) {
        $finalAttrs = $this->buildAttrs($attrs, array(
            "type" => "checkbox",
            "name" => $name));
        $checkTest = $this->checkTest;
        $result = $checkTest($value);
        if ($result)
            $finalAttrs['checked'] = 'checked';
        if (!in_array($value, array('', true, false, null), true))
            $finalAttrs['value'] = strval($value);
        return html::mark_safe(sprintf('<input%s />', flatatt($finalAttrs)));
    }
    
    function valueFromDataDict($data, $files, $name) {
        if (!isset($data[$name]))
            return false;
        $value = $data[$name];
        $values = array('true' => true, 'false' => false);
        if (is_string($value) && in_array(strtolower($value), array_keys($values)))
            $value = $values[strtolower($value)];
        return $value;
    }
    
    function hasChanged($initial, $data) {
        return (bool)$initial != (bool)$data;
    }
}

class Select extends Widget {
    var $choices;
    
    function __construct($attrs=null, $choices=null) {
        parent::__construct($attrs);
        if (empty($choices))
            $choices = array();
        $this->choices = $choices;
    }
    
    function render($name, $value, $attrs=null, array $choices=null) {
        if (is_null($value))
            $value = "";
        if (is_null($choices))
            $choices = array();
        $finalAttrs = $this->buildAttrs($attrs, array("name" => $name));
        $output = array(sprintf("<select%s>", flatatt($finalAttrs)));
        $options = $this->renderOptions($choices, array($value));
        if (!empty($options))
            $output[] = $options;
        $output[] = '</select>';
        return html::mark_safe(implode("\n", $output));
    }
    
    function renderOptions(array $choices, array $selectedChoices) {
        $selectedChoices = array_unique(array_map('strval', $selectedChoices), SORT_LOCALE_STRING);
        $output = array();
        foreach (array($this->choices, $choices) as $c) {
            foreach ($c as $cval => $clabel) {
                if (is_array($clabel)) {
                    $output[] = sprintf('<optgroup label="%s">', html::escape($cval));
                    foreach ($clabel as $oval => $olabel)
                        $output[] = $this->renderOption($selectedChoices, $oval, $olabel);
                    $output[] = '</optgroup>';
                } else {
                    $output[] = $this->renderOption($selectedChoices, $cval, $clabel);
                }
            }
        }
        return implode("\n", $output);
    }
    
    function renderOption(array $selectedChoices, $optionValue, $optionLabel) {
        $selectedHtml = in_array(strval($optionValue), $selectedChoices, true)
            ? ' selected="selected"'
            : '';
        return sprintf('<option value="%s"%s>%s</option>',
            html::escape($optionValue), $selectedHtml,
            html::conditional_escape($optionLabel));
    }
}

class NullBooleanSelect extends Select {
    
    function __construct($attrs=null, $choices=null) {
        $choices = array(
            '1' => translation::gettext('Unknown'),
            '2' => translation::gettext('Yes'),
            '3' => translation::gettext('No'),
        );
        parent::__construct($attrs, $choices);
    }
    
    function render($name, $value, $attrs=null, array $choices=null) {
        if ($value === true)
            $value = '2';
        else if ($value === false)
            $value = '3';
        else {
            if (!in_array($value, array('2', '3'), true))
                $value = '1';
        }
        return parent::render($name, $value, $attrs, $choices);
    }
    
    function valueFromDataDict($data, $files, $name) {
        if (isset($data[$name]))
            $value = $data[$name];
        else
            $value = null;
        
        if (in_array($value, array('2', 'true', true), true))
            $value = true;
        else if (in_array($value, array('3', 'false', false), true))
            $value = false;
        else
            $value = null;
        
        return $value;
    }
    
    function hasChanged($initial, $data) {
        if (!is_null($data))
            $data = (bool)$data;
        if (!is_null($initial))
            $initial = (bool)$initial;
        return $initial != $data;
    }
}

class SelectMultiple extends Select {
    function render($name, $value, $attrs=null, array $choices=null) {
        if (is_null($value))
            $value = array();
        if (is_null($choices))
            $choices = array();
        $finalAttrs = $this->buildAttrs($attrs, array("name" => "{$name}[]"));
        $output = array(sprintf('<select multiple="multiple"%s>', flatatt($finalAttrs)));
        $options = $this->renderOptions($choices, $value);
        if (!empty($options))
            $output[] = $options;
        $output[] = '</select>';
        return html::mark_safe(implode("\n", $output));
    }
    
    function valueFromDataDict($data, $files, $name) {
        if (is_a($data, 'bjork\utils\datastructures\MultiValueDict'))
            return $data->getList($name);
        if (isset($data[$name]))
            return $data[$name];
        return null;
    }
    
    function hasChanged($initial, $data) {
        if (is_null($data))
            $dataValue = array();
        else
            $dataValue = $data;
        if (is_null($initial))
            $initialValue = array();
        else
            $initialValue = $initial;
        if (count($initialValue) != count($dataValue))
            return true;
        $initialSet = array_unique(array_map('strval', $initialValue), SORT_LOCALE_STRING);
        $dataSet = array_unique(array_map('strval', $dataValue), SORT_LOCALE_STRING);
        return $initialSet != $dataSet;
    }
}

class RadioInput {
    var $name, $value, $attrs, $choiceValue, $choiceLabel, $index;
    
    function __construct($name, $value, $attrs, $choice, $index) {
        $this->name = $name;
        $this->value = $value;
        $this->attrs = $attrs;
        $choice = each($choice);
        $this->choiceValue = $choice['key'];
        $this->choiceLabel = $choice['value'];
        $this->index = $index;
    }
    
    function render() {
        if (isset($this->attrs['id']))
            $labelFor = sprintf(' for="%s_%s"', $this->attrs['id'], $this->index);
        else
            $labelFor = '';
        $choiceLabel = html::conditional_escape($this->choiceLabel);
        return html::mark_safe(sprintf('<label%s>%s %s</label>', $labelFor, $this->tag(), $choiceLabel));
    }
    
    function isChecked() {
        return strval($this->value) === strval($this->choiceValue);
    }
    
    function tag() {
        if (isset($this->attrs['id']))
            $this->attrs['id'] = "{$this->attrs['id']}_{$this->index}";
        $finalAttrs = array_merge($this->attrs, array(
            'type' => 'radio',
            'name' => $this->name,
            'value' => $this->choiceValue,
        ));
        if ($this->isChecked())
            $finalAttrs['checked'] = 'checked';
        return html::mark_safe(sprintf('<input%s />', flatatt($finalAttrs)));
    }
}

class RadioFieldRenderer implements \IteratorAggregate, \ArrayAccess, \Countable {
    public $inputClass = 'bjork\forms\RadioInput';
    
    var $name, $value, $attrs, $choices;
    
    function __construct($name, $value, $attrs, $choices) {
        $this->name = $name;
        $this->value = $value;
        $this->attrs = $attrs;
        $this->choices = $choices;
    }
    
    function __toString() {
        return $this->render();
    }
    
    function render() {
        $choices = array();
        foreach ($this as $c)
            $choices[] = "<li>{$c->render()}</li>";
        return html::mark_safe(sprintf('<ul>%s</ul>', implode("\n", $choices)));
    }
    
    function getIterator() {
        $c = array();
        $i = 0; foreach ($this->choices as $k => $_) {
            $c[$k] = $this[$i];
            $i++;
        }
        return new \ArrayIterator($c);
    }
    
    function count() {
        return count($this->choices);
    }
    
    function offsetExists($offset) {
        return isset($this->choices[$offset]);
    }
    
    function offsetGet($offset) {
        $choice = array_slice($this->choices, $offset, 1, true);
        return new $this->inputClass($this->name, $this->value, $this->attrs, $choice, $offset);
    }
    
    function offsetSet($offset, $value) { /* nope */ }
    
    function offsetUnset($offset) { /* nope */ }
}

class RadioSelect extends Select {
    protected $renderer = 'bjork\forms\RadioFieldRenderer';
    
    function __construct($attrs=null, $choices=null, $renderer=null) {
        parent::__construct($attrs, $choices);
        if (!is_null($renderer))
            $this->renderer = $renderer;
    }
    
    function getRenderer($name, $value, $attrs=null, $choices=null) {
        if (is_null($value))
            $value = "";
        $strValue = strval($value);
        $finalAttrs = $this->buildAttrs($attrs);
        if (!is_null($choices))
            $choices = array_merge($this->choices, $choices);
        else
            $choices = $this->choices;
        return new $this->renderer($name, $strValue, $finalAttrs, $choices);
    }
    
    public function getIdForLabel($id) {
        if (is_string($id))
            $id .= '_0';
        return $id;
    }
    
    function render($name, $value, $attrs=null, array $choices=null) {
        return $this->getRenderer($name, $value, $attrs, $choices)->render();
    }
}

class CheckboxSelectMultiple extends SelectMultiple {
    public $inputClass = 'bjork\forms\CheckboxInput';
    
    public function getIdForLabel($id) {
        if (is_string($id))
            $id .= '_0';
        return $id;
    }
    
    function render($name, $value, $attrs=null, array $choices=null) {
        if (is_null($value))
            $value = array();
        if (is_null($choices))
            $choices = $this->choices;
        else
            $choices = array_merge($this->choices, $choices);
        $hasId = isset($attrs['id']);
        $finalAttrs = $this->buildAttrs($attrs, array("name" => "{$name}[]"));
        $output = array('<ul>');
        $strValues = array_unique(array_map('strval', $value), SORT_LOCALE_STRING);
        $i = 0; foreach ($choices as $choiceValue => $choiceLabel) {
            if ($hasId) {
                $finalAttrs['id'] = "{$attrs['id']}_{$i}";
                $labelFor = ' for="'.$finalAttrs['id'].'"';
            } else {
                $labelFor = '';
            }
            $checkTest = function($val) use ($strValues) {
                return in_array($val, $strValues, true);
            };
            $cb = new $this->inputClass($finalAttrs, $checkTest);
            $optionValue = strval($choiceValue);
            $renderedCb = $cb->render("{$name}[]", $optionValue);
            $optionLabel = html::conditional_escape(strval($choiceLabel));
            $output[] = sprintf('<li><label%s>%s %s</label></li>',
                $labelFor, $renderedCb, $optionLabel);
            $i++;
        }
        $output[] = '</ul>';
        return html::mark_safe(implode("\n", $output));
    }
}

/**
* A widget that is composed of multiple widgets.
*
* Its render() method is different than other widgets', because it has to
* figure out how to split a single value for display in multiple widgets.
* The ``value`` argument can be one of two things:
*
*     * A list.
*     * A normal value (e.g., a string) that has been "compressed" from
*       a list of values.
*
* In the second case -- i.e., if the value is NOT a list -- render() will
* first "decompress" the value into a list before rendering it. It does so by
* calling the decompress() method, which MultiWidget subclasses must
* implement. This method takes a single "compressed" value and returns a
* list.
*
* When render() does its HTML rendering, each value in the list is rendered
* with the corresponding widget -- the first value is rendered in the first
* widget, the second value is rendered in the second widget, etc.
*
* Subclasses may implement format_output(), which takes the list of rendered
* widgets and returns a string of HTML that formats them any way you'd like.
*
* You'll probably want to use this class with MultiValueField.
*/
class MultiWidget extends Widget {
    protected $widgets;
    
    function __construct(array $widgets, $attrs=null) {
        $this->widgets = array();
        foreach ($widgets as $widget) {
            if (is_string($widget))
                $widget = new $widget();
            $this->widgets[] = $widget;
        }
        parent::__construct($attrs);
    }
    
    function render($name, $value, $attrs=null) {
        if (!is_array($value))
            $value = $this->decompress($value);
        $output = array();
        $finalAttrs = $this->buildAttrs($attrs);
        $id = isset($finalAttrs['id']) ? $finalAttrs['id'] : null;
        for ($i = 0; $i < count($this->widgets); $i++) { 
            $widget = $this->widgets[$i];
            if (isset($value[$i]))
                $widget_value = $value[$i];
            else
                $widget_value = null;
            if (!empty($id)) {
                $finalAttrs = array_merge($finalAttrs, array(
                    'id' => "{$id}_{$i}",
                ));
            }
            $output[] = $widget->render("{$name}_{$i}", $widget_value, $finalAttrs);
        }
        return html::mark_safe($this->formatOutput($output));
    }
    
    public function getIdForLabel($id) {
        if (is_string($id))
            $id .= '_0';
        return $id;
    }
    
    function valueFromDataDict($data, $files, $name) {
        $ret = array();
        for ($i = 0; $i < count($this->widgets); $i++) { 
            $widget = $this->widgets[$i];
            $ret[] = $widget->valueFromDataDict($data, $files, "{$name}_{$i}");
        }
        return $ret;
    }
    
    function hasChanged($initial, $data) {
        if (is_null($initial)) {
            $initial = array();
            for ($i = 0; $i < count($data); $i++)
                $initial[] = '';
        } else {
            if (!is_array($initial))
                $initial = $this->decompress($initial);
        }
        
        for ($i = 0; $i < count($this->widgets); $i++) { 
            $widget = $this->widgets[$i];
            $_init = isset($initial[$i]) ? $initial[$i] : null;
            $_data = isset($data[$i]) ? $data[$i] : null;
            if ($widget->hasChanged($_init, $_data))
                return true;
        }
        
        return false;
    }
    
    protected function formatOutput($rendered_widgets) {
        return implode('', $rendered_widgets);
    }
    
    protected function decompress($value) {
        throw new \Exception('Subclasses must implement.');
    }
}
