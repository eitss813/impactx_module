<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_IndexController extends Core_Controller_Action_Standard {

  public function homesponsoredAction() {

    $this->view->sponserdSitemembersCount = $limit_sitemember = $_GET['curnt_limit'];
    $limit_sitemember_horizontal = $limit_sitemember * 2;
    $values = array();
    $values = $this->_getAllParams();
	$this->view->links = $this->_getParam('links', array("addfriend", "message"));
	if(isset($values['links']) && isset($values['links']['no'])) {
		$this->view->links = $values['links'] = array();
	}

    //GET COUNT
    $totalCount = $_GET['total'];
    
    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'];
    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit_sitemember;
    }
    
    if ($startindex < 0) {
      $startindex = 0;
    }
    
    $this->view->showOptions = $this->_getParam('showOptions', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField"));
    
    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $this->view->direction = $_GET['direction'];
    $values['start_index'] = $startindex;
    $this->view->totalItemsInSlide = $values['limit'] = $limit_sitemember_horizontal;
    $values['orderby'] = $this->_getParam('orderby', 'creation_date');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
    if ($fea_spo == 'featured') {
      $values['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $values['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $values['sponsored'] = 1;
      $values['featured'] = 1;
    }
    $values['has_photo'] = $this->_getParam('has_photo', 1);
    $this->view->sitemembers = Engine_Api::_()->sitemember()->getUsersSelect($values);
    $this->view->count = count($this->view->sitemembers);
    $this->view->vertical = $_GET['vertical'];
    $this->view->title_truncation = $this->_getParam('title_truncation', 50);
    $this->view->customParams = $this->_getParam('customParams', 5);
    $this->view->blockHeight = $this->_getParam('blockHeight', 245);
    $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    $this->view->titlePosition = $this->_getParam('titlePosition', 1);
    $this->view->custom_field_title = $this->_getParam('custom_field_title', 0);
    $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 0);
    
	 $this->view->circularImage = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight =$this->_getParam('circularImageHeight', 190);
  }

  //ACTION FOR JOIN PAGE.
  public function viewMoreAction() {

    $this->view->user_id = $this->_getParam('user_id');
    $this->view->show = $this->_getParam('show');
    $this->view->subject = $subject = Engine_Api::_()->getItem('user', $this->view->user_id);
    $this->view->showViewMore = $this->_getParam('showViewMore', 0);

    if ($this->view->show == 'friends') {

      $select = $subject->membership()->getMembersOfSelect();
      $this->view->paginator = $friends = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage(20);
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $this->view->count = $paginator->getTotalItemCount();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->resource_id;
      }
      $this->view->friendIds = $ids;

      // Get the items
      $friendUsers = array();
      foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
        $friendUsers[$friendUser->getIdentity()] = $friendUser;
      }
      $this->view->friendUsers = $friendUsers;
    } else {
      $this->view->paginator = $paginator = Engine_Api::_()->seaocore()->getMutualFriend($subject->getIdentity(), 20);
      $paginator->setItemCountPerPage(20);
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $this->view->count = $paginator->getTotalItemCount();
    }
  }

}