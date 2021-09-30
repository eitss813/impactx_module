<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Unhidephoto.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Unhidephoto extends Engine_Form {

  public function init() {

    $owner_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id', null);
    $isajax = Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax', null);

    $this->setTitle('Unhide All Photos')
            ->setDescription('Unhide all photos from the photo strip on top of my profile and reset the photo strip.');

    $hidePhoto = Engine_Api::_()->getItemTable('album_photo')->fetchRow(array('photo_hide = ?' => 1, 'owner_id = ?' => $owner_id));
    $this->addElement('Dummy', 'successmessage', array(
        'description' => '',
    ));

    if (!empty($hidePhoto)) {
      $this->addElement('Button', 'unhideall', array(
          'label' => 'Unhide All',
          'type' => 'submit',
          'class' => 'smoothbox',
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'unhide-photo', 'user_id' => $owner_id), 'sitealbum_extended', true),
          'decorators' => array(
              'ViewHelper',
          ),
      ));
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'onclick' => 'javascript:parent.Smoothbox.close()',
          'decorators' => array(
              'ViewHelper',
          ),
      ));
    } else {
      $this->addElement('Button', 'unhideall', array(
          'label' => 'Unhide All',
          'type' => 'submit',
          'decorators' => array(
              'ViewHelper',
              array('HtmlTag', array('tag' => 'div', 'class' => 'disable'))
          ),
      ));
      $this->unhideall->setAttrib('disable', true);
    }
  }

}