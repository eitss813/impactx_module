<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Account.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Signup_Account extends User_Form_Signup_Account {

    public function init() {
        parent::init();
        if (!empty($_SESSION['outlook_signup']) ||!empty($_SESSION['vk_signup']) ||
            !empty($_SESSION['yahoo_signup']) ||!empty($_SESSION['flickr_signup']) ||
            !empty($_SESSION['pinterest_signup']) ||!empty($_SESSION['instagram_signup']) || !empty($_SESSION['google_signup']) || !empty($_SESSION['linkedin_signup'])) {
        
            $this->removeElement('password');
            $this->removeElement('passconf');            
        }
        
        if (Engine_Api::_()->hasModuleBootstrap("sitesubscription")) {
            // Get Profile-type mapping setting
            $order = $this->getElement('profile_type')->getOrder();
            $this->removeElement('profile_type');
            $getMappingSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitesubscription.profile.mapping', '0');

            // Get package id from subscription session 
            $subscription_session = new Zend_Session_Namespace('Sitesubscription_Plugin_Signup_Subscription');
            $package_id = $subscription_session->data['package_id'];
            if ($package_id !== null) {
                $packagesTable = Engine_Api::_()->getDbtable('profiletypemapping', 'sitesubscription');
                $package = $packagesTable->select()->where('`package_id` = ?', $package_id)->limit(1)->query()->fetch();
            }
            // Element: profile_type
            $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
            if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                $profileTypeField = $topStructure[0]->getChild();
                $options = $profileTypeField->getOptions();

                $tabindex = 1;

                foreach ($options as $value)
                    $options_array[] = $value['option_id'];

                if (in_array($package['option_id'], $options_array) && $getMappingSetting == '1') {
                    $this->addElement('Hidden', 'profile_type', array(
                        'order' => $order,
                        'value' => (string) $package['option_id']
                    ));
                } else if (count($options) > 1) {
                    $options = $profileTypeField->getElementParams('user');
                    unset($options['options']['order']);
                    unset($options['options']['multiOptions']['0']);
                    $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
                        'required' => true,
                        'order' => $order,
                        'allowEmpty' => false,
                        'tabindex' => $tabIndex++,
                    )));
                } else if (count($options) == 1) {
                    $this->addElement('Hidden', 'profile_type', array(
                        'order' => $order,
                        'value' => $options[0]->option_id
                    ));
                }
            }
        } 
        // Set default action
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true));
    }
   
}
