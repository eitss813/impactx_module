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
class Sitepage_Form_Donate_Receipt extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->setTitle('Donate Receipt')
            ->setAttrib('name', 'sitepages_donate_receipt');
        $description = $this->getTranslator()->translate('Donation Receipt');


        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

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
