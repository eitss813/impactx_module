<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Payumoney.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Gateway_Payumoney extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('PayUmoney Account Configuration');
        $description = $this->getTranslator()->translate('SITEGATEWAY_FORM_ADMIN_GATEWAY_PAYUMONEY_DESCRIPTION');
        $description = vsprintf($description, array('https://www.payumoney.com/',));
        $this->setDescription($description);
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        /*
         * create a form for gateway credentials input.
         */
        $this->addElement('Text', 'merchant_key', array(
            'label' => 'Key',
            'required'=>true,
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));
        $this->merchant_key->setAllowEmpty(false);

        $this->addElement('Text', 'salt', array(
            'label' => 'Salt',
            'required'=>true,
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
