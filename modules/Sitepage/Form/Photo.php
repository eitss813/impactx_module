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
class Sitepage_Form_Photo extends Engine_Form {

  public function init() {

    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $this
            ->setTitle('Edit Profile Picture')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'EditPhoto');

    $this->addElement('Image', 'current', array(
        'label' => 'Current Photo',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formEditImage.tpl',
                     'class' => 'form element',
                    'testing' => 'testing'
            )))
    ));
    Engine_Form::addDefaultDecorators($this->current);

    $this->addElement('File', 'Filedata', array(
        'label' => 'Choose New Photo',
        'destination' => APPLICATION_PATH . '/public/temporary/',
        'validators' => array(
            array('Extension', false, 'jpg,jpeg,png,gif'),
        ),
        'onchange' => 'javascript:uploadPhoto();'
    ));

    $this->addElement('Hidden', 'coordinates', array(
         'order' => 900,
        'filters' => array(
            'HtmlEntities',
        )
    ));

    if (!Engine_Api::_()->getApi('settings', 'core')->sitepage_requried_photo) {
      if ($sitepage->photo_id != 0) {
        $this->addElement('Cancel', 'remove', array(
            'label' => 'Remove Photo',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'action' => 'remove-photo',
            )),
            'onclick' => null,
            'decorators' => array(
                'ViewHelper'
            ),
        ));
        $this->addDisplayGroup(array('done', 'remove'), 'buttons');
      }
    }
  }

}

?>