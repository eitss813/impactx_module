<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageVideoController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminManageVideoController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $this->view->contentType = $contentType = $this->_getParam('contentType', 'All');
        $this->view->contentModule = $contentModule = $this->_getParam('contentModule', 'sitevideo');

        //GET NAVIGATION
        if ($contentModule == 'sitereview') {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_managevideo');
        } elseif ($contentModule == 'siteevent') {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_managevideo');
        } elseif ($contentModule == 'sitevideo') {
            $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_video_manage');
        } else {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_managevideo');
        }

        //
        //MAKE FORM
        $this->view->formFilter = $formFilter = new Sitevideo_Form_Admin_Manage_Filter();

        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoTableName = $videoTable->info('name');

        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET CATEGORY TABLE
        $tableCategoryName = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->info('name');
        $tableChannelName = Engine_Api::_()->getDbtable('channels', 'sitevideo')->info('name');

        $select = $videoTable->select();
        //MAKE QUERY
        $select = $videoTable->select()
                ->setIntegrityCheck(false)
                ->from($videoTableName)
                ->joinLeft($tableUserName, "$videoTableName.owner_id = $tableUserName.user_id", 'username')
                ->joinLeft($tableChannelName, "$videoTableName.main_channel_id = $tableChannelName.channel_id", array('channel_title' => "$tableChannelName.title"))
                ->joinLeft($tableCategoryName, "$videoTableName.category_id = $tableCategoryName.category_id", 'category_name')
                ->group("$videoTableName.video_id");

        if ($contentType && $contentType != 'All' && $contentModule == 'sitereview' && !isset($_POST['search'])) {
            if (strpos($contentType, "sitereview_listing") !== false) {
                $explodedArray = explode("_", $contentType);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $videoTableName . '.parent_id', array(""));
                $select->where($videoTableName . ".parent_type =?", 'sitereview_listing_' . $explodedArray[2]);
                // ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            }
        } elseif ($contentType && $contentType != 'All' && !isset($_POST['search'])) {
            if ($contentType == 'user') {
                $select->where($videoTableName . '.parent_type is null');
            } else {
                $select->where($videoTableName . '.parent_type = ? ', $contentType);
            }
        }

        // searching
        $this->view->owner = '';
        $this->view->title = '';
        $this->view->videobrowse = '';
        $this->view->videotype = '';
        $this->view->category_id = '';
        $this->view->subcategory_id = '';
        $this->view->subsubcategory_id = '';
        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');
        if (!empty($_POST['title'])) {
            $this->view->title = $_POST['title'];
            $select->where($videoTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
        }

        if (!empty($_POST['owner'])) {
            $owner = $this->view->owner = $_POST['owner'];
            $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
        }
        if (!empty($_POST['videotype'])) {
            $this->view->videotype = $_POST['videotype'];
            if ($this->view->videotype == 'embedcode') {
                $select->where($videoTableName . '.type in (?) ', array('embedcode','instagram','twitter','pinterest'));
            } else {
                $select->where($videoTableName . '.type = ? ', $_POST['videotype']);
            }
        }
        if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
            $this->view->category_id = $_POST['category_id'];
            $select->where($videoTableName . '.category_id = ? ', $_POST['category_id']);
        } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
            $this->view->category_id = $_POST['category_id'];
            $this->view->subcategory_id = $_POST['subcategory_id'];
            $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;

            $select->where($videoTableName . '.category_id = ? ', $_POST['category_id'])
                    ->where($videoTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
        } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
            $this->view->category_id = $_POST['category_id'];
            $this->view->subcategory_id = $_POST['subcategory_id'];
            $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
            $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
            $this->view->subsubcategory_name = $tableCategory->getCategory($this->view->subsubcategory_id)->category_name;

            $select->where($videoTableName . '.category_id = ? ', $_POST['category_id'])
                    ->where($videoTableName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                    ->where($videoTableName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
        }

        if (!empty($_POST['videobrowse'])) {
            $this->view->videobrowse = $_POST['videobrowse'];
            if ($_POST['videobrowse'] == 1) {
                $select->order($videoTableName . '.video_id DESC');
            } elseif ($_POST['videobrowse'] == 2) {
                $select->order($videoTableName . '.view_count DESC');
            } elseif ($_POST['videobrowse'] == 3) {
                $select->order($videoTableName . '.like_count DESC');
            } elseif ($_POST['videobrowse'] == 4) {
                $select->order($videoTableName . '.comment_count DESC');
            } elseif ($_POST['videobrowse'] == 5) {
                $select->order($videoTableName . '.rating DESC');
            }
        }

        if (isset($_POST['contentType']) && !empty($_POST['contentType']) && $_POST['contentType'] != 'All') {
            $this->view->contentType = $_POST['contentType'];
            if (strpos($_POST['contentType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $_POST['contentType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $videoTableName . '.parent_id', array(""));
                $select->where($videoTableName . ".parent_type =?", 'sitereview_listing_' . $explodedArray[2]);
                // ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                if ($_POST['contentType'] == 'user') {
                    $select->where($videoTableName . '.parent_type is null');
                } else {
                    $select->where($videoTableName . '.parent_type = ? ', $_POST['contentType']);
                }
            }
        }

        $values = array_merge(array('order' => 'video_id', 'order_direction' => 'DESC'), $values);

        $select->order((!empty($values['order']) ? $values['order'] : 'video_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        $this->view->assign($values);

        $valuesCopy = array_filter($values);

        // MAKE PAGINATOR
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setItemCountPerPage(100);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->formValues = $valuesCopy;
    }

    //ACTION FOR MULTI-DELETE VIDEOS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $video = Engine_Api::_()->getItem('sitevideo_video', (int) $value);
                    Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function deleteAction() {

        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->video_id = $this->_getParam('id');
        $channelId = $this->_getParam('channel_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = $viewer->getIdentity();
        // Check post
        if ($this->getRequest()->isPost()) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $video = Engine_Api::_()->getItem('sitevideo_video', $this->_getParam('id'));
                Engine_Api::_()->getApi('core', 'sitevideo')->deleteVideo($video);
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
        $this->renderScript('admin-manage-video/delete.tpl');
    }

    public function sponsoredAction() {

        $video_id = $this->_getParam('id');
        if ($video_id) {
            $video = Engine_Api::_()->getItem('sitevideo_video', $video_id);
            $video->sponsored = !$video->sponsored;
            $video->save();
        }
        $this->_redirect('admin/sitevideo/manage-video');
    }

}
