<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Unhidephoto.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Unhidephoto extends Engine_Form {

  public function init() {

    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $isajax = Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax', null);

    $this->setTitle('Unhide All Photos')
            ->setDescription('Unhide all photos from the photo strip on top of Page profile and reset the photo strip.');

    $table = Engine_Api::_()->getDbtable('photos', 'sitepage');

    $select = $table->select()
            ->where('photo_hide = ?', 1)
            ->where('page_id = ?', $page_id)
    ;
    $row = $table->fetchAll($select)->toarray();

    $this->addElement('Dummy', 'successmessage', array(
        'description' => '',
    ));
    
    if (!empty($row)) {
      $this->addElement('Button', 'unhideall', array(
          'label' => 'Unhide All',
          'type' => 'submit',
          'class' => 'smoothbox',
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'unhidephoto'), 'sitepage_general', true),
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

?>