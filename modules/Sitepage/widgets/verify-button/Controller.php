<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_VerifyButtonController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $verifyenabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.enabled', 0);

    //CHECK FOR USER LOGGED IN OR NOT
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    if (empty($verifyenabled) || empty($viewer_id)) 
        return $this->setNoRender();

    // WE GET RESOURCE ID,RESOURCE TYPE AND RESOURCE TITLE
    $subject = Engine_Api::_()->core()->getSubject();

    $this->view->resource_id = $resource_id = $subject->getIdentity();
    $this->view->resource_owner_id = $resource_owner_id = $subject->owner_id;
    $this->view->resource_title = $subject->getTitle();
    
    if($viewer_id == $resource_owner_id ) {
      return $this->setNoRender();
    }

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $authorizationApi = Engine_Api::_()->authorization();
    $this->view->allowVerify = $allowVerify = $authorizationApi->isAllowed('sitepage_page', $viewer, 'page_verify');

    // Member level based check. Should be applied.
    if (!$allowVerify) {
      return $this->setNoRender();
    }

    //TO CHECK CURRENT VIEWING USER HAS BEEN VERIFIED OR NOT
    $verifyTable = Engine_Api::_()->getDbtable('verifies', 'sitepage');
    $this->view->hasVerified = $hasVerified = $verifyTable->hasVerify($resource_id);

    if (!empty($hasVerified)) {
      $this->view->admin_approve = $hasVerified->admin_approve;
      $this->view->verify_id = $hasVerified->verify_id;
    }

    //TO COUNT NO OF USERS WHO VERIFIED CURRENT VIEWING USER
    $this->view->verify_count = $verifyTable->getVerifyCount($resource_id);
    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.comment.verifypopup', 0);
    $this->view->verify_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3);

    if($viewer_id == $resource_owner_id && $this->view->verify_count <= 0) {
      return $this->setNoRender();
    }
    
  }

}