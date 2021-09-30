<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Yahoo.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Yahoo extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Yahoo Integration')
                ->setDescription('Integrate Yahoo Login & Signup')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod("POST");
        

        $description = $this->getTranslator()->translate('You can now Login to SocialEngine based website using Yahoo Apps. To do so, create an application through the <a href="https://developer.yahoo.com" target="_blank">Yahoo Developers</a> page and put the needful credentials here.');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'clientId', array(
            'label' => 'Yahoo Client ID',
            'description' => 'Please put the client id provided by Yahoo when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('Text', 'clientSecret', array(
            'label' => 'Yahoo Client Secret Key',
            'description' => 'This is a string of letters and numbers provided by Yahoo when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'yahooOptions', array(
            'label' => 'Login Buttons',
            'description' => 'Select the pages where you want Yahoo Login Button to appear.',
            'multiOptions' => array(
                'login' => 'Login Only',
                'signup' => 'Signup Only',
            ),
            'value' => 'login'
        ));
        $this->yahooOptions->getDecorator('Description')->setOption('placement', 'PREPEND');

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
