<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_PrivacyController extends Core_Controller_Action_Standard
{

    public function editPrivacyAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editprivacy';
        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Privacy();
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        $tableProjects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');

        $leaderList = $project->getLeaderList();
        //SAVE PROJECT ENTRY
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $projectTags = $project->tags()->getTagMaps();
            $tagString = '';

            foreach ($projectTags as $tagmap) {
                $temp = $tagmap->getTag();
                if (!empty($temp)) {
                    if ($tagString != '')
                        $tagString .= ', ';
                    $tagString .= $tagmap->getTag()->getTitle();
                }
            }

            $this->view->tagNamePrepared = $tagString;
            if (isset($form->tags))
                $form->tags->setValue($tagString);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($project, $role, "view")) {
                        $form->auth_view->setValue($roleString);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($project, $role, "comment")) {
                        $form->auth_comment->setValue($roleString);
                    }
                }
            }
            $ownerList = '';
            $roles_photo = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

            foreach ($roles_photo as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $sitecrowdfundingAllow = Engine_Api::_()->getApi('allow', 'sitecrowdfunding');
                if ($form->auth_topic && 1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'topic')) {
                    $form->auth_topic->setValue($roleString);
                }

                if (isset($form->auth_post) && $form->auth_post && 1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'post')) {
                    $form->auth_post->setValue($roleString);
                }
            }
            if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                if (empty($project->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                } else {
                    $form->networks_privacy->setValue(explode(",", $project->networks_privacy));
                }
            }

            // Set the member_invite, member_approval options
            $form->member_invite->setValue($tableProjects->getColumnValue($project_id, 'member_invite'));
            $form->member_approval->setValue($tableProjects->getColumnValue($project_id, 'member_approval'));

          //  $form->payment_action_label->setValue($tableProjects->getColumnValue($project_id, 'payment_action_label'));


            $notify_project_comment = $tableProjects->getColumnValue($project_id, 'notify_project_comment');
            $notify_project_donate = $tableProjects->getColumnValue($project_id, 'notify_project_donate');
            $is_user_followed_after_comment_yn = $tableProjects->getColumnValue($project_id, 'is_user_followed_after_comment_yn');
            $is_user_followed_after_donate_yn = $tableProjects->getColumnValue($project_id, 'is_user_followed_after_donate_yn');

            $form->notify_project_comment->setValue((explode(",",$notify_project_comment)));
            $form->notify_project_donate->setValue((explode(",",$notify_project_donate)));
            $form->is_user_followed_after_comment_yn->setValue($is_user_followed_after_comment_yn);
            $form->is_user_followed_after_donate_yn->setValue($is_user_followed_after_donate_yn);

            return;
        }


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            $notifyProjectCommentIdAsStr = null;
            $notifyProjectDonateIdAsStr = null;

            // check if user selected anyone in notification
            if(isset($values['notify_project_comment'])){
                $notifyProjectCommentIdAsArray = array();
                $notifyProjectCommentIdAsArray = $values['notify_project_comment'];
                $notifyProjectCommentIdAsStr = implode(",",$notifyProjectCommentIdAsArray);
            }

            // check if user selected anyone in notification
            if(isset($values['notify_project_donate'])){
                $notifyProjectDonateAsArray = array();
                $notifyProjectDonateAsArray = $values['notify_project_donate'];
                $notifyProjectDonateIdAsStr = implode(",",$notifyProjectDonateAsArray);
            }

            if (empty($values))
                return;

            $projectModel = $project;

            // Update the member_invite, member_approval options
            $tableProjects->update(array(
            //    'payment_action_label' =>  $values['payment_action_label'],
                'member_invite' => $values['member_invite'],
                'member_approval' => $values['member_approval'],
                'is_privacy_edited_yn' => 1,
                'notify_project_donate'  => $notifyProjectDonateIdAsStr,
                'notify_project_comment' => $notifyProjectCommentIdAsStr,
                'is_user_followed_after_comment_yn'  => $values['is_user_followed_after_comment_yn'],
                'is_user_followed_after_donate_yn' => $values['is_user_followed_after_donate_yn']
            ), array('project_id = ?' => $project_id));

            //PRIVACY WORK
            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $leaderList = $projectModel->getLeaderList();

            if (empty($values['auth_view'])) {
                $values['auth_view'] = "everyone";
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = "registered";
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {

                if ($role === 'leader') {
                    $role = $leaderList;
                }

                $auth->setAllowed($projectModel, $role, "view", ($i <= $viewMax));
                $auth->setAllowed($projectModel, $role, "comment", ($i <= $commentMax));
            }
            $ownerList = '';
            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            if (empty($values['auth_topic'])) {
                $values['auth_topic'] = "registered";
            }
            if (isset($values['auth_post']) && empty($values['auth_post'])) {
                $values['auth_post'] = "registered";
            }

            $topicMax = array_search($values['auth_topic'], $roles);
            $postMax = '';
            if (isset($values['auth_post']) && !empty($values['auth_post']))
                $postMax = array_search($values['auth_post'], $roles);

            foreach ($roles as $i => $role) {

                if ($role === 'leader') {
                    $role = $leaderList;
                }
                $auth->setAllowed($projectModel, $role, "topic", ($i <= $topicMax));
                if (!is_null( $postMax ) ) {
                    $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                }
            }
            // Create some auth stuff for all leaders
            $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
            $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
            $auth->setAllowed($projectModel, $leaderList, 'delete', 1);
            //UPDATE KEYWORDS IN SEARCH TABLE
            if (!empty($keywords)) {
                Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'sitecrowdfunding_project', 'id = ?' => $projectModel->project_id));
            }
            if (!empty($project_id)) {
                $projectModel->setLocation();
            }
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($projectModel) as $action) {
                $actionTable->resetActivityBindings($action);
            }
        }

    }
}