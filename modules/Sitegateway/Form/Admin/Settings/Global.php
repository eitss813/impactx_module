<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Global.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Settings_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "environment_mode",
        "submit_lsetting"
    );

    public function init() {

        $this
                ->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');

        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', 'sitegateway_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialApps.tech from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.lsettings'),
        ));

        if (APPLICATION_ENV == 'production') {
            $this->addElement('Checkbox', 'environment_mode', array(
                'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
                'description' => 'System Mode',
                'value' => 1,
            ));
        } else {
            $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
        }

        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if (Engine_Api::_()->hasModuleBootstrap('siteeventticket') || Engine_Api::_()->hasModuleBootstrap('sitestoreproduct') || Engine_Api::_()->hasModuleBootstrap('sitecrowdfunding')) {

            if (Engine_Api::_()->hasModuleBootstrap('siteeventticket') && Engine_Api::_()->hasModuleBootstrap('sitestoreproduct')) {
                $description = 'Do you want to enable Stripe Connect? [Note: If you enable the Stripe Connect for your website, then the payment flows: ‘Direct Payment to Sellers’ and ‘Payment to Website / Site Admin’ will not be applicable in Stores / Marketplace - Ecommerce Plugin and Advanced Events - Events Booking, Tickets Selling & Paid Events Extension for Stripe payment gateway because, Stripe Connect automatically splits the payment into sellers accounts and admin account once any payment has been made by user. Rest of the settings of the payment flows will remain applicable according to their defined settings. If you select Yes, then please edit the stripe gateway details from <a href="' . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . '" target="blank">Gateways</a> tab of this plugin.]';
            } elseif (Engine_Api::_()->hasModuleBootstrap('siteeventticket')) {
                $description = 'Do you want to enable Stripe Connect? [Note: If you enable the Stripe Connect for your website, then the payment flows: ‘Direct Payment to Sellers’ and ‘Payment to Website / Site Admin’ will not be applicable in Advanced Events - Events Booking, Tickets Selling & Paid Events Extension for Stripe payment gateway because, Stripe Connect automatically splits the payment into sellers accounts and admin account once any payment has been made by user. Rest of the settings of the payment flows will remain applicable according to their defined settings. If you select Yes, then please edit the stripe gateway details from <a href="' . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . '" target="blank">Gateways</a> tab of this plugin.]';
            } else {
                $description = 'Do you want to enable Stripe Connect? [Note: If you enable the Stripe Connect for your website, then the payment flows: ‘Direct Payment to Sellers’ and ‘Payment to Website / Site Admin’ will not be applicable in Stores / Marketplace - Ecommerce Plugin for Stripe payment gateway because, Stripe Connect automatically splits the payment into sellers accounts and admin account once any payment has been made by user. Rest of the settings of the payment flows will remain applicable according to their defined settings. If you select Yes, then please edit the stripe gateway details from <a href="' . $view->url(array('module' => 'payment', 'controller' => 'gateway', 'action' => 'index'), 'admin_default', true) . '" target="blank">Gateways</a> tab of this plugin.]';
            }

            $this->addElement('Radio', 'sitegateway_stripeconnect', array(
                'label' => 'Stripe Connect',
                'description' => $description,
                'multioptions' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'onclick' => 'javascript:stripeConnectOptions()',
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0),
            ));
            $this->sitegateway_stripeconnect->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

            $this->addElement('Radio', 'sitegateway_stripechargemethod', array(
                'RegisterInArrayValidator' => false,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => 'application/modules/Sitegateway/views/scripts/_stripeChargeMethod.tpl',
                            'class' => 'form element'))),
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripechargemethod', NULL),
            ));
        }

        $this->addElement('Text', 'sitegateway_mangopay_fees_charge', array(
            'label' => "MangoPay’s Fees",
            'description' => "The amount calculated based on this value will be charged from the seller to pay the MangoPay’s fees.<br />
[Note: In MangoPay, there is no option to choose that who will pay the fees for payment processing. So, enter the value if you want seller to pay the MangoPay’s fees or ‘0’ if you want to pay.]",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.mangopay.fees.charge', 0),
            'validators' => array(
                array('Float', true),
                array('LessThan', true, array(100)),
            )
        ));
        $this->sitegateway_mangopay_fees_charge->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}