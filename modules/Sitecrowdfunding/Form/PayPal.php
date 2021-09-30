<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paypal.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_PayPal extends Engine_Form {

    public function init() {
        parent::init();

        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', '0')) {
            $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Account Configuration')));
        } else {
            $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Account for receiving payments from %s'), $siteTitle));
        }

        $this->setName('sitecrowdfunding_payment_info');

        $host = $_SERVER['HTTP_HOST'];
        if($host == 'stage.impactx.co'){
            $host = $host . '/network/';
        }else{
            $host = $host . '/net/';
        }

        $description = $this->getTranslator()->translate('SITECROWDFUNDING_FORM_PAYPAL_DESCRIPTION');
        $description = vsprintf($description, array(
            'https://www.paypal.com/signup/account',
            'https://www.paypal.com/webapps/customerprofile/summary.view',
            'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-api-access',
            'https://developer.paypal.com/docs/classic/api/apiCredentials/',
            'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
            'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify',
            (_ENGINE_SSL ? 'https://' : 'http://') . $host . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'sitecrowdfunding',
                'controller' => 'project',
                'action' => 'paymentInfo'
                    ), 'default', true),
        ));
        $description = sprintf(Zend_Registry::get('Zend_Translate')->translate('Below, you can configure your Paypal Account to receive payments through %s. This information should be accurately provided and enabled.'), $siteTitle) . ' <br/> ' . $description . '<div id="show_paypal_form_massges"></div>';
        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'email', array(
            'label' => 'Paypal Email',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true)
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
        // Elements
        $this->addElement('Text', 'username', array(
            'label' => 'API Username',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'password', array(
            'label' => 'API Password',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'signature', array(
            'label' => 'API Signature',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Zend_Filter_StringTrim(),
            ),
        ));

        $showExtraInfo = true;
        $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
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
