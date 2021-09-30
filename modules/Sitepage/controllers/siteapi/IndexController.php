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
class Sitepage_IndexController extends Siteapi_Controller_Action_Standard {

    /**
     * Auth checkup and creating the subject.
     * 
     */
    public function init() {
       if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'view')) {

            $this->respondWithError('unauthorized');
        }

        if ($this->getRequestParam("page_id") && (0 !== ($page_id = (int) $this->getRequestParam("page_id")) && null !== ($page = Engine_Api::_()->getItem('sitepage_page', $page_id)))) {
            Engine_Api::_()->core()->setSubject($page);
        }
    }

    /**
     * Returns the Directory page listings matching the get parameters with pagination.
     * 
     * @return pagination of Directory Page listings
     */
    public function browseAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();

        $getRequest = $this->_getAllParams();

        if (isset($getRequest['type']) && $getRequest['type'] == 'userPages') {
            if (!$getRequest['user_id'])
                $this->respondWithValidationError('not_approved', 'for user Pages please send user_id');
            else {
                $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($getRequest['user_id']);
                //GET STUFF
                $manageadmin_ids = array();
                foreach ($adminpages as $adminpage) {
                    $manageadmin_ids[] = $adminpage->page_id;
                }
                $getRequest['adminpages'] = $manageadmin_ids;
                $getRequest['type'] = "manage";
                $getRequest['type_location'] = 'manage';
            }
        }
        $getRequest['type'] = "browse";
            

        //TO GET OR NOT THE EXACT LOCATION OF PAGE
        //ignore if profile page view...................
         $enabledModules = (_CLIENT_TYPE == 'android') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroid.maplocation', 0) : Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.maplocation', '1');
        $enabledModules = @unserialize($enabledModules);
       $enabledModules = (_CLIENT_TYPE == 'android') ? $enabledModules['siteandroid_maplocation']:$enabledModules['siteiosapp_maplocation'] ;
        $enableLocationBrowse=0;
        if(in_array('sitepage', $enabledModules)){
          $enableLocationBrowse=1;
        }
        
        //ignore if profile page view...................
        if (empty($enableLocationBrowse) || $getRequest['user_id'] || (isset($getRequest['category_id']) && !empty($getRequest['category_id'])))
            unset($getRequest['restapilocation']);

        if (isset($getRequest['restapilocation']) && !empty($getRequest['restapilocation'])) {
            $getRequest['sitepage_location'] = $this->getRequestParam('restapilocation', null);
            $getRequest['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 0);
        }

        if (isset($_GET['sitepage_location']) && !empty($_GET['sitepage_location'])) {
            $getRequest['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 0);
            $getRequest['sitepage_location'] = $_GET['sitepage_location'];
        }

        $getRequest['page'] = (isset($getRequest['page'])) ? $getRequest['page'] : 1;
        $getRequest['limit'] = !isset($getRequest['limit']) ? $getRequest['limit'] : 20;

        try {
            $response = $this->_getDirectoryPages($getRequest);

            if ($getRequest['showCategory'])
                $response = array_merge($response, $this->categoryAction());

            if ($getRequest['showOrderBy']) {
                $sitepageofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
                $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
                $searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitepage');

                if (isset($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']['display']) && isset($sitepagereviewEnabled) && !empty($sitepagereviewEnabled)) {
                    $searchForm['orderby'] = array(
                        array('name' => 'creation_date', 'title' => $this->translate('Most Recent')),
                        array('name' => 'view_count', 'title' => $this->translate('Most Viewed')),
                        array('name' => 'comment_count', 'title' => $this->translate('Most Commented')),
                        array('name' => 'like_count', 'title' => $this->translate('Most Liked')),
                        array('name' => 'title', 'title' => $this->translate('Alphabetical')),
                        array('name' => 'review_count', 'title' => $this->translate('Most Reviewed')),
                        array('name' => 'rating', 'title' => $this->translate('Most Rated')),
                    );
                } elseif (isset($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']) && isset($searchFormSettings['orderby']['display']) && !empty($searchFormSettings['orderby']['display'])) {
                    $searchForm['orderby'] = array(
                        array('name' => 'creation_date', 'title' => $this->translate('Most Recent')),
                        array('name' => 'view_count', 'title' => $this->translate('Most Viewed')),
                        array('name' => 'comment_count', 'title' => $this->translate('Most Commented')),
                        array('name' => 'like_count', 'title' => $this->translate('Most Liked')),
                        array('name' => 'title', 'title' => $this->translate('Alphabetical')),
                    );
                }

                $response = array_merge($response, $searchForm);
            }
            $response['packagesEnabled'] = 0;
            if (!empty($viewer) && $viewer->getIdentity())
                $response['packagesEnabled'] = $this->_packagesEnabled();

            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * Returns the Users Directory page listings matching the get parameters with pagination.
     * 
     * @return pagination of Directory Page listings
     */
    public function manageAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity())
            $this->respondWithError('unauthorized');

        $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($viewer->getIdentity());

        // GET STUFF
        $manageadmin_ids = array();
        foreach ($adminpages as $adminpage) {
            $manageadmin_ids[] = $adminpage->page_id;
        }
        $getRequest = $this->_getAllParams();
        $getRequest['type'] = 'manage';
        $getRequest['adminpages'] = $manageadmin_ids;
        $getRequest['visible'] = $getRequest['manage'] = 1;
        $getRequest['owner_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        $getRequest['type_location'] = 'manage';
        $getRequest['page'] = (isset($getRequest['page'])) ? $getRequest['page'] : 1;
        $getRequest['limit'] = !isset($getRequest['limit']) ? $getRequest['limit'] : 20;
        try {

            $response = $this->_getDirectoryPages($getRequest);
            $response['packagesEnabled'] = $this->_packagesEnabled();

            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

//    public function packagessAction() {
//        $response = $tempResponse = array();
//        $this->validateRequestMethod();
//
//        $viewer = Engine_Api::_()->user()->getViewer();
//
//        if (!$viewer->getIdentity())
//            $this->respondWithError('unauthorized');
//
//        if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create'))
//            $this->respondWithError('unauthorized');
//
//        $packageTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
//        $packageSql = $packageTable->getPackagesSql();
//        $result = $packageSql->query()->fetchAll();
//
//        // response
//        $response['totalItemCount'] = count($result);
//        if ($result) {
//            foreach ($result as $row => $value) {
//                $package_id = $value['package_id'];
//                $data = $value;
//                $package = Engine_Api::_()->getItem('sitepage_package', $package_id);
//                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($package));
//                $tempResponse[] = $data;
//            }
//            $response['response'] = $tempResponse;
//        }
//
//        $this->respondWithSuccess($response, true);
//    }

    /*
     * Package listing
     *
     */
    public function packagesAction() {
        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        // CHECK FOR PERMISSION OF CREATE EVENT
        if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, "create")->isValid())
            $this->respondWithError('unauthorized');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewer_id = $viewer->getIdentity();

        $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.overview', 0);


        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }


        $bodyParams = $tempArray = array();
        $packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "ticket_type"));

        $packageCount = Engine_Api::_()->getDbTable('packages', 'sitepage')->getEnabledPackageCount();
        if ($packageCount) {
            $packageTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
            $packageSql = $packageTable->getPackagesSql();
            $paginator = Zend_Paginator::factory($packageSql);
            if ($paginator) {
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

                    if (in_array('contactdetails', $packageInfoArray)) {
                        $packageShowArray['contactdetails']['label'] = $this->translate('Contact details');
                        $packageShowArray['contactdetails']['value'] = $this->translate('No');
                        if ($package->contact_details)
                            $packageShowArray['contactdetails']['value'] = $this->translate("Yes");
                    }

                    if (in_array('sendanupdate', $packageInfoArray)) {
                        $packageShowArray['sendanupdate']['label'] = $this->translate('Send an Updates');
                        $packageShowArray['sendanupdate']['value'] = $this->translate('No');
                        if ($package->sendupdate)
                            $packageShowArray['sendanupdate']['value'] = $this->translate("Yes");
                    }

                    if (in_array('apps', $packageInfoArray)) {
                        $modules = unserialize($package->modules);
                        $submodules = $package->getSubModulesString();
                        $packageShowArray['apps']['label'] = $this->translate('Apps available');
                        $packageShowArray['apps']['value'] = $this->translate('No');
                        if (!empty($modules) && !empty($submodules))
                            $packageShowArray['apps']['value'] = $this->translate("Yes");
                    }

                    if (in_array('adds', $packageInfoArray)) {
                        $packageShowArray['adds']['label'] = $this->translate('Advertisement');
                        $packageShowArray['adds']['value'] = $this->translate('No');
                        if ($package->contact_details)
                            $packageShowArray['adds']['value'] = $this->translate("Yes");
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

                    if (in_array('tellafriend', $packageInfoArray)) {
                        $packageShowArray['tellafriend']['label'] = $this->translate('Tell a friend');
                        $packageShowArray['tellafriend']['value'] = $this->translate('No');
                        if ($package->tellafriend)
                            $packageShowArray['tellafriend']['value'] = $this->translate('Yes');
                    }

                    if (in_array('print', $packageInfoArray)) {
                        $packageShowArray['print']['label'] = $this->translate('Print');
                        $packageShowArray['print']['value'] = $this->translate('No');
                        if ($package->print)
                            $packageShowArray['print']['value'] = $this->translate('Yes');
                    }

                    if (in_array('overview', $packageInfoArray)) {
                        $packageShowArray['overview']['label'] = $this->translate('Overview');
                        $packageShowArray['overview']['value'] = $this->translate('No');
                        if ($package->overview)
                            $packageShowArray['overview']['value'] = $this->translate('Yes');
                    }

                    if (in_array('map', $packageInfoArray)) {
                        $packageShowArray['map']['label'] = $this->translate('Map');
                        $packageShowArray['map']['value'] = $this->translate('No');
                        if ($package->overview)
                            $packageShowArray['map']['value'] = $this->translate('Yes');
                    }

                    if (in_array('insights', $packageInfoArray)) {
                        $packageShowArray['insights']['label'] = $this->translate('Insights');
                        $packageShowArray['insights']['value'] = $this->translate('No');
                        if ($package->insights)
                            $packageShowArray['insights']['value'] = $this->translate('Yes');
                    }

                    if (in_array('rich_overview', $packageInfoArray) && ($overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', "overview")))) {
                        if ($package->overview == 1) {
                            $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                            $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                            $packageShowArray['rich_overview']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('videos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', "video"))) {
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

                    if (in_array('photos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', "photo"))) {
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

                    if (in_array('description', $packageInfoArray)) {
                        $packageShowArray['description']['label'] = $this->translate("Description");
                        $packageShowArray['description']['value'] = $this->translate($package->description);
                    }
                    $packageArray["package"] = $packageShowArray;
                    $tempMenu = array();
                    $tempMenu[] = array(
                        'label' => $this->translate('Create Page'),
                        'name' => 'create',
                        'url' => 'sitepages/create',
                        'urlParams' => array(
                            'package_id' => $package->package_id
                        )
                    );
                    $tempMenu[] = array(
                        'label' => $this->translate('Package Info'),
                        'name' => 'package_info',
                        'url' => 'sitepages/packages',
                        'urlParams' => array(
                            'package_id' => $package->package_id
                        )
                    );
                    
                    $packageArray['menu'] = $tempMenu;
                    $bodyParams['response'][] = $packageArray;
                }

                if (isset($bodyParams) && !empty($bodyParams))
                    $this->respondWithSuccess($bodyParams);
            }
        }
        if (empty($bodyParams))
            $bodyParams['getTotalItemCount'] = 0;

        $this->respondWithSuccess($bodyParams);
    }

    public function upgradePackageAction() {
        $page_id = $this->_getParam('page_id', 0);
        $sitepage = $page = Engine_Api::_()->getItem('sitepage_page', $page_id);

        if (empty($sitepage))
            $this->respondWithError('no_record');

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewer_id = $viewer->getIdentity();

        $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.overview', 0);

        if (isset($sitepage->package_id) && !empty($sitepage->package_id))
            $currentPackage = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);


        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $params = $this->_getAllParams();



        if ($this->getRequest()->isGet()) {
            $bodyParams = $tempArray = array();
            $packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "ticket_type"));

            $packageCount = Engine_Api::_()->getDbTable('packages', 'sitepage')->getEnabledPackageCount();
            if ($packageCount) {
                $table = Engine_Api::_()->getItemtable('sitepage_package');
                $packages_select = $table->getPackagesSql($sitepage->getOwner())
                        ->where("update_list = ?", 1)
                        ->where("enabled = ?", 1)
                        ->where("package_id <> ?", $sitepage->package_id);
                $paginator = Zend_Paginator::factory($packages_select);
                if ($paginator) {
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

                        if (in_array('contactdetails', $packageInfoArray)) {
                            $packageShowArray['contactdetails']['label'] = $this->translate('Contact details');
                            $packageShowArray['contactdetails']['value'] = $this->translate('No');
                            if ($package->contact_details)
                                $packageShowArray['contactdetails']['value'] = $this->translate("Yes");
                        }

                        if (in_array('sendanupdate', $packageInfoArray)) {
                            $packageShowArray['sendanupdate']['label'] = $this->translate('Send an Updates');
                            $packageShowArray['sendanupdate']['value'] = $this->translate('No');
                            if ($package->sendupdate)
                                $packageShowArray['sendanupdate']['value'] = $this->translate("Yes");
                        }

                        if (in_array('apps', $packageInfoArray)) {
                            $modules = unserialize($package->modules);
                            $submodules = $package->getSubModulesString();
                            $packageShowArray['apps']['label'] = $this->translate('Apps available');
                            $packageShowArray['apps']['value'] = $this->translate('No');
                            if (!empty($modules) && !empty($submodules))
                                $packageShowArray['apps']['value'] = $this->translate("Yes");
                        }

                        if (in_array('adds', $packageInfoArray)) {
                            $packageShowArray['adds']['label'] = $this->translate('Advertisement');
                            $packageShowArray['adds']['value'] = $this->translate('No');
                            if ($package->contact_details)
                                $packageShowArray['adds']['value'] = $this->translate("Yes");
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

                        if (in_array('tellafriend', $packageInfoArray)) {
                            $packageShowArray['tellafriend']['label'] = $this->translate('Tell a friend');
                            $packageShowArray['tellafriend']['value'] = $this->translate('No');
                            if ($package->tellafriend)
                                $packageShowArray['tellafriend']['value'] = $this->translate('Yes');
                        }

                        if (in_array('print', $packageInfoArray)) {
                            $packageShowArray['print']['label'] = $this->translate('Print');
                            $packageShowArray['print']['value'] = $this->translate('No');
                            if ($package->print)
                                $packageShowArray['print']['value'] = $this->translate('Yes');
                        }

                        if (in_array('overview', $packageInfoArray)) {
                            $packageShowArray['overview']['label'] = $this->translate('Overview');
                            $packageShowArray['overview']['value'] = $this->translate('No');
                            if ($package->overview)
                                $packageShowArray['overview']['value'] = $this->translate('Yes');
                        }

                        if (in_array('map', $packageInfoArray)) {
                            $packageShowArray['map']['label'] = $this->translate('Map');
                            $packageShowArray['map']['value'] = $this->translate('No');
                            if ($package->overview)
                                $packageShowArray['map']['value'] = $this->translate('Yes');
                        }

                        if (in_array('insights', $packageInfoArray)) {
                            $packageShowArray['insights']['label'] = $this->translate('Insights');
                            $packageShowArray['insights']['value'] = $this->translate('No');
                            if ($package->insights)
                                $packageShowArray['insights']['value'] = $this->translate('Yes');
                        }

                        if (in_array('rich_overview', $packageInfoArray) && ($overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', "overview")))) {
                            if ($package->overview == 1) {
                                $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                                $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
                            } else {
                                $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                                $packageShowArray['rich_overview']['value'] = $this->translate('No');
                            }
                        }

                        if (in_array('videos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', "video"))) {
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

                        if (in_array('photos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'sitepage_page', "photo"))) {
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

                        if (in_array('description', $packageInfoArray)) {
                            $packageShowArray['description']['label'] = $this->translate("Description");
                            $packageShowArray['description']['value'] = $this->translate($package->description);
                        }
                        $packageArray['package'] = $packageShowArray;

                        $tempMenu = array();
                        $tempMenu[] = array(
                            'label' => $this->translate('Upgrade Package'),
                            'name' => 'upgrade_package',
                            'url' => 'sitepages/upgrade-package',
                            'urlParams' => array(
                                'package_id' => $package->package_id,
                                'page_id' => $sitepage->page_id
                            )
                        );
                        $tempMenu[] = array(
                            'label' => $this->translate('Package Info'),
                            'name' => 'package_info',
                            'url' => 'sitepages/upgrade-package',
                            'urlParams' => array(
                                'package_id' => $package->package_id,
                                'page_id' => $sitepage->page_id
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
                }
            }
            if (empty($bodyParams))
                $bodyParams['getTotalItemCount'] = 0;

            $this->respondWithSuccess($bodyParams);
        }
        else if ($this->getRequest()->isPost()) {
            if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
                $this->respondWithError('no_record');
            }
            $package_id = $this->_getParam('package_id');
            $package_chnage = Engine_Api::_()->getItem('sitepage_package', $package_id);
            if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($sitepage->getOwner()->level_id, explode(",", $package_chnage->level_id)))) {
                $this->respondWithError('unauthorized');
            }
            $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
            if (!empty($_POST['package_id'])) {
                $table = $sitepage->getTable();
                $db = $table->getAdapter();
                $db->beginTransaction();

                try {
                    $is_upgrade_package = true;

                    //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
                    if ($sitepage->status == 'active')
                        $sitepage->cancel($is_upgrade_package);

                    $sitepage->package_id = $_POST['package_id'];
                    $package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);

                    $sitepage->featured = $package->featured;
                    $sitepage->sponsored = $package->sponsored;
                    $sitepage->pending = 1;
                    $sitepage->expiration_date = new Zend_Db_Expr('NULL');
                    $sitepage->status = 'initial';
                    if (($package->isFree()) && !empty($getPackageAuth)) {
                        $sitepage->approved = $package->approved;
                    } else {
                        $sitepage->approved = 0;
                    }

                    if (!empty($sitepage->approved)) {
                        $sitepage->pending = 0;
                        $expirationDate = $package->getExpirationDate();
                        if (!empty($expirationDate))
                            $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $sitepage->expiration_date = '2250-01-01 00:00:00';

                        if (empty($sitepage->aprrove_date)) {
                            $sitepage->aprrove_date = date('Y-m-d H:i:s');
                            if (!empty($sitepage) && !empty($sitepage->draft) && empty($sitepage->pending)) {
                                Engine_Api::_()->sitepage()->attachPageActivity($sitepage);
                            }
                        }
                    }
                    $sitepage->save();
                    $db->commit();
                    $this->successResponseNoContent('no_content', true);
                } catch (Exception $e) {
                    $db->rollBack();
                }
            }
        }
        /* $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'format' => 'smoothbox',
          'parentRedirect' => $this->view->url(array('action' => 'update-package', 'page_id' => $sitepage->page_id), 'sitepage_packages', true),
          'parentRedirectTime' => 15,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The package for your Page has been successfully changed.'))
          )); */
    }

    /**
     * Returns the create DirectoryPage form or Stores data and creates a Directory Page.
     * 
     */
    public function createAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
        if (!$viewer_id)
            $this->respondWithError('unauthorized');

        if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create'))
            $this->respondWithError('unauthorized');
        $user_level = $viewer->level_id;
        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', "max");

        $currentCount = 10;

        if ($currentCount >= $quota && !empty($quota))
            $this->respondWithError('page_creation_quota_exceed');

        $package_id = $this->_getParam('package_id');

        $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
        $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.level.createhost', 0);
        $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);
        $LevelHost = $this->checkLevelHost($levelHost, 'sitepage');
        $PackagesHost = $this->checkPackageHost($package);
        $sub_status_table = Engine_Api::_()->getDbTable('pagestatistics', 'sitepage');

        if (!$package_id || (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))))
            $package_id = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;

        // Get directory page form 
        if ($this->getRequest()->isGet()) {
            $form_fields = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getForm();
            $this->respondWithSuccess($form_fields);
        }

        // If method not Post or form not valid , Return
        if ($this->getRequest()->isPost()) {

            $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
            $table = Engine_Api::_()->getItemTable('sitepage_page');
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                // Create sitepage
                $values = $this->getAllParams();
                $values['package_id'] = $package_id;
                $values['owner_id'] = $viewer->getIdentity();

                if (isset($values['profileur']) && !empty($values['profileur']))
                    $values['page_url'] = $page_url = $values['profileur'];

                // url validation process
                $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1);
                if ($show_url && !empty($sitepageUrlEnabled)) {
                    $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
                    if (empty($page_url)) {
                        $this->respondWithValidationError('urlNotvalid', array('profileur' => "Url not valid"));
                    }

                    $url_lenght = strlen($page_url);
                    if ($url_lenght < 3) {
                        $this->respondWithValidationError('urlNotvalid', array('profileur' => "Url should be atleast 3 characters long"));
                    } elseif ($url_lenght > 255) {
                        $this->respondWithValidationError('urlNotvalid', array('profileur' => "url should be atmost 255 characters long"));
                    }

                    $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);
                    $check_url = $this->_getParam('check_url');
                    if (!empty($check_url)) {
                        $pageId = $this->_getParam('page_id');
                        $page_id = Engine_Api::_()->sitepage()->getPageId($page_url, $pageId);
                    } else {
                        $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
                    }
                    if (!empty($sitepageUrlEnabled)) {
                        if (!empty($page_id) || (in_array(strtolower($page_url), $urlArray))) {
                            $this->respondWithValidationError('urlNotvalid', array('profileur' => "Url not available"));
                        }
                    } else {
                        if (!empty($page_id)) {
                            $this->respondWithValidationError('urlNotvalid', array('profileur' => "Url not available"));
                        }
                    }

                    if (!preg_match("/^[a-zA-Z0-9-_]+$/", $page_url)) {
                        $this->respondWithValidationError('urlNotvalid', array('profileur' => "URL component can contain alphabets, numbers, underscores & dashes only"));
                    }
                }

                // Start form validation
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitepage')->getFormValidators();
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);

                // if (!isset($values['category_id']) || empty($values['category_id']))
                // $this->respondWithValidationError('parameter_missing', array('category_id' => "Category field cannot be empty"));
                // Response validation error
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                // Custom field work
                $categoryIds = array();
                $categoryIds[] = $values['category_id'];
                $categoryIds[] = $values['subcategory_id'];
                $categoryIds[] = $values['subsubcategory_id'];
                try {
                    $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'sitepage')->getProfileType($categoryIds, 0, 'profile_type');
                } catch (Exception $ex) {
                    $values['profile_type'] = 0;
                }

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {
                    // START FORM VALIDATION
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

                // End form validation

                $values['owner_id'] = $viewer->getIdentity();
                $values['subcategory_id'] = (isset($values['subcategory_id']) && !empty($values['subcategory_id'])) ? $values['subcategory_id'] : 0;
                $values['subsubcategory_id'] = (isset($values['subsubcategory_id']) && !empty($values['subsubcategory_id'])) ? $values['subsubcategory_id'] : 0;
                $sitepage = $table->createRow();
                if (Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        } else if (is_array($values['networks_privacy'])) {
                            $values['networks_privacy'] = (string) join(",", $values['networks_privacy']);
                        }
                    }
                }

                $sitepage->setFromArray($values);
                $package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);
                if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
                    $sitepage->featured = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'featured');
                    $sitepage->sponsored = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'sponsored');
                    $sitepage->approved = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'approved');
                } else {
                    $sitepage->featured = $package->featured;
                    $sitepage->sponsored = $package->sponsored;
                    if ($package->isFree() && !empty($getPackageAuth)) {
                        $sitepage->approved = $package->approved;
                    } else {
                        $sitepage->approved = 0;
                    }
                }


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

                if (!empty($sitepage->approved)) {
                    Engine_Api::_()->sitepage()->sendMail("ACTIVE", $sitepage->page_id);
                } else {
                    Engine_Api::_()->sitepage()->sendMail("APPROVAL_PENDING", $sitepage->page_id);
                }

                $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
                $row = $manageadminsTable->createRow();
                $row->user_id = $sitepage->owner_id;
                $row->page_id = $sitepage->page_id;
                $row->save();

                // Start profile maps work
                Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->profileMapping($sitepage);
                $page_id = $sitepage->page_id;



                if (!empty($sitepageUrlEnabled)) {
                    $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($page_url))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $values['page_url'] = $page_url . '-' . $page_id;
                        $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
                    } else {
                        $values['page_url'] = $page_url;
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

                if (!empty($page_id)) {
                    $sitepage->setLocation();
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

                $values['auth_view'] = (isset($values['auth_view']) && !empty($values['auth_view'])) ? $values['auth_view'] : "everyone";
                $values['auth_comment'] = (isset($values['auth_comment']) && !empty($values['auth_comment'])) ? $values['auth_comment'] : "everyone";
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

                // Start work for sub page.
                $values['auth_sspcreate'] = (isset($values['auth_sspcreate']) && !empty($values['auth_sspcreate'])) ? $values['auth_sspcreate'] : "owner";

                $createMax = array_search($values['auth_sspcreate'], $roles);
                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitepage, $role, 'sspcreate', ($i <= $createMax));
                }
                // End work for subpage
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
                    $values['spcreate'] = (isset($values['spcreate']) && !empty($values['spcreate'])) ? $values['spcreate'] : "registered";
                    $createMax = array_search($values['spcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $createMax));
                    }
                    // End photo privacy work
                }

                // End sitepagealbum privacy work
                // Start sitepagesocument privacy work
                $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
                if ($sitepageDocumentEnabled) {
                    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
                    if (!empty($sitepagememberEnabled)) {
                        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    } else {
                        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    }

                    $values['sdcreate'] = (isset($values['sdcreate']) && !empty($values['sdcreate'])) ? $values['sdcreate'] : "registered";

                    $createMax = array_search($values['sdcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $createMax));
                    }
                }
                // End sitepagedocument privacy work
                // Start sitepagevideo privacy work
                $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
                if ($sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                    $values['svcreate'] = (isset($values['svcreate']) && !empty($values['svcreate'])) ? $values['svcreate'] : "registered";

                    $createMax = array_search($values['svcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $createMax));
                    }
                }
                // End sitepagevideo privacy work
                // Start sitepagepoll privacy work
                $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
                if ($sitepagePollEnabled) {
                    $values['splcreate'] = (isset($values['splcreate']) && !empty($values['splcreate'])) ? $values['splcreate'] : "registered";

                    $createMax = array_search($values['splcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $createMax));
                    }
                }
                // End sitepagepoll privacy work
                // Start sitepagenote privacy work
                $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
                if ($sitepageNoteEnabled) {
                    $values['sncreate'] = (isset($values['sncreate']) && !empty($values['sncreate'])) ? $values['sncreate'] : "registered";

                    $createMax = array_search($values['sncreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $createMax));
                    }
                }


                // End sitepagenote privacy work
                // Start sitepagemusic privacy work
                $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
                if ($sitepageMusicEnabled) {
                    $values['smcreate'] = (isset($values['smcreate']) && !empty($values['smcreate'])) ? $values['smcreate'] : "registered";

                    $createMax = array_search($values['smcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'smcreate', ($i <= $createMax));
                    }
                }
                // End sitepagemusic privacy work
                // Start sitepageevent privacy work
                $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
                if ($sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('sitepageevent') && Engine_Api::_()->getDbtable('modules', 'sitepageevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                    $values['specreate'] = (isset($values['specreate']) && !empty($values['specreate'])) ? $values['specreate'] : "registered";

                    $createMax = array_search($values['specreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitepage, $role, 'specreate', ($i <= $createMax));
                    }
                }
                // End sitepageevent privacy work
                // Start sitepagemember privacy work
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
                // End sitepagemember privacy work
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
                // Start subpage work
                $parent_id = $this->_getParam('parent_id');
                if (!empty($parent_id)) {
                    $sitepage->subpage = 1;
                    $sitepage->parent_id = $parent_id;
                    $sitepage->save();
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
                            if (isset($values[$key]) && !empty($values[$key])) {
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

                // Start default email to superadmin when anyone create pages.
                $emails = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.defaultpagecreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress());
                if (!empty($emails)) {
                    $emails = explode(",", $emails);
                    $host = $_SERVER['HTTP_HOST'];
                    $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                    $object_link = $newVar . $host . $sitepage->getHref();
                    $viewerGetTitle = $viewer->getTitle();
                    $sender_link = '<a href=' . $newVar . $host . $viewer->getHref() . ">$viewerGetTitle</a>";
                    foreach ($emails as $email) {
                        $email = trim($email);
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITEPAGE_PAGE_CREATION', array(
                            'sender' => $sender_link,
                            'object_link' => $object_link,
                            'object_title' => $sitepage->getTitle(),
                            'object_description' => $sitepage->getDescription(),
                            'queue' => true
                        ));
                    }
                }
                // End default email to superadmin when anyone create pages.

                if (!empty($sitepage) && !empty($sitepage->draft) && empty($sitepage->pending)) {
                    Engine_Api::_()->sitepage()->attachPageActivity($sitepage);


                    // Start AUTOMATICALLY LIKE THE PAGE WHEN MEMBER CREATE A PAGE.
                    $autoLike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.automatically.like', 1);
                    if (!empty($autoLike)) {
                        Engine_Api::_()->sitepage()->autoLike($sitepage->page_id, 'sitepage_page');
                    }
                    //END automatically like the page when member create a page.
                    // Sending activity feed to facebook.
                    $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                    if (!empty($enable_Facebooksefeed)) {
                        $sitepage_array = array();
                        $sitepage_array['type'] = 'sitepage_new';
                        $sitepage_array['object'] = $sitepage;
                        Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitepage_array);
                    }
                }
                // Commit
                $db->commit();
                $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getSitepage($sitepage);

                $this->respondWithSuccess($response, true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Returns the Directory page search form 
     * 
     */
    public function searchFormAction() {

        // Validate request method
        $this->validateRequestMethod();
        $this->respondWithSuccess(Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getBrowseSearchForm());
    }

    /**
     * Returns Categories , Sub-Categories, SubSub-Categories and pages array
     * 
     * 
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
        $showCategories = $this->getRequestParam('showCategories', 1);

        $page = $this->getRequestParam('page', 1);
        $limit = $this->getRequestParam('limit', 20);

        $showPages = $this->getRequestParam('showPages', 1);
        if ($this->getRequestParam('showCount')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }
        $orderBy = $this->getRequestParam('orderBy', 'category_name');

        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitepage');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $categories = array();

        // Get pages table
        $tableSitepage = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $sitepageShowAllCategories = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.categorywithslugs', 0);
        $showAllCategories = 1;

        if ($showCategories) {
                if ($showAllCategories) {
                    $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order'), null, 0, 0, 1, 0, $orderBy, 1);
                    $categoriesCount = count($category_info);
                     foreach ($category_info as $value) {
                        $sub_cat_array = array();
                        $photoName = Engine_Api::_()->storage()->get($value['file_id'], '');
                        if ($showCount) {
                            $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'order' => $value->cat_order,
                                'count' => $value->count,
                            );

                            if (!empty($photoName)) {
                                $category_array['image_icon'] = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                            }
                            $categoryImage = Engine_Api::_()->storage()->get($value['photo_id'], '');
                            if (!empty($categoryImage)) {
                                $category_array['image_profile'] = (strstr($categoryImage->getPhotoUrl(), 'http')) ? $categoryImage->getPhotoUrl() : $getHost . $categoryImage->getPhotoUrl();
                            }
                            
                            
                        } else {
                            $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'order' => $value->cat_order,
                            );
                            if (!empty($photoName)) {
                                $category_array['image_icon'] = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                            } 
                           $categoryImage = Engine_Api::_()->storage()->get($value['photo_id'], '');
                            if (!empty($categoryImage)) {
                                $category_array['image_profile'] = (strstr($categoryImage->getPhotoUrl(), 'http')) ? $categoryImage->getPhotoUrl() : $getHost . $categoryImage->getPhotoUrl();
                            }
                        }
                        $categories[] = $category_array;
                    }
                } else {
                    $category_info = $tableCategory->getAllCategories(0, 'category_id', null, array(), array('category_id', 'category_name', 'cat_order'));
                    $categoriesCount = count($category_info);
                    foreach ($category_info as $value) {
                        $photoName = Engine_Api::_()->storage()->get($value['file_id'], '');
                        if ($showCount) {
                            $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'order' => $value->cat_order,
                                'count' => $value->count,
                            );

                            if (!empty($photoName)) {
                                $category_array['image_icon'] = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                            }
                            $categoryImage = Engine_Api::_()->storage()->get($value['photo_id'], '');
                            if (!empty($categoryImage)) {
                                $category_array['image_profile'] = (strstr($categoryImage->getPhotoUrl(), 'http')) ? $categoryImage->getPhotoUrl() : $getHost . $categoryImage->getPhotoUrl();
                            }
                            
                        } else {
                            $category_array = array('category_id' => $value->category_id,
                                'category_name' => $this->translate($value->category_name),
                                'order' => $value->cat_order,
                                'count' => $value->count,
                            );

                            if (!empty($photoName)) {
                                $category_array['image_icon'] = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
                            }
                            $categoryImage = Engine_Api::_()->storage()->get($value['photo_id'], '');
                            if (!empty($categoryImage)) {
                                $category_array['image_profile'] = (strstr($categoryImage->getPhotoUrl(), 'http')) ? $categoryImage->getPhotoUrl() : $getHost . $categoryImage->getPhotoUrl();
                            }
                            
                        }
                        $categories[] = $category_array;
                    }
                }

            $response['categories'] = $categories;

            if (!empty($category_id)) {

                if ($showAllCategories) {
                    try {
                        $category_info2 = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getSubcategories($category_id);
                    } catch (Exception $ex) {
                        
                    }

                    foreach ($category_info2 as $subresults) {
                        if ($showCount) {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'count' => Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getPagesCount($subresults->category_id, 'subcategory_id', 0),
                                'order' => $subresults->cat_order);
                        } else {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'order' => $subresults->cat_order);
                        }
                    }
                } else {
                    $category_info2 = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getCategorieshaspages($category_id, 'subcategory_id', null, array(), array('category_id', 'category_name', 'cat_order'));
                    foreach ($category_info2 as $subresults) {
                        if ($showCount) {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'count' => Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getPagesCount($subresults->category_id, 'subcategory_id', 0),
                                'order' => $subresults->cat_order);
                        } else {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'order' => $subresults->cat_order);
                        }
                    }
                }

                $response['subCategories'] = $sub_cat_array;
            }

            if (!empty($subCategory_id)) {

                if ($showAllCategories) {
                    $subcategory_info2 = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getSubcategories($subCategory_id, array('category_id', 'category_name', 'cat_order'));
                    $treesubarrays = array();
                    foreach ($subcategory_info2 as $subvalues) {
                        if ($showCount) {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'count' => Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getPagesCount($subvalues->category_id, 'subsubcategory_id', 0),
                                'order' => $subvalues->cat_order,
                            );
                        } else {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'order' => $subvalues->cat_order,
                            );
                        }
                    }
                } else {
                    $subcategory_info2 = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getCategorieshaspages($subCategory_id, 'subsubcategory_id', null, array(), array('category_id', 'category_name', 'cat_order'));
                    $treesubarrays = array();
                    foreach ($subcategory_info2 as $subvalues) {
                        if ($showCount) {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'order' => $subvalues->cat_order,
                                'count' => Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getPagesCount($subvalues->category_id, 'subsubcategory_id', 0),
                            );
                        } else {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'order' => $subvalues->cat_order
                            );
                        }
                    }
                }
                $response['subsubCategories'] = $treesubarrays;
            }
        }

        if ($this->getRequestParam('action', null) == 'browse')
            return $response;

        if ($showPages && isset($category_id) && !empty($category_id)) {
            $params = array();
            $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 0);

            // Get categories
            $categories = array();

            $category_info = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getCategorieshaspages($category_id, 'category_id', $itemCount, $params, array('category_id', 'category_name', 'cat_order'));
            $category_pages_array = array();

            $params = $this->_getAllParams();

            // Set location of app dashboard
            if (isset($params['restapilocation']) && !empty($params['restapilocation']))
                $params['location'] = $params['restapilocation'];

            // Set location of adv search form
            if (isset($_GET['location']) && !empty($_GET['location']))
                $params['location'] = $_GET['location'];


            // Get page results
            $category_pages_info = $this->_getDirectoryPages($params);

            foreach ($category_pages_info['response'] as $result_info) {
                if (is_array($result_info))
                    $result_info = Engine_Api::_()->getItem('sitepage_page', $result_info['page_id']);
                $tmp_array = array('page_id' => $result_info->page_id,
                    'imageSrc' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($result_info),
                    'page_title' => $result_info->title,
                    'owner_id' => $result_info->owner_id,
                    'viewCount' => $result_info->view_count,
                    'slug' => $result_info->getSlug(),
                );
                $category_pages_array[] = $tmp_array;
            }

            $response['pages'] = $category_pages_info['response'];
        }
        $response['totalPageCount'] = $category_pages_info['totalItemCount'];
        if (isset($categoriesCount) && !empty($categoriesCount))
            $response['totalItemCount'] = $categoriesCount;
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create');
        $this->respondWithSuccess($response, true);
    }

    /**
     * Returns the tabs menu of the Directory Page
     * 
     * @return array
     */
    private function _tabsMenus() {
        if (!Engine_Api::_()->core()->hasSubject('page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $tabsMenu = array();

        // Prepare updated count
        $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $updates_count = $streamTable->select()
                        ->from($streamTable->info('name'), 'count(*) as count')
                        ->where('object_id = ?', $subject->page_id)
                        ->where('object_type = ?', "sitepage_page")
                        ->where('target_type = ?', "sitepage_page")
                        ->where('type like ?', "%post%")
                        ->query()->fetchColumn();

        $tabsMenu['updates'] = array(
            'count' => $updates_count,
            'name' => 'updates',
            'label' => $this->translate('Updates'),
            'url' => 'sitepage/updates/' . $subject->getIdentity(),
        );

        $tabsMenu['info'] = array(
            'name' => 'info',
            'label' => $this->translate('Info'),
            'url' => 'sitepage/info/' . $subject->getIdentity()
        );


        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
            $photos_count = Engine_Api::_()->getDbtable('photos', 'sitepage')->countTotalPhotos(array('page_id' => $sitepage->page_id));
            $tabsMenu['photos'] = array(
                'count' => $photos_count,
                'name' => 'photos',
                'label' => $this->translate('Photos'),
                'url' => 'sitepage/photos/' . $subject->getIdentity(),
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {

            $review_count = Engine_Api::_()->getDbtable('reviews', 'sitepagereview')->totalReviews($sitepage->page_id);
            $tabsMenu['reviews'] = array(
                'count' => $review_count,
                'name' => 'reviews',
                'label' => $this->translate('Reviews'),
                'url' => 'sitepage/Reviews/' . $subject->getIdentity(),
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $videos_count = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getPageVideoCount($sitepage->page_id);
            $tabsMenu['videos'] = array(
                'count' => $videos_count,
                'name' => 'videos',
                'label' => $this->translate('Videos'),
                'url' => 'sitepage/Videos/' . $subject->getIdentity(),
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
            $discussion_count = Engine_Api::_()->getDbtable('topics', 'sitepage')->countTotalTopics($sitepage->page_id);
            $tabsMenu['discussions'] = array(
                'count' => $discussion_count,
                'name' => 'topics',
                'label' => $this->translate('Discussions'),
                'url' => 'sitepage/topics/' . $subject->getIdentity(),
            );
        }

        return $tabsMenu;
    }

    /**
     * Returns the paginated Directory pages listings after filtering from search papameters if sent
     * 
     * 
     */
    private function _getDirectoryPages($params) {
        $response = $tempParams = $data = $tempResponse = array();
        $imageType = 'thumb.icon';
        $viewer = Engine_Api::_()->user()->getViewer();
        $tableObj = Engine_Api::_()->getDbtable('pages', 'sitepage');

        if ($params['action'] != "category")
            $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->checkRequire();

        if (empty($params['manage']) && $params['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        if (isset($params['image_type']) && !empty($params['image_type']))
            $imageType = $params['image_type'];
        $pagesObj = Engine_Api::_()->sitepage()->getSitepagesPaginator($params);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', $params['limit']);
        $pagesObj->clearPageItemCache();
        $response['totalItemCount'] = $getTempPagesCount = $pagesObj->getTotalItemCount();
        if (isset($params['show']) && $params['show'] == 6) {
            $columnsArray = array('page_id', 'title', 'page_url', 'owner_id', 'category_id', 'photo_id', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $columnsArray[] = 'member_count';
            }
            $columnsArray[] = 'member_title';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                $columnsArray[] = 'review_count';
                $columnsArray[] = 'rating';
            }
            $pagesObj = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Sponosred', $params, null, null, $columnsArray);
            $response['totalItemCount'] = $getTempPagesCount = count($pagesObj);
        }

        if ($getTempPagesCount) {
            foreach ($pagesObj as $pageObj) {

                $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageObj, 'edit');

                $data = $pageObj->toArray();

                if (!empty($params['manage']) && ($isManageAdmin))
                    $data["menu"] = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->gutterMenus($pageObj, 'manage');

                $categoryObj = Engine_Api::_()->getItem('sitepage_category', $data['category_id']);
                if (isset($categoryObj) && !empty($categoryObj))
                    $data['category_title'] = $categoryObj->getTitle();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($pageObj);
                $data = array_merge($data, $getContentImages);

                // Add owner images
                $getContentOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($pageObj, true);
                $data = array_merge($data, $getContentOwnerImages);

                $data["owner_title"] = $pageObj->getOwner()->getTitle();
                $ownerUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($pageObj->getOwner(), "owner_url");
                $data = array_merge($data, $ownerUrl);

                $contentUrl = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($pageObj);
                $data = array_merge($data, $contentUrl);

                $isAllowedView = $pageObj->authorization()->isAllowed($viewer, 'view');
                $data["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $isAllowedEdit = $pageObj->authorization()->isAllowed($viewer, 'edit');
                $data["edit"] = empty($isAllowedEdit) ? 0 : 1;
                $isAllowedDelete = $pageObj->authorization()->isAllowed($viewer, 'delete');
                $data["delete"] = empty($isAllowedDelete) ? 0 : 1;

                $tempResponse[] = $data;
            }

            if (!empty($tempResponse))
                $response['response'] = $tempResponse;
        }
        return $response;
    }

    /*
     * Page url Validation
     *
     */

    public function pageurlvalidationAction() {

        $this->validateRequestMethod();

        $page_url = $this->_getParam('page_url');
        if (!$page_url)
            $this->respondWithValidationError('parameter_missing', array('page_url' => "parameter named page_url missing"));

        $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
        if (!empty($sitepageUrlEnabled)) {
            $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
        }
        if (empty($page_url)) {
            $this->respondWithValidationError('urlNotvalid', "Url not valid");
        }

        $url_lenght = strlen($page_url);
        if ($url_lenght < 3) {
            $this->respondWithValidationError('urlNotvalid', "Url should be atleast 3 characters long");
        } elseif ($url_lenght > 255) {
            $this->respondWithValidationError('urlNotvalid', "url should be atmost 255 characters long");
        }

        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);
        $check_url = $this->_getParam('check_url');
        if (!empty($check_url)) {
            $pageId = $this->_getParam('page_id');
            $page_id = Engine_Api::_()->sitepage()->getPageId($page_url, $pageId);
        } else {
            $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
        }
        if (!empty($sitepageUrlEnabled)) {
            if (!empty($page_id) || (in_array(strtolower($page_url), $urlArray))) {
                $this->respondWithValidationError('urlNotvalid', "Url not available");
            }
        } else {
            if (!empty($page_id)) {
                $this->respondWithValidationError('urlNotvalid', "Url not available");
            }
        }



        if (!preg_match("/^[a-zA-Z0-9-_]+$/", $page_url)) {
            $this->respondWithValidationError('urlNotvalid', "URL component can contain alphabets, numbers, underscores & dashes only");
        } else {
            $this->successResponseNoContent('no_content');
        }
    }

    private function checkLevelHost($object, $itemType) {
        $length = 7;
        $encodeorder = 0;
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder += ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitepage()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $encodeorder;
        }
    }

    private function checkPackageHost($strKey) {
        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -= 4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitepage()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $getStr = implode($key);
        }
    }

    private function _packagesEnabled() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1)) {

                $packageTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
                $packageSql = $packageTable->getPackagesSql();
                $paginator = Zend_Paginator::factory($packageSql);
                $packageCount = $paginator->getTotalItemCount();

                if ($packageCount == 1) {
                    foreach ($paginator as $row => $package) {
                        if (($package->price == '0.00')) {
                            return 0;
                        } else
                            return 1;
                    }
                } else {
                    $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.overview', 0);

                    if ($packageCount > 0) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            }
        }
        return 0;
    }

    private function _categoryIcon($value) {
        try {

            $photoName = Engine_Api::_()->storage()->get($value['file_id'], '');
            if (!empty($photoName)) {
                $image_icon = (strstr($photoName->getPhotoUrl(), 'http')) ? $photoName->getPhotoUrl() : $getHost . $photoName->getPhotoUrl();
            } else {
                $getDefaultImage = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
                $image_icon = $getDefaultImage['image_icon'];
            }
            $ContentImage = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value);
            $ContentImage['image_icon'] = $image_icon;
            return $ContentImage;
        } catch (Exception $ex) {
            
        }
    }

}
