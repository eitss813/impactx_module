<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminManageController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_channel_manage');

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

        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelTableName = $channelTable->info('name');

        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET CATEGORY TABLE
        $tableCategoryName = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->info('name');

        $select = $channelTable->select();
        //MAKE QUERY
        $select = $channelTable->select()
                ->setIntegrityCheck(false)
                ->from($channelTableName)
                ->joinLeft($tableUserName, "$channelTableName.owner_id = $tableUserName.user_id", 'username')
                ->joinLeft($tableCategoryName, "$channelTableName.category_id = $tableCategoryName.category_id", 'category_name')
                ->group("$channelTableName.channel_id");

        // searching
        $this->view->owner = '';
        $this->view->title = '';
        $this->view->channelbrowse = '';
        $this->view->category_id = '';
        $this->view->subcategory_id = '';
        $this->view->subsubcategory_id = '';
        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        if (!empty($_POST['title'])) {
            $this->view->title = $_POST['title'];
            $select->where($channelTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
        }

        if (!empty($_POST['owner'])) {
            $owner = $this->view->owner = $_POST['owner'];
            $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
        }

        if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
            $this->view->category_id = $_POST['category_id'];
            $select->where($channelTableName . '.category_id = ? ', $_POST['category_id']);
        } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
            $this->view->category_id = $_POST['category_id'];
            $this->view->subcategory_id = $_POST['subcategory_id'];
            $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;

            $select->where($channelTableName . '.category_id = ? ', $_POST['category_id'])
                    ->where($channelTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
        } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
            $this->view->category_id = $_POST['category_id'];
            $this->view->subcategory_id = $_POST['subcategory_id'];
            $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
            $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
            $this->view->subsubcategory_name = $tableCategory->getCategory($this->view->subsubcategory_id)->category_name;

            $select->where($channelTableName . '.category_id = ? ', $_POST['category_id'])
                    ->where($channelTableName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                    ->where($channelTableName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
        }

        if (!empty($_POST['channelbrowse'])) {
            $this->view->channelbrowse = $_POST['channelbrowse'];
            if ($_POST['channelbrowse'] == 1) {
                $select->order($channelTableName . '.channel_id DESC');
            } elseif ($_POST['channelbrowse'] == 2) {
                $select->order($channelTableName . '.subscribe_count DESC');
            } elseif ($_POST['channelbrowse'] == 3) {
                $select->order($channelTableName . '.like_count DESC');
            } elseif ($_POST['channelbrowse'] == 4) {
                $select->order($channelTableName . '.comment_count DESC');
            } elseif ($_POST['channelbrowse'] == 5) {
                $select->order($channelTableName . '.rating DESC');
            }
        }

        $values = array_merge(array('order' => 'channel_id', 'order_direction' => 'DESC'), $values);

        $select->order((!empty($values['order']) ? $values['order'] : 'channel_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        $this->view->assign($values);

        $valuesCopy = array_filter($values);

        // MAKE PAGINATOR
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setItemCountPerPage(100);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->formValues = $valuesCopy;
    }

    //ACTION FOR MULTI-DELETE CHANNELS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getItem('sitevideo_channel', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function deleteAction() {

        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->channel_id = $this->_getParam('id');
        // Check post
        if ($this->getRequest()->isPost()) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $channel = Engine_Api::_()->getItem('sitevideo_channel', $this->_getParam('id'));
                Engine_Api::_()->getApi('core', 'sitevideo')->deleteChannel($channel);
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
        $this->renderScript('admin-manage/delete.tpl');
    }

    public function sponsoredAction() {

        $channel_id = $this->_getParam('channel_id');
        if ($channel_id) {
            $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
            $channel->sponsored = !$channel->sponsored;
            $channel->save();
        }
        $this->_redirect('admin/sitevideo/manage');
    }

}
