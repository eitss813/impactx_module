<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2017-03-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Installer extends Engine_Package_Installer_Module {

    private $_signupPorcess = array(
        'User_Plugin_Signup_Fields' => 'Sitelogin_Plugin_Signup_Fields',
        'User_Plugin_Signup_Photo' => 'Sitelogin_Plugin_Signup_Photo',
    );

    function onInstall() {
        $db = $this->getDb();
        $db->query("DELETE FROM `engine4_core_menuitems` WHERE `name` = 'sitelogin_admin_main_google'");
        $db->query("DELETE FROM `engine4_core_menuitems` WHERE `name` = 'sitelogin_admin_main_linkedin'");
        $db->query("UPDATE  `engine4_core_menuitems` SET  `label` =  'SEAO - Social Login and Sign-up Plugin' WHERE  `engine4_core_menuitems`.`name` ='core_admin_main_plugins_sitelogin';");
        $this->enableSignupProcess();
        parent::onInstall();
    }

    public function onEnable() {
        $this->enableSignupProcess();
        parent::onEnable();
    }
    
    public function onDisable() {
        $db = $this->getDb();
        try {
            $db->beginTransaction();
            foreach ($this->_signupPorcess as $coreProccess => $loginProcess) {
                $db->update('engine4_user_signup', array('class' => $coreProccess), array('class = ?' => $loginProcess));
            }
            $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  'sitesubscription'")->fetch();
            if( !empty($isMod) && !empty($isMod['enabled']) ) {
              $db->update('engine4_user_signup', array('class' => 'Sitesubscription_Plugin_Signup_Fields'), array('class = ?' => 'User_Plugin_Signup_Fields'));
                }
            parent::onDisable();
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            $errorMsg .= '<div class="tip"><span>Error: ' . $e->getMessage() . '</span></div>';
            $this->_error($errorMsg);
        }
    }
    private function enableSignupProcess() {
        $db = $this->getDb();
        try {
            $db->beginTransaction();
            $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  'sitesubscription'")->fetch();
            foreach ($this->_signupPorcess as $coreProccess => $loginProcess) {
                $db->update('engine4_user_signup', array('class' => $loginProcess), array('class = ?' => $coreProccess));
            }
            $db->update('engine4_user_signup', array('class' => 'Sitelogin_Plugin_Signup_Fields'), array('class = ?' => 'Sitesubscription_Plugin_Signup_Fields'));
            $db->update('engine4_user_signup', array('class' => 'User_Plugin_Signup_Account'), array('class = ?' => 'Sitelogin_Plugin_Signup_Account'));
            $db->query("INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES ('Sitelogin_Plugin_Signup_Fields', 2, 1) ");
            $db->query("INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES ('Sitelogin_Plugin_Signup_Photo', 3, 1) ");
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
        }
    }
}