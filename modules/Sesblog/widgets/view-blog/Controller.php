<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Controller.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Widget_ViewBlogController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    
    $this->view->allParams = $allparams = $this->_getAllParams();
    
    $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('blog_id', null);
    $this->view->blog_id = Engine_Api::_()->getDbtable('blogs', 'sesblog')->getBlogId($id);
    
    if(!Engine_Api::_()->core()->hasSubject())
      $this->view->sesblog = $sesblog = Engine_Api::_()->getItem('sesblog_blog', $this->view->blog_id);
    else
      $this->view->sesblog = $sesblog = Engine_Api::_()->core()->getSubject();
      
    $this->view->owner = $owner = $sesblog->getOwner();
      
    $show_criterias = $this->_getParam('show_criteria', array('title', 'description', 'photo', 'socialShare', 'ownerOptions', 'rating', 'postComment', 'likeButton', 'favouriteButton', 'view', 'like', 'comment', 'review', 'statics','shareButton','smallShareButton'));
    if(is_array($show_criterias)){
      foreach ($show_criterias as $show_criteria)
      $this->view->{$show_criteria . 'Active'} = $show_criteria;
    }
    
	  $this->view->image_height = $this->_getParam('heightss','500');
    $this->view->socialshare_enable_plusicon = $this->_getParam('socialshare_enable_plusicon', 1);
    $this->view->socialshare_icon_limit = $this->_getParam('socialshare_icon_limit', 2);

    $sesblog_profilsesblogs = Zend_Registry::isRegistered('sesblog_profilsesblogs') ? Zend_Registry::get('sesblog_profilsesblogs') : null;
    if (empty($sesblog_profilsesblogs))
      return $this->setNoRender();

    if( !$sesblog->isOwner($viewer) ) {
      Engine_Api::_()->getDbtable('blogs', 'sesblog')->update(array('view_count' => new Zend_Db_Expr('view_count + 1')), array('blog_id = ?' => $sesblog->getIdentity()));
    }
    
    // Get tags
    $this->view->sesblogTags = $sesblog->tags()->getTagMaps();
    
    $this->view->coreSettings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $coreApi = Engine_Api::_()->sesblog();
    
    $this->view->isBlogAdmin = $coreApi->isBlogAdmin($sesblog, 'edit');
    $this->view->can_delete = $coreApi->isBlogAdmin($sesblog, 'delete');
    $this->view->reviewCount = $coreApi->getTotalReviews($sesblog->blog_id);
    $this->view->LikeStatus = $coreApi->getLikeStatus($sesblog->getIdentity(), $sesblog->getType());
    
    $this->view->likeClass = (!$this->view->LikeStatus) ? 'fa-thumbs-up' : 'fa-thumbs-down' ;
    $this->view->likeText = ($this->view->LikeStatus) ?  $this->view->translate('Unlike') : $this->view->translate('Like');

    $this->view->favStatus = Engine_Api::_()->getDbTable('favourites', 'sesblog')->isFavourite(array('resource_type'=>$sesblog->getType(), 'resource_id' => $sesblog->getIdentity())); 
    
    $this->view->canComment =  $sesblog->authorization()->isAllowed($viewer, 'comment');

    $this->view->isAllowReview = $coreSettings->getSetting('sesblog.allow.review', 1);
    $this->view->enableSharng = $coreSettings->getSetting('sesblog.enable.sharing', 1);

    // Get category
    if( !empty($sesblog->category_id) )
      $this->view->category = Engine_Api::_()->getDbtable('categories', 'sesblog')->find($sesblog->category_id)->current();

    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $style = $table->select()
                  ->from($table, 'style')
                  ->where('type = ?', 'user_sesblog')
                  ->where('id = ?', $owner->getIdentity())
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
    if( !empty($style) ) {
      try {
        $this->view->headStyle()->appendStyle($style);
      } catch (Exception $e) {
        if (APPLICATION_ENV === 'development') {
          throw $e;
        }
      }
    }
  }
}
