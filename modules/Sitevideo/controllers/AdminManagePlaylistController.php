<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManagePlaylistController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminManagePlaylistController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_playlist_manage');

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

        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
        $playlistTableName = $playlistTable->info('name');

        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
        $select = $playlistTable->select();
        //MAKE QUERY
        $select = $playlistTable->select()
                ->setIntegrityCheck(false)
                ->from($playlistTableName)
                ->joinLeft($tableUserName, "$playlistTableName.owner_id = $tableUserName.user_id", 'username')
                ->group("$playlistTableName.playlist_id");

        // searching
        $this->view->owner = '';
        $this->view->title = '';
        $this->view->date = '';

        if (!empty($_POST['title'])) {
            $this->view->title = $_POST['title'];
            $select->where($playlistTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
        }

        if (!empty($_POST['owner'])) {
            $owner = $this->view->owner = $_POST['owner'];
            $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
        }
        if (!empty($_POST['starttime']) && !empty($_POST['starttime']['date'])) {
            $creationDate = $this->view->starttime = $_POST['starttime']['date'];
            $select->where("date($playlistTableName.creation_date)='$creationDate'");
        }

        $values = array_merge(array('order' => 'playlist_id', 'order_direction' => 'DESC'), $values);

        $select->order((!empty($values['order']) ? $values['order'] : 'playlist_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

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
                    $playlist = Engine_Api::_()->getItem('sitevideo_playlist', (int) $value);
                    // Delete all the mapped video with this playlist
                    foreach ($playlist->getPlaylistAllMap() as $map)
                        $map->delete();

                    //Delete Playlist
                    $playlist->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->video_id = $this->_getParam('id');
        // Check post
        if ($this->getRequest()->isPost()) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $playlist = Engine_Api::_()->getItem('sitevideo_playlist', $this->_getParam('id'));
                // Delete all the mapped video with this playlist
                foreach ($playlist->getPlaylistAllMap() as $map)
                    $map->delete();

                //Delete Playlist
                $playlist->delete();
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
        $this->renderScript('admin-manage-playlist/delete.tpl');
    }

}
