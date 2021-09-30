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
class Sesblog_Widget_ManageBlogsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
 
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->defaultOptionsArray = $defaultOptionsArray = $this->_getParam('search_type',array('recentlySPcreated','mostSPviewed','mostSPliked','mostSPcommented','mostSPrated','mostSPfavourite','featured','sponsored', 'verified'));
  
    if (isset($_POST['params']))
    $params = $_POST['params'];
    
    if (isset($_POST['searchParams']) && $_POST['searchParams'])
    parse_str($_POST['searchParams'], $searchArray);
    
    $this->view->identityForWidget = $identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : $this->view->identity;
    $this->view->widgetParams = $widgetParams = Engine_Api::_()->sesblog()->getWidgetParams($identityForWidget);
    
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $this->view->view_more = isset($_POST['view_more']) ? true : false;
    $defaultOpenTab = array();
    $defaultOptions = $arrayOptions = array();
    if (!$is_ajax && is_array($defaultOptionsArray)) {
      foreach ($defaultOptionsArray as $key => $defaultValue) {
				if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1) && $defaultValue == 'mostSPfavourite')
					continue;
        if ($this->_getParam($defaultValue . '_order'))
          $order = $this->_getParam($defaultValue . '_order') . '||' . $defaultValue;
        else
          $order = (1000 + $key).'||'.$defaultValue;
        if ($this->_getParam($defaultValue.'_label'))
          $valueLabel = $this->_getParam($defaultValue.'_label');
        else {
          if ($defaultValue == 'recentlySPcreated')
            $valueLabel = 'Recently Created';
          else if ($defaultValue == 'mostSPviewed')
            $valueLabel = 'Most Viewed';
          else if ($defaultValue == 'mostSPliked')
            $valueLabel = 'Most Liked';
          else if ($defaultValue == 'mostSPcommented')
            $valueLabel = 'Most Commented';
          else if ($defaultValue == 'mostSPrated')
            $valueLabel = 'Most Rated';
          else if ($defaultValue == 'mostSPfavourite')
            $valueLabel = 'Most Favourite';
          else if ($defaultValue == 'featured')
            $valueLabel = 'Featured';
          else if ($defaultValue == 'sponsored')
            $valueLabel = 'Sponsored';
          else if ($defaultValue == 'verified')
            $valueLabel = 'Verified';
        }
        $arrayOptions[$order] = $valueLabel;
      }
      ksort($arrayOptions);
      $counter = 0;
      foreach ($arrayOptions as $key => $valueOption) {
        $key = explode('||',$key);
        if ($counter == 0)
          $this->view->defaultOpenTab = $defaultOpenTab = $key[1];
        $defaultOptions[$key[1]] = $valueOption;
        $counter++;
      }
    }
    $this->view->defaultOptions = $defaultOptions;
    
    if (isset($_GET['openTab']) || $is_ajax) {
      $this->view->defaultOpenTab = $defaultOpenTab = (isset($_GET['openTab']) ? str_replace('_', 'SP', $_GET['openTab']) : ($this->_getParam('openTab') != NULL ? $this->_getParam('openTab') : (isset($params['openTab']) ? $params['openTab'] : '' )));
    }
    $sesblog_tabbed = Zend_Registry::isRegistered('sesblog_tabbed') ? Zend_Registry::get('sesblog_tabbed') : null;
		//simple list view
		$this->view->title_truncation_simplelist = $title_truncation_simplelist = isset($params['title_truncation_simplelist']) ? $params['title_truncation_simplelist'] : $this->_getParam('title_truncation_simplelist', '100');
		$this->view->width_simplelist = $width_simplelist = isset($params['width_simplelist']) ? $params['width_simplelist'] : $this->_getParam('width_simplelist','140');
    $this->view->height_simplelist = $height_simplelist = isset($params['height_simplelist']) ? $params['height_simplelist'] : $this->_getParam('height_simplelist','160');
		$this->view->description_truncation_simplelist = $description_truncation_simplelist = isset($params['description_truncation_simplelist']) ? $params['description_truncation_simplelist'] : $this->_getParam('description_truncation_simplelist', '100');
		$this->view->limit_data_list2 = $limit_data_list2 = isset($params['limit_data_list2']) ? $params['limit_data_list2'] : $this->_getParam('limit_data_list2', '10');
		//advanced list view
		$this->view->title_truncation_advlist = $title_truncation_advlist = isset($params['title_truncation_advlist']) ? $params['title_truncation_advlist'] : $this->_getParam('title_truncation_advlist', '100');
		$this->view->description_truncation_advlist= $description_truncation_advlist = isset($params['description_truncation_advlist']) ? $params['description_truncation_advlist'] : $this->_getParam('description_truncation_advlist', '100');

		$this->view->description_truncation_advlist2 = $description_truncation_advlist2 = isset($params['description_truncation_advlist2']) ? $params['description_truncation_advlist2'] : $this->_getParam('description_truncation_advlist2', '100');
		
		$this->view->limit_data_list3= $limit_data_list3 = isset($params['limit_data_list3']) ? $params['limit_data_list3'] : $this->_getParam('limit_data_list3', '10');
		//advanced grid view
		$this->view->title_truncation_advgrid = $title_truncation_advgrid = isset($params['title_truncation_advgrid']) ? $params['title_truncation_advgrid'] : $this->_getParam('title_truncation_advgrid', '100');
		$this->view->width_advgrid = $width_advgrid = isset($params['width_advgrid']) ? $params['width_advgrid'] : $this->_getParam('width_advgrid','140');
    $this->view->height_advgrid = $height_advgrid = isset($params['height_advgrid']) ? $params['height_advgrid'] : $this->_getParam('height_advgrid','160');
		$this->view->description_truncation_advgrid = $description_truncation_advgrid = isset($params['description_truncation_advgrid']) ? $params['description_truncation_advgrid'] : $this->_getParam('description_truncation_advgrid', '100');
		$this->view->limit_data_grid2 = $limit_data_grid2 = isset($params['limit_data_grid2']) ? $params['limit_data_grid2'] : $this->_getParam('limit_data_grid2', '10');
    if (empty($sesblog_tabbed))
      return $this->setNoRender();
		//super advanced grid view
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
    
    //START CODE OF FORM FILTERING
    $value['sort'] = isset($searchArray['sort']) ? $searchArray['sort'] : (isset($_GET['sort']) ? $_GET['sort'] : (isset($params['sort']) ? $params['sort'] : $this->_getParam('sort', 'mostSPliked')));
    
    $value['category_id'] =  isset($searchArray['category_id']) ? $searchArray['category_id'] : (isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ? $params['category_id'] : ''));
    
    $value['subcat_id'] = isset($searchArray['subcat_id']) ? $searchArray['subcat_id'] :  (isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ? $params['subcat_id'] : ''));
    
    $value['subsubcat_id'] = isset($searchArray['subsubcat_id']) ? $searchArray['subsubcat_id'] : (isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ? $params['subsubcat_id'] : ''));    
    
    $value['alphabet'] = isset($_GET['alphabet']) ? $_GET['alphabet'] : (isset($params['alphabet']) ? $params['alphabet'] : '');
    
    $value['tag'] = isset($_GET['tag_id']) ? $_GET['tag_id'] : (isset($params['tag_id']) ? $params['tag_id'] : '');
    
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'ratingStar', 'by', 'title', 'featuredLabel','sponsoredLabel','category','description_list','description_grid','description_pinboard', 'favouriteButton','likeButton', 'socialSharing', 'view', 'creationDate', 'readmore'));
    
    $value['location'] = isset($searchArray['location']) ? $searchArray['location'] :  (isset($_GET['location']) ? $_GET['location'] : (isset($params['location']) ?  $params['location'] : ''));
    
    $value['lat'] = isset($searchArray['lat']) ? $searchArray['lat'] :  (isset($_GET['lat']) ? $_GET['lat'] : (isset($params['lat']) ?  $params['lat'] : ''));
    
    $value['lng'] = isset($searchArray['lng']) ? $searchArray['lng'] :  (isset($_GET['lng']) ? $_GET['lng'] : (isset($params['lng']) ?  $params['lng'] : ''));
    
    $value['miles'] = isset($searchArray['miles']) ? $searchArray['miles'] :  (isset($_GET['miles']) ? $_GET['miles'] : (isset($params['miles']) ?  $params['miles'] : ''));
    
    $value['show'] = isset($searchArray['show']) ? $searchArray['show'] :  (isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ?  $params['show'] : ''));
    
    $value['user_id'] = isset($_GET['user_id']) ? $_GET['user_id'] : (isset($params['user_id']) ?  $params['user_id'] : '');
    
    $text =  isset($searchArray['search']) ? $searchArray['search'] : (!empty($params['search']) ? $params['search'] : (isset($_GET['search']) && ($_GET['search'] != '') ? $_GET['search'] : ''));
    //END CODE OF SEARH FILTERING
    
    //Need to Discuss 
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'featuredLabel','sponsoredLabel','category','description_list','description_grid','description_pinboard', 'favouriteButton','likeButton', 'socialSharing', 'view'));
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
      $view_type = $this->_getParam('openViewType', 'list');
      if (count($optionsEnable) > 1) {
        $this->view->bothViewEnable = true;
      }
    }
		
		$this->view->socialshare_enable_plusicon = $socialshare_enable_plusicon =isset($params['socialshare_enable_plusicon']) ? $params['socialshare_enable_plusicon'] : $this->_getParam('socialshare_enable_plusicon', 1);
		$this->view->socialshare_icon_limit = $socialshare_icon_limit =isset($params['socialshare_icon_limit']) ? $params['socialshare_icon_limit'] : $this->_getParam('socialshare_icon_limit', 2);
		
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $view_type));
   
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $params = array('alphabet' => $value['alphabet'], 'height' => $defaultHeight,'tag' => $value['tag'],'location'=>$value['location'],'lat'=>$value['lat'],'lng'=>$value['lng'] ,'miles'=>$value['miles'],'height_list' => $defaultHeightList, 'width_list' => $defaultWidthList,'height_grid' => $defaultHeightGrid, 'width_grid' => $defaultWidthGrid,'width_pinboard'=>$defaultWidthPinboard,'limit_data_pinboard'=>$limit_data_pinboard,'limit_data_list1'=>$limit_data_list1,'limit_data_list4' => $limit_data_list4,'limit_data_grid1'=>$limit_data_grid1,'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type,  'description_truncation_list' => $description_truncation_list, 'title_truncation_list' => $title_truncation_list, 'title_truncation_grid' => $title_truncation_grid,'title_truncation_pinboard'=>$title_truncation_pinboard,'description_truncation_grid'=>$description_truncation_grid,'description_truncation_pinboard'=>$description_truncation_pinboard,'category_id'=>$value['category_id'],'subcat_id'=>$value['subcat_id'],'subsubcat_id' => $value['subsubcat_id'], 'sort' => $value['sort'],'show'=>$value['show'],'user_id'=>$value['user_id'],'title_truncation_simplelist'=>$title_truncation_simplelist,'width_simplelist'=>$width_simplelist,'height_simplelist'=>$height_simplelist,'description_truncation_simplelist'=>$description_truncation_simplelist,'limit_data_list2'=>$limit_data_list2,'title_truncation_advlist'=>$title_truncation_advlist,'description_truncation_advlist'=>$description_truncation_advlist, 'description_truncation_advlist2'=>$description_truncation_advlist2,'limit_data_list3'=>$limit_data_list3,'title_truncation_advgrid'=>$title_truncation_advgrid,'width_advgrid'=>$width_advgrid,'height_advgrid'=>$height_advgrid,'description_truncation_advgrid'=>$description_truncation_advgrid,'limit_data_grid2'=>$limit_data_grid2,	'title_truncation_supergrid'=>$title_truncation_supergrid,'width_supergrid'=>$width_supergrid,'height_supergrid'=>$height_supergrid,'description_truncation_supergrid'=>$description_truncation_supergrid,'limit_data_grid3'=>$limit_data_grid3, 'limit_data_grid4' => $limit_data_grid4, 'socialshare_enable_plusicon' => $socialshare_enable_plusicon, 'socialshare_icon_limit' => $socialshare_icon_limit);
      
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
    }
    
    //START CODE OF SEARCH FORM
    $this->view->category = $value['category_id'];
    $this->view->text = $value['text']  = stripslashes($text);
    if (isset($value['sort']) && $value['sort'] != '') 
    $value['getParamSort'] = str_replace('SP', '_', $value['sort']);
    else
    $value['getParamSort'] = 'creation_date';
      
    if (isset($value['getParamSort'])) {
      switch ($value['getParamSort']) {
        case 'most_viewed':
          $value['popularCol'] = 'view_count';
          break;
        case 'most_liked':
          $value['popularCol'] = 'like_count';
          break;
        case 'most_commented':
          $value['popularCol'] = 'comment_count';
          break;
        case 'sponsored':
          $value['popularCol'] = 'sponsored';
					$value['fixedData'] = 'sponsored';
          break;
				case 'featured':
          $value['popularCol'] = 'featured';
					$value['fixedData'] = 'featured';
          break;
				case 'verified':
					$value['popularCol'] = 'verified';
				  $value['fixedData'] = 'verified';
				  break;
        case 'most_rated':
          $value['popularCol'] = 'rating';
          break;
        case 'recently_created':
        default:
          $value['popularCol'] = 'creation_date';
          break;
      }
    }
    //END CODE OF SEARCH FORM
   
    $this->view->type = $type;
    $value['popularCol'] = isset($popularCol) ? $popularCol : '';
    $value['fixedData'] = isset($fixedData) ? $fixedData : '';
    $value['search'] = 1;
    $value['manage-widget'] = 1;
    
    // Code added by questwalk

    $selectedTemplate = isset($_POST['selectedTemplate'])
                  ? $_POST['selectedTemplate']
                  : (
                       isset($_GET['selectedTemplate'])
                       ? $_GET['selectedTemplate']
                       : 0
                  );

    $queryParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $value['org_id'] = isset( $queryParams['org_id']) ?  $queryParams['org_id'] : (isset( $_POST['params']['org_id']) ?  $_POST['params']['org_id'] : 0 );
    $value['project_id'] = isset( $queryParams['project_id']) ?  $queryParams['project_id'] : (isset( $_POST['params']['project_id']) ?  $_POST['params']['project_id'] : 0 );
  
    $options = array('tabbed' => true, 'paggindData' => true);
    $this->view->optionsListGrid = $options;
    $this->view->widgetName = 'manage-blogs';			
    $params = array_merge($params, $value, array('search'=>addslashes($text)));

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
  }
}
