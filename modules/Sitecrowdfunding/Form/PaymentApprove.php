<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentApprove.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_PaymentApprove extends Engine_Form {

    protected $_cheque_no;
    protected $_customer_signature;
    protected $_account_number;
    protected $_bank_routing_number;

    public function setCheque_no($id) {
        $this->_cheque_no = $id;
        return $this;
    }

    public function setCustomer_signature($id) {
        $this->_customer_signature = $id;
        return $this;
    }

    public function setAccount_number($id) {
        $this->_account_number = $id;
        return $this;
    }

    public function setBank_routing_number($id) {
        $this->_bank_routing_number = $id;
        return $this;
    }

    public function init() {
        $this->setAttribs(array(
            'id' => 'payment_approve_form',
            'title' => 'Payment Approve',
            'class' => 'global_form_box',
        ));

        $this->addElement('Text', 'cheque_no', array(
            'label' => 'Cheque No',
            'value' => $this->_cheque_no,
            'attribs' => array('disabled' => 'disabled'),
           'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'customer_signature', array(
            'label' => 'Account Holder Name',
            'value' => $this->_customer_signature,
            'attribs' => array('disabled' => 'disabled'),
           'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'account_number', array(
            'label' => 'Account Number',
            'value' => $this->_account_number,
            'attribs' => array('disabled' => 'disabled'),
        'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'bank_routing_number', array(
            'label' => 'Bank Routing No',
            'value' => $this->_bank_routing_number,
            'attribs' => array('disabled' => 'disabled'),
          'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'transaction_no', array(
            'label' => 'Bank Transaction No',
            'description' => 'Please enter the transaction no if you have or leave otherwise.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $this->transaction_no->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

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
