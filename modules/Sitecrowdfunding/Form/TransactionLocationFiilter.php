<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locationsearch.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_TransactionLocationFiilter extends Fields_Form_Search
{


    public function init()
    {

        $this->setAttrib('id', 'location_form')->setMethod('post');

        $this->addElement('Hidden', 'page', array('order' => '1'));

        // Search
        $this->addElement('Text', 'search', array(
            'label' => 'What',
            'autocomplete' => 'off',
            'description' => '(Enter keywords or Project name)',
        ));
        $this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


        // Location
        $this->addElement('Text', 'location', array(
            'label' => 'Where',
            'autocomplete' => 'off',
            'description' => Zend_Registry::get('Zend_Translate')->_('(Address, city, State or Country)'),
            'onclick' => 'locationPage();'
        ));
        $this->location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


        // Miles or KM
        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
        if ($flage) {
            $locationLable = "Within Kilometers";
            $locationOption = array(
                '0' => '',
                '1' => '1 Kilometer',
                '2' => '2 Kilometers',
                '5' => '5 Kilometers',
                '10' => '10 Kilometers',
                '20' => '20 Kilometers',
                '50' => '50 Kilometers',
                '100' => '100 Kilometers',
                '250' => '250 Kilometers',
                '500' => '500 Kilometers',
                '750' => '750 Kilometers',
                '1000' => '1000 Kilometers',
            );
        } else {
            $locationLable = "Within Miles";
            $locationOption = array(
                '0' => '',
                '1' => '1 Mile',
                '2' => '2 Miles',
                '5' => '5 Miles',
                '10' => '10 Miles',
                '20' => '20 Miles',
                '50' => '50 Miles',
                '100' => '100 Miles',
                '250' => '250 Miles',
                '500' => '500 Miles',
                '750' => '750 Miles',
                '1000' => '1000 Miles',
            );
        }
        $this->addElement('Select', 'locationmiles', array(
            'label' => $locationLable,
            'multiOptions' => $locationOption,
            'value' => '0'
        ));

        //Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Button', 'clear', array(
            'label' => 'Clear',
            'decorators' => array('ViewHelper')
        ));
        $this->addDisplayGroup(array('search', 'clear'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }
}
