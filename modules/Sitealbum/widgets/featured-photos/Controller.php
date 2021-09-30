<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_FeaturedPhotosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->getElement()->removeDecorator('Title');
        $this->view->defaultDuration = $this->_getParam("speed", 5000);
        $this->view->slideWidth = $this->_getParam("width", null);
        $this->view->slideHeight = $this->_getParam("height", 583);
        $this->view->featuredPhotosHtmlTitle = $this->_getParam("featuredPhotosHtmlTitle", "The community for all your photos.");
        $this->view->featuredPhotosHtmlDescription = $this->_getParam("featuredPhotosHtmlDescription", "Upload, access, organize, edit, and share your photos.");
        $this->view->featuredPhotosSearchBox = $this->_getParam("featuredPhotosSearchBox", 1);
        $this->view->featuredAlbums = $this->_getParam("featuredAlbums", 1);
				$this->view->contentFullWidth = $this->_getParam("contentFullWidth", 0);
				
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'random');
        $this->view->orderby = '';
        switch ($param['popularType']) {
            case 'view':
                $this->view->orderby = $param['orderby'] = 'view_count';
                break;
            case 'comment':
                $this->view->orderby = $param['orderby'] = 'comment_count';
                break;
            case 'like':
                $this->view->orderby = $param['orderby'] = 'like_count';
                break;
            case 'rating':
                $this->view->orderby = $param['orderby'] = 'rating';
                break;
            case 'creation':
                $this->view->orderby = $param['orderby'] = 'creation_date';
                break;
            case 'modified':
                $this->view->orderby = $param['orderby'] = 'modified_date';
                break;
            case 'random':
                $this->view->orderby = $param['orderby'] = 'random';
                break;
        }
        $params = array();
        $params['featured'] = $this->_getParam('featured', 1);
        $params['limit'] = 10;
        $params['orderby'] = 'random';
        $this->view->params = $params;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($params);
        $paginator->setItemCountPerPage($params['limit']);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $params['limit'] = 3;
        $this->view->albumpaginator = $albumpaginator = Engine_Api::_()->getDbTable('albums', 'sitealbum')->albumBySettings($params);
        $albumpaginator->setItemCountPerPage($params['limit']);
        $albumpaginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->albums_count = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getAlbumsCount(array('foruser' => 1));
        $this->view->photos_count = Engine_Api::_()->getDbTable('photos', 'sitealbum')->getPhotosCount();
    }

}
