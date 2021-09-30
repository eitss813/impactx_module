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
class Sesblog_Widget_tagCloudCategoryController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
 
    $countItem = $this->_getParam('itemCountPerPage', '25');
    $this->view->showType = $this->_getParam('showType', 'simple');
    $this->view->height =  $this->_getParam('height', '300');
    $this->view->color =  $this->_getParam('color', '#00f');
    $this->view->textHeight =  $this->_getParam('text_height', '15');
    $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sesblog');
		$paginator = $categoriesTable->getCloudCategory(array('paginator'=>true,'column_name'=>'*'));
   
    $this->view->storage = Engine_Api::_()->storage();
    $this->view->paginator = $paginator;
    $paginator->setItemCountPerPage($countItem);
    $paginator->setCurrentPageNumber(1);
    if( $paginator->getTotalItemCount() <= 0 )
    return $this->setNoRender();
  }
}
