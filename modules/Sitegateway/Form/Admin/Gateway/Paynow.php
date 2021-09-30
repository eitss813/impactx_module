<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Paynow.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Gateway_Paynow extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('Paynow Account Configuration');

        $description = $this->getTranslator()->translate('- Register on <a href="https://www.paynow.co.zw/Customer/Register">Paynow</a> and follow the email validation steps.<br />
- Login with your newly created account and setup the bank account details you would like to receive payment into.<br />
- <a href="https://www.paynow.co.zw/Home/Receive">Go to the</a> [Other Ways To Get Paid] page.<br />
- Click [Create/Manage Shopping Carts]<br />
- Click [Create Advanced Integration]<br />
- Enter a name you will use to identify the integration.<br />
- Opt whether you with to absorb fees on this integration.<br />
- Enter the email address you wish to receive transaction updates to.<br />
- Notification URL should be left blank as we will be specifying individual URLs per transaction.<br />
- Enter any note you wish to keep about the integration, this will not be shown to the client.<br />
- Choose which Payment Methods you want this integration to use.<br />
- Click Save.<br />');

        $description .= $this->getTranslator()->translate('- The integration will be created and you will be returned to the same page.<br />- In the Integration Keys section you will see Integration ID, this is the id you will use below when initiating a transaction, note this id is unique to the integration not your account, if you have more than one integration you will get multiple ids.<br />- For security reasons we do not display your integration key on this page, you need to click [Email Key
To Company Address].<br /> It is vital you keep your Integration Key a secret.<br /> ');
        
        $description .= $this->getTranslator()->translate('- You can now begin integration in test mode. It is recommended you [Generate New Key] when moving your site from a development environment to live, this will stop you generating test transactions on the live account and that any other developers will no longer know the Integration Key.<br /><br />');
         

        $description .= $this->getTranslator()->translate('<b>Note:</b> This Paynow Payment Integration method does not support the Recurring Payments & Webhooks.');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        /*
         * Add the code to create a form for gateway credentials input.
         */
        $this->addElement('Text', 'integration_id', array(
            'label' => 'Integration ID',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'integration_key', array(
            'label' => 'Integration Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $testModeText = 'Note: If you have selected the test mode option, then please ensure that you have entered correct credentials: Test Integration ID and Test Integration Key.';

        $this->addElement('Checkbox', 'test_mode', array(
            'label' => "Enable Test Mode. [" . $testModeText . "]",
            'value' => 1
        ));
    }

}
