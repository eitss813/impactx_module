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
class Sesblog_Widget_OtherModulesProfileSesblogsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type',array('recentlySPcreated','mostSPviewed','mostSPliked','mostSPcommented','mostSPrated','mostSPfavourite','featured','sponsored', 'verified', 'week', 'month'));
  
    if (isset($_POST['params']))
    $params = $_POST['params'];
    
    if (isset($_POST['searchParams']) && $_POST['searchParams'])
    parse_str($_POST['searchParams'], $searchArray);
    
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $this->view->view_more = isset($_POST['view_more']) ? true : false;
    
    if(!$is_ajax) {
			if( !Engine_Api::_()->core()->hasSubject() ) {
				return $this->setNoRender();
			}
      // Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
        return $this->setNoRender();
      }
    }
    
    $this->view->identityForWidget = $identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : $this->view->identity;
    $this->view->widgetParams = $widgetParams = Engine_Api::_()->sesblog()->getWidgetParams($identityForWidget);
   
    $defaultOpenTab = array();
    $defaultOptions = $arrayOptions = array();
    if (!$is_ajax && is_array($defaultOptionsArray)) {
      foreach ($defaultOptionsArray as $key => $defaultValue) {
        if ($this->_getParam($defaultValue . '_order'))
          $order = $this->_getParam($defaultValue . '_order');
        else
          $order = (777 + $key);
        if ($this->_getParam($defaultValue.'_label'))
          $valueLabel = $this->_getParam($defaultValue . '_label'). '||' . $defaultValue;
        else {
          if ($defaultValue == 'recentlySPcreated')
            $valueLabel = 'Recently Created'. '||' . $defaultValue;
          else if ($defaultValue == 'mostSPviewed')
            $valueLabel = 'Most Viewed'. '||' . $defaultValue;
          else if ($defaultValue == 'mostSPliked')
            $valueLabel = 'Most Liked'. '||' . $defaultValue;
          else if ($defaultValue == 'mostSPcommented')
            $valueLabel = 'Most Commented'. '||' . $defaultValue;
          else if ($defaultValue == 'mostSPrated')
            $valueLabel = 'Most Rated'. '||' . $defaultValue;
          else if ($defaultValue == 'mostSPfavourite')
            $valueLabel = 'Most Favourite'. '||' . $defaultValue;
          else if ($defaultValue == 'featured')
            $valueLabel = 'Featured'. '||' . $defaultValue;
          else if ($defaultValue == 'sponsored')
            $valueLabel = 'Sponsored'. '||' . $defaultValue;
          else if ($defaultValue == 'verified')
            $valueLabel = 'Verified'. '||' . $defaultValue;
          else if ($defaultValue == 'week')
            $valueLabel = 'This Week'. '||' . $defaultValue;
					else if ($defaultValue == 'month')
            $valueLabel = 'This Month'. '||' . $defaultValue;
        }
        $arrayOptions[$order] = $valueLabel;
      }
      ksort($arrayOptions);
      $counter = 0;
      foreach ($arrayOptions as $key => $valueOption) {
        $key = explode('||', $valueOption);
        if ($counter == 0)
          $this->view->defaultOpenTab = $defaultOpenTab = $key[1];
        $defaultOptions[$key[1]] = $key[0];
        $counter++;
      }
    }
    $this->view->defaultOptions = $defaultOptions;
    
    if (isset($_GET['openTab']) || $is_ajax) {
      $this->view->defaultOpenTab = $defaultOpenTab = (isset($_GET['openTab']) ? str_replace('_', 'SP', $_GET['openTab']) : ($this->_getParam('openTab') != NULL ? $this->_getParam('openTab') : (isset($params['openTab']) ? $params['openTab'] : '' )));
    }

		
		//List 2
		$this->view->title_truncation_simplelist = $title_truncation_simplelist = isset($params['title_truncation_simplelist']) ? $params['title_truncation_simplelist'] : $this->_getParam('title_truncation_simplelist', '100');
		$this->view->width_simplelist = $width_simplelist = isset($params['width_simplelist']) ? $params['width_simplelist'] : $this->_getParam('width_simplelist','140');
    $this->view->height_simplelist = $height_simplelist = isset($params['height_simplelist']) ? $params['height_simplelist'] : $this->_getParam('height_simplelist','160');
		$this->view->description_truncation_simplelist = $description_truncation_simplelist = isset($params['description_truncation_simplelist']) ? $params['description_truncation_simplelist'] : $this->_getParam('description_truncation_simplelist', '100');
		$this->view->limit_data_list2 = $limit_data_list2 = isset($params['limit_data_list2']) ? $params['limit_data_list2'] : $this->_getParam('limit_data_list2', '10');

      
		//List View 3
		$this->view->title_truncation_advlist = $title_truncation_advlist = isset($params['title_truncation_advlist']) ? $params['title_truncation_advlist'] : $this->_getParam('title_truncation_advlist', '100');
		$this->view->description_truncation_advlist= $description_truncation_advlist = isset($params['description_truncation_advlist']) ? $params['description_truncation_advlist'] : $this->_getParam('description_truncation_advlist', '100');
		$this->view->limit_data_list3= $limit_data_list3 = isset($params['limit_data_list3']) ? $params['limit_data_list3'] : $this->_getParam('limit_data_list3', '10');
		
		//Grid 2
		$this->view->title_truncation_advgrid = $title_truncation_advgrid = isset($params['title_truncation_advgrid']) ? $params['title_truncation_advgrid'] : $this->_getParam('title_truncation_advgrid', '100');
		$this->view->width_advgrid = $width_advgrid = isset($params['width_advgrid']) ? $params['width_advgrid'] : $this->_getParam('width_advgrid','140');
    $this->view->height_advgrid = $height_advgrid = isset($params['height_advgrid']) ? $params['height_advgrid'] : $this->_getParam('height_advgrid','160');
		$this->view->description_truncation_advgrid = $description_truncation_advgrid = isset($params['description_truncation_advgrid']) ? $params['description_truncation_advgrid'] : $this->_getParam('description_truncation_advgrid', '100');
		$this->view->limit_data_grid2 = $limit_data_grid2 = isset($params['limit_data_grid2']) ? $params['limit_data_grid2'] : $this->_getParam('limit_data_grid2', '10');

		//Grid 3
		$this->view->title_truncation_supergrid = $title_truncation_supergrid = isset($params['title_truncation_supergrid']) ? $params['title_truncation_supergrid'] : $this->_getParam('title_truncation_supergrid', '100');
		$this->view->width_supergrid = $width_supergrid = isset($params['width_supergrid']) ? $params['width_supergrid'] : $this->_getParam('width_supergrid','140');
    $this->view->height_supergrid = $height_supergrid = isset($params['height_supergrid']) ? $params['height_supergrid'] : $this->_getParam('height_supergrid','160');
		$this->view->description_truncation_supergrid = $description_truncation_supergrid = isset($params['description_truncation_supergrid']) ? $params['description_truncation_supergrid'] : $this->_getParam('description_truncation_supergrid', '100');
		$this->view->limit_data_grid3 = $limit_data_grid3 = isset($params['limit_data_grid3']) ? $params['limit_data_grid3'] : $this->_getParam('limit_data_grid3', '10');
		
    //Grid 4
		$this->view->title_truncation_advgrid2 = $title_truncation_advgrid2 = isset($params['title_truncation_advgrid2']) ? $params['title_truncation_advgrid2'] : $this->_getParam('title_truncation_advgrid2', '100');
		$this->view->width_advgrid2 = $width_advgrid2 = isset($params['width_advgrid2']) ? $params['width_advgrid2'] : $this->_getParam('width_advgrid2','140');
    $this->view->height_advgrid2 = $height_advgrid2 = isset($params['height_advgrid2']) ? $params['height_advgrid2'] : $this->_getParam('height_advgrid2','160');
		$this->view->description_truncation_advgrid2 = $description_truncation_advgrid2 = isset($params['description_truncation_advgrid2']) ? $params['description_truncation_advgrid2'] : $this->_getParam('description_truncation_advgrid2', '100');
		$this->view->limit_data_grid4 = $limit_data_grid4 = isset($params['limit_data_grid4']) ? $params['limit_data_grid4'] : $this->_getParam('limit_data_grid4', '10');
		//end
		
		//List View1
		$this->view->socialshare_enable_listview1plusicon = $socialshare_enable_listview1plusicon =isset($params['socialshare_enable_listview1plusicon']) ? $params['socialshare_enable_listview1plusicon'] : $this->_getParam('socialshare_enable_listview1plusicon', 1);
		$this->view->socialshare_icon_listview1limit = $socialshare_icon_listview1limit =isset($params['socialshare_icon_listview1limit']) ? $params['socialshare_icon_listview1limit'] : $this->_getParam('socialshare_icon_listview1limit', 2);
		
		//List View2
		$this->view->socialshare_enable_listview2plusicon = $socialshare_enable_listview2plusicon =isset($params['socialshare_enable_listview2plusicon']) ? $params['socialshare_enable_listview2plusicon'] : $this->_getParam('socialshare_enable_listview2plusicon', 1);
		$this->view->socialshare_icon_listview2limit = $socialshare_icon_listview2limit =isset($params['socialshare_icon_listview2limit']) ? $params['socialshare_icon_listview2limit'] : $this->_getParam('socialshare_icon_listview2limit', 2);
		
		//List View3
		$this->view->socialshare_enable_listview3plusicon = $socialshare_enable_listview3plusicon =isset($params['socialshare_enable_listview3plusicon']) ? $params['socialshare_enable_listview3plusicon'] : $this->_getParam('socialshare_enable_listview3plusicon', 1);
		$this->view->socialshare_icon_listview3limit = $socialshare_icon_listview3limit =isset($params['socialshare_icon_listview3limit']) ? $params['socialshare_icon_listview3limit'] : $this->_getParam('socialshare_icon_listview3limit', 2);
		
		//List View4
		$this->view->socialshare_enable_listview4plusicon = $socialshare_enable_listview4plusicon =isset($params['socialshare_enable_listview4plusicon']) ? $params['socialshare_enable_listview4plusicon'] : $this->_getParam('socialshare_enable_listview4plusicon', 1);
		$this->view->socialshare_icon_listview4limit = $socialshare_icon_listview4limit =isset($params['socialshare_icon_listview4limit']) ? $params['socialshare_icon_listview4limit'] : $this->_getParam('socialshare_icon_listview4limit', 2);
		
		//Grid View 1
		$this->view->socialshare_enable_gridview1plusicon = $socialshare_enable_gridview1plusicon =isset($params['socialshare_enable_gridview1plusicon']) ? $params['socialshare_enable_gridview1plusicon'] : $this->_getParam('socialshare_enable_gridview1plusicon', 1);
		$this->view->socialshare_icon_gridview1limit = $socialshare_icon_gridview1limit =isset($params['socialshare_icon_gridview1limit']) ? $params['socialshare_icon_gridview1limit'] : $this->_getParam('socialshare_icon_gridview1limit', 2);
		
		//Grid View 2
		$this->view->socialshare_enable_gridview2plusicon = $socialshare_enable_gridview2plusicon =isset($params['socialshare_enable_gridview2plusicon']) ? $params['socialshare_enable_gridview2plusicon'] : $this->_getParam('socialshare_enable_gridview2plusicon', 1);
		$this->view->socialshare_icon_gridview2limit = $socialshare_icon_gridview2limit =isset($params['socialshare_icon_gridview2limit']) ? $params['socialshare_icon_gridview2limit'] : $this->_getParam('socialshare_icon_gridview2limit', 2);
		
		//Grid View 3
		$this->view->socialshare_enable_gridview3plusicon = $socialshare_enable_gridview3plusicon =isset($params['socialshare_enable_gridview3plusicon']) ? $params['socialshare_enable_gridview3plusicon'] : $this->_getParam('socialshare_enable_gridview3plusicon', 1);
		$this->view->socialshare_icon_gridview3limit = $socialshare_icon_gridview3limit =isset($params['socialshare_icon_gridview3limit']) ? $params['socialshare_icon_gridview3limit'] : $this->_getParam('socialshare_icon_gridview3limit', 2);
		
		//Grid View 4
		$this->view->socialshare_enable_gridview4plusicon = $socialshare_enable_gridview4plusicon =isset($params['socialshare_enable_gridview4plusicon']) ? $params['socialshare_enable_gridview4plusicon'] : $this->_getParam('socialshare_enable_gridview4plusicon', 1);
		$this->view->socialshare_icon_gridview4limit = $socialshare_icon_gridview4limit =isset($params['socialshare_icon_gridview4limit']) ? $params['socialshare_icon_gridview4limit'] : $this->_getParam('socialshare_icon_gridview4limit', 2);
		
		//Pinboard View
		$this->view->socialshare_enable_pinviewplusicon = $socialshare_enable_pinviewplusicon =isset($params['socialshare_enable_pinviewplusicon']) ? $params['socialshare_enable_pinviewplusicon'] : $this->_getParam('socialshare_enable_pinviewplusicon', 1);
		$this->view->socialshare_icon_pinviewlimit = $socialshare_icon_pinviewlimit =isset($params['socialshare_icon_pinviewlimit']) ? $params['socialshare_icon_pinviewlimit'] : $this->_getParam('socialshare_icon_pinviewlimit', 2);
		
		
		//Map View
		$this->view->socialshare_enable_mapviewplusicon = $socialshare_enable_mapviewplusicon =isset($params['socialshare_enable_mapviewplusicon']) ? $params['socialshare_enable_mapviewplusicon'] : $this->_getParam('socialshare_enable_mapviewplusicon', 1);
		$this->view->socialshare_icon_mapviewlimit = $socialshare_icon_mapviewlimit =isset($params['socialshare_icon_mapviewlimit']) ? $params['socialshare_icon_mapviewlimit'] : $this->_getParam('socialshare_icon_mapviewlimit', 2);
		

		$this->view->htmlTitle = $htmlTitle = isset($params['htmlTitle']) ? $params['htmlTitle'] : $this->_getParam('htmlTitle','1');
		$this->view->tab_option = $tab_option = isset($params['tabOption']) ? $params['tabOption'] : $this->_getParam('tabOption','vertical');
    $this->view->height_list = $defaultHeightList = isset($params['height_list']) ? $params['height_list'] : $this->_getParam('height_list','160');
    
    $this->view->width_list = $defaultWidthList = isset($params['width_list']) ? $params['width_list'] : $this->_getParam('width_list','140');
    
    $this->view->height_grid = $defaultHeightGrid = isset($params['height_grid']) ? $params['height_grid'] : $this->_getParam('height_grid','160');
    
    $this->view->width_grid = $defaultWidthGrid = isset($params['width_grid']) ? $params['width_grid'] : $this->_getParam('width_grid','140');
    
    $this->view->width_pinboard = $defaultWidthPinboard = isset($params['width_pinboard']) ? $params['width_pinboard'] : $this->_getParam('width_pinboard','300');
    
    $this->view->height = $defaultHeight = isset($params['height']) ? $params['height'] : $this->_getParam('height', '200px');
    
    $this->view->title_truncation_list = $title_truncation_list = isset($params['title_truncation_list']) ? $params['title_truncation_list'] : $this->_getParam('title_truncation_list', '100');
    
    $this->view->title_truncation_grid = $title_truncation_grid = isset($params['title_truncation_grid']) ? $params['title_truncation_grid'] : $this->_getParam('title_truncation_grid', '100');
    
    $this->view->title_truncation_pinboard = $title_truncation_pinboard = isset($params['title_truncation_pinboard']) ? $params['title_truncation_pinboard'] : $this->_getParam('title_truncation_pinboard', '100');
    
    $this->view->description_truncation_list = $description_truncation_list = isset($params['description_truncation_list']) ? $params['description_truncation_list'] : $this->_getParam('description_truncation_list', '100');
    
    $this->view->description_truncation_grid = $description_truncation_grid = isset($params['description_truncation_grid']) ? $params['description_truncation_grid'] : $this->_getParam('description_truncation_grid', '100');
    
    $this->view->description_truncation_pinboard = $description_truncation_pinboard = isset($params['description_truncation_pinboard']) ? $params['description_truncation_pinboard'] : $this->_getParam('description_truncation_pinboard', '100'); 
    
    $this->view->show_limited_data = $show_limited_data = isset($params['show_limited_data']) ? $params['show_limited_data'] : $this->_getParam('show_limited_data', 0);
     $this->view->category_id = $category_id = isset($params['category_id']) ? $params['category_id'] : $this->_getParam('category_id', 0);
    
    //Need to Discuss 
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'ratingStar', 'by', 'title', 'featuredLabel','sponsoredLabel','verifiedLabel', 'category','description_list','description_grid','description_pinboard', 'favouriteButton','likeButton', 'socialSharing', 'view', 'creationDate', 'readmore'));
    if(is_array($show_criterias)) {
      foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    }

    $this->view->limit_data_pinboard = $limit_data_pinboard = isset($params['limit_data_pinboard']) ? $params['limit_data_pinboard'] : $this->_getParam('limit_data_pinboard', '10');
    
    $this->view->limit_data_grid1 = $limit_data_grid1 = isset($params['limit_data_grid1']) ? $params['limit_data_grid1'] : $this->_getParam('limit_data_grid1', '10');
    
    $this->view->limit_data_list1 = $limit_data_list1 = isset($params['limit_data_list1']) ? $params['limit_data_list1'] : $this->_getParam('limit_data_list1', '10');
    
    $this->view->limit_data_list4 = $limit_data_list4 = isset($params['limit_data_list4']) ? $params['limit_data_list4'] : $this->_getParam('limit_data_list4', '10');
    
    $this->view->bothViewEnable = false;
    if (!$is_ajax) {
      $this->view->optionsEnable = $optionsEnable = $this->_getParam('enableTabs', array('list1', 'list2', 'list3', 'list4','grid1', 'grid2', 'grid3', 'grid4', 'pinboard', 'map'));
      
      if($optionsEnable == '')
      $this->view->optionsEnable = array();
      else
      $this->view->optionsEnable = $optionsEnable;
      $view_type = $this->_getParam('openViewType', 'list1');
      if (count($optionsEnable) > 1) {
        $this->view->bothViewEnable = true;
      }
    }
    
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $view_type));
    
    
    $this->view->resource_id = $value['resource_id'] = $resource_id = isset($_GET['resource_id']) ? $_GET['resource_id'] : (isset($params['resource_id']) ?  $params['resource_id'] : Engine_Api::_()->core()->getSubject()->getIdentity());
    
    $this->view->resource_type = $value['resource_type'] = $resource_type = isset($_GET['resource_type']) ? $_GET['resource_type'] : (isset($params['resource_type']) ?  $params['resource_type'] : Engine_Api::_()->core()->getSubject()->getType());
    
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $params = array('resource_id' => $resource_id, 'resource_type' => $resource_type, 'height' => $defaultHeight,'openTab' => $defaultOpenTab,'height_list' => $defaultHeightList, 'width_list' => $defaultWidthList,'height_grid' => $defaultHeightGrid, 'width_grid' => $defaultWidthGrid,'width_pinboard'=>$defaultWidthPinboard,'limit_data_pinboard'=>$limit_data_pinboard,'limit_data_list1'=>$limit_data_list1,'limit_data_list4'=>$limit_data_list4,'limit_data_grid1'=>$limit_data_grid1,'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type,  'description_truncation_list' => $description_truncation_list, 'title_truncation_list' => $title_truncation_list, 'title_truncation_grid' => $title_truncation_grid,'title_truncation_pinboard'=>$title_truncation_pinboard,'description_truncation_grid'=>$description_truncation_grid,'description_truncation_pinboard'=>$description_truncation_pinboard, 'show_limited_data' => $show_limited_data,'title_truncation_simplelist'=>$title_truncation_simplelist,'width_simplelist'=>$width_simplelist,'height_simplelist'=>$height_simplelist,'description_truncation_simplelist'=>$description_truncation_simplelist,'limit_data_list2'=>$limit_data_list2,'title_truncation_advlist'=>$title_truncation_advlist,'description_truncation_advlist'=>$description_truncation_advlist,'limit_data_list3'=>$limit_data_list3,'title_truncation_advgrid'=>$title_truncation_advgrid,'width_advgrid'=>$width_advgrid,'height_advgrid'=>$height_advgrid,'description_truncation_advgrid'=>$description_truncation_advgrid,'limit_data_grid4'=>$limit_data_grid4,	'title_truncation_supergrid'=>$title_truncation_supergrid,'width_supergrid'=>$width_supergrid,'height_supergrid'=>$height_supergrid,'description_truncation_supergrid'=>$description_truncation_supergrid,'limit_data_grid3'=>$limit_data_grid3,'category_id'=>$category_id,   'title_truncation_advgrid2'=>$title_truncation_advgrid2,'width_advgrid2'=>$width_advgrid2,'height_advgrid2'=>$height_advgrid2,'description_truncation_advgrid2'=>$description_truncation_advgrid2,'limit_data_grid2'=>$limit_data_grid2, 'socialshare_enable_listview1plusicon' => $socialshare_enable_listview1plusicon, 'socialshare_icon_listview1limit' => $socialshare_icon_listview1limit, 'socialshare_enable_listview2plusicon' => $socialshare_enable_listview2plusicon, 'socialshare_icon_listview2limit' => $socialshare_icon_listview2limit, 'socialshare_enable_listview3plusicon' => $socialshare_enable_listview3plusicon, 'socialshare_icon_listview3limit' => $socialshare_icon_listview3limit, 'socialshare_enable_listview4plusicon' => $socialshare_enable_listview4plusicon, 'socialshare_icon_listview4limit' => $socialshare_icon_listview4limit, 'socialshare_enable_gridview1plusicon' => $socialshare_enable_gridview1plusicon, 'socialshare_icon_gridview1limit' => $socialshare_icon_gridview1limit, 'socialshare_enable_gridview2plusicon' => $socialshare_enable_gridview2plusicon, 'socialshare_icon_gridview2limit' => $socialshare_icon_gridview2limit, 'socialshare_enable_gridview3plusicon' => $socialshare_enable_gridview3plusicon, 'socialshare_icon_gridview3limit' => $socialshare_icon_gridview3limit, 'socialshare_enable_gridview4plusicon' => $socialshare_enable_gridview4plusicon, 'socialshare_icon_gridview4limit' => $socialshare_icon_gridview4limit, 'socialshare_enable_pinviewplusicon' => $socialshare_enable_pinviewplusicon, 'socialshare_icon_pinviewlimit' => $socialshare_icon_pinviewlimit, 'socialshare_enable_mapviewplusicon' => $socialshare_enable_mapviewplusicon, 'socialshare_icon_mapviewlimit' => $socialshare_icon_mapviewlimit);


    
    $this->view->loadJs = true;
    
    // custom list grid view options
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sesblog_blog', null, 'create');
    
    $type = '';
    switch ($defaultOpenTab) {
      case 'recentlySPcreated':
        $popularCol = 'creation_date';
        $type = 'creation';
        break;
      case 'mostSPviewed':
        $popularCol = 'view_count';
        $type = 'view';
        break;
      case 'mostSPliked':
        $popularCol = 'like_count';
        $type = 'like';
        break;
      case 'mostSPcommented':
        $popularCol = 'comment_count';
        $type = 'comment';
        break;
      case 'mostSPrated':
        $popularCol = 'rating';
        $type = 'rating';
        break;
      case 'mostSPfavourite':
        $popularCol = 'favourite_count';
        $type = 'favourite';
        break;
      case 'featured':
        $popularCol = 'featured';
        $type = 'featured';
        $fixedData = 'featured';
        break;
      case 'sponsored':
        $popularCol = 'sponsored';
        $type = 'sponsored';
        $fixedData = 'sponsored';
        break;
      case 'verified':
        $popularCol = 'verified';
        $type = 'verified';
        $fixedData = 'verified';
        break;
      case 'week':
        $popularCol = 'week';
        $type = 'week';
        break;
      case 'month':
        $popularCol = 'month';
        $type = 'month';
        break;
    }
   
    $this->view->type = $type;
    $value['popularCol'] = isset($popularCol) ? $popularCol : '';
    $value['fixedData'] = isset($fixedData) ? $fixedData : '';
    $value['draft'] = 0;
    $value['search'] = 1;
    $options = array('tabbed' => true, 'paggindData' => true);
    $this->view->optionsListGrid = $options;
    $this->view->widgetName = 'other-modules-profile-sesblogs';			
    $params = array_merge($params, $value);

    // Get Blogs
    $paginator = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsPaginator($value);
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $limit_data = $this->view->{'limit_data_'.$view_type};
    $paginator->setItemCountPerPage($limit_data);
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    $this->view->params = $params;
    if ($is_ajax)
    $this->getElement()->removeDecorator('Container');
    
    // Add count to title if configured
    if( $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }
  
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}
