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
class Sitecrowdfunding_Widget_ProjectDiscussionController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $sitecrowdfundingProjectDiscussion = Zend_Registry::isRegistered('sitecrowdfundingProjectDiscussion') ? Zend_Registry::get('sitecrowdfundingProjectDiscussion') : null;
        //WHO CAN POST THE DISCUSSION
        $this->view->canPost = Engine_Api::_()->authorization()->isAllowed($project, $viewer, 'topic');

        //GET PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitecrowdfunding_topic')->getProjectTopics($project->getIdentity());

        //DONT RENDER IF NOTHING TO SHOW
        if (($paginator->getTotalItemCount() <= 0 && (!$viewer->getIdentity() || empty($this->view->canPost)))) {
            return $this->setNoRender();
        }
        if (empty($sitecrowdfundingProjectDiscussion))
            return $this->setNoRender();
        //ADD COUNT TO TITLE
        if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
            $this->_childCount = $paginator->getTotalItemCount();
        }

        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
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

    public function getChildCount() {
        return $this->_childCount;
    }

}
