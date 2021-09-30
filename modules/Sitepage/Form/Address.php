<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Address extends Engine_Form {

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
    // custom sitepage fields
    if (!$this->_item) {
      $sitepage_item = new Sitepage_Model_Page(null);
      $this->setItem($sitepage_item);
    }
    parent::init();

    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);

    if (!empty($multipleLocation)) {
			$this->addElement('Text', 'locationname', array(
					'label' => 'Location Title',
					'description' => 'Eg: Headquarter, Main Store',
                            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
					//'filters' => array('StripTags', new Engine_Filter_Censor()
			));
			$this->locationname->getDecorator('Description')->setOption('placement', 'append');
    }
    
    // LOCATION
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1)) {
      $this->addElement('Text', 'location', array(
          'label' => 'Location',
          'description' => 'Eg: Fairview Park, Berkeley, CA',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              )));
      $this->location->getDecorator('Description')->setOption('placement', 'append');
      $this->addElement('Hidden', 'locationParams', array( 'order' => 800000));
      
      
      include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';       
    }
    
    if (!empty($multipleLocation)) {
			$this->addElement('Checkbox', 'main_location', array(
					//'description' => 'Main Location',
					'label' => 'Associate this location with my page. (Note: If you select this option, then this location will display under the Info tab on your page profile.)',
					'value' => 0,
			));
		}
		
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