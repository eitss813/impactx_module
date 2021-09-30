<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_PlaylistViewController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        // CHECKING FOR THE SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_playlist')) {
            return $this->setNoRender();
        }
        
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            return $this->setNoRender();
        }
        
        //FIND THE PLAYLIST MODEL
        $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject();
        $this->view->edit = $playlist->canEdit();
        $viewer = Engine_Api::_()->user()->getViewer();
        //FIND ALL THE REQUEST PARAMETER
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        
        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;

        //ASSIGNING THE PARAMETER 
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        $sitevideoPlaylist = Zend_Registry::isRegistered('sitevideoPlaylist') ? Zend_Registry::get('sitevideoPlaylist') : null;
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->is_ajax = $this->_getParam('isajax', '');
        $this->view->orderBy = $params['orderBy'] = $this->_getParam('orderBy');
        $this->view->params = $params;
        $params['paginator'] = 1;
        $this->view->id = $params['id'] = $this->_getParam('identity');
        // if this is sending a message id, the user is being directed from a coversation
        // check if member is part of the conversation
        $message_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('message');
        $message_view = false;
        if(empty($sitevideoPlaylist))
            return $this->setNoRender();
        
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
                $message_view = true;
            }
        }

        $this->view->message_view = $message_view;
        
        if ($playlist->getOwner()->getIdentity() != $viewer->getIdentity()) {
            $playlist->view_count++;
            $playlist->save();
        }
        //FIND ALL THE PLAYLIST MAP RECORDS
        $paginator = $this->view->paginator = $playlist->getPlaylistMap(array('limit' => $params['itemCountPerPage'], 'orderby' => $params['orderBy']));
        //FIND THE TOTAL RECORD COUNT
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET NO. OF VIDEOS PER PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
        $this->view->totalPlaylists = $paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalPlaylists) / $params['itemCountPerPage']);
        $this->view->viewer_id = $viewer->getIdentity();
    }

}
