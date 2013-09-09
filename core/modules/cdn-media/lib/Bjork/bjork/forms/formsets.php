<?php

namespace bjork\forms\formsets;

use bjork\core\exceptions\ValidationError,
    bjork\forms,
    bjork\utils\translation;

const TOTAL_FORM_COUNT = 'TOTAL_FORMS';
const INITIAL_FORM_COUNT = 'INITIAL_FORMS';
const MAX_NUM_FORM_COUNT = 'MAX_NUM_FORMS';
const ORDERING_FIELD_NAME = 'ORDER';
const DELETION_FIELD_NAME = 'DELETE';

class ManagementForm extends forms\Form {} ManagementForm::fields(array(
    TOTAL_FORM_COUNT   => new forms\IntegerField(array('widget'=>'bjork\forms\HiddenInput')),
    INITIAL_FORM_COUNT => new forms\IntegerField(array('widget'=>'bjork\forms\HiddenInput')),
    MAX_NUM_FORM_COUNT => new forms\IntegerField(array('widget'=>'bjork\forms\HiddenInput', 'required'=>false)),
));

abstract class Formset implements \IteratorAggregate, \ArrayAccess, \Countable {
    
    protected static
        $form,
        $extra = 1,
        $maxNum = null,
        $canOrder = false,
        $canDelete = false;
    
    protected $nonFormErrors, $errors, $isBound, $forms, $deletedFormIndexes;
    
    var $data, $files, $initial, $prefix, $autoId, $errorClass;
    
    function __construct($data=null, $files=null, array $options=null) {
        if (is_null($options))
            $options = array();
        
        $this->isBound = !is_null($data) || !is_null($files);
        $this->data = !is_null($data) ? $data : array();
        $this->files = !is_null($files) ? $files : array();
        
        $this->prefix = isset($options['prefix']) ? $options['prefix'] : static::getDefaultPrefix();
        $this->autoId = isset($options['autoId']) ? $options['autoId'] : 'id_%s';
        $this->errorClass = isset($options['errorClass']) ? $options['errorClass'] : 'bjork\forms\ErrorList';
        $this->initial = isset($options['initial']) ? $options['initial'] : array();
        
        $this->errors = null;
        $this->nonFormErrors = null;
        $this->deleteFormIndexes = null;
        
        $this->constructForms();
    }
    
    function getIterator() {
        return new \ArrayIterator($this->forms);
    }
    
    function count() {
        return count($this->forms);
    }
    
    function offsetExists($offset) {
        return isset($this->forms[$offset]);
    }
    
    function offsetGet($offset) {
        return $this->forms[$offset];
    }
    
    function offsetSet($offset, $value) { /* nope */ }
    
    function offsetUnset($offset) { /* nope */ }
    
    // API
    
    /**
    * Hook for doing any extra formset-wide cleaning after Form.clean() has
    * been called on every form. Any ValidationError raised by this method
    * will not be associated with a particular form; it will be accesible via
    * formset.getNonFormErrors()
    */
    public function clean() {}
    
    public function isBound() {
        return $this->isBound;
    }
    
    public function isValid() {
        if (!$this->isBound())
            return false;
        $formsValid = true;
        $errors = $this->getErrors();
        for ($i = 0; $i < $this->getTotalFormCount(); $i++) {
            $form = $this->forms[$i];
            if (static::$canDelete) {
                if ($this->shouldDeleteForm($form))
                    continue;
            }
            if (count($errors[$i]) !== 0)
                $formsValid = false;
        }
        return $formsValid && count($this->getNonFormErrors()) === 0;
    }
    
    public function getErrors() {
        if (is_null($this->nonFormErrors))
            $this->fullClean();
        return $this->errors;
    }
    
    public function getNonFormErrors() {
        if (!is_null($this->nonFormErrors))
            return $this->nonFormErrors;
        return new $this->errorClass();
    }
    
    public function getTotalFormCount() {
        if ($this->isBound()) {
            $data = $this->getManagementForm()->getCleanedData();
            return $data[TOTAL_FORM_COUNT];
        }
        $initialForms = $this->getInitialFormCount();
        $totalForms = $initialForms + static::$extra;
        // Allow all existing related objects/inlines to be displayed,
        // but don't allow extra beyond max_num.
        if (is_int(static::$maxNum) && static::$maxNum >= 0) {
            if ($initialForms > static::$maxNum)
                $totalForms = $initialForms;
            else if ($totalForms > static::$maxNum)
                $totalForms = static::$maxNum;
        }
        return $totalForms;
    }
    
    public function getInitialFormCount() {
        if ($this->isBound()) {
            $data = $this->getManagementForm()->getCleanedData();
            return $data[INITIAL_FORM_COUNT];
        }
        // Use the length of the initial data if it's there, 0 otherwise.
        $initialForms = empty($this->initial) ? 0 : count($this->initial);
        if (is_int(static::$maxNum) && static::$maxNum >= 0)
            $initialForms = min($initialForms, static::$maxNum);
        return $initialForms;
    }
    
    public function getInitialForms() {
        return array_slice($this->forms, 0, $this->getInitialFormCount());
    }
    
    public function getExtraForms() {
        return array_slice($this->forms, $this->getInitialFormCount());
    }
    
    public function getManagementForm() {
        if ($this->isBound()) {
            $form = new ManagementForm($this->data, null, array(
                'autoId' => $this->autoId,
                'prefix' => $this->prefix,
            ));
            if (!$form->isValid())
                throw new ValidationError(
                    'ManagementForm data is missing or has been tampered with');
        } else {
            $form = new ManagementForm(null, null, array(
                'autoId' => $this->autoId,
                'prefix' => $this->prefix,
                'initial' => array(
                    TOTAL_FORM_COUNT => $this->getTotalFormCount(),
                    INITIAL_FORM_COUNT => $this->getInitialFormCount(),
                    MAX_NUM_FORM_COUNT => static::$maxNum,
                ),
            ));
        }
        return $form;
    }
    
    public function getDeletedForms() {
        if (!$this->isValid() || !static::$canDelete) {
            $classname = get_called_class();
            throw new \Exception(
              "'{$classname}::getDeletedForms' is not accessible because ".
              "the form is not valid or not allowed to delete.");
        }
        
        if (is_null($this->deleteFormIndexes)) {
            $deletedFormIndexes = array();
            for ($i = 0; $i < $this->getTotalFormCount(); $i++) { 
                $form = $this->forms[$i];
                if ($i >= $this->getInitialFormCount() && !$form->hasChanged())
                    continue;
                if ($this->shouldDeleteForm($form))
                    $deletedFormIndexes[] = $i;
            }
            $this->deleteFormIndexes = $deletedFormIndexes;
        }
        
        $forms = array();
        foreach ($this->deleteFormIndexes as $i) {
            $forms[] = $this->forms[$i];
        }
        return $forms;
    }
    
    // Protected
    
    function fullClean() {
        $this->errors = array();
        if (!$this->isBound())
            return;
        for ($i = 0; $i < $this->getTotalFormCount(); $i++) {
            $form = $this->forms[$i];
            $this->errors[] = $form->getErrors();
        }
        try {
            $this->clean();
        } catch (ValidationError $e) {
            $this->nonFormErrors = new $this->errorClass($e->getMessages());
        }
    }
    
    function constructForms() {
        $this->forms = array();
        for ($i = 0; $i < $this->getTotalFormCount(); $i++)
            $this->forms[] = $this->constructForm($i);
    }
    
    function constructForm($i, array $extraOptions=null) {
        $extraOptions = is_null($extraOptions) ? array() : $extraOptions;
        $defaults = array(
            'autoId' => $this->autoId,
            'prefix' => $this->addPrefix($i));
        if (!empty($this->initial))
            if (isset($this->initial[$i]))
                $defaults['initial'] = $this->initial[$i];
        if ($i >= $this->getInitialFormCount())
            $defaults['emptyPermitted'] = true;
        $defaults = array_merge($defaults, $extraOptions);
        $formClass = static::$form;
        if ($this->isBound()) {
            $data = $this->data;
            $files = $this->files;
        } else {
            $data = null;
            $files = null;
        }
        $form = $this->constructFormInstance($i, $formClass, $data, $files, $defaults);
        $this->addFields($form, $i);
        return $form;
    }
    
    function constructFormInstance($i, $form_class, $data, $files, array $options) {
        return new $form_class($data, $files, $options);
    }
    
    function addPrefix($index) {
        return "{$this->prefix}-{$index}";
    }
    
    function addFields($form, $index) {
        if (static::$canOrder) {
            if (!is_null($index) && $index < $this->getInitialFormCount()) {
                $form->fields[ORDERING_FIELD_NAME] = new forms\IntegerField(array(
                    'label' => translation::gettext('Order'),
                    'initial' => $index + 1,
                    'required' => false,
                ));
            } else {
                $form->fields[ORDERING_FIELD_NAME] = new forms\IntegerField(array(
                    'label' => translation::gettext('Order'),
                    'required' => false,
                ));
            }
        }
        if (static::$canDelete) {
            $form->fields[DELETION_FIELD_NAME] = new forms\BooleanField(array(
                'label' => translation::gettext('Delete'),
                'required' => false,
            ));
        }
    }
    
    function shouldDeleteForm($form) {
        $field = $form->fields[DELETION_FIELD_NAME];
        $rawValue = $form->getRawValue(DELETION_FIELD_NAME);
        return $field->clean($rawValue);
    }
    
    static function getDefaultPrefix() {
        return 'form';
    }
}
