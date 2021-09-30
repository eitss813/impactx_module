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
class User_Form_Edit_Location extends Engine_Form {

    public function init() {

        $this->setMethod('post')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this
                ->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => 'global_form_box',
        ));

        $this->addElement('Text', 'formatted_address', array(
            'label' => 'Formatted Address',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->formatted_address->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));


        $this->addElement('Text', 'latitude', array(
            'label' => 'Latitude',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->latitude->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Text', 'longitude', array(
            'label' => 'Longitude',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->longitude->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Text', 'address', array(
            'label' => 'Street Address',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        )));
        $this->address->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Text', 'city', array(
            'label' => 'City',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));
        $this->city->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Text', 'zipcode', array(
            'label' => 'Zipcode',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));
        $this->zipcode->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Text', 'state', array(
            'label' => 'State',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));
        $this->state->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Text', 'country', array(
            'label' => 'Country',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        $this->country->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
                ->addDecorator('HtmlTag', array('tag' => 'div'));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
        ));

        $this->addElement('Hidden', 'zoom', array(
            'order' => '99999'
        ));
    }

}