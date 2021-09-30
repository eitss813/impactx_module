<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Ratingparameter_Delete extends Engine_Form {

  public function init() {

    $this->setTitle('Delete Review Parameters?')
        ->setDescription('Please click on the checkbox to select a parameter from below and then click "Delete" to delete them. Note that these member parameters will not be recoverable after being deleted.')
        ->setMethod('post')
        ->setAttrib('class', 'global_form_box');

    $profiletypesIdsArray = array();
    $profiletypesIdsArray[] = Zend_Controller_Front::getInstance()->getRequest()->getParam('profiletype_id', null);
    $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->memberParams($profiletypesIdsArray, 'user');

    foreach ($ratingParams as $ratingparam_id) {
      $this->addElement('Checkbox', 'ratingparam_name_' . $ratingparam_id->ratingparam_id, array(
          'label' => $ratingparam_id->ratingparam_name,
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