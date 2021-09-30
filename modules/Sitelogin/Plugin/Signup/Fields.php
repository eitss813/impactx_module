<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Fields.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Plugin_Signup_Fields extends User_Plugin_Signup_Fields {
    protected $_adminFormClass = 'Sitelogin_Form_Admin_Signup_Fields';
    public function getForm() {
        if (Engine_Api::_()->hasModuleBootstrap("sitesubscription")) {
            // Get Profile-type mapping setting
            $getMappingSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitesubscription.profile.mapping', '0');
            // Get package id from subscription session 
            $subscription_session = new Zend_Session_Namespace('Sitesubscription_Plugin_Signup_Subscription');
            $package_id = $subscription_session->data['package_id'];
            $packagesTable = Engine_Api::_()->getDbtable('profiletypemapping', 'sitesubscription');
            $mappedProfile = $packagesTable->getProfileMap($package_id);
            $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
            if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                $profileTypeField = $topStructure[0]->getChild();
                $options = $profileTypeField->getOptions();
            }
            foreach ($options as $value) {
                $options_array[] = $value['option_id'];
            }
        }
        if (is_null($this->_form)) {
            parent::getForm();
            $formArgs = array();
            if (Engine_Api::_()->hasModuleBootstrap("sitesubscription")) {
                // Preload profile type field stuff
                $profileTypeField = $this->getProfileTypeField();
                if ($getMappingSetting == '1' && in_array($mappedProfile, $options_array)) {
                    $formArgs = array(
                        'topLevelId' => $profileTypeField->field_id,
                        'topLevelValue' => $mappedProfile,
                    );
                } else if ($profileTypeField) {
                    $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
                    $profileTypeValue = @$accountSession->data['profile_type'];
                    if ($profileTypeValue) {
                        $formArgs = array(
                            'topLevelId' => $profileTypeField->field_id,
                            'topLevelValue' => $profileTypeValue,
                        );
                    } else if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                        if (count($options) == 1) {
                            $formArgs = array(
                                'topLevelId' => $profileTypeField->field_id,
                                'topLevelValue' => $options[0]->option_id,
                            );
                        }
                    }
                }
            } else {
                $profileTypeField = $this->getProfileTypeField();
                if ($profileTypeField) {
                    $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
                    $profileTypeValue = @$accountSession->data['profile_type'];
                    if ($profileTypeValue) {
                        $formArgs = array(
                            'topLevelId' => $profileTypeField->field_id,
                            'topLevelValue' => $profileTypeValue,
                        );
                    } else {
                        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                            $profileTypeField = $topStructure[0]->getChild();
                            $options = $profileTypeField->getOptions();
                            if (count($options) == 1) {
                                $formArgs = array(
                                    'topLevelId' => $profileTypeField->field_id,
                                    'topLevelValue' => $options[0]->option_id,
                                );
                            }
                        }
                    }
                }
            }

            // Engine_Loader::loadClass($this->_formClass);
            // $class = $this->_formClass;
            // $this->_form = new $class($formArgs);
            // $data = $this->getSession()->data;

            if (!empty($_SESSION['linkedin_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->linkedinIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');
                    if (isset($_SESSION['linkedin_access_token']) && !empty($_SESSION['linkedin_access_token'])) {
                        $userDetails = Engine_Api::_()->getDbtable('linkedin', 'sitelogin')->fetch();
                    }
                    $fb_data = array();
                    $apiInfo['last_name'] = isset($userDetails->lastName) ? $userDetails->lastName : "";
                    $apiInfo['first_name'] = isset($userDetails->firstName) ? $userDetails->firstName : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    if (isset($apiInfo['birthday']) && !empty($apiInfo['birthday'])) {
                        $fb_data['birthdate'] = date("Y-m-d", strtotime($fb_data['birthday']));
                    }
                    // populate fields, using linkedin data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['google_signup'])) {
                try {
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    $loginEnable = Engine_Api::_()->getDbtable('google', 'sitelogin')->googleIntegrationEnabled();
                    if (empty($loginEnable)) {
                        return;
                    }
                    $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
                    $apiInfoObj = $googleTable->getGoogleInstance();
                    $fb_data = array();
                    $apiInfo['last_name'] = isset($apiInfoObj->familyName) ? $apiInfoObj->familyName : "";
                    $apiInfo['first_name'] = isset($apiInfoObj->givenName) ? $apiInfoObj->givenName : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    if (isset($apiInfo['birthday']) && !empty($apiInfo['birthday'])) {
                        $fb_data['birthdate'] = date("Y-m-d", strtotime($fb_data['birthday']));
                    }
                    // populate fields, using google data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['instagram_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->instagramIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sitelogin');
                    if (isset($_SESSION['instagram_access_token']) && !empty($_SESSION['instagram_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('instagram', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->data;
                    }
                    $fb_data = array();
                    $apiInfo['first_name'] = isset($userDetails->full_name) ? $userDetails->full_name : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    // populate fields, using instagram data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['pinterest_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->pinterestIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    $pinterestTable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin');
                    if (isset($_SESSION['pinterest_access_token']) && !empty($_SESSION['pinterest_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('pinterest', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->data;
                    }
                    $fb_data = array();
                    $apiInfo['last_name'] = isset($userDetails->last_name) ? $userDetails->last_name : "";
                    $apiInfo['first_name'] = isset($userDetails->first_name) ? $userDetails->first_name : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    // populate fields, using pinterest data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['yahoo_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->yahooIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {
                    $settings = Engine_Api::_()->getDbtable('settings', 'core');
                    $yahooTable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin');
                    if (isset($_SESSION['yahoo_access_token']) && !empty($_SESSION['yahoo_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('yahoo', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->profile;
                    }
                    $fb_data = array();
                    $apiInfo['last_name'] = isset($userDetails->familyName) ? $userDetails->familyName : "";
                    $apiInfo['first_name'] = isset($userDetails->givenName) ? $userDetails->givenName : "";
                    $apiInfo['birthdate'] = isset($userDetails->birthdate) ? $userDetails->birthdate : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    if (isset($apiInfo['birthdate']) && !empty($apiInfo['birthdate'])) {
                        $fb_data['birthdate'] = date("Y-m-d", strtotime($fb_data['birthdate']));
                    }
                    // populate fields, using yahoo data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['vk_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('vk', 'sitelogin')->vkIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {

                    if (isset($_SESSION['vk_access_token']) && !empty($_SESSION['vk_access_token'])) {
                        $userDetailsdata = Engine_Api::_()->getDbtable('vk', 'sitelogin')->fetch();
                        $userDetails= $userDetailsdata->response[0];
                    }
                    $fb_data = array();
                    $apiInfo['last_name'] = isset($userDetails->last_name) ? $userDetails->last_name : "";
                    $apiInfo['first_name'] = isset($userDetails->first_name) ? $userDetails->first_name : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    // populate fields, using vk data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['outlook_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->outlookIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {

                    if (isset($_SESSION['outlook_access_token']) && !empty($_SESSION['outlook_access_token'])) {
                        $userDetails = Engine_Api::_()->getDbtable('outlook', 'sitelogin')->fetch();
                    }
                    $fb_data = array();
                    $apiInfo['last_name'] = isset($userDetails->surname) ? $userDetails->surname : "";
                    $apiInfo['first_name'] = isset($userDetails->givenName) ? $userDetails->givenName : "";
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    // populate fields, using outlook data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($_SESSION['flickr_signup'])) {
                $loginEnable = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->flickrIntegrationEnabled();
                if (empty($loginEnable)) {
                    return;
                }
                try {

                    if (isset($_SESSION['flickr_access_token']) && !empty($_SESSION['flickr_access_token'])) {
                        $userDetails = Engine_Api::_()->getDbtable('flickr', 'sitelogin')->fetch();
                    }
                    $fb_data = array();
                    $realname=isset($userDetails['name']) ? $userDetails['name'] : "";
                    if(!empty($realname)) {
                        $realname=explode(" ",$realname);
                        $apiInfo['last_name'] = !empty($realname[1]) ? $realname[1] : "";
                        $apiInfo['first_name'] = !empty($realname[0]) ? $realname[0] : "";                     
                    }
                    $fb_data = array();
                    $fb_keys = array('first_name', 'last_name', 'birthday', 'birthdate');
                    foreach ($fb_keys as $key) {
                        if (isset($apiInfo[$key])) {
                            $fb_data[$key] = $apiInfo[$key];
                        }
                    }
                    // populate fields, using flickr data
                    $struct = $this->_form->getFieldStructure();
                    foreach ($struct as $fskey => $map) {
                        $field = $map->getChild();
                        if ($field->isHeading())
                            continue;
                        if (isset($field->type) && in_array($field->type, $fb_keys)) {
                            $el_key = $map->getKey();
                            $el_val = $fb_data[$field->type];
                            $el_obj = $this->_form->getElement($el_key);
                            if ($el_obj instanceof Zend_Form_Element &&
                                    !$el_obj->getValue()) {
                                $el_obj->setValue($el_val);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Silence?
                }
            }
            if (!empty($data)) {
                foreach ($data as $key => $val) {
                    $el = $this->_form->getElement($key);
                    if ($el instanceof Zend_Form_Element) {
                        $el->setValue($val);
                    }
                }
            }
        }
        return $this->_form;
    }
}