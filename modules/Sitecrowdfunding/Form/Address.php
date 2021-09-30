<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Address extends Engine_Form {

    public $_error = array();
    protected $_item;

    public function getItem() {
        return $this->_item;
    }

    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {

        $this->setTitle('Edit Location')
                ->setDescription("Edit your location below, then click 'Save Location' to save your location.");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $this->addElement('Text', 'location', array(
                'label' => 'Location',
                'description' => 'Eg: Fairview Park, Berkeley, CA',
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            )));
            $this->location->getDecorator('Description')->setOption('placement', 'append');
            
            include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';
        }
        $this->addElement('Hidden', 'locationParams', array('order' => 800000));
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Location',
            'order' => '998',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        /*$this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'order' => '999',
            'onclick' => "javascript:parent.Smoothbox.close();",
            'href' => "javascript:void(0);",
            'decorators' => array(
                'ViewHelper',
            ),
        ));*/

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