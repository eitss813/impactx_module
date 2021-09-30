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
class Sesblog_Widget_CategoryAssociateBlogController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $params = array();
    if (isset($_POST['params']))
      $params = json_decode($_POST['params'], true);
    $this->view->loadOptionData = $loadOptionData = isset($params['loadOptionData']) ? $params['loadOptionData'] : $this->_getParam('pagging', 'auto_load');
    $this->view->category_limit = $category_limit = isset($params['category_limit']) ? $params['category_limit'] : $this->_getParam('category_limit', '10');
    $this->view->blog_description_truncation = $blog_description_truncation = isset($params['blog_description_truncation']) ? $params['blog_description_truncation'] : $this->_getParam('blog_description_truncation', '300');
    $this->view->blog_limit = $blog_limit = isset($params['blog_limit']) ? $params['blog_limit'] : $this->_getParam('blog_limit', '8');
    $this->view->count_blog = $count_blog = isset($params['count_blog']) ? $params['count_blog'] : $this->_getParam('count_blog', '1');
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '250px');
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '160px');
    $this->view->seemore_text = $seemore_text = isset($params['seemore_text']) ? $params['seemore_text'] : $this->_getParam('seemore_text', '+ See all [category_name]');
    $this->view->allignment_seeall = $allignment_seeall = isset($params['allignment_seeall']) ? $params['allignment_seeall'] : $this->_getParam('allignment_seeall', 'left');
    $this->view->identityForWidget = $identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->popularity_blog = $popularity_blog = isset($params['popularity_blog']) ? $params['popularity_blog'] : $this->_getParam('popularity_blog', 'like_count');
    $criteriaData = isset($params['criteria']) ? $params['criteria'] : $this->_getParam('criteria', 'alphabetical');
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->widgetName = 'category-associate-blog';
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'view', 'rating', 'ratingStar', 'by', 'title', 'featuredLabel', 'sponsoredLabel', 'favourite', 'creationDate', 'readmore'));
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    if ($popularity_blog == 'featured' || $popularity_blog == 'sponsored') {
      $fixedData = $popularity_blog;
      $popularCol = '';
    } else {
      $fixedData = '';
      $popularCol = $popularity_blog;
    }
    // initialize type variable type
    $this->view->params = $params = array('loadOptionData' => $loadOptionData, 'category_limit' => $category_limit, 'blog_limit' => $blog_limit,'blog_description_truncation' => $blog_description_truncation, 'count_blog' => $count_blog, 'seemore_text' => $seemore_text, 'allignment_seeall' => $allignment_seeall, 'show_criterias' => $show_criterias, 'height' => $height, 'width' => $width, 'criteria' => $criteriaData, 'popularity_blog' => $popularity_blog);
    $blogData = $countArray = array();
    $this->view->paginatorCategory = $paginatorCategory = Engine_Api::_()->getDbTable('categories', 'sesblog')->getCategory(array('hasBlog' => true, 'criteria' => $criteriaData, 'blogDesc' => 'desc'), array('paginator' => 'yes'));
    $paginatorCategory->setItemCountPerPage($category_limit);
    $paginatorCategory->setCurrentPageNumber($page);
    if ($paginatorCategory->getTotalItemCount() > 0) {
      foreach ($paginatorCategory as $key => $valuePaginator) {
        $countArray[] = $valuePaginator->total_blogs_categories;
        $blogData['blog_data'][$valuePaginator->category_id] = Engine_Api::_()->getDbTable('blogs', 'sesblog')->getSesblogsPaginator(array('category_id' => $valuePaginator->category_id, 'status' => 1, 'limit' => $blog_limit, 'popularCol' => $popularCol, 'fixedData' => $fixedData, 'fetchAll' => 0), false);
      }
    } else {
      if (!$is_ajax)
        return $this->setNoRender();
    }
    $this->view->countArray = $countArray;
    $this->view->resultArray = $blogData;
    // Set item count per page and current page number
    $this->view->page = $page;
    $this->view->paginatorCategory = $paginatorCategory;
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    }
  }

}
