<?php

namespace bjork\forms;

require_once __DIR__.'/../core/exceptions.php';
require_once __DIR__.'/../core/validators.php';
require_once __DIR__.'/../utils/translation/__init.php';

use bjork\core\exceptions\ValidationError,
    bjork\core\validators,
    bjork\forms\ErrorList,
    bjork\utils\translation;

class Field {
    var
        $widget = 'bjork\forms\TextInput',
        $hiddenWidget = 'bjork\forms\HiddenInput';
    
    var
        $label,
        $helpText,
        $required,
        $showHiddenInitial,
        $validators,
        $errorMessages,
        $initial;
    
    var
        $options;
    
    function __construct(array $options=null) {
        if (is_null($options))
            $options = array();
        
        $this->options = $options;
        
        $this->label = $this->getOption("label", null);
        $this->widget = $this->getOption("widget", $this->widget);
        $this->required = $this->getOption("required", true);
        $this->helpText = $this->getOption("helpText", "");
        $this->initial = $this->getOption("initial", null);
        $this->showHiddenInitial = $this->getOption("showHiddenInitial", false);
        
        $widget = $this->getWidget();
        $widget->setRequired($this->required);
        $attrs = $this->getWidgetAttrs($widget);
        if (!empty($attrs)) {
            $attrs = array_merge($widget->attrs, $attrs);
            $widget->attrs = $attrs;
        }
        $this->widget = $widget;
        
        $errorMessages = $this->getOption("errorMessages", array());
        $this->errorMessages = array_merge($this->getDefaultErrorMessages(), $errorMessages);
        
        $validators = $this->getOption("validators", array());
        $this->validators = array_merge($this->getDefaultValidators(), $validators);
    }
    
    function __clone() {
        if (!is_string($this->widget))
            $this->widget = clone $this->widget;
        
        if (!is_string($this->hiddenWidget))
            $this->hiddenWidget = clone $this->hiddenWidget;
        
        if (!empty($this->validators)) {
            $validators = array();
            foreach ($this->validators as $validator)
                $validators[] = clone $validator;
            $this->validators = $validators;
        }
    }
    
    // API
    
    public function clean($value) {
        $value = $this->normalize($value);
        $this->validate($value);
        $this->runValidators($value);
        return $value;
    }
    
    // Subclassing API
    
    function prepare($value) {
        return $value;
    }
    
    function normalize($value) {
        return $value;
    }
    
    function getBoundData($data, $initial) {
        return $data;
    }
    
    function getWidgetAttrs($widget) {
        return array();
    }
    
    function getDefaultValidators() {
        return array();
    }
    
    function getDefaultErrorMessages() {
        return array(
            "required" => translation::gettext("This field is required."),
            "invalid" => translation::gettext("Enter a valid value."),
        );
    }
    
    function getErrorMessages() {
        return $this->errorMessages;
    }
    
    // Protected
    
    function isRequired() {
        return $this->required;
    }
    
    function setRequired($required) {
        $this->required = $required;
        $this->getWidget()->setRequired($required);
    }
    
    function showHiddenInitial() {
        return $this->showHiddenInitial;
    }
    
    function getLabel() {
        return $this->label;
    }
    
    function getHelpText() {
        return $this->helpText;
    }
    
    function getWidget() {
        if (is_string($this->widget)) {
            $widget_cls = $this->widget;
            $widget = new $widget_cls();
            $this->widget = $widget;
        }
        return $this->widget;
    }
    
    function getHiddenWidget() {
        if (is_string($this->hiddenWidget)) {
            $widget_cls = $this->hiddenWidget;
            $widget = new $widget_cls();
            $this->hiddenWidget = $widget;
        }
        return $this->hiddenWidget;
    }
    
    function getOption($key, $default) {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }
    
    function validate($value) {
        if (validators::is_empty($value) && $this->required)
            throw new ValidationError($this->errorMessages["required"]);
    }
    
    function runValidators($value) {
        if (empty($value))
            return;
        $errors = array();
        foreach ($this->validators as $v) {
            try {
                $v->validate($value);
            } catch (ValidationError $e) {
                $code = $e->getErrorCode();
                if (array_key_exists($code, $this->errorMessages)) {
                    $message = $this->errorMessages[$code];
                    $params = $e->getParams();
                    if (!empty($params))
                        $message = vsprintf($message, $params);
                    $errors[] = $message;
                } else {
                    $errors = array_merge($errors, $e->getMessages());
                }
            }
        }
        if (!empty($errors))
            throw new ValidationError($errors);
    }
}

class CharField extends Field {
    var
        $minLength,
        $maxLength;
    
    function __construct(array $options=null) {
        parent::__construct($options);
        $this->setMinLength($this->getOption("minLength", null));
        $this->setMaxLength($this->getOption("maxLength", null));
    }
    
    function normalize($value) {
        if (empty($value))
            return "";
        return strval($value);
    }
    
    function getWidgetAttrs($widget) {
        if (!is_null($this->maxLength) && (
          is_a($widget, 'bjork\forms\TextInput') ||
          $widget == 'bjork\forms\TextInput'))
            return array("maxlength" => $this->maxLength);
        return parent::getWidgetAttrs($widget);
    }
    
    function setMinLength($len) {
        $this->minLength = $len;
        unset($this->validators['min']);
        if (!is_null($this->minLength))
            $this->validators['min'] = new validators\MinLengthValidator($this->minLength);
    }
    
    function setMaxLength($len) {
        $this->maxLength = $len;
        unset($this->validators['max']);
        if (!is_null($this->maxLength))
            $this->validators['max'] = new validators\MaxLengthValidator($this->maxLength);
    }
}

class IntegerField extends Field {
    var
        $minValue,
        $maxValue;
    
    function __construct(array $options=null) {
        parent::__construct($options);
        $this->minValue = $this->getOption("minValue", null);
        $this->maxValue = $this->getOption("maxValue", null);
        if (!is_null($this->minValue))
            $this->validators[] = new validators\MinValueValidator($this->minValue);
        if (!is_null($this->maxValue))
            $this->validators[] = new validators\MaxValueValidator($this->maxValue);
    }
    
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid" => translation::gettext("Enter a whole number."),
            "max_value" => translation::gettext("Ensure this value is less than or equal to %s."),
            "min_value" => translation::gettext("Ensure this value is greater than or equal to %s."),
        ));
    }
    
    function getDefaultValidators() {
        return array(new validators\IntegerValidator());
    }
    
    function normalize($value) {
        if (validators::is_empty($value))
            return null;
        if (!is_numeric($value))
            throw new ValidationError($this->errorMessages['invalid']);
        $value = 0 + $value;
        if (!is_int($value))
            throw new ValidationError($this->errorMessages['invalid']);
        return intval($value, 10);
    }
}

class FloatField extends IntegerField {
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid" => translation::gettext("Enter a number."),
        ));
    }
    
    function getDefaultValidators() {
        return array();
    }
    
    function normalize($value) {
        if (validators::is_empty($value))
            return null;
        if (!is_numeric($value))
            throw new ValidationError($this->errorMessages['invalid']);
        $value = 0.0 + $value;
        if (!is_float($value))
            throw new ValidationError($this->errorMessages['invalid']);
        return floatval($value);
    }
}

// class DecimalField extends Field {}
// class DateField extends Field {}
// class TimeField extends Field {}

class DateTimeField extends Field {
    
}

class RegexField extends CharField {
    var
        $regex;
    
    function __construct($regex, array $options=null) {
        parent::__construct($options);
        $this->regex = $regex;
        $this->validators[] = new validators\RegexValidator($this->regex);
    }
}

class EmailField extends CharField {
    function clean($value) {
        $value = trim($this->normalize($value));
        return parent::clean($value);
    }
    
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid" => translation::gettext("Enter a valid e-mail address."),
        ));
    }
    
    function getDefaultValidators() {
        return array(new validators\EmailValidator());
    }
}

// class FileField extends Field {}
// class ImageField extends FileField {}
// class URLField extends CharField {}

class BooleanField extends Field {
    var $widget = 'bjork\forms\CheckboxInput';
  
    function normalize($value) {
        if (is_string($value) && in_array(strtolower($value), array('false', '0')))
            $value = false;
        else
            $value = (bool)$value;
        $value = parent::normalize($value);
        if (!$value && $this->required)
            throw new ValidationError($this->errorMessages['required']);
        return $value;
    }
}

class NullBooleanField extends BooleanField {
    var $widget = 'bjork\forms\NullBooleanSelect';
  
    function normalize($value) {
        if (in_array($value, array(true, 'true', '1')))
            return true;
        else if (in_array($value, array(false, 'false', '0')))
            return false;
        return null;
    }
    
    function validate($value) { /* don't validate */ }
}

class ChoiceField extends Field {
    var $widget = 'bjork\forms\Select';
    
    var $choices;
    
    function __construct(array $choices=null, array $options=null) {
        parent::__construct($options);
        $this->setChoices(is_null($choices) ? array() : $choices);
    }
    
    function normalize($value) {
        if (validators::is_empty($value))
            return '';
        return $value;
    }
    
    function validate($value) {
        parent::validate($value);
        if (!empty($value) && !$this->isValidChoice($value))
            throw new ValidationError(sprintf($this->errorMessages["invalid_choice"], $value));
    }
    
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid_choice" => translation::gettext("Select a valid choice. %s is not one of the available choices."),
        ));
    }
    
    public function isValidChoice($value) {
        foreach ($this->choices as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($value == $k2)
                        return true;
                }
            } else {
                if ($value == $k)
                    return true;
            }
        }
        return false;
    }
    
    public function getChoices() {
        return $this->choices;
    }
    
    public function setChoices(array $choices) {
        $c = array();
        foreach ($choices as $key => $value)
            $c[$key] = $value;
        $this->choices = $c;
        $this->getWidget()->choices = $c;
    }
}

// class TypedChoiceField extends ChoiceField {}

class MultipleChoiceField extends ChoiceField {
    var
        $widget = 'bjork\forms\SelectMultiple',
        $hiddenWidget = 'bjork\forms\MultipleHiddenInput';
    
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid_list" => translation::gettext("Enter a list of values."),
        ));
    }
    
    function normalize($value) {
        if (empty($value))
            return array();
        else if (!is_array($value))
            throw new ValidationError($this->errorMessages['invalid_list']);
        return $value;
    }
    
    function validate($value) {
        if (empty($value) && $this->required)
            throw new ValidationError($this->errorMessages["required"]);
        foreach ($value as $val) {
            if (!$this->isValidChoice($val))
                throw new ValidationError(sprintf($this->errorMessages['invalid_choice'], $val));
        }
    }
}

// class TypedMultipleChoiceField extends MultipleChoiceField {}
// class ComboField extends Field {}

/**
* A Field that aggregates the logic of multiple Fields.
* 
* Its clean() method takes a "decompressed" list of values, which are then
* cleaned into a single value according to self.fields. Each value in
* this list is cleaned by the corresponding field -- the first value is
* cleaned by the first field, the second value is cleaned by the second
* field, etc. Once all fields are cleaned, the list of clean values is
* "compressed" into a single value.
* 
* Subclasses should not have to implement clean(). Instead, they must
* implement compress(), which takes a list of valid values and returns a
* "compressed" version of those values -- a single value.
* 
* You'll probably want to use this with MultiWidget.
*/
abstract class MultiValueField extends Field {
    var $fields;
    
    public function __construct(array $fields, array $options=null) {
        parent::__construct($options);
        foreach ($fields as $f)
            $f->required = false;
        $this->fields = $fields;
    }
    
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid" => translation::gettext("Enter a list of values."),
        ));
    }
    
    /*
    * Validates every value in the given list. A value is validated against
    * the corresponding Field in self.fields.
    * 
    * For example, if this MultiValueField was instantiated with
    * fields=(DateField(), TimeField()), clean() would call
    * DateField.clean(value[0]) and TimeField.clean(value[1]).
    */
    public function clean($value) {
        $clean_data = array();
        $errors = array();
        if (empty($value) || is_array($value)) {
            $list = array();
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (!validators::is_empty($v))
                        $list[] = $v;
                }
            }
            if (empty($value) || count($list) == 0) {
                if ($this->required)
                    throw new ValidationError($this->errorMessages["required"]);
                else
                    return $this->compress($list); // $list is empty array
            }
        } else {
            throw new ValidationError($this->errorMessages["invalid"]);
        }
        
        for ($i = 0; $i < count($this->fields); $i++) { 
            $field = $this->fields[$i];
            if (isset($value[$i]))
                $field_value = $value[$i];
            else
                $field_value = null;
            if ($this->required && validators::is_empty($field_value))
                throw new ValidationError($this->errorMessages["required"]);
            try {
                $clean_data[] = $field->clean($field_value);
            } catch (ValidationError $e) {
                $errors = array_merge($errors, $e->getMessages());
            }
        }
        
        if (count($errors) != 0)
            throw new ValidationError($errors);
        
        $out = $this->compress($clean_data);
        $this->validate($out);
        return $out;
    }
    
    function validate($value) {
        // pass
    }
    
    /*
    * Returns a single value for the given list of values. The values can be
    * assumed to be valid.
    * 
    * For example, if this MultiValueField was instantiated with
    * fields=(DateField(), TimeField()), this might return a datetime
    * object created by combining the date and time in data_list.
    */
    abstract protected function compress(array $data_list);
}

// class FilePathField extends ChoiceField {}
// class SplitDateTimeField extends MultiValueField {}
// class IPAddressField extends CharField {}

class SlugField extends CharField {
    function getDefaultErrorMessages() {
        return array_merge(parent::getDefaultErrorMessages(), array(
            "invalid" => translation::gettext("Enter a valid 'slug' consisting of letters, numbers, underscores or hyphens."),
        ));
    }
    
    function getDefaultValidators() {
        return array(new validators\SlugValidator());
    }
}
