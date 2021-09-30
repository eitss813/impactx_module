<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Controller.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Widget_PopularFeaturedVerifiedReviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (isset($_POST['params']))
    $params = json_decode($_POST['params'], true);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $identity = $view->identity;
    $this->view->widgetIdentity = $this->_getParam('content_id', $identity);
    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
    if ($this->_getParam('showLimitData', 1))
    $this->view->widgetName = 'popular-featured-verified-reviews';
    $this->view->review_description_truncation = isset($params['review_description_truncation']) ? $params['review_description_truncation'] : $this->_getParam('review_description_truncation', '45');
    $this->view->title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '45');
    $this->view->view_type = isset($params['viewType']) ? $params['viewType'] : $this->_getParam('viewType', 'list');
    $this->view->image_type = isset($params['imageType']) ? $params['imageType'] : $this->_getParam('imageType', 'square');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'title', 'view', 'featuredLabel', 'verifiedLabel', 'likeButton', 'rating', 'description', 'by'));
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;

    $limit = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', 5);
    $value['info'] = isset($params['info']) ? $params['info'] : $this->_getParam('info', 'recently_created');
    $value['order'] = isset($params['order']) ? $params['order'] : $this->_getParam('order', '');
    $value['paginator'] = true;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->params = array('review_description_truncation' => $this->view->review_description_truncation, 'title_truncation' => $this->view->title_truncation, 'viewType' => $this->view->view_type, 'imageType' => $this->view->image_type, 'show_criterias' => $show_criterias, 'limit_data' => $limit, 'info' => $value['info'], 'order' => $value['order']);

    $this->view->results = $paginator = Engine_Api::_()->getDbTable('reviews', 'sesblog')->getBlogReviewSelect($value, array());
    $paginator->setItemCountPerPage($limit);
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    if ($paginator->getTotalItemCount() <= 0)
      return $this->setNoRender();

    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
  }

}
