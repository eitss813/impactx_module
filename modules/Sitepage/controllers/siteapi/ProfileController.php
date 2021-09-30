<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_ProfileController extends Siteapi_Controller_Action_Standard {
    /**
     * Auth checkup and creating the subject.
     * 
     */
    public function init() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // Authorization check
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', $viewer, "view")->isValid())
            $this->respondWithError('unauthorized');

        if ($this->getRequestParam("page_id") && (0 !== ($page_id = (int) $this->getRequestParam("page_id")) &&
                null !== ($page = Engine_Api::_()->getItem('sitepage_page', $page_id)))) {
            Engine_Api::_()->core()->setSubject($page);
        }
        else if ($this->getRequestParam("page_id") && (null !== ($page_url = (string) $this->getRequestParam("page_id")))) {
            $page = Engine_Api::_()->getApi('Core','siteapi')->getSubjectByModuleUrl('sitepage','pages','page_url',$page_url);        
            Engine_Api::_()->core()->setSubject($page);
        }
    }

    /**
     * Returns the profile listing of the particular Directory Page
     * 
     * 
     */
    public function viewAction() {
        $this->validateRequestMethod();

        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitepage_page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

        // Return if no subject available.
        if (empty($subject))
            $this->respondWithError('no_record');
        $params = $this->_getAllParams();
        $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getSitepage($sitepage, $params);
        if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order']) && $_REQUEST['field_order'] == 1) {
            $response['profile_fields'] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($response['profile_fields']);
        }

        $this->respondWithSuccess($response, true);
    }

    /*
     * Notification Settings
     *
     */
    public function notificationSettingsAction() {
        $viewer = Engine_Api::_()->user()->getviewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $page_id = $this->_getParam('page_id');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        // Return if no subject available.
        if (empty($sitepage))
            $this->respondWithError('no_record');

        if ($this->getRequest()->isGet()) {
            $form = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getNotificationSettings($sitepage);
        }
    }

    /**
     * Deletes the directory Page
     * 
     * 
     */
    public function deleteAction() {

        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        // Get page id and object
        $page_id = $this->_getParam('page_id');
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        // Return if no subject available.
        if (empty($sitepage))
            $this->respondWithError('no_record');

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'delete');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }
        // End manage-admin check

        $getSubPageids = Engine_Api::_()->getDbTable('pages', 'sitepage')->getsubPageids($page_id);

        try {
            foreach ($getSubPageids as $getSubPageid) {
                Engine_Api::_()->sitepage()->onPageDelete($getSubPageid['page_id']);
            }
            // End sub-page work
            Engine_Api::_()->sitepage()->onPageDelete($page_id);
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Edits the Directory page
     * 
     */
    public function editAction() {

        // Get the page id and object
        $page_id = $this->_getParam('page_id');
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);

        if (!$subject)
            $this->respondWithError('no_record');

        // Start manage admin check
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewer_id = $viewer->getIdentity();

        if ($this->getRequest()->isGet()) {
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getForm($sitepage, 'edit');
            $form_fields['formValues'] = array();
            $form_fields['formValues'] = $subject->toArray();

            $sitepageTags = $sitepage->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitepageTags as $tagmap) {

                if ($tagString !== '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }


            if (!empty($tagString))
                $form_fields['formValues']['tags'] = $tagString;

            // Custom field work
            $categoryIds = array();
            $categoryIds[] = $sitepage->category_id;
            $categoryIds[] = $sitepage->subcategory_id;
            $categoryIds[] = $sitepage->subsubcategory_id;
            $profile_type = Engine_Api::_()->getDbTable('categories', 'sitepage')->getProfileType($categoryIds, 0, 'profile_type');

            $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitepage_page');
            $fieldValues = Engine_Api::_()->fields()->getFieldsValues($sitepage)->toArray();
            $fieldValuesArray = array();
            foreach ($fieldValues as $key => $value)
                $fieldValuesArray[$value['field_id']] = $value['value'];

            if (isset($form_fields['fields'][$sitepage->category_id]) && !empty($form_fields['fields'][$sitepage->category_id])) {
                foreach ($form_fields['fields'][$sitepage->category_id] as $key => $value) {
                    $name = $value['name'];
                    $name_array = explode('_', $name);
                    if (isset($fieldValuesArray[$name_array[2]]) && !empty($fieldValuesArray[$name_array[2]]))
                        $form_fields['formValues'][$name] = $fieldValuesArray[$name_array[2]];
                }
            }

	     //Auth view and auth comment work
	    $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if ($auth->isAllowed($sitepage, $role, 'view'))
                    $form_fields['formValues']['auth_view'] = $role;

                if ($auth->isAllowed($video, $role, 'comment'))
                    $form_fields['formValues']['auth_comment'] = $role;
            }
	    

            // $fieldValuesArray = array();
            // if (!empty($fieldValues))
            //     foreach ($fieldValues as $value)
            //     {
            //         $fieldValuesArray[$value->field_id] = $value->value;
            //     }
            // // Getting profile fields
            // $getRowsMatching = $mapData->getRowsMatching('option_id', $profile_type);
            // $fieldValuesResponse = array();
            // if (!empty($getRowsMatching)) {
            //     foreach ($getRowsMatching as $value) {
            //         if (array_key_exists($value->child_id, $fieldValuesArray)) {
            //             $key = $field->field_id . '_' . $field->option_id . '_' . $field->child_id . '_field_' . $field->child_id;
            //             $fieldValuesResponse[$key] = $fieldValuesArray[$value->child_id];
            //         }
            //     }
            //     if (!empty($fieldValuesResponse))
            //     {
            //         $form_fields['field_values'] = $fieldValuesArray;
            //     }
            // }

            $this->respondWithSuccess($form_fields, true);
        }
        else if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {
            if(!empty($sitepage->location)){
	    $location_id = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocationId($page_id, $sitepage->location);
            if (isset($location_id) && !empty($location_id))
                $location = Engine_Api::_()->getItem('sitepage_location', $location_id);
	    }
            $package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);
            $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
            $table = Engine_Api::_()->getItemTable('sitepage_page');
            $_POST = $_REQUEST;
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {

                // Create sitepage
                $values = $this->getAllParams();

                // Start form validation
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepage')->getFormValidators($sitepage, $values);
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);

                // Response validation error
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }


                $values = array_merge($values, array(
                    'owner_id' => $viewer->getIdentity(),
                ));


                $is_error = 0;
                if (isset($values['category_id']) && empty($values['category_id'])) {
                    $is_error = 1;
                }
                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }
                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }

                if (Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        } else {
                            $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                        }
                    }
                }



                $sitepage->setFromArray($values);

                $user_level = $viewer->level_id;

                if (!empty($sitepage->approved)) {
                    $sitepage->pending = 0;
                    $sitepage->aprrove_date = date('Y-m-d H:i:s');

                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                        $expirationDate = $package->getExpirationDate();
                        if (!empty($expirationDate))
                            $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $sitepage->expiration_date = '2250-01-01 00:00:00';
                    }
                    else {
                        $sitepage->expiration_date = '2250-01-01 00:00:00';
                    }
                }

                $sitepage->save();


                $page_id = $sitepage->page_id;

                if (!empty($sitepage->approved)) {
                    Engine_Api::_()->sitepage()->sendMail("ACTIVE", $sitepage->page_id);
                } else {
                    Engine_Api::_()->sitepage()->sendMail("APPROVAL_PENDING", $sitepage->page_id);
                }

                // $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
                // $row = $manageadminsTable->createRow();
                // $row->user_id = $sitepage->owner_id;
                // $row->page_id = $sitepage->page_id;
                // $row->save();
                // //START PROFILE MAPS WORK
                // Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->profileMapping($sitepage);


                $page_id = $sitepage->page_id;
                if (!empty($sitepageUrlEnabled)) {
                    $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $values['page_url'] = $values['page_url'] . '-' . $page_id;
                        $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
                    } else {
                        $values['page_url'] = $values['page_url'];
                        $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
                    }
                }

                $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
                if ($sitepageFormEnabled) {
                    $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
                    $params = $tablecontent->select()
                                    ->from($tablecontent->info('name'), 'params')
                                    ->where('name = ?', 'sitepageform.sitepage-viewform')
                                    ->query()->fetchColumn();
                    $decodedParam = Zend_Json::decode($params);
                    $tabName = $decodedParam['title'];
                    if (empty($tabName))
                        $tabName = 'Form';
                    $sitepageformtable = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
                    $optionid = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
                    $table_option = Engine_Api::_()->fields()->getTable('sitepageform', 'options');
                    $sitepageform = $table_option->createRow();
                    $sitepageform->setFromArray($values);
                    $sitepageform->label = $values['title'];
                    $sitepageform->field_id = 1;
                    $option_id = $sitepageform->save();
                    $optionids = $optionid->createRow();
                    $optionids->option_id = $option_id;
                    $optionids->page_id = $page_id;
                    $optionids->save();
                    $sitepageforms = $sitepageformtable->createRow();
                    if (isset($sitepageforms->offer_tab_name))
                        $sitepageforms->offer_tab_name = $tabName;
                    $sitepageforms->description = 'Please leave your feedback below and enter your contact details.';
                    $sitepageforms->page_id = $page_id;
                    $sitepageforms->save();
                }


                // Set photo
                if (!empty($_FILES)) {
                    Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->setPhoto($_FILES['photo'], $sitepage);
                    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
                    $album_id = $albumTable->update(array('photo_id' => $sitepage->photo_id), array('page_id = ?' => $sitepage->page_id));
                }


                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $tags = array_filter(array_map("trim", $tags));
                $sitepage->tags()->addTagMaps($viewer, $tags);

                $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);
                $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
                if (!empty($page_id)) {
                    if (!empty($multipleLocation)) {
                        if (!empty($values['main_location'])) {
                            $sitepage->location = $values['location'];
                            $sitepage->save();
                        } elseif ($sitepage->location == $location->location) {
                            $sitepage->location = '';
                            $sitepage->save();
                        }
                        if ($location->location != $values['location']) {
                            $locationTable->delete(array('location_id =?' => $location_id));
                            $sitepage->setLocation($values['location']);
                        }
                    } else {
                        if (!empty($values['location']) && $values['location'] != $location->location) {
                            $locationTable->delete(array('location_id =?' => $location_id));
                            $sitepage->location = $values['location'];
                            $sitepage->setLocation($values['location']);
                            $sitepage->save();
                        }
                    }
                    $location = $values['location'];

                    if (isset($values['locationname']))
                        $locationName = $values['locationname'];


                    if (!empty($location)) {
                        // $sitepage->setLocation();

                        if (!empty($multipleLocation)) {
                            $locationTable->update(array('location' => $location, 'locationname' => $locationName), array('page_id =?' => $page_id, 'location_id =?' => $location_id));
                        } else {
                            $locationTable->update(array('location' => $location), array('page_id =?' => $page_id, 'location_id =?' => $location_id));
                        }
                    } else {
                        $locationTable->delete(array('page_id =?' => $page_id, 'location_id =?' => $location_id));
                    }
                }

                // Set privacy
                $auth = Engine_Api::_()->authorization()->context;

                // Get the page admin list.
                $ownerList = $sitepage->getPageOwnerList();

                $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if (!empty($sitepagememberEnabled)) {
                    $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }




                if (!isset($values['auth_view']) || empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (!isset($values['auth_comment']) || empty($values['auth_comment'])) {
                    $values['auth_comment'] = "everyone";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($sitepage, $role, 'print', 1);
                    $auth->setAllowed($sitepage, $role, 'tfriend', 1);
                    $auth->setAllowed($sitepage, $role, 'overview', 1);
                    $auth->setAllowed($sitepage, $role, 'map', 1);
                    $auth->setAllowed($sitepage, $role, 'insight', 1);
                    $auth->setAllowed($sitepage, $role, 'layout', 1);
                    $auth->setAllowed($sitepage, $role, 'contact', 1);
                    $auth->setAllowed($sitepage, $role, 'form', 1);
                    $auth->setAllowed($sitepage, $role, 'offer', 1);
                    $auth->setAllowed($sitepage, $role, 'invite', 1);
                }

                $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if (!empty($sitepagememberEnabled)) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                // Start work on sub-page
                if (empty($values['sspcreate'])) {
                    $values['sspcreate'] = "owner";
                    $values['auth_sspcreate'] = "owner";
                }

                $createMax = array_search($values['auth_sspcreate'], $roles);
                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'sspcreate', ($i <= $createMax));
                }
                // End work on sub-page
                // Start sitepagediscussion plugin work
                $sitepagediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
                if ($sitepagediscussionEnabled) {

                    // Start discussion privacy work
                    if (empty($values['sdicreate'])) {
                        $values['sdicreate'] = "registered";
                    }

                    $createMax = array_search($values['sdicreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $createMax));
                    }
                    // End discussion privacy work
                }
                // End sitepagediscussion plugin work
                // Start sitepagealbum plugin work
                $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
                if ($sitepagealbumEnabled) {

                    // Start photo privacy work
                    if (empty($values['spcreate'])) {
                        $values['spcreate'] = "registered";
                    }

                    $createMax = array_search($values['spcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $createMax));
                    }
                    // End photo privacy work
                }
                // End sitepagealbum plugin work
                // Start sitepagedocument plugin work
                $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
                if ($sitepageDocumentEnabled) {
                    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                    if (!empty($sitepagememberEnabled)) {
                        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    } else {
                        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    }

                    if (empty($values['sdcreate'])) {
                        $values['sdcreate'] = "registered";
                    }

                    $createMax = array_search($values['sdcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $createMax));
                    }
                }
                // End sitepagedocument plugin work 
                // Start sitepagevideo plugin work
                $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
                if ($sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                    if (empty($values['svcreate'])) {
                        $values['svcreate'] = "registered";
                    }

                    $createMax = array_search($values['svcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $createMax));
                    }
                }
                // End sitepagevideo plugin work
                // Start sitepagepoll plugin work
                $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
                if ($sitepagePollEnabled) {
                    if (empty($values['splcreate'])) {
                        $values['splcreate'] = "registered";
                    }

                    $createMax = array_search($values['splcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $createMax));
                    }
                }
                // End sitepagepoll plugin work
                // Start sitepagenote plugin work
                $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
                if ($sitepageNoteEnabled) {
                    if (empty($values['sncreate'])) {
                        $values['sncreate'] = "registered";
                    }

                    $createMax = array_search($values['sncreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $createMax));
                    }
                }


                // End sitepagenote plugin work
                // Start sitepagemusic plugin work
                $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
                if ($sitepageMusicEnabled) {
                    if (empty($values['smcreate'])) {
                        $values['smcreate'] = "registered";
                    }

                    $createMax = array_search($values['smcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'smcreate', ($i <= $createMax));
                    }
                }
                // End sitepagemusic plugin work
                // Start sitepageevent plugin work
                $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
                if ($sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                    if (empty($values['secreate'])) {
                        $values['secreate'] = "registered";
                    }

                    $createMax = array_search($values['secreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'secreate', ($i <= $createMax));
                    }
                }
                // End sitepageevent plugin work
                // Start sitepagemember plugin work
                $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                if ($sitepageMemberEnabled) {
                    $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                    $row = $membersTable->createRow();
                    $row->resource_id = $sitepage->page_id;
                    $row->page_id = $sitepage->page_id;
                    $row->user_id = $sitepage->owner_id;
                    $row->notification = '0';
                    //$row->action_notification = '["posted","created"]';
                    $row->save();
                    Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
                    $sitepage->save();
                }
                $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.option', 1);
                $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.option', 1);
                if (empty($memberInvite)) {
                    $memberInviteOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.automatically', 1);
                    $sitepage->member_invite = $memberInviteOption;
                    $sitepage->save();
                }
                if (empty($member_approval)) {
                    $member_approvalOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.automatically', 1);
                    $sitepage->member_approval = $member_approvalOption;
                    $sitepage->save();
                }
                // End sitepagemember plugin work
                // Start business integration work
                $business_id = $this->_getParam('business_id');
                if (!empty($business_id)) {
                    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessintegration');
                    if (!empty($moduleEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitebusinessintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitepage->owner_id;
                        $row->business_id = $business_id;
                        $row->resource_type = 'sitepage_page';
                        $row->resource_id = $sitepage->page_id;
                        $row->save();
                    }
                }
                $group_id = $this->_getParam('group_id');
                if (!empty($group_id)) {
                    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
                    if (!empty($moduleEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitepage->owner_id;
                        $row->group_id = $group_id;
                        $row->resource_type = 'sitepage_page';
                        $row->resource_id = $sitepage->page_id;
                        $row->save();
                    }
                }
                // End business integration work
                // Start store integration work
                $store_id = $this->_getParam('store_id');
                if (!empty($store_id)) {
                    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
                    $sitestoreEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
                    if (!empty($moduleEnabled) && !empty($sitestoreEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitepage->owner_id;
                        $row->store_id = $store_id;
                        $row->resource_type = 'sitepage_page';
                        $row->resource_id = $sitepage->page_id;
                        $row->save();
                    }
                }
                // End store integration work
                // Start sub-pages work
                $parent_id = $this->_getParam('parent_id');
                if (!empty($parent_id)) {
                    $sitepage->subpage = 1;
                    $sitepage->parent_id = $parent_id;
                    $sitepage->save();
                }
                // End sub-pages work
                // Custom field work
                $categoryIds = array();
                $categoryIds[] = $values['category_id'];
                $categoryIds[] = $values['subcategory_id'];
                $categoryIds[] = $values['subsubcategory_id'];

                $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitepage')->getProfileType($categoryIds, 0, 'profile_type');

                // Update the profile field on profile field values
                $fieldValues = Engine_Api::_()->fields()->getFieldsValues($sitepage);
                $fieldvalue = $fieldValues->getRowsMatching(array(
                    'item_id' => $sitepage->page_id,
                    'field_id' => 1,
                ));

                if (!empty($fieldvalue) && count($fieldvalue) == 1) {
                    $fieldvalue[0]->value = $values['profile_type'];
                    $fieldvalue[0]->save();
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {

                    // Start form validation
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepage')->getFieldsFormValidations($values);
                    $values['validators'] = $profileFieldsValidators;
                    $profileFieldsValidationMessage = $this->isValid($values);
                }

                if (is_array($eventValidationMessage) && is_array($profileFieldsValidationMessage))
                    $validationMessage = array_merge($eventValidationMessage, $profileFieldsValidationMessage);

                else if (is_array($eventValidationMessage))
                    $validationMessage = $eventValidationMessage;
                else if (is_array($profileFieldsValidationMessage))
                    $validationMessage = $profileFieldsValidationMessage;
                else
                    $validationMessage = 1;

                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                // Custom field work
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.profile.fields', 1)) {

                    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitepage_page');

                    // Getting profile fields
                    $getRowsMatching = $mapData->getRowsMatching('option_id', $values['profile_type']);
                    $fieldValues = Engine_Api::_()->fields()->getFieldsValues($sitepage);

                    // Looking for data in form post and inserting in field values
                    if (!empty($getRowsMatching)) {
                        foreach ($getRowsMatching as $field) {
                            $key = $field->field_id . '_' . $field->option_id . '_' . $field->child_id . '_field_' . $field->child_id;
                            if (isset($values[$key])) {
                                $fieldvalue = $fieldValues->getRowsMatching(array(
                                    'field_id' => $field->child_id,
                                    'item_id' => $sitepage->page_id,
                                ));

                                if (!empty($fieldvalue)) {
                                    $fieldvalue[0]->value = $values[$key];
                                    $fieldvalue[0]->save();
                                } else {
                                    $valuesRow = $fieldValues->createRow();
                                    $valuesRow->field_id = $field->child_id;
                                    $valuesRow->item_id = $sitepage->page_id;
                                    $valuesRow->index = 0;
                                    $valuesRow->value = $values[$key];
                                    $valuesRow->save();
                                }
                            }
                        }
                    }
                }

                // Commit
                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Close/Open the directory page
     * 
     *
     */
    public function closeAction() {

        // Check method
        $this->validateRequestMethod("POST");

        // Get Page Object
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));

        // Get Viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }
        // End manage-admin check

        $db = Engine_Api::_()->getDbtable('pages', 'sitepage')->getAdapter();
        $db->beginTransaction();
        try {
            $sitepage->closed = ($sitepage->closed) ? 0 : 1;
            $sitepage->save();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Publish the directory page
     * 
     *
     */
    public function publishAction() {

        // Check method
        $this->validateRequestMethod("POST");

        // Check user validation
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $page_id = $this->_getParam('page_id');

        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Start manage-admin check
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            $this->respondWithError('unauthorized');
        }
        // End manage-admin check

        $db = Engine_Api::_()->getDbtable('pages', 'sitepage')->getAdapter();
        $db->beginTransaction();
        $search = $this->_getParam('search');
        $search = (isset($search) && !empty($search)) ? $search : 0;

        try {
            $sitepage->modified_date = new Zend_Db_Expr('NOW()');
            $sitepage->draft = 1;
            $sitepage->search = $search;
            $sitepage->save();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollback();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     *  Claim a Directory Page
     */
    public function claimAction() {

        // Get logged in user information   
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $page_id = $this->_getParam('page_id');

        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);

        if (empty($sitepage))
            $this->respondWithError('no_record');

        // Get level id
        $level_id = 0;
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
            $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
            if (isset($authorization) && !empty($authorization))
                $level_id = $authorization->level_id;
        }


        // check user have to allow claim of not
        $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', 'claim');
        $getPackageClaim = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
        if (empty(Engine_Api::_()->getApi('settings', 'core')->sitepage_claimlink) || empty($allow_claim)) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitepage')->getClaimForm();
            $this->respondWithSuccess($response, true);
        } elseif ($this->getRequest()->isPost()) {
            $value = $this->_getAllParams();
            if (empty($value['email']))
                $value['email'] = $viewer->email;

            if (empty($value['nickname']))
                $value['nickname'] = $viewer->displayname;

            $items = array();
            $items['page_id'] = $page_id;
            $items['viewer_id'] = $viewer_id;

            $claimpages = Engine_Api::_()->getDbtable('claims', 'sitepage')->getClaimStatus($items);
            if (!empty($claimpages)) {
                if ($claimpages->status == 3 || $claimpages->status == 4) {
                    $error = "You have already filed a claim for the page: $sitepage->title, which is either on hold or is awaiting action by administration.";
                    $this->respondWithError('already_claimed', $error);
                } elseif ($claimpages->status == 2) {
                    $error = "You have already filed a claim for the page: $sitepage->title , which has been declined by the site admin.";
                    $this->respondWithError('already_claimed', $error);
                }
            }

            $email = $values['email'];

            // Check email validation
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            if (!$validator->isValid($email)) {
                $this->respondWithValidationError('validation_fail', 'Invalid email address value');
            }

            // Get admin email
            $coreApiSettings = Engine_Api::_()->getApi('settings', 'core');
            $adminEmail = $coreApiSettings->getSetting('core.mail.contact', $coreApiSettings->getSetting('core.mail.from', "email@domain.com"));
            if (!$adminEmail)
                $adminEmail = $coreApiSettings->getSetting('core.mail.from', "email@domain.com");

            // Get claim table
            $tableClaim = Engine_Api::_()->getDbTable('claims', 'sitepage');
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                if (!empty($getPackageClaim)) {

                    // Get sitepage item
                    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

                    // Get page url
                    $page_url = Engine_Api::_()->sitepage()->getPageUrl($page_id);
                    $page_title = $sitepage->title;

                    // Send claim email
                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.claim.email', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage_claimlink', 1)) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($adminEmail, 'SITEPAGE_CLAIMOWNER_EMAIL', array(
                            'page_title' => $page_title,
                            'page_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true) . '"  >' . $page_title . ' </a>',
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true),
                            'email' => $coreApiSettings->getSetting('core.mail.from', "email@domain.com"),
                            'queue' => true
                        ));
                    }

                    $row = $tableClaim->createRow();
                    $row->page_id = $page_id;
                    $row->user_id = $viewer_id;
                    $row->about = $values['about'];
                    $row->nickname = $values['nickname'];
                    $row->email = $email;
                    $row->contactno = $values['contactno'];
                    $row->usercomments = $values['usercomments'];
                    $row->status = 3;
                    $row->save();
                }
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     *  Send a message to page owner
     */
    public function messageownerAction() {

        // Get viewer detail
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // Get page id and page object
        $page_id = $this->_getParam("page_id");
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);

        if (empty($sitepage))
            $this->respondWithError('no_record');

        // Page owner can't send message to himself
        if ($viewer_id == $sitepage->owner_id)
            $this->respondWithError('unauthorized');

        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitepage')->getMessageOwnerForm();
            $this->respondWithSuccess($response, true);
        } elseif ($this->getRequest()->isPost()) {

            // Get admins id for sending message
            $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($page_id);
            $manageAdminData = $manageAdminData->toArray();

            $recipients = array();
            if (!empty($manageAdminData)) {
                foreach ($manageAdminData as $key => $user_ids) {
                    $user_id = $user_ids['user_id'];
                    if ($viewer_id != $user_id) {
                        $recipients[] = $user_id;
                    }
                }
            }

            $values = $this->getAllParams();
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepage')->getMessageOwnerFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();

            try {

                // Limit recipients if it is not a special list of members
                $recipients = array_slice($recipients, 0, 1000);
                $recipients = array_unique($recipients);
                $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
                $sitepage_title = $sitepage->title;
                $page_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view') . ">$sitepage_title</a>";
                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                        $viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->translate("This message corresponds to the Page:") . $page_title_with_link
                );

                foreach ($recipientsUsers as $user) {
                    if ($user->getIdentity() == $viewer->getIdentity()) {
                        continue;
                    }

                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                            $user, $viewer, $conversation, 'message_new'
                    );
                }

                // Increment message counter
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     *  Message friends about this page
     */
    public function tellafriendAction() {

        // Check user validation
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        // Get form
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Sitepage')->getTellAFriendForm();
            $response['formValues']['sender_name'] = $viewer->getTitle();
            $response['formValues']['sender_email'] = $viewer->email;

            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            // Form validation
            // Get page id and object
            $page_id = $this->_getParam('page_id', $this->_getParam('page_id', null));
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

            if (empty($sitepage))
                $this->respondWithError('no_record');


            // Get form values
            $values = $this->_getAllParams();

            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepage')->tellaFriendFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            // Explode email ids
            $reciver_ids = explode(',', $values['receiver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];

            $heading = $sitepage->title;

            // Check valid email id format
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $this->respondWithValidationError('validation_fail', 'Invalid sender email address value');
            }

            if (!empty($reciver_ids)) {
                foreach ($reciver_ids as $receiver_id) {
                    $receiver_id = trim($receiver_id, ' ');
                    ($reciver_ids);
                    if (!$validator->isValid($receiver_id)) {
                        $this->respondWithValidationError('validation_fail', 'Please enter correct email address of the receiver(s).');
                    }
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEPAGE_TELLAFRIEND_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender_name' => $sender,
                    'sender_email' => $sender_email,
                    'page_title' => $sitepage->getTitle(),
                    'header' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => "http://" . $_SERVER['HTTP_HOST'] . $sitepage->getHref(),
                    'email' => $sender_email,
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

    /*
     * Overview
     *
     */
    public function overviewAction() {
        // Validate request method
        $this->validateRequestMethod();

        // Get page id and object
        $page_id = $this->_getParam('page_id');
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');

        $overview = @str_replace('src="/', 'src="' . $this->getHost . '/', $sitepage->overview);
        $this->respondWithSuccess($overview, true);
    }

    /*
     * Payment Method selection
     *
     */
    public function gatewayAction() {
        $this->validateRequestMethod();
        // Get page id and object
        $page_id = $this->_getParam('page_id', $this->_session->page_id);
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer || !$viewer_id)
            $this->respondWithError('unauthorized');

        $existManageAdmin = Engine_Api::_()->sitepage()->isPageOwner($sitepage);

        if (!$sitepage->package_id)
            $this->respondWithError('no_record');

        $package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);

        // Gateways
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()
                ->where('enabled = ?', 1)
        ;
        $gateways = $gatewayTable->fetchAll($gatewaySelect);
        $gatewayPlugins = array();
        foreach ($gateways as $gateway) {
            // Check billing cycle support
            if (!$package->isOneTime()) {
                $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                    continue;
                }
            }
            $gatewayPlugins[] = array(
                'gateway' => $gateway,
                'plugin' => $gateway->getGateway(),
            );
        }

        $response = $tempArray = array();

        foreach ($gatewayPlugins as $row => $value) {
            $gateway = $value['gateway'];
            $plugin = $value['plugin'];
            $tempArray[] = array(
                'title' => $this->translate("Pay with " . $gateway->title),
                'url' => 'sitepage/process/' . $subject->getIdentity(),
                'urlParams' => array(
                    'gateway_id' => $gateway->gateway_id,
                ),
            );
        }


        $response['gateways'] = $tempArray;
        $response['getTotalItemCount'] = count($tempArray);

        $this->respondWithSuccess($response, true);
    }

    /*
     * Process payment
     */
    public function processAction() {
        $this->validateRequestMethod();
        // Get page id and object
        $page_id = $this->_getParam('page_id');
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer || !$viewer_id)
            $this->respondWithError('unauthorized');

        $gateway_id = $this->_getParam('gateway_id');
        if (!$gateway_id)
            $this->respondWithValidationError('parameter_missing', array('gateway_id' => 'Please select a Gateway first'));

        $gateway = Engine_Api::_()->getItem('sitepage_gateway', $gateway_id);

        if (!$gateway)
            $this->respondWithError('no_record');

        $package = $sitepage->getPackage();

        $existManageAdmin = Engine_Api::_()->sitepage()->isPageOwner($subject);
        if (!$existManageAdmin)
            $this->respondWithError('unauthorized');

        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }

        $ordersTable->insert(array(
            'user_id' => $viewer->getIdentity(),
            'gateway_id' => $gateway->gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'sitepage_page',
            'source_id' => $subject->page_id,
        ));
        $order_id = $ordersTable->getAdapter()->lastInsertId();

        $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();
        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];

        // Prepare transaction
        $params = array();
        $user_language = $viewer->language;
        $localeParts = explode('_', $user_language);
        if (count($localeParts) > 1) {
            $params['region'] = $localeParts[1];
        }
        $params['vendor_order_id'] = $order_id;

        $params['return_url'] = array(
            'name' => 'return_url',
            'url' => 'sitepage/return/' . $subject->getIdentity(),
            'urlParams' => array(
                'order_id' => $order_id,
                'state' => 'return',
            ),
        );

        $params['cancel_url'] = array(
            'name' => 'cancel_url',
            'url' => 'sitepage/return/' . $subject->getIdentity(),
            'urlParams' => array(
                'order_id' => $order_id,
                'state' => 'cancel',
            ),
        );

        $params['ipn_url'] = array(
            'name' => 'ipn_url',
            'url' => 'sitepage/ipn/' . $subject->getIdentity(),
            'urlParams' => array(
                'order_id' => $order_id,
                'gatewaytype' => 'index',
            ),
        );

        // Process transaction
        $transaction = $plugin->createPageTransaction($viewer, $subject, $package, $params);

        // Pull transaction params
        $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $transactionData = $transaction->getData();

        $response = array(
            'name' => 'payment_url',
            'url' => $transactionUrl,
            'method' => $transactionMethod,
            'urlParams' => $transactionData,
        );

        $this->respondWithSuccess($response, true);
    }

    /*
     * Gateway return action
     */
    public function returnAction() {
        // Get page id and object
        $page_id = $this->_getParam('page_id');
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer || !$viewer_id)
            $this->respondWithError('unauthorized');

        $order_id = $this->_getParam('order_id');
        if (!$order_id)
            $this->respondWithValidationError('parameter_missing', array('order_id' => 'order_id missing'));

        $order = Engine_Api::_()->getItem('payment_order', $order_id);

        if ($order->user_id != $viewer_id)
            $this->respondWithValidationError('parameter_missing', array('user_id' > 'user id of order does not match with the current user'));

        $gateway = Engine_Api::_()->getItem('sitepage_gateway', $order->gateway_id);

        if (!$gateway)
            $this->respondWithError('no_record');

        $package = $sitepage->getPackage();

        $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.level.createhost', 0);

        $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);
        $LevelHost = $this->checkLevelHost($levelHost, 'sitepage');
        $PackagesHost = $this->checkPackageHost($package);

        if (($PackagesHost != $LevelHost)) {
            $response['status'] = 'active';
            $this->respondWithSuccess($response, true);
        }

        $plugin = $gateway->getPlugin();

        try {
            $status = $plugin->onPageTransactionReturn($order, $this->_getAllParams());
            $response['status'] = $status;
            $this->respondWithSuccess($response, true);
        } catch (Payment_Model_Exception $e) {
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Ipn action
     */
    public function ipnAction() {
        $params = $this->_getAllParams();
        $gatewayType = $params['gatewaytype'];
        $gatewayId = (!empty($params['gateway_id']) ? $params['gateway_id'] : null );
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        unset($params['rewrite']);
        unset($params['gateway_id']);
        if (!empty($gatewayType) && 'index' !== $gatewayType) {
            $params['gatewayType'] = $gatewayType;
        } else {
            $gatewayType = null;
        }

        // Log ipn
        $ipnLogFile = APPLICATION_PATH . '/temporary/log/sitepage-payment-ipn.log';
        file_put_contents($ipnLogFile, date('c') . ': ' .
                print_r($params, true), FILE_APPEND);

        try {

            // Get gateways
            $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
            $gateways = $gatewayTable->fetchAll(array('enabled = ?' => 1));

            // Try to detect gateway
            $activeGateway = null;
            foreach ($gateways as $gateway) {
                $gatewayPlugin = $gateway->getPlugin();

                // Action matches end of plugin
                if ($gatewayType &&
                        substr(strtolower($gateway->plugin), - strlen($gatewayType)) == strtolower($gatewayType)) {
                    $activeGateway = $gateway;
                } else if ($gatewayId && $gatewayId == $gateway->gateway_id) {
                    $activeGateway = $gateway;
                } else if (method_exists($gatewayPlugin, 'detectIpn') &&
                        $gatewayPlugin->detectIpn($params)) {
                    $activeGateway = $gateway;
                }
            }
        } catch (Exception $e) {
            // Gateway detection failed
            file_put_contents($ipnLogFile, date('c') . ': ' .
                    'Gateway detection failed: ' . $e->__toString(), FILE_APPEND);
            echo 'ERR';
            exit(1);
        }

        // Gateway could not be detected
        if (!$activeGateway) {
            file_put_contents($ipnLogFile, date('c') . ': ' .
                    'Active gateway could not be detected.', FILE_APPEND);
            echo 'ERR';
            exit(1);
        }

        // Validate ipn
        try {
            $gateway = $activeGateway;
            $gatewayPlugin = $gateway->getPlugin();

            $ipn = $gatewayPlugin->createIpn($params);
        } catch (Exception $e) {
            // IPN validation failed
            file_put_contents($ipnLogFile, date('c') . ': ' .
                    'IPN validation failed: ' . $e->__toString(), FILE_APPEND);
            echo 'ERR';
            exit(1);
        }


        // Process IPN
        try {
            $gatewayPlugin->onIpn($ipn);
        } catch (Exception $e) {
            $gatewayPlugin->getGateway()->getLog()->log($e, Zend_Log::ERR);
            // IPN validation failed
            file_put_contents($ipnLogFile, date('c') . ': ' .
                    'IPN processing failed: ' . $e->__toString(), FILE_APPEND);
            echo 'ERR';
            exit(1);
        }

        // Exit
        echo 'OK';
        exit(0);
    }

    /**
     * 
     * Returns the basic information and profile fields of the Directory Page
     * 
     * 
     */
    public function informationAction() {

        // Validate request method
        $this->validateRequestMethod();

        // Get page id and object
        $page_id = $this->_getParam('page_id');
        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getInformation($sitepage), true);
    }

    /**
     * Follow and unfollow a page
     */
    public function followAction() {

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Validate request method
        $this->validateRequestMethod("POST");

        // Get page id and object
        $page_id = $this->_getParam('page_id');

        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');


        $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $follow = $followTable->getFollow($sitepage, $viewer);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            if ($follow) {
                $follow->delete();

                $sitepage->follow_count = $sitepage->follow_count - 1;
                $sitepage->save();
            } else {
                $newrow = $followTable->createRow();
                $newrow->resource_type = $sitepage->getType();
                $newrow->resource_id = $sitepage->getIdentity();
                $newrow->poster_type = $viewer->getType();
                $newrow->poster_id = $viewer->getIdentity();
                $newrow->creation_date = date("Y-m-d H:i:s");
                $newrow->save();

                $sitepage->follow_count = $sitepage->follow_count + 1;
                $sitepage->save();
            }

            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Set Contact Info in Page
     */
    public function getContactAction() {

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // Get page id and object
        $page_id = $this->_getParam('page_id');

        $sitepage = $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (empty($sitepage) && !isset($sitepage))
            $this->respondWithError('no_record');

        $sitePageArray = $sitepage->toArray();

        if ($this->getRequest()->isGet()) {
            $contactForm[] = array(
                'type' => 'Text',
                'name' => 'email',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email'),
            );
            $contactForm[] = array(
                'type' => 'Text',
                'name' => 'phone',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Phone'),
            );
            $contactForm[] = array(
                'type' => 'Text',
                'name' => 'website',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Website'),
            );
            $contactForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
            );

            $response['form'] = $contactForm;
            $response['formValues']['email'] = $sitePageArray['email'];
            $response['formValues']['phone'] = $sitePageArray['phone'];
            $response['formValues']['website'] = $sitePageArray['website'];
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $sitepage->website = $values['website'];
                $sitepage->phone = $values['phone'];
                $sitepage->email = $values['email'];
                $sitepage->save();
                $db->commit();

                $sitePageArray = $sitepage->toArray();
                $response['email'] = $sitePageArray['email'];
                $response['phone'] = $sitePageArray['phone'];
                $response['website'] = $sitePageArray['website'];
                $this->respondWithSuccess($response, true);
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
        }
    }

    public function projectsAction() {
        $this->validateRequestMethod();

        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitepage_page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');
        // Get page id and object
        $page_id = $this->_getParam('page_id');
        $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageIdOrganization($page_id);
        $projects  = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageId($projectsIds);


        // Return if no subject available.
        if (empty($subject))
            $this->respondWithError('no_record');
        $params = $this->_getAllParams();
        $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getSitepage($sitepage, $params);
        if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order']) && $_REQUEST['field_order'] == 1) {
            $response['profile_fields'] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($response['profile_fields']);
        }

        $this->respondWithSuccess($projects, true);
    }
    public function initiativesAction() {
        $this->validateRequestMethod();

        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitepage_page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');
        // Get page id and object
        $page_id = $this->_getParam('page_id');

        $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);

        $this->respondWithSuccess($initiatives, true);
    }
    public function categoryAction() {
        $this->validateRequestMethod();

        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitepage_page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');
        // Get page id and object
        $category_id = $this->_getParam('category_id');

        $projects = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getProjectByCategory($category_id);

        $this->respondWithSuccess($projects, true);
    }
    public function coreAction() {
        $this->validateRequestMethod();

        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitepage_page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');
        // Get page id and object
        $page_id = $this->_getParam('page_id');

        $initiativesId = $this->_getParam('initiative_id');
        $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageIdAndInitiativesId($page_id,$initiativesId,null) ;
        $projects  = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageId($projectsIds);


        $this->respondWithSuccess($projects, true);

    }



}
