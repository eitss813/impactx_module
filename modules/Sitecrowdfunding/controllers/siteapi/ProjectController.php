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
class Sitecrowdfunding_ProjectController extends Siteapi_Controller_Action_Standard {
    /*
     * PACKAGE ENABLE
     */

    protected $_hasPackageEnable;

    public function init() {
// SET LANGUAGE TRANSLATOR 
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        //SET VIEW AND LOCALE 
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

//SET THE SUBJECT
        if (0 !== ($project_id = (int) $this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }

        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    /*
     * Calling of adv search form
     * 
     * @return JSON
     */

    public function searchFormAction() {
        //Variable Declaration 
        $searchForm = array();
        // Validate request methods
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        try {
            $searchForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getSearchForm();
            $this->respondWithSuccess($searchForm, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * Enable Packages
     * 
     * @return JSON
     */

    public function packagesAction() {
        //Variable declaraion Here...
        $bodyParams = array();
        $values = array();
        $errorMessage = array();
        //For Loged out User
        if (!$this->_helper->requireUser()->isValid()) {
            $this->respondWithError('unauthorized', "Only login user can create this project");
        }

        //Create permission....
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "create")->isValid()) {

            $this->respondWithError('unauthorized', "You don't have permission to create project");
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        try {

            $values['owner_id'] = $viewer->getIdentity();
            $values['allProjects'] = 'all';
            $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($values);
            $current_count = $paginator->getTotalItemCount();

            //max allow to create project
            $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "max");
            if (!empty($quota) && $current_count >= $quota) {
                $this->respondWithError('unauthorized', 'You have already started the maximum number of projects allowed');
            }

            $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
            $packageInfoArray = $coreSettingsApi->getSetting('sitecrowdfunding.package.information');
            $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 0);

            if (!is_array($packageInfoArray))
                $packageInfoArray = array();

            //get all package
            $paginator = Engine_Api::_()->getDbtable('packages', 'sitecrowdfunding')->getPackagesSql($viewer->getIdentity());

            $bodyParams['getTotalItemCount'] = $paginator->getTotalItemCount();

            foreach ($paginator as $row => $package) {
                $packageShowArray = array();

                if (isset($package->package_id) && !empty($package->package_id))
                    $packageShowArray['package_id'] = $package->package_id;

                if (isset($package->title) && !empty($package->title)) {
                    $packageShowArray['title']['label'] = $this->translate('Title');
                    $packageShowArray['title']['value'] = $this->translate($package->title);
                }

                if (in_array('price', $packageInfoArray)) {
                    if ($package->price > 0.00) {
                        $packageShowArray['price']['label'] = $this->translate('Price');
                        $packageShowArray['price']['value'] = $package->price;
                        $packageShowArray['price']['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                    } else {
                        $packageShowArray['price']['label'] = $this->translate('Price');
                        $packageShowArray['price']['value'] = $this->translate('FREE');
                    }
                }

                if (in_array('billing_cycle', $packageInfoArray)) {
                    $packageShowArray['billing_cycle']['label'] = $this->translate('Billing Cycle');
                    $packageShowArray['billing_cycle']['value'] = $package->getBillingCycle();
                }

                if (in_array('duration', $packageInfoArray)) {
                    $packageShowArray['duration']['label'] = $this->translate("Duration");
                    $packageShowArray['duration']['value'] = $package->getPackageQuantity();
                }

                if (in_array('featured', $packageInfoArray)) {
                    if ($package->featured == 1) {
                        $packageShowArray['featured']['label'] = $this->translate('Featured');
                        $packageShowArray['featured']['value'] = $this->translate('Yes');
                    } else {
                        $packageShowArray['featured']['label'] = $this->translate('Featured');
                        $packageShowArray['featured']['value'] = $this->translate('No');
                    }
                }

                if (in_array('sponsored', $packageInfoArray)) {
                    if ($package->sponsored == 1) {
                        $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                        $packageShowArray['Sponsored']['value'] = $this->translate('Yes');
                    } else {
                        $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                        $packageShowArray['Sponsored']['value'] = $this->translate('No');
                    }
                }

                if (in_array('rich_overview', $packageInfoArray) && ($overview && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "overview")))) {
                    if ($package->overview == 1) {
                        $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                        $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
                    } else {
                        $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                        $packageShowArray['rich_overview']['value'] = $this->translate('No');
                    }
                }

                if (in_array('videos', $ackageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "video"))) {
                    if ($package->video == 1) {
                        if ($package->video_count) {
                            $packageShowArray['videos']['label'] = $this->translate('Videos');
                            $packageShowArray['videos']['value'] = $package->video_count;
                        } else {
                            $packageShowArray['videos']['label'] = $this->translate('Videos');
                            $packageShowArray['videos']['value'] = $this->translate("Unlimited");
                        }
                    } else {
                        $packageShowArray['videos']['label'] = $this->translate('Videos');
                        $packageShowArray['videos']['value'] = $this->translate('No');
                    }
                }

                if (in_array('photos', $packageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding', "photo"))) {
                    if ($package->photo == 1) {
                        if ($packagem->photo_count) {
                            $packageShowArray['photos']['label'] = $this->translate('Photos');
                            $packageShowArray['photos']['value'] = $package->photo_count;
                        } else {
                            $packageShowArray['photos']['label'] = $this->translate('Photos');
                            $packageShowArray['photos']['value'] = $this->translate("Unlimited");
                        }
                    } else {
                        $packageShowArray['photos']['label'] = $this->translate('Photos');
                        $packageShowArray['photos']['value'] = $this->translate('No');
                    }
                }

                if (in_array('commission', $packageInfoArray)) {
                    if (!empty($package->commission_settings)) {
                        $commissionInfo = @unserialize($package->commission_settings);
                        $commissionType = $commissionInfo['commission_handling'];
                        $commissionFee = $commissionInfo['commission_fee'];
                        $commissionRate = $commissionInfo['commission_rate'];
                    }
                    if (!empty($package->commission_settings) && isset($commissionType)) {
                        if (empty($commissionType)) {
                            $packageShowArray['commission']['label'] = $this->translate('Commission');
                            $packageShowArray['commission']['value'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency((int) $commissionFee);
                        } else {
                            $packageShowArray['commission']['label'] = $this->translate('Commission');
                            $packageShowArray['commission']['value'] = $commissionRate . '%';
                        }
                    }
                }

                if (in_array('description', $packageInfoArray)) {
                    $packageShowArray['description']['label'] = $this->translate("Description");
                    $packageShowArray['description']['value'] = $this->translate($package->description);
                }
                $packageArray["package"] = $packageShowArray;
                $tempMenu = array();
                $tempMenu[] = array(
                    'label' => $this->translate('Create Project'),
                    'name' => 'create',
                    'url' => 'crowdfunding/create',
                    "actionType" => "create",
                    "dialogueTitle" => $this->translate("Create Project"),
                    "successMessage" => $this->translate("Project Created successfuly."),
                    'urlParams' => array(
                        'package_id' => $package->package_id,
                    ),
                );
                $tempMenu[] = array(
                    'label' => $this->translate('Package Info'),
                    'name' => 'package_info',
                    'url' => 'crowdfunding/packages',
                    'urlParams' => array(
                        'package_id' => $package->package_id
                    )
                );

                $packageArray['menu'] = $tempMenu;
                $bodyParams['response'][] = $packageArray;
            }

            if (isset($bodyParams) && !empty($bodyParams))
                $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * ACTION FOR PACKAGE UPGRADATION
     * 
     * @return Json
     */

    public function upgradePackageAction() {

        //Variable Declaration......
        $bodyParams = array();
        //PACKAGE ENABLE VALIDATION
        if (!Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $this->respondWithError('unauthorized', "Package is not enabled. Please contact your admin");
        }

        //GET PROJECT ID PROJECT OBJECT AND THEN CHECK VALIDATIONS
        $project_id = $project_id = $this->_getParam('project_id');
        if (empty($project_id)) {
            $this->respondWithError('unauthorized', "Project id not found");
        }

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            $this->respondWithError('unauthorized', "product not found");
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized', "you don't have upgrade permission. Please contact your admin.");
        }

        $package_view = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.package.view', 1);

        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $packageInfoArray = $settings->getSetting('sitecrowdfunding.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "commission"));

        $show_editor = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tinymceditor', 0);

        if ($this->getRequest()->isGet()) {
            try {


                $currentPackage = $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id);
                $paginator = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageResult($project);
                $paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));

                $bodyParams['getTotalItemCount'] = $paginator->getTotalItemCount();

                foreach ($paginator as $row => $package) {
                    $packageShowArray = array();

                    if (isset($package->package_id) && !empty($package->package_id))
                        $packageShowArray['package_id'] = $package->package_id;

                    if (isset($package->title) && !empty($package->title)) {
                        $packageShowArray['title']['label'] = $this->translate('Title');
                        $packageShowArray['title']['value'] = $this->translate($package->title);
                    }

                    if (in_array('price', $packageInfoArray)) {
                        if ($package->price > 0.00) {
                            $packageShowArray['price']['label'] = $this->translate('Price');
                            $packageShowArray['price']['value'] = $package->price;
                            $packageShowArray['price']['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                        } else {
                            $packageShowArray['price']['label'] = $this->translate('Price');
                            $packageShowArray['price']['value'] = $this->translate('FREE');
                        }
                    }

                    if (in_array('billing_cycle', $packageInfoArray)) {
                        $packageShowArray['billing_cycle']['label'] = $this->translate('Billing Cycle');
                        $packageShowArray['billing_cycle']['value'] = $package->getBillingCycle();
                    }

                    if (in_array('duration', $packageInfoArray)) {
                        $packageShowArray['duration']['label'] = $this->translate("Duration");
                        $packageShowArray['duration']['value'] = $package->getPackageQuantity();
                    }

                    if (in_array('featured', $packageInfoArray)) {
                        if ($package->featured == 1) {
                            $packageShowArray['featured']['label'] = $this->translate('Featured');
                            $packageShowArray['featured']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['featured']['label'] = $this->translate('Featured');
                            $packageShowArray['featured']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('sponsored', $packageInfoArray)) {
                        if ($package->sponsored == 1) {
                            $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                            $packageShowArray['Sponsored']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                            $packageShowArray['Sponsored']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('rich_overview', $packageInfoArray) && ($overview && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "overview")))) {
                        if ($package->overview == 1) {
                            $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                            $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                            $packageShowArray['rich_overview']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('videos', $ackageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', "video"))) {
                        if ($package->video == 1) {
                            if ($package->video_count) {
                                $packageShowArray['videos']['label'] = $this->translate('Videos');
                                $packageShowArray['videos']['value'] = $package->video_count;
                            } else {
                                $packageShowArray['videos']['label'] = $this->translate('Videos');
                                $packageShowArray['videos']['value'] = $this->translate("Unlimited");
                            }
                        } else {
                            $packageShowArray['videos']['label'] = $this->translate('Videos');
                            $packageShowArray['videos']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('photos', $packageInfoArray) && (!empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding', "photo"))) {
                        if ($package->photo == 1) {
                            if ($packagem->photo_count) {
                                $packageShowArray['photos']['label'] = $this->translate('Photos');
                                $packageShowArray['photos']['value'] = $package->photo_count;
                            } else {
                                $packageShowArray['photos']['label'] = $this->translate('Photos');
                                $packageShowArray['photos']['value'] = $this->translate("Unlimited");
                            }
                        } else {
                            $packageShowArray['photos']['label'] = $this->translate('Photos');
                            $packageShowArray['photos']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('commission', $packageInfoArray)) {
                        if (!empty($package->commission_settings)) {
                            $commissionInfo = @unserialize($package->commission_settings);
                            $commissionType = $commissionInfo['commission_handling'];
                            $commissionFee = $commissionInfo['commission_fee'];
                            $commissionRate = $commissionInfo['commission_rate'];
                        }
                        if (!empty($package->commission_settings) && isset($commissionType)) {
                            if (empty($commissionType)) {
                                $packageShowArray['commission']['label'] = $this->translate('Commission');
                                $packageShowArray['commission']['value'] = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency((int) $commissionFee);
                            } else {
                                $packageShowArray['commission']['label'] = $this->translate('Commission');
                                $packageShowArray['commission']['value'] = $commissionRate . '%';
                            }
                        }
                    }

                    if (in_array('description', $packageInfoArray)) {
                        $packageShowArray['description']['label'] = $this->translate("Description");
                        $packageShowArray['description']['value'] = $this->translate($package->description);
                    }
                    $packageArray["package"] = $packageShowArray;
                    $tempMenu = array();
                    $tempMenu[] = array(
                        'label' => $this->translate('Upgrade Package'),
                        'name' => 'upgrade_package',
                        'url' => 'crowdfunding/upgrade-package',
                        "mSuccessMessage" => $this->translate('Your package has been upgraded successfully.'),
                        "mDialogueMessage" => $this->translate('Would you like to upgrade your current package ?'),
                        "mDialogueTitle" => $this->translate("Upgrade package"),
                        "mDialogueButton" => $this->translate('Upgrade'),
                        'urlParams' => array(
                            'package_id' => $package->package_id,
                            'project_id' => $project->getIdentity()
                        )
                    );
                    $tempMenu[] = array(
                        'label' => $this->translate('Package Info'),
                        'name' => 'package_info',
                        'url' => 'crowdfunding/upgrade-package',
                        'urlParams' => array(
                            'package_id' => $package->package_id,
                            'project_id' => $project->getIdentity()
                        )
                    );

                    $packageArray['menu'] = $tempMenu;
                    $bodyParams['response'][] = $packageArray;
                }

                if (isset($currentPackage) && !empty($currentPackage)) {
                    $bodyParams['currentPackage'] = $currentPackage->toArray();
                }

                if (isset($bodyParams) && !empty($bodyParams))
                    $this->respondWithSuccess($bodyParams);
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        } elseif ($this->getRequest()->getPost()) {
            $package_id = $this->_getParam('package_id');
            $package_chnage = Engine_Api::_()->getItem('sitecrowdfunding_package', $package_id);
            if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($project->getOwner()->level_id, explode(",", $package_chnage->level_id)))) {
                $this->respondWithError('unauthorized', "You don't have permission to upgrade this Project");
            }

            if (!empty($_POST['package_id'])) {
                $table = $project->getTable();
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {
                    $is_upgrade_package = true;
                    //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
                    if ($project->status == 'active')
                        $project->cancel($is_upgrade_package);

                    $project->package_id = $_POST['package_id'];
                    $package = Engine_Api::_()->getItem('sitecrowdfunding_package', $project->package_id);

                    $project->featured = $package->featured;
                    $project->sponsored = $package->sponsored;
                    $project->pending = 1;
                    $project->funding_end_date = new Zend_Db_Expr('NULL');
                    $project->funding_status = 'initial';
                    if (($package->isFree())) {
                        $project->funding_approved = $package->funding_approved;
                    } else {
                        $project->funding_approved = 0;
                    }
                    if (!empty($project->funding_approved)) {
                        $project->pending = 0;
                        $expirationDate = $package->getExpirationDate();
                        $currentDate = date('Y-m-d H:i:s');
                        if (!empty($expirationDate))
                            $project->funding_end_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $project->funding_end_date = '2250-01-01 00:00:00';

                        if (empty($project->approved_date)) {
                            $project->approved_date = date('Y-m-d H:i:s');
                            if ($project->funding_state != 'draft' && $project->search && $project->is_gateway_configured && $project->funding_start_date <= $currentDate) {
                                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($project->getOwner(), $project, 'sitecrowdfunding_project_new');
                                if ($action != null) {
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $project);
                                }
                            }
                        }
                    }
                    $project->save();
                    $db->commit();
                    $this->successResponseNoContent('no_content', true);
                } catch (Exception $ex) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $ex->getMessage());
                }
            }
        }
    }

    /*
     * CREATE PROJECT 
     * 
     * @return Json
     */

    public function createAction() {

        //VARIABLE DECLARAION
        $values = array();
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to create Project. You are logged out user");
        $package_id = $this->_getParam('package_id', 0);
        $parent_type = $this->_getParam('parent_type', null);
        $parent_id = $this->_getParam('parent_id', 0);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        if ($this->_hasPackageEnable && empty($package_id)) {
            $this->respondWithError('unauthorized', "Please choose package first.");
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        if ($this->_hasPackageEnable) {
            $packageInfoArray = $settings->getSetting('sitecrowdfunding.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "photos", "description"));
        }

        if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitecrowdfunding.createFormFields')) {
            $createFormFields = $settings->getSetting('sitecrowdfunding.createFormFields');
        }

        $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitecrowdfunding')->defaultProfileId();
        $isCreatePrivacy = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "create");
        if (empty($isCreatePrivacy))
            $this->respondWithError('unauthorized', "you don't have permission to create project.Please contact your admin");

        if ($parent_id && $parent_type) {
            $isParentCreatePrivacy = Engine_Api::_()->sitecrowdfunding()->isCreatePrivacy($parent_type, $parent_id);
            if (empty($isParentCreatePrivacy))
                $this->respondWithError('unauthorized', "you don't have permission to create project.Please contact your admin");


            $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);
        }

        //PACKAGE BASED CHECKS
        if ($this->_hasPackageEnable) {
            $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 0);

            $package = Engine_Api::_()->getItemTable('sitecrowdfunding_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
            if (empty($package)) {
                $this->respondWithError('unauthorized', "Package not available or not enable.");
            }

            if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
                $this->respondWithError('unauthorized', "You don't have permissionn to create project with this package");
            }
        }

        $values['owner_id'] = $viewer_id;
        $values['allProjects'] = 'all';
        $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($values);

        $current_count = $paginator->getTotalItemCount();
        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitecrowdfunding_project', "max");
        if (!empty($quota) && $current_count >= $quota) {
            $msg = 'You have already started the maximum number of projects allowed';
            $this->respondWithError('unauthorized', $msg);
        }

        if ($this->getRequest()->isGet()) {

            $from = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getForm($defaultProfileId, $parentTypeItem);
            $this->respondWithSuccess($from);
        }

        // If method not Post or form not valid , Return
        if ($this->getRequest()->isPost()) {
            $values = $data = $_REQUEST;
            // Start form validation
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {

                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $daysCompare = 90;
            if (isset($values['lifetime']) && $values['lifetime'] == 1) {
                $daysCompare = 1825;
            }
            $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
            $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
            //START DATE AND END DATE ARE REQUIRED
            if ($startDate > $endDate) {
                $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                $validationMessage['endtime'] = $this->translate('Please enter End Date greater than Start Date - it is required.');
            } else {
                $days = Engine_Api::_()->sitecrowdfunding()->findDays($startDate, $endDate);
            }

            if ($days > $daysCompare) {
                $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                $validationMessage['lifetime'] = $this->translate('Please do not enter duration more than 1825 days(5 Years).');
                if ($daysCompare == 90) {
                    $validationMessage['lifetime'] = $this->translate('Please do not enter duration more than 90 days.');
                }
            }

            if (!empty($validationMessage) && @is_array($validationMessage)) {

                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            if (isset($values['category_id'])) {
                $categoryIds = array();
                $categoryIds['categoryIds'][] = $values['category_id'];
                $categoryIds['categoryIds'][] = $values['subcategory_id'];
                $categoryIds['categoryIds'][] = $values['subsubcategory_id'];

                try {
                    $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                } catch (Exception $ex) {
                    $values['profile_type'] = 0;
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {     //profile fields validation
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getFieldsFormValidations($values, 'sitecrowdfunding_project');
                    $data['validators'] = $profileFieldsValidators;
                    $validationMessage = $this->isValid($data);
                }

                if (!empty($validationMessage) && @is_array($validationMessage)) {

                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }
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
                        'featured' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitecrowdfunding_project', "featured"),
                        'sponsored' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitecrowdfunding_project', "sponsored"),
                        //'approved' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitecrowdfunding_project', "approved"),
                        //'status' => 'active'
                        'approved'=> 0,
                        'status' => 'initial'
                        
                    ));
                } else {
                    $values = array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer_id,
                        'featured' => $package->featured,
                        'sponsored' => $package->sponsored,
                        //'approved' => $ackage->isFree() ? $package->approved : 0,
                        //'status' => $package->isFree() ? 'active' : 'initial'
                        'approved'=> 0,
                        'status' => 'initial'
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
                if ($days < $daysCompare) {
                    $projectModel->start_date = date('Y-m-d', strtotime($startDate));
                    $projectModel->expiration_date = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));
                    $projectModel->save();
                }

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
                $projectModel->save();
                if (isset($projectModel->package_id)) {
                    $projectModel->package_id = $package_id;
                }
                $projectModel->save();
                $project_id = $projectModel->project_id;

                //SET PHOTO
                if (!empty($_FILES['photo'])) {
                    $projectModel = Engine_Api::_()->getApi('Photo', 'siteapi')->setPhoto($_FILES['photo'], $projectModel);
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

                $categoryIds = array();
                $categoryIds[] = $projectModel->category_id;
                $categoryIds[] = $projectModel->subcategory_id;
                $categoryIds[] = $projectModel->subsubcategory_id;
                try {
                    $projectModel->profile_type = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                    $projectModel->save();
                } catch (Exception $ex) {
                    $projectModel->profile_type = 0;
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
                    if (!is_null($postMax)) {
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
                if ($project->state == 'published' && $project->approved == 1 && $project->is_gateway_configured && $project->start_date <= $currentDate) {
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
                }
                //NOTIFICATION TO SUPERADMINS FOR PROJECT CREATION 
                $superAdmins = Engine_Api::_()->user()->getSuperAdmins();
                foreach ($superAdmins as $superAdmin) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($superAdmin, $viewer, $project, 'sitecrowdfunding_project_created');
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
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

                try {
                    //save profile field
                    Engine_Api::_()->getApi('Profilefields', 'siteapi')->setProfileFields($project, $values);
                } catch (Exception $ex) {
                    
                }
                //COMMIT
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
            $bodyParams = array();
            if (!empty($projectModel)) {
                $bodyParams['response']['project_id'] = $projectModel->getIdentity();
                $bodyParams['response']['project_type'] = $projectModel->getType();
            }
            $this->respondWithSuccess($bodyParams, true);
        }
    }

    /*
     * ACTION FOR EDITING THE PROJECT
     * 
     */

    public function editAction() {
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to edit Project. You are logged out user");

        $listValues = array();
        $project_id = $this->_getParam('project_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            $this->respondWithError('no_record');
        }
        $form = array();
        //$previous_location = $project->location;
        $form['formValues'] = $project->toarray();

        $previous_category_id = $project->category_id;
        $subcategory_id = $project->subcategory_id;
        $subsubcategory_id = $project->subsubcategory_id;

        $row = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategory($subcategory_id);

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            Engine_Api::_()->core()->setSubject($project);
        }

        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('unauthorized', "You don't have permission to edit this Project.");

        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized', "You don't have permission to edit this Project.");
        }
        $parent_type = $project->parent_type;
        if (strstr($parent_type, 'sitereview_listing')) {
            $parent_type = 'sitereview_listing';
        }
        $parent_id = $project->parent_id;
        if (!empty($parent_id) && !empty($parent_type)) {
            $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);
            $isEditPrivacy = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($parent_type, $parent_id, $project);
            if (empty($isEditPrivacy))
                $this->respondWithError('unauthorized', "You don't have permission to edit this Project.");
        }

        //GET DEFAULT PROFILE TYPE ID
        $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitecrowdfunding')->defaultProfileId();
        //GET PROFILE MAPPING ID
        $formpopulate_array = $categoryIds = array();
        //MAKE FORM
        if ($this->getRequest()->isGet()) {
            $form['formValues']['fieldCategoryLevel'] = "";
            if (isset($project->category_id) && !empty($project->category_id)) {
                $categoryObject = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategory($project->category_id);

                if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && !empty($categoryObject->profile_type))
                    $form['formValues']['fieldCategoryLevel'] = 'category_id';
            }

            if (isset($project->subcategory_id) && !empty($project->subcategory_id)) {
                $categoryObject = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategory($project->subcategory_id);

                if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && !empty($categoryObject->profile_type))
                    $form['formValues']['fieldCategoryLevel'] = 'subcategory_id';
            }

            if (isset($project->subsubcategory_id) && !empty($project->subsubcategory_id)) {
                $categoryObject = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategory($project->subsubcategory_id);

                if (isset($categoryObject) && !empty($categoryObject) && isset($categoryObject->profile_type) && !empty($categoryObject->profile_type))
                    $form['formValues']['fieldCategoryLevel'] = 'subsubcategory_id';
            }

            if ($project->category_id) {
                //GET PROFILE MAPPING ID
                $categoryIds = array();
                $categoryIds[] = $project->category_id;
                if ($project->subcategory_id)
                    $categoryIds[] = $project->subcategory_id;
                if ($project->subsubcategory_id)
                    $categoryIds[] = $project->subsubcategory_id;
                try {
                    $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                } catch (Exception $ex) {
                    $previous_profile_type = 0;
                }
            }

            $leaderList = $project->getLeaderList();
            //SAVE PROJECT ENTRY
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

            $form['formValues']['tags'] = $tagString;

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if (1 == $auth->isAllowed($project, $role, "view")) {
                    $form['formValues']['auth_view'] = $roleString;
                }

                if (1 == $auth->isAllowed($project, $role, "comment")) {
                    $form['formValues']['auth_comment'] = $roleString;
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
                if (1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'topic')) {
                    $form['formValues']['auth_topic'] = $roleString;
                }

                if (1 == $sitecrowdfundingAllow->isAllowed($project, $role, 'post')) {
                    $form['formValues']['auth_post'] = $roleString;
                }
            }
            if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                if (empty($project->networks_privacy)) {
                    $form['formValues']['networks_privacy'] = array(0);
                } else {
                    $form['formValues']['networks_privacy'] = explode(",", $project->networks_privacy);
                }
            }
            $form['formValues']['starttime'] = $project->start_date;
            $form['formValues']['endtime'] = $project->expiration_date;


            $days = Engine_Api::_()->sitecrowdfunding()->findDays($project->expiration_date, $project->start_date);

            $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getForm($defaultProfileId, $parentTypeItem, $project);
            $response['formValues'] = $form['formValues'];
            $response['editForm'] = $form['formValues'];
            $this->respondWithSuccess($response);
        }

        if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {

            $data = $values = $_REQUEST;

//            $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
//            $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getFormValidators();
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {

                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $viewerIsAdmin = $viewer->isAdminOnly();


            if (!empty($project->backer_count) && !$viewerIsAdmin) {
                if ($project->start_date != $values['starttime']) {
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['starttime'] = $this->translate('This project has been backed - You can not change Start Date.');
                }

                if ($project->expiration_date != $values['endtime']) {
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['endtime'] = $this->translate('This project has been backed - You can not change End Date.');
                }
            }
            if (empty($project->backer_count) || $viewerIsAdmin) {
                $daysCompare = 90;
                if (isset($values['lifetime']) && $values['lifetime'] == 1) {
                    $daysCompare = 1825;
                }

                $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));

                if ($startDate > $endDate) {

                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['endtime'] = $this->translate('Please enter End Date greater than Start Date - it is required.');
                } else {
                    $days = Engine_Api::_()->sitecrowdfunding()->findDays($startDate, $endDate);
                }

                if ($days > $daysCompare) {
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();
                    $validationMessage['lifetime'] = $this->translate('Please do not enter duration more than 1825 days(5 Years).');
                    if ($daysCompare == 90) {
                        $validationMessage['lifetime'] = $this->translate('Please do not enter duration more than 90 days.');
                    }
                }

                if (!empty($validationMessage) && @is_array($validationMessage)) {

                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }


                if (isset($values['category_id'])) {
                    $categoryIds = array();
                    $categoryIds['categoryIds'][] = $values['category_id'];
                    $categoryIds['categoryIds'][] = $values['subcategory_id'];
                    $categoryIds['categoryIds'][] = $values['subsubcategory_id'];

                    try {
                        $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                    } catch (Exception $ex) {
                        $values['profile_type'] = 0;
                    }

                    if (isset($values['profile_type']) && !empty($values['profile_type'])) {     //profile fields validation
                        $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getFieldsFormValidations($values, 'sitecrowdfunding_project');
                        $data['validators'] = $profileFieldsValidators;
                        $validationMessage = $this->isValid($data);
                    }

                    if (!empty($validationMessage) && @is_array($validationMessage)) {

                        $this->respondWithValidationError('validation_fail', $validationMessage);
                    }
                }
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
                if (!empty($project->backer_count) && !$viewerIsAdmin) {
                    if (isset($values['goal_amount']))
                        unset($values['goal_amount']);
                }


                $projectModel->setFromArray($values);
                if (empty($project->backer_count) || $viewerIsAdmin) {
                    if ($days < $daysCompare) {
                        $projectModel->start_date = date('Y-m-d', strtotime($startDate));
                        $projectModel->expiration_date = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));
                        $projectModel->save();
                    }
                }
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

                if (isset($values['category_id']) && !empty($values['category_id'])) {
                    $categoryIds = array();
                    $categoryIds[] = $projectModel->category_id;
                    $categoryIds[] = $projectModel->subcategory_id;
                    $categoryIds[] = $projectModel->subsubcategory_id;
                    try {
                        $projectModel->profile_type = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getProfileType($categoryIds, 0, 'profile_type');
                    } catch (Exception $ex) {
                        $projectModel->profile_type = 0;
                    }

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
                    if (!is_null($postMax)) {
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
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }

    /*
     * VIEW PROJECT
     * RETURN JSON
     */

    public function viewAction() {
        $bodyParams = array();
        $this->validateRequestMethod();
        //DONT RENDER IF SUBJECT IS NOT SET
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError('no_record');
        }
        //GET PROJECT SUBJECT
        $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $projectOption = $this->_getParam('projectOption', array("title", "description", "owner", "location", "fundingRatio", "fundedAmount", "daysLeft", "backerCount", "backButton", "category", "dashboardButton", "shareOptions", "optionsButton"));

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');

        $profile_cover = $tableOtherinfo->getColumnValue($project->getIdentity(), 'profile_cover');
        $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');

        try {

            $bodyParams['response'] = $project->toArray();
            $bodyParams['response']['profile_cover'] = $profile_cover;
            $bodyParams['response']['showPhoto'] = true;

            //video project cover work
            if ($profile_cover == 0 && !empty($project->video_id) && $sitevideoEnabled && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitecrowdfunding_project", 'item_module' => 'sitecrowdfunding'))) {
                $video = Engine_Api::_()->getItem('sitevideo_video', $project->video_id);
                $bodyParams['response']['type'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->videoType($video->type);
                $bodyParams['response'] ['video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitevideo')->getVideoURL($video);
                if ($video) {
                    //video thumbnail..............
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($video);
                    if (!empty($getContentImages))
                        $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
                    $bodyParams['response']['showPhoto'] = false;
                }
            }
//...................................................

            $owner = Engine_Api::_()->user()->getUser($project->owner_id);
            if (!empty($owner)) {
                $bodyParams['response']['owner_title'] = $owner->getTitle();
            }
            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $project->category_id);
            if (!empty($category)) {
                $bodyParams['response']['category_name'] = $category->category_name;
            }

            $bodyParams['response']['show_location'] = 0;
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $projectOption) && $project->location) {
                $bodyParams['response']['show_location'] = 1;
            }

            //project image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($project);
            if (!empty($getContentImages))
                $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

            //Project Owner image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($project, true);
            if (!empty($getContentImages))
                $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
            // Like work
            $bodyParams['response']["isLike"] = $bodyParams['response']["is_like"] = (bool) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($project);
            $bodyParams['response']['like_count'] = $project->likes()->getLikeCount();



            // Suggest to Friend link show work
            $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
            if (!empty($is_suggestion_enabled)) {
                $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitecrowdfunding', $project->project_id, null, null);
                if (!empty($modContentObj)) {
                    $contentCreatePopup = @COUNT($modContentObj);
                }
                Engine_Api::_()->sitecrowdfunding()->deleteSuggestion(Engine_Api::_()->user()->getViewer()->getIdentity(), 'sitecrowdfunding', $project->project_id, 'sitecrowdfunding_project', 'sitecrowdfunding_suggestion');
                if (!empty($contentCreatePopup)) {
                    $bodyParams['response']['projectSuggLink'] = Engine_Api::_()->suggestion()->getModSettings('sitecrowdfunding', 'link');
                }
            } else {
                $bodyParams['response']['projectSuggLink'] = 0;
            }

            $fundedAmount = $project->getFundedAmount();
            $fundedRatio = $project->getFundedRatio();
            $priceOnly = 1;
            
            if((((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '3.5') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '2.6.1'))){
                $bodyParams['response']['funded_amount'] = $fundedAmount = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($fundedAmount, 1);
                $bodyParams['response']['goal_amount'] = $goalAmount = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($project->goal_amount,1);
                $bodyParams['response']['backed_amount'] = $fundedAmount;
            }
            else {
                $bodyParams['response']['funded_amount'] = $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                $bodyParams['response']['goal_amount'] = $goalAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($project->goal_amount);
                $bodyParams['response']['backed_amount'] = $fundedAmount . " " . $this->translate("Backed");
            }
            $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $bodyParams['response']['currency'] = $currency;
            $days = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_end_date);
            $daysToStart = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_start_date);
            $currentDate = date('Y-m-d');
            if ($project->backer_count > 1)
                $backerTitle = $this->translate("Backers");
            else
                $backerTitle = $this->translate("Backer");

            $bodyParams['response']['backer_count'] = $project->backer_count . " " . $backerTitle;
            $bodyParams['response']['fundedRatio'] = $fundedRatio;
            $bodyParams['response']['funded_ratio_title'] = $fundedAmount . " of " . $goalAmount . " goal";
            $projectStartDate = date('Y-m-d', strtotime($project->start_date));
            if ($project->state == 'successful') {
                $bodyParams['response']['state'] = $this->translate("Project Successfully Completed");
            } elseif ($project->state == 'failed') {
                $bodyParams['response']['state'] = $this->translate("Funding Failed");
            } elseif ($project->state == 'draft') {
                $bodyParams['response']['state'] = $this->translate("Project in Draft");
            } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
                $bodyParams['response']['state'] = $daysToStart . " " . $this->translate("Day to Live");
            } elseif ($project->lifetime) {
                $bodyParams['response']['state'] = $this->translate('Life Time');
            } elseif ($days >= 1) {
                $bodyParams['response']['state'] = $days . " " . $this->translate("Day Left");
            } else {
                $bodyParams['response']['state'] = $this->translate($project->getProjectStatus());
            }

            $bodyParams['response']['can_delete'] = $bodyParams['response']['can_edit'] = $canEdit = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($project->parent_type, $project->parent_id, $project);
            $bodyParams['response']['isFavourite'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->isFavourite($project->getIdentity(), 'sitecrowdfunding_project', $viewer->getIdentity());
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')) {

                $getMainPhotoMenu = Engine_Api::_()->getApi('Siteapi_Core', 'sitecontentcoverphoto')->getCoverPhotoMenu($project, 0, 1, 'profile');
                if (!empty($getMainPhotoMenu))
                    $bodyParams['response'] = array_merge($bodyParams['response'], $getMainPhotoMenu);
            }

            $bodyParams['response']['biography'] = $this->_getOwnerBiography($project);

            //Has reward
            $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
            $rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project->getIdentity())->query()->fetchColumn();
            
            $bodyParams['response']['backable'] =false;
            $bodyParams['response']['hasReward'] = !empty($rewardCount) ?true:false;
            if (!$project->isExpired() && $project->status == 'active' &&  !empty($project->is_gateway_configured)){
                $bodyParams['response']['backable'] =true;
            }
            

            //gutter menu and profile tab work
            // Getting the gutter-menus.
            if ($this->getRequestParam('gutter_menu', true))
                $bodyParams['menu'] = $this->_gutterMenus($project);

            if ($this->getRequestParam('tabs_menu', true))
                $bodyParams['profile_tabs'] = $this->_tabsMenus($project);

            //..........................................................
            $this->respondWithSuccess($bodyParams);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * List of Project for loged in and logedout user
     * Return Json
     */

    public function browseAction() {
        $this->validateRequestMethod();
        $params = $this->getRequestAllParams;
        $params['projectType'] = $contentType = $this->_getParam('projectType', null);
        if (empty($contentType)) {
            $params['projectType'] = $this->_getParam('projectType', 'All');
        }

        $params['selectProjects'] = $this->_getParam('selectProjects', 'all');
        $params['orderby'] = $this->_getParam('orderby', 'startDate');
        if(empty($params['orderby']))
        {
            $params['orderby'] = 'startDate';
        }

        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = $this->_getParam('limit', 20);


        $$param['latitude'] = 0;
        $param['longitude'] = 0;
        $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);

        $detactLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

        if ($detactLocation) {
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }
        $params['owner_id'] = $this->getRequestParam('user_id', null);
        $response = array();
        try {

            if (isset($params['category_id']) && !empty($params['category_id'])) {

                $profileFields = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getSearchProfileFields();

                if (isset($profileFields) && !empty($profileFields)) {
                    foreach ($profileFields[$params['category_id']] as $element) {
                        if (isset($values[$element['name']]))
                            $customFieldValues[$element['name']] = $values[$element['name']];
                    }
                }
            }

            $viewer = Engine_Api::_()->user()->getViewer();

            $response = $this->_getProject($params, $customFieldValues);
                    if($viewer && $viewer->getIdentity()){
            $multiOPtionsOrderBy = array(
                'all' => 'All',
                'backed' => 'Backed',
                'liked' => 'Liked',
                'favourite' => 'Favourite',
                "launched" => 'Launched',
                "successful" => 'Successful',
                "failed" => 'Failed'
            );

            $filter = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => $this->translate('Browse By'),
                'multiOptions' => $this->translate($multiOPtionsOrderBy)
            );
            $response['filter'] = $filter;
        }
            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /*
     * List of Project for loged in user
     * Return Json
     */

        public function manageAction() {
        $this->validateRequestMethod();
        $params = $this->getRequestAllParams;
        $params['projectType'] = $contentType = $this->_getParam('projectType', null);
        if (empty($contentType)) {
            $params['projectType'] = $this->_getParam('projectType', 'All');
        }


        $params['selectProjects'] = $this->_getParam('selectProjects', 'all');




        $params['orderby'] = $this->_getParam('orderby', 'startDate');

        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = $this->_getParam('limit', 20);


        $$param['latitude'] = 0;
        $param['longitude'] = 0;
        $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);

        $detactLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

        if ($detactLocation) {
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['search'] = null;
        $tempParam = array();
        $projectIds = array();
        $projectIds[] = ' ';

        $params['allProjects'] = 'all';
        $params['owner_type'] = $viewer->getType();
        $params['owner_id'] = $viewer->getIdentity();
        $response = array();
        try {
            $response = $this->_getProject($params, $customFieldValues, 1);

            $multiOPtionsOrderBy = array(
                'all' => 'All',
                'backed' => 'Backed',
                'liked' => 'Liked',
                'favourite' => 'Favourite',
                "launched" => 'Launched',
                "successful" => 'Successful',
                "failed" => 'Failed'
            );

            $filter = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => $this->translate('Browse By'),
                'multiOptions' => $this->translate($multiOPtionsOrderBy)
            );
            $response['filter'] = $filter;

            $this->respondWithSuccess($response);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }
    /*
     * All category of Project
     * Return Json
     */

    public function categoryAction() {

        // Validate request method
        $this->validateRequestMethod();

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        // Prepare response
        $values = $response = array();
        $category_id = $this->getRequestParam('category_id', null);
        $subCategory_id = $this->getRequestParam('subcategory_id', null);
        $subsubcategory_id = $this->getRequestParam('subsubcategory_id', null);
        $showAllCategories = $this->getRequestParam('showAllCategories', 1);
        $showCategories = $this->getRequestParam('show_categories', 1);
        $show_project = $this->getRequestParam('show_project', 1);

        if ($this->getRequestParam('show_count')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }
        $orderBy = $this->getRequestParam('orderBy', 'category_name');
        $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();


        try {
            $tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
            $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            $storage = Engine_Api::_()->storage();
            $categories = array();

            if ($showCategories) {
                if ($showAllCategories) {
                    $category_info = $tableCategory->getCategories(array(), null, 0, 0, 1);
                    $categoriesCount = count($category_info);
                    foreach ($category_info as $value) {
                        $sub_cat_array = array();
                        $photoName = $storage->get($value->photo_id, '');
                        $category_array = array(
                            'category_id' => $value->category_id,
                            'category_name' => $this->translate($value->category_name),
                            'order' => $value->cat_order,
                            'count' => $tableProject->getProjectsCount($value->category_id, 'category_id', 1),
                        );

                        if (!empty($photoName)) {
                            $category_image = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                        } else {
                            $getDefaultImage = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                            $category_image = $getDefaultImage['image_icon'];
                        }
                        $category_array['images'] = array(
                            "image_icon" => "",
                            "image" => $category_image
                        );

                        $categories[] = $category_array;
                    }
                }

                $response['categories'] = $categories;
                if (!empty($category_id)) {

                    if ($showAllCategories) {
                        $category_info2 = $tableCategory->getSubcategories($category_id);
                        foreach ($category_info2 as $subresults) {

                            $tmp_array = array(
                                'sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'count' => $tableProject->getProjectsCount($subresults->category_id, 'subcategory_id', 1),
                                'order' => $subresults->cat_order);

                            $photoName = $storage->get($subresults->photo_id, '');
                            if (!empty($photoName)) {
                                $category_image = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                            } else {
                                $getDefaultImage = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                                $category_image = $getDefaultImage['image_icon'];
                            }
                            $tmp_array['images'] = array(
                                "image_icon" => "",
                                "image" => $category_image
                            );

                            $sub_cat_array[] = $tmp_array;
                        }
                    }

                    $response['subCategories'] = $sub_cat_array;
                }

                if (!empty($subCategory_id)) {
                    $subcategory_info2 = $tableCategory->getSubcategories($subCategory_id);
                    $treesubarrays = array();
                    foreach ($subcategory_info2 as $subvalues) {
                        $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                            'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                            'count' => $tableProject->getProjectsCount($subvalues->category_id, 'subsubcategory_id', 1),
                            'order' => $subvalues->cat_order,
                        );

                        $photoName = $storage->get($subvalues->photo_id, '');
                        if (!empty($photoName)) {
                            $category_image = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                        } else {
                            $getDefaultImage = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                            $category_image = $getDefaultImage['image_icon'];
                        }
                        $treesubarray['images'] = array(
                            "image_icon" => "",
                            "image" => $category_image
                        );
                        $treesubarrays[] = $treesubarray;
                    }

                    $response['subsubCategories'] = $treesubarrays;
                }
            }

            if ($show_project && isset($category_id) && !empty($category_id)) {
                $params = array();
                $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 0);

                // Get categories
                $categories = array();

                $category_project_array = array();

                $params = $this->_getAllParams();
                // Get group results
                $category_groups_info = $this->_getProject($params);
                $response['projects'] = $category_groups_info;
            }
            if (isset($categoriesCount) && !empty($categoriesCount))
                $response['totalItemCount'] = $categoriesCount;

            if (!empty($viewer_id)) {
                $level_id = Engine_Api::_()->user()->getViewer()->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            $response['canCreate'] = $response['canCreate'] = $can_create = $allow_upload_project = Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', 'create');


            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Delete Project
     * Return Status code for success or failure
     */

    public function deleteAction() {
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (Engine_Api::_()->core()->hasSubject())
            $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($project))
            $this->respondWithError('no_record');


        $parent_id = $project->parent_id;
        if ($project->parent_type && $project->parent_id) {
            $parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($project->parent_type, $parent_id);

            $isParentDeletePrivacy = Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($project->parent_type, $project->parent_id, $project);

            if (empty($isParentDeletePrivacy))
                $this->respondWithError('unauthorized', "You cannot delete this Project");
        } else {
            if ($viewer->getIdentity() != $project->owner_id && !$this->_helper->requireAuth()->setAuthParams($project, null, 'delete')->isValid()) {
                $this->respondWithError('unauthorized', "You cannot delete this Project");
            }
            if (!Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy(null, null, $project))
                $this->respondWithError('unauthorized', "You cannot delete this Project");
        }

        $db = $project->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            Engine_Api::_()->getApi('core', 'sitecrowdfunding')->deleteProject($project);
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Add project to Favourite
     * Return Status code for success or failure
     */

    public function favouriteAction() {
        $this->validateRequestMethod('POST');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');
        if (Engine_Api::_()->core()->hasSubject())
            $project = Engine_Api::_()->core()->getSubject();

        if (empty($project))
            $this->respondWithError('no_record');

        $values = $this->_getAllParams();

        if (isset($values['value'])) {
            try {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->favourite($values['project_id'], 'sitecrowdfunding_project', $values['value']);

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        } else {
            $this->respondWithValidationError("parameter_missing", "value");
        }
    }

    /*
     * Payment configration
     * Return Json
     */

    public function paymentInfoAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError('no_record');
        } else {
            $project = Engine_Api::_()->core()->getSubject();
            $project_id = $project->project_id;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized', "You don't have permission to configure payment method");
        }

        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id)) {
            $this->respondWithError('unauthorized', "You are logout user.");
        }
        try {


            $stripeConnected = 0;
            //$paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.paymentmethod', 'paypal');
            $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.to.siteadmin', 0);
            $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.payment.method', 'normal');
            if ($paymentMethod == 'split') {
                $enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.split.gateway', array());
            } elseif ($paymentMethod == 'escrow') {
                $enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.escrow.gateway', array());
            } else {
                if (empty($paymentToSiteadmin)) {
                    $enablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.allowed.payment.gateway', array('paypal'));
                }
            }

            $projectEnabledgateway = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getColumnValue($project_id, 'project_gateway');
            if (!empty($projectEnabledgateway)) {
                $projectEnabledgateway = Zend_Json_Decoder::decode($projectEnabledgateway);
            }

            $getEnabledGateways = array();
            if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                $getEnabledGateways = Engine_Api::_()->sitegateway()->getAdditionalEnabledGateways(array('plugin' => array('Sitegateway_Plugin_Gateway_Stripe', 'Sitegateway_Plugin_Gateway_PayPalAdaptive', 'Sitegateway_Plugin_Gateway_MangoPay')));
            }
            $formValues = array();

            $formValues['stripeConnected'] = $stripeConnected = 0;
            $formValues['stripeEnabled'] = $stripeEnabled = 0;
            $formValues['adaptivepaypalEnable'] = $adaptivepaypalEnable = 0;
            $formValues['mangopayEnable'] = $mangopayEnable = 0;
            $formValues['paypalEnable'] = $paypalEnable = 0;

            $mainForm = array();
            $subform = array();
            foreach ($getEnabledGateways as $getEnabledGateway) {
                $gatewyPlugin = explode('Sitegateway_Plugin_Gateway_', $getEnabledGateway->plugin);
                $gatewayKey = strtolower($gatewyPlugin[1]);
                $gatewayKeyUC = ucfirst($gatewyPlugin[1]);

                $enable_getway_method = $this->checkEnableGetway($gatewayKey, $enablePaymentGateway);
                if (empty($enable_getway_method))
                    continue;
                if ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_Stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                    $stripUrl = '';
                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_Stripe\''));
                    if (!empty($projectGatewayObj) && !empty($projectGatewayObj->projectgateway_id)) {
                        if (is_array($projectGatewayObj->config) && !empty($projectGatewayObj->config['stripe_user_id'])) {
                            $stripeConnected = 1;
                            $stripeEnabled = 1;
                            $formValues['stripeEnable'] = $stripeEnabled;
                            $formValues['stripeConnected'] = $stripeConnected;
                        }
                    } else {
                        $stripUrl = $this->stripUrl($project);
                    }
                    $editForm = array();
                    $mainForm[] = array(
                        'type' => 'Dummy',
                        "subType" => 'payment_method',
                        "hasSubForm" => true,
                        "isActive" => $stripeEnabled,
                        'name' => 'StripeHeading',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Stripe'),
                    );

                    $mainForm[] = array(
                        "name" => "stripeEnable",
                        "type" => "Checkbox",
                        "label" => $this->translate("Stripe"),
                        "stripurl" => $stripUrl,
                        "value" => $stripeConnected
                    );
                } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_PayPalAdaptive') {
                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
                    if (!empty($projectGatewayObj)) {

                        if (!empty($projectGatewayObj->projectgateway_id)) {
                            // Populate form
                            if (is_array($projectGatewayObj->config)) {
                                $formValues = array_merge($formValues, $projectGatewayObj->config);
                            }
                            if ($projectGatewayObj->enabled == 1) {
                                $formValues['adaptivepaypalEnable'] = $adaptivepaypalEnable = 1;
                            }
                        }
                    }

                    $mainForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm($mainForm, $adaptivepaypalEnable, 0);
                } elseif ($getEnabledGateway->plugin == 'Sitegateway_Plugin_Gateway_MangoPay') {

                    $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Sitegateway_Plugin_Gateway_MangoPay\''));
                    if (!empty($projectGatewayObj)) {
                        // Populate form
                        $adminAPGateway = Engine_Api::_()->sitegateway()->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
                        $mode = 'live';
                        if ($adminAPGateway->config['test_mode']) {
                            $mode = 'sandbox';
                        }
                        $config = isset($projectGatewayObj->config[$mode]) ? ($projectGatewayObj->config[$mode]) : null;
                        if (is_array($config)) {
                            $birthday = $projectGatewayObj->config[$mode]['birthday'];
                            $config['birthday'] = date('Y-m-d', $birthday);
                            $formValues = array_merge($formValues, $config);
                        }
                        if ($projectGatewayObj->enabled == 1) {
                            $formValues['mangopayEnable'] = $mangopayEnable = 1;
                        }
                    } else {
                        $viewer = Engine_Api::_()->user()->getViewer();
                        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
                        $select = $searchTable->select();
                        $select->where('item_id = ?', $viewer->getIdentity());
                        $otherUserRecords = $searchTable->fetchRow($select);
                        if ($otherUserRecords) {
                            $formValues['first_name'] = $otherUserRecords->first_name;
                            $formValues['last_name'] = $otherUserRecords->last_name;
                            $formValues['birthday'] = $otherUserRecords->birthdate;
                        }
                        $formValues['mango_pay_email'] = $viewer->email;
                    }

                    $mangoPayForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getMangoPayForm($mainForm, $mangopayEnable);
                    $mainForm = $mangoPayForm['mainForm'];
                    $subform['mangopayHeading_1'] = $mangoPayForm['subForm'];
                }
            }

            $paypalEnable = 0;
            if (!empty($projectEnabledgateway['paypal']) || !empty($paymentToSiteadmin)) {
                $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
                if (!empty($projectGatewayObj)) {
                    $gateway_id = $projectGatewayObj->projectgateway_id;
                    $formValues['paypalEnable'] = $paypalEnable = 1;

                    $mainForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm($mainForm, $paypalEnable);

                    $formValues = array_merge($formValues, $projectGatewayObj->toArray());
                    if (is_array($projectGatewayObj->config)) {
                        $formValues = array_merge($formValues, $projectGatewayObj->config);
                    }
                }
            }
            else{
               $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
                if (!empty($projectGatewayObj)) {
                    $gateway_id = $projectGatewayObj->projectgateway_id;
                    $formValues['paypalEnable'] = $paypalEnable = 0;
                    $formValues = array_merge($formValues, $projectGatewayObj->toArray());
                    if (is_array($projectGatewayObj->config)) {
                        $formValues = array_merge($formValues, $projectGatewayObj->config);
                    }
                } 
            }
            

            //form work here.............................
            if (empty($enablePaymentGateway)) {

                $mainForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm($mainForm, $paypalEnable);
               
            } elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->sitegateway()->isValidGateway($paymentMethod)) {
                if ($paymentMethod == 'stripe' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                    $mainForm[] = array(
                        'type' => 'Dummy',
                        "subType" => 'payment_method',
                        "hasSubForm" => true,
                        "isActive" => $stripeEnabled,
                        'name' => 'StripeHeading',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Stripe'),
                    );


                    $mainForm[] = array(
                        "name" => "stripeEnable",
                        "type" => "Checkbox",
                        "label" => $this->translate("Stripe"),
                        "stripurl" => $stripUrl,
                        "value" => $stripeConnected
                    );
                }
            } else {
                if (is_array($enablePaymentGateway) && count($enablePaymentGateway) >= 1) {
                    foreach ($enablePaymentGateway as $paymentGateway) {
                        if (empty($paypalEnable) && $paymentGateway == 'paypal') {
                            $mainForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm($mainForm, $paypalEnable);
                        } elseif ($paymentGateway == 'paypaladaptive') {
                            $mainForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm($mainForm, $adaptivepaypalEnable, 0);
                        } elseif ($paymentGateway == 'mangopay' && Engine_Api::_()->sitegateway()->isValidGateway($paymentGateway)) {
                            
                        }
                    }
                    
                }
            }

            $reponse['form'] = $mainForm;
            $reponse['formValues'] = $formValues;
            //$reponse['editForm'] = $formValues;
            $this->respondWithSuccess($reponse, true);
        } catch (Exception $ex) {
            
        }
    }

    // End of payment method configration...............................

    /*
     * Save configration payment method Details
     * Return Success of failure code.
     */
    public function setProjectGatewayInfoAction() {
        $project_id = $this->_getParam('project_id', 0);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            $this->respondWithError('no_record');
        }
        $data = $formsData = $_REQUEST;
        $projectGateway = array();
        try {


            if (isset($formsData['mangopayEnable']) && !empty($formsData['mangopayEnable'])) {
                $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getMangoPayFormValidators();
                $data['validators'] = $validators;
                $validationMessage = $this->isValid($data);
                //FORM VALIDATION
                if (isset($formsData['account_type'])) {
                    $validationMessage = is_array($validationMessage) ? $validationMessage : array();

                    if ($formsData['account_type'] == 'US') {
                        if (!isset($formsData['deposit_account_type']) || empty($formsData['deposit_account_type']))
                            $validationMessage['deposit_account_type'] = "Please complete this field - it is required.";
                        if (!isset($formsData['aba']) || empty($formsData['aba']))
                            $validationMessage['aba'] = "Please complete this field - it is required.";
                        if (!isset($formsData['us_account_number']) || empty($formsData['us_account_number']))
                            $validationMessage['us_account_number'] = "Please complete this field - it is required.";
                    }
                    elseif ($formsData['account_type'] == 'CA') {
                        if (!isset($formsData['branch_code']) || empty($formsData['branch_code']))
                            $validationMessage['branch_code'] = "Please complete this field - it is required.";
                        if (!isset($formsData['bank_name']) || empty($formsData['bank_name']))
                            $validationMessage['bank_name'] = "Please complete this field - it is required.";
                        if (!isset($formsData['institution_number']) || empty($formsData['institution_number']))
                            $validationMessage['institution_number'] = "Please complete this field - it is required.";
                        if (!isset($formsData['ca_account_number']) || empty($formsData['ca_account_number']))
                            $validationMessage['ca_account_number'] = "Please complete this field - it is required.";
                    }
                    elseif ($formsData['account_type'] == 'IBAN') {
                        if (!isset($formsData['iban']) || empty($formsData['iban']))
                            $validationMessage['iban'] = "Please complete this field - it is required.";
                        if (!isset($formsData['bic']) || empty($formsData['bic']))
                            $validationMessage['bic'] = "Please complete this field - it is required.";
                    }
                    elseif ($formsData['account_type'] == 'GB') {
                        if (!isset($formsData['sort_code']) || empty($formsData['sort_code']))
                            $validationMessage['sort_code'] = "Please complete this field - it is required.";
                        if (!isset($formsData['account_number']) || empty($formsData['account_number']))
                            $validationMessage['account_number'] = "Please complete this field - it is required.";
                    }
                    elseif ($formsData['account_type'] == 'OTHER') {
                        if (!isset($formsData['other_bic']) || empty($formsData['other_bic']))
                            $validationMessage['other_bic'] = "Please complete this field - it is required.";
                        if (!isset($formsData['other_account_number']) || empty($formsData['other_account_number']))
                            $validationMessage['other_account_number'] = "Please complete this field - it is required.";
                    }
                }
                if (!empty($validationMessage) && @is_array($validationMessage)) {

                    $this->respondWithValidationError('validation_fail', $validationMessage);
                    //vallidation issues
                } else {
                    $mangoPayCompleteDetails = $formsData;
                    $mangoPayCompleteDetails['email'] = $_REQUEST['mango_pay_email'];
                    $result = $project_gateway_table->mangoPayConfigSettings($mangoPayCompleteDetails, $project);
                    if ($result['error'] == 1) {
                        $msg = $result['error_message'];
                        $this->respondWithError('unauthorized', $msg);

                        //error 
                    } else {
                        $bankDetails = $project_gateway_table->setMangoPayBankDetails($mangoPayCompleteDetails, $project);
                        if ($bankDetails['error'] == 1) {
                            $msg = $bankDetails['errorMessage'];
                            $this->respondWithError('unauthorized', $msg);
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
            if (isset($formsData['paypalEnable']) && !empty($formsData['paypalEnable'])) {
                $paypalDetails = array();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm();
                foreach ($getForm as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $paypalDetails[$element['name']] = $_REQUEST[$element['name']];
                }
                $isPaypal = true;
                $data = $paypalDetails;

                $paypalEmail = $paypalDetails['email'];
                $validationMessage = array();
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getPayPalFormValidators();
                $data['validators'] = $validators;
                $validationMessage = $this->isValid($data);
                //FORM VALIDATION
                $validation_Message = $this->isValid($mangoPayDetails);
                if (!empty($validationMessage) && @is_array($validationMessage)) {

                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }
                unset($paypalDetails['email']);
            } else {
                $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''));
                if (!empty($project_gateway_table_obj)) {
                    $project_gateway_table_obj->enabled = 0;
                    $project_gateway_table_obj->save();
                }
            }
// adaptivepaypalEnable,mangopayEnable,paypalEnable,stripeEnable
            if (isset($formsData['adaptivepaypalEnable']) && !empty($formsData['adaptivepaypalEnable'])) {
                $paypalDetails = array();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->getPayPalForm();
                foreach ($getForm as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $paypalDetails[$element['name']] = $_REQUEST[$element['name']];
                }
                $data = $paypalDetails;
                $paypalEmail = $paypalDetails['email'];
                //unset($paypalDetails['email']);
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitecrowdfunding')->getPayPalFormValidators();
                $data['validators'] = $validators;
                $validation_Message = array();
                $validationMessage = $this->isValid($data);
                //FORM VALIDATION
                $validation_Message = $this->isValid($mangoPayDetails);
                if (!empty($validationMessage) && @is_array($validationMessage)) {

                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }
            } else {
                $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
                $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
                if (!empty($project_gateway_table_obj)) {
                    $project_gateway_table_obj->enabled = 0;
                    $project_gateway_table_obj->save();
                }
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }
        // IF PAYPAL GATEWAY IS ENABLE, THEN INSERT PAYPAL ENTRY IN ENGINE4_SITECROWDFUNDING_GATEWAY TABLE
        if (!empty($paypalDetails)) {
            $project_gateway_table = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding');
            if ($isPaypal) {
                $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''));
            } else {
                $project_gateway_table_obj = $project_gateway_table->fetchRow(array('project_id = ?' => $project_id, 'plugin = \'Sitegateway_Plugin_Gateway_PayPalAdaptive\''));
            }
            if (!empty($project_gateway_table_obj))
                $gateway_id = $project_gateway_table_obj->projectgateway_id;
            else
                $gateway_id = 0;
            $paypalDetails['test_mode'] = 0;
            if ($isPaypal) {
                $adminAPGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway('Payment_Plugin_Gateway_PayPal');
                $paypalDetails['test_mode'] = $adminAPGateway->test_mode;
            } else {
                $adminAPGateway = Engine_Api::_()->sitecrowdfunding()->getPaymentGateway('Sitegateway_Plugin_Gateway_PayPalAdaptive');
                if (isset($adminAPGateway->config['test_mode'])) {
                    $paypalDetails['test_mode'] = $adminAPGateway->config['test_mode'];
                }
            }
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
                    $row->title = $isPaypal ? 'Paypal' : 'PayPalAdaptive';
                    $row->description = '';
                    $row->plugin = $isPaypal ? 'Payment_Plugin_Gateway_PayPal' : 'Sitegateway_Plugin_Gateway_PayPalAdaptive';
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
            } catch (Exception $ex) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }

            // Validate gateway config
            $gatewayObject = $gateway->getGateway();

            try {
                $gatewayObject->setConfig($paypalDetails);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $paypalEnabled = false;
                $this->respondWithValidationError('internal_server_error', "Gateway login failed. Please try again");
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
                } else {
                    $projectGateway['paypaladaptive'] = $proectPaypalId;
                }
            }
        }
//	if(Engine_Api::_()->hasModuleBootstrap('sitegateway') ) {
//	    if(isset($formsData['mangopayEnable']) && !empty($formsData['mangopayEnable'])){
//		 if(isset($formsData['email'])){
//		     unset($formsData['email']);
//		 }
//		$gatewayDetails = $formsData;
//	   
//	    $gatewayDetails['email'] = $formsData['mango_pay_email'];
//	    $sitecrowdfunding_gateway_table=Engine_Api::_()->getDbtable('projectGateways',
//		    'sitecrowdfunding');
//	    $sitecrowdfunding_gateway_table_obj=$sitecrowdfunding_gateway_table->fetchRow(array('project_id = ?'=>$project_id, 'plugin = ?'=>"Sitegateway_Plugin_Gateway_MangoPay"));
//
//	    if(!empty($sitecrowdfunding_gateway_table_obj))
//		$gateway_id=$sitecrowdfunding_gateway_table_obj->projectgateway_id;
//	    else
//		$gateway_id=0;
//
//	    $db=Engine_Db_Table::getDefaultAdapter();
//	    $db->beginTransaction();
//	    $gatewayEnabled=true;
//	    // Process
//	    try {
//		//GET VIEWER ID
//		$viewer=Engine_Api::_()->user()->getViewer();
//		$viewer_id=$viewer->getIdentity();
//		$email=$viewer->email;
//		if(empty($gateway_id)) {
//		    $row=$sitecrowdfunding_gateway_table->createRow();
//		    $row->project_id=$project_id;
//		    $row->user_id=$viewer_id;
//		    $row->email=$email;
//		    $row->title="MangoPay";
//		    $row->description='';
//		    $row->plugin="Sitegateway_Plugin_Gateway_MangoPay";
//		    $obj=$row->save();
//		    $gateway=$row;
//		}else {
//		    $gateway=Engine_Api::_()->getItem("sitecrowdfunding_projectGateway",
//			    $gateway_id);
//		    $gateway->email=$email;
//		    $gateway->save();
//		}
//		$db->commit();
//	    }catch(Exception $ex) {
//		die("data:".$ex);
//		$this->respondWithValidationError('internal_server_error',
//		    $ex->getMessage());
//	    }
//	    // Validate gateway config
//	    $gatewayObject=$gateway->getGateway();
//	    try {
//		$gatewayObject->setConfig($gatewayDetails);
//		$response=$gatewayObject->test();
//	    }catch(Exception $ex) {
//		echo $ex;die;
//		$gatewayEnabled=false;
//		$this->respondWithValidationError('internal_server_error',
//		    $ex->getMessage());
//	    }
//
//// Process
//	    $message_additional_gateway=null;
//	    try {
//		$values=$gateway->getPlugin()->processAdminGatewayForm($gatewayDetails);
//	    }catch(Exception $ex) {
//		$this->respondWithValidationError('internal_server_error',
//		    $ex->getMessage());
//		$values=null;
//	    }
//
//	    $formValuesValidation=true;
//	    foreach($gatewayDetails as $k=> $gatewayParam) {
//		if($k!='test_mode'&&empty($gatewayParam)) {
//		    $formValuesValidation=false;
//		    break;
//		}
//	    }
//
//	    if($formValuesValidation) {
//		$gateway->setFromArray(array(
//		    'enabled'=>$gatewayEnabled,
//		    'config'=>$gatewayDetails,
//		));
//		$gateway->save();
//		$projectGateway['mangopay']=$gateway->projectgateway_id;
//	    }
//	}
//    }
        // INSERT ALL ENABLED GATEWAY ENTRY IN PROJECT TABLE
        $projectOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding')->getOtherinfo($project->project_id);
        $projectOtherInfo->project_gateway = Zend_Json_Encoder::encode($projectGateway);
        $projectOtherInfo->save();
        $this->successResponseNoContent('no_content', true);
    }

    // End of payment method configration...............................

    private function _getProject($params, $customFieldValues, $isManage = 0) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!empty($viewer)) {
            $viewer_id = $viewer->getIdentity();
        } else {
            $viewer_id = 0;
        }
        
        $projectTable = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
        $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
        
        if(in_array($params['orderby'],array("backed","liked","favourite","launched","successful","failed")))
                {
                    $params['owner_id'] = $viewer_id;
                }
                
          if(!empty( $params['owner_id'])){
                switch ($params['orderby']) {
                    case 'backed':
                        $tempParam['user_id'] = $params['owner_id'];
                        $projects = $backersTable->getBackedProjects($tempParam);
                        foreach ($projects as $project) {
                            $projectIds[] = $project->project_id;
                        }
                        $params['project_ids'] = $projectIds;
                        $params['owner_id'] = null;
                        break;
                    case 'liked':
                        $select = $likesTable->select();
                        $select->where('resource_type = ?', 'sitecrowdfunding_project');
                        $select->where('poster_id = ?', $params['owner_id']);
                        $projects = $select->query()->fetchAll();
                        foreach ($projects as $project) {
                            $projectIds[] = $project["resource_id"];
                        }
                        $params['project_ids'] = $projectIds;
                        $params['owner_id'] = null;
                        break;
                    case 'favourite':
                        $select = $favouritesTable->select();
                        $select->where('poster_id = ?', $params['owner_id'])
                                ->where('poster_type = ?', 'user')
                                ->where('resource_type = ?', 'sitecrowdfunding_project');
                        $projects = $select->query()->fetchAll();
                        foreach ($projects as $project) {
                            $projectIds[] = $project["resource_id"];
                        }
                        $params['project_ids'] = $projectIds;
                        $params['owner_id'] = null;
                        break;
                    case 'launched': $params['selectProjects'] = 'all';
                        break;
                    case 'successful': $params['selectProjects'] = 'successful';
                        break;
                    case 'failed': $params['selectProjects'] = 'failed';
                        break;
                }
              }

        if (isset($params['orderby']) && $params['orderby'] == 'sponsored')
            $params['showProject'] = 'sponsored';
        if (isset($params['orderby']) && $params['orderby'] == 'featured')
            $params['showProject'] = 'featured';
        if (isset($params['orderby']) && $params['orderby'] == 'featuredSponsored')
            $params['showProject'] = 'featuredSponsored';
        $response = array();
        $response['response'] = array();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $response['canCreate'] = $can_create = $allow_upload_project = Engine_Api::_()->authorization()->getPermission($level_id, 'sitecrowdfunding_project', 'create');

        $projectDbTables = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $paginator = $projectDbTables->getProjectPaginator($params, $customFieldValues);


        $paginator->setItemCountPerPage($params['limit']);
        $paginator->setCurrentPageNumber($params['page']);
        foreach ($paginator as $project) {
            $browseProject = array();
            $browseProject = $project->toArray();
            $browseProject['like_count'] = $project->likes()->getLikeCount();
            $browseProject["isLike"] = $browseProject["is_like"] = (bool) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($project);
            $browseProject['owner_title'] = $project->getOwner()->getTitle();
            $backedAmount = $project->getFundedAmount(true);
            $can_delete = $browseProject['can_delete'] = Engine_Api::_()->sitecrowdfunding()->canDeletePrivacy($project->parent_type, $project->parent_id, $project);
            $can_edit = $browseProject['can_edit'] = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($project->parent_type, $project->parent_id, $project);
            if ($project->backer_count > 1)
                $backerTitle = $this->translate("Backers");
            else
                $backerTitle = $this->translate("Backer");
            $fundedAmount = $project->getFundedAmount();
            $browseProject['funded_ratio'] = $fundedRatio = $project->getFundedRatio();
            if((((_CLIENT_TYPE == 'android') && _ANDROID_VERSION >= '3.5') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '2.6.1'))){
                $browseProject['funded_amount'] = $fundedAmount = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($fundedAmount, 1);
                $browseProject['goal_amount'] = $goalAmount = Engine_Api::_()->getApi('Siteapi_Core', 'sitemulticurrency')->getPriceString($project->goal_amount,1);
                $browseProject['backed_amount'] = $fundedAmount;
            }
            else {
                $browseProject['funded_amount'] = $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                $browseProject['goal_amount'] = $goalAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($project->goal_amount);
                $browseProject['backed_amount'] = $fundedAmount . " " . $this->translate("Backed");
            }
            $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $browseProject['currency'] = $currency;
            $days = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_end_date);
            $daysToStart = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_start_date);
            $browseProject['backer_count'] = $project->backer_count . " " . $backerTitle;
            $browseProject['fundedRatio'] = $fundedRatio;
            $browseProject['funded_ratio_title'] = $fundedRatio . "% " . $this->translate("Funded");
            $currentDate = date('Y-m-d');
            $projectStartDate = date('Y-m-d', strtotime($project->start_date));
            if ($project->state == 'successful') {
                $browseProject['state'] = $this->translate("Funding Successful");
            } elseif ($project->state == 'failed') {
                $browseProject['state'] = $this->translate("Funding Failed");
            } elseif ($project->state == 'draft') {
                $browseProject['state'] = $this->translate("In Draft mode");
            } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
                $browseProject['state'] = $daysToStart . " " . $this->translate("Day to Live");
            } elseif ($project->lifetime) {
                $browseProject['state'] = $this->translate('Life Time');
            } elseif ($days >= 1) {
                $browseProject['state'] = $days . " " . $this->translate("Day Left");
            } else {
                $browseProject['state'] = $this->translate($project->getProjectStatus());
            }
            $browseProject['isFavourite'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitecrowdfunding')->isFavourite($project->getIdentity(), 'sitecrowdfunding_project', $viewer->getIdentity());
            $owner = Engine_Api::_()->user()->getUser($project->owner_id);
            if (!empty($owner)) {
                $browseProject['owner_title'] = $owner->getTitle();
            }
            $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $project->category_id);
            if (!empty($category)) {
                $browseProject['category_name'] = $category->category_name;
            }

            $browseProject['show_location'] = 0;
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $projectOption) && $project->location) {
                $browseProject['show_location'] = 1;
            }

            //project image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($project);
            if (!empty($getContentImages))
                $browseProject = array_merge($browseProject, $getContentImages);

            //Project Owner image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($project, true);
            if (!empty($getContentImages))
                $browseProject = array_merge($browseProject, $getContentImages);

            $menus = array();
            if (!empty($isManage)) {
                if ($can_edit) {
                    $menus[] = array(
                        'label' => $this->translate('Edit Project'),
                        'name' => 'edit',
                        'url' => 'crowdfunding/edit/' . $project->getIdentity(),
                        "actionType" => "edit",
                        "dialogueTitle" => $this->translate("Edit Project"),
                        "successMessage" => $this->translate("Project Edited successfuly.")
                    );
                }
                if ($can_delete) {
                    $menus[] = array(
                        'label' => $this->translate('Delete Project'),
                        'name' => 'delete',
                        'url' => 'crowdfunding/delete/' . $project->getIdentity(),
                        "actionType" => "alertDialog",
                        "dialogueMessage" => $this->translate("Do you want to delete this project?"),
                        "dialogueTitle" => $this->translate("Delete Project"),
                        "dialogueButton" => $this->translate("Delete"),
                        "successMessage" => $this->translate("Project Deleted successfuly."),
                    );
                }
                
                if($params['orderby'] == 'backed'){
                $menus[] = array(
                        'label' => $this->translate('Backing Details'),
                        'name' => 'info',
                        'url' => 'crowdfunding/backer/view-backed-details/' . $project->getIdentity(),
                        "actionType" => "info",
                    );
                }

                $browseProject['menu'] = $menus;
            }

            $response['response'][] = $browseProject;
        }
        $response['totalItemCount'] = $paginator->getTotalItemCount();
        return $response;
    }

    /*
     * Project Owner FAQ
     * Return Json
     */

    public function projectOwnerFaqAction() {
        $response = array();
        $coreSetting = Engine_Api::_()->getApi('settings', 'core');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.projectownerfaq.enabled', 1)) {
            $this->respondWithError('unauthorized');
        }
        $response[] = array(
            "question" => "1. What are the things I should do before starting my project? \n",
            "answer" => "You should follow below steps before starting your project:\n a) Make a detailed budget of your costs required to bring your idea to life. Use this to set your project’s funding goal.\nb) Think thoroughly about what rewards to offer to your project’s backers.\nc) Have a plan to market your project so that it can reach to maximum people.\nd) Be active before launching your project till it reaches it’s funding goal amount.\n"
        );
        $response[] = array(
            "question" => "2. What are the various steps to create a project on this website?\n",
            "answer" => "a) Click on ‘Create a Project’ button or link.\nb) Select the package with the feature set you need for your project.\nc) Fill the required details in the project creation form which you have gathered before starting this process.\nd) Go to dashboard of your project to compile overview, to configure payment gateways, to create various rewards, to add videos / photos, to set main photo / video for your project etc."
        );
        $response[] = array(
            "question" => "3. What should I consider while setting up funding goal for my project?\n",
            "answer" => "Your funding goal should be the minimum amount of funds you need to complete your project along with make and ship of rewards.\nMake a list of all the materials, resources, and expenses you'll need to complete your project, and the estimated costs for each.\nShare a breakdown of this budget in your project description to show backers you've thought things through."
        );
        $response[] = array(
            "question" => "4. What are the different payment gateways which I can enable for my project’s backers?\n",
            "answer" => "You can configure below payment gateways if they are enabled by the site admin:\na) Stripe\nb) PayPal\nc) PayPal Adaptive\nd) Mangopay"
        );
        $response[] = array(
            "question" => "5. What information should I share about my project on its profile page?\n",
            "answer" => "After visiting your project page backers should have a clear sense of:\na) What is your project as all about.\nb) How you will bring your project to life.\nc) How the funds collected will be used. \nd) The identities of the people on your team (if you have one).\n\nAlso, your project page should tell your story and include an eye-catching project image or video, and some attractive rewards.
The more information you share, the more you will earn your backers’ trust."
        );
        $response[] = array(
            "question" => "6. How do I include images or other media in my project overview?\n",
            "answer" => "You can include photos, links, videos etc. in your project overview via TinyMCE editor. TinyMCE offers HTML formatting tools, like bold, italic, underline, ordered and unordered lists, different types of alignments, in-line placement of images and videos, etc. It allows users to edit HTML documents online. The different options can be configured at the time of integration with a project, which improves the flexibility of a project."
        );
        $response[] = array(
            "question" => "7. What are image specifications for project pages?\n",
            "answer" => "Your project image size should be 680x1400 pixels. Recommended file types are: JPG, JPEG, PNG, or GIF."
        );
        $response[] = array(
            "question" => "8. What does estimated delivery date mean?\n",
            "answer" => "The estimated delivery date for a reward is the date you expect to deliver that reward to backers. If you're offering more than one thing in a single reward tier, set your estimated delivery date to when you expect everything in the reward tier to be delivered. 
If you're not sure what the estimated delivery date is for a reward, take some time out to create a timeline for your project so that you have a good sense of when you'll complete it. Choose a delivery date that you feel confident about and will be working towards."
        );
        $response[] = array(
            "question" => "9. What can be offered as a reward?\n",
            "answer" => "Rewards are generally items produced by the project itself — a copy of the album, a print from the show, a limited edition of the comic, naming characters after backers, personal phone calls etc."
        );
        $response[] = array(
            "question" => "10. Is there a way to limit the quantity of a reward?\n",
            "answer" => "Yes, there is a way to limit the quantity of a reward. You can do so while creating the reward, select the ‘Limit Quantity’ checkbox and enter the limit for backers who can choose this reward while backing your project."
        );

        $response[] = array(
            "question" => "11. How do I charge shipping on my rewards?",
            "answer" => "You can charge shipping cost for rewards selected by backers of certain places. To do so choose the location where you want to ship your reward and add the shipping charges in the textbox appearing along with it. This shipping cost will be added to the amount set for that reward when a backer selects the reward to fund your project."
        );
        $response[] = array(
            "question" => "12. I am unable to edit my Project. What might be the reason behind it?\n",
            "answer" => "You are unable to edit your project because of possible below reasons:\n1. Published Project with at least 1 Backer: When a project is in draft mode, it is not finalized so all the details related to that project are editable. But, once a project is published and is backed by at least one backer then few fields are non editable like: Project Duration and Funding Amount.\nIf Project Owner still wants to edit the published project then he can contact to the site admin. Site admin can take proper action in such scenario and do the needful changes."
        );
        $response[] = array(
            "question" => "13. I am unable to delete my Project. What might be the reason behind it?\n",
            "answer" => "You are unable to delete your project because of possible below reasons:\n1. Project Backer’s: When the project is funded even by a single backer.\n2. Member Level Settings: ‘Allow Deletion of Projects?’ is disabled for the member belonging to the particular member level.\nIf Project Owner still wants to delete the project then he can contact to the site admin. Site admin can take proper action in such scenario like: to refund the backed amount to the respective backer and delete the project.\n[Note: In case, no one has backed the project or the project is in draft mode, then Project Owner can delete that project.]"
        );
        $response[] = array(
            "question" => "14. Is it possible for a project to be funded more than the set goal amount?\n",
            "answer" => "Yes, it is possible for a project to be funded more than the set goal amount or more than 100%."
        );
        $response[] = array(
            "question" => "15. Is it possible to run a Project without creating any rewards in it? I am unable to find the link to create rewards in my Project, from where I can do the same?\n",
            "answer" => "Yes, it is possible to run a Project without creating any rewards in it. There is always one option to back the project with any desired amount and that is without selecting any rewards.\nTo create rewards in a project, follow below steps:\n1. Open the profile page of the project.\n2. Now, go to the dashboard of this project.\n3. Click on “Rewards” from the options displaying on left side of the dashboard page.\n4. Create / edit / delete various rewards from here."
        );
        $response[] = array(
            "question" => "16. I am unable to edit / delete my reward of my Project. What might be the reason behind it?\n",
            "answer" => "You are unable to edit / delete reward of your project because of possible below reasons: \n1. Reward Selected: Once a reward is selected by even a single backer then few fields become non editable like: Backed Amount, Estimated Delivery, Shipping Details and Reward Quantity.\n2. Project Completed: Once a project has reached its goal in defined set of time, rewards of these projects cannot be edited or deleted whether it has been selected any backer or not.\n[Note: If Project Owner still wants to edit / delete the selected reward then he can contact to the site admin. Site admin can do the needful changes.]"
        );
        $response[] = array(
            "question" => "17. If I choose a subcategory, will my project also show up in the main category?\n",
            "answer" => "Yes. For example, if you have started an art based project and you put it in the Art subcategory i.e. Design, your project will appear in the both Art and Design category / sub-category."
        );
        $response[] = array(
            "question" => "18. Can I run more than one project at once?\n",
            "answer" => "Yes, you can run more than one project at once. But, we recommend you to focus on one project at a time as it requires lots of your effort, time and patience."
        );
        $response[] = array(
            "question" => "19. Will my project go live automatically once it's approved?\n",
            "answer" => "Yes, your project will be live automatically once it is approved by site admin."
        );
        $response[] = array(
            "question" => "20. When and how should I start planning my promotion strategy?",
            "answer" => "You should start planning as soon as you decide you want to run a project. Start by thinking through who your existing fans and contacts are and organizing their information into an actionable contact list.\nChoose different social media’s and ways to promote your project. This way it will reach out to maximum people. You can also ask your friends, family members, team members etc. to spread the word about your project."
        );
        $response[] = array(
            "question" => "21. My funding has stalled after a few days, what should I do?",
            "answer" => "a) You can change your promotion strategy.\nb) Ask friends and family to share your project with their networks. Getting your project beyond your immediate supporters can only help. \nc) Share your project via blogs, newsletters etc.\n"
        );
        $response[] = array(
            "question" => "22. Where can I find my project ?\n",
            "answer" => "You can find your created projects, backed / liked / favorited projects at one place, i.e. at ‘My Projects’ page."
        );
        $response[] = array(
            "question" => "23. Where can I track my project’s progress?",
            "answer" => "You can track your project’s progress from ‘My Projects’ page or from the project’s profile page."
        );
        $response[] = array(
            "question" => "24. What is my responsibility for answering questions from backers and non-backers?\n",
            "answer" => "Backers: You can contact your backers from ‘Backers Report’ section of the dashboard of your project. You can compose message for specific backer or all backers at once.\nNon-backers: The members who are interested in your project and wants to contact you before backing your project, can do so via ‘Contact me’ button placed on the project profile page."
        );
        $response[] = array(
            "question" => "25. Can I run my project again if funding is unsuccessful?\n",
            "answer" => "Yes, of course! You can always try again and relaunch with a new goal, whenever you're ready. \nBefore relaunching, we recommend taking some time to review your project to see what might be improved the next time around. "
        );
        $response[] = array(
            "question" => "26. What do I do if I miss my Estimated Delivery Date?\n",
            "answer" => "The Estimated Delivery Date is intended to set expectations for backers on when they will receive rewards. Setbacks are possible with any project — creative ones especially. \nWhen the unforeseen occurs, creators are expected to post a project update explaining the situation. Sharing the story, speed bumps and all.\nCreators who are honest and transparent will find backers to be far more forgiving. We’ve all felt the urge to avoid things when we feel bad about them, but leaving backers in the dark makes them assume the worst. It not only reflects badly on the project, it’s disrespectful to the support that community has given. Regular communication is a must."
        );
        $response[] = array(
            "question" => "27. What should I consider when I'm planning to relaunch my project?\n",
            "answer" => "Each launched project is a learning experience, so if you're planning to re-launch a project that wasn't successful in reaching it's goal, just make sure you've taken stock of what worked and what didn't. Here are some common points that creators usually reexamine:\na) Your project's goal and budget. \nb) Your supporters. Did you let your supporters know about your project? \nc) Your promotion plan. "
        );
        $response[] = array(
            "question" => "28. How do I communicate with backers?\n",
            "answer" => "To communicate with your backers, you can post announcements in your project’s profile page. You can also start a discussion if want any opinion of your backers.\n"
        );
        $response[] = array(
            "question" => "29. What information can I see about my backers?\n",
            "answer" => "You can see backers name, amount funded, payment method and mode used while backing your project."
        );
        $response[] = array(
            "question" => "30. How can I use the backer export?",
            "answer" => "The backer export (available from your Backer Report) lets you export all your backer data into a spreadsheet where you can organize and sort the information to meet 
almost any need. "
        );
        $body = array();
        $body['response'] = $response;
        $filter[] = array(
            'name' => 'owner_faq',
            'label' => $this->translate('Owner FAQ'),
            'url' => "crowdfunding/project-owner-faq",
        );
        $filter[] = array(
            'name' => 'backer_faq',
            'label' => $this->translate('Backer FAQ'),
            'url' => "crowdfunding/backer-faq",
        );

        $body['filter'] = $filter;

        $body['title'] = $coreSetting->getSetting('sitecrowdfunding.projectownerfaq.title', 'FAQs for Project Owner');
        $this->respondWithSuccess($body, true);
    }

    /*
     * Backer FAQ
     * Return Json
     */

    public function backerFaqAction() {
        $coreSetting = Engine_Api::_()->getApi('settings', 'core');
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.backersfaq.title', 1)) {
            $this->respondWithError('unauthorized');
        }
        $response = array();
        $response[] = array(
            "question" => "1. How can I found interesting projects as per my preference? \n",
            "answer" => "To find interesting projects as per your preference follow below steps:\na) Go to ‘Browse Projects’ page.\nb) Enter the criteria in the search form as per your preference.\nc) You can go through the projects as per the searched criteria."
        );
        $response[] = array(
            "question" => "2. How can I back a project? \n",
            "answer" => "You can back a project in two ways:\na)  Back Button: Click on back button and you will be re-directed to the page with all the list of rewards and an option to back any amount to the project.\nb)Reward Selection: choose the reward listed on the project profile page and you will be redirected to the page where that reward is pre-selected.\n\nNext step is to fill your delivery address and pay for the back amount using available payment options."
        );

        $response[] = array(
            "question" => "3. Can I back a project more than once? \n",
            "answer" => "Yes, you can back a project more than once."
        );
        $response[] = array(
            "question" => "4. How can I contact Project Owner for any queries related to his project? \n",
            "answer" => "Amount backed can be refunded back or not is entirely dependent on the project owner and site admin. So, in case of any refund, please contact project owner or site admin."
        );
        $response[] = array(
            "question" => "5. Is it possible to get refund of the amount I have backed for a project?\n",
            "answer" => "Amount backed can be refunded back or not is entirely dependent on the project owner and site admin. So, in case of any refund, please contact project owner or site admin."
        );
        $response[] = array(
            "question" => "6. Do I get notified if a project I have backed succeeds or not?\n",
            "answer" => "Yes, you will be notified about the success and failure of the project which you have backed."
        );
        $response[] = array(
            "question" => "7. Is my pledge amount publicly displayed?\n",
            "answer" => "This depends entirely on you. You can make your contribution anonymous while backing the project."
        );
        $response[] = array(
            "question" => "8.How can I know in detail about the project owner?\n",
            "answer" => "You can see the full biography of the project owner by clicking on the ‘Full Bio’ button present on the project profile page. Here, you can also the link of other social media profile of the project owner like: Facebook, Twitter, LinkedIn, Google Plus etc."
        );

        $response[] = array(
            "question" => "9. Where can I keep track of my backed details related to various projects? \n",
            "answer" => "You can keep track of your backed details related to various projects from ‘My Projects’ section. You can also print invoice of the backing details from here.
"
        );

        $response[] = array(
            "question" => "10. Will I receive the invoice for my backed amount? \n",
            "answer" => "Yes, you will receive the invoice for you backed amount on your registered email address. You can also print invoice of the backing details from ‘My Projects’ section."
        );
        $response[] = array(
            "question" => "11. How do I know when rewards for a project will be delivered? \n",
            "answer" => "Projects have an Estimated Delivery Date under each reward on the project page. You can view the Estimated Delivery Date either on the project profile page. This date is entered by project owners as their best guess for delivery to backers."
        );

        $response[] = array(
            "question" => "12. I haven't gotten my reward yet. What do I do? \n",
            "answer" => "The first step is checking the Estimated Delivery Date on the project page. Backing a project is a lot different than simply ordering a product online, and sometimes projects are in very early stages when they are funded.\nIf the Estimated Delivery Date has passed, check for project updates that may explain what happened. Sometimes project owners hit unexpected roadblocks, or simply underestimate how much work it takes to complete a project. PRoject owners are expected to communicate these setbacks when they happen.\nIf the project owner hasn’t posted any update, send them a direct message to request more information about their progress, or post a public comment on their project asking for a status update."
        );

        $body = array();
        $body['response'] = $response;

        $body['title'] = $coreSetting->getSetting('sitecrowdfunding.backersfaq.title', 'FAQs for Backers');
        $this->respondWithSuccess($body, true);
    }

    /*
     * Project Owner BIO 
     * Return Json
     */
    
    public function userFullBioAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->respondWithError("no_record");
        }
        $project = Engine_Api::_()->core()->getSubject();
        $owner = $project->getOwner();
        $owner_id = $owner->user_id;

        $userinfoTable = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $userinfoTableName = $userinfoTable->info('name');
        $select = $userinfoTable->select()->from($userinfoTableName, '*')
                        ->where('user_id = ?', $owner_id)->limit(1);
        $ownerBio = $select->query()->fetch();
        $response = array();
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($owner);
        $response['response'] = $ownerBio;
        $response['response'] = array_merge($response['response'], $getContentImages);
        $tableProject = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $params = array();
        $params['users'] = array($owner_id);
        $projects = $tableProject->getProjectPaginator($params);

        $response['totalItemCount'] = $projects->getTotalItemCount();
        foreach ($projects as $project) {
            $BrowseProject = array();
            $BrowseProject['title'] = $project->getTitle();
            $BrowseProject['type'] = $project->getType();
            $BrowseProject['project_id'] = $project->getIdentity();
            $response['projects'][] = $BrowseProject;
        }
        $this->respondWithSuccess($response, true);
    }

    private function _gutterMenus($project) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $owner = $project->getOwner();
        $owner_id = $owner->getIdentity();

        if (!empty($viewer_id) && Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($project->parent_type, $project->parent_id, $project)) {
            $menus[] = array(
                'name' => 'edit',
                'label' => $this->translate('Edit Project'),
                'url' => 'crowdfunding/edit/' . $project->getIdentity(),
                "actionType" => "edit",
                "dialogueTitle" => $this->translate("Edit Project"),
                "successMessage" => $this->translate("Project Edited successfuly.")
            );
            $menus[] = array(
                'name' => 'delete',
                'label' => $this->translate('Delete Project'),
                'url' => 'crowdfunding/delete/' . $project->getIdentity(),
                "actionType" => "alertDialog",
                "dialogueMessage" => $this->translate("Do you want to delete this project?"),
                "dialogueTitle" => $this->translate("Delete Project"),
                "dialogueButton" => $this->translate("Delete"),
                "successMessage" => $this->translate("Project Deleted successfuly."),
            );
        }

        if (!$project->isExpired() && $project->status == 'active' &&  !empty($project->is_gateway_configured)) {
            $menus[] = array(
                'name' => 'back_project',
                'label' => $this->translate('Back This Project'),
                'url' => 'crowdfunding/back-project/' . $project->getIdentity(),
            );
        }




        if ($viewer_id) {
            $menus[] = array(
                'label' => $this->translate('Suggest to friends'),
                'name' => 'suggest',
                "actionType" => "suggest_to_friend",
                "dialogueTitle" => $this->translate("Suggest to friends"),
                "successMessage" => $this->translate("Your Suggestion has been sent."),
                'url' => 'suggestions/suggest-to-friend',
                'urlParams' => array(
                    "entity" => $project->getType(),
                    "entity_id" => $project->getIdentity()
                )
            );

            $menus[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                "actionType" => "share",
                "dialogueTitle" => $this->translate("Share"),
                "successMessage" => $this->translate("Your entity has been share successfuly."),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $project->getType(),
                    "id" => $project->getIdentity()
                )
            );
        }

        if ($this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            $menus[] = array(
                'name' => 'dashboard',
                'label' => $this->translate('Dashboard'),
                "subMenu" => $this->subMenu($project)
            );
        }

        return $menus;
    }

    private function _tabsMenus($project) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $owner = $project->getOwner();
        $owner_id = $owner->getIdentity();
        $can_edit = $project->authorization()->isAllowed($viewer, "edit");
        $tabsMenu[] = array(
            'name' => 'update',
            'label' => $this->translate('Updates'),
        );

        $tabsMenu[] = array(
            'name' => 'information',
            'label' => $this->translate('Info'),
            'url' => 'crowdfunding/profiletab/information/' . $project->getIdentity()
        );

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $overview = $tableOtherinfo->getColumnValue($project->getIdentity(), 'overview');
        if ($overview) {
            $tabsMenu[] = array(
                'name' => 'overview',
                'label' => $this->translate('Overview'),
                'url' => 'crowdfunding/profiletab/overview/' . $project->getIdentity(),
                "urlParams" => array(
                    "tab_info" => 1,
                )
            );
        }

        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $params = array();
        $params['project_id'] = $project->getIdentity();
        $paginator = $backersTable->getBackersPaginator($params);
        $backersCount = $paginator->getTotalItemCount();
        if ($backersCount > 0) {
            $tabsMenu[] = array(
                'name' => 'project_backer',
                'totalItemCount' => $backersCount,
                'label' => $this->translate('Project Backer'),
                'url' => 'crowdfunding/profiletab/backers/' . $project->getIdentity()
            );
        }

        if (method_exists('Siteapi_Api_Core', 'isSitevideoPluginEnabled')) {
            $subject_type = $project->getType();
            $subject_id = $project->getIdentity();
            $advVideoEnableArray = Engine_Api::_()->getApi('Core', 'siteapi')->isSitevideoPluginEnabled($subject_type, $subject_id);

            if (isset($advVideoEnableArray['sitevideoPluginEnabled']) && !empty($advVideoEnableArray['sitevideoPluginEnabled']) && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '3.0') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION > '2.1.6'))) {
                $count = isset($advVideoEnableArray['totalVideoItemCount']) ? $advVideoEnableArray['totalVideoItemCount'] : 0;
                $tabsMenu[] = array(
                    'name' => 'video',
                    'label' => $this->translate('Videos'),
                    'totalItemCount' => $count,
                    'canUpload'=> empty($can_edit)?false:true,
                    "uploadUrl" => "advancedvideos/create?subject_type=" . $project->getType() . "&subject_id=" . $project->getIdentity() . "&post_attach=1",
                    'url' => 'advancedvideos/index/' . $project->getIdentity(),
                    "urlParams" => array(
                        "subject_type" => $project->getType(),
                        "subject_id" => $project->getIdentity()
                    )
                );
            }
        }

        $album = $project->getSingletonAlbum();
        $paginator = $album->getCollectiblesPaginator();
        $total_images = $paginator->getTotalItemCount();
            $tabsMenu[] = array(
                'name' => 'photo',
                'label' => $this->translate('Photos'),
                'canUpload'=> empty($can_edit)?false:true,
                'totalItemCount' => $total_images,
                'url' => 'albums/view-content-album',
                "uploadUrl"=>'crowdfunding/profiletab/upload-photo/' . $project->getIdentity(),
                "urlParams" => array(
                    "subject_type" => $project->getType(),
                    "subject_id" => $project->getIdentity()
                )
            );
        return $tabsMenu;
    }

    public function stripUrl($project) {
        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            if (isset($_SESSION['stripe_connect_oauth_process'])) {
                $session = new Zend_Session_Namespace('stripe_connect_oauth_process');
                $session->unsetAll();
            }

            $session = new Zend_Session_Namespace('stripe_connect_oauth_process');
            $session->resource_type = $project->getType();
            $session->resource_id = $project->getIdentity();

            $client_id = Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'key' => 'client_id', 'productType' => 'payment_package'));

            $redirectURL = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitegateway', 'controller' => 'payment', 'action' => 'stripe-connect'), 'default', true);
            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
            $redirectURL = $redirectURL . "?token=" . $getOauthToken['token'];
            Engine_Api::_()->sitegateway()->getKey(array('gateway' => 'stripe', 'key' => 'client_id', 'productType' => 'payment_package'));
            $stripurl = " https://connect.stripe.com/oauth/authorize?response_type=code&client_id=" . $client_id . "&scope=read_write" . '&redirect_uri=' . $redirectURL;

            return $stripurl;
        } catch (Exception $ex) {
            
        }
    }

    public function checkEnableGetway($geteway = null, $enableGatway = array()) {
        if (!$geteway)
            return 0;

        if (in_array($geteway, $enableGatway))
            return 1;
        else {
            return 0;
        }
    }

    public function subMenu($project = null) {
        $submenu = array();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if ($this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {

            $submenu[] = array(
                'name' => 'overview',
                'label' => $this->translate('Overview'),
                'url' => 'crowdfunding/profiletab/overview/' . $project->getIdentity(),
                "actionType" => "edit",
                "dialogueTitle" => $this->translate("Overview"),
                "successMessage" => $this->translate("Overview edited successfuly."),
            );

            $submenu[] = array(
                'name' => 'inforamtion',
                'label' => $this->translate('About You'),
                'url' => 'crowdfunding/profiletab/about-you/' . $project->getIdentity(),
                "actionType" => "edit",
                "dialogueTitle" => $this->translate("About You"),
                "successMessage" => $this->translate("Information edited successfuly."),
            );

            $submenu[] = array(
                'name' => 'manage_leader',
                'label' => $this->translate('Manage Admins'),
                'url' => 'crowdfunding/leader/manage-leaders/' . $project->getIdentity(),
            );

            $paginator = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageResult($project);
            $totalPackageCount = $paginator->getTotalItemCount();
            if ($totalPackageCount > 0 && $this->_hasPackageEnable) {
                $submenu[] = array(
                    'name' => 'upgrade_package',
                    'label' => $this->translate('Upgrade Package'),
                    'url' => 'crowdfunding/upgrade-package/',
                    "actionType" => "upgrade_package",
                    "dialogueTitle" => $this->translate("Upgrade Package"),
                    "urlParams" => array(
                        "project_id" => $project->getIdentity()
                    )
                );
            }

            $submenu[] = array(
                'name' => 'Reward',
                'label' => $this->translate('Rewards'),
                'url' => 'crowdfunding/reward/manage/' . $project->getIdentity(),
            );

            $submenu[] = array(
                'name' => 'payment_method',
                'label' => $this->translate('Payment Method'),
                'url' => 'crowdfunding/payment-info/' . $project->getIdentity(),
                "actionType" => "edit",
                'module' => "payment_method_config",
                "dialogueTitle" => $this->translate("Payment Method"),
                "successMessage" => $this->translate("Payment method configured successfuly."),
            );
//            $can_edit = $project->authorization()->isAllowed($viewer, "edit");
//            if ($can_edit) {
//                $submenu[] = array(
//                    'name' => 'photo_upload',
//                    'label' => $this->translate('Upload Photo'),
//                    'url' => 'crowdfunding/profiletab/upload-photo/' . $project->getIdentity(),
//                );
//            }

            $sitegatewayApi = Engine_Api::_()->sitegateway();
            $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
            $project_gateway_obj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $project->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
            $mode = 'live';
            if ($adminGateway->config['test_mode']) {
                $mode = 'sandbox';
            }
            $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
            $slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.slugplural', 'projects');
            $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
            $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
            $baseUrl = @trim($baseUrl, "/");

            if (isset($project_gateway_obj->config[$mode]['mangopay_user_id']) && !empty($project_gateway_obj->config[$mode]['mangopay_user_id'])) {
                $submenu[] = array(
                    'name' => 'kyc_upload',
                    'label' => $this->translate('KYC'),
                    'url' => $getHost . '/' . $baseUrl . "/" . $slug_plural . '/upload-kyc/' . $project->getIdentity() . "?token=" . $getOauthToken['token'] . "&disableHeaderAndFooter=1",
                    "actionType" => "web",
                    "dialogueTitle" => $this->translate("KYC"),
                );
            }
            $submenu[] = array(
                'name' => 'backer_report',
                'label' => $this->translate('Backers Report'),
                'url' => $getHost . '/' . $baseUrl . "/" . $slug_plural . '/backer/backers-report/project_id/' . $project->getIdentity() . "?token=" . $getOauthToken['token'] . "&disableHeaderAndFooter=1",
                "actionType" => "web",
                "dialogueTitle" => $this->translate("Backers Report"),
            );
            $submenu[] = array(
                'name' => 'transactions',
                'label' => $this->translate('Transactions'),
                'url' => $getHost . '/' . $baseUrl . "/" . $slug_plural . '/dashboard/project-transactions/' . $project->getIdentity() . "?token=" . $getOauthToken['token'] . "&disableHeaderAndFooter=1",
                "actionType" => "web",
                "dialogueTitle" => $this->translate("Transactions"),
            );
        }

        return $submenu;
    }

    private function _getOwnerBiography($project) {
        $owner = $project->getOwner();
        $owner_id = $owner->user_id;

        $userinfoTable = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
        $userinfoTableName = $userinfoTable->info('name');
        $select = $userinfoTable->select()->from($userinfoTableName, '*')
                        ->where('user_id = ?', $owner_id)->limit(1);
        $ownerBio = $select->query()->fetch();
        $response = array();
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($owner);
        $response['projectOwnerInfo'] = $ownerBio;
        $response['projectOwnerInfo']['owner_title'] = $owner->getTitle();
        $response['projectOwnerInfo']['user_id'] = $owner->getIdentity();

        $response['projectOwnerInfo'] = array_merge($response['projectOwnerInfo'], $getContentImages);
        $tableProject = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
        $params = array();
        $params['users'] = array($owner_id);
        $projects = $tableProject->getProjectPaginator($params);

        $response['totalItemCount'] = $projects->getTotalItemCount();
        foreach ($projects as $project) {
            $BrowseProject = array();
            $BrowseProject['title'] = $project->getTitle();
            $BrowseProject['type'] = $project->getType();
            $BrowseProject['project_id'] = $project->getIdentity();
            $response['projects'][] = $BrowseProject;
        }
        return $response;
    }

}

?>
