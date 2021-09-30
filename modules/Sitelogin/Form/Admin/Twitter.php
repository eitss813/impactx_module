<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Twitter.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Twitter extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Twitter Integration')
                ->setDescription('Integrate Twitter Login')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod("POST");
        
        $description = $this->getTranslator()->translate('You can now Login to SocialEngine based website using Twitter. To do so, create an application through the <a href="https://dev.twitter.com/" target="_blank">Twitter Developers</a> page and put the needful credentials here.');
        $this->setDescription($description);
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'clientId', array(
            'label' => 'Twitter Api Consumer ID',
            'description' => 'Please put the api consumer id provided by Twitter when you have created a application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('Text', 'clientSecret', array(
            'label' => 'Twitter Api Consumer Key',
            'description' => 'This is a character string of letters and numbers provided by Twitter when you have created a application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'twitterOptions', array(
            'label' => 'Login Buttons',
            'description' => 'Select the pages where you want Twitter Login Button to appear.',
            'multiOptions' => array(
                'login' => 'Login Form',
                'signup' => 'Signup Form',
            ),
            'value' => 'login'
        ));
        $this->twitterOptions->getDecorator('Description')->setOption('placement', 'PREPEND');
               
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
