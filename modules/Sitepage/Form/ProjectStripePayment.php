<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Order.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Form_ProjectStripePayment extends Engine_Form {

    public function init() {
        parent::init();

        $this->setTitle('Stripe Account Configuration');

        $description = $this->getTranslator()->translate("1) If you haven't already, you will need to establish a <a href='https://dashboard.stripe.com/register' target='_blank'>Stripe</a> account.<br/>2) Enable API access on your Stripe account via: Your Account > Account Settings > API Keys > API Secret Key & API Publishable Key.<br/>3) Insert the Stripe API Secret Key and API Publishable Key values into this form.");
        $description = $description . '<div id="show_stripe_form_massges"></div>';
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

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();

        $showExtraInfo = true;
        if ($module == 'siteevent' || $module == 'siteeventticket') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        } elseif ($module == 'sitestore' || $module == 'sitestoreproduct') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
        }elseif ($module == 'sitecrowdfunding') {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
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

        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

}
