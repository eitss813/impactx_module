<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Facebook.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Facebook extends Engine_Form {

    public function init() {
        $this->setTitle('Facebook Integration')
                ->setDescription('Integrate Facebook Login')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod("POST");

        $description = $this->getTranslator()->translate('You can now Login to SocialEngine based website using Facebook. To do so, create an application through the <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a> page and put the needful credentials here.');
        $this->setDescription($description);
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'clientId', array(
            'label' => 'Facebook App Id',
            'description' => 'Please put the app id provided by Facebook when you have created a application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('Text', 'clientSecret', array(
            'label' => 'Facebook App Secret Key',
            'description' => 'This is a character string of letters and numbers provided by Facebook when you have created a application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'facebookOptions', array(
            'label' => 'Login Buttons',
            'description' => 'Select the pages where you want Facebook Login Button to appear.',
            'multiOptions' => array(
                'login' => 'Login Form',
                'signup' => 'Signup Form',
            ),
            'value' => 'login'
        ));
        $this->facebookOptions->getDecorator('Description')->setOption('placement', 'PREPEND');

        $this->addElement('Checkbox', 'quickEnable', array(
            'description' => 'Quick Signup via Facebook',
            'label' => 'You can enable Quick Signup where users do not need to fill any details while signup. They will directly be able to join your community using Facebook credentials. [Note: For this option you need to choose member level and profile type for users joining via Facebook.]', 'value' => '',
            'onchange' => 'QuickSignup()'
        ));
        $this->quickEnable->getDecorator('Description')->setOption('placement', 'PREPEND');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $URL = $view->baseUrl() . "/admin/user/fields";
        $click = '<a href="' . $URL . '" target="_blank">here</a>';
        $description = "Select the Profile Type you want to assign to the user in case of Quick Signup via Facebook. You can manage the profile type from " . $click . ".";
        $profileTypes = Engine_Api::_()->sitelogin()->getProfileTypes();
        $this->addElement('Select', 'facebookProfileType', array(
            'label' => 'Profile Type',
            'description' => $description,
            'multiOptions' => $profileTypes,
            'value' => reset($profileTypes)
        ));
        $this->getElement('facebookProfileType')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $URL = $view->baseUrl() . "/admin/authorization/level";
        $click = '<a href="' . $URL . '" target="_blank">here</a>';
        $description = "Select the Member Level you want to assign to the user in case of Quick Signup via Facebook. You can manage the member level from " . $click . ".";
        $levelMultiOptions = array();
        $defaultLevelId = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel()->level_id;
        $levelMultiOptions = Engine_Api::_()->sitelogin()->getMemberlLevels();
        $this->addElement('Select', 'memberLevel', array(
            'label' => 'Member Level',
            'description' => $description,
            'multiOptions' => $levelMultiOptions,
            'value' => $defaultLevelId
        ));
        $this->memberLevel->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
