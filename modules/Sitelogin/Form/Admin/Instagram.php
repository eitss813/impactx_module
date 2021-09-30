<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Instagram.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Instagram extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Instagram Integration')
                ->setDescription('Integrate Instagram Login & Signup')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod("POST");
        

        $description = $this->getTranslator()->translate('You can now Login to SocialEngine based website using Instagram Apps. To do so, create an application through the <a href="https://www.instagram.com/developer/" target="_blank">Instagram Developers</a> page and put the needful credentials here.');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'clientId', array(
            'label' => 'Instagram Client ID',
            'description' => 'Please put the client id provided by Instagram when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('Text', 'clientSecret', array(
            'label' => 'Instagram Client Secret Key',
            'description' => 'This is a string of letters and numbers provided by Instagram when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'instagramOptions', array(
            'label' => 'Login Buttons',
            'description' => 'Select the pages where you want Instagram Login Button to appear.',
            'multiOptions' => array(
                'login' => 'Login Only',
                'signup' => 'Signup Only',
            ),
            'value' => 'login'
        ));
        $this->instagramOptions->getDecorator('Description')->setOption('placement', 'PREPEND');
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
