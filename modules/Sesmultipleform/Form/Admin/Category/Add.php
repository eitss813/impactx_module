<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Add.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Category_Add extends Engine_Form {

  public function init() {

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', 0);
    if ($category_id)
      $category = Engine_Api::_()->getItem('sesmultipleform_category', $category_id);

    $this->setMethod('post');

    $this->addElement('Text', 'title', array(
        'label' => 'Category Title',
        'allowEmpty' => true,
        'required' => false,
    ));

    $profiletype = array();
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sesmultipleform_entry');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $options = $profileTypeField->getElementParams('sesmultipleform_entry');
      unset($options['options']['order']);
      unset($options['options']['multiOptions']['0']);
      $profiletype = $options['options']['multiOptions'];
    }
    $this->addElement('Select', 'profile_type', array(
        'label' => 'Profile Type',
        'allowEmpty' => true,
        'required' => false,
        'multiOptions' => $profiletype
    ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Add',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
