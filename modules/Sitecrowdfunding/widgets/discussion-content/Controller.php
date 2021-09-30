<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_DiscussionContentController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    //ACTION FOR FETCHING THE DISCUSSIONS FOR THE PROJECTS
    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_topic')) {
            return $this->setNoRender();
        }

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_topic')->getParent();
        $order = $this->_getParam('postorder', 0);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //SEND TAB ID TO THE TPL
        $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id');
        //GET TOPIC  SUBJECT
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();
        $this->view->canEdit = $topic->canEdit();
        //GET PROJECT OBJECT
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $topic->project_id);
        $sitecrowdfundingDiscussionContent = Zend_Registry::isRegistered('sitecrowdfundingDiscussionContent') ? Zend_Registry::get('sitecrowdfundingDiscussionContent') : null;
        //WHO CAN POST TOPIC
        $this->view->canPost = $canPost = $project->authorization()->isAllowed($viewer, "topic");

        //INCREASE THE VIEW COUNT
        if (!$viewer || !$viewer_id || $viewer_id != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

        //CHECK WATHCHING
        $isWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'sitecrowdfunding');
            $isWatching = $topicWatchesTable->isWatching($project->getIdentity(), $topic->getIdentity(), $viewer_id);
            if (false == $isWatching) {
                $isWatching = null;
            } else {
                $isWatching = (bool) $isWatching;
            }
        }
        if (empty($sitecrowdfundingDiscussionContent))
            return $this->setNoRender();
        $this->view->isWatching = $isWatching;

        //GET POST ID
        $this->view->post_id = $post_id = (int) $this->_getParam('post');

        $table = Engine_Api::_()->getDbtable('posts', 'sitecrowdfunding');
        $select = $table->select()
                ->where('project_id = ?', $project->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity());

        if ($order == 1) {
            $select->order('creation_date DESC');
        } else {
            $select->order('creation_date ASC');
        }

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));
        if ($canPost && !$topic->closed) {
            $this->view->form = $form = new Sitecrowdfunding_Form_Post_Create();
            $form->populate(array(
                'topic_id' => $topic->getIdentity(),
                'ref' => $topic->getHref(),
                'watch' => ( false == $isWatching ? '0' : '1' ),
            ));
        }
    }

}
