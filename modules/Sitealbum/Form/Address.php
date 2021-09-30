<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitealbum_Form_Address extends Engine_Form {

  public $_error = array();
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    // custom sitealbum fields
    if (!$this->_item) {
      $sitealbum_item = new Album_Model_Album(null);
      $this->setItem($sitealbum_item);
    }
    parent::init();
    if ($this->_item->getType() == 'album') {
      $this->setTitle('Edit Location')
              ->setDescription('Edit your location below, then click "Save Location" to save album location.');
    } elseif ($this->_item->getType() == 'album_photo') {
      $this->setTitle('Edit Location')
              ->setDescription('Edit your location below, then click "Save Location" to photo location.');
    }
    // LOCATION
    $this->addElement('Text', 'location', array(
        'label' => 'Location',
        'placeholder' => '',
        'description' => 'Eg: Fairview Park, Berkeley, CA',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
    $this->location->getDecorator('Description')->setOption('placement', 'append');
    $this->addElement('Hidden', 'locationParams', array('order' => 800000));
    $this->addElement('Hidden', 'dataParams', array('order' => 800001));
    include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Location',
        'order' => '998',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'order' => '999',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder('999');
  }

}