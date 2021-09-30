<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MangoPay.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Gateway_MangoPay extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('MangoPay Account Configuration');

        $description = $this->getTranslator()->translate('SITEGATEWAY_FORM_ADMIN_GATEWAY_MANGOPAY_DESCRIPTION');
        $description = vsprintf($description, array(
            'https://www.mangopay.com/',
         ));
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'client_id', array(
            'label' => 'Client Id',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->addElement('Text', 'client_password', array(
            'label' => 'Client Passphrase',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $testModeText = 'Note: If you have selected the test mode option, then please ensure that you have entered correct sandbox credentials.';
        $this->addElement('Checkbox', 'test_mode', array(
            'label' => "Enable Test Mode. [" . $testModeText . "]",
            'value' => 1
        ));
    }

}
