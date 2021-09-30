<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Reward_Create extends Engine_Form {

    protected $_reward_number;

    public function getReward_number() {
        return $this->_reward_number;
    }

    public function setReward_number($count) {
        $this->_reward_number = $count;
        return $this;
    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

        $layoutType = Zend_Controller_Front::getInstance()->getRequest()->getParam('layoutType', null);

        $reward_number = $this->getReward_number();

        $this->addElement('Text', 'title', array(
            'label' => "Reward Title",
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => 75,
        ));
        $this->addElement('Text', 'pledge_amount', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Minimum Back Amount (%s)'), $currencyName),
            'required' => true,
            // 'allowEmpty' => false,
            'validators' => array(
                array('Float', false),
                array('GreaterThan', true, array(-1))
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        )));
        $this->addElement('textarea', 'description', array(
            'label' => "Description",
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
        $this->addElement('File', 'photo', array(
            'label' => 'Main Photo',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $this->addElement('Date', 'delivery_date', array(
            'label' => "Estimated Delivery",
            'allowEmpty' => false,
            'required' => true,
            'value' => array('year' => date('Y'), 'month' => date('m'), 'day' => date('d'))
        ));
        $currentYear = date('Y');
        $this->delivery_date->setYearMax($currentYear + 10);
        $this->delivery_date->setYearMin($currentYear - 1);
        $this->addElement('Select', 'shipping_method', array(
            'label' => 'Shipping Detail',
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => array('' => 'Select an option', '1' => 'No shipping involved', '2' => 'Ships only to certain countries', '3' => 'Ships anywhere in the world'),
            'onchange' => 'addLocationObj.changeShippingOption($(this).value)',
        ));
        $this->addElement('Checkbox', 'limit', array(
            'label' => "Limit quantity <i class='fa fa-question-circle' title='Limit this reward quantity to be available for backers'> </i>",
            'value' => 0,
            'onclick' => 'checkQuantity()'
        ));
        $this->limit->addDecorator('Label', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Text', 'quantity', array(
            'label' => "Limit Quantity",
            'validators' => array(
                array('Int', true),
            )
        ));
        $this->addDisplayGroup(array(
            'title',
            'pledge_amount',
            'description',
            'photo',
            'delivery_date',
            'shipping_method',
            'limit',
            'quantity'), 'reward_form', array(
            'label' => 'Website Url',
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Create',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'form-label')),
            ),
        ));

        if($layoutType == 'fundingDetails'){
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'onClick' => 'javascript:parent.Smoothbox.close();',
                'prependText' => ' or ',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        }else{
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        }


        $this->addDisplayGroup(array(
            'execute',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
