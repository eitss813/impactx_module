<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_ItemSitememberController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $user_id = $this->_getParam('user_id');
    if (empty($user_id)) {
      return $this->setNoRender();
    }
    $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
    $starttime = $this->_getParam('starttime');
    $endtime = $this->_getParam('endtime');
    $currenttime = date('Y-m-d H:i:s');

    if ((!empty($starttime) && $currenttime < $starttime) || (!empty($endtime) && $currenttime > $endtime)) {
      return $this->setNoRender();
    }

    //GET MEMBER OF THE DAY
    $this->view->sitemember = $sitemember = Engine_Api::_()->getItem('user', $user_id);
    if (empty($sitemember)) {
      return $this->setNoRender();
    }

    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $this->view->featured = $tableUserInfo->getColumnValue($user_id, 'featured');
    $this->view->sponsored = $tableUserInfo->getColumnValue($user_id, 'sponsored');
    $this->view->statistics = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField"));
    $this->view->customParams = $this->_getParam('customParams', 5);
    $this->view->custom_field_title = $this->_getParam('custom_field_title', 0);
    $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 0);
	$this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
  }

}