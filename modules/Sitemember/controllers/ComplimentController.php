<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ComplimentController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_ComplimentController extends Core_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

       
    }
    function indexAction() {
        $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
    }
    //ACTION FOR WRITE A REVIEW
    public function createAction() {

        $subject_id = $this->_getParam('subject_id');
        $subject_type = $this->_getParam('subject_type');
        if(empty($subject_id) || empty($subject_type)){
            $this->view->error = true;
            return;
        }
        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER SUBJECT
        $item = Engine_Api::_()->getItem($subject_type,$subject_id);
        if(empty($item) || empty($viewer_id) ){
            $this->view->error = true;
            return;
        }
        $table = Engine_Api::_()->getDbtable('complimentCategories', 'sitemember');
        
        $select = $table->select()
                    ->order('order ASC');
        $this->view->complimentIcons = $table->fetchAll($select);
    
        $this->view->form = $form = new Sitemember_Form_CreateCompliment();
        if (!$this->getRequest()->isPost()) {
          return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
          return;
        }
        $complimentTable = Engine_Api::_()->getDbtable('compliments','sitemember');
        $row = $complimentTable->createRow();
        $values = array(
            'resource_type' => $subject_type,
            'resource_id'   => $subject_id,
            'user_id'       => $viewer_id,
            'date'          => date('Y-m-d H:i:s')
        );
        $row->setFromArray(array_merge($values,$form->getValues()));
        $row->save();
        $this->_helper->redirector->gotoUrl($item->getHref(), array('prependBase' => false));
    }
    public function complimentsAction() {

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
    $values['has_photo'] = $this->_getParam('has_photo', 1);
    $this->view->compliment_category = $compliment_category = $this->_getParam('compliment_category'); 
    $this->view->complimentItem = Engine_Api::_()->getItem('sitemember_compliment_category',$compliment_category);
    $this->view->complimentTable = Engine_Api::_()->getDbTable('compliments','sitemember');
    $user_ids = Engine_Api::_()->getDbTable('compliments','sitemember')->getUserIdsByComplimentCategoryId(array("complimentcategory_id"=>$compliment_category));
    if(empty($user_ids)){
        return $this->setNoRender();
    } 
    $values["compliments"] = array_unique($user_ids);
     
    $this->view->sitemembers = Engine_Api::_()->sitemember()->getUsersSelect($values);
    $this->view->count = count($this->view->sitemembers);
    $this->view->vertical = $_GET['vertical'];
    $this->view->title_truncation = $this->_getParam('title_truncation', 50);
    $this->view->itemViewType = $this->_getParam('itemViewType', 0);
    $this->view->customParams = $this->_getParam('customParams', 5);
    $this->view->blockHeight = $this->_getParam('blockHeight', 245);
    $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    $this->view->titlePosition = $this->_getParam('titlePosition', 1);
    $this->view->custom_field_title = $this->_getParam('custom_field_title', 0);
    $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 0);
    $this->view->circularImage = $this->_getParam('circularImage', 0);
    $this->view->circularImageHeight =$this->_getParam('circularImageHeight', 190);
  }

}