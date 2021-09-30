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
class Sitegateway_Form_Order_MangoPay extends Engine_Form {

    public function init() {
        parent::init();

        $this->setTitle('MangoPay Account Configuration');

        $description = $this->getTranslator()->translate('Add steps here to get the gateway credentials.');

        $description .= ' <br/> ' . '<div id="show_mangopay_form_massges"></div>';
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);




        /*
         * Add the code to create form elements for entering gateway credentials.
         */




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
