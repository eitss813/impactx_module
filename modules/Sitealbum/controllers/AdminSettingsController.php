<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitealbum_Form_Admin_Global') {
            
        }
        return true;
    }

    public function indexAction() {

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {

            $tableSitetagcheckin = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
            $checkInTableSelect = $tableSitetagcheckin->select()->from($tableSitetagcheckin->info('name'), 'resource_id')->where('resource_type =?', 'album');
            $checkInTableResults = $tableSitetagcheckin->fetchAll($checkInTableSelect);
            $checkInAlbumLocationExist = '';
            foreach ($checkInTableResults as $checkInTableResult) {
                $checkInAlbumLocationExist .= $checkInTableResult['resource_id'] . ',';
            }
            $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');
            $select = $tableSitealbum->select()->from($tableSitealbum->info('name'), array('album_id', 'location'))->where('location <>?', '');
            if (!empty($checkInAlbumLocationExist))
                $select->where($tableSitealbum->info('name') . '.album_id not in (?)', new Zend_Db_Expr(trim($checkInAlbumLocationExist, ',')));
            $this->view->results = $results = $tableSitealbum->fetchAll($select);

            $checkInTablePhotoSelect = $tableSitetagcheckin->select()->from($tableSitetagcheckin->info('name'), 'resource_id')->where('resource_type =?', 'album_photo');
            $checkInTablePhotoResults = $tableSitetagcheckin->fetchAll($checkInTablePhotoSelect);
            $checkInTablePhotoLocationExist = '';
            foreach ($checkInTablePhotoResults as $checkInTablePhotoResult) {
                $checkInTablePhotoLocationExist .= $checkInTablePhotoResult['resource_id'] . ',';
            }
            $tablePhotoSitealbum = Engine_Api::_()->getDbtable('photos', 'sitealbum');
            $select = $tablePhotoSitealbum->select()->from($tablePhotoSitealbum->info('name'), array('photo_id', 'location'))->where('location <>?', '');
            if (!empty($checkInTablePhotoLocationExist))
                $select->where($tablePhotoSitealbum->info('name') . '.photo_id not in (?)', new Zend_Db_Expr(trim($checkInTablePhotoLocationExist, ',')));
            $this->view->resultss = $resultss = $tablePhotoSitealbum->fetchAll($select);

            $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
            $this->view->syncAlbumCount = $addlocationsTable->select()->from($addlocationsTable->info('name'), array('count(*) as sync_album'))->where('object_type LIKE ?', 'album%')->where('resource_type LIKE ?', 'album%')->where('sync_album = ?', 0)->query()->fetchColumn();
        }

        $previousLocationFieldSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1);

        // UPDATE PHOTOS_COUNT
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $results = $select
                ->from('engine4_album_albums', array('album_id'))
                ->where('photos_count = ?', 0)
                ->order('creation_date DESC')
                ->limit(10000)
                ->query()
                ->fetchAll();
        if (!empty($results)) {
            foreach ($results as $result) {
                $select = new Zend_Db_Select($db);
                $photos_count = $select
                        ->from('engine4_album_photos', new Zend_Db_Expr('COUNT(photo_id)'))
                        ->where('album_id = ?', $result['album_id'])
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                $db->query("UPDATE `engine4_album_albums` SET `photos_count` = '$photos_count' WHERE `engine4_album_albums`.`album_id` ='" . $result['album_id'] . "' LIMIT 1 ;");
            }
        }

        $coreTable = Engine_Api::_()->getDbtable('pages', 'core');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $this->view->page_id = $page_id = $coreTable->select()->from($coreTable->info('name'), 'page_id')
                ->where('name = ?', 'header')
                ->query()
                ->fetchColumn();
        $content_id = 0;

        if (!empty($page_id)) {
            $contentTable = Engine_Api::_()->getDbtable('content', 'core');
            $content_id = $contentTable->select()
                    ->from($contentTable->info('name'), 'page_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'seaocore.seaocores-lightbox')
                    ->query()
                    ->fetchColumn();
        }
        $this->view->content_id = $content_id;

        $redirectionPrevious = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.redirection', 'index');

        if ($this->getRequest()->isPost()) {
            if (isset($_POST['sitealbum_location']) && $previousLocationFieldSetting != $_POST['sitealbum_location']) {
                $menuItemTable = Engine_Api::_()->getDbtable('menuItems', 'core');
                $menuItemTable->update(array('enabled' => $_POST['sitealbum_location']), array('name = ?' => 'sitealbum_main_location'));
            }
            if (isset($_POST['sitealbum_category_enabled'])) {
                $menuItemTable = Engine_Api::_()->getDbtable('menuItems', 'core');
                $menuItemTable->update(array('enabled' => $_POST['sitealbum_category_enabled']), array('name = ?' => 'sitealbum_main_categories'));
            }
        }

        $onactive_disabled = array('sitealbum_photolightbox_show', 'sitealbum_photo_badge', 'sitealbum_photo_specialalbum', 'sitealbum_comment_view', 'sitealbum_adtype', 'sitealbum_lightbox_onloadshowthumb', 'submit', 'sitealbum_nonprivacybase', 'main_photo_height', 'main_photo_width', 'normal_photo_height', 'normal_photo_width', 'sitealbum_location', '', 'sitealbum_proximity_search_kilometer', 'seaocore_locationdefault', 'seaocore_locationdefaultmiles', 'sitealbum_rating', 'sitealbum_network', 'sitealbum_default_show', 'sitealbum_networks_type', 'sitealbum_networkprofile_privacy', 'sitealbum_privacybase', 'sitealbumshow_navigation_tabs', 'normallarge_photo_height', 'normallarge_photo_width', 'sitealbum_tag_enabled', 'sitealbum_category_enabled', 'sitealbumrating_update');
        $afteractive_disabled = array('submit_lsetting', 'environment_mode', 'include_in_package');


        $pluginName = 'sitealbum';
        if (!empty($_POST[$pluginName . '_lsettings']))
            $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);

        include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license1.php';
        $this->view->isModsSupport = Engine_Api::_()->sitealbum()->isModulesSupport();

        $redirectionNew = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.redirection', 'index');
        if ($redirectionPrevious != $redirectionNew) {
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->update('engine4_core_menuitems', array('params' => '{"route":"sitealbum_general","action":"' . $redirectionNew . '"}'), array('name = ?' => 'core_main_sitealbum'));
        }
    }

    // ACTION FOR ACTIVIT FEED FILES CHANGES
    public function changesAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_changes');
        $this->view->showTip = 1;
    }

    public function faqAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_faq');
        $this->view->action = 'faq';
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    public function checkAlbumPluginAction() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (!$this->getRequest()->isPost()) {
            // Some websites having database tables: engine4_album_params and engine4_album_features reported problem because of another album plugin that was previously installed on their site. Below code corrects that.
            $this->view->albumPluginTips = false;
            $file_path = APPLICATION_PATH . "/application/modules/Album/settings/manifest.php";
            $is_file = file_exists($file_path);
            $is_module = false;
            if ($is_file) {
                $ret = include $file_path;
                if ($ret['package']['author'] == 'Webligo Developments')
                    $is_module = true;
            }
            $table_engine4_album_params_exist = $db->query("SHOW TABLES LIKE 'engine4_album_params'")->fetch();
            $table_engine4_album_features_exist = $db->query("SHOW TABLES LIKE 'engine4_album_features'")->fetch();
            if ($is_module && empty($table_engine4_album_features_exist) && empty($table_engine4_album_params_exist))
                return;
            $this->view->albumPluginTips = true;
        }else {
            $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'album_admin_main_photos' LIMIT 1;");
            $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'user_home_album' LIMIT 1;");
            $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'user_profile_album' LIMIT 1;");
            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ('mobi_browse_album', 'album', 'Albums', '', '{\"route\":\"sitealbum_general\",\"action\":\"browse\"}', 'mobi_browse', '', 2);");
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules', array('title', 'version'))
                    ->where('name = ?', "album");

            $getModVersion = $select->query()->fetchObject();

            if ($getModVersion->version < '4.8.13') {
                $db->query("ALTER TABLE `engine4_album_albums` CHANGE `type` `type` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL;");
            }
            $column_exist = $db->query("SHOW COLUMNS FROM `engine4_album_photos` LIKE 'order'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_album_photos` ADD COLUMN `order` int(11) unsigned NOT NULL default '0' ;");
                $db->query("UPDATE `engine4_album_photos` SET `order` = `photo_id` ;");
            }

            $db->query("DROP TABLE IF EXISTS `engine4_album_features`;");
            $db->query("DROP TABLE IF EXISTS `engine4_album_params`;");
            $db->query("UPDATE `engine4_core_modules` SET `version` = '4.1.5' WHERE `engine4_core_modules`.`name` = 'album' LIMIT 1 ;");
            return $this->_helper->redirector->gotoRoute(array("module" => "core", "controller" => "packages"), 'admin_default', true);
        }
    }

    public function readmeAction() {
        $this->view->action = 'readme';
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES
    public function categoriesAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_categories');

//        //GET TASK
        $this->view->getCategoriesTempInfo = $getCategoriesTempInfo = false;
        if (isset($_POST['task'])) {
            $task = $_POST['task'];
        } elseif (isset($_GET['task'])) {
            $task = $_GET['task'];
        } else {
            $task = "main";
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license2.php';
        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
        $tableCategoryName = $tableCategory->info('name');

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET ALBUM TABLE
        $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');

        if ($task == "changeorder") {
            $divId = $_GET['divId'];
            $sitealbumOrder = explode(",", $_GET['sitealbumorder']);
            //RESORT CATEGORIES
            if ($divId == "categories") {
                for ($i = 0; $i < count($sitealbumOrder); $i++) {
                    $category_id = substr($sitealbumOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 7) == "subcats") {
                for ($i = 0; $i < count($sitealbumOrder); $i++) {
                    $category_id = substr($sitealbumOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            }
        }

        $categories = array();
        $category_info = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'file_id', 'banner_id', 'sponsored'), 'sponsored' => 0, 'cat_depandancy' => 1));
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $subcategories = $tableCategory->getSubCategories(array('category_id' => $value->category_id, 'fetchColumns' => array('category_id', 'category_name', 'file_id', 'banner_id', 'cat_order', 'sponsored')));
            foreach ($subcategories as $subresults) {
                //GET TOTAL ALBUMS COUNT
                $subcategory_sitealbum_count = $tableSitealbum->getAlbumsCount(array('category_id' => $subresults->category_id, 'columnName' => 'subcategory_id'));

                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'count' => $subcategory_sitealbum_count,
                    'file_id' => $subresults->file_id,
                    'banner_id' => $subresults->banner_id,
                    'order' => $subresults->cat_order,
                    'sponsored' => $subresults->sponsored);
            }

            //GET TOTAL ALBUMS COUNT
            $category_sitealbum_count = $tableSitealbum->getAlbumsCount(array('category_id' => $value->category_id, 'columnName' => 'category_id'));

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $category_sitealbum_count,
                'file_id' => $value->file_id,
                'banner_id' => $value->banner_id,
                'sponsored' => $value->sponsored,
                'sub_categories' => $sub_cat_array);
        }

        if (!empty($getCategoriesTempInfo) && !empty($categories))
            $this->view->categories = $categories;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->category_id = $category_id = $request->getParam('category_id', 0);
        $perform = $request->getParam('perform', 'add');
        $cat_dependency = 0;

        if ($category_id) {
            $category = Engine_Api::_()->getItem('album_category', $category_id);
            $cat_dependency = $category->category_id;
        }

        if (($perform == 'add') && !empty($getCategoriesTempInfo)) {
            $this->view->form = $form = new Sitealbum_Form_Admin_Categories_Add();

            //CHECK POST
            if (empty($getCategoriesTempInfo) || !$this->getRequest()->isPost()) {
                return;
            }

            //CHECK VALIDITY
            if (empty($getCategoriesTempInfo) || !$form->isValid($this->getRequest()->getPost())) {

                if (empty($_POST['category_name'])) {
                    $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
                }
                return;
            }

            //PROCESS
            $values = $form->getValues();
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
                $cat_order = $row_info['cat_order'] + 1;

                //GET CATEGORY TITLE
                $category_name = str_replace("'", "\'", trim($values['category_name']));
                $values['cat_order'] = $cat_order;
                $values['category_name'] = $category_name;
                $values['cat_dependency'] = $cat_dependency;
                $row = $tableCategory->createRow();
                $row->setFromArray($values);

                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $photoFileIcon = $row->setPhoto($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileIcon->file_id)) {
                        $row->file_id = $photoFileIcon->file_id;
                    }
                }

                //UPLOAD CATEGORY PHOTO
                if (isset($_FILES['photo'])) {
                    $photoFile = $row->setPhoto($form->photo, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFile->file_id)) {
                        $row->photo_id = $photoFile->file_id;
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $photoFileBanner = $row->setPhoto($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileBanner->file_id)) {
                        $row->banner_id = $photoFileBanner->file_id;
                    }
                }

                $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

                if (empty($banner_url)) {
                    if ($values['banner_url']) {
                        $row->banner_url = (_ENGINE_SSL ? 'https://' : 'http://') . $values['banner_url'];
                    } else {
                        $row->banner_url = $values['banner_url'];
                    }
                } else {
                    $row->banner_url = $values['banner_url'];
                }

                $category_id = $row->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitealbum', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        } else if (!empty($getCategoriesTempInfo)) {
            $this->view->form = $form = new Sitealbum_Form_Admin_Categories_Edit();
            $category = Engine_Api::_()->getItem('album_category', $category_id);
            $form->populate($category->toArray());

            //CHECK POST
            if (empty($getCategoriesTempInfo) || !$this->getRequest()->isPost()) {
                return;
            }

            //CHECK VALIDITY
            if (empty($getCategoriesTempInfo) || !$form->isValid($this->getRequest()->getPost())) {

                if (empty($_POST['category_name'])) {
                    $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
                }
                return;
            }
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET CATEGORY TITLE
                $category_name = str_replace("'", "\'", trim($values['category_name']));

                $category->category_name = $category_name;
                $category->meta_title = $values['meta_title'];
                $category->meta_description = $values['meta_description'];
                $category->meta_keywords = $values['meta_keywords'];
                $category->sponsored = $values['sponsored'];
                $category->banner_title = $values['banner_title'];
                $category->banner_url_window = $values['banner_url_window'];
                $category->category_slug = $values['category_slug'];
                $category->top_content = $values['top_content'];
                $category->bottom_content = $values['bottom_content'];
                $category->cat_dependency = $category->cat_dependency;

                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $previous_file_id = $category->file_id;
                    $photoFileIcon = $category->setPhoto($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileIcon->file_id)) {

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_file_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
                            $file->delete();
                        }

                        $category->file_id = $photoFileIcon->file_id;
                        $category->save();
                    }
                }

                //UPLOAD CATEGORY PHOTO
                if (isset($_FILES['photo'])) {
                    $previous_photo_id = $category->photo_id;
                    $photoFile = $category->setPhoto($form->photo, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFile->file_id)) {
                        $category->photo_id = $photoFile->file_id;

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_photo_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_photo_id);
                            $file->delete();
                        }
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $previous_banner_id = $category->banner_id;
                    $photoFileBanner = $category->setPhoto($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileBanner->file_id)) {

                        //DELETE PREVIOUS CATEGORY BANNER
                        if ($previous_banner_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
                            $file->delete();
                        }

                        $category->banner_id = $photoFileBanner->file_id;
                        $category->save();
                    }
                }

                $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

                if (empty($banner_url)) {
                    if ($values['banner_url']) {
                        $category->banner_url = (_ENGINE_SSL ? 'https://' : 'http://') . $values['banner_url'];
                    } else {
                        $category->banner_url = $values['banner_url'];
                    }
                } else {
                    $category->banner_url = $values['banner_url'];
                }
                $category->save();

                if (isset($values['removephoto']) && !empty($values['removephoto'])) {
                    //DELETE CATEGORY ICON
                    $file = Engine_Api::_()->getItem('storage_file', $category->photo_id);

                    //UPDATE FILE ID IN CATEGORY TABLE
                    $category->photo_id = 0;
                    $category->save();
                    $file->delete();
                }

                if (isset($values['removeicon']) && !empty($values['removeicon'])) {

                    $previous_icon_id = $category->file_id;

                    if ($previous_icon_id) {
                        //UPDATE FILE ID IN CATEGORY TABLE
                        $category->file_id = 0;
                        $category->save();

                        //DELETE CATEGORY ICON
                        $file = Engine_Api::_()->getItem('storage_file', $previous_icon_id);
                        $file->delete();
                    }
                }

                if (isset($values['removebanner']) && !empty($values['removebanner'])) {

                    $previous_banner_id = $category->banner_id;

                    if ($previous_banner_id) {
                        //UPDATE FILE ID IN CATEGORY TABLE
                        $category->banner_id = 0;
                        $category->save();

                        //DELETE CATEGORY ICON
                        $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
                        $file->delete();
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitealbum', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        }
    }

    //ACTION FOR MAPPING OF ALBUMS
    Public function mappingCategoryAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID AND OBJECT
        $this->view->catid = $catid = $this->_getParam('category_id');
        $category = Engine_Api::_()->getItem('album_category', $catid);

        //CREATE FORM
        $this->view->form = $form = new Sitealbum_Form_Admin_Settings_Mapping();

        $this->view->close_smoothbox = 0;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if ($this->getRequest()->isPost()) {

            //GET FORM VALUES
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET ALBUM TABLE
                $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');

                //GET CATEGORY TABLE
                $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitealbum');

                //ON CATEGORY DELETE
                $rows = $tableCategory->getSubCategories(array('category_id' => $catid, 'fetchColumns' => array('category_id')));
                foreach ($rows as $row) {
                    $row->delete();
                }

                $previous_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $catid));
                $new_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $values['new_category_id']));

                /// ALBUMS WHICH HAVE THIS CATEGORY
                if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
                    $albumsIds = $tableSitealbum->getCategoryList(array('category_id' => $catid, 'category_type' => 'category_id'));

                    foreach ($albumsIds as $album_id) {

                        //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                        Engine_Api::_()->fields()->getTable('album', 'values')->delete(array('item_id = ?' => $album_id));
                        Engine_Api::_()->fields()->getTable('album', 'search')->delete(array('item_id = ?' => $$album_id));

                        //UPDATE THE PROFILE TYPE OF ALREADY CREATED ALBUMS
                        $tableSitealbum->update(array('profile_type' => $new_cat_profile_type), array('album_id = ?' => $album_id));
                    }
                }

                //ALBUM TABLE CATEGORY DELETE WORK
                if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
                    $tableSitealbum->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
                } else {

                    $selectAlbums = $tableSitealbum->select()
                            ->from($tableSitealbum->info('name'))
                            ->where('category_id = ?', $catid);

                    foreach ($tableSitealbum->fetchAll($selectAlbums) as $album) {
                        $album->delete();
                    }
                }

                $category->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        $this->view->close_smoothbox = 1;
    }

    public function deleteCategoryAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $category_id = $this->_getParam('category_id');

        $this->view->category_id = $category_id;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');

        //GET ALBUM TABLE
        $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');

        if ($this->getRequest()->isPost()) {

            //IF SUB-CATEGORY IS MAPPED
            $previous_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $category_id));

            if ($previous_cat_profile_type) {

                //SELECT ALBUMS WHICH HAVE THIS CATEGORY
                $albumsIds = $tableSitealbum->getCategoryList(array('category_id' => $category_id, 'category_type' => 'category_id'));

                foreach ($albumsIds as $album_id) {
                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('album', 'values')->delete(array('item_id = ?' => $album_id));
                    Engine_Api::_()->fields()->getTable('album', 'search')->delete(array('item_id = ?' => $album_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED ALBUMS
                    $tableSitealbum->update(array('profile_type' => 0), array('album_id = ?' => $album_id));
                }
            }

            //SITEALBUMT TABLE SUB-CATEGORY DELETE WORK
            $tableSitealbum->update(array('subcategory_id' => 0), array('subcategory_id = ?' => $category_id));

            $tableCategory->delete(array('category_id = ?' => $category_id));


            //GET URL
            $url = $this->_helper->url->url(array('action' => 'categories', 'controller' => 'settings', 'perform' => 'add', 'category_id' => 0));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }

        $this->renderScript('admin-settings/delete-category.tpl');
    }

    public function integrationsAction() {

        $pluginName = 'sitealbum';
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_integrations');
    }

    //SINK LOCATIN FROM CHECKIN PLUGIN IN TO ALBUM PLUGIN.
    public function sinkcheckinlocationAction() {

        //PROCESS
        set_time_limit(0);
        ini_set("max_execution_time", "300");
        ini_set("memory_limit", "256M");
        $this->view->error = 0;
        $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

        $select = $addlocationsTable->select()
                        ->from($addlocationsTable->info('name'), array('resource_type', 'resource_id', 'params', 'addlocation_id'))->where('object_type LIKE ?', 'album%')->where('resource_type LIKE ?', 'album%')->where('sync_album = ?', 0);

        $results = $addlocationsTable->fetchAll($select);


        if ($this->getRequest()->isPost()) {
            foreach ($results as $result) {
                $addlocation_id = $result->addlocation_id;
                $resource_type = $result->resource_type;
                $resource_id = $result->resource_id;

                if (isset($result->params['checkin']['label'])) {
                    if (!empty($result->params['checkin']['label'])) {
                        $location = $params = $result->params['checkin']['label'];
                        if (!empty($location)) {
                            $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
                            if (!empty($resource)) {
                                $getLocationItemIds = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemIds(array('resource_id' => $resource_id, 'resource_type' => $resource_type));
                                if (empty($getLocationItemIds)) {
                                    $seaoLocationId = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($location, '', $resource_type, $resource_id);
                                    $resource->seao_locationid = $seaoLocationId;
                                    $resource->location = $location;
                                    $resource->save();

                                    $addlocationItem = Engine_Api::_()->getItem('sitetagcheckin_addlocation', $addlocation_id);
                                    $addlocationItem->sync_album = 1;
                                    $addlocationItem->save();
                                }
                            } else {
                                $addlocationItem = Engine_Api::_()->getItem('sitetagcheckin_addlocation', $addlocation_id);
                                $addlocationItem->sync_album = 1;
                                $addlocationItem->save();
                            }
                        }
                    }
                }
                $this->view->error = 1;
            }
        }
    }

    //ACTINO FOR SEARCH FORM TAB
    public function formSearchAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_formsearch');

        //GET SEARCH TABLE
        $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        //CHECK POST
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $values = $_POST;
            $rowCategory = $tableSearchForm->getFieldsOptions('sitealbum', 'category_id');
            $rowLocation = $tableSearchForm->getFieldsOptions('sitealbum', 'location');
            $defaultCategory = 0;
            $defaultAddition = 0;
            $count = 1;
            try {
                foreach ($values['order'] as $key => $value) {
                    $multiplyAddition = $count * 5;
                    $tableSearchForm->update(array('order' => $defaultAddition + $defaultCategory + $key + $multiplyAddition + 1), array('searchformsetting_id = ?' => (int) $value));

                    if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id) {
                        $defaultCategory = 1;
                        $defaultAddition = 10000000;
                    }
                    $count++;
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license2.php';
    }

    //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
    public function diplayFormAction() {

        $field_id = $this->_getParam('id');
        $name = $this->_getParam('name');
        $display = $this->_getParam('display');

        if (!empty($field_id)) {

            if ($name == 'location' && $display == 0) {
                Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitealbum', 'name = ?' => 'proximity'));
            }

            Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitealbum', 'searchformsetting_id = ?' => (int) $field_id));
        }
        $this->_redirect('admin/sitealbum/settings/form-search');
    }

    public function sinkLocationAction() {

        set_time_limit(0);
        ini_set("max_execution_time", "300");
        ini_set("memory_limit", "256M");
        $this->view->error = 0;
        $tableSitetagcheckin = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
        $checkInTableSelect = $tableSitetagcheckin->select()->from($tableSitetagcheckin->info('name'), 'resource_id')->where('resource_type =?', 'album');
        $checkInTableResults = $tableSitetagcheckin->fetchAll($checkInTableSelect);
        $checkInAlbumLocationExist = '';
        foreach ($checkInTableResults as $checkInTableResult) {
            $checkInAlbumLocationExist .= $checkInTableResult['resource_id'] . ',';
        }
        $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');
        $select = $tableSitealbum->select()->from($tableSitealbum->info('name'), array('album_id', 'location'))->where('location <>?', '');
        if (!empty($checkInAlbumLocationExist))
            $select->where($tableSitealbum->info('name') . '.album_id not in (?)', new Zend_Db_Expr(trim($checkInAlbumLocationExist, ',')));
        $this->view->results = $results = $tableSitealbum->fetchAll($select);

        $checkInTablePhotoSelect = $tableSitetagcheckin->select()->from($tableSitetagcheckin->info('name'), 'resource_id')->where('resource_type =?', 'album_photo');
        $checkInTablePhotoResults = $tableSitetagcheckin->fetchAll($checkInTablePhotoSelect);
        $checkInTablePhotoLocationExist = '';
        foreach ($checkInTablePhotoResults as $checkInTablePhotoResult) {
            $checkInTablePhotoLocationExist .= $checkInTablePhotoResult['resource_id'] . ',';
        }
        $tablePhotoSitealbum = Engine_Api::_()->getDbtable('photos', 'sitealbum');
        $select = $tablePhotoSitealbum->select()->from($tablePhotoSitealbum->info('name'), array('photo_id', 'location'))->where('location <>?', '');
        if (!empty($checkInTablePhotoLocationExist))
            $select->where($tablePhotoSitealbum->info('name') . '.photo_id not in (?)', new Zend_Db_Expr(trim($checkInTablePhotoLocationExist, ',')));
        $this->view->resultss = $resultss = $tablePhotoSitealbum->fetchAll($select);
        if ($this->getRequest()->isPost()) {
            foreach ($results as $albumresult) {
                $album = Engine_Api::_()->getItem('album', $albumresult->album_id);
                $urladdress = urlencode($album->location);
                $this->setInCheckInTable($album, $urladdress, 0);
            }

            foreach ($resultss as $photoresults) {
                $photo = Engine_Api::_()->getItem('album_photo', $photoresults->photo_id);
                $urladdress = urlencode($photo->location);
                $this->setInCheckInTable($photo, $urladdress, 1);
            }
            $this->view->error = 1;
        }
    }

    public function setInCheckInTable($object, $urladdress, $noCheckin) {
        $params = array();
        $geocode_pending = true;
        while ($geocode_pending) {
            $key = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
            if (!empty($key)) {
                $request_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$urladdress&key=$key";
            } else {
                $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress";
            }

            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $doGetContents = ob_get_contents();
            $json_resopnse = !empty($doGetContents) ? Zend_Json::decode($doGetContents) : array();
            ob_end_clean();
            $status = $json_resopnse['status'];
            if (strcmp($status, "OK") == 0) {
                $geocode_pending = false;
                $result = $json_resopnse['results'];
                //FORMAT: LONGIDUDE, LATITUDE, ALTITUDE
                $lat = $result[0]['geometry']['location']['lat'];
                $lng = $result[0]['geometry']['location']['lng'];
                $params['google_id'] = $result[0]['id'];
                $params['latitude'] = $lat;
                $params['longitude'] = $lng;
                $params['icon'] = $result[0]['icon'];
                $params['reference'] = $result[0]['reference'];
                $params['type'] = 'place';
                $params['vicinity'] = isset($result[0]['vicinity']) ? $result[0]['vicinity'] : $result[0]['formatted_address'];
                $params['types'] = implode(',', $result[0]['types']);
                $params['prefixadd'] = 'at';
                $params['name'] = $result[0]['name'];
                $params['label'] = $params['name'] . ', ' . $params['vicinity'];
                Engine_Api::_()->sitealbum()->saveInCheckinTable($params, $object->location, $object, $noCheckin);
            } else if (strcmp($status, "620") == 0) {
                //sent geocodes too fast
                $delay += 100000;
            } else {
                //FAILURE TO GEOCODE
                $geocode_pending = false;
                $overQueryLimit = 1;
                //echo "Address " . $location . " failed to geocoded. ";
                //echo "Received status " . $status . "\n";
            }
            usleep($delay);
        }
    }

    public function setTemplateAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitealbum_admin_main', array(), 'sitealbum_admin_main_template');

        $this->view->form = $form = new Sitealbum_Form_Admin_Template();

        $previousProfileTemplate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.profiletemplate', 'default');

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitecontentcoverphoto')
                ->where('enabled = ?', 1);
        $this->view->is_sitecontentcoverphoto_object = $is_sitecontentcoverphoto_object = $select->query()->fetchObject();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
            $header_page_id = Engine_Api::_()->sitealbum()->getWidgetizedPageId(array('name' => 'header'));
            $main_content_id = $tableNameContent->select()
                    ->from($tableNameContent->info('name'), 'content_id')
                    ->where('name =?', 'main')
                    ->where('page_id =?', $header_page_id)
                    ->query()
                    ->fetchColumn();

            if (!empty($main_content_id)) {
                $content_id = $tableNameContent->select()
                        ->from($tableNameContent->info('name'), 'content_id')
                        ->where('name =?', 'core.html-block')
                        ->where('page_id =?', $header_page_id)
                        ->where('params like (?)', '%jQuery.noConflict()%')->query()
                        ->fetchColumn();

                if (!$content_id) {
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'core.html-block',
                        'page_id' => $header_page_id,
                        'parent_content_id' => $main_content_id,
                        'order' => 1,
                        'params' => '{"title":"","data":"<script type=\"text\/javascript\"> \r\nif(typeof(window.jQuery) !=  \"undefined\") {\r\njQuery.noConflict();\r\n}\r\n<\/script>","nomobile":"0","name":"core.html-block"}'
                    ));
                }
            }

            $templateApi = Engine_Api::_()->getApi('settemplate', 'sitealbum');
            if (isset($values['sitealbum_profiletemplate']) && !empty($values['sitealbum_profiletemplate']) && !empty($previousProfileTemplate) && $values['sitealbum_profiletemplate'] != $previousProfileTemplate) {

                // WORK FOR ADVANCED ALBUM PLUGIN
                if (!empty($is_sitecontentcoverphoto_object) && $values['sitealbum_profiletemplate'] == 'template2') {
                    $templateApi->setAlbumsViewPageWithCoverPhoto(true);
                } else {
                    $templateApi->setAlbumsViewPageWithoutCoverPhoto(true);
                }
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumHome', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsHomePage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumBrowse', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsBrowsePage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumPhotoBrowse', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsPhotoBrowsePage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumLocations', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsLocationsPage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumPinboardView', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsPinboardViewPage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumManage', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsManagePage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('albumCategoriesHome', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setAlbumsCategoriesHomePage(true);
            }

            if (!empty($values['sitealbum_otherpagestemplate']) && in_array('memberProfileAlbumWidgetParameter', $values['sitealbum_otherpagestemplate'])) {
                $templateApi->setMemberProfileAlbumWidgetParameter(true);
            }

            foreach ($values as $key => $value) {
                if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key)) {
                    Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
                }
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }

            $form->addNotice('Your changes have been saved.');
        }
    }

    public function orderUpdateAction() {
        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $results = $select
                ->from('engine4_album_photos', array('photo_id', 'order'))
                ->query()
                ->fetchAll();
        $db = Engine_Db_Table::getDefaultAdapter();
        $order = 0;
        foreach ($results as $key => $value) {
            $photo_id = $value['photo_id'];
            $order = $order + 1;
            $db->query("UPDATE `engine4_album_photos` SET `order` = $order WHERE `engine4_album_photos`.`photo_id` = $photo_id;");
        }
    }

}
