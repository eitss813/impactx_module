<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Pinterest.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Pinterest extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Pinterest Integration')
                ->setDescription('Integrate Pinterest Login & Signup')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod("POST");
        

        $description = $this->getTranslator()->translate('You can now Login to SocialEngine based website using Pinterest Apps. To do so, create an application through the <a href="https://developers.pinterest.com/" target="_blank">Pinterest Developers</a> page and put the needful credentials here.<br/>[Note: Site URL must use the HTTPS protocols.]');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'clientId', array(
            'label' => 'Pinterest App ID',
            'description' => 'Please put the app id provided by Pinterest when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('Text', 'clientSecret', array(
            'label' => 'Pinterest App Secret Key',
            'description' => 'This is a string of letters and numbers provided by Pinterest when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'pinterestOptions', array(
            'label' => 'Login Buttons',
            'description' => 'Select the pages where you want Pinterest Login Button to appear.',
            'multiOptions' => array(
                'login' => 'Login Only',
                'signup' => 'Signup Only',
            ),
            'value' => 'login'
        ));
        $this->pinterestOptions->getDecorator('Description')->setOption('placement', 'PREPEND');

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
