<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Vk.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Vk extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Vkontakte Integration')
                ->setDescription('Integrate Vkontakte Login & Signup')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod("POST");
        

        $description = $this->getTranslator()->translate('You can now Login to SocialEngine based website using Vkontakte Apps. To do so, create an application through the <a href="https://vk.com/dev" target="_blank">Vkontakte Developers</a> page and put the needful credentials here.<br/>[Note: You need to get extra permission for emails if you want to enable quick signup for vk users.]');
        $this->setDescription($description);

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'clientId', array(
            'label' => 'Vkontakte Application ID',
            'description' => 'Please put the application id provided by Vkontakte when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('Text', 'clientSecret', array(
            'label' => 'Vkontakte Secure Key',
            'description' => 'This is a string of letters and numbers provided by Vkontakte when you have created an application.',
            'filters' => array(
                'StringTrim',
            ),
        ));

        $this->addElement('MultiCheckbox', 'vkOptions', array(
            'label' => 'Login Buttons',
            'description' => 'Select the pages where you want Vkontakte Login Button to appear.',
            'multiOptions' => array(
                'login' => 'Login Only',
                'signup' => 'Signup Only',
            ),
            'value' => 'login'
        ));
        $this->vkOptions->getDecorator('Description')->setOption('placement', 'PREPEND');

        $this->addElement('Checkbox', 'quickEnable', array(
            'description' => 'Quick Signup via Vkontakte',
            'label' => 'You can enable Quick Signup where users do not need to fill any details while signup. They will directly be able to join your community using Vkontakte credentials. [Note: For this option you need to choose member level and profile type for users joining via Vkontakte.] ',
            'value' => '',
            'onchange' => 'QuickSignup()'
        ));
        $this->quickEnable->getDecorator('Description')->setOption('placement', 'PREPEND');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $URL = $view->baseUrl() . "/admin/user/fields";
        $click = '<a href="' . $URL . '" target="_blank">here</a>';
        $description = "Select the Profile Type you want to assign to the user in case of Quick Signup via Vkontakte. You can manage the profile type from " . $click . ".";
        $profileTypes = Engine_Api::_()->sitelogin()->getProfileTypes();
        $this->addElement('Select', 'vkProfileType', array(
            'label' => 'Profile Type',
            'description' => $description,
            'multiOptions' => $profileTypes,
            'value' => reset($profileTypes)
        ));
        $this->vkProfileType->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $URL = $view->baseUrl() . "/admin/authorization/level";
        $click = '<a href="' . $URL . '" target="_blank">here</a>';
        $description = "Select the Member Level you want to assign to the user in case of Quick Signup via Vkontakte. You can manage the member level from " . $click . ".";
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
