<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Payment.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_Payment extends Engine_Form {

    public function init() {

        $this->setTitle('Payment Settings')
                ->setDescription('Here, you can configure the payment based settings for your Project’s backers.')
                ->setName('sitecrowdfunding_backer_payment');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PAYMENT GATEWAY
        $splitGateways = array();
        $escrowGateway = array();
        $otherGateways = array();
        $normalGateway = array();
        $stripeconnect = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0);
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('pluginLike' => 'Sitegateway_Plugin_Gateway_'));
            foreach ($getEnabledGateways as $getEnabledGateway) {
                $gatewayKey = strtolower($getEnabledGateway->title);
                if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                    $escrowGateway[$gatewayKey] = $getEnabledGateway->title;
                    $splitGateways[$gatewayKey] = $getEnabledGateway->title;
                } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && !empty($stripeconnect)) {
                    $splitGateways[$gatewayKey] = $getEnabledGateway->title;
                    $normalGateway[$gatewayKey] = $getEnabledGateway->title;
                    $otherGateways[$gatewayKey] = $getEnabledGateway->title;
                } else {
                    $normalGateway[$gatewayKey] = $getEnabledGateway->title;
                    $otherGateways[$gatewayKey] = $getEnabledGateway->title;
                }
            }
        }
        $note1 = '';
        if (empty($escrowGateway)) {
            $note1 = "<div class='tip'><span>Note: You do not have any escrow gateway enabled. Please configure and enable Payment Gateways from <a target='_blank' href='admin/payment/gateway'>here</a>.</span></div>";
        }
        $note2 = '';
        if (empty($splitGateways)) {
            $note2 = "<div class='tip'><span>Note: You do not have any split gateway enabled. Please configure and enable Payment Gateways from <a target='_blank' href='admin/payment/gateway'>here</a>.</span></div>";
        }
//        $normalGateway = array_merge($normalGateway, array(
//            'paypal' => 'PayPal',
//        ));
        $normalGateway = array(
            'paypal' => 'PayPal',
            'stripe' => 'Stripe'
        );
        $this->addElement('Radio', 'sitecrowdfunding_payment_method', array(
            'label' => 'Payment Method',
            'multiOptions' => array('normal' => 'Normal', 'split' => 'Split Immediately', 'escrow' => 'Escrow  [Note: Option to create projects with lifetime duration will not work with this payment method.]'),
            'onclick' => 'selectPaymentMethod(this.value)',
            'value' => $settings->getSetting('sitecrowdfunding.payment.method', 'normal'),
        ));
        //SETTINGS FOR "DIRECT PAYEMENT TO SELLERS" OR "PAYMENT TO WEBSITE / SITEADMIN"
        $this->addElement('Radio', 'sitecrowdfunding_payment_to_siteadmin', array(
            'label' => 'Payment for Projects',
            'description' => 'Please choose the default payment flow for orders on your website.',
            'multiOptions' => array(
                '0' => 'Direct Payment to Project Owners',
                '1' => 'Payment to Website / Site Admin'
            ),
            'onchange' => 'showPaymentForOrdersGateway(this.value)',
            'value' => $settings->getSetting('sitecrowdfunding.payment.to.siteadmin', '0'),
        ));

        $this->addElement('MultiCheckbox', 'sitecrowdfunding_allowed_payment_gateway', array(
            'label' => 'Payment Gateways',
            'multiOptions' => $normalGateway,
            'value' => $settings->getSetting('sitecrowdfunding.allowed.payment.gateway'),
        ));
        $this->addElement('MultiCheckbox', 'sitecrowdfunding_allowed_payment_escrow_gateway', array(
            'label' => 'Escrow Payment Gateways',
            'description' => $note1,
            'multiOptions' => $escrowGateway,
            'value' => $settings->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway'),
        ));

        $this->addElement('MultiCheckbox', 'sitecrowdfunding_allowed_payment_split_gateway', array(
            'label' => 'Split Payment Gateways',
            'description' => $note2,
            'multiOptions' => $splitGateways,
            'value' => $settings->getSetting('sitecrowdfunding.allowed.payment.split.gateway'),
        ));
        //PAYMENT GATEWAY FOR "PAYMENT TO WEBSITE / SITE ADMIN"
        $this->addElement('MultiCheckbox', 'sitecrowdfunding_admin_gateway', array(
            'label' => 'Payment Gateways',
            'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Select the payment gateway to be available during checkout process. [To enable payment gateways PayPal and 2Checkout, click %1$shere%2$s.]'), "<a href='" . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . "' target='_blank'>", "</a>"),
            'multiOptions' => array(),
        ));
        $this->sitecrowdfunding_admin_gateway->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {

            //$gatewayOptions = array_merge(array('paypal' => 'Paypal'), $otherGateways);
            $gatewayOptions = array('paypal' => 'Paypal');
            $this->addElement('Radio', 'sitecrowdfunding_paymentmethod', array(
                'label' => "Payment for 'Commissions Bill'",
                'description' => "Select the payment gateway to be available to project owners for admin ‘Commissions Bill’ payment, if ‘Direct Payment to Project Owners’ is selected.",
                'multiOptions' => $gatewayOptions,
                'value' => $settings->getSetting('sitecrowdfunding.paymentmethod', 'paypal'),
            ));
        }

        $this->addElement('Radio', 'sitecrowdfunding_thresholdnotification', array(
            'label' => 'Email Notification for Commission Bill Payment',
            'description' => 'Do you want to enable email notifications for project owners for your commission bill payment? Once total commission bill amount exceed to threshold amount, project owners will start getting email notifications for your due commission payment and it will continue until the payment has been not made.',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'onchange' => 'thresholdNotification(this.value)',
            'value' => $settings->getSetting('sitecrowdfunding.thresholdnotification', 0),
        ));

        $this->addElement('Text', 'sitecrowdfunding_thresholdnotificationamount', array(
            'label' => 'Enter Threshold Amount',
            'value' => $settings->getSetting('sitecrowdfunding.thresholdnotificationamount', 100),
        ));

        $this->addElement('MultiCheckbox', 'sitecrowdfunding_thresholdnotify', array(
            'description' => 'Please select to whom this email notification will be send. This notification will repeat whenever a project is backed.',
            'multiOptions' => array(
                'owner' => 'Send Email Notification to Project Owner',
                'admin' => 'Send Email Notification to Site Admin',
            ),
            'value' => $settings->getSetting('sitecrowdfunding.thresholdnotify', array('owner', 'admin')),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_payment_setting', array(
            'label' => 'Payment Option',
            'description' => 'Please choose the option for payment.',
            'onclick' => 'selectPaymentOption(this.value)',
            'multiOptions' => array(
                'automatic' => 'Automatic',
                'mannual' => 'Mannual'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.payment.setting', 'mannual'),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_automatic_payment_method', array(
            'label' => 'Automatic Payment Method',
            'description' => 'Choose the action for automatic payment when Project does not reach its goal in the defined period of time. 
If you choose \'Payout\' then backed amount will be given to the Project owner even though Project didn\'t reach its goal. And if you choose \'Refund\' then the backed amount will be returned to the backers of that Project.',
            'multiOptions' => array(
                'payout' => 'Payout',
                'refund' => 'Refund'
            ),
            'value' => $settings->getSetting('sitecrowdfunding.automatic.payment.method', 'payout'),
        ));

        $this->sitecrowdfunding_allowed_payment_escrow_gateway->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $this->sitecrowdfunding_allowed_payment_split_gateway->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}