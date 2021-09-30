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
class Sitepage_Widget_ProfileJoinedSitepageController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->subject_id = Engine_Api::_()->core()->getSubject()->getIdentity();

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    //GET SUBJECT AND CHECK AUTHENTICATION
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

		$values = array();
    $this->view->isajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->textShow = $this->_getParam('textShow', 'Verified');
    $this->view->showMemberText = $this->_getParam('showMemberText', 1);
    $this->view->pageAdminJoined = $pageAdminJoined = $this->_getParam('pageAdminJoined', 2);
    $this->view->category_id =  $values['category_id'] = $this->_getParam('category_id',0);
    if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    
		if($pageAdminJoined == 1) {

			//GET PAGES
			$adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($subject->getIdentity());
			//GET STUFF
			$ids = array();
			foreach ($adminpages as $adminpage) {
				$ids[] = $adminpage->page_id;
			}
			$values['adminpages'] = $ids;

			$onlymember = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($subject->getIdentity(), 'onlymember');
	
			$onlymemberids = array();
			foreach ($onlymember as $onlymembers) {
				$onlymemberids[] = $onlymembers->page_id;
			} 
			if (!empty($onlymemberids)) {
			$values['adminpages'] = array_merge($onlymemberids, $values['adminpages']);
			}
		}
		else {
			$onlymember = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($subject->getIdentity(), 'onlymember');
	
			$onlymemberids = array();
			foreach ($onlymember as $onlymembers) {
				$onlymemberids[] = $onlymembers->page_id;
			} 
			$values['onlymember'] = $onlymemberids;
						if (empty($onlymemberids)) {
			return $this->setNoRender();
			}
		}

		$values['type'] = 'browse';
		$values['orderby'] = 'creation_date';
	//	$values['type_location'] = 'manage';
		if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagemember'))  {
			$values['type_location'] = 'profilebrowsePage';
    }

    $this->view->paginator = $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //DONT RENDER IF NOTHING TO SHOW
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    //ADD COUNT IF CONFIGURED
    if ($paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    //PAGE-RATING IS ENABLE OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
    
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

  public function getChildCount() {
    return $this->_childCount;
  }

}
?>