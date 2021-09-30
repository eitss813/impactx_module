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
class Sitepage_Widget_DiscussionContentController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE PAGES
  public function indexAction() { 	
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitepage_topic')) {
      return $this->setNoRender();
    }

    //SEND THE VIEWER TO THE TPL   
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //SEND THE TOPIC SUBJECT TO THE TPL
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    $order = $this->_getParam('postorder', 0);

    //GET THE SITEPAGE ITEM 
    $this->view->sitepage = $sitepage = $topic->getParentSitepage();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //GET COMMENT PRIVACY
    $this->view->canPost = $canPost = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');

    //GET EDIT PRIVACY
    $this->view->canEdit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    //INCREMENT IN VIEWS
    if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    //CHECK WAITING
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');
      $isWatching = $topicWatchesTable->isWatching($sitepage->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    }
    $this->view->isWatching = $isWatching;

    //@TODO IMPLEMENT SCAN TO POST
    $this->view->post_id = $post_id = (int) $this->_getParam('post');

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('posts', 'sitepage')->getPost($sitepage->getIdentity(), $topic->getIdentity(), $order);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));
    
//     //SKIP TO PAGE OF SPECIFIED POST
//     if (0 !== ($post_id = (int) $this->_getParam('post_id')) &&
//             null !== ($post = Engine_Api::_()->getItem('sitepage_post', $post_id))) {
//       $icpp = $paginator->getItemCountPerPage();
//       $page = ceil(($post->getPostIndex() + 1) / $icpp);
//       $paginator->setCurrentPageNumber($page);
//     }
// 
//     //USE SPECIFIED PAGE
//     else if (0 !== ($page = (int) $this->_getParam('page'))) {
//       $paginator->setCurrentPageNumber($this->_getParam('page'));
//     }

    if ($canPost && !$topic->closed) {
      $this->view->form = $form = new Sitepage_Form_Post_Create();
      $form->populate(array(
          'topic_id' => $topic->getIdentity(),
          'ref' => $topic->getHref(),
          'watch' => ( false === $isWatching ? '0' : '1' ),
      ));
    }
  }

}