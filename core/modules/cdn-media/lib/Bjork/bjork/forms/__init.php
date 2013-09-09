<?php

namespace bjork\forms;

require_once __DIR__.'/../core/exceptions.php';
require_once __DIR__.'/fields.php';
require_once __DIR__.'/widgets.php';
require_once __DIR__.'/utils.php';

use bjork\core\exceptions\ValidationError;

const NON_FIELD_ERRORS_KEY = "__all__";

// require_once 'bjork/forms/fields.php';
// require_once 'bjork/forms/widgets.php';
// require_once 'bjork/forms/utils.php';

function pretty_name($name) {
    if (empty($name))
        return '';
    return ucfirst(preg_replace("/_/", " ", $name));
}

function get_class_fields($base_fields_map, $classname) {
    while ($classname && !isset($base_fields_map[$classname])) {
        $classname = get_parent_class($classname);
        if ($classname === false || $classname == 'bjork\forms\Form')
            $classname = null;
    }
    return $classname ? $base_fields_map[$classname] : array();
}

abstract class FormBase implements \Iterator, \ArrayAccess, \Countable {
    var $fields;
    
    private $_keys, $_keyIndex;
    
    static $baseFields = array();
    
    public static function fields(array $fields) {
        $classname = get_called_class();
        $baseFields = array();
        
        // find the parent class
        $parentClassname = get_parent_class($classname);
        if ($parentClassname === false || $parentClassname == 'bjork\forms\Form')
            $parentClassname = null;
        
        // get parent fields first
        if ($parentClassname !== null && isset(self::$baseFields[$parentClassname])) {
            $parentFields = self::$baseFields[$parentClassname];
            foreach ($parentFields as $name => $field)
                $baseFields[$name] = $field;
        }
        
        // then append the new fields
        foreach ($fields as $name => $field)
            $baseFields[$name] = $field;
        
        self::$baseFields[$classname] = $baseFields;
    }
    
    function __construct() {
        $classname = get_called_class();
        $this->fields = array();
        foreach (get_class_fields(self::$baseFields, $classname) as $name => $field)
            $this->fields[$name] = clone $field;
    }
    
    function current() {
        return $this[$this->key()];
    }
    
    function valid() {
        return isset($this->_keys[$this->_keyIndex]);
    }
    
    function key() {
        return $this->_keys[$this->_keyIndex];
    }
    
    function next() {
        ++$this->_keyIndex;
    }
    
    function rewind() {
        $this->_keyIndex = 0;
        $this->_keys = array_keys($this->fields);
    }
    
    function count() {
        return count($this->fields);
    }
    
    function offsetExists($offset) {
        return isset($this->fields[$offset]);
    }
    
    function offsetGet($offset) {
        return new BoundField($this, $this->fields[$offset], $offset);
    }
    
    function offsetSet($offset, $value) { /* nope */ }
    
    function offsetUnset($offset) { /* nope */ }
}

class Form extends FormBase {
    protected
        $cleanedData,
        $changedData,
        $errors,
        $isBound;
    
    var $data, $files, $initial, $prefix, $labelSuffix, $autoId,
        $emptyPermitted, $errorClass;
    
    function __construct($data=null, $files=null, array $options=null) {
        parent::__construct();
        
        if (is_null($options))
            $options = array();
        
        $this->isBound = !is_null($data) || !is_null($files);
        
        if (is_null($data))
            $data = array();
        if (is_null($files))
            $files = array();
        $this->data = $data;
        $this->files = $files;
        
        $this->changedData = null;
        $this->cleanedData = null;
        $this->errors = null;
        
        $this->errorClass = isset($options["errorClass"]) ? $options["errorClass"] : 'bjork\forms\ErrorList';
        $this->prefix = isset($options["prefix"]) ? $options["prefix"] : null;
        $this->initial = isset($options["initial"]) ? $options["initial"] : array();
        $this->labelSuffix = isset($options["labelSuffix"]) ? $options["labelSuffix"] : ":";
        $this->autoId = isset($options["autoId"]) ? $options["autoId"] : "id_%s";
        $this->emptyPermitted = isset($options["emptyPermitted"]) ? $options["emptyPermitted"] : false;
    }
    
    // API
    
    public function isBound() {
        return $this->isBound;
    }
    
    public function isValid() {
        $errors = $this->getErrors();
        $res = $this->isBound() && count($errors) == 0;
        return $res;
    }
    
    public function isMultipart() {
        foreach ($this->fields as $field)
            if ($field->getWidget()->needsMultipartForm())
                return true;
        return false;
    }
    
    public function hasChanged() {
        $data = $this->getChangedData();
        $res = !empty($data);
        return $res;
    }
    
    public function getChangedData() {
        if (is_null($this->changedData)) {
            $this->changedData = array();
            
            foreach ($this->fields as $name => $field) {
                $prefixedName = $this->addPrefix($name);
                $dataValue = $field->getWidget()->valueFromDataDict($this->data, $this->files, $prefixedName);
                if (!$field->showHiddenInitial) {
                    $initialValue = isset($this->initial[$name]) ? $this->initial[$name] : $field->initial;
                } else {
                    $initialPrefixedName = $this->addInitialPrefix($name);
                    $hiddenWidget = $field->getHiddenWidget();
                    $initialValue = $hiddenWidget->valueFromDataDict($this->data, $this->files, $initialPrefixedName);
                }
                
                if ($field->getWidget()->hasChanged($initialValue, $dataValue)) {
                    $this->changedData[] = $name;
                }
            }
        }
        return $this->changedData;
    }
    
    public function getCleanedData() {
        $classname = get_called_class();
        if (is_null($this->cleanedData))
            throw new \Exception("'{$classname}::getCleanedData' is not accessible because the form has not been cleaned");
        return $this->cleanedData;
    }
    
    public function getErrors() {
        if (is_null($this->errors))
            $this->fullClean();
        return $this->errors;
    }
    
    public function getNonFieldErrors() {
        $errors = $this->getErrors();
        return isset($errors[NON_FIELD_ERRORS_KEY])
            ? $errors[NON_FIELD_ERRORS_KEY]
            : new $this->errorClass();
    }
    
    public function getHiddenFields() {
        foreach ($this as $field)
            if ($field->isHidden())
                $o[] = $field;;
        return $o;
    }
    
    public function getVisibleFields() {
        foreach ($this as $field)
            if (!$field->isHidden())
                $o[] = $field;;
        return $o;
    }
    
    function getRawValue($fieldname) {
        $field = $this->fields[$fieldname];
        $prefix = $this->addPrefix($fieldname);
        return $field->getWidget()->valueFromDataDict($this->data, $this->files, $prefix);
    }
    
    // Subclassing API
    
    protected function clean() {
        return $this->cleanedData;
    }
    
    // Protected
    
    function addPrefix($fieldname) {
        if (!empty($this->prefix))
            return "{$this->prefix}-{$fieldname}";
        return $fieldname;
    }
    
    function addInitialPrefix($fieldname) {
        return "initial-" . $this->addPrefix($fieldname);
    }
    
    function fullClean() {
        $this->errors = new ErrorDict();
        if (!$this->isBound)
            return;
        $this->cleanedData = array();
        if ($this->emptyPermitted && !$this->hasChanged())
            return;
        $this->cleanFields();
        $this->cleanForm();
        if (count($this->errors) !== 0)
            $this->cleanedData = null;
    }
    
    function cleanFields() {
        foreach ($this->fields as $name => $field) {
            $value = $field->getWidget()->valueFromDataDict($this->data, $this->files, $this->addPrefix($name));
            try {
                if (is_subclass_of($field, 'forms\FileField')) {
                    $initial = isset($this->initial[$name]) ?  $this->initial[$name] : $field->initial;
                    $value = $field->clean($value, $initial);
                } else {
                    $value = $field->clean($value);
                }
                $this->cleanedData[$name] = $value;
                $method = "clean_$name";
                if (method_exists($this, $method)) {
                    $value = $this->$method();
                    $this->cleanedData[$name] = $value;
                }
            } catch (ValidationError $e) {
                $this->errors[$name] = new $this->errorClass($e->getMessages());
                unset($this->cleanedData[$name]);
            }
        }
    }
    
    function cleanForm() {
        try {
            $this->cleanedData = $this->clean();
        } catch (ValidationError $e) {
            $this->errors[NON_FIELD_ERRORS_KEY] = new $this->errorClass($e->getMessages());
        }
    }
}

class BoundField {
    var
        $form,
        $field,
        $name,
        $label,
        $helpText,
        $htmlName,
        $htmlInitialName,
        $htmlInitialId;
    
    function __construct(Form $form, Field $field, $name) {
        $this->form = $form;
        $this->field = $field;
        $this->name = $name;
        $this->htmlName = $form->addPrefix($name);
        $this->htmlInitialName = $form->addInitialPrefix($name);
        $this->htmlInitialId = $form->addInitialPrefix($this->getAutoId());
        $this->helpText = $field->getHelpText();
        if (is_null($field->getLabel()))
            $this->label = pretty_name($name);
        else
            $this->label = $field->getLabel();
    }
    
    function __toString() {
        if ($this->field->showHiddenInitial())
            return strval($this->asWidget()) . strval($this->asHidden(null, $onlyInitial=true));
        return strval($this->asWidget());
    }
    
    // API
    
    public function isHidden() {
        return $this->field->getWidget()->isHidden();
    }
    
    public function isRequired() {
        return $this->field->getWidget()->isRequired();
    }
    
    public function hasErrors() {
        $errors = $this->form->getErrors();
        return isset($errors[$this->name]);
    }
    
    public function getErrors() {
        $errors = $this->form->getErrors();
        return isset($errors[$this->name]) ? $errors[$this->name] : new $this->form->errorClass();
    }
    
    public function getHtmlName() {
        return $this->htmlName;
    }
    
    public function getIdForLabel() {
        $widget = $this->field->getWidget();
        $id = isset($widget->attrs["id"]) ? $widget->attrs["id"] : $this->getAutoId();
        return $widget->getIdForLabel($id);
    }
    
    public function getAutoId() {
        $autoId = $this->form->autoId;
        if (!empty($autoId) && false !== strpos($autoId, "%s"))
            return sprintf($autoId, $this->htmlName);
        else if (!empty($autoId))
            return $this->htmlName;
        return "";
    }
    
    public function getData() {
        return $this->field->getWidget()->valueFromDataDict(
            $this->form->data,
            $this->form->files,
            $this->htmlName);
    }
    
    public function getValue() {
        $initial = isset($this->form->initial[$this->name])
            ? $this->form->initial[$this->name]
            : $this->field->initial;
        if (!$this->form->isBound()) {
            $data = $initial;
        } else {
            $data = $this->field->getBoundData($this->getData(), $initial);
        }
        return $this->field->prepare($data);
    }
    
    public function getLabelTag($contents=null, array $attrs=null) {
        if (is_null($contents))
            $contents = $this->label;
        $widget = $this->field->getWidget();
        $id = isset($widget->attrs["id"]) ? $widget->attrs["id"] : $this->getAutoId();
        if (!empty($id)) {
            $attributes = "";
            if (!empty($attrs)) {
                foreach ($attrs as $name => $value) {
                    $attributes .= " $name=\"$value\"";
                }
            }
            $contents = sprintf("<label for=\"%s\"%s>%s</label>",
                $widget->getIdForLabel($id),
                $attributes,
                $contents);
        }
        return $contents;
    }
    
    public function asText(array $attrs=null, $onlyInitial=false) {
        $widget = new TextInput();
        return $this->asWidget($widget, $attrs, $onlyInitial);
    }
    
    public function asTextarea(array $attrs=null, $onlyInitial=false) {
        $widget = new TextArea();
        return $this->asWidget($widget, $attrs, $onlyInitial);
    }
    
    public function asHidden(array $attrs=null, $onlyInitial=false) {
        $widget = $this->field->getHiddenWidget();
        return $this->asWidget($widget, $attrs, $onlyInitial);
    }
    
    public function asWidget(Widget $widget=null, array $attrs=null, $onlyInitial=false) {
        if (is_null($widget))
            $widget = $this->field->getWidget();
        if (is_null($attrs))
            $attrs = array();
        
        $autoId = $this->getAutoId();
        if (!empty($autoId) && !array_key_exists("id", $attrs) && !array_key_exists("id", $widget->attrs)) {
            if (!$onlyInitial)
                $attrs["id"] = $autoId;
            else
                $attrs["id"] = $this->htmlInitialId;
        }
        
        if (!$onlyInitial)
            $name = $this->htmlName;
        else
            $name = $this->htmlInitialName;
        return $widget->render($name, $this->getValue(), $attrs);
    }
    
    // protected
    
    function getField() {
        return $this->field;
    }
}
