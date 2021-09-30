<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Menus.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Plugin_Menus {

  public function canCreateSesblogs() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() )
    return false;

    // Must be able to create sesblogs
    if( !Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'create') )
    return false;

    return true;
  }

  public function canBlogsContributors() {

    if(!Engine_Api::_()->getDbtable("modules", "core")->isModuleEnabled("sesmember"))
      return false;
    else
      return true;
  }
  
  public function onMenuInitialize_SesblogMainManagePackage($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 0)) {
      return false;
    }
    return true;
  }

  public function canViewSesblogsRequest() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() )
    return false;

    // Must be able to create sesblogs
    if( !Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'create') )
    return false;

    return true;
  }

  public function canViewSesblogs() {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view sesblogs
    if( !Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'view') )
    return false;

    return true;
  }

  public function canViewRssblogs() {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view sesblogs
    if( !Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'view') )
    return false;

    return true;
  }

  public function canClaimSesblogs() {

    // Must be able to view sesblogs
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() )
    return false;
    if(Engine_Api::_()->authorization()->getPermission($viewer, 'sesblog_claim', 'create') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.claim', 1))
    return true;

    return false;
  }

  public function onMenuInitialize_SesblogQuickStyle($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    //if( $request->getParam('module') != 'sesblog' || $request->getParam('action') != 'manage' )
    return false;

    // Must be able to style sesblogs
    if( !Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'style') )
    return false;

    return true;
  }

  public function onMenuInitialize_SesblogGutterList($row) {
    if( !Engine_Api::_()->core()->hasSubject() )
    return false;

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof User_Model_User ) {
      $user_id = $subject->getIdentity();
    } else if( $subject instanceof Sesblog_Model_Blog ) {
      $user_id = $subject->owner_id;
    } else {
      return false;
    }

		return array(
        'label' => 'View All User Blogs',
				'class'=>'buttonlink icon_sesblog_viewall',
        'icon' => 'application/modules/Sesbasic/externals/images/edit.png',
        'route' => 'sesblog_general',
        'params' => array(
            'action' => 'browse',
            'user_id' => $user_id,
        )
    );

  }

  public function onMenuInitialize_SesblogGutterShare($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1))
    return false;

    if( !Engine_Api::_()->core()->hasSubject() )
    return false;

    $subject = Engine_Api::_()->core()->getSubject();
    if( !($subject instanceof Sesblog_Model_Blog) )
    return false;

    // Modify params
    $params = $row->params;
    $params['params']['type'] = $subject->getType();
    $params['params']['id'] = $subject->getIdentity();
    $params['params']['format'] = 'smoothbox';
    return $params;
  }

  public function onMenuInitialize_SesblogGutterReport($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1))
    return false;

    if( !Engine_Api::_()->core()->hasSubject() )
    return false;

    $subject = Engine_Api::_()->core()->getSubject();
    if( ($subject instanceof Sesblog_Model_Blog) &&
        $subject->owner_id == $viewer->getIdentity() ) {
      return false;
    } else if( $subject instanceof User_Model_User &&
        $subject->getIdentity() == $viewer->getIdentity() ) {
      return false;
    }

    // Modify params
    $subject = Engine_Api::_()->core()->getSubject();
    $params = $row->params;
    $params['params']['subject'] = $subject->getGuid();
    return $params;
  }

  public function onMenuInitialize_SesblogGutterSubscribe($row) {

    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    return false;

    // Check subject
    if( !Engine_Api::_()->core()->hasSubject() )
    return false;

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof Sesblog_Model_Blog ) {
      $owner = $subject->getOwner('user');
    } else if( $subject instanceof User_Model_User ) {
      $owner = $subject;
    } else {
      return false;
    }

    if( $owner->getIdentity() == $viewer->getIdentity() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.subscription', 1))
    return false;

    // Modify params
    $params = $row->params;
    $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sesblog');
    if( !$subscriptionTable->checkSubscription($owner, $viewer) ) {
      $params['label'] = 'Subscribe';
      $params['params']['user_id'] = $owner->getIdentity();
      $params['action'] = 'add';
      $params['class'] = 'buttonlink smoothbox icon_sesblog_subscribe';
    } else {
      $params['label'] = 'Unsubscribe';
      $params['params']['user_id'] = $owner->getIdentity();
      $params['action'] = 'remove';
      $params['class'] = 'buttonlink smoothbox icon_sesblog_unsubscribe';
    }
    return $params;
  }

  public function onMenuInitialize_SesblogGutterCreate($row) {

    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $owner = Engine_Api::_()->getItem('user', $request->getParam('user_id'));

    if( $viewer->getIdentity() != $owner->getIdentity() )
    return false;

    if( !Engine_Api::_()->authorization()->isAllowed('sesblog_blog', $viewer, 'create') )
    return false;

    return true;
  }

  public function onMenuInitialize_SesblogGutterSubblogCreate($row) {

    if( !Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.subblog', 1))
    return false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $sesblog = Engine_Api::_()->core()->getSubject('sesblog_blog');
    $isBlogAdmin = Engine_Api::_()->sesblog()->isBlogAdmin($sesblog, 'edit');
    if( !$isBlogAdmin)
    return false;

    $params = $row->params;
    $params['params']['parent_id'] = $sesblog->blog_id;
    return $params;
  }

  public function onMenuInitialize_SesblogGutterStyle($row){
			return true;
	}
	public function onMenuInitialize_SesblogGutterEdit($row){
		 return true;
	}
  public function onMenuInitialize_SesblogGutterDashboard($row) {

    if( !Engine_Api::_()->core()->hasSubject())
    return false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $sesblog = Engine_Api::_()->core()->getSubject('sesblog_blog');
    $isBlogAdmin = Engine_Api::_()->sesblog()->isBlogAdmin($sesblog, 'edit');
    if( !$isBlogAdmin)
    return false;

    // Modify params
    $params = $row->params;
    $params['params']['blog_id'] = $sesblog->custom_url;
    return $params;
  }

  public function onMenuInitialize_SesblogGutterDelete($row) {

    if( !Engine_Api::_()->core()->hasSubject())
    return false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $sesblog = Engine_Api::_()->core()->getSubject('sesblog_blog');

    if( !$sesblog->authorization()->isAllowed($viewer, 'delete'))
    return false;

    // Modify params
    $params = $row->params;
    $params['params']['blog_id'] = $sesblog->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SesblogreviewProfileEdit() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject();

    if($review->owner_id != $viewer->getIdentity())
	  return false;

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.review', 1))
    return false;

    if (!$viewer->getIdentity())
    return false;

    if (!$review->authorization()->isAllowed($viewer, 'edit'))
    return false;

    return array(
        'label' => 'Edit Review',
        'route' => 'sesblogreview_view',
        'params' => array(
            'action' => 'edit',
            'review_id' => $review->getIdentity(),
            'slug' => $review->getSlug(),
        )
    );
  }

  public function onMenuInitialize_SesblogreviewProfileReport() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject();

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.show.report', 1) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.report', 1))
    return false;

    if($review->owner_id == $viewer->getIdentity())
	  return false;

    if (!$viewer->getIdentity())
    return false;

    return array(
        'label' => 'Report',
        'icon' => 'application/modules/Sesbasic/externals/images/report.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $review->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesblogreviewProfileShare() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject();

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.share', 1) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1))
    return false;

    if (!$viewer->getIdentity())
    return false;

    return array(
        'label' => 'Share',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $review->getType(),
            'id' => $review->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SesblogreviewProfileDelete() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject();

    if (!$viewer->getIdentity())
    return false;

    if($review->owner_id != $viewer->getIdentity())
	  return false;

		if (!$review->authorization()->isAllowed($viewer, 'delete'))
    return false;

    return array(
        'label' => 'Delete Review',
        'class' => 'smoothbox',
        'route' => 'sesblogreview_view',
        'params' => array(
            'action' => 'delete',
            'review_id' => $review->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function reviewEnable() {
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.review', 1) || !Engine_Api::_()->sesbasic()->getViewerPrivacy('sesblog_review', 'view')) {
      return false;
    }
    return true;
  }

  public function locationEnable() {
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.location', 1)) {
      return false;
    }

    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1))
      return false;
    return true;
  }

  public function onMenuInitialize_SesblogProfileMember()
  {

    $menu = array();

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'sesblog_blog' )
    {
      throw new Core_Model_Exception('Whoops, not a blog!');
    }

    if( !$viewer->getIdentity() )
    {
      return false;
    }

    $checkBlogUserAdmin = Engine_Api::_()->sesblog()->checkBlogUserAdmin($subject->getIdentity());
    if(empty($checkBlogUserAdmin))
        return false;

    $row = $checkBlogUserAdmin; //$subject->membership()->getRow($viewer);

    if($viewer->getIdentity() != $subject->owner_id) {
        if(!empty($row) && empty($row->resource_approved)) {

            $menu[] = array(
                'label' => 'Accept Admin Request',
                'class' => 'smoothbox sesblog_icon_accept buttonlink',
                'route' => 'sesblog_extended',
                'params' => array(
                    'controller' => 'index',
                    'action' => 'accept',
                    'blog_id' => $subject->getIdentity()
                ),
            );

            $menu[] =  array(
                'label' => 'Decline Admin Request',
                'class' => 'smoothbox sesblog_icon_reject buttonlink',
                'route' => 'sesblog_extended',
                'params' => array(
                    'controller' => 'index',
                    'action' => 'reject',
                    'blog_id' => $subject->getIdentity()
                ),
            );



        } else if(!empty($row) && !empty($row->resource_approved)) {

            $menu[] =  array(
                'label' => 'Remove As Admin',
                'class' => 'smoothbox sesblog_icon_reject buttonlink',
                'route' => 'sesblog_extended',
                'params' => array(
                    'controller' => 'index',
                    'action' => 'removeasadmin',
                    'blog_id' => $subject->getIdentity()
                ),
            );
        }
    }

    if( count($menu) == 1 ) {
      return $menu[0];
    }
    return $menu;
  }
}
