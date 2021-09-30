<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Hidephoto.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Hidephoto extends Engine_Form {

  public function init() {

    $photo_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('photo_id', null);
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $this
            ->setTitle('Are you sure you want to hide this photo?')
            ->setDescription("Hiding a photo only removes it from your profile page. People you've allowed will still be able to view this photo on your photos page.");

    $this->addElement('Button', 'submit', array(
        'label' => 'Hide Photo',
        'ignore' => true,
        'onclick' => "return hidephoto('$photo_id', '$page_id');",
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'type' => 'link',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}

?>