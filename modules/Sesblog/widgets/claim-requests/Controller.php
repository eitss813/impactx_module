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

class Sesblog_Widget_ClaimRequestsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!$viewer->getIdentity())
    return $this->setNoRender();

		$page = isset($_POST['page']) ? $_POST['page'] : 1;
		$paginator = Engine_Api::_()->getDbtable('claims', 'sesblog')->getblogclaimsPaginator();
		$this->view->paginator = $paginator;
		// Set item count per page and current page number
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
  }

}
