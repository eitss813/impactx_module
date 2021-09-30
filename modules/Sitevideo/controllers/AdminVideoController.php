<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminVideoController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminVideoController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_manage');
        $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_submain', array(), 'sitevideo_admin_submain_video_tab');
//    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitevideo', 'type' => 'videos'));
    }

    public function videoOfDayAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_manage');
        $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_submain', array(), 'sitevideo_admin_submain_video_day');
        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitevideo_Form_Admin_Filter();
        $page = $this->_getParam('page', 1);

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }
        $values = array_merge(array(
            'order' => 'start_date',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        $this->view->videoOfDaysList = $videoOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitevideo')->getVideoOfDayList($values);
        $videoOfDay->setItemCountPerPage(50);
        $videoOfDay->setCurrentPageNumber($page);
    }

    //ACTION FOR ADDING VIDEO OF THE DAY
    public function addVideoOfDayAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');
        $viewer = Engine_Api::_()->user()->getViewer();
        //FORM GENERATION
        $form = $this->view->form = new Sitevideo_Form_Admin_ItemOfDayday();
        $form->setTitle('Add a Video of the Day')
                ->setDescription('Select a start date and end date below and the corresponding Video Title from the auto-suggest Video Title field. The selected Video will be displayed as "Video of the Day" for this duration and if more than one videos are found to be displayed in the same duration then they will be dispalyed randomly one at a time. NOTE: Below you will not be able to add those Videos as "Video of the Day" for which the  privacy of the Channels they belong to is not set to "Everyone" or "All Registered Members" as they cannot be highlighted to users.');
        $form->getElement('title')->setLabel('Video');

        //CHECK POST
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitevideo');
                $row = $table->getItem('sitevideo_video', $values["resource_id"]);
                if (empty($row)) {
                    $row = $table->createRow();
                }
                $values = array_merge($values, array('resource_type' => 'sitevideo_video'));

                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $start = strtotime($values['start_date']);
                $end = strtotime($values['end_date']);
                date_default_timezone_set($oldTz);
                $values['start_date'] = date('Y-m-d H:i:s', $start);
                $values['end_date'] = date('Y-m-d H:i:s', $end);

                if ($values['start_date'] > $values['end_date'])
                    $values['end_date'] = $values['start_date'];

                $row->setFromArray($values);
                $row->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => 10,
                        'parentRefresh' => 10,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Page of the Day has been added successfully.'))
            ));
        }
    }

    //ACTION FOR VIDEO SUGGESTION DROP-DOWN
    public function getVideoAction() {
        $title = $this->_getParam('text', null);
        $limit = $this->_getParam('limit', 40);
        $featured = $this->_getParam('featured', 0);
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoName = $videoTable->info('name');
        $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
        $allowName = $allowTable->info('name');
        $data = array();
        $select = $videoTable->select()
                ->setIntegrityCheck(false)
                ->from($videoName);

        $select->join($allowName, $videoName . '.video_id = ' . $allowName . '.resource_id', array('resource_type', 'role'))
                ->where($allowName . '.resource_type = ?', 'video')
                ->where($allowName . '.role = ?', 'registered')
                ->where($allowName . '.action = ?', 'view');
        $select->where("$videoName.search = ?", true)
                ->where("lower($videoName.title)  LIKE ? ", '%' . strtolower($title) . '%')
                ->limit($limit)
                ->order($videoName . '.title')
                ->order($videoName . '.creation_date');

        if (!empty($featured))
            $select->where($videoName . ".featured = ?", 0);

        $videos = $videoTable->fetchAll($select);

        foreach ($videos as $video) {
            $content_video = $this->view->itemPhoto($video, 'thumb.normal');
            $data[] = array(
                'id' => $video->video_id,
                'label' => $video->title,
                'video' => $content_video
            );
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR DELETE VIDEO OF DAY
    public function deleteVideoOfDayAction() {
        $this->view->id = $this->_getParam('id');
        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getDbtable('itemofthedays', 'sitevideo')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => 10,
                        'parentRefresh' => 10,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
        $this->renderScript('admin-video/delete.tpl');
    }

    //ACTION FOR MULTI DELETE VIDEO ENTRIES
    public function multiDeleteVideoAction() {
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {

                    $sitepageitemofthedays = Engine_Api::_()->getItem('sitevideo_itemofthedays', (int) $value);
                    if (!empty($sitepageitemofthedays)) {
                        $sitepageitemofthedays->delete();
                    }
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'video-of-day'));
    }

    public function featuredAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_featured');
        $this->view->navigationGeneral = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_featured', array(), 'sitevideo_admin_main_featured_video');
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelName = $channelTable->info('name');
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoName = $videoTable->info('name');
        $data = array();
        $select = $videoTable->select()
                ->setIntegrityCheck(false)
                ->from($videoName);

        $select->where($videoName . ".featured = ?", 1)
                ->order($videoName . '.creation_date DESC');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        // Set item count per page and current page number
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
    }

    public function addFeaturedAction() {
        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        $id = $this->_getParam('id', null);
        //FORM GENERATION
        $form = $this->view->form = new Sitevideo_Form_Admin_FeaturedChannel();
        $form->setTitle('Add a Video as Featured')
                ->setDescription('Using the auto-suggest field below, choose the Video to be made as featured. NOTE: You will not be able to add those Videos as "Featured" for which the privacy of their Channels is not set to "Everyone" or "All Registered Members" as they are not visible to all members');
        $form->getElement('title')->setLabel('Video Title (Use this auto-suggest video title box to select the video that you want to make featured.)');
        $form->getElement('url')->setLabel('URL [Enter the URL where you want to redirect the users upon clicking on this featured video.]');
        if ($id) {
            $video = Engine_Api::_()->getItem('sitevideo_video', $id);
            if ($video) {
                $formValue = array('title' => $video->getTitle(), 'resource_id' => $id);
                $tableOtherinfo = Engine_Api::_()->getDbtable('videootherinfo', 'sitevideo');
                $row = $tableOtherinfo->getOtherinfo($video->video_id);
                if ($row) {
                    $form->setTitle('Edit an Video as Featured');
                    $formValue = array_merge($formValue, $row->toArray());
                }
                $form->populate($formValue);
                $form->getElement('title')->setAttrib('readonly', 'readonly');
            }
        }
        //CHECK POST
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $video = Engine_Api::_()->getItem('sitevideo_video', $values['resource_id']);
                if (!$video->featured)
                    $video->featured = !$video->featured;
                $video->save();
                $tableOtherinfo = Engine_Api::_()->getDbtable('videootherinfo', 'sitevideo');
                $row = $tableOtherinfo->getOtherinfo($video->video_id);
                if (empty($row)) {
                    Engine_Api::_()->getDbTable('videootherinfo', 'sitevideo')->insert(array(
                        'video_id' => $video->video_id,
                        'tagline1' => $values['tagline1'],
                        'tagline2' => $values['tagline2'],
                        'tagline_description' => $values['tagline_description'],
                        'url' => $values['url']
                    ));
                } else {
                    $row->tagline1 = $values['tagline1'];
                    $row->tagline2 = $values['tagline2'];
                    $row->tagline_description = $values['tagline_description'];
                    $row->url = $values['url'];
                    $row->save();
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => 10,
                        'parentRefresh' => 10,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured video has been added successfully.'))
            ));
        }
    }

    public function removeFeaturedAction() {

        $this->view->id = $this->_getParam('id');
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $video = Engine_Api::_()->getItem('sitevideo_video', $this->_getParam('id'));
                $video->featured = !$video->featured;
                $video->save();
                $tableOtherinfo = Engine_Api::_()->getDbtable('videootherinfo', 'sitevideo');
                $row = $tableOtherinfo->getOtherinfo($video->video_id);
                if ($row) {
                    $row->delete();
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
        $this->renderScript('admin-video/un-featured.tpl');
    }

// ACTION FOR CHANGE SETTINGS OF TABBED CHANNEL WIDZET TAB
    public function editTabAction() {
        //FORM GENERATION
        $this->view->form = $form = new Sitevideo_Form_Admin_EditTab();
        $id = $this->_getParam('tab_id');
        $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $values = $tab->toarray();
            $form->populate($values);
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $tab->setFromArray($values);
            $tab->save();
            $db->commit();
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => 10,
                        'parentRefresh' => 10,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR UPDATE ORDER  OF VIDEOS WIDGTS TAB
    public function updateOrderAction() {
        //CHECK POST
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $values = $_POST;
            try {
                foreach ($values['order'] as $key => $value) {
                    $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
                    if (!empty($tab)) {
                        $tab->order = $key + 1;
                        $tab->save();
                    }
                }
                $db->commit();
                $this->_helper->redirector->gotoRoute(array('action' => 'index'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    //ACTION FOR MAKE TAB ENABLE/DISABLE
    public function enabledAction() {
        $id = $this->_getParam('tab_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
        try {
            $tab->enabled = !$tab->enabled;
            $tab->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');

        $categoriesTable = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo');
        $select = $categoriesTable->select()
                ->from($categoriesTable->info('name'), array('category_id', 'category_name'))
                ->where("$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $category->category_name;
                $data['category_id'] = $category->category_id;
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

}
