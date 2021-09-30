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
class Sitegateway_Form_Order_Payumoney extends Engine_Form {

    public function init() {


        $Payment_gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $Payment_gateway_table_obj = $Payment_gateway_table->fetchRow(array('plugin = \'Sitegateway_Plugin_Gateway_Payumoney\''));
        $testmode = $Payment_gateway_table_obj->test_mode;
   
        parent::init();

        $this->setTitle('PayUmoney Account Configuration');
       
        $description = $this->getTranslator()->translate('SITEGATEWAY_FORM_ORDER_GATEWAY_PAYUMONEY_DESCRIPTION');
        $description .= ' <br/> ' . '<div id="show_payumoney_form_massges"></div>';
        $description = vsprintf($description, array('https://www.payumoney.com/',));

        if($testmode) {   

            $description.='<div class="tip"><span> Test mode is enabled by Admin. Please enter your Test credentials.</span></div>';
        }

        $this->setDescription($description);
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);
        
        /*
         * create form elements for entering gateway credentials.
         */
        $this->addElement('Text', 'merchant_key', array(
            'label' => 'Merchant Key',
            'filters' => array(
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'salt', array(
            'label' => 'Secret Key (Salt)',
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
// $this->addElement('dummy', 'message', array(
//             'description' => '<div class="tip"><span>Note: If you do not map RSS belonging to this category with any other, then channels associated with this category will also be deleted from your website. This data is not recoverable.</span></div>',
//         ));
//         $this->message->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
