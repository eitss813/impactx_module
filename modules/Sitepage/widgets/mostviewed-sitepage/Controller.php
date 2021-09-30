<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Widget_MostviewedSitepageController extends Engine_Content_Widget_Abstract 
{
  public function indexAction() 
  {
    $params =array();
    $current_time = date("Y-m-d H:i:s");
    $params['totalpages'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['featured'] = $this->_getParam('featured',0);
    $params['sponsored'] = $this->_getParam('sponsored',0);
    $interval = $this->_getParam('interval', 'overall');
    
			//MAKE TIMING STRING
		if($interval == 'week') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" ;
		}
		elseif($interval == 'month') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
		}
		else {
			$sqlTimeStr = '';
		} 
    $params['sqlTimeStr'] = $sqlTimeStr;
    
    //GET SITEPAGE FOR MOST VIEWED
    $this->view->sitepages = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Most Viewed List',$params,$interval, $sqlTimeStr, array('page_id', 'photo_id', 'page_url', 'title', 'owner_id', 'view_count'));

    if (!(count($this->view->sitepages) > 0)) { 
			return $this->setNoRender();
		}
  }
}

?>