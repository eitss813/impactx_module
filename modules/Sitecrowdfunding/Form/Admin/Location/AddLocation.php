<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddLocation.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitecrowdfunding_Form_Admin_Location_AddLocation extends Engine_Form {

  public function init() {

    $this->setTitle('Add Location')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    //TAKING COUNTRIES OBJECT
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
    foreach ($countries as $keys => $tempCountry) {
      $country[$keys] = $tempCountry;
    }
   
    //GETTING DISABLED COUNTRIES
    $disabledCountryArray =  Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getDisabledCountries();
    
    //UNSETTING DISABLED COUNTRIES IN ARRAY
    foreach($disabledCountryArray as $countryName)
    {
      unset ($country[$countryName]);
    }
    @asort($country);
    
    $getAllEmptyRegionsArray = Engine_Api::_()->getDbtable('regions', 'sitecrowdfunding')->getAllRegionsCountryArray();
    foreach($getAllEmptyRegionsArray as $obj) {
      if(array_key_exists($obj['country'], $country)) {
        unset($country[$obj['country']]);
      }
    }
    

    $this->addElement('Select', 'country', array(
        'label' => 'Country',
        'multiOptions' => $country,
        'value' => key($country),
    ));
    $this->addElement('Hidden', 'all_regions', array(
        'value' => 1,
       
    ));


    $this->addElement('Button', 'submit', array(
        'label' => 'Add Location',
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

}