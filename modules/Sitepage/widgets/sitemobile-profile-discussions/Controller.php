<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

class Sitepage_Widget_SitemobileProfileDiscussionsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE PAGES
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() == 'sitepage_page') {
      return $this->setNoRender();
    } else {
      if ($subject->getType() == 'sitepageevent_event') {
        $sitepage = $subject->getParentPage();
      } elseif ($subject->getType() == 'sitepagemusic_playlist') {
        $sitepage = $subject->getParentType();
      } else {
        $sitepage = $subject->getParent();
      }
    }

    if (!$sitepage || $sitepage->getType() !== 'sitepage_page') {
      return $this->setNoRender();
    }

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    //GET PAGE ID
    $this->view->page_id = $sitepage->page_id;

    //START PACKAGE LEVEL CHECK
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE LEVEL CHECK

    $this->view->canPost = $topicComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');

    if (empty($topicComment)) {
      return $this->setNoRender();
    }

    //GET CURRENT PAGE NUMBER     
    $page = $this->_getParam('page', 1);

    //GET PAGINATORS
    $this->view->paginators = $paginators = Engine_Api::_()->getDbtable('topics', 'sitepage')->getPageTopics($sitepage->page_id, array('resource_type' => $subject->getType(), 'resource_id' => $subject->getIdentity()));
    $paginators->setItemCountPerPage(100)->setCurrentPageNumber($page);

    //ADD COUNT TO TITLE IF CONFIGURED
    if ($this->_getParam('titleCount', false) && $paginators->getTotalItemCount() > 0) {
      $this->_childCount = $paginators->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}