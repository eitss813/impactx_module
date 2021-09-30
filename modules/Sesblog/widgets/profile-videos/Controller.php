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
class Sesblog_Widget_ProfileVideosController extends Engine_Content_Widget_Abstract {
  protected $_childCount;
  public function indexAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		if (isset($_POST['params']))
      $params = $_POST['params'];
    
    if (empty($_POST['is_ajax'])) {

      if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesvideo'))
        return $this->setNoRender();
			if (!Engine_Api::_()->core()->hasSubject())
        return $this->setNoRender();
      //Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject->authorization()->isAllowed($viewer, 'view'))
        return $this->setNoRender();
    }else if($is_ajax){
			$subject = Engine_Api::_()->getItem('sesblog_blog', $params['parent_id']);	
		}
		$this->view->allow_create = true;
		if(!$is_ajax && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 1)){
			$package = $subject->getPackage();
			$viewAllowed = $package->getItemModule('video');
			if(!$viewAllowed)
				return $this->setNoRender();
			//allow upload video
			$this->view->allow_create = $allow_create = $package->allowUploadVideo($subject);
		}
    //Default option for widget
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->widgetType = 'tabbed';
    $this->view->parent_id = $parent_id = isset($params['parent_id']) ? $params['parent_id'] : $subject->getIdentity();
    $this->view->parent_type = $parent_type = isset($params['parent_type']) ? $params['parent_type'] : $subject->getType();
    $this->view->height = $defaultHeight = isset($params['height']) ? $params['height'] : $this->_getParam('height', '160px');
    $this->view->width = $defaultWidth = isset($params['width']) ? $params['width'] : $this->_getParam('width', '140px');
    $this->view->limit_data = $limit_data = isset($params['limit_data']) ? $params['limit_data'] : $this->_getParam('limit_data', '10');
    $this->view->limit = ($page - 1) * $limit_data;
    $this->view->title_truncation_list = $title_truncation_list = isset($params['title_truncation_list']) ? $params['title_truncation_list'] : $this->_getParam('title_truncation_list', '100');
    $this->view->title_truncation_grid = $title_truncation_grid = isset($params['title_truncation_grid']) ? $params['title_truncation_grid'] : $this->_getParam('title_truncation_grid', '100');
    $this->view->description_truncation_list = $description_truncation_list = isset($params['description_truncation_list']) ? $params['description_truncation_list'] : $this->_getParam('description_truncation_list', '100');
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->canUpload = $canUpload = Engine_Api::_()->sesblog()->isBlogAdmin($subject, 'create');
		$this->view->can_edit = Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'edit');
    $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'delete');
    $this->view->isBlogAdmin = Engine_Api::_()->sesblog()->checkBlogAdmin($subject);
		
		$this->view->socialshare_enable_plusicon = $socialshare_enable_plusicon =isset($params['socialshare_enable_plusicon']) ? $params['socialshare_enable_plusicon'] : $this->_getParam('socialshare_enable_plusicon', 1);
		$this->view->socialshare_icon_limit = $socialshare_icon_limit =isset($params['socialshare_icon_limit']) ? $params['socialshare_icon_limit'] : $this->_getParam('socialshare_icon_limit', 2);
		
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'featuredLabel', 'sponsoredLabel', 'watchLater', 'category', 'description', 'duration', 'hotLabel', 'favouriteButton', 'playlistAdd', 'likeButton', 'socialSharing', 'view'));
    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;

    if (!$is_ajax) {
      $this->view->optionsEnable = $optionsEnable = $this->_getParam('enableTabs', array('list', 'grid', 'pinboard'));
      $view_type = $this->_getParam('openViewType', 'list');
      if (count($optionsEnable) > 1) {
        $this->view->bothViewEnable = true;
      }
    }
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $view_type));
    $params = $this->view->params = array('height' => $defaultHeight, 'width' => $defaultWidth, 'limit_data' => $limit_data, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type, 'description_truncation_list' => $description_truncation_list, 'title_truncation_list' => $title_truncation_list, 'title_truncation_grid' => $title_truncation_grid, 'parent_id' => $parent_id, 'parent_type' => $parent_type, 'socialshare_enable_plusicon' => $socialshare_enable_plusicon, 'socialshare_icon_limit' => $socialshare_icon_limit);
    $this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
    $this->view->loadJs = true;
    // custom list grid view options
    $options = array('profileTabbed' => true, 'paggindData' => true);
    $this->view->optionsListGrid = $options;
    $this->view->widgetName = 'profile-videos';
    $this->view->showTabType = $this->_getParam('showTabType', '1');
    // initialize type variable type
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getVideo(array( 'parent_id' => $parent_id, 'parent_type' => $parent_type));
    $this->view->paginator = $paginator;
    // Add count to title if configured
    if ($paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $limit_data));
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    if ($is_ajax)
      $this->getElement()->removeDecorator('Container');
		// Do not render if nothing to show and cannot upload
    if ($paginator->getTotalItemCount() <= 0 && !$canUpload) {
      return $this->setNoRender();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
