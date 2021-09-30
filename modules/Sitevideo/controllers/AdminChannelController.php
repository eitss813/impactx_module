<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminChannelController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminChannelController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_manage');
        $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_submain', array(), 'sitevideo_admin_submain_channel_tab');
    }

    //ACTION FOR CHANNEL SUGGESTION DROP-DOWN
    public function getChannelAction() {
        $title = $this->_getParam('text', null);
        $limit = $this->_getParam('limit', 40);
        $featured = $this->_getParam('featured', 0);
        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelName = $channelTable->info('name');
        $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
        $allowName = $allowTable->info('name');
        $data = array();
        $select = $channelTable->select()
                ->setIntegrityCheck(false)
                ->from($channelName)
                ->join($allowName, $channelName . '.channel_id = ' . $allowName . '.resource_id', array('resource_type', 'role'))
                ->where($channelName . '.search = ?', true)
                ->where("lower($channelName.title)  LIKE ? ", '%' . strtolower($title) . '%')
                ->where($allowName . '.resource_type = ?', 'sitevideo_channel')
                ->where($allowName . '.role = ?', 'registered')
                ->where($allowName . '.action = ?', 'view')
                ->limit($limit)
                ->order($channelName . '.creation_date DESC');

        if (!empty($featured))
            $select->where($channelName . ".featured = ?", 0);

        $channels = $channelTable->fetchAll($select);
        foreach ($channels as $channel) {
            $content_video = $this->view->itemPhoto($channel, 'thumb.normal');
            $data[] = array(
                'id' => $channel->channel_id,
                'label' => $channel->title,
                'video' => $content_video
            );
        }
        return $this->_helper->json($data);
    }

    public function featuredAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_featured');
        $this->view->navigationGeneral = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_featured', array(), 'sitevideo_admin_main_featured_channel');
        $table = Engine_Api::_()->getItemTable('sitevideo_channel');
        $select = $table->select()
                ->where("featured = ?", 1)
                ->order('creation_date DESC');
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
        $form->setTitle('Add a Channel as Featured')
                ->setDescription('Using the auto-suggest field below, choose the channel to be made featured. NOTE: Below you will not be able to add those Channels as "Featured" whose privacy is not set to "Everyone" or "All Registered Members" as they cannot be highlighted to users.');
        if ($id) {
            $channel = Engine_Api::_()->getItem('sitevideo_channel', $id);
            if ($channel) {
                $formValue = array('title' => $channel->getTitle(), 'resource_id' => $id);
                $tableOtherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitevideo');
                $row = $tableOtherinfo->getOtherinfo($channel->channel_id);
                if ($row) {
                    $form->setTitle('Edit an Channel as Featured');
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

                $channel = Engine_Api::_()->getItem('sitevideo_channel', $values['resource_id']);
                if (!$channel->featured)
                    $channel->featured = !$channel->featured;
                $channel->save();
                $tableOtherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitevideo');
                $row = $tableOtherinfo->getOtherinfo($channel->channel_id);
                if (empty($row)) {
                    Engine_Api::_()->getDbTable('otherinfo', 'sitevideo')->insert(array(
                        'channel_id' => $channel->channel_id,
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
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('The make featured channel has been added successfully.'))
            ));
        }
    }

    public function removeFeaturedAction() {

        $this->view->id = $this->_getParam('id');
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $channel = Engine_Api::_()->getItem('sitevideo_channel', $this->_getParam('id'));
                $channel->featured = !$channel->featured;
                $channel->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => 10,
                        'parentRefresh' => 10,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
        $this->renderScript('admin-channel/un-featured.tpl');
    }

    //ACTION FOR UPDATE ORDER  OF CHANNELS WIDGTS TAB
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
        $this->_redirect('admin/sitevideo/channel');
    }

    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');

        $categoriesTable = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo');
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
