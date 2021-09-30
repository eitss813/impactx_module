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
class Sitegateway_Form_Order_Mollie extends Engine_Form {

    public function init() {

        $Payment_gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $Payment_gateway_table_obj = $Payment_gateway_table->fetchRow(array('plugin = \'Sitegateway_Plugin_Gateway_Mollie\''));
        $testmode = $Payment_gateway_table_obj->test_mode;
   
        parent::init();

        $this->setTitle('Payment Gateway: Mollie');

        $description = $this->getTranslator()->translate('SITEGATEWAY_FORM_GATEWAY_MOLLIE_DESCRIPTION');

        if($testmode) {   

            $description.='<div class="tip"><span> Test mode is enabled by Admin. Please enter your Test credentials.</span></div>';
        }
        $description .= ' <br/> ' . '<div id="show_mollie_form_massges"></div>';
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);
 
         $this->addElement('Text', 'merchant_key', array(
            'label' => 'Integration Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));


        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $showExtraInfo = true;
        if ($module == 'siteevent' || $module == 'siteeventticket') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        } elseif ($module == 'sitestore' || $module == 'sitestoreproduct') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
        }
        if (empty($isPaymentToSiteEnable)) {
            $showExtraInfo = false;
        }
        if (!empty($showExtraInfo)) {
            // Element: enabled
            $this->addElement('Radio', 'enabled', array(
                'label' => 'Enabled?',
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => '0',
            ));

            // Element: execute
            $this->addElement('Button', 'submit', array(
                'label' => 'Save Changes',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => '_formSetDivAddress.tpl',
                            'class' => 'form element')))
            ));

            $this->addDisplayGroup(array('submit'), 'buttons', array(
                'decorators' => array(
                    'FormElements',
                    'DivDivDivWrapper',
                ),
            ));
        } 
    }

}
