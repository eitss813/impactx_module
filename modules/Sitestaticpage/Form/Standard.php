<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Standard.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Form_Standard extends Fields_Form_Standard {

    protected $_user_id;
    protected $_item;
    protected $_processedValues = array();
    protected $_topLevelId;
    protected $_topLevelValue;
    protected $_isCreation = false;

    public function __construct($options = null) {
        Engine_FOrm::enableForm($this);
        self::enableForm($this);

        parent::__construct($options);
    }

    public static function enableForm(Zend_Form $form) {
        $form
                ->addPrefixPath('Fields_Form_Element', APPLICATION_PATH . '/application/modules/Fields/Form/Element', 'element');
    }

    /* General */

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function setTopLevelId($id) {
        $this->_topLevelId = $id;
        return $this;
    }

    public function getTopLevelId() {
        return $this->_topLevelId;
    }

    public function setTopLevelValue($val) {
        $this->_topLevelValue = $val;
        return $this;
    }

    public function getTopLevelValue() {
        return $this->_topLevelValue;
    }

    public function setIsCreation($flag = true) {
        $this->_isCreation = (bool) $flag;
        return $this;
    }

    public function setProcessedValues($values) {
        $this->_processedValues = $values;
        $this->_setFieldValues($values);
        return $this;
    }

    public function getProcessedValues() {
        return $this->_processedValues;
    }

    public function getFieldMeta() {
        return Engine_Api::_()->fields()->getFieldsMeta($this->getItem());
    }

    public function getFieldStructure() {
        // Let's allow fallback for no profile type (for now at least)
        if (!$this->_topLevelId || !$this->_topLevelValue) {
            $this->_topLevelId = null;
            $this->_topLevelValue = null;
        }
        return Engine_Api::_()->fields()->getFieldsStructureFull($this->getItem(), $this->_topLevelId, $this->_topLevelValue);
    }

    public function getUserId() {
        return $this->_user_id;
    }

    public function setUserId($user_id) {
        $this->_user_id = $user_id;
        return $this;
    }

    public function init() {

        $option_id = parent::getTopLevelValue();
            
        //GET FORM HEADING AND FORM DESCRIPTION
        $db = Engine_Db_Table::getDefaultAdapter();
        $result = $db->select()
                ->from('engine4_sitestaticpage_page_fields_options', array('form_heading', 'form_description', 'button_text'))
                ->where('option_id = ?', $option_id)
                ->query()
                ->fetchAll();
        if (!empty($result)) {
            $this->setTitle($result[0]['form_heading'])->setDescription($result[0]['form_description']);
        }
        parent::init();


        $this->loadDefaultDecorators();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->setAction('')->getDecorator('HtmlTag')->setOption('class', '');

        $this->generate();

        if (empty($result[0]['button_text']))
            $button_label = 'Submit';
        else
            $button_label = $result[0]['button_text'];
        $this->addElement('hidden', 'profile_id', array('order' => 6001,'value' => $option_id));
        $this->addElement('Button', 'submit', array(
            'label' => $button_label,
            'type' => 'submit',
            'order' => 10000,
        ));
    }

    public function generate() {
        $option_id = parent::getTopLevelValue();
        $struct = $this->getFieldStructure();

        $orderIndex = 0;

        foreach ($struct as $fskey => $map) {
            $field = $map->getChild();
            // Skip fields hidden on signup
            if (isset($field->show) && !$field->show && $this->_isCreation) {
                continue;
            }

            // Add field and load options if necessary
            $params = Engine_Api::_()->sitestaticpage()->getElementParams($this->getItem(), array(), $this->_user_id, $field);

            $key = $map->getKey();
            // If value set in processed values, set in element
            if (!empty($this->_processedValues[$field->field_id])) {
                $params['options']['value'] = $this->_processedValues[$field->field_id];
            }

            if (!@is_array($params['options']['attribs'])) {
                $params['options']['attribs'] = array();
            }

            // Heading
            if ($params['type'] == 'Heading') {
                $params['options']['value'] = Zend_Registry::get('Zend_Translate')->_($params['options']['label']);
                unset($params['options']['label']);
            }

            // Order
            // @todo this might cause problems, however it will prevent multiple orders causing elements to not show up
            $params['options']['order'] = $orderIndex++;

            $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
            unset($params['options']['alias']);
            unset($params['options']['publish']);
            $this->addElement($inflectedType, $key, $params['options']);

            $element = $this->getElement($key);

            if (method_exists($element, 'setFieldMeta')) {
                $element->setFieldMeta($field);
            }

            // Set attributes for hiding/showing fields using javscript
            $classes = 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
            $element->setAttrib('class', $classes);

            //
            if ($field->canHaveDependents()) {
                $element->setAttrib('onchange', 'changeFields(this)');
            }

            // Set custom error message
            if ($field->error) {
                $element->addErrorMessage($field->error);
            }

            if ($field->isHeading()) {
                $element->removeDecorator('Label')
                        ->removeDecorator('HtmlTag')
                        ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
            }
        }

        if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
            $option_table = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'options');
            $recaptcha = $option_table->select()->from($option_table->info('name'), 'recaptcha')
                    ->where('option_id = ?', $option_id)
                    ->query()
                    ->fetchColumn();
            if ($recaptcha) {
                Zend_Registry::get('Zend_View')->recaptcha($this);
            }
           
            $zend_recaptcha = Zend_Registry::isRegistered('Zend_Recaptcha_Value') ? Zend_Registry::get('Zend_Recaptcha_Value') : null;
            
            if(!$zend_recaptcha) {
               Zend_Registry::set('Zend_Recaptcha_Value', $recaptcha);
            }
            
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Save',
            'type' => 'submit',
            'order' => 10000,
        ));
    }

}
