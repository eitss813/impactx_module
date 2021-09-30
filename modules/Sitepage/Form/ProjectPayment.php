<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Projectpayment extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->setTitle('Project Payment')
            ->setAttrib('name', 'sitepages_project_payment');
        $description = $this->getTranslator()->translate('SITECROWDFUNDING_FORM_PAYPAL_DESCRIPTION');
        $description = vsprintf($description, array(
            'https://www.paypal.com/signup/account',
            'https://www.paypal.com/webapps/customerprofile/summary.view',
            'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-api-access',
            'https://developer.paypal.com/docs/classic/api/apiCredentials/',
            'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
            'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify',
            (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'sitecrowdfunding',
                'controller' => 'project',
                'action' => 'paymentInfo'
            ), 'default', true),
        ));

        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Account Configuration')));

        $description = sprintf(Zend_Registry::get('Zend_Translate')->translate('Below, you can configure your Paypal Account to receive payments through %s. This information should be accurately provided and enabled.'), $siteTitle) . ' <br/> ' . $description . '<div id="show_paypal_form_massges"></div>';
        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $email = Zend_Registry::get('Zend_Translate')->_('Paypal Email');
        // Init email
        $this->addElement('Text', 'payment_email', array(
            'label' => $email,
            'required' => true,
            'allowEmpty' => false,
            'autocomplete'=>'false',
            'filters' => array(
                'StringTrim',
            ),
            'validators' => array(
                'EmailAddress'
            ),

            // Fancy stuff
            'autofocus' => 'autofocus',
            'inputType' => 'email',
            'class' => 'text',
        ));
        $this->payment_email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

        //Username
        $this->addElement('Text', 'payment_username', array(
            'label' => 'API Username',
            'allowEmpty' => false,
            'validators' => array(
                array('StringLength', true, array(5, 64))
            ),
            'autocomplete'=>'new password',
            'required' => true
        ));
        //$this->payment_username->getValidator('StringLength')->getHostnameValidator()->setValidateTld(false);
        //password
        $this->addElement('Text', 'payment_password', array(
            'label' => 'API Password',
            'allowEmpty' => false,
            'validators' => array(
                array('StringLength', true, array(6, 64))
            ),
            'autocomplete'=>'new password',
            'required' => true
        ));

        //password
        //  $password = Zend_Registry::get('Zend_Translate')->_('API Password');
        // Init password
        //   $this->addElement('Password', 'password', array(
        //  'label' => $password,
        //   'required' => true,
        //  'allowEmpty' => false,
        //  'autocomplete'=>'false',
        //  'filters' => array(
        //      'StringTrim',
        //   ),
        // ));

        //Signature
        $this->addElement('Text', 'payment_signature', array(
            'label' => 'API Signature',
            'allowEmpty' => false,
            'required' => true
        ));


        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

}

?>
