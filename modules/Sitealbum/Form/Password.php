<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Password.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Password extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Private Photo Album')
                ->setDescription('This is password protected Photo Album.')
                ->setAttrib('id', 'sitealbum_check_password_protection');

        $this->addElement('Text', 'password', array(
            'label' => 'Password',
            'description' => 'To view this photo album, please provide the correct password.',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                ),
        ));
				$this->password->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Button', 'submitForm', array(
            'label' => 'Access',
            'onclick' => "checkPasswordProtection($('sitealbum_check_password_protection'));return false",
            'type' => 'submit',
        ));
    }

}
