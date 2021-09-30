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
class Sitevideo_Widget_BrowsePlaylistController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            return $this->setNoRender();
        }
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        //FIND THE PAGE NUMBER
        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;
        if (isset($params['viewFormat']) && !empty($params['viewFormat'])) {
            $this->view->viewFormat = $params['viewFormat'];
        } else
            $this->view->viewFormat = $params['viewFormat'] = $this->_getParam('viewFormat', 'gridView');

        // ASSIGNING THE PARAMETER
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->viewType = $this->_getParam('viewType', array('gridView', 'listView'));
        $this->view->playlistGridViewWidth = $params['playlistGridViewWidth'] = $this->_getParam('playlistGridViewWidth', 150);
        $this->view->playlistGridViewHeight = $params['playlistGridViewHeight'] = $this->_getParam('playlistGridViewHeight', 150);
        $sitevideoBrowsePlaylist = Zend_Registry::isRegistered('sitevideoBrowsePlaylist') ? Zend_Registry::get('sitevideoBrowsePlaylist') : null;
        $this->view->playlistOption = $params['playlistOption'] = $this->_getParam('playlistOption', array('owner', 'videosCount', 'view', 'like'));
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/browse-playlist';
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->videoShowLinkCount = 3;
        $this->view->playlistVideoOrder = 'creation_date';
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
        $this->view->id = $params['id'] = $this->_getParam('identity');
        $this->view->showCreatePlaylistLink = false;
        $params['browsePrivacy'] = 'public';
        $this->view->message = "Nobody has created a playlist yet.";
        if ((isset($params['search']) && !empty($params['search'])) || (isset($params['video_title']) && !empty($params['video_title'])) || (isset($params['membername']) && !empty($params['membername'])))
            $this->view->message = "Nobody has created a playlist with that criteria.";
        
        $element = $this->getElement();
        $widgetTitle = $this->view->heading = $element->getTitle();
        if (!empty($widgetTitle)) {
            $element->setTitle("");
        } else {
            $this->view->heading = "";
        }
        $this->view->params = $params;
        //FIND THE PLAYLIST MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('playlists', 'sitevideo')->getPlaylistPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET THE NO. OF RECORDS PER PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
        if(empty($sitevideoBrowsePlaylist))
            return $this->setNoRender();
        $this->view->totalPlaylists = $paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalPlaylists) / $params['itemCountPerPage']);
    }

}
