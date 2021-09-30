<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitecrowdfunding_Form_Admin_Settings_Global') {
            
        }
        return true;
    }

    //ACTION FOR GLOBAL SETTINGS
    public function indexAction() {
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license1.php';
    }

    public function createEditAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_settings', array(), 'sitecrowdfunding_admin_main_createedit');

        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }
        $local_language = explode('_', $this->view->locale()->getLocale()->__toString());
        $this->view->language = $local_language[0];

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_CreateEdit();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        }
    }

    //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES AND 3RD LEVEL CATEGORIES
    public function categoriesAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_categories');

        $this->view->success_msg = $this->_getParam('success');

        //LIGHTBOX FOR OTHER PLUGINS    
        $this->view->template_type = $this->_getParam('template_type');

        //GET TASK
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

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');
        $tableCategoryName = $tableCategory->info('name');

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET PROJECT TABLE
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');

        if ($task == "changeorder") {
            $divId = $_GET['divId'];
            $sitecrowdfundingOrder = explode(",", $_GET['sitecrowdfundingorder']);
            //RESORT CATEGORIES
            if ($divId == "categories") {
                for ($i = 0; $i < count($sitecrowdfundingOrder); $i++) {
                    $category_id = substr($sitecrowdfundingOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 7) == "subcats") {
                for ($i = 0; $i < count($sitecrowdfundingOrder); $i++) {
                    $category_id = substr($sitecrowdfundingOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 11) == "treesubcats") {
                for ($i = 0; $i < count($sitecrowdfundingOrder); $i++) {
                    $category_id = substr($sitecrowdfundingOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            }
        }

        $categories = array();
        $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'file_id', 'banner_id', 'sponsored', 'font_icon'), null, 0, 0, 1);
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $subcategories = $tableCategory->getSubCategories($value->category_id);
            foreach ($subcategories as $subresults) {
                $subsubcategories = $tableCategory->getSubCategories($subresults->category_id);
                $treesubarrays[$subresults->category_id] = array();

                foreach ($subsubcategories as $subsubcategoriesvalues) {
                    //GET TOTAL PROJECT COUNT
                    $subcategory_project_count = $tableProject->getProjectsCount($subsubcategoriesvalues->category_id, 'subsubcategory_id');
                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
                        'count' => $subcategory_project_count,
                        'file_id' => $subsubcategoriesvalues->file_id,
                        'banner_id' => $subsubcategoriesvalues->banner_id,
                        'order' => $subsubcategoriesvalues->cat_order,
                        'sponsored' => $subsubcategoriesvalues->sponsored,
                        'font_icon' => $value->font_icon,);
                }

                //GET TOTAL PROJECTS COUNT
                $subcategory_project_count = $tableProject->getProjectsCount($subresults->category_id, 'subcategory_id');

                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $subcategory_project_count,
                    'file_id' => $subresults->file_id,
                    'banner_id' => $subresults->banner_id,
                    'order' => $subresults->cat_order,
                    'sponsored' => $subresults->sponsored,
                    'font_icon' => $value->font_icon,
                );
            }

            //GET TOTAL PROJECTS COUNT
            $category_project_count = $tableProject->getProjectsCount($value->category_id, 'category_id');

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $category_project_count,
                'file_id' => $value->file_id,
                'banner_id' => $value->banner_id,
                'sponsored' => $value->sponsored,
                'sub_categories' => $sub_cat_array,
                'font_icon' => $value->font_icon,
            );
        }

        $this->view->categories = $categories;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');
        $tableCategoryName = $tableCategory->info('name');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->category_id = $category_id = $request->getParam('category_id', 0);
        $perform = $request->getParam('perform', 'add');
        $cat_dependency = 0;
        $subcat_dependency = 0;
        if ($category_id) {
            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id);
            if ($category && empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
            } elseif ($category && !empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
                $subcat_dependency = $category->category_id;
            }
        }

        if ($perform == 'add') {
            $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Categories_Add();

            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                return;
            }

            //CHECK VALIDITY
            if (!$form->isValid($this->getRequest()->getPost())) {

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
                        $row->banner_url = "http://" . $values['banner_url'];
                    } else {
                        $row->banner_url = $values['banner_url'];
                    }
                } else {
                    $row->banner_url = $values['banner_url'];
                }

                $category_id = $row->save();

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->sitecrowdfunding()->categoriesPageCreate(array(0 => $category_id));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitecrowdfunding', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        } else {
            $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Categories_Edit();
            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id);
            $form->populate($category->toArray());

            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                return;
            }

            //CHECK VALIDITY
            if (!$form->isValid($this->getRequest()->getPost())) {

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
                $category->font_icon = $values['font_icon'];
                if (isset($values['banner_description'])) {
                    $category->banner_description = $values['banner_description'];
                }
                if (isset($values['featured_tagline'])) {
                    $category->featured_tagline = $values['featured_tagline'];
                }
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

                include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';

                if (empty($tempCategoriesFlag))
                    return;

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
                        $category->banner_url = "http://" . $values['banner_url'];
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

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->sitecrowdfunding()->categoriesPageCreate(array(0 => $category_id));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'sitecrowdfunding', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        }
    }

    //ACTION FOR DELETE THE Category
    public function deleteCategoryAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $category_id = $this->_getParam('category_id');

        $cat_dependency = $this->_getParam('cat_dependency');

        $this->view->category_id = $category_id;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');
        $tableCategoryName = $tableCategory->info('name');

        //GET PROJECT TABLE
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');

        if ($this->getRequest()->isPost()) {
            //IF SUB-CATEGORY AND 3RD LEVEL CATEGORY IS MAPPED
            $previous_cat_profile_type = $tableCategory->getProfileType(null, $category_id);

            if ($previous_cat_profile_type) {

                //SELECT PROJECTS WHICH HAVE THIS CATEGORY
                $projects = $tableProject->getCategoryList($category_id, 'category_id');

                foreach ($projects as $project) {

                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'values')->delete(array('item_id = ?' => $project->project_id));
                    Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->delete(array('item_id = ?' => $project->project_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED PROJECTS
                    $tableProject->update(array('profile_type' => 0), array('project_id = ?' => $project->project_id));
                }
            }

            //SITEPROJECT TABLE SUB-CATEGORY/3RD LEVEL DELETE WORK
            $tableProject->update(array('subcategory_id' => 0, 'subsubcategory_id' => 0), array('subcategory_id = ?' => $category_id));
            $tableProject->update(array('subsubcategory_id' => 0), array('subsubcategory_id = ?' => $category_id));

            $tableCategory->delete(array('cat_dependency = ?' => $category_id, 'subcat_dependency = ?' => $category_id));
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

    //ACTION FOR MAPPING OF CATEGORIES
    Public function mappingCategoryAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID AND OBJECT
        $this->view->catid = $catid = $this->_getParam('category_id');
        $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $catid);

        //GET CATEGORY DEPENDANCY
        $this->view->subcat_dependency = $subcat_dependency = $this->_getParam('subcat_dependency');

        //CREATE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_Mapping();

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

                //GET PROJECT TABLE
                $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
                //GET CATEGORY TABLE
                $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');

                //ON CATEGORY DELETE
                $rows = $tableCategory->getSubCategories($catid);
                foreach ($rows as $row) {
                    $subrows = $tableCategory->getSubCategories($row->category_id);
                    foreach ($subrows as $subrow) {
                        $subrow->delete();
                    }
                    $row->delete();
                }

                $previous_cat_profile_type = $tableCategory->getProfileType(null, $catid);
                $new_cat_profile_type = $tableCategory->getProfileType(null, $values['new_category_id']);

                /// PROJECTS WHICH HAVE THIS CATEGORY
                if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
                    $projects = $tableProject->getCategoryList($catid, 'category_id');
                    foreach ($projects as $project) {

                        //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                        Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'values')->delete(array('item_id = ?' => $project->project_id));
                        Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->delete(array('item_id = ?' => $project->project_id));
                        //UPDATE THE PROFILE TYPE OF ALREADY CREATED PROJECTS
                        $tableProject->update(array('profile_type' => $new_cat_profile_type), array('project_id = ?' => $project->project_id));
                    }
                }

                //PROJECT TABLE CATEGORY DELETE WORK
                if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
                    $tableProject->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
                } else {

                    $selectProjects = $tableProject->select()
                            ->from($tableProject->info('name'))
                            ->where('category_id = ?', $catid);

                    foreach ($tableProject->fetchAll($selectProjects) as $project) {
                        $project->delete();
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

    //ACTINO FOR PROJECT SEARCH FORM TAB
    public function projectFormSearchAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_projectsearchform');

        //GET SEARCH TABLE
        $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        //CHECK POST
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $values = $_POST;
            $rowCategory = $tableSearchForm->getFieldsOptions('sitecrowdfunding_project', 'category_id');
            $rowLocation = $tableSearchForm->getFieldsOptions('sitecrowdfunding_project', 'location');
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

        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
    }

    //ACTION TO GET PARAMS FOR SPECIAL PROJECTS WIDGET
    public function getProjectWidgetParamsAction() {
        $content_id = isset($_GET['content_id']) ? $_GET['content_id'] : 0;
        $toValues = $toValuesArray = array();
        $params = array('starttime' => '', 'endtime' => '');
        $toValuesString = '';
        if ($content_id) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $pageTableName = $pageTable->info('name');
            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();
            //WE NEED PAGE NAME TO HIDE THE PROJECT SELECT SETTING FROM THE WIDGET
            $page_id = $tableContent->select()
                    ->from($tableContentName, array('page_id'))
                    ->where('content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn(); 
            $pageName = $pageTable->select()
                    ->from($pageTableName, array('name'))
                    ->where('page_id = ?', $page_id)
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
                            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $id);
                            if ($project instanceof Core_Model_Item_Abstract) {
                                $toValuesArray[$key]['id'] = $id;
                                $toValuesArray[$key]['title'] = $project->getTitle();
                            }
                        }
                    }
                }
            }
        }
        $this->view->toValuesArray = $toValuesArray;
        $this->view->toValuesString = $toValuesString;
        $this->view->widgetPageName = $pageName;
    }

    //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
    function getProjectsAction() {

        //GET PROJECTS TABLE
        $sitecrowdfundingTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $sitecrowdfundingTableName = $sitecrowdfundingTable->info('name');
        $currentDate = date('Y-m-d H:i:s');
        //MAKE QUERY
        //DO NOT INCLUDE THE PROJECTS BEFORE START DATE
        $select = $sitecrowdfundingTable->select()
                ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where("$sitecrowdfundingTableName.state != ?", 'draft')
                ->where("$sitecrowdfundingTableName.approved = ?", 1)
                ->where("start_date <= '$currentDate'")
                ->where("is_gateway_configured = ?", 1)
                ->order('title ASC')
                ->limit($this->_getParam('limit', 40));

        //FETCH RESULTS
        $usersiteprojects = $sitecrowdfundingTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($usersiteprojects as $usersiteproject) {
                $content_photo = $this->view->itemPhoto($usersiteproject, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteproject->project_id,
                    'label' => $usersiteproject->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($usersiteprojects as $usersiteproject) {
                $content_photo = $this->view->itemPhoto($usersiteproject, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteproject->project_id,
                    'label' => $usersiteproject->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
    public function displayProjectFormAction() {

        $field_id = $this->_getParam('id');
        $name = $this->_getParam('name');
        $display = $this->_getParam('display');

        if (!empty($field_id)) {

            if ($name == 'location' && $display == 0) {
                Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitecrowdfunding_project', 'name = ?' => 'proximity'));
            }

            Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitecrowdfunding_project', 'searchformsetting_id = ?' => (int) $field_id));
        }
        $this->_redirect('admin/sitecrowdfunding/settings/project-form-search');
    }

    //ACTION FOR LEVEL SETTINGS
    public function levelAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_level');

        //$this->view->tab_type = 'levelType';
        //GET LEVEL ID
        if (null != ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $form->level_id->setValue($id);

        //POPULATE DATA
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $form->populate($permissionsTable->getAllowed('sitecrowdfunding_project', $id, array_keys($form->getValues())));
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
        if (empty($tempLevelFlag))
            return;
        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK VALIDITY
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        } 

        //PROCESS
        $values = $form->getValues();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        try {
            $permissionsTable->setAllowed('sitecrowdfunding_project', $id, $values);
            $db->commit();  
             //DISABLE THE SHIPPING LOCATIONS TAB IF NO MEMBER LEVEL IS HAVING REWARD CREATION ENABLED 
            $enabledRewardCreation = Engine_Api::_()->getDbtable('permissions', 'authorization')->fetchRow(array('name = ?' => 'reward_create', 'value = ?' => 1));

            $enable = empty($enabledRewardCreation) ? 0 : 1; 
            Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => $enable), array('name = ?' => 'sitecrowdfunding_admin_main_shippinglocation', 'module = ?' => 'sitecrowdfunding'));
             //GET NEW NAVIGATION
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_level');  
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function paymentAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_payment');
        // $this->view->enabledGatewayCount = Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount();

        //FORM GENERATION
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_Payment();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        }
    }

    //ACTION FOR SHOW STATISTICS OF CROWDFUNDING PLUGIN
    public function statisticAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_statistics_report');
        //GET NAVIGATION FOR SUB TABS
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_statistics_report', array(), 'sitecrowdfunding_admin_main_statistics');

        //GET TABLE
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backerTable->info('name');

        //GET Project DETAILS
        $select = $projectTable->select()->from($projectTableName, 'count(*) AS totalproject');

        $this->view->totalProjects = $select->query()->fetchColumn();

        //Total Projects in Draft
        $select = $projectTable->select()->from($projectTableName, 'count(*) AS totaldrafted')->where('state = ?', 'draft');
        $this->view->totalDrafted = $select->query()->fetchColumn();
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';

        //Total Failed Projects
        $select = $projectTable->select()->from($projectTableName, 'count(*) AS totalfailed')->where('state = ?', 'failed');
        $this->view->totalFailed = $select->query()->fetchColumn();

        //Total Successfull Projects
        $select = $projectTable->select()->from($projectTableName, 'count(*) AS totalsuccessfull')->where('state = ?', 'successful');
        $this->view->totalSuccessfull = $select->query()->fetchColumn();

        //Total Approved projects
        $select = $projectTable->select()->from($projectTableName, 'count(*) AS totalapproved')
                ->where('approved = ?', 1)
                ->where('state <> ?', 'draft');
        $this->view->totalApproved = $select->query()->fetchColumn();

        //Total Disapproved projects
        $select = $projectTable->select()->from($projectTableName, 'count(*) AS totaldisapproved')
                ->where('approved = ?', 0)
                ->where('state <> ?', 'draft');
        $this->view->totalDisapproved = $select->query()->fetchColumn();

        //Total Funded Amount
        $select = $backerTable->select()->from($backerTableName, 'sum(amount) AS totalfunded')
                ->where('payment_status = "active"')
                ->where('payout_status = "success"');

        $this->view->totalFundedAmount = $select->query()->fetchColumn();


        //Total Backed Amount
        $select = $backerTable->select()->from($backerTableName, 'sum(amount) AS totalfund')
                ->where('payment_status = "active" OR payment_status = "authorised"');
        $this->view->totalBackedAmount = $select->query()->fetchColumn();
    }

    public function faqAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_faq');
        //$this->view->faq_id = $faq_id = $this->_getParam('faq_id', 'faq_1');
    }

    public function projectOwnerFaqAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_settings', array(), 'sitecrowdfunding_admin_main_projectownerfaq');
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_ProjectOwnerFaq();
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        }
    }

    public function backersFaqAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main_settings', array(), 'sitecrowdfunding_admin_main_backersfaq');
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_BackersFaq();
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        }
    }

    public function landingPageSetupAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_landingpage_setup');
        $isSitehomepagevideo = false;
        $isCaptivate = false;
        if (Engine_Api::_()->hasModuleBootstrap('sitehomepagevideo')) {
            $isSitehomepagevideo = true;
        }
        if (Engine_Api::_()->hasModuleBootstrap('captivate')) {
            $isCaptivate = true;
        }
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_LandingPageSetup();
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            if ($values['sitecrowdfunding_landingpage_setup'] == 1 && $isSitehomepagevideo && $isCaptivate) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecrowdfunding_landingpage_setup', 1);
                $slideShowId = Engine_Api::_()->sitecrowdfunding()->createDefaultSlideshow();
                Engine_Api::_()->getApi('settemplate', 'sitecrowdfunding')->landingPage(true,$slideShowId);
            } else {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('sitecrowdfunding_landingpage_setup', 0);
            }
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
        }
    }

    public function readmeAction() {
        
    }

    public function manageMemberAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationManageMember = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main_settings', array(), 'sitecrowdfunding_admin_main_managemember');

        $this->view->manageRoleSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1);

        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }
        $local_language = explode('_', $this->view->locale()->getLocale()->__toString());
        $this->view->language = $local_language[0];

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_ManageMember();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                // Okay, save
                foreach ($values as $key => $value) {
                    if ($value != '') {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage-member'));
        }

        //GET ROLES TABLE NAME
        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');
        $projectSelect = $projectTable->select()
            ->from($projectTableName);
        $projectData = $projectSelect->query()->fetchAll();

        $projects = array();

        foreach ($projectData as $data) {
            $role_params = array();
            $getCatRolesParams = $rolesTable->rolesByProjectIdParams($data['project_id']);
            foreach ($getCatRolesParams as $roleParam) {
                $role_params[$data['project_id']][] = array(
                    'role_id' => $roleParam->role_id,
                    'role_name' => $roleParam->role_name,
                );
            }

            $projects[]  = array(
                'project_id' => $data['project_id'],
                'project_name' => $data['title'],
                'role_params' => $role_params,
            );
        }

        $this->view->projects = $projects;

    }

    public function manageMemberRoleAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationManageMemberRole = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main_settings', array(), 'sitecrowdfunding_admin_main_managememberrole');

        $this->view->manageRoleSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1);

        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }
        $local_language = explode('_', $this->view->locale()->getLocale()->__toString());
        $this->view->language = $local_language[0];

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Settings_ManageMemberRoles();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                // Okay, save
                foreach ($values as $key => $value) {
                    if ($value != '') {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage-member'));
        }

        //GET ROLES TABLE NAME
        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');
        $projectSelect = $projectTable->select()
            ->from($projectTableName);
        $projectData = $projectSelect->query()->fetchAll();

        $projects = array();

        foreach ($projectData as $data) {
            $role_params = array();
            $getCatRolesParams = $rolesTable->rolesByProjectIdParams($data['project_id']);
            foreach ($getCatRolesParams as $roleParam) {
                $role_params[$data['project_id']][] = array(
                    'role_id' => $roleParam->role_id,
                    'role_name' => $roleParam->role_name,
                );
            }

            $projects[]  = array(
                'project_id' => $data['project_id'],
                'project_name' => $data['title'],
                'role_params' => $role_params,
            );
        }

        $this->view->projects = $projects;

    }

    //ACTION FOR CREATE NEW ROLE PARAMETER
    public function createRoleAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GENERATE FORM
        $form = $this->view->form = new Sitecrowdfunding_Form_Admin_Settings_CreateRole();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        $this->view->options = array();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                //CHECK ROLES
                $options = (array) $this->_getParam('optionsArray');
                $options = array_filter(array_map('trim', $options));
                $options = array_slice($options, 0, 100);
                $this->view->options = $options;
                if (empty($options) || !is_array($options) || count($options) < 1) {
                    return $form->addError('You must add at least one roles.');
                }

                $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding');
                foreach ($options as $option) {
                    $row = $rolesTable->createRow();
                    $row->project_id = $this->_getParam('project_id');
                    $row->role_name = $option;
                    $row->is_admincreated = 1;
                    $row->save();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }

        $this->renderScript('admin-settings/create-role.tpl');
    }

    //ACTION FOR EDITING THE ROLE PARAMETER NAME
    public function editRoleAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        $project_id = $this->_getParam('project_id');

        if (!($project_id)) {
            die('No identifier specified');
        }

        //FETCH ROLES ACCORDING TO THIS CATEGORY
        $roleParams = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->rolesByProjectIdParams($project_id);

        $this->view->options = array();
        $this->view->totalOptions = 1;

        //GENERATE A FORM
        $form = $this->view->form = new Sitecrowdfunding_Form_Admin_Settings_EditRole();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
        $form->setField($roleParams);

        //CHECK ROLES
        $options = (array) $this->_getParam('optionsArray');
        $options = array_filter(array_map('trim', $options));
        $options = array_slice($options, 0, 100);
        $this->view->options = $options;
        $this->view->totalOptions = Count($options);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                foreach ($values as $key => $value) {
                    if ($key != 'options' && $key != 'dummy_text') {
                        $role_id = explode('role_name_', $key);

                        if (!empty($role_id)) {
                            $role = Engine_Api::_()->getItem('sitecrowdfunding_roles', $role_id[1]);

                            if (!empty($role)) {
                                $role->role_name = $value;
                                $role->save();
                            }
                        }
                    }
                }

                foreach ($options as $index => $option) {
                    $row = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->createRow();
                    $row->project_id = $project_id;
                    $row->role_name = $option;
                    $row->is_admincreated = 1;
                    $row->save();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Roles has been edited successfully.')
            ));
        }

        $this->renderScript('admin-settings/edit-role.tpl');
    }

    //ACTION FOR DELETING THE ROLE PARAMETERS
    public function deleteRoleAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        $project_id = $this->_getParam('project_id');

        if (!($project_id)) {
            die('No identifier specified');
        }

        //GENERATE FORM
        $form = $this->view->form = new Sitecrowdfunding_Form_Admin_Settings_DeleteRole();

        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            foreach ($values as $key => $value) {
                if ($value == 1) {
                    $role_id = explode('role_name_', $key);
                    $role = Engine_Api::_()->getItem('sitecrowdfunding_roles', $role_id[1]);

                    Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->delete(array('role_id = ?' => $role_id[1], 'is_admincreated =? ' => 1));

                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        $role->delete();
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Roles has been deleted successfully.')
            ));
        }
        $this->renderScript('admin-settings/delete-role.tpl');
    }

}
