<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Mollie.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Admin_Gateway_Mollie extends Payment_Form_Admin_Gateway_Abstract {

    public function init() {
        parent::init();

        $this->setTitle('Payment Gateway: Mollie');

        $description = $this->getTranslator()->translate('SITEGATEWAY_FORM_GATEWAY_MOLLIE_DESCRIPTION');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'merchant_key', array(
          'label' => 'Integration Key',
          'filters' => array(
            new Zend_Filter_StringTrim(),
            ),
          ));
        $this->merchant_key->setAllowEmpty(false);

        $testModeText = 'Note: If you have selected the test mode option, then please ensure that you have entered correct Test Integration Key.';

        $this->addElement('Checkbox', 'test_mode', array(
            'label' => "Enable Test Mode. [" . $testModeText . "]",
            'value' => 1
        ));
    }

}
