<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminAbstract.php 9790 2012-09-27 23:12:26Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Yndynamicform
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author
 */
class Yndynamicform_AdminFormFieldsController extends Core_Controller_Action_Admin
{
  protected $_fieldType = 'yndynamicform_entry';

  protected $_moduleName;

  public function init()
  {
    // Parse module name from class
    if( !$this->_moduleName ) {
      $this->_moduleName = substr(get_class($this), 0, strpos(get_class($this), '_'));
    }

    // Try to set item type to module name (usually an item type)
    if( !$this->_fieldType ) {
      $this->_fieldType = Engine_Api::deflect($this->_moduleName);
    }

    if( !$this->_fieldType || !$this->_moduleName || !Engine_APi::_()->hasItemType($this->_fieldType) ) {
      throw new Fields_Model_Exception('Invalid fieldType or modulePath');
    }

    $this->view->fieldType = $this->_fieldType;

    // Hack up the view paths
    $this->view->addHelperPath(dirname(dirname(__FILE__)) . '/views/helpers', 'Fields_View_Helper');
    $this->view->addScriptPath(dirname(dirname(__FILE__)) . '/views/scripts');

    $this->view->addHelperPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/helpers', $this->_moduleName . '_View_Helper');
    $this->view->addScriptPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/scripts');
  }

  public function indexAction()
  {
    $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array());
    $form_id = $this->_getParam('id');
    $form = Engine_Api::_()->getItem('yndynamicform_form', $form_id);
    if (!$form->getIdentity()) return;

    $this->view->form = $form;
    // Add type
    $categories = Engine_Api::_()->yndynamicform()->getFieldInfo('categories');
    $advanced = Engine_Api::_()->yndynamicform()->getFieldInfo('advanced_fields');
    $ua = Engine_Api::_()->yndynamicform()->getFieldInfo('user_analytics_fields');
    $types = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');
    $fieldByCat = array();
    $standardFields = array();
    $advancedFields = array();
    $analyticsFields = array();
    foreach( $types as $fieldType => $info ) {
      $fieldByCat[$info['category']][$fieldType] = $info['label'];
      if (in_array($fieldType, $advanced))
        $advancedFields[$fieldType] = $info['label'];
      else if (in_array($fieldType, $ua))
        $analyticsFields[$fieldType] = $info['label'];
    }
    foreach( $categories as $catType => $categoryInfo ) {
      $label = $categoryInfo['label'];
      $standardFields[$label] = $fieldByCat[$catType];
    }
    $this->view->standardFields = $standardFields;
    $this->view->advancedFields = $advancedFields;
    $this->view->analyticsFields = $analyticsFields;

    // Set data
    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta($this->_fieldType);
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($this->_fieldType);

    // Get top level fields
    $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
    $topLevelFields = array();
    foreach( $topLevelMaps as $map ) {
      $field = $map->getChild();
      $topLevelFields[$field->field_id] = $field;
    }
    $this->view->topLevelMaps = $topLevelMaps;
    $this->view->topLevelFields = $topLevelFields;


    // Get top level field
    // Only allow one top level field
    if( count($topLevelFields) > 1 ) {
      throw new Engine_Exception('Only one top level field is currently allowed');
    }
    $topLevelField = array_shift($topLevelFields);
    // Only allow the "profile_type" field to be a top level field (for now)
    if( $topLevelField->type !== 'profile_type' ) {
      throw new Engine_Exception('Only profile_type can be a top level field');
    }
    $this->view->topLevelField = $topLevelField;
    $this->view->topLevelFieldId = $topLevelField->field_id;

    // Get top level options
    $topLevelOptions = array();
    foreach( $optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option ) {
      $topLevelOptions[$option->option_id] = $option->label;
    }
    $this->view->topLevelOptions = $topLevelOptions;

    // Get selected option
    $option_id = $this->_getParam('option_id');
    if( empty($option_id) || empty($topLevelOptions[$option_id]) ) {
      $option_id = current(array_keys($topLevelOptions));
    }
    $topLevelOption = $optionsData->getRowMatching('option_id', $option_id);
    if( !$topLevelOption ) {
      throw new Engine_Exception('Missing option');
    }
    $this->view->topLevelOption = $topLevelOption;
    $this->view->topLevelOptionId = $topLevelOption->option_id;

    // Get second level fields
    $secondLevelMaps = array();
    $secondLevelFields = array();
    if( !empty($option_id) ) {
      $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
      if( !empty($secondLevelMaps) ) {
        foreach( $secondLevelMaps as $map ) {
          $secondLevelFields[$map->child_id] = $map->getChild();
        }
      }
    }
    $this->view->secondLevelMaps = $secondLevelMaps;
    $this->view->secondLevelFields = $secondLevelFields;
  }

  // Headings
  public function headingCreateAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

    // Create form
    $this->view->form = $form = new Yndynamicform_Form_Admin_Heading();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
        'option_id' => $option->option_id,
        'type' => 'heading',
        'display' => 1
    ), $form->getValues()));

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->option = $option->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function headingEditAction()
  {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Create form
    $this->view->form = $form = new Yndynamicform_Form_Admin_Heading();
    $form->submit->setLabel('Edit Heading');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($field->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    Engine_Api::_()->fields()->editField($this->_fieldType, $field, $form->getValues());

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  // Fields
  public function fieldCreateAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    $formItem = Engine_Api::_()->getItemTable('yndynamicform_form')->fetchAll()->getRowMatching('option_id', $this->_getParam('option_id'));

    // Check type param and get form class
    $cfType = $this->_getParam('type');
    $adminFormClass = null;
    if( !empty($cfType) ) {
      $adminFormClass = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'adminFormClass');
      $fieldLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'label');
    }
    if( empty($adminFormClass) || !@class_exists($adminFormClass) ) {
      $adminFormClass = 'Yndynamicform_Form_Admin_Field';
    }

    // Create form
    $this->view->form = $form = new $adminFormClass();
    $form->setTitle('Create Form Field: ' . $fieldLabel);

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($this->_getAllParams());

      // special case for page break
      if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
        if( is_array($formItem->page_break_config) ){
          $form->populate($formItem->page_break_config);
          $form->populate(array(
            'page_names_hidden' => json_encode($formItem->page_break_config['page_names']),
          ));
        }
        $totalPages = $this->getTotalPageBreaks() + 2;
        $form->populate(array(
            'total_pages' => $totalPages,
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $params = $this ->_getAllParams();
    $values = $form->getValues();
    unset($params['conditional_logic_tpl']);
    $values['conditional_enabled'] = $params['conditional_enabled'];
    $values['conditional_logic'] = $params['conditional_logic'];
    $values['conditional_show'] = $params['conditional_show'];
    $values['conditional_scope'] = $params['conditional_scope'];
    $values['page_names'] = $params['page_names'];
    if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_UserAnalytics')
      $values['label'] = $fieldLabel;

    $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
        'option_id' => ( is_object($option) ? $option->option_id : '0' ),
    ), $values));

    // save filed config to form page break config
    if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
      unset($field->config['conditional_enabled']);
      unset($field->config['conditional_logic']);
      $formItem->page_break_config = $field->config;
      $formItem->save();
    }

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->option = is_object($option) ? $option->toArray() : array('option_id' => '0');
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function fieldEditAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    $formItem = Engine_Api::_()->getItemTable('yndynamicform_form')->fetchAll()->getRowMatching('option_id', $this->_getParam('option_id'));
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Check type param and get form class
    $cfType = $this->_getParam('type', $field->type);
    $adminFormClass = null;
    if( !empty($cfType) ) {
      $adminFormClass = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'adminFormClass');
      $fieldLabel = Engine_Api::_()->yndynamicform()->getFieldInfo($cfType, 'label');
    }
    if( empty($adminFormClass) || !@class_exists($adminFormClass) ) {
      $adminFormClass = 'Yndynamicform_Form_Admin_Field';
    }

    // Create form
    $this->view->form = $form = new $adminFormClass();
    $form->setTitle('Edit Form Field: ' . $fieldLabel);

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($field->toArray());
      $form->populate($this->_getAllParams());
      if( is_array($field->config) ){
        $form->populate($field->config);
        $form->populate(array(
            'conditional_logic'=> json_encode($field->config['conditional_logic'])
        ));
      }
      // special case for page break
      if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
        if( is_array($formItem->page_break_config) ){
          $form->populate($formItem->page_break_config);
          $form->populate(array(
              'page_names_hidden' => json_encode($formItem->page_break_config['page_names']),
          ));
        }
        $totalPages = $this->getTotalPageBreaks() + 1;
        $form->populate(array(
            'total_pages' => $totalPages,
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $params = $this ->_getAllParams();
    $values = $form->getValues();
    unset($params['conditional_logic_tpl']);
    $values['conditional_enabled'] = $params['conditional_enabled'];
    $values['conditional_logic'] = $params['conditional_logic'];
    $values['conditional_show'] = $params['conditional_show'];
    $values['conditional_scope'] = $params['conditional_scope'];

    Engine_Api::_()->fields()->editField($this->_fieldType, $field, $values);

    // special case for page break
    if ($adminFormClass == 'Yndynamicform_Form_Admin_Field_PageBreak') {
      // save filed config to form page break config
      unset($params['label']);
      unset($params['description']);
      unset($params['conditional_enabled']);
      unset($params['conditional_logic']);
      $formItem->page_break_config = $params;
      $formItem->save();
    }

    // edit the first page break too

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function fieldDeleteAction()
  {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    $this->view->form = $form = new Engine_Form(array(
        'method' => 'post',
        'action' => $_SERVER['REQUEST_URI'],
        'elements' => array(
            array(
                'type' => 'submit',
                'name' => 'submit',
            )
        )
    ));

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $this->view->status = true;
    Engine_Api::_()->fields()->deleteField($this->_fieldType, $field);
  }

  // Option

  public function optionCreateAction()
  {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
    $label = $this->_getParam('label');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Create new option
    $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
        'label' => $label,
    ));

    $this->view->status = true;
    $this->view->option = $option->toArray();
    $this->view->field = $field->toArray();

    // Re-render all maps that have this options's field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function optionEditAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    $field = Engine_Api::_()->fields()->getField($option->field_id, $this->_fieldType);

    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Option();
    $form->submit->setLabel('Edit Choice');

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($option->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    Engine_Api::_()->fields()->editOption($this->_fieldType, $option, $form->getValues());

    // Process
    $this->view->status = true;
    $this->view->form = null;
    $this->view->option = $option->toArray();
    $this->view->field = $field->toArray();

    // Re-render all maps that have this options's field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function optionDeleteAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Delete all values
    $option_id = $option->option_id;
    Engine_Api::_()->fields()->deleteOption($this->_fieldType, $option);
  }

  public function mapCreateAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    //$field = Engine_Api::_()->fields()->getField($this->_getParam('parent_id'), $this->_fieldType);

    $child_id = $this->_getParam('child_id', $this->_getParam('field_id'));
    $label = $this->_getParam('label');
    $child = null;

    if( $child_id ) {
      $child = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowMatching('field_id', $child_id);
    } else if( $label ) {
      $child = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowsMatching('label', $label);
      if( count($child) > 1 ) {
        throw new Fields_Model_Exception('Duplicate label');
      }
      $child = current($child);
    } else {
      throw new Fields_Model_Exception('No child field specified');
    }

    if( !($child instanceof Fields_Model_Meta) ) {
      throw new Fields_Model_Exception('No child field found');
    }

    $fieldMap = Engine_Api::_()->fields()->createMap($child, $option);

    $this->view->field = $child->toArray();
    $this->view->fieldMap = $fieldMap->toArray();

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $option->field_id),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $option->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function mapDeleteAction()
  {
    $map = Engine_Api::_()->fields()->getMap($this->_getParam('child_id'), $this->_getParam('option_id'), $this->_fieldType);
    Engine_Api::_()->fields()->deleteMap($map);
  }


  // Other

  public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Get params
    $fieldOrder = (array) $this->_getParam('fieldOrder');
    $optionOrder = (array) $this->_getParam('optionOrder');

    // Sort
    ksort($fieldOrder, SORT_NUMERIC);
    ksort($optionOrder, SORT_NUMERIC);

    // Get data
    $mapData = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType);
    $optionData = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType);

    // Parse fields (maps)
    $i = 0;
    foreach( $fieldOrder as $index => $ids ) {
      $map = $mapData->getRowMatching(array(
          'field_id' => $ids['parent_id'],
          'option_id' => $ids['option_id'],
          'child_id' => $ids['child_id'],
      ));
      $map->order = ++$i;
      $map->save();
    }

    // Parse options
    $i = 0;
    foreach( $optionOrder as $index => $ids ) {
      $option = $optionData->getRowMatching('option_id', $ids['suboption_id']);
      $option->order = ++$i;
      $option->save();
    }

    // Flush cache
    $mapData->getTable()->flushCache();
    $metaData->getTable()->flushCache();
    $optionData->getTable()->flushCache();

    $this->view->status = true;
  }

  public function getTotalPageBreaks()
  {
    $option_id = $this->_getParam('option_id', 0);
    $count = 0;
    // get first page break
    $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('option_id', $option_id);
    foreach( $fieldMaps as $map ) {
      $field = $map->getChild();
      if ($field->type == 'page_break') {
        $count++;
      }
    }

    return $count;
  }
}