<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FeaturedAlbum.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Admin_FeaturedAlbum extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');
    $this->setTitle('Add an Album as Featured')
            ->setDescription('Using the auto-suggest field below, choose the album to be made featured');
    // init to
    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Album Name')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:300px;');

    $this->addElements(array(
        $label,
    ));

    $this->addElement('Hidden', 'resource_id', array(
        'order' => 9999,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
    ));
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Make Featured',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}