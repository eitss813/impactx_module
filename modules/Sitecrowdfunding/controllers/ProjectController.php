<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_ProjectController extends Seaocore_Controller_Action_Standard {

    protected $_hasPackageEnable;

    public function init() {
        //SET THE SUBJECT
        if (0 !== ($project_id = (int) $this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    //THIS ACTION USED TO CREATE A Project
    public function createAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        $package_id = $this->_getParam('id', 0);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->level_id = $viewer->level_id;
        $this->view->parent_type = $parent_type = $this->_getParam('parent_type');
        $this->view->parent_id = $parent_id = $this->_getParam('parent_id');
        if (strstr($parent_type, 'sitereview_listing')) {
            $this->view->parent_type = $parent_type = 'sitereview_listing';
        }
        //RENDER PAGE
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;

        if ($this->_hasPackageEnable && empty($package_id)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'parent_type' => $parent_type, 'parent_id' => $parent_id), 'sitecrowdfunding_package', true);
        } else
            $package_id = 1; // Default Package for all projects

        $settings = Engine_Api::_()->getApi('settings', 'core');
        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        if ($this->_hasPackageEnable) {
            $this->view->packageInfoArray = $settings->getSetting('sitecrowdfunding.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "photos", "description"));
        }

        $this->view->viewFullPage = 0;
        if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitecrowdfunding.createFormFields')) {
            $createFormFields = $settings->getSetting('sitecrowdfunding.createFormFields');
            $this->view->viewFullPage = 1;
        }
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->level_id = $viewer->level_id;
        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitecrowdfunding')->defaultProfileId();
        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "create");
        if (empty($isCreatePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');
        $isParentCreatePrivacy = Engine_Api::_()->sitecrowdfunding()->isCreatePrivacy($parent_type, $parent_id);
        if (empty($isParentCreatePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');
        
        if ($parent_id && $parent_type)
            $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);
        
        //PACKAGE BASED CHECKS
        if ($this->_hasPackageEnable) {
            $this->view->overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 0);
            $this->view->viewer = Engine_Api::_()->user()->getViewer();
            //REDIRECT
            $package_id = $this->_getParam('id');
            if (empty($package_id)) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
            $this->view->package = $package = Engine_Api::_()->getItemTable('sitecrowdfunding_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
            if (empty($this->view->package)) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }

            if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
        }
        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create(
                array('defaultProfileId' => $defaultProfileId,
            'parentTypeItem' => $parentTypeItem,
        ));

        $form->populate(array(
            'return_url' => $this->_getParam('return_url'),
        ));
        //COUNT PROJECT CREATED BY THIS USER AND GET ALLOWED COUNT SETTINGS
        $values['owner_id'] = $viewer_id;
        $values['allProjects'] = 'all';
        $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($values);

        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "max");
        $this->view->category_count = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategories(array('category_id'), null, 1, 0, 1);
        $this->view->sitecrowdfunding_render = 'sitecrowdfunding_form';

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();
            if (empty($values))
                return;

            //CATEGORY IS REQUIRED FIELD
            if (empty($_POST['category_id'])) {
                $error = $this->view->translate('Please complete Category field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
            $db = $table->getAdapter();
            $db->beginTransaction();
            $user_level = $viewer->level_id;
            try {
                //Create Project
                if (!$this->_hasPackageEnable) {
                    //Create Project
                    $values = array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer_id,
                        //'featured' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitecrowdfunding_project', "featured"),
                        //'sponsored' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitecrowdfunding_project', "sponsored"),
                        //'approved' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitecrowdfunding_project', "approved"),
                        //'status' => 'active'
                        'featured' => 0,
                        'sponsored'=> 0,
                        'approved' => 0,
                        'status' => 'initial',
                        'state' => 'draft'
                    ));
                } else {
                    $values = array_merge($form->getValues(), array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer_id,
//                        'featured' => $package->featured,
//                        'sponsored' => $package->sponsored,
                        // 'approved' => $package->isFree() ? $package->approved : 0,
                        // 'status' => $package->isFree() ? 'active' : 'initial'
                        'featured'=> 0,
                        'sponsored' => 0,
                        'approved' => 0,
                        'status' => 'initial',
                        'state' => 'draft'
                    ));
                }
                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }

                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }

                if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            unset($values['networks_privacy']);
                        } else {
                            $values['networks_privacy'] = implode(',', $values['networks_privacy']);
                        }
                    }
                }
                $projectModel = $table->createRow();

                //WHO WILL BE THE PARENT OF PROJECT CREATED THROUGH THE OTHER MODULES
                $projectOwnerSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitecrowdfunding.project.leader.owner.$parent_type", 1);
                if ($parent_id && $parent_type && $projectOwnerSetting) {
                    $values['parent_type'] = $parent_type;
                    $values['parent_id'] = $parent_id;
                } else {
                    $values['parent_type'] = 'user';
                    $values['parent_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
                }
                $projectModel->setFromArray($values);
                if ($projectModel->approved) {
                    $projectModel->approved_date = date('Y-m-d H:i:s');
                }
                $projectModel->start_date = date('Y-m-d H:i:s');
                $projectModel->save();
                if (isset($projectModel->package_id)) {
                    $projectModel->package_id = $package_id;
                }
                $projectModel->save();
                $project_id = $projectModel->project_id;

                //SET PHOTO
                if (!empty($values['photo'])) {
                    $projectModel->setPhoto($form->photo);
                    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitecrowdfunding');
                    $album_id = $albumTable->update(array('photo_id' => $projectModel->photo_id), array('project_id = ?' => $projectModel->project_id));
                }
                //ADDING TAGS
                $keywords = '';
                if (isset($values['tags']) && !empty($values['tags'])) {
                    $tags = preg_split('/[,]+/', $values['tags']);
                    $tags = array_filter(array_map("trim", $tags));
                    $projectModel->tags()->addTagMaps($viewer, $tags);

                    foreach ($tags as $tag) {
                        $keywords .= " $tag";
                    }
                }

                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($projectModel);
                $customfieldform->saveValues();
                $categoryIds = array();
                $categoryIds[] = $projectModel->category_id;
                $categoryIds[] = $projectModel->subcategory_id;
                $categoryIds[] = $projectModel->subsubcategory_id;
                $projectModel->profile_type = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                $projectModel->save();

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
                    if ( !is_null( $postMax ) ) {
                        $auth->setAllowed($projectModel, $role, "post", ($i <= $postMax));
                    }
                }
                // Create some auth stuff for all leaders
                $auth->setAllowed($projectModel, $leaderList, 'topic.edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'edit', 1);
                $auth->setAllowed($projectModel, $leaderList, 'delete', 1);

                if (!empty($project_id)) {
                    $projectModel->setLocation();
                }
                $project = $projectModel;
                $currentDate = date('Y-m-d H:i:s');
                /*if ($project->state == 'published' && $project->approved == 1 && $project->is_gateway_configured && $project->start_date <= $currentDate) {
                    $owner = $project->getOwner();
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $project, 'sitecrowdfunding_project_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                    }
                    $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                    if (!empty($enable_Facebooksefeed)) {
                        $sitecrowdfunding_array = array();
                        $sitecrowdfunding_array['type'] = 'sitecrowdfunding_project_new';
                        $sitecrowdfunding_array['object'] = $project;
                        Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitecrowdfunding_array);
                    }
                }*/
                //NOTIFICATION TO SUPERADMINS FOR PROJECT CREATION 
                $superAdmins = Engine_Api::_()->user()->getSuperAdmins();
                foreach ($superAdmins as $superAdmin) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($superAdmin, $viewer, $project, 'sitecrowdfunding_project_created');
                }
                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            //UPDATE KEYWORDS IN SEARCH TABLE
            if (!empty($keywords)) {
                Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'sitecrowdfunding_project', 'id = ?' => $project->project_id));
            }
            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
            $db->beginTransaction();
            try {
                $row = $tableOtherinfo->getOtherinfo($project_id);
                if (empty($row)) {
                    Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->insert(array(
                        'project_id' => $project_id,
                        'overview' => ""
                    ));
                }
                //COMMIT
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $redirectValue = $coreSettings->getSetting('sitecrowdfunding.create.redirection', 0);
            $uri = $form->getValue('return_url');
            if ($uri && substr($uri, 0, 5) == 'SE64-') {
                return $this->_redirect(base64_decode(substr($uri, 5)), array('prependBase' => false));
            } else if ($redirectValue == 1)
                return $this->_redirectCustom($project->getHref());

            return $this->_helper->redirector->gotoRoute(array('controller' => 'project', 'action' => 'edit', 'project_id' => $project_id), 'sitecrowdfunding_specific', true);
        }
    }

    //ACTION FOR EDITING THE PROJECT
    public function editAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->TabActive = "edit";
        $listValues = array();
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->display_type = $display_type = $this->_getParam('type');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        //$previous_location = $project->location;
        $projectinfo = $project->toarray();

        $this->view->category_id = $previous_category_id = $project->category_id;
        $this->view->subcategory_id = $subcategory_id = $project->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $project->subsubcategory_id;

        $row = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategory($subcategory_id);
        $this->view->subcategory_name = "";
        if (!empty($row)) {
            $this->view->subcategory_name = $row->category_name;
        }

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            Engine_Api::_()->core()->setSubject($project);
        }

        if (!$this->_helper->requireSubject()->isValid())
            return;


        $parent_type = $project->parent_type;
        if (strstr($parent_type, 'sitereview_listing')) {
            $parent_type = 'sitereview_listing';
        }
        $parent_id = $project->parent_id;
        $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);
        $isEditPrivacy = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($parent_type, $parent_id, $project);

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($isEditPrivacy) &&empty($editPrivacyOrganization)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }



        if(empty($display_type)){
            return $this->_helper->redirector->gotoRoute(array('controller' => 'outcome', 'action' => 'settings', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }
        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitecrowdfunding')->defaultProfileId();
        //GET PROFILE MAPPING ID
        $formpopulate_array = $categoryIds = array();
        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Edit(array('item' => $project, 'defaultProfileId' => $defaultProfileId,
            'parentTypeItem' => $parentTypeItem,));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        //ALLOW THE ADMINS TO EDIT ALL INFORMATION OF PROJECT
        $this->view->viewerIsAdmin = $viewerIsAdmin = $viewer->isAdminOnly();
        $this->view->backerCount = $project->backer_count;
        //!empty($project->backer_count) &&
        $this->view->category_id = $project->category_id;
        $form->getElement('category_id')->setValue($project->category_id);
        //populating organization ids
        $this->view->subcategory_id = $project->subcategory_id;
        $this->view->subsubcategory_id = $project->subsubcategory_id;
        if ($project->category_id) {
            //GET PROFILE MAPPING ID
            $categoryIds = array();
            $categoryIds[] = $project->category_id;
            if ($project->subcategory_id)
                $categoryIds[] = $project->subcategory_id;
            if ($project->subsubcategory_id)
                $categoryIds[] = $project->subsubcategory_id;
            $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
        }
        $populatedArray = $formpopulate_array = $project->toArray();

        $form->populate($populatedArray);
        $form->removeElement('photo');

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

            return;
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();
            if (empty($values))
                return;

            //CATEGORY IS REQUIRED FIELD
            if (empty($_POST['category_id'])) {
                $error = $this->view->translate('Please complete Category field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
            $db = $table->getAdapter();
            $db->beginTransaction();
            $user_level = $viewer->level_id;
            try {
                //Create Project
                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }

                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }
                if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            unset($values['networks_privacy']);
                            $values['networks_privacy'] = 0;
                        } else {
                            $values['networks_privacy'] = implode(',', $values['networks_privacy']);
                        }
                    }
                }
                $projectModel = $project;
                //!empty($project->backer_count) &&
                if (!$viewerIsAdmin) {
                    if (isset($values['goal_amount']))
                        unset($values['goal_amount']);
                }
                $projectModel->setFromArray($values);
                $projectModel->save();
                $project_id = $projectModel->project_id;
                //ADDING TAGS
                $keywords = '';
                if (isset($values['tags']) && !empty($values['tags'])) {
                    $tags = preg_split('/[,]+/', $values['tags']);
                    $tags = array_filter(array_map("trim", $tags));
                    $projectModel->tags()->setTagMaps($viewer, $tags);
                    foreach ($tags as $tag) {
                        $keywords .= " $tag";
                    }
                }

                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($projectModel);
                $customfieldform->saveValues();
                if ($customfieldform->getElement('submit')) {
                    $customfieldform->removeElement('submit');
                }

                if (isset($values['category_id']) && !empty($values['category_id'])) {
                    $categoryIds = array();
                    $categoryIds[] = $projectModel->category_id;
                    $categoryIds[] = $projectModel->subcategory_id;
                    $categoryIds[] = $projectModel->subsubcategory_id;
                    $projectModel->profile_type = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                    if ($projectModel->profile_type != $previous_profile_type) {

                        $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'values');
                        $fieldvalueTable->delete(array('item_id = ?' => $projectModel->project_id));

                        Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->delete(array(
                            'item_id = ?' => $projectModel->project_id,
                        ));

                        if (!empty($projectModel->profile_type) && !empty($previous_profile_type)) {
                            //PUT NEW PROFILE TYPE
                            $fieldvalueTable->insert(array(
                                'item_id' => $projectModel->project_id,
                                'field_id' => $defaultProfileId,
                                'index' => 0,
                                'value' => $projectModel->profile_type,
                            ));
                        }
                    }
                    $projectModel->save();
                }

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
                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'type'=> $display_type, 'project_id' => $project->project_id), 'sitecrowdfunding_specific', true);
        }
    }

    //ACTION FOR EDIT THE LOCATION
    public function editlocationAction() {

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //IF LOCATION SETTING IS ENABLED
        if (!Engine_Api::_()->sitecrowdfunding()->enableLocation()) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //AUTHORIZATION CHECK
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "location";

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');

        //MAKE VALUE ARRAY
        $values = array();
        $value['id'] = $project->project_id;

        //GET LOCATION
        $this->view->location = $location = $locationTable->getLocation($value);

        if (!empty($location)) {

            //MAKE FORM
            $this->view->form = $form = new Sitecrowdfunding_Form_Location(array(
                'item' => $project,
                'location' => $location->location
            ));
            $form->removeDecorator('title');
            $form->removeDecorator('description');
            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                $form->populate($location->toarray());
                return;
            }

            //FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            //GET FORM VALUES
            $values = $form->getValues();

            unset($values['submit']);
            unset($values['location']);

            //UPDATE LOCATION
            $locationTable->update($values, array('project_id = ?' => $project_id));
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        $this->view->location = $locationTable->getLocation($value);
    }

    //ACTION FOR EDIT THE PROJECT ADDRESS
    public function editaddressAction() {

        //GET PROJECT ID AND OBJECT
        $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();


        //IF PROJECT IS NOT EXIST
        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Address(array('item' => $project));
        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $form->populate($project->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $oldProjectLocation = $project->location;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $location = $_POST['location'];
            $project->location = $location;
            $project->save();

            if ($project->location !== $oldProjectLocation) {

                // NOTIFY BACKERS ABOUT LOCATION UPDATE
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $backerIds = $project->getUniqueBackers();
                $owner = $project->getOwner();
                $type = 'sitecrowdfunding_location_updated';
                $params = array('newlocation' => $project->location);

                foreach ($backerIds as $user_id) {
                    $notifyUser = Engine_Api::_()->getItem('user', $user_id);
                    $notifyApi->addNotification($notifyUser, $owner, $project, $type, $params);
                }
            }

            //GET LOCATION TABLE
            $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
            if (!empty($location)) {
                $project->setLocation();
                $locationTable->update(array('location' => $location), array('project_id = ?' => $project_id));
            } else {
                $locationTable->delete(array('project_id = ?' => $project_id));
            }

            $db->commit();

            $url = $this->_helper->url->url(array('action' => 'editlocation', 'controller' => 'project', 'project_id' => $project->project_id));
            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your project location has been modified successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR PROJECT VIEW
    public function viewAction() {
        if (!$this->_helper->requireSubject('sitecrowdfunding_project')->isValid())
            return;

        $this->view->project = $project = Engine_Api::_()->core()->getSubject();

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //WHO CAN VIEW THE PROJECT
        $this->view->viewPrivacy = 1;
        if (!$project->canView($viewer)) {
            $this->view->viewPrivacy = 0;
        }
        if (!$this->view->viewPrivacy) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$this->_helper->requireAuth()->setAuthParams($project, null, 'view')->isValid())
            return;

        //GET META KEYWORDS
        $params['keywords'] = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding')->getColumnValue($project->project_id, 'keywords');
        //SET META KEYWORDS
        Engine_Api::_()->sitecrowdfunding()->setMetaKeywords($params);


        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfundingprofile.css');

        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function deleteAction() {

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $parent_type = $project->parent_type;
        if (strstr($parent_type, 'sitereview_listing')) {
            $parent_type = 'sitereview_listing';
        }
        $parent_id = $project->parent_id;
        if ($project->parent_type && $project->parent_id) {
            $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);

            $isParentDeletePrivacy = Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($project->parent_type, $project->parent_id, $project);

            if (empty($isParentDeletePrivacy))
                return $this->_forwardCustom('requireauth', 'error', 'core');
        } else {
            if ($viewer->getIdentity() != $project->owner_id && !$this->_helper->requireAuth()->setAuthParams($project, null, 'delete')->isValid()) {
                return $this->_forward('requireauth', 'error', 'core');
            }
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Backed projects can not be deleted.');
            if (!Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy(null, null, $project))
                return $this->_forward('success', 'utility', 'core', array(
                            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'default', true),
                            'messages' => Array($this->view->message)));
        }
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Delete();
        if (!$project) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Project doesn't exists or not authorized to delete");
            return;
        }
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        if (!empty($project->backer_count)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('You cannot delete this Project as it has been backed by some users. Still, you want to delete this Project then please contact site admin to do so. [Note: It would be better if you can inform the backers about the deletion of the project.');
            return;
        }
        $db = $project->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            Engine_Api::_()->getApi('core', 'sitecrowdfunding')->deleteProject($project);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Project has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'default', true),
                    'messages' => Array($this->view->message)
        ));
    }

    public function uploadKycAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }
        $this->view->project_id = $project_id = $project->project_id;
        $this->view->form = $form = new Sitegateway_Form_Order_Kyc();
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $this->view->adminGateway = $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $project_gateway_obj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
        $mode = 'live';
        if ($adminGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        $this->view->mangopayuser = true;
        if (!(isset($project_gateway_obj->config[$mode]['mangopay_user_id']) && !empty($project_gateway_obj->config[$mode]['mangopay_user_id']))) {
            $this->view->mangopayuser = false;
            $form->addError($this->view->translate('Please configure your MangoPay payment method'));
            return;
        }
        $this->view->mangoPayUserId = $project_gateway_obj->config[$mode]['mangopay_user_id'];
        if (!$this->getRequest()->isPost()) {
            return;
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        //CREATE DOCUMENT
        if (!empty($values['page'])) {
            $project_gateway_obj->uploadKYC($form->page, $values['document_type'], $values['tag']);
        }
        $form->reset();
        return $this->_helper->redirector->gotoRoute(array('action' => 'upload-kyc', 'project_id' => $project->getIdentity()), 'sitecrowdfunding_specific', true);
    }

    public function paymentInfoAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        } else {
            $this->view->project = $project = Engine_Api::_()->core()->getSubject();
            $this->view->project_id = $project_id = $project->project_id;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

     /**   // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);
        if ($isProjectAdmin != 1) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }
     */
        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1  && empty($editPrivacyOrganization)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }

        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            return;
        }
        $this->view->paypalForm = $paypalForm = new Sitecrowdfunding_Form_PayPal();

        //check irganization exists or not
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);


        if(!$project->is_payment_details_editable &&  !empty($parentOrganization) ) {
           //set fields as read only
            $paypalForm->getElement('email')->setAttrib('readonly', 'readonly');
            $paypalForm->getElement('username')->setAttrib('readonly', 'readonly');
            $paypalForm->getElement('password')->setAttrib('readonly', 'readonly');
            $paypalForm->getElement('signature')->setAttrib('readonly', 'readonly');
            $paypalForm->addError("You can't able to edit payment. Only organisation can edit it.");

            echo "<script>
              removeSave();              
              function  removeSave() {
                document.getElementsByName('save_gateway')[0].style.display = 'none';  
              }
            </script>";
        }


        //$this->view->paypalEnable = true;
        $this->view->stripeConnected = 0;
        $this->view->paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.paymentmethod', 'paypal');
        $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
        if ($paymentMethod == 'split') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
        } elseif ($paymentMethod == 'escrow') {
            $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
        } else {
            if (empty($paymentToSiteadmin)) {
                $this->view->enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
            }
        }
        $projectEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'project_gateway');
        if (!empty($projectEnabledgateway)) {
            $projectEnabledgateway = Zend_Json_Decoder::decode($projectEnabledgateway);
        }
        $getEnabledGateways = array();
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('plugin' => array('Sitegateway_Plugin_Gateway_Stripe','Sitegateway_Plugin_Gateway_MangoPay')));
        }
        foreach ($getEnabledGateways as $getEnabledGateway) {
            $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
            $gatewayKey = strtolower($gatewyPlugin[1]);
            $gatewayKeyUC = ucfirst($gatewyPlugin[1]);
            if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                if (isset($_SESSION['redirect_stripe_connect_oauth_process'])) {
                    $session = new Zend_Session_Namespace('redirect_stripe_connect_oauth_process');
                    $session->unsetAll();
                }
                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                if (!empty($projectGatewayObj) && !empty($projectGatewayObj->projectgateway_id)) {
                    if (is_array($projectGatewayObj->config) && !empty($projectGatewayObj->config['stripe_user_id'])) {
                        $this->view->stripeConnected = 1;
                        $this->view->stripeEnabled = true;
                    }
                }
            } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {
                $this->view->mangopayForm = $mangopayForm = new Sitecrowdfunding_Form_MangoPay();
                $this->view->mangopayBankDetailForm = $mangopayBankDetailForm = new Sitecrowdfunding_Form_MangoPayBankDetail();

                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_MangoPay\''));
                if (!empty($projectGatewayObj)) {
                    // Populate form
                    $mangopayForm->populate($projectGatewayObj->toArray());
                    $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
                    $mode = 'live';
                    if ($adminAPGateway->config['test_mode']) {
                        $mode = 'sandbox';
                    }
                    $config = isset($projectGatewayObj->config[$mode]) ? ($projectGatewayObj->config[$mode]) : null;
                    if (is_array($config)) {
                        $birthday = $projectGatewayObj->config[$mode]['birthday'];
                        $config['birthday'] = date('Y-m-d', $birthday);
                        $mangopayForm->populate($config);
                        $mangopayBankDetailForm->populate($config);
                    }
                    if ($projectGatewayObj->enabled == 1) {
                        $this->view->mangopayEnable = true;
                    }
                } else {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                    $select = $searchTable->select();
                    $select->where('item_id = ?', $viewer->getIdentity());
                    $otherUserRecords = $searchTable->fetchRow($select);
                    if ($otherUserRecords) {
                        $formData['first_name'] = $otherUserRecords->first_name;
                        $formData['last_name'] = $otherUserRecords->last_name;
                        $formData['birthday'] = $otherUserRecords->birthdate;
                    }
                    $formData['email'] = $viewer->email;
                    $mangopayForm->populate($formData);
                }
            } else {
                $formName = "form$gatewayKeyUC";
                $formClass = "Sitegateway_Form_Order_$gatewayKeyUC";
                $this->view->$formName = $gatewayForm = new $formClass();
                $gatewayForm->setName("sitecrowdfunding_payment_info_$gatewayKey");
                if ((!empty($projectEnabledgateway[$gatewayKey]) || !empty($paymentToSiteadmin))) {
                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin = ?' => $getEnabledGateway->plugin));
                    if (!empty($projectGatewayObj)) {
                        $gateway_id = $projectGatewayObj->projectgateway_id;
                        if (!empty($gateway_id)) {
                            $gatewyEnabled = $gatewayKey . 'Enabled';
                            $this->view->$gatewyEnabled = true;
                            $gatewayForm->populate($projectGatewayObj->toArray());
                            if (is_array($projectGatewayObj->config)) {
                                $gatewayForm->populate($projectGatewayObj->config);
                            }
                        }
                    }
                }
            }
        }

        if (!empty($projectEnabledgateway['paypal']) || !empty($paymentToSiteadmin)) {
            $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
            if (!empty($projectGatewayObj)) {
                $gateway_id = $projectGatewayObj->projectgateway_id;
                $this->view->paypalEnable = true;
                $paypalForm->populate($projectGatewayObj->toArray());
                if (is_array($projectGatewayObj->config)) {
                    $paypalForm->populate($projectGatewayObj->config);
                }
            }
        }
    }

    public function setProjectGatewayInfoAction() {

        if (empty($_POST) || !isset($_POST['project_id']) || !isset($_POST['data'])) {
            return false;
        }

        $project_id = $_POST['project_id'];
        $data = $_POST['data'];
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $formsData = !empty($data) ? Zend_Json::decode($data) : array();
        $formsData['additionalGateway'] = array();
        if (isset($_POST['additionalGatewayDetailArray'])) {
            $formsData['additionalGateway'] = $_POST['additionalGatewayDetailArray'];
        }

        if (empty($project) || count($formsData) == 0) {
            return false;
        }
        $projectGateway = array();
        $payment_info_error = false;
        if (isset($formsData['mangopay'])) {
            $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            $mangoPayDetails = array();
            @parse_str($formsData['mangopay'], $mangoPayDetails);
            $mangoPayBankDetails = array();
            @parse_str($formsData['mangopayBankDetail'], $mangoPayBankDetails);

            $mangoPayCompleteDetails = array_merge($mangoPayBankDetails, $mangoPayDetails);
            $mangoPayform = new Sitecrowdfunding_Form_MangoPay();
            $this->view->mangopay_error = false;
            $this->view->mangopay_bankDetail_error = false;
            //FORM VALIDATION
            if (!$mangoPayform->isValid($mangoPayDetails)) {
                $this->view->mangopay_error = true;
                $this->view->mangopay_message = "<ul class='form-errors'><li>" . $this->view->translate("Please complete all field - it is required.") . "</li></ul>";
            } else {
                $result = $project_gateway_table->mangoPayConfigSettings($mangoPayCompleteDetails, $project);
                if ($result['error'] == 1) {
                    $this->view->mangopay_error = true;
                    $this->view->mangopay_message = "<ul class='form-errors'><li>" . $result['error_message'] . "</li></ul>";
                } else {
                    $bankDetails = $project_gateway_table->setMangoPayBankDetails($mangoPayBankDetails, $project);
                    if ($bankDetails['error'] == 1) {
                        $this->view->mangopay_bankDetail_error = true;
                        $this->view->mangopay_bankDetail_message = $bankDetails['errorMessage'];
                    }
                    $projectGateway['mangopay'] = $result['gateway_id'];
                }
            }
        } else {
            $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            if (!empty($project_gateway_table_obj)) {
                $project_gateway_table_obj->enabled = 0;
                $project_gateway_table_obj->save();
            }
        }
        $isPaypal = false;
        if (isset($formsData['paypal'])) {
            $isPaypal = true;
            $paypalDetails = array();
            @parse_str($formsData['paypal'], $paypalDetails);
            $paypalEmail = $paypalDetails['email'];
            unset($paypalDetails['email']);
            if (!empty($paypalDetails)) {
                $form = new Sitecrowdfunding_Form_PayPal();

                // Validate the email
                if (!filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) {
                    $payment_info_error = true;
                    $this->view->email_error = $this->view->translate('Please enter a valid email address.');
                }

                if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
                    $payment_info_error = true;
                    $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                }
            } else {
                $payment_info_error = true;
                $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
            }
        } else {
            $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''));
            if (!empty($project_gateway_table_obj)) {
                $project_gateway_table_obj->enabled = 0;
                $project_gateway_table_obj->save();
            }
        }

        if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $gatewayDatasValidation = array();
            $additionalGateway = array();
            $isStripeConnect = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0);
            foreach ($formsData['additionalGateway'] as $key => $addGateway) {
                if ($key == 'stripe' && $isStripeConnect) {
                    continue;
                }
                @parse_str($addGateway, $gateway);
                $additionalGateway[$key] = $gateway;
            }
            foreach ($additionalGateway as $key => $additionalGatewaysCheckedArray) {
                if ($additionalGatewaysCheckedArray) {
                    $gatewayKeyFinal = strtolower($key);
                    $gatewayKeyFinalUC = ucfirst($gatewayKeyFinal);
                    $showInfoError = false;
                    foreach ($additionalGatewaysCheckedArray as $gatewayParam) {
                        if (empty($gatewayParam)) {
                            $showInfoError = true;
                            break;
                        }
                    }
                    $gateway_info_error = $gatewayKeyFinal . "_info_error";
                    $formClass = "Sitegateway_Form_Order_$gatewayKeyFinalUC";
                    $form = new $formClass();
                    if ($showInfoError) {
                        $payment_info_error = true;
                        $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
                    }
                }
            }
        }
        if (!empty($payment_info_error))
            return;

        // IF PAYPAL GATEWAY IS ENABLE, THEN INSERT PAYPAL ENTRY IN ENGINE4_SITECROWDFUNDING_GATEWAY TABLE
        if (!empty($paypalDetails)) {
            $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            if ($isPaypal) {
                $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''));
            } 

            if (!empty($project_gateway_table_obj))
                $gateway_id = $project_gateway_table_obj->projectgateway_id;
            else
                $gateway_id = 0;
            $paypalDetails['test_mode'] = 0;
            if ($isPaypal) {
                $adminAPGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway('Payment_Plugin_Gateway_PayPal');
                $paypalDetails['test_mode'] = $adminAPGateway->test_mode;
            } 

            $success_message = $error_message = false;
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $paypalEnabled = true;
            // Process
            try {
                //GET VIEWER ID
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                if (empty($gateway_id)) {
                    $row = $project_gateway_table->createRow();
                    $row->project_id = $project_id;
                    $row->user_id = $viewer_id;
                    $row->email = $paypalEmail;
                    $row->title = 'Paypal';
                    $row->description = '';
                    $row->plugin = 'Payment_Plugin_Gateway_PayPal';
                    $row->test_mode = $paypalDetails['test_mode'];
                    $obj = $row->save();
                    $gateway = $row;
                } else {
                    $gateway = Engine_Api::_()->getItem("sitecrowdfunding_projectGateway", $gateway_id);
                    $gateway->email = $paypalEmail;
                    $gateway->test_mode = $paypalDetails['test_mode'];
                    $gateway->save();
                }
                $db->commit();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            // Validate gateway config
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($paypalDetails);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $paypalEnabled = false;
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }

            // Process
            $message = null;
            try {
                $values = $gateway->getPlugin()->processAdminGatewayForm($paypalDetails);
            } catch (Exception $e) {
                $message = $e->getMessage();
                $values = null;
            }

            if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
                $paypalDetails = null;
            }

            if (null !== $paypalDetails) {
                $gateway->setFromArray(array(
                    'enabled' => $paypalEnabled,
                    'config' => $paypalDetails,
                ));
                $gateway->save();
                $proectPaypalId = $gateway->projectgateway_id;
                if ($isPaypal) {
                    $projectGateway['paypal'] = $proectPaypalId;
                } 
            } else {
                if (!$error_message) {
                    $error_message = $message;
                }
            }

            $this->view->error_message = $error_message;
        }
        if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && !empty($additionalGateway)) {

            foreach ($additionalGateway as $key => $gatewayDetails) {

                $gatewayKeyFinalUC = ucfirst($key);
                $sitecrowdfunding_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $sitecrowdfunding_gateway_table_obj = $sitecrowdfunding_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = ?' => "Sitegateway_Plugin_Gateway_$gatewayKeyFinalUC"));

                if (!empty($sitecrowdfunding_gateway_table_obj))
                    $gateway_id = $sitecrowdfunding_gateway_table_obj->projectgateway_id;
                else
                    $gateway_id = 0;

                $error_message_additional_gateway = false;
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                $gatewayEnabled = true;
                // Process
                try {
                    //GET VIEWER ID
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $viewer_id = $viewer->getIdentity();
                    $email = $viewer->email;
                    if (empty($gateway_id)) {
                        $row = $sitecrowdfunding_gateway_table->createRow();
                        $row->project_id = $project_id;
                        $row->user_id = $viewer_id;
                        $row->email = $email;
                        $row->title = "$gatewayKeyFinalUC";
                        $row->description = '';
                        $row->plugin = "Sitegateway_Plugin_Gateway_$gatewayKeyFinalUC";
                        $obj = $row->save();
                        $gateway = $row;
                    } else {
                        $gateway = Engine_Api::_()->getItem("sitecrowdfunding_projectGateway", $gateway_id);
                        $gateway->email = $email;
                        $gateway->save();
                    }
                    $db->commit();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                // Validate gateway config
                $gatewayObject = $gateway->getGateway();
                try {
                    $gatewayObject->setConfig($gatewayDetails);
                    $response = $gatewayObject->test();
                } catch (Exception $e) {
                    $gatewayEnabled = false;
                    $error_message_additional_gateway = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
                }

// Process
                $message_additional_gateway = null;
                try {
                    $values = $gateway->getPlugin()->processAdminGatewayForm($gatewayDetails);
                } catch (Exception $e) {
                    $message_additional_gateway = $e->getMessage();
                    $values = null;
                }

                $formValuesValidation = true;
                foreach ($gatewayDetails as $k => $gatewayParam) {
                    if ($k != 'test_mode' && empty($gatewayParam)) {
                        $formValuesValidation = false;
                        break;
                    }
                }

                if ($formValuesValidation) {
                    $gateway->setFromArray(array(
                        'enabled' => $gatewayEnabled,
                        'config' => $gatewayDetails,
                    ));
                    $gateway->save();
                    $projectGateway[$key] = $gateway->projectgateway_id;
                } elseif (!$error_message_additional_gateway) {
                    $error_message_additional_gateway = $message_additional_gateway;
                }

                $error_message_gateway = "error_message_$key";
                $this->view->$error_message_gateway = $error_message_additional_gateway;
            }
        }

        // INSERT ALL ENABLED GATEWAY ENTRY IN PROJECT TABLE
        $projectOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getOtherinfo($project->project_id);
        $projectOtherInfo->project_gateway = Zend_Json_Encoder::encode($projectGateway);
        $projectOtherInfo->save();
        $this->view->success_message = $this->view->translate('Changes saved.');
    }

    public function setPaymentInfoAction() {
        $values = array();
        $values['username'] = $_POST['username'];
        $values['password'] = $_POST['password'];
        $values['signature'] = $_POST['signature'];
        $values['enabled'] = $_POST['enabled'];
        $project_id = $_POST['project_id'];
        $email = $_POST['email'];
        $form = new Sitecrowdfunding_Form_PayPal();
        $payment_info_error = false;
        if (!$form->isValid(array('email' => $email))) {
            $payment_info_error = true;
            $this->view->email_error = $this->view->translate('Please enter a valid email address.');
        }
        if (empty($values['username']) || empty($values['password']) || empty($values['signature'])) {
            $payment_info_error = true;
            $this->view->paypal_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
        }
        $sitecrowdfunding_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
        $gateway_id = $sitecrowdfunding_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''))->projectgateway_id;
        $enabled = (bool) $values['enabled'];
        $success_message = $error_message = false;
        unset($values['enabled']);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

// Process
        try {
//GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (empty($gateway_id)) {
                $row = $sitecrowdfunding_gateway_table->createRow();
                $row->project_id = $project_id;
                $row->user_id = $viewer_id;
                $row->email = $email;
                $row->title = 'Paypal';
                $row->description = '';
                $row->plugin = 'Payment_Plugin_Gateway_PayPal';
                $obj = $row->save();
                $gateway = $row;
            } else {
                $gateway = Engine_Api::_()->getItem("sitecrowdfunding_projectGateway", $gateway_id);
                $gateway->email = $email;
                $gateway->save();
            }
            $db->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

// Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
// $form->populate(array('enabled' => false));
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $error_message = $this->view->translate('Gateway is currently disabled.');
        }

// Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (empty($values['username']) || empty($values['password']) || empty($values['signature'])) {
            $values = null;
        }

        if (null !== $values) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            $gateway->save();
            $success_message = $this->view->translate('Changes saved.');
        } else {
            if (!$error_message) {
                $error_message = $message;
            }
        }

        $this->view->success_message = $success_message;
        $this->view->error_message = $error_message;
    }

    public function setPaymentInfoAdditionalGatewayAction() {

        $values = array();
        @parse_str($_POST['gatewayCredentials'], $values);
        $gatewayCredentials = $values;
        $values['enabled'] = $_POST['enabled'];
        $project_id = $_POST['project_id'];
        $gatewayName = $_POST['gatewayName'];
        $gatewayNameUC = ucfirst($gatewayName);
        $Payment_gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
        $Payment_gateway_table_obj = $Payment_gateway_table->fetchRow(array("plugin = 'Sitegateway_Plugin_Gateway_$gatewayNameUC'"));
        $testmode = $Payment_gateway_table_obj->test_mode;
        $sitecrowdfunding_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
        $gateway_id = $sitecrowdfunding_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = ?' => "Sitegateway_Plugin_Gateway_$gatewayNameUC"))->projectgateway_id;
        $payment_info_error = false;
        $showInfoError = false;
        foreach ($gatewayCredentials as $gatewayParam) {
            if (empty($gatewayParam)) {
                $showInfoError = true;
                break;
            }
        }

        if ($showInfoError) {
            $payment_info_error = true;
            $gateway_info_error = $gatewayName . "_info_error";
            $this->view->$gateway_info_error = $this->view->translate('Gateway login failed. Please insert all the informations or double check your connection information.');
        }
        $enabled = (bool) $values['enabled'];
        $success_message = $error_message = false;
        unset($values['enabled']);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
// Process
        try {
//GET VIEWER ID
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            // $gateway_id = $gatewayRow->gateway_id;
            if (empty($gateway_id)) {
                $row = $sitecrowdfunding_gateway_table->createRow();
                $row->project_id = $project_id;
                $row->user_id = $viewer_id;
                $row->email = $viewer->email;
                $row->title = "$gatewayNameUC";
                $row->description = '';
                $row->plugin = "Sitegateway_Plugin_Gateway_$gatewayNameUC";
                $gateway->test_mode = $testmode;
                $row->save();
                $gateway = $row;
            } else {
                $gateway = Engine_Api::_()->getItem("sitecrowdfunding_projectGateway", $gateway_id);
                $gateway->test_mode = $testmode;
                $gateway->email = $viewer->email;
                $gateway->save();
            }
            $db->commit();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

// Validate gateway config
        if ($enabled) {
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
                $error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $error_message = $this->view->translate('Gateway is currently disabled.');
        }

// Process
        $message = null;
        try {
            $values = $gateway->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (!$showInfoError) {
            $gateway->setFromArray(array(
                'enabled' => $enabled,
                'config' => $values,
            ));
            $gateway->save();
            $success_message = $this->view->translate('Changes saved.');
        } else {
            if (!$error_message) {
                $error_message = $message;
            }
        }

        $this->view->success_message = $success_message;
        $this->view->error_message = $error_message;
    }

    //ACTION FOR GETTING THE  AUTOSUGGESTED PROJECTS BASED ON SEARCHING
    public function getSearchProjectsAction() {

        $siteprojects = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($siteprojects);

        if ($mode == 'text') {

            $i = 0;
            foreach ($siteprojects as $siteproject) {
                $sitecrowdfunding_url = $siteproject->getHref();
                $i++;
                $content_project = $this->view->itemPhoto($siteproject, 'thumb.normal');
                $data[] = array(
                    'id' => $siteproject->project_id,
                    'label' => $siteproject->title,
                    'project' => $content_project,
                    'sitecrowdfunding_url' => $sitecrowdfunding_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($siteprojects as $siteproject) {
                $sitecrowdfunding_url = $siteproject->getHref();
                $content_project = $this->view->itemPhoto($siteproject, 'thumb.normal');
                $i++;
                $data[] = array(
                    'id' => $siteproject->project_id,
                    'label' => $siteproject->title,
                    'project' => $content_project,
                    'sitecrowdfunding_url' => $sitecrowdfunding_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['sitecrowdfunding_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //GET CATEGORIES ACTION
    public function getProjectsCategoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding');
        $categoryTableName = $categoriesTable->info('name');
        $select = $categoriesTable->select()
                ->from($categoryTableName, array('category_id', 'category_name'))
                ->where($categoryTableName . ".$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        if (!$showAllCategories) {
            $tableProjects = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
            $tableProjectsName = $tableProjects->info('name');
            $select->setIntegrityCheck();
            if ($element_type == 'subcat_dependency') {
                $select->join($tableProjectsName, "$tableProjectsName.subcategory_id=$categoryTableName.$element_type", null);
            } else {
                $select->join($tableProjectsName, "$tableProjectsName.category_id=$categoryTableName.$element_type", null);
            }
            $select->where($tableProjectsName . '.approved = ?', 1)->where($tableProjectsName . '.search = ?', 1);

            $select = $tableProjects->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        $select->group($categoryTableName . '.category_id');
        $categoriesData = $categoriesTable->fetchAll($select);
        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $this->view->translate($category->category_name);
                $data['category_id'] = $category->category_id;
                $data['category_slug'] = $category->getCategorySlug();
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagscloudAction() {
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tags', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
    }

    public function browseAction() {

        $this->_helper->content->setNoRender()->setEnabled();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, 'view'))
            return;

        $params = array();
        $project_type_title = '';
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($project_type_title)
                $params['project_type_title'] = $title = $project_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }
            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);
            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }

                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('sitecrowdfunding_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->sitecrowdfunding()->setMetaTitles($params);
        //SET META DESCRIPTION
        Engine_Api::_()->sitecrowdfunding()->setMetaDescriptionsBrowse($params);
        //GET PROJECT CATEGORIES TITLE
        $params['project_type_title'] = $this->view->translate('Projects');
        //SET META KEYWORDS
        Engine_Api::_()->sitecrowdfunding()->setMetaKeywords($params);
    }

    //ACTION FOR BROWSE LOCATION PROJECTS.
    public function mapAction() {
        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitecrowdfunding_project_map");
        $pageObject = $pageTable->fetchRow($pageSelect);
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        } else {
            $this->_helper->content->setContentName($pageObject->page_id)->setNoRender()->setEnabled();
        }
    }

    public function manageAction() {


        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function pinboardAction() {

        $this->_helper->content->setNoRender()->setEnabled();
    }

    // ACTION FOR GET LINK WORK
    public function getLinkAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if(!empty($viewer_id)){
            $viewer = Engine_Api::_()->user()->getViewer();
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $this->view->noSendMessege = 0;
            if (empty($ids)) {
                $this->view->noSendMessege = 1;
            }
        }else{
            $this->view->noSendMessege = 1;
        }

        $this->view->subject = $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
        $this->view->url = Engine_Api::_()->getApi('Shorturl', 'core')->generateShorturl($subject,null);

    }

    //ACTION TO SEND MAIL TO PROJECT BACKERS
    public function composeAction() {
        $this->_helper->layout->setLayout('default-simple');
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        } else {
            $this->view->subject = $project = Engine_Api::_()->core()->getSubject();
        }
        $this->view->project_id = $project->project_id;
        // Make form 
        $this->view->form = $form = new Sitecrowdfunding_Form_Compose();

        $project_title = $project->getTitle();
        $project_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('project_id' => $project->project_id, 'slug' => $project->getSlug()), "sitecrowdfunding_entry_view") . ">$project_title</a>";

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            $backerIds = explode(",", $values['user_ids']);
            $projectOwner = $project->getOwner();

            //REMOVE THE EMPTY ENTRY IF ANY
            $backerIds = array_filter($backerIds);
            foreach ($backerIds as $backerId) {
                if (!empty($backerId) && $projectOwner->user_id != $backerId) {
                    $backer = Engine_Api::_()->user()->getUser($backerId);
                    $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($projectOwner, $backer, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Project: ') . $project_title_with_link);
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($backer, $projectOwner, $conversation, 'message_new');
                }
            }
            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
            ));
        }
    }

    //ACTION TO CONTACT PROJECT OWNER
    public function contactOwnerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        } else {
            $this->view->subject = $project = Engine_Api::_()->core()->getSubject();
        }
        //OWNER CANT SEND A MESSAGE TO HIMSELF
        if ($viewer_id == $project->owner_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Messages_Form_Compose();
        $form->setDescription('If you have any query related to the project or reward then you can compose your message using below form.)');
        $form->removeElement('to');
        $form->toValues->setValue("$project->owner_id");

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {
            $values = $this->getRequest()->getPost();

            $form->populate($values);

            $is_error = 0;
            if (empty($values['title'])) {
                $is_error = 1;
            }

            //SENDING MESSAGE
            if ($is_error == 1) {
                $error = $this->view->translate('Subject is required field !');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
            //GET USER
            $owner = Engine_Api::_()->getItem('user', $project->owner_id);

            $project_title = $project->getTitle();
            $project_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('project_id' => $project->project_id, 'slug' => $project->getSlug()), "sitecrowdfunding_entry_view") . ">$project_title</a>";

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $owner, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Project: ') . $project_title_with_link);

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $conversation, 'message_new');
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $project, 'sitecrowdfunding_contact_project_owner');

            //INCREMENT MESSAGE COUNTER
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR UPLOADING IMAGES THROUGH WYSIWYG EDITOR
    public function uploadPhotoAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->_helper->layout->disableLayout();

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            return false;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
            return;

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
        if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $table = Engine_Api::_()->getDbtable('albums', 'album');
            $album = $table->getSpecialAlbum($viewer, 'message');

            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);

            $db->commit();
        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            throw $e;
            return;
        }
    }

    //ACTION TO RENDER WIDGET USER PROFILE IN POP-UP
    public function userFullBioAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return;
        }
        $this->view->subject = $project = Engine_Api::_()->core()->getSubject();
        $this->view->user = $owner = $project->getOwner();
        $this->view->user_id = $owner_id = $owner->user_id;

        $userinfoTable = new Seaocore_Model_DbTable_UserInfo();
        $userinfoTableName = $userinfoTable->info('name');
        $select = $userinfoTable->select()->from($userinfoTableName, '*')
                        ->where('user_id = ?', $owner_id)->limit(1);
        $this->view->ownerBio = $ownerBio = $select->query()->fetch();

        $tableProject = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $params = array();
        $params['users'] = array($owner_id);
        $this->view->projects = $projects = $tableProject->getProjectPaginator($params);
        $this->view->totalCount = $projects->getTotalItemCount();

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $page_id = $pageTable->select()
                        ->from($pageTable->info('name'), 'page_id')
                        ->where('name = ?', 'user_profile_index')
                        ->limit(1)->query()->fetchColumn();
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $content_id = $contentTable->select()
                        ->from($contentTable->info('name'), 'content_id')
                        ->where('name = ?', 'sitecrowdfunding.contenttype-projects')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)->query()->fetchColumn();
        $this->view->contentwidget_id = $content_id;
    }

    public function testAction() {
        Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->uploadDefaultImages();
        die;
    }

}
