<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitevideo_Form_Admin_Global') {
            
        }
        return true;
    }

    public function indexAction() {
        $this->view->route = $this->_getParam('route', '');
        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license1.php';
    }

    public function videoSettingsAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_settings');
        $this->view->navigationGeneral = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_settings', array(), 'sitevideo_admin_main_video_settings');
        $this->view->form = $form = new Sitevideo_Form_Admin_VideoSettings();


        // If not post or form not valid, return
        if (!$this->getRequest()->isPost()) {
            $formValues = $form->getValues();
            $form->getElement('sitevideo_allowed_video')->setDescription('Select type of video source that you want to be available for members while uploading new video. [ Note: You can apply this setting on per member level basis from ‘Member Level Settings’. ]');
            $form->getElement('sitevideo_allowed_video')->getDecorator('Description')->setOptions(array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
            $is_ffmpeg_installed = false;
            if (function_exists('exec')) {
                $ffmpeg_path = $formValues['sitevideo_ffmpeg_path'];
                $output = null;
                $return = null;
                $is_ffmpeg_installed = true;
                exec($ffmpeg_path . ' -version', $output, $return);
                if (empty($output) || ($output != NULL && is_array($output) && count($output) == 0)) {
                    $is_ffmpeg_installed = false;
                }
            }
            if ($is_ffmpeg_installed == false) {
                $form->getElement('sitevideo_allowed_video')->setDescription('Select type of video source that you want to be available for members while uploading new video. [ Note: You can apply this setting on per member level basis from ‘Member Level Settings’. ]<br /><span style="color:red;" >Please install FFMPEG on your server and configure its path to show "My Computer" option on "Post New Video" page to upload my computer\'s video on your site.</span>');
            }

            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $currentYouTubeApiKey = $coreSettings->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
        if (!empty($values['sitevideo_youtube_apikey']) && $values['sitevideo_youtube_apikey'] != $currentYouTubeApiKey) {
            $response = $this->verifyYotubeApiKey($values['sitevideo_youtube_apikey']);
            if (!empty($response['errors'])) {
                $error_message = array('Invalid API Key');
                foreach ($response['errors'] as $error) {
                    $error_message[] = "Error Reason (" . $error['reason'] . '): ' . $error['message'];
                }
                return $form->sitevideo_youtube_apikey->addErrors($error_message);
            }
        }
        $form->getElement('sitevideo_allowed_video')->setDescription('Select type of video source that you want to be available for members while uploading new video. [ Note: You can apply this setting on per member level basis from ‘Member Level Settings’. ]');
        $form->getElement('sitevideo_allowed_video')->getDecorator('Description')->setOptions(array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        // Check ffmpeg path
        if (!empty($values['sitevideo_ffmpeg_path'])) {
            if (function_exists('exec')) {
                $ffmpeg_path = $values['sitevideo_ffmpeg_path'];
                $output = null;
                $return = null;
                exec($ffmpeg_path . ' -version', $output, $return);
                if (empty($output) || ($output != NULL && is_array($output) && count($output) == 0)) {
                    $form->sitevideo_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
                    $values['sitevideo_ffmpeg_path'] = '';
                    $form->getElement('sitevideo_allowed_video')->setDescription('Select type of video source that you want to be available for members while uploading new video. [ Note: You can apply this setting on per member level basis from ‘Member Level Settings’. ]<br /><span style="color:red;" >Please install FFMPEG on your server and configure its path to show "My Computer" option on "Post New Video" page to upload my computer\'s video on your site.</span>');
                }
            } else {
                $form->sitevideo_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
                $values['sitevideo_ffmpeg_path'] = '';
                $form->getElement('sitevideo_allowed_video')->setDescription('Select type of video source that you want to be available for members while uploading new video. [ Note: You can apply this setting on per member level basis from ‘Member Level Settings’. ]<br /><span style="color:red;">Please install FFMPEG on your server and configure its path to show "My Computer" option on "Post New Video" page to upload my computer\'s video on your site.</span>');
            }
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.allowed.video')) {
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitevideo.allowed.video');
        }
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
    }

    public function channelSettingsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_settings');
        $this->view->navigationGeneral = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_settings', array(), 'sitevideo_admin_main_channel_settings');
        $this->view->form = $form = new Sitevideo_Form_Admin_ChannelSettings();
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.add.videos.options')) {
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitevideo.add.videos.options');
        }
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
    }

    public function faqAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_faq');
        $this->view->action = 'faq';
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    public function readmeAction() {
        $this->view->action = 'readme';
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES
    public function categoriesAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_categories');
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_categories', array(), 'sitevideo_admin_main_categories_channel');
//        //GET TASK
        $this->view->getCategoriesTempInfo = $getCategoriesTempInfo = true;
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

        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $tableCategoryName = $tableCategory->info('name');

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET CHANNEL TABLE
        $tableSitevideo = Engine_Api::_()->getDbtable('channels', 'sitevideo');

        if ($task == "changeorder") {
            $divId = $_GET['divId'];
            $sitevideoOrder = explode(",", $_GET['sitevideoorder']);
            //RESORT CATEGORIES
            if ($divId == "categories") {
                for ($i = 0; $i < count($sitevideoOrder); $i++) {
                    $category_id = substr($sitevideoOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 7) == "subcats") {
                for ($i = 0; $i < count($sitevideoOrder); $i++) {
                    $category_id = substr($sitevideoOrder[$i], 4);
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
                $subsubcategories = $tableCategory->getSubCategories(array('category_id' => $subresults->category_id, 'fetchColumns' => array('category_id', 'category_name', 'file_id', 'banner_id', 'cat_order', 'sponsored')));
                $treesubarrays[$subresults->category_id] = array();

                foreach ($subsubcategories as $subsubcategoriesvalues) {

                    //GET TOTAL CHANNELS COUNT
                    $subsubcategory_sitevideo_count = $tableSitevideo->getChannelsCount(array('category_id' => $subsubcategoriesvalues->category_id, 'columnName' => 'subsubcategory_id'));

                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
                        'count' => $subsubcategory_sitevideo_count,
                        'file_id' => $subsubcategoriesvalues->file_id,
                        'banner_id' => $subsubcategoriesvalues->banner_id,
                        'order' => $subsubcategoriesvalues->cat_order,
                        'sponsored' => $subsubcategoriesvalues->sponsored);
                }

                //GET TOTAL CHANNELS COUNT
                $subcategory_sitevideo_count = $tableSitevideo->getChannelsCount(array('category_id' => $subresults->category_id, 'columnName' => 'subcategory_id'));
                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $subcategory_sitevideo_count,
                    'file_id' => $subresults->file_id,
                    'banner_id' => $subresults->banner_id,
                    'order' => $subresults->cat_order,
                    'sponsored' => $subresults->sponsored);
            }

            //GET TOTAL CHANNELS COUNT
            $category_sitevideo_count = $tableSitevideo->getChannelsCount(array('category_id' => $value->category_id, 'columnName' => 'category_id'));

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $category_sitevideo_count,
                'file_id' => $value->file_id,
                'banner_id' => $value->banner_id,
                'sponsored' => $value->sponsored,
                'sub_categories' => $sub_cat_array);
        }

        $this->view->categories = $categories;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->category_id = $category_id = $request->getParam('category_id', 0);

        $perform = $request->getParam('perform', 'add');
        $cat_dependency = 0;
        $subcat_dependency = 0;
        if ($category_id) {
            $category = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);
            if ($category && empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
            } elseif ($category && !empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
                $subcat_dependency = $category->category_id;
            }
        }

        if (($perform == 'add') && !empty($getCategoriesTempInfo)) {
            $this->view->form = $form = new Sitevideo_Form_Admin_Categories_Add();

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
                $values['subcat_dependency'] = $subcat_dependency;
                $row = $tableCategory->createRow();
                $row->setFromArray($values);

                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $videoFileIcon = $row->setVideo($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileIcon->file_id)) {
                        $row->file_id = $videoFileIcon->file_id;
                    }
                }

                //UPLOAD CATEGORY VIDEO
                if (isset($_FILES['video'])) {
                    $videoFile = $row->setVideo($form->video, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFile->file_id)) {
                        $row->video_id = $videoFile->file_id;
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $videoFileBanner = $row->setVideo($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileBanner->file_id)) {
                        $row->banner_id = $videoFileBanner->file_id;
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

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->sitevideo()->categoriesPageCreate(array(0 => $category_id));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitevideo', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        } else if (!empty($getCategoriesTempInfo)) {
            $this->view->form = $form = new Sitevideo_Form_Admin_Categories_Edit();
            $category = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);
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
                $cat_dependency = $category->cat_dependency;
                $subcat_dependency = $category->subcat_dependency;
                if ($category_id && empty($subcat_dependency) && !empty($cat_dependency)) {
                    $cat_dependency = $cat_dependency;
                    $subcat_dependency = 0;
                } elseif ($category_id && !empty($subcat_dependency) && !empty($cat_dependency)) {
                    $cat_dependency = $cat_dependency;
                    $subcat_dependency = $subcat_dependency;
                }

                $category->cat_dependency = $cat_dependency;
                $category->subcat_dependency = $subcat_dependency;
                $category->banner_description = $values['banner_description'];
                $category->featured_tagline = $values['featured_tagline'];
                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $previous_file_id = $category->file_id;
                    $videoFileIcon = $category->setVideo($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileIcon->file_id)) {

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_file_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
                            $file->delete();
                        }

                        $category->file_id = $videoFileIcon->file_id;
                        $category->save();
                    }
                }

                //UPLOAD CATEGORY VIDEO
                if (isset($_FILES['video'])) {
                    $previous_video_id = $category->video_id;
                    $videoFile = $category->setVideo($form->video, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFile->file_id)) {
                        $category->video_id = $videoFile->file_id;

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_video_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_video_id);
                            $file->delete();
                        }
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $previous_banner_id = $category->banner_id;
                    $videoFileBanner = $category->setVideo($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileBanner->file_id)) {

                        //DELETE PREVIOUS CATEGORY BANNER
                        if ($previous_banner_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
                            $file->delete();
                        }

                        $category->banner_id = $videoFileBanner->file_id;
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

                if (isset($values['removevideo']) && !empty($values['removevideo'])) {
                    //DELETE CATEGORY ICON
                    $file = Engine_Api::_()->getItem('storage_file', $category->video_id);

                    //UPDATE FILE ID IN CATEGORY TABLE
                    $category->video_id = 0;
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

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->sitevideo()->categoriesPageCreate(array(0 => $category_id));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitevideo', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        }
    }

    //ACTION FOR MAPPING OF CHANNELS
    Public function mappingCategoryAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID AND OBJECT
        $this->view->catid = $catid = $this->_getParam('category_id');
        $category = Engine_Api::_()->getItem('sitevideo_channel_category', $catid);

        //CREATE FORM
        $this->view->form = $form = new Sitevideo_Form_Admin_Settings_Mapping();

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

                //GET CHANNEL TABLE
                $tableSitevideo = Engine_Api::_()->getDbtable('channels', 'sitevideo');

                //GET CATEGORY TABLE
                $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');

                //ON CATEGORY DELETE
                $rows = $tableCategory->getSubCategories(array('category_id' => $catid, 'fetchColumns' => array('category_id')));
                foreach ($rows as $row) {
                    $row->delete();
                }

                $previous_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $catid));
                $new_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $values['new_category_id']));

                /// CHANNELS WHICH HAVE THIS CATEGORY
                if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
                    $channelsIds = $tableSitevideo->getCategoryList(array('category_id' => $catid, 'category_type' => 'category_id'));

                    foreach ($channelsIds as $channel_id) {

                        //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                        Engine_Api::_()->fields()->getTable('sitevideo', 'values')->delete(array('item_id = ?' => $channel_id));
                        Engine_Api::_()->fields()->getTable('sitevideo', 'search')->delete(array('item_id = ?' => $channel_id));

                        //UPDATE THE PROFILE TYPE OF ALREADY CREATED CHANNELS
                        $tableSitevideo->update(array('profile_type' => $new_cat_profile_type), array('channel_id = ?' => $channel_id));
                    }
                }

                //CHANNEL TABLE CATEGORY DELETE WORK
                if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
                    $tableSitevideo->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
                } else {

                    $selectChannels = $tableSitevideo->select()
                            ->from($tableSitevideo->info('name'))
                            ->where('category_id = ?', $catid);

                    foreach ($tableSitevideo->fetchAll($selectChannels) as $channel) {
                        Engine_Api::_()->getApi('core', 'sitevideo')->deleteChannel($channel);
                    }
                }

                $page_id = $db->select()
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', "sitevideo_index_categories-home_category_" . $catid)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();


                $content_ids = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $page_id)
                        ->query()
                        ->fetchAll();
                foreach ($content_ids as $content_id) {
                    $content = $content_id['content_id'];
                    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = $content");
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
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');

        //GET CHANNEL TABLE
        $tableSitevideo = Engine_Api::_()->getDbtable('channels', 'sitevideo');

        if ($this->getRequest()->isPost()) {

            //IF SUB-CATEGORY IS MAPPED
            $previous_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $category_id));

            if ($previous_cat_profile_type) {

                //SELECT CHANNELS WHICH HAVE THIS CATEGORY
                $channelsIds = $tableSitevideo->getCategoryList(array('category_id' => $category_id, 'category_type' => 'category_id'));

                foreach ($channelsIds as $channel_id) {
                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values')->delete(array('item_id = ?' => $channel_id));
                    Engine_Api::_()->fields()->getTable('sitevideo_channel', 'search')->delete(array('item_id = ?' => $channel_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED CHANNELS
                    $tableSitevideo->update(array('profile_type' => 0), array('channel_id = ?' => $channel_id));
                }
            }

            //SITEVIDEOT TABLE SUB-CATEGORY DELETE WORK
            $tableSitevideo->update(array('subcategory_id' => 0), array('subcategory_id = ?' => $category_id));

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

        $pluginName = 'sitevideo';
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_integrations');
    }

    //SINK LOCATIN FROM CHECKIN PLUGIN IN TO CHANNEL PLUGIN.
    public function sinkcheckinlocationAction() {

        //PROCESS
        set_time_limit(0);
        ini_set("max_execution_time", "300");
        ini_set("memory_limit", "256M");
        $this->view->error = 0;
        $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

        $select = $addlocationsTable->select()
                        ->from($addlocationsTable->info('name'), array('resource_type', 'resource_id', 'params', 'addlocation_id'))->where('object_type LIKE ?', 'channel%')->where('resource_type LIKE ?', 'channel%')->where('sync_channel = ?', 0);

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
                                    $addlocationItem->sync_channel = 1;
                                    $addlocationItem->save();
                                }
                            } else {
                                $addlocationItem = Engine_Api::_()->getItem('sitetagcheckin_addlocation', $addlocation_id);
                                $addlocationItem->sync_channel = 1;
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
                ->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_formsearch');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main_formsearch', array(), 'sitevideo_admin_main_formsearch_channel');

        //GET SEARCH TABLE
        $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        //CHECK POST
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $values = $_POST;
            $rowCategory = $tableSearchForm->getFieldsOptions('sitevideo', 'category_id');
            $rowLocation = $tableSearchForm->getFieldsOptions('sitevideo', 'location');
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

        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
    }

    //ACTINO FOR VIDEO SEARCH FORM TAB
    public function videoFormSearchAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_formsearch');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main_formsearch', array(), 'sitevideo_admin_main_formsearch_video');

        //GET SEARCH TABLE
        $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        //CHECK POST
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $values = $_POST;
            $rowCategory = $tableSearchForm->getFieldsOptions('sitevideo', 'category_id');
            $rowLocation = $tableSearchForm->getFieldsOptions('sitevideo', 'location');
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

        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
    }

    //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
    public function displayFormAction() {

        $field_id = $this->_getParam('id');
        $name = $this->_getParam('name');
        $display = $this->_getParam('display');

        if (!empty($field_id)) {

            if ($name == 'location' && $display == 0) {
                Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitevideo_channel', 'name = ?' => 'proximity'));
            }

            Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitevideo_channel', 'searchformsetting_id = ?' => (int) $field_id));
        }
        $this->_redirect('admin/sitevideo/settings/form-search');
    }

    //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
    public function displayVideoFormAction() {

        $field_id = $this->_getParam('id');
        $name = $this->_getParam('name');
        $display = $this->_getParam('display');

        if (!empty($field_id)) {

            if ($name == 'location' && $display == 0) {
                Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitevideo_video', 'name = ?' => 'proximity'));
            }

            Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitevideo_video', 'searchformsetting_id = ?' => (int) $field_id));
        }
        $this->_redirect('admin/sitevideo/settings/video-form-search');
    }

    public function setTemplateAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_template');

        $this->view->form = $form = new Sitevideo_Form_Admin_Template();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
            $header_page_id = Engine_Api::_()->sitevideo()->getWidgetizedPageId(array('name' => 'header'));
            $main_content_id = $tableNameContent->select()
                    ->from($tableNameContent->info('name'), 'content_id')
                    ->where('name =?', 'main')
                    ->where('page_id =?', $header_page_id)
                    ->query()
                    ->fetchColumn();
            $db = Engine_Db_Table::getDefaultAdapter();
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

            $templateApi = Engine_Api::_()->getApi('settemplate', 'sitevideo');
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            if (Engine_Api::_()->seaocore()->getCurrentActivateTheme()) {
                $coreSettings->setSetting('sitevideoshow.navigation.tabs', 7);
            } else {
                $coreSettings->setSetting('sitevideoshow.navigation.tabs', 6);
            }
            foreach ($values as $key => $value) {
                $coreSettings->setSetting($key, $value);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('playlistBrowsePage', $values['sitevideo_pagestemplate'])) {
                $templateApi->playlistBrowsePage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('postNewVideo', $values['sitevideo_pagestemplate'])) {
                $templateApi->postNewVideo(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('editVideo', $values['sitevideo_pagestemplate'])) {
                $templateApi->editVideo(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('videoView', $values['sitevideo_pagestemplate'])) {
                $templateApi->videoView(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('videoManage', $values['sitevideo_pagestemplate'])) {
                $templateApi->videoManage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('browseVideo', $values['sitevideo_pagestemplate'])) {
                $templateApi->browseVideo(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('setVideoLocations', $values['sitevideo_pagestemplate'])) {
                $templateApi->setVideoLocations(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('videoCategories', $values['sitevideo_pagestemplate'])) {
                $templateApi->videoCategories(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('pinboardBrowseVideo', $values['sitevideo_pagestemplate'])) {
                $templateApi->pinboardBrowseVideo(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('setVideoCategories', $values['sitevideo_pagestemplate'])) {
                $templateApi->setVideoCategories(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('tagCloudVideo', $values['sitevideo_pagestemplate'])) {
                $templateApi->tagCloudVideo(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('playlistCreatePage', $values['sitevideo_pagestemplate'])) {
                $templateApi->playlistCreatePage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('playlistViewPage', $values['sitevideo_pagestemplate'])) {
                $templateApi->playlistViewPage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('watchLaterManage', $values['sitevideo_pagestemplate'])) {
                $templateApi->watchLaterManage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('channelCreate', $values['sitevideo_pagestemplate'])) {
                $templateApi->channelCreate(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('channelEdit', $values['sitevideo_pagestemplate'])) {
                $templateApi->channelEdit(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('channelView', $values['sitevideo_pagestemplate'])) {
                $templateApi->channelView(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('channelManage', $values['sitevideo_pagestemplate'])) {
                $templateApi->channelManage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('browseChannel', $values['sitevideo_pagestemplate'])) {
                $templateApi->browseChannel(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('channelHome', $values['sitevideo_pagestemplate'])) {
                $templateApi->channelHome(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('channelCategories', $values['sitevideo_pagestemplate'])) {
                $templateApi->channelCategories(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('pinboardBrowseChannel', $values['sitevideo_pagestemplate'])) {
                $templateApi->pinboardBrowseChannel(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('setChannelCategories', $values['sitevideo_pagestemplate'])) {
                $templateApi->setChannelCategories(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('tagCloudChannel', $values['sitevideo_pagestemplate'])) {
                $templateApi->tagCloudChannel(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('subscriptionManage', $values['sitevideo_pagestemplate'])) {
                $templateApi->subscriptionManage(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('topicView', $values['sitevideo_pagestemplate'])) {
                $templateApi->topicView(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('videoHome', $values['sitevideo_pagestemplate'])) {
                $templateApi->videoHome(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('setChannelEditVideos', $values['sitevideo_pagestemplate'])) {
                $templateApi->setChannelEditVideos(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('setBadgeCreate', $values['sitevideo_pagestemplate'])) {
                $templateApi->setBadgeCreate(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('setManagePlaylist', $values['sitevideo_pagestemplate'])) {
                $templateApi->setManagePlaylist(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('memberProfileChannelParameter', $values['sitevideo_pagestemplate'])) {
                $templateApi->memberProfileChannelParameter(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('setVideoCategories', $values['sitevideo_pagestemplate'])) {
                $templateApi->setVideoCategories(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('setChannelCategories', $values['sitevideo_pagestemplate'])) {
                $templateApi->setChannelCategories(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('memberProfileChannelParameter', $values['sitevideo_pagestemplate'])) {
                $templateApi->memberProfileChannelParameter(true);
            }

            if (!empty($values['sitevideo_pagestemplate']) && in_array('memberProfileVideoParameter', $values['sitevideo_pagestemplate'])) {
                $templateApi->memberProfileVideoParameter(true);
            }
            if (!empty($values['sitevideo_pagestemplate']) && in_array('playlistPlayallPage', $values['sitevideo_pagestemplate'])) {
                $templateApi->playlistPlayallPage(true);
            }
            $form->addNotice('Your changes have been saved.');
        }
    }

    public function orderUpdateAction() {
        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $results = $select
                ->from('engine4_video_videos', array('video_id', 'order'))
                ->query()
                ->fetchAll();
        $db = Engine_Db_Table::getDefaultAdapter();
        $order = 0;
        foreach ($results as $key => $value) {
            $video_id = $value['video_id'];
            $order = $order + 1;
            $db->query("UPDATE `engine4_video_videos` SET `order` = $order WHERE `engine4_video_videos`.`video_id` = $video_id;");
        }
    }

    //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES
    public function videoCategoriesAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_categories');
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_categories', array(), 'sitevideo_admin_main_categories_video');
//        //GET TASK
        $this->view->getCategoriesTempInfo = $getCategoriesTempInfo = true;
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

        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');
        $tableCategoryName = $tableCategory->info('name');

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET CHANNEL TABLE
        $tableSitevideo = Engine_Api::_()->getDbtable('videos', 'sitevideo');

        if ($task == "changeorder") {
            $divId = $_GET['divId'];
            $sitevideoOrder = explode(",", $_GET['sitevideoorder']);
            //RESORT CATEGORIES
            if ($divId == "categories") {
                for ($i = 0; $i < count($sitevideoOrder); $i++) {
                    $category_id = substr($sitevideoOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 7) == "subcats") {
                for ($i = 0; $i < count($sitevideoOrder); $i++) {
                    $category_id = substr($sitevideoOrder[$i], 4);
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
                $subsubcategories = $tableCategory->getSubCategories(array('category_id' => $subresults->category_id, 'fetchColumns' => array('category_id', 'category_name', 'file_id', 'banner_id', 'cat_order', 'sponsored')));
                $treesubarrays[$subresults->category_id] = array();

                foreach ($subsubcategories as $subsubcategoriesvalues) {

                    //GET TOTAL VIDEOS COUNT
                    $subsubcategory_sitevideo_count = $tableSitevideo->getVideosCount(array('category_id' => $subsubcategoriesvalues->category_id, 'columnName' => 'subsubcategory_id'));

                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
                        'count' => $subsubcategory_sitevideo_count,
                        'file_id' => $subsubcategoriesvalues->file_id,
                        'banner_id' => $subsubcategoriesvalues->banner_id,
                        'order' => $subsubcategoriesvalues->cat_order,
                        'sponsored' => $subsubcategoriesvalues->sponsored);
                }

                //GET TOTAL VIDEOS COUNT
                $subcategory_sitevideo_count = $tableSitevideo->getVideosCount(array('category_id' => $subresults->category_id, 'columnName' => 'subcategory_id'));
                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $subcategory_sitevideo_count,
                    'file_id' => $subresults->file_id,
                    'banner_id' => $subresults->banner_id,
                    'order' => $subresults->cat_order,
                    'sponsored' => $subresults->sponsored);
            }

            //GET TOTAL VIDEOS COUNT
            $category_sitevideo_count = $tableSitevideo->getVideosCount(array('category_id' => $value->category_id, 'columnName' => 'category_id'));

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $category_sitevideo_count,
                'file_id' => $value->file_id,
                'banner_id' => $value->banner_id,
                'sponsored' => $value->sponsored,
                'sub_categories' => $sub_cat_array);
        }

        $this->view->categories = $categories;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->category_id = $category_id = $request->getParam('category_id', 0);
        $perform = $request->getParam('perform', 'add');

        $cat_dependency = 0;
        $subcat_dependency = 0;
        if ($category_id) {
            $category = Engine_Api::_()->getItem('sitevideo_video_category', $category_id);
            if ($category && empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
            } elseif ($category && !empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
                $subcat_dependency = $category->category_id;
            }
        }

        if (($perform == 'add') && !empty($getCategoriesTempInfo)) {
            $this->view->form = $form = new Sitevideo_Form_Admin_Videocategories_Add();

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
                $values['subcat_dependency'] = $subcat_dependency;
                $row = $tableCategory->createRow();
                $row->setFromArray($values);

                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $videoFileIcon = $row->setVideo($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileIcon->file_id)) {
                        $row->file_id = $videoFileIcon->file_id;
                    }
                }

                //UPLOAD CATEGORY VIDEO
                if (isset($_FILES['video'])) {
                    $videoFile = $row->setVideo($form->video, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFile->file_id)) {
                        $row->video_id = $videoFile->file_id;
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $videoFileBanner = $row->setVideo($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileBanner->file_id)) {
                        $row->banner_id = $videoFileBanner->file_id;
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
                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->sitevideo()->videoCategoriesPageCreate(array(0 => $category_id));
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitevideo', 'action' => 'video-categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        } else if (!empty($getCategoriesTempInfo)) {
            $this->view->form = $form = new Sitevideo_Form_Admin_Videocategories_Edit();
            $category = Engine_Api::_()->getItem('sitevideo_video_category', $category_id);
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
                $cat_dependency = $category->cat_dependency;
                $subcat_dependency = $category->subcat_dependency;
                if ($category_id && empty($subcat_dependency) && !empty($cat_dependency)) {
                    $cat_dependency = $cat_dependency;
                    $subcat_dependency = 0;
                } elseif ($category_id && !empty($subcat_dependency) && !empty($cat_dependency)) {
                    $cat_dependency = $cat_dependency;
                    $subcat_dependency = $subcat_dependency;
                }

                $category->cat_dependency = $cat_dependency;
                $category->subcat_dependency = $subcat_dependency;
                $category->banner_description = $values['banner_description'];
                $category->featured_tagline = $values['featured_tagline'];
                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $previous_file_id = $category->file_id;
                    $videoFileIcon = $category->setVideo($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileIcon->file_id)) {

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_file_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
                            $file->delete();
                        }

                        $category->file_id = $videoFileIcon->file_id;
                        $category->save();
                    }
                }

                //UPLOAD CATEGORY VIDEO
                if (isset($_FILES['video'])) {
                    $previous_video_id = $category->video_id;
                    $videoFile = $category->setVideo($form->video, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFile->file_id)) {
                        $category->video_id = $videoFile->file_id;

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_video_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_video_id);
                            $file->delete();
                        }
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $previous_banner_id = $category->banner_id;
                    $videoFileBanner = $category->setVideo($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($videoFileBanner->file_id)) {

                        //DELETE PREVIOUS CATEGORY BANNER
                        if ($previous_banner_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
                            $file->delete();
                        }

                        $category->banner_id = $videoFileBanner->file_id;
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

                if (isset($values['removevideo']) && !empty($values['removevideo'])) {
                    //DELETE CATEGORY ICON
                    $file = Engine_Api::_()->getItem('storage_file', $category->video_id);

                    //UPDATE FILE ID IN CATEGORY TABLE
                    $category->video_id = 0;
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

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->sitevideo()->videoCategoriesPageCreate(array(0 => $category_id));
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitevideo', 'action' => 'video-categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        }
    }

    //ACTION FOR MAPPING OF CHANNELS
    public function mappingVideoCategoryAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID AND OBJECT
        $this->view->catid = $catid = $this->_getParam('category_id');
        $category = Engine_Api::_()->getItem('sitevideo_video_category', $catid);

        //CREATE FORM
        $this->view->form = $form = new Sitevideo_Form_Admin_Settings_Video_Mapping();

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

                //GET CHANNEL TABLE
                $tableSitevideo = Engine_Api::_()->getDbtable('videos', 'sitevideo');

                //GET CATEGORY TABLE
                $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');

                //ON CATEGORY DELETE
                $rows = $tableCategory->getSubCategories(array('category_id' => $catid, 'fetchColumns' => array('category_id')));
                foreach ($rows as $row) {
                    $row->delete();
                }

                $previous_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $catid));
                $new_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $values['new_category_id']));

                /// CHANNELS WHICH HAVE THIS CATEGORY
                if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
                    $videosIds = $tableSitevideo->getCategoryList(array('category_id' => $catid, 'category_type' => 'category_id'));

                    foreach ($videosIds as $video_id) {

                        //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                        Engine_Api::_()->fields()->getTable('sitevideo', 'values')->delete(array('item_id = ?' => $video_id));
                        Engine_Api::_()->fields()->getTable('sitevideo', 'search')->delete(array('item_id = ?' => $video_id));

                        //UPDATE THE PROFILE TYPE OF ALREADY CREATED CHANNELS
                        $tableSitevideo->update(array('profile_type' => $new_cat_profile_type), array('video_id = ?' => $video_id));
                    }
                }

                //CHANNEL TABLE CATEGORY DELETE WORK
                if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
                    $tableSitevideo->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
                } else {

                    $selectChannels = $tableSitevideo->select()
                            ->from($tableSitevideo->info('name'))
                            ->where('category_id = ?', $catid);

                    foreach ($tableSitevideo->fetchAll($selectChannels) as $video) {
                        $video->delete();
                    }
                }
                $page_id = $db->select()
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', "sitevideo_video_categories-home_category_" . $catid)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                $content_ids = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $page_id)
                        ->query()
                        ->fetchAll();
                foreach ($content_ids as $content_id) {
                    $content = $content_id['content_id'];
                    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = $content");
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

    public function deleteVideoCategoryAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $category_id = $this->_getParam('category_id');

        $this->view->category_id = $category_id;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');

        //GET CHANNEL TABLE
        $tableSitevideo = Engine_Api::_()->getDbtable('videos', 'sitevideo');

        if ($this->getRequest()->isPost()) {

            //IF SUB-CATEGORY IS MAPPED
            $previous_cat_profile_type = $tableCategory->getProfileType(array('categoryIds' => null, 'category_id' => $category_id));

            if ($previous_cat_profile_type) {

                //SELECT CHANNELS WHICH HAVE THIS CATEGORY
                $videosIds = $tableSitevideo->getCategoryList(array('category_id' => $category_id, 'category_type' => 'category_id'));

                foreach ($videosIds as $video_id) {
                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('video', 'values')->delete(array('item_id = ?' => $video_id));
                    Engine_Api::_()->fields()->getTable('video', 'search')->delete(array('item_id = ?' => $video_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED CHANNELS
                    $tableSitevideo->update(array('profile_type' => 0), array('video_id = ?' => $video_id));
                }
            }

            //SITEVIDEOT TABLE SUB-CATEGORY DELETE WORK
            $tableSitevideo->update(array('subcategory_id' => 0), array('subcategory_id = ?' => $category_id));

            $tableCategory->delete(array('category_id = ?' => $category_id));


            //GET URL
            $url = $this->_helper->url->url(array('action' => 'video-categories', 'controller' => 'settings', 'perform' => 'add', 'category_id' => 0));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }

        $this->renderScript('admin-settings/delete-video-category.tpl');
    }

    public function utilityAction() {

        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_settings');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main_settings', array(), 'sitevideo_admin_main_utility');

        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
        if (function_exists('shell_exec')) {
            // Get version
            $this->view->version = $version = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
            $command = "$ffmpeg_path -formats 2>&1";
            $this->view->format = $format = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
                    . shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
        }
    }

    private function verifyYotubeApiKey($key) {
        $option = array(
            'part' => 'id',
            'key' => $key,
            'maxResults' => 1
        );
        $url = "https://www.googleapis.com/youtube/v3/search?" . http_build_query($option, 'a', '&');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $json_response = curl_exec($curl);
        curl_close($curl);
        $responseObj = !empty($json_response) ? Zend_Json::decode($json_response): array();
        if (empty($responseObj['error'])) {
            return array('success' => 1);
        }
        return $responseObj['error'];
    }

    //Action for synchronize video
    public function synchronizeVideoAction() {
        set_time_limit(0);
        ini_set("max_execution_time", "300");
        ini_set("memory_limit", "256M");
        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_synchronize');
        $this->view->videoCount = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideosCount(array('synchronized' => 0));
        $this->view->otherVideoCount = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideosCount(array('synchronized' => 0, 'type' => array( 'dailymotion', 'vimeo', 'youtube', 'iframely')));
        $this->view->mycomputerVideoCount = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideosCount(array('synchronized' => 0, 'type' => array( 'upload', 'mydevice' )));
        $this->view->ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitevideo_ffmpeg_path;
        $start = $this->_getParam('start', 0);
        $this->view->autoSink = $this->_getParam('autoSink', 0);
        //1 for completed
        $this->view->status = $this->_getParam('status', 0);
        if ($start) {
            $nonSynchronizedVideos = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideos(array('synchronized' => 0, 'selectLimit' => 10, 'custom_order' => array('type', 1, 2, 4, 3)));
            foreach ($nonSynchronizedVideos as $video) {

                if (!$video->checkType('upload')) {
                    $thumbnailUrl = $this->handleThumbnail($video->type, $video->code);
                    $video->saveVideoThumbnail($thumbnailUrl);
                    $video->synchronized = 1;
                } else if ($video->checkType('upload') && !empty($this->view->ffmpeg_path)) {
                    $this->generateMycomputerThumbnail($video);
                    $video->synchronized = 1;
                }

                $video->save();
            }
            $videoCount = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideosCount(array('synchronized' => 0));
            if ($videoCount > 0 && ($this->view->otherVideoCount > 0 || ($this->view->mycomputerVideoCount > 0 && !empty($this->view->ffmpeg_path)))) {
                $this->_redirect("admin/sitevideo/settings/synchronize-video?autoSink=1");
            } else {
                if ($this->view->mycomputerVideoCount > 0 && empty($this->view->ffmpeg_path))
                    $this->_redirect("admin/sitevideo/settings/synchronize-video?status=2");
                else
                    $this->_redirect("admin/sitevideo/settings/synchronize-video?status=1");
            }
        }
    }

    public function generateMycomputerThumbnail($video) {
        if (empty($video->file_id))
            return false;

        $storageFile = Engine_Api::_()->getItem('storage_file', $video->file_id);

        if (empty($storageFile))
            return false;

        if (!function_exists('shell_exec')) {
            throw new Sitevideo_Model_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
        }

        if (!function_exists('exec')) {
            throw new Sitevideo_Model_Exception('Unable to execute shell commands using exec(); the function is disabled.');
        }

        // Make sure FFMPEG path is set
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        // Make sure FFMPEG path is set
        $ffmpeg_path = $coreSettings->getSetting('sitevideo.ffmpeg.path', '');

        if (!$ffmpeg_path) {
            throw new Sitevideo_Model_Exception('Ffmpeg not configured');
        }

        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);

            if ($return > 0) {
                throw new Sitevideo_Model_Exception('Ffmpeg found, but is not executable');
            }
        }
        $path = @chmod(APPLICATION_PATH . '/' . $storageFile->storage_path, 0777);
        $thumbTemp = APPLICATION_PATH . '/temporary/link_thumb_' . Engine_Api::_()->seaocore()->getSlug($video->title, 225) . $storageFile->parent_id . '_t.jpg';
        $thumbMain = APPLICATION_PATH . '/temporary/link_thumb_' . Engine_Api::_()->seaocore()->getSlug($video->title, 225) . $storageFile->parent_id . '_m.jpg';
        $thumbLarge = APPLICATION_PATH . '/temporary/link_thumb_' . Engine_Api::_()->seaocore()->getSlug($video->title, 225) . $storageFile->parent_id . '_l.jpg';
        $thumbNormal = APPLICATION_PATH . '/temporary/link_thumb_' . Engine_Api::_()->seaocore()->getSlug($video->title, 225) . $storageFile->parent_id . '_in.jpg';
        $outputPath = APPLICATION_PATH . '/' . $storageFile->storage_path;
        $thumb_splice = $video->duration / 2;
        $thumbCommandMain = "'$ffmpeg_path' -i '$outputPath' -f image2 -ss $thumb_splice -vframes 1 -filter scale=1600:1600 -v 2 -y '$thumbTemp' 2>&1";
        $out = shell_exec($thumbCommandMain);
        @chmod($thumbTemp, 0777);
        $normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
        $normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
        $largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);

        // Save video and thumbnail to storage system
        $params = array(
            'parent_id' => $video->getIdentity(),
            'parent_type' => $video->getType(),
           // 'parent_file_id' => $video->file_id,
            'user_id' => $video->owner_id
        );
       // $table = Engine_Api::_()->getDbTable('albums', 'sitevideo');
       // $db = $table->getAdapter();
       // $db->query("update engine4_storage_files set type=concat(concat(type,'_'),file_id) where parent_file_id ={$video->file_id} and type = 'thumb.large'");
        //$db->query("update engine4_storage_files set type=concat(concat(type,'_'),file_id) where parent_file_id ={$video->file_id} and type = 'thumb.main'");
        //$db->query("update engine4_storage_files set type=concat(concat(type,'_'),file_id) where parent_file_id ={$video->file_id} and type = 'thumb.normal'");
        if (file_exists($thumbTemp)) {
            $image = Engine_Image::factory();
            $image->open($thumbTemp)
                    ->resize($mainWidth, $mainHeight)
                    ->write($thumbMain)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($thumbTemp)
                    ->resize($largeWidth, $largeHeight)
                    ->write($thumbLarge)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($thumbTemp)
                    ->resize($normalWidth, $normalHeight)
                    ->write($thumbNormal)
                    ->destroy();
            $thumbNormalSuccessRow = Engine_Api::_()->storage()->create($thumbMain, array_merge($params, array('type' => 'thumb.main')));
            Engine_Api::_()->storage()->create($thumbLarge, array_merge($params, array('type' => 'thumb.large')));
            Engine_Api::_()->storage()->create($thumbNormal, array_merge($params, array('type' => 'thumb.normal')));

            // Video processing was a success!
            // Save the information
            if ($thumbNormalSuccessRow) {
                $video->photo_id = $thumbNormalSuccessRow->file_id;
            }
            $video->save();

            unlink($thumbMain);
            unlink($thumbLarge);
            unlink($thumbNormal);
            unlink($thumbTemp);
        }
    }

    // handles thumbnails
    public function handleThumbnail($type, $code = null) {
        switch ($type) {

            //youtube
            case "youtube":
                $thumbnail = "";
                $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
                foreach ($thumbnailSize as $size) {
                    $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
                    $file_headers = @get_headers($thumbnailUrl);
                    if (isset($file_headers[0]) && strpos($file_headers[0], '404 Not Found') == false) {
                        $thumbnail = $thumbnailUrl;
                        break;
                    }
                }
                return $thumbnail;
            //vimeo
            case "vimeo":
                $thumbnail = "";
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                if (isset($data->video->thumbnail_large))
                    $thumbnail = $data->video->thumbnail_large;
                else if (isset($data->video->thumbnail_medium))
                    $thumbnail = $data->video->thumbnail_medium;
                else if (isset($data->video->thumbnail_small))
                    $thumbnail = $data->video->thumbnail_small;

                return $thumbnail;

            //dailymotion
            case "dailymotion":
                $thumbnail = "";
                $thumbnailUrl = 'https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_small_url,thumbnail_large_url,thumbnail_medium_url';
                $json_thumbnail = file_get_contents($thumbnailUrl);
                if ($json_thumbnail) {
                    $thumbnails = json_decode($json_thumbnail);
                    if (isset($thumbnails->thumbnail_large_url))
                        $thumbnail = $thumbnails->thumbnail_large_url;
                    else if (isset($thumbnails->thumbnail_medium_url)) {
                        $thumbnail = $thumbnails->thumbnail_medium_url;
                    } else if (isset($thumbnails->thumbnail_small_url)) {
                        $thumbnail = $thumbnails->thumbnail_small_url;
                    }
                }
                return $thumbnail;
        }
    }

    //ACTION FOR SHOW STATISTICS OF VIDEO PLUGIN
    public function statisticAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_statistics');

        //GET EVENT TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoTableName = $videoTable->info('name');

        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';

        //GET Video DETAILS
        $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalvideo');

        $this->view->totalSitevideo = $select->query()->fetchColumn();
        //Total Featured videos
        $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalfeatured')->where('featured = ?', 1);
        $this->view->totalfeatured = $select->query()->fetchColumn();

        //Total sponsored videos
        $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalsponsored')->where('sponsored = ?', 1);
        $this->view->totalsponsored = $select->query()->fetchColumn();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        //Total rated video
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $ratingTableName = $ratingTable->info('name');
        $this->view->totalVideorated = $db->query("
            SELECT count( totalvideorating )
            FROM (

            SELECT count( * ) AS `totalvideorating`
            FROM {$ratingTableName}
            WHERE (
            {$ratingTableName}.resource_type = 'video'
            )
            GROUP BY {$ratingTableName}.resource_id
            ) AS t
        ")
                ->fetchColumn();

        $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
        $favouriteTableName = $favouriteTable->info('name');
        $this->view->totalVideofavourited = $db->query("
            SELECT count( totalvideofavourited )
            FROM (

            SELECT count( * ) AS `totalvideofavourited`
            FROM {$favouriteTableName}
            WHERE (
            {$favouriteTableName}.resource_type = 'video'
            )
            GROUP BY {$favouriteTableName}.resource_id
            ) AS t
        ")
                ->fetchColumn();

        // Total Playlist
        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
        $playlistTableName = $playlistTable->info('name');
        $select = $playlistTable->select()->from($playlistTableName, 'count(*) AS totalvideo');
        $this->view->totalPlaylist = $select->query()->fetchColumn();

        //Total Watchlater
        $watchlaterTable = Engine_Api::_()->getDbtable('watchlaters', 'sitevideo');
        $watchlaterTableName = $watchlaterTable->info('name');
        $select = $watchlaterTable->select()->from($watchlaterTableName, 'count(*) AS totalvideo');
        $this->view->totalWatchlater = $select->query()->fetchColumn();

        //Total Channel
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelTableName = $channelTable->info('name');
        $select = $channelTable->select()->from($channelTableName, 'count(*) AS totalvideo');
        $this->view->totalChannel = $select->query()->fetchColumn();

        //Total Subscribed channels
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionTableName = $subscriptionTable->info('name');
        $this->view->totalChannelsubscribed = $db->query("
            SELECT count( totalvideofavourited )
            FROM (

            SELECT count( * ) AS `totalvideofavourited`
            FROM {$subscriptionTableName}
            GROUP BY {$subscriptionTableName}.channel_id
            ) AS t
        ")
                ->fetchColumn();

        //Total rated video
        $this->view->totalChannelrated = $db->query("
            SELECT count( totalvideorating )
            FROM (

            SELECT count( * ) AS `totalvideorating`
            FROM {$ratingTableName}
            WHERE (
            {$ratingTableName}.resource_type = 'sitevideo_channel'
            )
            GROUP BY {$ratingTableName}.resource_id
            ) AS t
        ")
                ->fetchColumn();

        $this->view->totalChannelfavourited = $db->query("
            SELECT count( totalvideofavourited )
            FROM (

            SELECT count( * ) AS `totalvideofavourited`
            FROM {$favouriteTableName}
            WHERE (
            {$favouriteTableName}.resource_type = 'sitevideo_channel'
            )
            GROUP BY {$favouriteTableName}.resource_id
            ) AS t
        ")
                ->fetchColumn();
        //Total Featured videos
        $select = $channelTable->select()->from($channelTableName, 'count(*) AS totalfeatured')->where('featured = ?', 1);
        $this->view->totalChannelfeatured = $select->query()->fetchColumn();

        //Total sponsored videos
        $select = $channelTable->select()->from($channelTableName, 'count(*) AS totalsponsored')->where('sponsored = ?', 1);
        $this->view->totalChannelsponsored = $select->query()->fetchColumn();
    }

    //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
    function getChannelsAction() {
        //GET EVENT TABLE
        $siteeventTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $siteeventTableName = $siteeventTable->info('name');

        //MAKE QUERY
        $select = $siteeventTable->select()
                ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where($siteeventTableName . '.search = ?', '1')
                ->order('title ASC')
                ->limit($this->_getParam('limit', 40));

        //FETCH RESULTS
        $usersiteevents = $siteeventTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($usersiteevents as $usersiteevent) {
                $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteevent->event_id,
                    'label' => $usersiteevent->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($usersiteevents as $usersiteevent) {
                $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteevent->channel_id,
                    'label' => $usersiteevent->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

    public function getChannelWidgetParamsAction() {
        $content_id = isset($_GET['content_id']) ? $_GET['content_id'] : 0;
        $toValues = $toValuesArray = array();
        $params = array('starttime' => '', 'endtime' => '');
        $toValuesString = '';
        if ($content_id) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');
            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('content_id = ?', $content_id)
                    ->where('name = ?', 'sitevideo.special-channels')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
                if (isset($params['toValues']) && !empty($params['toValues'])) {
                    $toValues = $params['toValues'];
                    if (!empty($toValues)) {
                        $toValues = explode(',', $toValues);
                        $toValues = array_unique($toValues);
                        $toValuesString = implode(',', $toValues);
                        $toValuesArray = array();
                        foreach ($toValues as $key => $id) {
                            $channel = Engine_Api::_()->getItem('sitevideo_channel', $id);
                            if ($channel instanceof Core_Model_Item_Abstract) {
                                $toValuesArray[$key]['id'] = $id;
                                $toValuesArray[$key]['title'] = $channel->getTitle();
                            }
                        }
                    }
                }
            }
        }
        $this->view->toValuesArray = $toValuesArray;
        $this->view->toValuesString = $toValuesString;
    }

    public function getModelInfoAction() {
        $model = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.videoswipper.destination');
        $toValues = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.videoswipper.model');
        $toValuesString = $toValues;
        if (!empty($toValues)) {
            $toValues = explode(',', $toValues);
        } else {
            $toValues = array();
        }
        $itemType = "";
        switch ($model) {
            case 'channel' :
                $itemType = 'sitevideo_channel';
                break;
            case 'event' :
                $itemType = 'siteevent_event';
                break;
        }
        $toValuesArray = array();
        if (!empty($itemType)) {
            foreach ($toValues as $key => $id) {
                $item = Engine_Api::_()->getItem($itemType, $id);
                if ($item instanceof Core_Model_Item_Abstract) {
                    $toValuesArray[$key]['id'] = $id;
                    $toValuesArray[$key]['title'] = $item->getTitle();
                }
            }
        }
        $this->view->toValuesArray = $toValuesArray;
        $this->view->toValuesString = $toValuesString;
    }

    public function getVideoWidgetParamsAction() {
        $content_id = isset($_GET['content_id']) ? $_GET['content_id'] : 0;
        $toValues = $toValuesArray = array();
        $params = array('starttime' => '', 'endtime' => '');
        $toValuesString = '';
        if ($content_id) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');
            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('content_id = ?', $content_id)
                    ->where('name = ?', 'sitevideo.special-videos')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
                if (isset($params['toValues']) && !empty($params['toValues'])) {
                    $toValues = $params['toValues'];
                    if (!empty($toValues)) {
                        $toValues = explode(',', $toValues);
                        $toValues = array_unique($toValues);
                        $toValuesString = implode(',', $toValues);
                        $toValuesArray = array();
                        foreach ($toValues as $key => $id) {
                            $video = Engine_Api::_()->getItem('sitevideo_video', $id);
                            if ($video instanceof Core_Model_Item_Abstract) {
                                $toValuesArray[$key]['id'] = $id;
                                $toValuesArray[$key]['title'] = $video->getTitle();
                            }
                        }
                    }
                }
            }
        }
        $this->view->toValuesArray = $toValuesArray;
        $this->view->toValuesString = $toValuesString;
    }

    /*
      //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
      function getChannelsAction() {

      //GET EVENT TABLE
      $siteeventTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
      $siteeventTableName = $siteeventTable->info('name');

      //MAKE QUERY
      $select = $siteeventTable->select()
      ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
      ->order('title ASC')
      ->limit($this->_getParam('limit', 40));

      //FETCH RESULTS
      $usersiteevents = $siteeventTable->fetchAll($select);
      $data = array();
      $mode = $this->_getParam('struct');

      if ($mode == 'text') {
      foreach ($usersiteevents as $usersiteevent) {
      $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
      $data[] = array(
      'id' => $usersiteevent->channel_id,
      'label' => $usersiteevent->title,
      'photo' => $content_photo
      );
      }
      } else {
      foreach ($usersiteevents as $usersiteevent) {
      $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
      $data[] = array(
      'id' => $usersiteevent->channel_id,
      'label' => $usersiteevent->title,
      'photo' => $content_photo
      );
      }
      }
      return $this->_helper->json($data);
      }
     */

    //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
    function getVideosAction() {

        //GET VIDEOS TABLE
        $sitevideoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $sitevideoTableName = $sitevideoTable->info('name');

        //MAKE QUERY
        $select = $sitevideoTable->select()
                ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->order('title ASC')
                ->limit($this->_getParam('limit', 40));

        //FETCH RESULTS
        $usersitevideos = $sitevideoTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($usersitevideos as $usersitevideo) {
                $content_photo = $this->view->itemPhoto($usersitevideo, 'thumb.icon');
                $data[] = array(
                    'id' => $usersitevideo->video_id,
                    'label' => $usersitevideo->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($usersitevideos as $usersitevideo) {
                $content_photo = $this->view->itemPhoto($usersitevideo, 'thumb.icon');
                $data[] = array(
                    'id' => $usersitevideo->video_id,
                    'label' => $usersitevideo->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

    public function templateAction() {
        //render to tpl
    }

}
