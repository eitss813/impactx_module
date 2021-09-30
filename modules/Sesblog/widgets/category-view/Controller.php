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

class Sesblog_Widget_CategoryViewController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
 
    // Default option for tabbed widget
    if (isset($_POST['params']))
      $params = ($_POST['params']);
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->limit_data = $limit_data = isset($params['blog_limit']) ? $params['blog_limit'] : $this->_getParam('blog_limit', '10');
    $this->view->limit = ($page - 1) * $limit_data;
    $this->view->description_truncation = $descriptionLimit = isset($params['description_truncation']) ? $params['description_truncation'] : $this->_getParam('description_truncation', '150');
    $this->view->viewType = isset($params['viewType']) ? $params['viewType'] : $this->_getParam('viewType', 'list');
    $categoryId = isset($params['category_id']) ? $params['category_id'] : '';
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'ratingStar', 'by', 'title', 'featuredLabel', 'sponsoredLabel','favourite','description','creationDate', 'readmore'));
    $this->view->show_subcat = $show_subcat = isset($params['show_subcat']) ? $params['show_subcat'] : $this->_getParam('show_subcat', '1');
		if(is_array($show_criterias)){
			foreach ($show_criterias as $show_criteria)
				$this->view->{$show_criteria . 'Active'} = $show_criteria;
		}
    $show_subcatcriterias = isset($params['show_subcatcriteria']) ? $params['show_subcatcriteria'] : $this->_getParam('show_subcatcriteria', array('countBlog', 'icon', 'title'));
		if(is_array($show_subcatcriterias)){
			foreach ($show_subcatcriterias as $show_subcatcriteria)
				$this->view->{$show_subcatcriteria . 'SubcatActive'} = $show_subcatcriteria;
		}
    $this->view->widthSubcat = $widthSubcat = isset($params['widthSubcat']) ? $params['widthSubcat'] : $this->_getParam('widthSubcat', '250px');
    $this->view->heightSubcat = $heightSubcat = isset($params['heightSubcat']) ? $params['heightSubcat'] : $this->_getParam('heightSubcat', '160px');
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '250px');
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '160px');
		$this->view->textBlog = $textBlog = isset($params['textBlog']) ? $params['textBlog'] : $this->_getParam('textBlog', 'Blogs we love');
    $params = array('viewType' => $this->view->viewType,'blog_limit' => $limit_data, 'description_truncation' => $descriptionLimit, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias,'category_id' => $categoryId, 'width' => $width, 'height' => $height, 'show_subcat' => $show_subcat, 'show_subcatcriteria' => $show_subcatcriterias, 'widthSubcat' => $widthSubcat, 'heightSubcat', $heightSubcat,'textBlog'=>$textBlog);
    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->category = $category = Engine_Api::_()->core()->getSubject();
      $category_id = $category->category_id;
    } else {
      $this->view->category = $category = Engine_Api::_()->getItem('sesblog_category', $params['category_id']);
      $category_id = $params['category_id'];
    }
    $innerCatData = array();
    $columnCategory = 'category_id';
    if (!$is_ajax) {
      if ($category->subcat_id == 0 && $category->subsubcat_id == 0) {
        if($category->category_id)
        $innerCatData = Engine_Api::_()->getDbtable('categories', 'sesblog')->getModuleSubcategory(array('category_id' => $category->category_id, 'column_name' => '*', 'countBlogs' => true));
        $columnCategory = 'category_id';
      } else if ($category->subsubcat_id == 0) {
        $innerCatData = Engine_Api::_()->getDbtable('categories', 'sesblog')->getModuleSubsubcategory(array('countBlogs' => true, 'category_id' => $category->category_id, 'column_name' => '*'));
        $columnCategory = 'subcat_id';
      } else
        $columnCategory = 'subsubcat_id';
      $this->view->innerCatData = $innerCatData;
      //breadcum
      $this->view->breadcrumb = $breadcrumb = Engine_Api::_()->getDbtable('categories', 'sesblog')->getBreadcrumb($category);
    }
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsPaginator(array($columnCategory => $category->category_id, 'status' => 1));
    $paginator->setItemCountPerPage($limit_data);
    $paginator->setCurrentPageNumber($page);
    $this->view->widgetName = 'category-view'; 
    // initialize type variable type
    $this->view->page = $page;
    $params = array_merge($params, array('category_id' => $category_id));
    $this->view->params = $params;
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    } else {
      // Do not render if nothing to show
      if ($paginator->getTotalItemCount() <= 0) {
        
      }
    }
  }

}
