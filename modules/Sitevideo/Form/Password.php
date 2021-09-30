<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Password.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Password extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Private Video')
                ->setDescription('This is password protected video.')
                ->setAttrib('id', 'sitevideo_check_password_protection');

        $this->addElement('Text', 'password', array(
            'label' => 'Password',
            'description' => 'To view this video, please provide the correct password.',
            'required' => true,
            
            'allowEmpty' => false
        ));
        $this->password->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Button', 'submitForm', array(
            'label' => 'Access',
            'onclick' => "checkPasswordProtection($('sitevideo_check_password_protection'));return false",
            'type' => 'submit',
        ));
    }

}
