<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    User
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Location.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_Form_Signup_Location extends Engine_Form
{

    public function init()
    {

        $this->setTitle('Setup Location')
            ->setAttrib('id', 'signup_location_form');


        $this->addElement('Text', 'location', array(
            'label' => 'Location',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('hidden', 'locationParams', array('order' => 800000));

        $this->addElement('Text', 'formatted_address', array(
            'label' => 'Formatted Address',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('hidden', 'latitude', array('order' => 900001));

        $this->addElement('hidden', 'longitude', array('order' => 700002));

        $this->addElement('Text', 'address', array(
            'label' => 'Street Address',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('Text', 'city', array(
            'label' => 'City',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )));

        $this->addElement('Text', 'zipcode', array(
            'label' => 'Zipcode',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )));

        $this->addElement('Text', 'state', array(
            'label' => 'State',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )));

        $this->addElement('Text', 'country', array(
            'label' => 'Country',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )));

        $this->addElement('Hidden', 'zoom', array('order' => '99999'));

        $this->addElement('Button', 'submit', array(
            'label' => 'Continue',
            'type' => 'submit',
        ));

    }

}