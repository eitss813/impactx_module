<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        $PRODUCT_TYPE = 'sitestaticpage';
        $PLUGIN_TITLE = 'Sitestaticpage';
        $PLUGIN_VERSION = '5.0.0';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'HTML Blocks and Multiple Forms Plugin';
        $PRODUCT_TITLE = 'HTML Blocks and Multiple Forms Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.7.1p6';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }

        parent::onPreinstall();
    }

    function onInstall() {
        $db = $this->getDb();
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitestaticpage';");
        $sitestaticpage_pages_table = $db->query('SHOW TABLES LIKE \'engine4_sitestaticpage_pages\'')->fetch();
        if (!empty($sitestaticpage_pages_table)) {
            $column_widget_exist = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_pages LIKE 'page_widget'")->fetch();
            if (!empty($column_widget_exist))
                $db->query("ALTER TABLE `engine4_sitestaticpage_pages`
  DROP `page_widget`");
        }

        $sitestaticpage_values_table = $db->query('SHOW TABLES LIKE \'engine4_sitestaticpage_page_fields_values\'')->fetch();
        if (!empty($sitestaticpage_values_table)) {
            $column_member_id_exist = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_page_fields_values LIKE 'member_id'")->fetch();
            $column_form_id_exist = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_page_fields_values LIKE 'form_id'")->fetch();

            if (empty($column_member_id_exist))
                $db->query("ALTER TABLE `engine4_sitestaticpage_page_fields_values` ADD `member_id` INT NOT NULL");
            if (empty($column_form_id_exist))
                $db->query("ALTER TABLE `engine4_sitestaticpage_page_fields_values` ADD `form_id` INT NOT NULL");

            $db->query("ALTER TABLE engine4_sitestaticpage_page_fields_values DROP PRIMARY KEY");
            $db->query("ALTER TABLE `engine4_sitestaticpage_page_fields_values` ADD PRIMARY KEY ( `item_id` , `field_id` , `index` , `member_id` ) ;");
        }

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Static Pages & HTML Blocks' WHERE `engine4_core_menuitems`.`name` ='sitestaticpage_admin_main_manage' LIMIT 1 ;");

        $table_columns = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_pages")->fetchAll();
        foreach ($table_columns as $cloumn) {
            if (strstr($cloumn['Field'], 'body')) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->query("ALTER TABLE `engine4_sitestaticpage_pages` CHANGE " . $cloumn['Field'] . " " . $cloumn['Field'] . " LONGTEXT NOT NULL");
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_sitestaticpage_pages', array('page_id', $cloumn['Field']));
                $results = $select->query()->fetchAll();
                foreach ($results as $result) {
                    $output = $this->getstring($result[$cloumn['Field']], '[static_form', ']');
                    $params = serialize($output);
                    $page_id = $result['page_id'];
                    if ($cloumn['Field'] == 'body') {
                        $db->query('UPDATE `engine4_sitestaticpage_pages` SET `params` = \'' . $params . '\' WHERE `engine4_sitestaticpage_pages`.`page_id` = " ' . $page_id . ' ";');
                    } else {
                        $body_field = explode('body_', $cloumn['Field']);
                        $params_field = 'params_' . $body_field[1];
                        $db->query("UPDATE `engine4_sitestaticpage_pages` SET  " . $params_field . " =  '" . $params . "'  WHERE `engine4_sitestaticpage_pages`.`page_id` = '" . $page_id . "' ;");
                    }
                }
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siterecaptcha')
                ->where('enabled = ?', 1);
        $is_siterecaptcha_object = $select->query()->fetchObject();
        if (!empty($is_siterecaptcha_object)) {
            $engine4StaticPageFieldOptions = $db->query("SHOW TABLES LIKE 'engine4_sitestaticpage_page_fields_options'")->fetch();
            if ($engine4StaticPageFieldOptions) {
                $sitestaticpage_recaptcha_column = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_page_fields_options LIKE 'recaptcha'")->fetch();
                if (empty($sitestaticpage_recaptcha_column)) {
                    $db->query("ALTER TABLE `engine4_sitestaticpage_page_fields_options` ADD `recaptcha` TINYINT( 1 ) DEFAULT NULL");
                }
            }
        }
        parent::onInstall();
    }

    function onDisable() {
        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_menuitems', array('name'))
                ->where('name LIKE "%sitestaticpage%"')
                ->where('enabled = ?', 1);
        $results = $select->query()->fetchAll();
        foreach ($results as $result) {
            $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = '" . $result['name'] . "';");
        }

        parent::onDisable();
    }

    function onEnable() {
        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_menuitems', array('name'))
                ->where('name LIKE "%sitestaticpage%"')
                ->where('enabled = ?', 0);
        $results = $select->query()->fetchAll();
        foreach ($results as $result) {
            $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = '" . $result['name'] . "';");
        }

        parent::onEnable();
    }

    public function onPostInstall() {
        //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
        $moduleName = 'sitestaticpage';
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$moduleName','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', $moduleName)
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/' . $moduleName . '/integrated/0/redirect/install');
                }
            }
        }
        //END - SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
    }

    public function getstring($string, $start, $end) {

        $found = array();
        $pos = 0;
        while (true) {
            $pos = strpos($string, $start, $pos);
            if ($pos === false) { // Zero is not exactly equal to false...
                return $found;
            }
            $pos += strlen($start);
            $len = strpos($string, $end, $pos) - $pos;
            $found[] = substr($string, $pos, $len);
        }
    }

}
