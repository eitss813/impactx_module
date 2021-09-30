<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Photo_Cover extends Engine_Form {

  public function init() {


    $this
            ->setTitle('Upload Page Cover Photo')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('id', 'cover_photo_form')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'Upload A Cover Photo');



    $this->addElement('File', 'Filedata', array(
        'label' => 'Choose a cover photo.',
        'destination' => APPLICATION_PATH . '/public/temporary/',
        'validators' => array(
            array('Extension', false, 'jpg,jpeg,png,gif'),
        ),
      'onchange' => 'javascript:uploadPhoto();'
    ));

//    $this->addElement('Button', 'submit', array(
//        'label' => 'Save Photos',
//        'type' => 'submit',
//    ));

//    if (!Engine_Api::_()->getApi('settings', 'core')->sitepage_requried_photo) {
//      if ($sitepage->photo_id != 0) {
//        $this->addElement('Cancel', 'remove', array(
//            'label' => 'Remove Photo',
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
//                'action' => 'remove-photo',
//            )),
//            'onclick' => null,
//            'decorators' => array(
//                'ViewHelper'
//            ),
//        ));
//        $this->addDisplayGroup(array('done', 'remove'), 'buttons');
//      }
//    }
  }

}

?>