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
class Sesblog_Widget_BrowseBlogsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    // Code for blog added by questwalk [SR813]
    $org_subject_id = 0;
    try{
      if( $subject = Engine_Api::_()->core()->getSubject() ) {
         
        
        $subject_type = $subject->getType();
        if(!empty($subject_type) && ($subject_type  == 'sitepage_page'))
          $org_subject_id = $subject->getIdentity();
        elseif(!empty($subject_type) && ($subject_type  == 'sitecrowdfunding_project')){
          $project_subject_id = $subject->getIdentity();
        }  
      }
    }catch(Exception $e) {
      // blank exception
    }

  

    $searchArray = array();
    if (isset($_POST['params']))
      $params = $_POST['params'];

     $org_subject_id = (isset($_POST['params']['org_id']) && !empty($_POST['params']['org_id']))? $_POST['params']['org_id']: $org_subject_id;
     $project_subject_id = (isset($_POST['params']['project_id']) && !empty($_POST['params']['project_id']))? $_POST['params']['project_id']: $project_subject_id;
   
    if (isset($_POST['searchParams']) && $_POST['searchParams'])
      parse_str($_POST['searchParams'], $searchArray);

    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    
    $this->view->is_search =  $is_search = !empty($_POST['is_search']) ? true : false;
    
    $searchParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    $form = new Sesblog_Form_Search(array('searchTitle' => 'yes','browseBy' => 'yes','categoriesSearch' => 'yes','searchFor'=>'blog','FriendsSearch'=>'yes','defaultSearchtype'=>'mostSPliked','locationSearch' => 'yes','kilometerMiles' => 'yes','hasPhoto' => 'yes'));
    
		if ($is_search)
      $form->populate($searchArray);
		elseif ($is_ajax)
      $form->populate($params);
		else
      $form->populate($searchParams);
    
    $this->view->view_more = isset($_POST['view_more']) ? true : false;

    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = $identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : $this->view->identity;
    
    $this->view->widgetParams = $widgetParams = Engine_Api::_()->sesblog()->getWidgetParams($identityForWidget);
		
		//simple list view
		$this->view->title_truncation_simplelist = $title_truncation_simplelist = isset($params['title_truncation_simplelist']) ? $params['title_truncation_simplelist'] : $this->_getParam('title_truncation_simplelist', '100');
		$this->view->width_simplelist = $width_simplelist = isset($params['width_simplelist']) ? $params['width_simplelist'] : $this->_getParam('width_simplelist','140');
    $this->view->height_simplelist = $height_simplelist = isset($params['height_simplelist']) ? $params['height_simplelist'] : $this->_getParam('height_simplelist','160');
		$this->view->description_truncation_simplelist = $description_truncation_simplelist = isset($params['description_truncation_simplelist']) ? $params['description_truncation_simplelist'] : $this->_getParam('description_truncation_simplelist', '100');
		$this->view->limit_data_list2 = $limit_data_list2 = isset($params['limit_data_list2']) ? $params['limit_data_list2'] : $this->_getParam('limit_data_list2', '10');
		//advanced list view
		$this->view->title_truncation_advlist = $title_truncation_advlist = isset($params['title_truncation_advlist']) ? $params['title_truncation_advlist'] : $this->_getParam('title_truncation_advlist', '100');
		$this->view->description_truncation_advlist= $description_truncation_advlist = isset($params['description_truncation_advlist']) ? $params['description_truncation_advlist'] : $this->_getParam('description_truncation_advlist', '100');
		$this->view->limit_data_list3= $limit_data_list3 = isset($params['limit_data_list3']) ? $params['limit_data_list3'] : $this->_getParam('limit_data_list3', '10');
		//advanced grid view
		$this->view->title_truncation_advgrid = $title_truncation_advgrid = isset($params['title_truncation_advgrid']) ? $params['title_truncation_advgrid'] : $this->_getParam('title_truncation_advgrid', '100');
		$this->view->width_advgrid = $width_advgrid = isset($params['width_advgrid']) ? $params['width_advgrid'] : $this->_getParam('width_advgrid','140');
    $this->view->height_advgrid = $height_advgrid = isset($params['height_advgrid']) ? $params['height_advgrid'] : $this->_getParam('height_advgrid','160');
		$this->view->description_truncation_advgrid = $description_truncation_advgrid = isset($params['description_truncation_advgrid']) ? $params['description_truncation_advgrid'] : $this->_getParam('description_truncation_advgrid', '100');
		$this->view->limit_data_grid2 = $limit_data_grid2 = isset($params['limit_data_grid2']) ? $params['limit_data_grid2'] : $this->_getParam('limit_data_grid2', '10');
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
    
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
    $this->view->height_list = $defaultHeightList = isset($params['height_list']) ? $params['height_list'] : $this->_getParam('height_list','160');
    $this->view->width_list = $defaultWidthList = isset($params['width_list']) ? $params['width_list'] : $this->_getParam('width_list','140');
    $this->view->height_grid = $defaultHeightGrid = isset($params['height_grid']) ? $params['height_grid'] : $this->_getParam('height_grid','160');
    $this->view->width_grid = $defaultWidthGrid = isset($params['width_grid']) ? $params['width_grid'] : $this->_getParam('width_grid','140');
    $this->view->width_pinboard = $defaultWidthPinboard = isset($params['width_pinboard']) ? $params['width_pinboard'] : $this->_getParam('width_pinboard','300');
    $this->view->height = $defaultHeight = isset($params['height']) ? $params['height'] : $this->_getParam('height', '200px');
    $this->view->show_item_count = $show_item_count = isset($params['show_item_count']) ? $params['show_item_count'] :  $this->_getParam('show_item_count',0);
    
    $this->view->title_truncation_list = $title_truncation_list = isset($params['title_truncation_list']) ? $params['title_truncation_list'] : $this->_getParam('title_truncation_list', '100');
    $this->view->title_truncation_grid = $title_truncation_grid = isset($params['title_truncation_grid']) ? $params['title_truncation_grid'] : $this->_getParam('title_truncation_grid', '100');
    $this->view->title_truncation_pinboard = $title_truncation_pinboard = isset($params['title_truncation_pinboard']) ? $params['title_truncation_pinboard'] : $this->_getParam('title_truncation_pinboard', '100');
    $this->view->description_truncation_list = $description_truncation_list = isset($params['description_truncation_list']) ? $params['description_truncation_list'] : $this->_getParam('description_truncation_list', '100');
    $this->view->description_truncation_grid = $description_truncation_grid = isset($params['description_truncation_grid']) ? $params['description_truncation_grid'] : $this->_getParam('description_truncation_grid', '100');
    $this->view->description_truncation_pinboard = $description_truncation_pinboard = isset($params['description_truncation_pinboard']) ? $params['description_truncation_pinboard'] : $this->_getParam('description_truncation_pinboard', '100');
    
    $sesblog_browssesblogs = Zend_Registry::isRegistered('sesblog_browssesblogs') ? Zend_Registry::get('sesblog_browssesblogs') : null;
    if (empty($sesblog_browssesblogs))
      return $this->setNoRender();
      
    $text =  isset($searchArray['search']) ? $searchArray['search'] : (!empty($params['search']) ? $params['search'] : (isset($_GET['search']) && ($_GET['search'] != '') ? $_GET['search'] : ''));
    //search data
    $value['sort'] = isset($searchArray['sort']) ? $searchArray['sort'] : (isset($_GET['sort']) ? $_GET['sort'] : (isset($params['sort']) ? $params['sort'] : $this->_getParam('sort', 'mostSPliked')));
    $value['category_id'] =  isset($searchArray['category_id']) ? $searchArray['category_id'] : (isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ? $params['category_id'] : $this->_getParam('category')));
    $value['subcat_id'] = isset($searchArray['subcat_id']) ? $searchArray['subcat_id'] :  (isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ? $params['subcat_id'] : ''));
    $value['subsubcat_id'] = isset($searchArray['subsubcat_id']) ? $searchArray['subsubcat_id'] : (isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ? $params['subsubcat_id'] : ''));    
    $value['alphabet'] = isset($_GET['alphabet']) ? $_GET['alphabet'] : (isset($params['alphabet']) ? $params['alphabet'] : '');
    $value['tag'] = isset($_GET['tag_id']) ? $_GET['tag_id'] : (isset($params['tag_id']) ? $params['tag_id'] : '');
    $value['date'] = isset($_GET['date']) ? $_GET['date'] : (isset($params['date']) ? $params['date'] : '');
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'ratingStar', 'by', 'title', 'featuredLabel','sponsoredLabel', 'verifiedLabel' ,'category','description_list','description_grid','description_pinboard', 'favouriteButton','likeButton', 'socialSharing', 'view', 'creationDate', 'readmore'));
    
    $value['location'] = isset($searchArray['location']) ? $searchArray['location'] :  (isset($_GET['location']) ? $_GET['location'] : (isset($params['location']) ?  $params['location'] : ''));
    $value['lat'] = isset($searchArray['lat']) ? $searchArray['lat'] :  (isset($_GET['lat']) ? $_GET['lat'] : (isset($params['lat']) ?  $params['lat'] : ''));
    $value['lng'] = isset($searchArray['lng']) ? $searchArray['lng'] :  (isset($_GET['lng']) ? $_GET['lng'] : (isset($params['lng']) ?  $params['lng'] : ''));
    $value['miles'] = isset($searchArray['miles']) ? $searchArray['miles'] :  (isset($_GET['miles']) ? $_GET['miles'] : (isset($params['miles']) ?  $params['miles'] : ''));
    $value['show'] = isset($searchArray['show']) ? $searchArray['show'] :  (isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ?  $params['show'] : ''));
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$paramsUser = $request->getParams();
		
		
    $value['user_id'] = isset($_GET['user_id']) ? $_GET['user_id'] : (isset($params['user_id']) ?  $params['user_id'] : (isset($paramsUser['user_id']) ? $paramsUser['user_id'] : ''));
    if(is_array($show_criterias)){
      foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    }
    $this->view->bothViewEnable = false;
		if (!$is_ajax) {
			$this->view->optionsEnable = $optionsEnable = $this->_getParam('enableTabs', array('list1', 'list2', 'list3', 'advlist2','grid1', 'grid2', 'grid3', 'grid4', 'pinboard', 'map'));
			$view_type = $this->_getParam('openViewType', 'list');
			if (count($optionsEnable) > 1) {
				$this->view->bothViewEnable = true;
			}
		}
    $this->view->limit_data_pinboard = $limit_data_pinboard = isset($params['limit_data_pinboard']) ? $params['limit_data_pinboard'] : $this->_getParam('limit_data_pinboard', '10');
    $this->view->limit_data_grid1 = $limit_data_grid1 = isset($params['limit_data_grid1']) ? $params['limit_data_grid1'] : $this->_getParam('limit_data_grid1', '10');
    $this->view->limit_data_list1 = $limit_data_list1 = isset($params['limit_data_list1']) ? $params['limit_data_list1'] : $this->_getParam('limit_data_list1', '10');
    $this->view->limit_data_list4 = $limit_data_list4 = isset($params['limit_data_list4']) ? $params['limit_data_list4'] : $this->_getParam('limit_data_list4', '10');
 
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $view_type));
    
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

    $params = array('alphabet' => $value['alphabet'], 'height' => $defaultHeight,'tag' => $value['tag'],'location'=>$value['location'],'lat'=>$value['lat'],'lng'=>$value['lng'] ,'miles'=>$value['miles'],'height_list' => $defaultHeightList, 'width_list' => $defaultWidthList,'height_grid' => $defaultHeightGrid, 'width_grid' => $defaultWidthGrid,'width_pinboard'=>$defaultWidthPinboard,'limit_data_pinboard'=>$limit_data_pinboard,'limit_data_list1'=>$limit_data_list1,'limit_data_list4'=>$limit_data_list4,'limit_data_grid1'=>$limit_data_grid1,'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type,  'description_truncation_list' => $description_truncation_list, 'title_truncation_list' => $title_truncation_list, 'title_truncation_grid' => $title_truncation_grid,'title_truncation_pinboard'=>$title_truncation_pinboard,'description_truncation_grid'=>$description_truncation_grid,'description_truncation_pinboard'=>$description_truncation_pinboard,'category_id'=>$value['category_id'],'subcat_id'=>$value['subcat_id'],'subsubcat_id' => $value['subsubcat_id'], 'sort' => $value['sort'],'show'=>$value['show'],'user_id'=>$value['user_id'],'date'=>$value['date'], 'show_item_count'=>$show_item_count,'title_truncation_simplelist'=>$title_truncation_simplelist,'width_simplelist'=>$width_simplelist,'height_simplelist'=>$height_simplelist,'description_truncation_simplelist'=>$description_truncation_simplelist,'limit_data_list2'=>$limit_data_list2,'title_truncation_advlist'=>$title_truncation_advlist,'description_truncation_advlist'=>$description_truncation_advlist,'limit_data_list3'=>$limit_data_list3,'title_truncation_advgrid'=>$title_truncation_advgrid,'width_advgrid'=>$width_advgrid,'height_advgrid'=>$height_advgrid,'description_truncation_advgrid'=>$description_truncation_advgrid,'limit_data_grid2'=>$limit_data_grid2,	'title_truncation_supergrid'=>$title_truncation_supergrid,'width_supergrid'=>$width_supergrid,'height_supergrid'=>$height_supergrid,'description_truncation_supergrid'=>$description_truncation_supergrid,'limit_data_grid3'=>$limit_data_grid3, 'limit_data_grid4' => $limit_data_grid4, 'socialshare_enable_listview1plusicon' => $socialshare_enable_listview1plusicon, 'socialshare_icon_listview1limit' => $socialshare_icon_listview1limit, 'socialshare_enable_listview2plusicon' => $socialshare_enable_listview2plusicon, 'socialshare_icon_listview2limit' => $socialshare_icon_listview2limit, 'socialshare_enable_listview3plusicon' => $socialshare_enable_listview3plusicon, 'socialshare_icon_listview3limit' => $socialshare_icon_listview3limit, 'socialshare_enable_listview4plusicon' => $socialshare_enable_listview4plusicon, 'socialshare_icon_listview4limit' => $socialshare_icon_listview4limit, 'socialshare_enable_gridview1plusicon' => $socialshare_enable_gridview1plusicon, 'socialshare_icon_gridview1limit' => $socialshare_icon_gridview1limit, 'socialshare_enable_gridview2plusicon' => $socialshare_enable_gridview2plusicon, 'socialshare_icon_gridview2limit' => $socialshare_icon_gridview2limit, 'socialshare_enable_gridview3plusicon' => $socialshare_enable_gridview3plusicon, 'socialshare_icon_gridview3limit' => $socialshare_icon_gridview3limit, 'socialshare_enable_gridview4plusicon' => $socialshare_enable_gridview4plusicon, 'socialshare_icon_gridview4limit' => $socialshare_icon_gridview4limit, 'socialshare_enable_pinviewplusicon' => $socialshare_enable_pinviewplusicon, 'socialshare_icon_pinviewlimit' => $socialshare_icon_pinviewlimit, 'socialshare_enable_mapviewplusicon' => $socialshare_enable_mapviewplusicon, 'socialshare_icon_mapviewlimit' => $socialshare_icon_mapviewlimit);
    
    $this->view->loadMoreLink = $this->_getParam('openTab') != NULL ? true : false;
    $this->view->loadJs = true;
    
    // custom list grid view options
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sesblog_blog', null, 'create');
    if (!$is_ajax) {
     // Make form	
     if (!empty($_GET['tag_id'])) {
       $tag = Engine_Api::_()->getItem('core_tag', $_GET['tag_id']);
       if($tag) {
        $this->view->tag = $tag->text;
       }
     }
    }
    
    $this->view->category = $value['category_id'];
    $this->view->text = $value['text']  = stripslashes($text);
    if (isset($value['sort']) && $value['sort'] != '') {
      $value['getParamSort'] = str_replace('SP', '_', $value['sort']);
    } else
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
				case 'most_favourite':
					$value['popularCol'] = 'favourite_count';
					break;
				case 'sponsored':
					$value['popularCol'] = 'sponsored';
					$value['fixedData'] = 'sponsored';
					break;
				case 'verified':
					$value['popularCol'] = 'verified';
					$value['fixedData'] = 'verified';
				break;
				case 'featured':
					$value['popularCol'] = 'featured';
					$value['fixedData'] = 'featured';
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

    // Blog browse page work
    $type = '';
    $page_id = Engine_Api::_()->sesblog()->getWidgetPageId($identityForWidget);
    if($page_id) {
      $pageName = Engine_Db_Table::getDefaultAdapter()->select()
              ->from('engine4_core_pages', 'name')
              ->where('page_id = ?', $page_id)
              ->limit(1)
              ->query()
              ->fetchColumn();
      if($pageName) {
        $explode = explode('sesblog_index_', $pageName);
        if(count($explode) > 0 && is_numeric($explode[1])) {
          $type = Engine_Db_Table::getDefaultAdapter()->select()
                ->from('engine4_sesblog_integrateothermodules', 'content_type')
                ->where('integrateothermodule_id = ?', $explode[1])
                ->limit(1)
                ->query()
                ->fetchColumn();
          if($type) {
            $value['resource_type'] = $type;
          }
        }
      }
    }
    $this->view->type = $type;
    // Blog browse page work
		
    $value['status'] = 1;
    $value['search'] = 1;

    if(isset($org_subject_id) && !empty($org_subject_id))
      $value['org_id'] = $org_subject_id;
    if(isset($project_subject_id) && !empty($project_subject_id))
      $value['project_id'] = $project_subject_id;
     
    $this->view->widgetName = 'browse-blogs';			
    $params = array_merge($params, $value,array('search'=>addslashes($text)));

    $value['draft'] = "0";
    $value['visible'] = "1";
    // Get blogs
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsPaginator($params, $form->getValues());
  
    // Set item count per page and current page number
    $limit_data = $this->view->{'limit_data_'.$view_type};
    $paginator->setItemCountPerPage($limit_data);
    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);
    unset($params['text']);
    $this->view->params = $params;
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    } else {
      // Do not render if nothing to show
      if ($paginator->getTotalItemCount() <= 0) {
        //silence
      }
    }
    
  }
}
