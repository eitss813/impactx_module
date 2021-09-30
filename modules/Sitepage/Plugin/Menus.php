<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Plugin_Menus {

  public function canCreateSitepages() {

    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    // Must be able to view Sitepages
    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'view')) {
      return false;
    }

    // Must be able to create Sitepages
    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create')) {
      return false;
    }
    return true;
  }

  
  public function onMenuInitialize_SitepageSubpageGutterCreate($row) {

    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }
    
    if (!empty($subject->subpage)) {
			return false;
    }
    
    // Must be able to view Sitepages
    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'view')) {
      return false;
    }

    $subpageCreate = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'sspcreate');
    if (empty($subpageCreate) ){
			return false;
    }
    
		$isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sspcreate');
		if (empty($isPageOwnerAllow)) {
			return false;
		}
		
		if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
			return array(
        'label' => $row->label,
        'route' => 'sitepage_packages',
        'action' => 'index',
        'class' => 'buttonlink item_icon_sitepage',
        'params' => array(
          'parent_id' =>  $subject->getIdentity()
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'sitepage_general',
        'action' => 'create',
        'class' => 'buttonlink item_icon_sitepage',
        'params' => array(
          'parent_id' =>  $subject->getIdentity()
        ),
      );
		}
  }

  public function canViewSitepages($row) {

    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be able to view Sitepages
    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'view')) {
      return false;
    }
    
//     $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
//     if (empty($enableLocation)) { 
// 			return false;
// 	  }

    //Page location work for navigation show.
//     if ($row->params['route'] == 'sitepage_general' && $row->params['action'] == 'map') {
// 			$results = Engine_Api::_()->getDbtable('pages', 'sitepage')->getLocationCount();
// 			if (empty($results)) {
// 				return false;
// 			}
// 	  }
	  //End Page location work.

    return true;
  }

  // SHOWING LINK ON "USER HOME PAGE".
  public function onMenuInitialize_CoreMainSitepage($row) {

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (!empty($viewer)) {
      return array(
          'label' => $row->label,
          // 'icon' => $view->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/sitepage.png',
          'route' => 'sitepage_general',
      );
    }
    return false;
  }

  public function onMenuInitialize_SitepageGutterShare() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if(Engine_Api::_()->seaocore()->isSitemobileApp() && !$moduleEnabled){
       return false;
    }
    
    // Check share is enable/disable
    $can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.share', 1);

    if (!$viewer->getIdentity() || empty($can_share)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_sitepages_share buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterDelete() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'delete');
    if (empty($isManageAdmin)) {
      $can_delete = 0;
    } else {
      $can_delete = 1;
    }

    if (!$viewer->getIdentity() || empty($can_delete)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitepages_delete',
        'route' => 'sitepage_delete',
        'params' => array(
            'page_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterPublish() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (!$viewer->getIdentity() || empty($can_edit) || $subject->draft == 1) {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox icon_sitepage_publish',
        'route' => 'sitepage_publish',
        'params' => array(
            'page_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterPrint() {
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'print');
    if (empty($isManageAdmin)) {
      $can_print = 0;
    } else {
      $can_print = 1;
    }

    if (empty($can_print)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitepages_print',
        'target' => '_blank',
        'route' => 'sitepage_profilepage',
        'params' => array(
            'action' => 'print',
            'id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterTfriend() {
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'tfriend');
    if (empty($isManageAdmin)) {
      $can_tellfriend = 0;
    } else {
      $can_tellfriend = 1;
    }

    if (empty($can_tellfriend)) {
      return false;
    }

    $class = 'smoothbox buttonlink icon_sitepages_tellafriend';
    $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
      $class = 'buttonlink icon_sitepages_tellafriend';
    return array(
        'class' => $class,
        'route' => 'sitepage_profilepage',
        'params' => array(
            'action' => 'tell-a-friend',
            'id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterClaim() {
    $viewer = Engine_Api::_()->user()->getViewer();

//    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'view')) {
//      return false;
//    }
    $viewer_id = $viewer->getIdentity();

    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
      $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
      if (!empty($authorization))
        $level_id = $authorization->level_id;
    }

    $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', 'claim');
    if (empty($allow_claim)) {
      return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.claimlink', 1)) {
      return false;
    }

    $listmemberclaimsTable = Engine_Api::_()->getDbtable('listmemberclaims', 'sitepage');
    $listmemberclaimsTablename = $listmemberclaimsTable->info('name');
    $select = $listmemberclaimsTable->select()->from($listmemberclaimsTablename, array('count(*) as total_count'))
            ->where('user_id = ?', $subject->owner_id);
    $row = $listmemberclaimsTable->fetchAll($select);

    if (!empty($row[0]['total_count'])) {
      $total_count = 1;
    }

    if (empty($total_count) || $subject->owner_id == $viewer_id || empty($subject->userclaim) || empty($allow_claim)) {
      return false;
    }
		if($viewer_id){
    return array(
        'class' => 'smoothbox buttonlink icon_sitepages_claim',
        'route' => 'sitepage_claimpages',
        'params' => array(
            'action' => 'claim-page',
            'page_id' => $subject->getIdentity(),
        ),
    );
		} else{
		return array(
						'class' => 'buttonlink icon_sitepages_claim',
						'route' => 'user_login',
						'params' => array(
									'return_url' => '64-' . base64_encode($subject->getHref()),
						),
				);
		}
  }

  public function onMenuInitialize_SitepageGutterMessageowner() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    
    if(Engine_Api::_()->seaocore()->isSitemobileApp() && !$moduleEnabled){
       return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    if ($subject->owner_id == $viewer_id || empty($viewer_id)) {
      return false;
    }

    $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($showMessageOwner == 'none') {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox icon_sitepages_invite',
        'route' => 'sitepage_profilepage',
        'params' => array(
            'action' => 'message-owner',
            'page_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterOpen() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (!$viewer->getIdentity() || $subject->closed != 1 || empty($can_edit)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitepages_open',
        'route' => 'sitepage_close',
        'params' => array(
            'page_id' => $subject->getIdentity(),
            'closed' => 0,
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterClose() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (!$viewer->getIdentity() || $subject->closed != 0 || empty($can_edit)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitepages_close',
        'route' => 'sitepage_close',
        'params' => array(
            'page_id' => $subject->getIdentity(),
            'closed' => 1,
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterReport() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

    $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.report', 1);

    if (!$viewer->getIdentity() || empty($report)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_sitepages_report buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SitepageGutterEditdetail($row) {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK
    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitepageGutterEditoverview($row) {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK
    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitepageGutterEditstyle($row) {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK
    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitepageGutterEditlayout($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK

    $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();

    if (!empty($check)) {
      return $params;
    }
  }

  public function onMenuInitialize_SitepageMainClaim($row) {

    // Modify params
    $params = $row->params;
    return $params;
  }

  public function canViewClaims() {
    $viewer = Engine_Api::_()->user()->getViewer();
    //Must be able to view Sitepages
    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'view')) {
      return false;
    }

    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
      $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
      if (!empty($authorization))
        $level_id = $authorization->level_id;
    }

    $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', 'claim');

    if (!Engine_Api::_()->getApi('settings', 'core')->sitepage_claimlink || empty($allow_claim)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablename = $table->info('name');
    $select = $table->select()->from($tablename, array('count(*) as count'))->where($tablename . '.closed = ?', '0')
            ->where($tablename . '.approved = ?', '1')
            ->where($tablename . '.declined = ?', '0')
            ->where($tablename . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($tablename . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    $results = $table->fetchAll($select);
    if (!$results[0]['count']) {
      return false;
    }
    return true;
  }

  // START FOR PROMOTE WITH AN AD LINK
  public function onMenuInitialize_SitepageGutterPromotead($row) {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    // check if Communityad Plugin is enabled
    $sitepagecommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$sitepagecommunityadEnabled) {
      return false;
    }

		// check if it is upgraded version
    $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
		$adversion = $communityadmodulemodule->version;
    if(empty(Engine_Api::_()->seaocore()->checkVersion($adversion, '4.1.5'))) {
				return;
    }

    $sitepage = Engine_Api::_()->core()->getSubject();
    $ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled('sitepage');
    if (!$ismoduleads_enabled) {
      return false;
    }

    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    $select = $useradsTable->select();
    $select
            ->from($useradsName, array('userad_id'))
            ->where('resource_type = ?', 'sitepage')
            ->where('resource_id = ?', $sitepage->page_id)
            ->limit(1);
    $ad_exist = $useradsTable->fetchRow($select);
    if (!empty($ad_exist)) {
      return false;
    }

    //START OWNER CHECK
    $isOwner = Engine_Api::_()->sitepage()->isPageOwner($sitepage);
    if (!$isOwner) {
      return false;
    }
    //END OWNER CHECK
    // Modify params
    $params = $row->params;
    $params['params']['type'] = 'sitepage';
    $params['params']['type_id'] = $sitepage->getIdentity();
    return $params;
  }

    // START FOR ADD fAVOURITE
  public function onMenuInitialize_SitepageGutterFavourite($row) { 

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    //FOR SHOW ADD FAVOURITE LINK ON THE PAGE PROFILE PAGE
    $show_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addfavourite.show', 0);
    if (empty($show_link)) {
      return false;
    }

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject();

    $page_id = $sitepage->page_id;
    $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablename = $table->info('name');

    $select = $table->select()->from($tablename, array('count(*) as count'))
            ->where('owner_id = ?', $viewer_id)
            ->where($tablename . '.page_id <> ?', $page_id)
            //->where($tablename . '.owner_id  <> ?', $viewer_id)
            ->where($tablename . '.approved = 1')
            ->where($tablename . '.draft = ?', '1')
            //->group($tablename . '.owner_id')
            ->where($tablename . '.closed = ?', '0');
    $results = $table->fetchRow($select);
    $count = $results->count;

    if ($count < 1) {
      return false;
    }

    $check = Engine_Api::_()->getDbtable('favourites', 'sitepage')->isShow($sitepage->getIdentity());
    $table_favourites = Engine_Api::_()->getDbtable('favourites', 'sitepage');
    $tablename = $table->info('name');

    $select_content = $table_favourites->select()->where('owner_id = ?', $viewer_id);
    $content = $select_content->query()->fetchAll();

    if (!empty($content)) {
      //Started the select query
      $select = $table->select()
              ->from($tablename, 'page_id')
              ->where($tablename . '.page_id <> ?', $page_id)
              ->where($tablename . '.owner_id  =?', $viewer_id)
              ->where($tablename . '.approved = 1')
              ->where($tablename . '.draft = ?', '1')
              ->where($tablename . '.closed = ?', '0')
              ->where('NOT EXISTS (SELECT `page_id` FROM `engine4_sitepage_favourites` WHERE `page_id_for`=' . $page_id . ' AND `page_id` = ' . $tablename . '.`page_id`) ');
      $content_result = $select->query()->fetchAll();
      $count_result1 = count($content_result);

      if (($count_result1 == 0)) {
        return false;
      }
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();

    if (!empty($check)) {
      return $params;
    } else {
      return $params;
    }
  }

  // END FOR ADD fAVOURITE
  // START FOR DELETE fAVOURITE
  public function onMenuInitialize_SitepageGutterFavouritedelete($row) {

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    $check = Engine_Api::_()->getDbtable('favourites', 'sitepage')->isnotShow($sitepage->getIdentity());

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();

    if (!empty($check)) {
      return $params;
    }
  }
  // END FOR DELETE fAVOURITE

	//ADD TO WISHLIST LINK
  public function onMenuInitialize_SitepageGutterWishlist($row) {
		
		//GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		if(empty($viewer_id)) {
			return false;
		}

    $canView = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepagewishlist_wishlist', 'view');
		if(empty($canView)) {
			return false;
		}

		//RETURN FALSE IF SUBJECT IS NOT SET
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepage_page') {
      return false;
    }

		//SHOW ADD TO WISHLIST LINK IF SITEPAGWISHLIST MODULES IS ENABLED
		$sitepageWishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagewishlist');
    if (!$viewer->getIdentity() || empty($sitepageWishlistEnabled)) {
      return false;
    }

    return array(
        'class' => 'icon_sitepagewishlist_add buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'sitepagewishlist',
            'controller' => 'index',
            'action' => 'add',
            'page_id' => $subject->getIdentity(),
        ),
    );
  }
  
  public function onMenuInitialize_SitepageSitegroupGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $sitegroupmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroup' ) ;
    if (empty($moduleEnabled) || empty($sitegroupmoduleEnabled)) {
			return false;
    }
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('sitegroup_group', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitegroup_group_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sitegroup_group_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'create')) {
      return false;
    }

		if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
			return array(
        'label' => $row->label,
        'route' => 'sitegroup_packages',
        'action' => 'index',
        'class' => 'buttonlink item_icon_sitegroup',
        'params' => array(
          'page_id' =>  $sitepage->page_id
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'sitegroup_general',
        'action' => 'create',
        'class' => 'buttonlink item_icon_sitegroup',
        'params' => array(
          'page_id' =>  $sitepage->page_id
        ),
      );
		}
  }
  
  public function onMenuInitialize_SitepageSitebusinessGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $sitebusinessmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitebusiness' ) ;
    if (empty($moduleEnabled) || empty($sitebusinessmoduleEnabled)) {
			return false;
    }
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('sitebusiness_business', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitebusiness_business_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sitebusiness_business_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }
    if (!Engine_Api::_()->authorization()->isAllowed('sitebusiness_business', $viewer, 'create')) {
      return false;
    }

		if (Engine_Api::_()->sitebusiness()->hasPackageEnable()) {
			return array(
        'label' => $row->label,
        'route' => 'sitebusiness_packages',
        'action' => 'index',
        'class' => 'buttonlink item_icon_sitebusiness',
        'params' => array(
          'page_id' =>  $sitepage->page_id
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'sitebusiness_general',
        'action' => 'create',
        'class' => 'buttonlink item_icon_sitebusiness',
        'params' => array(
          'page_id' =>  $sitepage->page_id
        ),
      );
		}
  }
  
  public function onMenuInitialize_SitepageDocumentGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'document' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('document', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "document_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'document_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('document', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageFolderGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'folder' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('folder', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "folder_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'folder_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageQuizGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'quiz' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('quiz', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "quiz_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'quiz_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('quiz', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageListGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'list' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('list_listing', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "list_listing_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'list_listing_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }

    if (!Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageTutorialGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $sitetutorialmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitetutorial' ) ;
    if (empty($moduleEnabled) || empty($sitetutorialmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('sitetutorial_tutorial', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitetutorial_tutorial_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sitetutorial_tutorial_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitetutorial_tutorial', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageFaqGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $sitefaqmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitefaq' ) ;
    if (empty($moduleEnabled) || empty($sitefaqmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('sitefaq_faq', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitefaq_faq_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sitefaq_faq_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitefaq_faq', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  public function sitepagesitereviewGutterCreate($row) {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $sitereviewmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitereview' ) ;
    if (empty($moduleEnabled) || empty($sitereviewmoduleEnabled)) {
			return false;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    //GET LISTING TYPE ID
    $listingtype_id = $row->params['listing_id'];
    $listingType = Engine_Api::_()->getItem('sitereview_listingtype',$listingtype_id);
    $titleSinUc = ucfirst($listingType->title_singular);
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitereview_listing_$listingtype_id")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, "sitereview_listing_$listingtype_id");
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }

    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('sitereview_listing', $listingtype_id);
    if (empty($item_enabled)) {
			return false;
    }
    
    //MUST BE ABLE TO VIEW LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "view_listtype_$listingtype_id")) {
      return false;
    }

    //MUST BE ABLE TO CRETE LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "create_listtype_$listingtype_id")) {
      return false;
    }

		$sitereviewpaidlistingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewpaidlisting');
		if($sitereviewpaidlistingEnabled) {
			$total_packages = Engine_Api::_()->getDbtable('packages', 'sitereviewpaidlisting')->getTotalPackage($listingtype_id, 1);
		}
    
		if($sitereviewpaidlistingEnabled && $listingType->package && $total_packages == 1) {
			$package = Engine_Api::_()->getDbTable('packages', 'sitereviewpaidlisting')->getEnabledPackage($listingtype_id);			
			return array(
        'label' => "Post New $titleSinUc Listings",
        'route' => "sitereview_general_listtype_$listingtype_id",
        'action' => 'create',
        'class' => $row->params['class'],
        'params' => array(
          'id' => $package->package_id,
          'page_id' =>  $sitepage->page_id,
        ),
      );
		} elseif($sitereviewpaidlistingEnabled && $listingType->package && $total_packages > 1) {
			return array(
        'label' => "Post New $titleSinUc Listings",
        'route' => "sitereview_all_package_listtype_$listingtype_id",
        'action' => 'index',
        'class' => $row->params['class'],
        'params' => array(
          'page_id' =>  $sitepage->page_id,
        ),
      );
			
		} else {
			return array(
        'label' => "Post New $titleSinUc Listings",
        'route' => "sitereview_general_listtype_$listingtype_id",
        'action' => 'create',
        'class' => $row->params['class'],
        'params' => array(
          'page_id' =>  $sitepage->page_id,
        ),
      );
		}
  }
  
  public function onMenuInitialize_SitepageSitestoreproductGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
    $sitestoreproductmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestoreproduct' ) ;
    if (empty($moduleEnabled) || empty($sitestoreproductmoduleEnabled)) {
			return false;
    }
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getItemsEnabled('sitestoreproduct_product', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitestoreproduct_product_0")) {
        return false;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sitestoreproduct_product_0');
      if (empty($isPageOwnerAllow)) {
        return false;
      }
    }
    
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, 'create')) {
      return false;
    }
    
    if(!empty($sitepage->owner_id)) {
			$store = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreId($sitepage->owner_id);
		}
		
	  if(count($store) == 0) {
			return false;
		}
		
		if($sitestoreproductmoduleEnabled) {
			$authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store[0]['store_id']);
			if(empty($authValue)) {
				return false;
			}
		}
		
		if(count($store) == 1) {
			$store_id = $store[0]['store_id'];
			return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_general',
        'action' => 'create',
        'class' => 'buttonlink seaocore_icon_add',
        'params' => array(
          'page_id' =>  $sitepage->page_id,
          'store_id' => $store_id
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'default',
        'module' => 'sitepageintegration',
        'controller' => 'index',
        'action' => 'storeintegration',
        'class' => 'buttonlink seaocore_icon_add smoothbox',
        'params' => array(
          'resource_id' =>  $sitepage->page_id
        ),
      );
		}
  }
  
}