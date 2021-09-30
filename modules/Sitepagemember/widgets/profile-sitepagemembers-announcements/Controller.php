<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Widget_ProfileSitepagemembersAnnouncementsController extends Seaocore_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction(){
    
    //DONT RENDER THIS IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return $this->setNoRender();
    }
    
    $pageannoucement = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.announcement' , 1);
    $sitepagememberGetAnnouncement = Zend_Registry::isRegistered('sitepagememberGetAnnouncement') ? Zend_Registry::get('sitepagememberGetAnnouncement') : null;
    if (empty($pageannoucement) || empty($sitepagememberGetAnnouncement)) {
			return $this->setNoRender();
    }
    
    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
   
    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    $this->view->user_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    // PACKAGE BASE PRIYACY END    
    $this->view->isajax = $is_ajax = $this->_getParam('isajax', '');
		if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
		$this->view->identity_temp = $this->view->identity;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagemember.profile-sitepagemembers-announcements', $sitepage->page_id, $layout);

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $this->view->allowView = true;
    } 

    $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitepage')->announcements(array('page_id' => $sitepage->page_id, 'limit' => $this->_getParam('itemCount', 3) , 'hideExpired' => 1), array('announcement_id', 'title', 'body', 'creation_date'));
    $this->_childCount = count($this->view->announcements);
		if ($this->_childCount <= 0) {
			return $this->setNoRender();
		}
		

		if(!$this->view->isajax) {
			$this->view->params = $this->_getAllParams();
			if ($this->_getParam('loaded_by_ajax', true)) {
				$this->view->loaded_by_ajax = true;
				if ($this->_getParam('is_ajax_load', false)) {
					$this->view->is_ajax_load = true;
					$this->view->loaded_by_ajax = false;
					if (!$this->_getParam('onloadAdd', false))
						$this->getElement()->removeDecorator('Title');
					$this->getElement()->removeDecorator('Container');
				} else { 
					return;
				}
			}
			$this->view->showContent = true;    
    }
    else {
      $this->view->showContent = true;
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}