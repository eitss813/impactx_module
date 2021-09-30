<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    private $_profileTypeId = false;

    /**
     * Validation: user signup account form
     * 
     * @return array
     */
    public function getSignupAccountFormValidations($formValidators = array()) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $options = array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'email'));

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $showBothPhoneAndEmail = $settings->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);
        $reqphoneno = !empty($showBothPhoneAndEmail) && $settings->getSetting('siteotpverifier.singupRequirePhone', 1);


        if (!empty($showBothPhoneAndEmail)) {
            $formValidators['email'] = $this->getEmailValidator($options);
             if($reqphoneno || !empty($_REQUEST['phoneno']))
            $formValidators['phoneno'] = $this->getMobileValidator();
        } else {
            if (!strstr($_REQUEST['emailaddress'], '@')) {
                $formValidators['emailaddress'] = $this->getMobileValidator();
		$formValidators['email'] = $this->getEmailValidator($options);
            } else {
                $formValidators['emailaddress'] = $this->getEmailValidator($options);
            }
        }


        if ($settings->getSetting('user.signup.inviteonly') > 0) {
            $formValidators['code'] = array(
                'required' => true,
                'allowEmpty' => false,
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite Code'),
            );
        }

        if ($settings->getSetting('user.signup.random', 0) == 0 && empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id'])) {
            $formValidators['password'] = $this->getPasswordValidator();
            $formValidators['passconf'] = $this->getPasswordValidator();
        }

        if ($settings->getSetting('user.signup.username', 1) > 0) {
            $formValidators['username'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('Alnum', true),
                    array('StringLength', true, array(4, 64)),
                    array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
                    array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'username'))
                ),
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Address'),
            );
        }

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            if (COUNT($options) > 1) {
                $formValidators['profile_type'] = array(
                    'required' => true,
                    'allowEmpty' => false,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Type'),
                );
            }
        }

        $formValidators['timezone'] = array(
            'required' => true,
            'allowEmpty' => false,
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Timezone'),
        );

        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();
        if (COUNT($languageList) > 1) {
            $formValidators['language'] = array(
                'required' => true,
                'allowEmpty' => false,
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Language'),
            );
        }

        if ($settings->getSetting('user.signup.terms', 1) == 1) {
            $formValidators['terms'] = array(
                'required' => true,
                'allowEmpty' => false,
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Terms of Service'),
            );
        }
        
        return $formValidators;
    }

}
