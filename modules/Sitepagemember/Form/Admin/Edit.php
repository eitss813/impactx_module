<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Form_Admin_Edit extends Engine_Form {

  protected $_field;

  public function init() {
    $this
            ->setTitle('Edit Member Roles')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    $categoryIdsArray = array();
    $categoryIdsArray[] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $rolesParams = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->rolesParams($categoryIdsArray);

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