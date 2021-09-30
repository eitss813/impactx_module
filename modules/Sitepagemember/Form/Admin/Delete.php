<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Form_Admin_Delete extends Engine_Form {

  public function init() {

    $this->setTitle('Delete Member Roles?')
        ->setDescription('Please click on the checkbox to select a roles from below and then click "Delete" to delete them. Note that these member roles will not be recoverable after being deleted.')
        ->setMethod('post')
        ->setAttrib('class', 'global_form_box');

    $categoryIdsArray = array();
    $categoryIdsArray[] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $roleParams = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->rolesParams($categoryIdsArray);

    foreach ($roleParams as $roleParam) {
      $this->addElement('Checkbox', 'role_name_' . $roleParam->role_id, array(
          'label' => $roleParam->role_name,
          'value' => 0,
      ));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Delete',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => 'or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}