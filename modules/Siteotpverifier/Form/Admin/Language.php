<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Filter.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Siteotpverifier_Form_Admin_Language extends Engine_Form
{
    public function init() {
        // Change description decorator
        $this->setTitle('Message Editor')
        ->setDescription("Here, you can set the template to be sent in SMS with the OTP code for various process.You can use [code], [website_name], [username] and [expirytime] text for OTP code, Website name, Member name and Expire time of code.[Note: SMS must contain [code] variable.");

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

        $translate = Zend_Registry::get('Zend_Translate');
        // Prepare language list
        $languageList = $translate->getList();
        $localeObject = Zend_Registry::get('Locale');

        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }
        $localeMultiOptions = array();
        foreach ($languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName;
            } else {
                $localeMultiOptions[$key] = 'Unknown';
            }
        }
        $localeMultiOptions = array_merge(array($defaultLanguage => $defaultLanguage
                ), $localeMultiOptions);
        
        // Element: level_id
        $this->addElement('Select', 'language', array(
            'label' => 'Language',
            'multiOptions' => $localeMultiOptions,
            'onchange' => 'javascript:fetchLanguageSettings(this.value);',
        ));
        
        // for signup
        $this->addElement('Textarea', 'signup', array(
            'rows' => 1,
            'label' => 'Signup Process',
            'description' => 'Enter the message you want to send with the OTP at the time of signup.',
            'value' => 'Your verification code for registration is [code].This code will expire in [expirytime] .',
        ));
        
         $this->addElement('Textarea', 'login', array(
            'rows' => 1,
            'label' => 'Login Process',
            'description' => 'Enter the message you want to send with the OTP at the time of login.',
            'value' => 'Your verification code for login is [code].This code will expire in [expirytime] .',
        ));
         
        $this->addElement('Textarea', 'forget', array(
            'rows' => 1,
            'label' => 'Forget Password',
            'description' => 'Enter the message you want to send with the OTP to reset the account password.',
            'value' => 'Your verification code for reseting password is [code].This code will expire in [expirytime] .',
        ));
        
        $this->addElement('Textarea', 'add', array(
            'rows' => 1,
            'label' => 'Adding Phone Number',
            'description' => 'Enter the message you want to send with the OTP for adding the phone number.',
            'value' => 'Your verification code for add phone number is [code].This code will expire in [expirytime] .',
        ));
        
        $this->addElement('Textarea', 'edit', array(
            'rows' => 1,
            'label' => 'Editing Phone Number',
            'description' => 'Enter the message you want to send with the OTP for editing the phone number.',
            'value' => 'Your verification code for editing phone number is [code].This code will expire in [expirytime] .',
        ));
        
        // Add submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'order' => 100000,
        ));      
    }
}