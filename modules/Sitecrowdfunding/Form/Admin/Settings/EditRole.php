<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_EditRole extends Engine_Form {

  protected $_field;

  public function init() {
    $this
            ->setTitle('Edit Member Roles')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
    $rolesParams = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->rolesByProjectIdParams($project_id);

    foreach ($rolesParams as $roleParam) {
      $this->addElement('Text', 'role_name_' . $roleParam->role_id, array(
          'label' => '',
          'required' => true,
      ));
    }

    $this->addElement('textarea', 'options', array(
        'style' => 'display:none;',
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
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
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField($rolesParams) {
    $this->_field = $rolesParams;

    foreach ($rolesParams as $roleParam) {
      $roleparam_field = 'role_name_' . $roleParam->role_id;
      $this->$roleparam_field->setValue($roleParam->role_name);
    }
  }

}