<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Stripe.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegateway_Form_Admin_Gateway_Stripe extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        
        $showStripeConnectTexts = false;
        $webhookURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'payment', 'controller' => 'ipn', 'action' => 'Stripe'), 'default', true);

        $formTitle = 'Stripe Account Configuration';
        $description = $this->getTranslator()->translate("1) If you haven't already, you will need to establish a <a href='https://dashboard.stripe.com/register' target='_blank'>Stripe</a> account.<br/>
            2) Enable API access on your Stripe account via: Your Account > Account Settings > API Keys > API Secret Key & API Publishable Key.<br/>
3) Insert the Stripe API Secret Key and API Publishable Key values into this form.<br/>
4) Select 'Yes' for Enabled and click on 'Save Changes'. If the details provided by you are correct, then your Stripe account is configured successfully.<br/>
5) Enable <a href='https://dashboard.stripe.com/account/webhooks' target='_blank'>Webhooks</a> via: Your Account > Account Settings > Webhooks > Add endpoints > Select 'Account' as an endpoint type > Add an account endpoint URL, Select Live / Test mode, Select 'send me all events' and click on Create Endpoint button. The endpoints URL should be set to: <b>$webhookURL</b>
");
        if ($coreSettings->getSetting('sitegateway.stripeconnect', 0) && (Engine_Api::_()->hasModuleBootstrap('siteeventticket') || Engine_Api::_()->hasModuleBootstrap('sitestoreproduct') || Engine_Api::_()->hasModuleBootstrap('sitecrowdfunding'))) {
            $showStripeConnectTexts = true;
            $formTitle = 'Stripe Connect Account Configuration';
            $stripeConnectURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitegateway', 'controller' => 'payment', 'action' => 'stripe-connect'), 'default', true);
            $description = $this->getTranslator()->translate("1) If you haven't already, you will need to establish a <a href='https://dashboard.stripe.com/register' target='_blank'>Stripe</a> account.<br/>
2) Enable API access on your Stripe account via: Your Account > Account Settings > API Keys > API Secret Key & API Publishable Key.<br/>
3) To access client id for your Stripe Connect, go to: Your Account > Account Settings > Connects > Platform Settings > client_id.<br/>
4) Insert the Stripe API Secret Key, API Publishable Key and Client Id values into this form.<br/>
5) Select 'Yes' for Enabled and click on 'Save Changes'. If the details provided by you are correct, then your Stripe Connect account is configured successfully.<br/>
6) Enable <a href='https://dashboard.stripe.com/account/webhooks' target='_blank'>Webhooks</a> via: Your Account > Account Settings > Webhooks > Add endpoints > Select 'Account' as an endpoint type > Add an account endpoint URL, Select Live / Test mode, Select 'send me all events' and click on Create Endpoint button. The endpoints URL should be set to: <b>$webhookURL</b> <br/>
7) Enable <a href='https://dashboard.stripe.com/account/applications/settings' target='_blank'>OAuth</a> on your Stripe Connect standalone account via: Your Account > Account Settings > Connects > Platform Settings > write your application name, write your website URL, upload your website logo & icon (optional) and add redirect URL (Endpoint of your server that Stripe will redirect your users back after they connect with Stripe): <b>$stripeConnectURL</b> and click on 'Done' button.<br/><div class='tip'><span>You can view this video tutorial: <a href='https://www.youtube.com/watch?v=vCXAJHEwsJ8' target='_blank'>https://www.youtube.com/watch?v=vCXAJHEwsJ8</a> for how to configure and integrate Stripe Connect with a SocialEngine website.</span></div>");
        }

        $this->setTitle($formTitle);

        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'secret', array(
            'label' => 'API Secret Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'publishable', array(
            'label' => 'API Publishable Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        if ($showStripeConnectTexts) {
            $this->addElement('Text', 'client_id', array(
                'label' => 'Client Id',
                'filters' => array(
                    new Zend_Filter_StringTrim(),
                ),
            ));
        }

        $testModeText = 'Note: If you select the test mode option, then please ensure that you have entered correct: Test Secret Key and Test Publishable Key credentials.';
        if($coreSettings->getSetting('sitegateway.stripeconnect', 0)) {
            $testModeText = 'Note: If you select the test mode option, then please ensure that you have entered correct: Test Secret Key, Test Publishable Key and Development client_id credentials.';
        }
        
        $this->addElement('Checkbox', 'test_mode', array(
            'label' => "Enable Test Mode. [".$testModeText."]",
            'value' => 1
        ));
    }

}
