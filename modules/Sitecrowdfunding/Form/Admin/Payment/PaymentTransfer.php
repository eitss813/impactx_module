<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentTransfer.php 2017-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Payment_PaymentTransfer extends Engine_Form {

    protected $_amount;

    public function getAmount() {
        return $this->_amount;
    }

    public function setAmount($id) {
        $this->_amount = $id;
        return $this;
    }

    public function init() {

        $this->setAttribs(array(
            'id' => 'payment_transfer_form',
            'class' => 'global_form_box',
        ));

        $this->addElement('hidden', 'user_req_amount', array('order' => 200,'value' => $this->_amount));
        $this->addElement('Text', 'amount', array(
            'label' => 'Approve Amount',
            'value' => $this->_amount,
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('Float', true),
                array('GreaterThan', false, array(0))
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Textarea', 'response_message', array(
            'label' => 'Response Message',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Approve Payment',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }

}
