<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Widget_PagesSlideshowController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();

        // //GET SLIDESHOW HIGHT
        $this->view->height = $params['height'] = $this->_getParam('height', 350);

        //GET SLIDESHOW DELAY
        $this->view->delay = $params['delay'] = $this->_getParam('delay', 3500);

        // GET TRUNCATION LIMIT
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 50);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 100);

        $this->view->fullWidth = $params['fullWidth'] = $this->_getParam('fullWidth', 1);

        $params['limit'] = $this->_getParam('slidesLimit', 10);

        $params['popularType'] = $this->_getParam('popularType', 'random');

        $params['interval'] = $interval = $this->_getParam('interval', 'overall');

        $this->view->showNavigationButton = $params['showNavigationButton'] = $this->_getParam('showNavigationButton', 1);

//        switch ($params['popularType']) {
//            case 'comment':
//                $params['orderby'] = 'comment_count';
//                break;
//            case 'like':
//                $params['orderby'] = 'like_count';
//                break;
//            case 'start_date':
//                $params['orderby'] = 'start_date';
//                break;
//            case 'modified':
//                $params['orderby'] = 'modified_date';
//                break;
//            case 'random':
//                $params['orderby'] = 'random';
//                break;
//        }

        $this->view->params = $params;

        $columnsArray = array('page_id', 'title', 'page_url', 'body' ,'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count','show_name');

        $this->view->paginator = $paginator =  Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('recent', $params, null, null, $columnsArray);
        $this->view->totalCount = count($paginator);
        // Do not render if nothing to show
        if ((count($paginator) <= 0)) {
            return $this->setNoRender();
        }

        $this->view->storage = Engine_Api::_()->storage();

    }

}
