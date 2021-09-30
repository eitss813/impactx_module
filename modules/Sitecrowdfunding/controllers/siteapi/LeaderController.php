<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_LeaderController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        //SET THE SUBJECT
        if (0 !== ($project_id = (int) $this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
        }
    }

    /*
     * Get All Leader of this project .
     * Return Json
     */

    public function manageLeadersAction() {
        
        $response=array();
        $this->validateRequestMethod();
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError("unauthorized");

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError('no_record');
        }
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PROJECT SUBJECT
        $project = Engine_Api::_()->core()->getSubject();
        $project_id = $project->project_id;
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized', "You don't have permission to manage admin of this Project.");
        }
        $leader = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1);
        if (!$leader) {
            $this->respondWithError('unauthorized', "You do not have permission to view Admins.");
        }

        try {

            $list = $project->getLeaderList();
            $list_id = $list['list_id'];
            $auth = Engine_Api::_()->authorization()->context;

            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
            $listItemTableName = $listItemTable->info('name');

            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userTableName = $userTable->info('name');
            $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $project->owner_id;

            $select = $userTable->select()
                    ->from($userTableName)
                    ->where("$userTableName.user_id IN (?)", (array) $selectLeaders)
                    ->order('displayname ASC');

            $members = $userTable->fetchAll($select);
            foreach ($members as $member) {
                $browseLeader = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($member);
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($member);
                $browseLeader = array_merge($browseLeader, $getContentImages);
                $browseLeader['is_owner'] = false;
                if ($project->owner_id == $member->user_id) {
                    $browseLeader['is_owner'] = true;
                }
                $manu = array();
                if ($project->owner_id != $member->user_id) {
                    $manu[] = array(
                        "name" => "remove_leader",
                        "label" => "Remove as Admin",
                        "url" => "sitecrowdfunding/leader/remove/" . $project->getIdentity(),
                        "urlParams" => array(
                            "user_id" => $member->getIdentity()
                        )
                    );
                }
                $browseLeader['menu'] = $manu;
                $response['response'][] = $browseLeader;
            }
            $response['totalItemCount'] = count($members);
            $response['canCreate'] = $editPrivacy;
            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Add leader for this project .
     * Return Success code or failure code
     */
    
    public function addLeaderAction() {
        $this->validateRequestMethod('POST');
        $user_id = $this->getRequestParam('user_id', 0);
        if (empty($user_id)) {
            $this->respondWithError('unauthorized', "user_id is not found");
        }

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError("unauthorized");

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError('no_record');
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $project = Engine_Api::_()->core()->getSubject();
        $project_id = $project->project_id;
        $editPrivacy = $project->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            $this->respondWithError('unauthorized', "You don't have permission to manage admin of this Project.");
        }
        $leader = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1);
        if (!$leader) {
            $this->respondWithError('unauthorized', "This service is disabled by admin.Please contact your admin");
        }

        try {
            $user = Engine_Api::_()->getItem('user', $user_id);
            $list = $project->getLeaderList();
            $list_id = $list['list_id'];
            $auth = Engine_Api::_()->authorization()->context;

            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
            $listItemTableName = $listItemTable->info('name');

            $userTable = Engine_Api::_()->getDbtable('users', 'user');

            //RETURN IF USER IS ALREADY A LEADER: A CASE WHEN WE CLICK MULTIPLE TIMES
            if ($list->has($user)) {
                $this->respondWithError('unauthorized', "All ready added.");
            }

            $table = $list->getTable();
            $db = $table->getAdapter();
            $db->beginTransaction();


            $list->add($user);
            // Add notification
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($user, $viewer, $project, 'sitecrowdfunding_create_leader');

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $activityApi->addActivity($user, $project, 'sitecrowdfunding_promote');

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Remove leader from this project .
     * Return Success code or failure code
     */
    
    public function removeAction() {
        $this->validateRequestMethod('POST');
        $multipleLeaderSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.leader', 1);

        if (empty($multipleLeaderSetting)) {
            $this->respondWithError('unauthorized', "This service is disabled by admin.Please contact your admin");
        }
        $user_id = $this->_getParam('user_id', 0);
        $user = Engine_Api::_()->getItem('user', $user_id);
        // Get user
        if (empty($user)) {
            $this->respondWithError('no_record');
        }

        $project = Engine_Api::_()->core()->getSubject();
        $list = $project->getLeaderList();

        $table = $list->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $list->remove($user);
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

     /*
     * Suggest User .
     * Return Json
     */
    
    public function leaderAutoSuggestAction() {
        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError("unauthorized", "You are logout user.");
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError("no_record");
        }
        //GET PROJECT SUBJECT
        $project = Engine_Api::_()->core()->getSubject();

        //GETTING THE PAGE ID.
        $project_id = $this->_getParam('project_id');
        try {

            $list = $project->getLeaderList();

            $list_id = $list['list_id'];
            $text = $this->_getParam('search', null);

            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
            $listItemTableName = $listItemTable->info('name');

            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userTableName = $userTable->info('name');
            $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $project->owner_id;

            $select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($userTableName);

            $select = $select->where("$userTableName.user_id NOT IN (?)", (array) $selectLeaders);
            $select = $select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ? OR " . $userTableName . ".email LIKE ?", '%' . $text . '%')
                    ->group("$userTableName.user_id")
                    ->order('displayname ASC')
                    ->limit(20);

            //FETCH ALL RESULT.
            $userlists = $userTable->fetchAll($select);
            $data = array();

            foreach ($userlists as $userlist) {
                $content_photo = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($userlist);
                $data[] = array(
                    'user_id' => $userlist->user_id,
                    'label' => $userlist->displayname,
                    'photo' => $content_photo['image_icon'],
                );
            }
            $response['response'] = $data;
            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

}

?>
