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
class Sesblog_Widget_BlogLocationController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog_enable_location', 1))
      return $this->setNoRender();
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $limit = $this->_getParam('limit_data', 200);
    if (isset($_POST['searchParams']) && $_POST['searchParams'])
      parse_str($_POST['searchParams'], $searchArray);
    if (!$is_ajax)
      $value['locationWidget'] = true;


    $this->view->socialshare_enable_plusicon = $value['socialshare_enable_plusicon'] = isset($searchArray['socialshare_enable_plusicon']) ? $searchArray['socialshare_enable_plusicon'] : (isset($_GET['socialshare_enable_plusicon']) ? $_GET['socialshare_enable_plusicon'] : (isset($params['socialshare_enable_plusicon']) ? $params['socialshare_enable_plusicon'] : 1));

    $this->view->socialshare_icon_limit = $value['socialshare_icon_limit'] = isset($searchArray['socialshare_icon_limit']) ? $searchArray['socialshare_icon_limit'] : (isset($_GET['socialshare_icon_limit']) ? $_GET['socialshare_icon_limit'] : (isset($params['socialshare_icon_limit']) ? $params['socialshare_icon_limit'] : ''));


    $value['category_id'] = isset($searchArray['category_id']) ? $searchArray['category_id'] : (isset($_GET['category_id']) ? $_GET['category_id'] : (isset($params['category_id']) ? $params['category_id'] : ''));
    $value['sort'] = isset($searchArray['sort']) ? $searchArray['sort'] : (isset($_GET['sort']) ? $_GET['sort'] : (isset($params['sort']) ? $params['sort'] : $this->_getParam('sort', 'mostSPliked')));
    $value['subcat_id'] = isset($searchArray['subcat_id']) ? $searchArray['subcat_id'] : (isset($_GET['subcat_id']) ? $_GET['subcat_id'] : (isset($params['subcat_id']) ? $params['subcat_id'] : ''));
    $value['subsubcat_id'] = isset($searchArray['subsubcat_id']) ? $searchArray['subsubcat_id'] : (isset($_GET['subsubcat_id']) ? $_GET['subsubcat_id'] : (isset($params['subsubcat_id']) ? $params['subsubcat_id'] : ''));
    $value['search'] = 1;
    $value['location'] = isset($searchArray['location']) ? $searchArray['location'] : (isset($_GET['location']) ? $_GET['location'] : (isset($params['location']) ? $params['location'] : ''));
    $this->view->lat = $value['lat'] = isset($searchArray['lat']) ? $searchArray['lat'] : (isset($_GET['lat']) ? $_GET['lat'] : (isset($params['lat']) ? $params['lat'] : $this->_getParam('lat', '26.9110600')));
    $value['show'] = isset($searchArray['show']) ? $searchArray['show'] : (isset($_GET['show']) ? $_GET['show'] : (isset($params['show']) ? $params['show'] : ''));
    $this->view->lng = $value['lng'] = isset($searchArray['lng']) ? $searchArray['lng'] : (isset($_GET['lng']) ? $_GET['lng'] : (isset($params['lng']) ? $params['lng'] : $this->_getParam('lng', '75.7373560')));
    $value['miles'] = isset($searchArray['miles']) ? $searchArray['miles'] : (isset($_GET['miles']) ? $_GET['miles'] : (isset($params['miles']) ? $params['miles'] : $this->_getParam('miles', '1000')));
    $value['text'] = $text = isset($searchArray['search']) ? $searchArray['search'] : (!empty($params['search']) ? $params['search'] : (isset($_GET['search']) && ($_GET['search'] != '') ? $_GET['search'] : ''));
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
        case 'featured':
          $value['popularCol'] = 'is_featured';
          break;
        case 'sponsored':
          $value['popularCol'] = 'is_sponsored';
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
    $this->view->show_criterias = $show_criterias = isset($_POST['show_criterias']) ? json_decode($_POST['show_criterias'], true) : $this->_getParam('show_criteria', array('like', 'view', 'comment', 'favourite', 'ratingStar', 'rating', 'title', 'likeButton', 'favouriteButton', 'socialSharing', 'location', 'sponsoredLabel', 'featuredLabel', 'verifiedLabel'));

    foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getSesblogsPaginator($value, true);
    $paginator->setItemCountPerPage($limit);
    $paginator->setCurrentPageNumber(1);
    $this->view->widgetName = 'blog-location';
  }

}
