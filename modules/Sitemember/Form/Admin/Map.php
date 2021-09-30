<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Map extends Engine_Form {

  public function init() {
  
    $this->setMethod('post')
			->setAttrib('class', 'global_form_box')
			->setTitle("Add Mapping");

    $option_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('option_id', null);
    if (!empty($option_id)) {
      $typeField = array('location', 'city', 'country');

      $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
      $metaTableName = $metaTable->info('name');

      $mapsTable = Engine_Api::_()->fields()->getTable('user', 'maps');
      $mapsTableName = $mapsTable->info('name');

      $select = $metaTable->select()
              ->setIntegrityCheck(false)
              ->from($metaTableName, array('label', 'field_id', 'type'))
              ->joinLeft($mapsTableName, "$metaTableName.field_id = $mapsTableName.child_id", null)
              ->where($mapsTableName . '.option_id = ?', $option_id)
              ->where($metaTableName . '.display IN (?)', array( '1', '2'))
              ->where($metaTableName . '.type IN (?)', (array) $typeField);
      $locationResult = $metaTable->fetchAll($select);

      if (count($locationResult) != 0) {
        $auTitle[0] = "";
        foreach ($locationResult as $locationResults) {
          $auTitle[$locationResults->field_id] = $locationResults->label;
        }

        $this->addElement('Select', 'profile_type', array(
            'label' => 'Location Type Field',
            'multiOptions' => $auTitle,
        ));

        $this->addElement('Button', 'yes_button', array(
            'label' => 'Add',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('yes_button', 'no_button', 'cancel'), 'buttons');
        $this->getDisplayGroup('buttons');
      } else {
      
        $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("You have currently not created any “Location” type profile field for this Profile Type. To create a “Location” type field for this Profile Type, please go to ‘Settings’ > ‘Profile Questions’ section of your ‘Admin Panel’.") . "</span></div>";

        //VALUE FOR LOGO PREVIEW.
        $this->addElement('Dummy', 'no_profile_type', array(
            'description' => $description,
        ));
        $this->no_profile_type->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Button', 'no_button', array(
            'label' => 'Cancel',
            'type' => 'submit',
            'ignore' => true,
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array('ViewHelper')
        ));
      }
    }
  }
}