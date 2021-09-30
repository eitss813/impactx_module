<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Ratingparameter_Edit extends Engine_Form {

  protected $_field;

  public function init() {
    $this
            ->setTitle('Edit Review Parameters')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    $profiletypesIdsArray = array();
    $profiletypesIdsArray[] = Zend_Controller_Front::getInstance()->getRequest()->getParam('profiletype_id', null);
    $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->memberParams($profiletypesIdsArray, 'user');

    foreach ($ratingParams as $ratingparam_id) {
      $this->addElement('Text', 'ratingparam_name_' . $ratingparam_id->ratingparam_id, array(
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

  public function setField($ratingParams) {
    $this->_field = $ratingParams;

    foreach ($ratingParams as $ratingparam_id) {
      $ratingparam_field = 'ratingparam_name_' . $ratingparam_id->ratingparam_id;
      $this->$ratingparam_field->setValue($ratingparam_id->ratingparam_name);
    }
  }

}