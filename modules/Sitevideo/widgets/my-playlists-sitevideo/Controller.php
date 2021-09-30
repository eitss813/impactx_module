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
class Sitevideo_Widget_MyPlaylistsSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            return $this->setNoRender();
        }

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

//FIND THE PAGE NUMBER
        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;

        // ASSIGNING THE PARAMETER
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->videoShowLinkCount = $this->_getParam('videoShowLinkCount', 3);
        $this->view->playlistOrder = $params['playlistOrder'] = $this->_getParam('playlistOrder');
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        $this->view->playlistVideoOrder = $params['playlistVideoOrder'] = $this->_getParam('playlistVideoOrder');
        $this->view->viewFormat = $params['viewFormat'] = $this->_getParam('viewFormat', 'gridView');
        $this->view->playlistGridViewWidth = $params['playlistGridViewWidth'] = $this->_getParam('playlistGridViewWidth', 150);
        $this->view->playlistGridViewHeight = $params['playlistGridViewHeight'] = $this->_getParam('playlistGridViewHeight', 150);
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/my-playlists-sitevideo';
        $this->view->playlistOption = array('like', 'videosCount');
        $this->view->showCreatePlaylistLink = true;
        $this->view->viewer = $params['owner'] = Engine_Api::_()->user()->getViewer();
        $params['owner_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->params = $params;
        $params['paginator'] = 1;
        $this->view->message = "You do not have any playlists yet.";

        if(empty($sitevideoVideosList))
            return $this->setNoRender();
        
        if (isset($params['search']) && !empty($params['search']))
            $this->view->message = "You do not have created a playlist with that criteria.";

        //FIND THE PLAYLIST MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('playlists', 'sitevideo')->getPlaylistPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET THE NO. OF RECORDS PER PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
        $this->view->totalPlaylists = $paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalPlaylists) / $params['itemCountPerPage']);
    }

}
