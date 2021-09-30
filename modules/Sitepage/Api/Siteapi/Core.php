<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_Siteapi_Core extends Core_Api_Abstract {
    /**
     * Get the "Directory Pages Search" form.
     * 
     * @return array
     */
    public function getBrowseSearchForm() {

        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitepage');
        $sitepageofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
        $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
        $coreSettings = Engine_Api::_()->getDbtable('settings', 'core');

        if (isset($sitepageofferEnabled) && !empty($sitepageofferEnabled) && isset($searchFormSettings['offer_type']) && !empty($searchFormSettings['offer_type']) && isset($searchFormSettings['offer_type']['display']) && !empty($searchFormSettings['offer_type']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'offer_type',
                'label' => $this->translate('Pages With Offers'),
                'multiOptions' => array(
                    '' => '',
                    'all' => 'All Offers',
                    'hot' => 'Hot Offers',
                    'featured' => 'Featured Offers',
                )
            );
        }

        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => $this->translate('Search Pages')
        );

        $sitepage_location = $coreSettings->getSetting('sitepage.locationfield', 1);
        if ($sitepage_location) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'sitepage_location',
                'label' => $this->translate(' Location ')
            );
        }

        if (!empty($searchFormSettings['location']) && !empty($searchFormSettings['location']['display'])) {
            $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
            if (!empty($enableLocation)) {
                if (!empty($searchFormSettings['locationmiles']) && !empty($searchFormSettings['locationmiles']['display'])) {
                    $enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximitysearch', 1);
                    if (!empty($enableProximitysearch)) {
                        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
                        if ($flage) {
                            $locationLable = "Within Kilometers";
                            $locationOption = array(
                                '0' => '',
                                '1' => '1 Kilometer',
                                '2' => '2 Kilometers',
                                '5' => '5 Kilometers',
                                '10' => '10 Kilometers',
                                '20' => '20 Kilometers',
                                '50' => '50 Kilometers',
                                '100' => '100 Kilometers',
                                '250' => '250 Kilometers',
                                '500' => '500 Kilometers',
                                '750' => '750 Kilometers',
                                '1000' => '1000 Kilometers',
                            );
                        } else {
                            $locationLable = "Within Miles";
                            $locationOption = array(
                                '0' => '',
                                '1' => '1 Mile',
                                '2' => '2 Miles',
                                '5' => '5 Miles',
                                '10' => '10 Miles',
                                '20' => '20 Miles',
                                '50' => '50 Miles',
                                '100' => '100 Miles',
                                '250' => '250 Miles',
                                '500' => '500 Miles',
                                '750' => '750 Miles',
                                '1000' => '1000 Miles',
                            );
                        }

                        $searchForm[] = array(
                            'type' => 'Select',
                            'name' => 'locationmiles',
                            'label' => $locationLable,
                            'multiOptions' => $locationOption,
                        );
                    }
                }
            }
        }

        if (
                isset($searchFormSettings['street']) &&
                !empty($searchFormSettings['street']) &&
                isset($searchFormSettings['street']['display']) &&
                !empty($searchFormSettings['street']['display'])
        ) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'sitepage_street',
                'label' => $this->translate('Street')
            );
        }

        if (isset($searchFormSettings['city']) && !empty($searchFormSettings['city']) && isset($searchFormSettings['city']['display']) && !empty($searchFormSettings['city']['display'])) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'sitepage_city',
                'label' => $this->translate('City')
            );
        }

        if (isset($searchFormSettings['state']) && !empty($searchFormSettings['state']) && isset($searchFormSettings['state']['display']) && !empty($searchFormSettings['state']['display'])) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'sitepage_state',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('State')
            );
        }

        if (isset($searchFormSettings['country']) && !empty($searchFormSettings['country']) && isset($searchFormSettings['country']['display']) && !empty($searchFormSettings['country']['display'])) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'sitepage_country',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country')
            );
        }


        if (isset($searchFormSettings['show']) && !empty($searchFormSettings['show']) && isset($searchFormSettings['show']['display']) && !empty($searchFormSettings['show']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
                'multiOptions' => array(
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone\'s Pages'),
                    '2' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends\' Pages'),
                    '4' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pages I Like'),
                    '5' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Featured Pages')
                )
            );
        }

        if (isset($searchFormSettings['closed']) && !empty($searchFormSettings['closed']) && isset($searchFormSettings['closed']['display']) && !empty($searchFormSettings['closed']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'closed',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                'multiOptions' => array(
                    '' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Pages'),
                    '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Open Pages'),
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Closed Pages')
                )
            );
        }

        if (isset($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']['display']) && isset($sitepagereviewEnabled) && !empty($sitepagereviewEnabled)) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => array(
                    '' => '',
                    'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Commented'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Liked'),
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Alphabetical'),
                    'review_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Reviewed'),
                    'rating' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Rated')
                ),
            );
        } elseif (isset($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']) && isset($searchFormSettings['orderby']['display']) && !empty($searchFormSettings['orderby']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => array(
                    '' => '',
                    'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Commented'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Liked'),
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Alphabetical'),
                ),
            );
        }


        $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
        $getCategoryArray = array();
        $getCategoryArray[0] = "";
        if (isset($categories) && !empty($categories)) {
            foreach ($categories as $category)
                $getCategoryArray[$category->category_id] = $category->category_name;
        }

        if (count($getCategoryArray) > 0) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $getCategoryArray
            );
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        $searchFormData = array();
        $searchFormData['form'] = $searchForm;
        return $searchFormData;
    }

    /*
     * Get sitepage
     */
    public function getSitepage($sitepage, $params = null) {
        $subject = $sitepage;

        // viewer information
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $response = $tagArray = array();

        $response = $subject->toArray();

        // check for 
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (!empty($isManageAdmin))
            $response['can_upload_photo'] = 1;
        else
            $response['can_upload_photo'] = 0;

        $followsData = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollow($subject, $viewer);

        $response['isPageFollowed'] = 0;
        if ($followsData)
            $response['isPageFollowed'] = 1;

        $response['isOwner'] = 0;
        if ($subject->isOwner($viewer))
            $response['isOwner'] = 1;

        $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $response['currency'] = $currency;

        $contentURL = Engine_Api::_()->getApi('Core', 'siteapi')->getContentURL($subject);

        if (isset($contentURL) && !empty($contentURL))
            $response = array_merge($response, $contentURL);

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
        $getOwnerImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
        $response = array_merge($response, $getContentImages);
        $response = array_merge($response, $getOwnerImages);
        $response["owner_title"] = $subject->getOwner()->getTitle();
        // get the category profile fields
        $profileFields = $this->getInfoFields($sitepage);

        // Getting viewer like or not to content.
        $response["is_like"] = (bool) Engine_Api::_()->getApi('Core', 'siteapi')->isLike($subject);

        // Getting like count.
        $response["like_count"] = Engine_Api::_()->getApi('Core', 'siteapi')->getLikeCount($subject);

        //Advanced video integration..........................
        if (method_exists('Siteapi_Api_Core', 'isSitevideoPluginEnabled')) {
            $subject_type = $subject->getType();
            $subject_id = $subject->getIdentity();
            $advVideoEnableArray = Engine_Api::_()->getApi('Core', 'siteapi')->isSitevideoPluginEnabled($subject_type, $subject_id);
            if (!empty($advVideoEnableArray) && is_array($advVideoEnableArray)) {
                $response = array_merge($response, $advVideoEnableArray);
            } else {
                $response['sitevideoPluginEnabled'] = 0;
            }
        } else {
            $response['sitevideoPluginEnabled'] = 0;
        }

        //end of  Advanced video integration work..........................

        $sitepageTags = $sitepage->tags()->getTagMaps();
        $tagString = '';

        foreach ($sitepageTags as $tagmap) {
            $tagMapObject = $tagmap->getTag();
            if(empty($tagMapObject))
                continue;
            if ($tagString !== '')
                $tagString .= ', ';
            $tagString .= $tagMapObject->getTitle();
        }

        // Page View Count Increment
        if (!$subject->isOwner($viewer)) {
            Engine_Api::_()->getDbtable('pages', 'sitepage')->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
                    ), array(
                'page_id = ?' => $subject->getIdentity(),
            ));
        }
        if (!empty($tagString))
            $response['tags'] = $tagString;

        if (isset($response['email']) && !empty($response['email']))
            $response['contactInfo']['Email'] = $response['email'];

        if (isset($response['phone']) && !empty($response['phone']))
            $response['contactInfo']['Phone'] = $response['phone'];

        if (isset($response['website']) && !empty($response['website']))
            $response['contactInfo']['Website'] = $response['website'];

        $categoryObj = Engine_Api::_()->getItem('sitepage_category', $response['category_id']);
        if (!empty($categoryObj))
            $response['category_title'] = $categoryObj->getTitle();
        if(empty($response['price'])){
            unset($response['price']);
            unset($response['currency']);
        }

        // Getting the gutter-menus.
        $response['profile_fields'] = $profileFields;
        $response['gutterMenu'] = $this->gutterMenus($subject);
        $response['profile_tabs'] = $this->_tabsMenus($subject);
        return $response;
    }

    /**
     * Returns the tabs menu of the Directory Page
     * 
     * @return array
     */
    public function _tabsMenus($sitepage) {
        $subject = $sitepage;

        $tabsMenu = array();

        $tabsMenu[] = array(
            'name' => 'update',
            'label' => $this->translate('Updates'),
            'url' => 'sitepage/updates/' . $subject->getIdentity(),
        );

        $tabsMenu[] = array(
            'name' => 'information',
            'label' => $this->translate('Info'),
            'url' => 'sitepage/information/' . $subject->getIdentity()
        );

        if ($subject->overview) {
            $tabsMenu[] = array(
                'name' => 'overview',
                'label' => $this->translate('Overview'),
                'url' => 'sitepage/overview/' . $subject->getIdentity()
            );
        }


        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
            $flag = false;
            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                if (Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagealbum")) {
                    $flag = true;
                }
            } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'spcreate');
                if (!empty($isPageOwnerAllow)) {
                    $flag = true;
                }
            }

            $photos_count = $this->countTotalPhotos($sitepage->page_id);
            if ($photos_count && $flag) {
                $tabsMenu[] = array(
                    'totalItemCount' => $photos_count,
                    'name' => 'photos',
                    'label' => $this->translate('Albums'),
                    'url' => 'sitepage/photos/index/' . $subject->getIdentity(),
                );
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {

            $review_count = Engine_Api::_()->getDbtable('reviews', 'sitepagereview')->totalReviews($sitepage->page_id);
            if ($review_count) {
                $tabsMenu[] = array(
                    'totalItemCount' => $review_count,
                    'name' => 'reviews',
                    'label' => $this->translate('Reviews'),
                    'url' => 'sitepage/reviews/browse/' . $subject->getIdentity(),
                );
            }
        }

        if (method_exists('Siteapi_Api_Core', 'isSitevideoPluginEnabled') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '3.2.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION > '2.1.6')) {
            $advVideoEnableArray = Engine_Api::_()->getApi('Core', 'siteapi')->isSitevideoPluginEnabled($subject->getType(), $subject->getIdentity());
            if (isset($advVideoEnableArray['sitevideoPluginEnabled']) && !empty($advVideoEnableArray['sitevideoPluginEnabled'])) {
                if (isset($advVideoEnableArray['totalVideoItemCount']) && !empty($advVideoEnableArray['totalVideoItemCount']))
                    $tabsMenu[] = array(
                        'name' => 'video',
                        'label' => $this->translate('Videos'),
                        'totalItemCount' => $advVideoEnableArray['totalVideoItemCount'],
                        'url' => 'advancedvideos/index/' . $subject->getIdentity(),
                        "subject_type" => $subject->getType(),
                        "subject_id" => $subject->getIdentity(),
                        'urlParams' => array(
                            "subject_type" => $subject->getType(),
                            "subject_id" => $subject->getIdentity(),
                            )
                    );
            }
        }
        
        $eventModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
        if (!empty($eventModuleEnabled) && method_exists('Siteevent_Api_Siteapi_Core', 'isSiteeventPluginEnabled') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '55.5') || _CLIENT_TYPE == 'ios' && _IOS_VERSION > '2.4')) {
            $advEventEnableArray = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->isSiteeventPluginEnabled($subject->getType(), $subject->getIdentity());
            if ($advEventEnableArray['totalEventItemCount'] && isset($advEventEnableArray['siteeventPluginEnabled']) && !empty($advEventEnableArray['siteeventPluginEnabled'])) {
                $tabsMenu[] = array(
                    'totalItemCount' => $advEventEnableArray['totalEventItemCount'],
                    'name' => 'advevents',
                    'label' => $this->translate('Events'),
                    'url' => 'advancedevents/events-integration',
                    'urlParams' => array(
                        'subject_type' => $subject->getType(),
                        'subject_id' => $subject->getIdentity()
                    )
                );
            }
        }


        return $tabsMenu;
    }

    /**
     * Get the "Page Create" form.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    public function getForm($sitepage = null, $action = null) {

        $user = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $user->getIdentity();
        $createForm = array();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;
        $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        $allowMemberInthisPackage = false;
        if(!empty($sitepage))
        $allowMemberInthisPackage = Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember");
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $createFormFields = array(
            'location',
            'tags',
            'photo',
            'description',
            'overview',
            'price',
            'viewPrivacy',
            'commentPrivacy',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options',
            'allPostPrivacy'
        );

        try {

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                $createFormFields = array_merge($createFormFields, array('discussionPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                $createFormFields = array_merge($createFormFields, array('photoPrivacy'));
            }

            if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
                $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
            } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
                $createFormFields = array_merge($createFormFields, array('EventPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
                $createFormFields = array_merge($createFormFields, array('documentPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
                $createFormFields = array_merge($createFormFields, array('pollPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
                $createFormFields = array_merge($createFormFields, array('notePrivacy'));
            }

            // if ((Engine_Api::_()->hasModuleBootstrap('sitepage') && Engine_Api::_()->getDbtable('modules', 'sitepage')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            //     $createFormFields = array_merge($createFormFields, array('pagePrivacy'));
            // } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
            //     $createFormFields = array_merge($createFormFields, array('pagePrivacy'));
            // }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                $createFormFields = array_merge($createFormFields, array('musicPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $createFormFields = array_merge($createFormFields, array('memberTitle'));
                $createFormFields = array_merge($createFormFields, array('memberInvite'));
                $createFormFields = array_merge($createFormFields, array('memberApproval'));
            }
            if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy'));
        }
        

            $createFormFields = array_merge($createFormFields, array(
                'subPagePrivacy',
                'claimThisPage',
                'status',
                'search'
            ));

            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitepage.createFormFields')) {
                $createFormFields = $settings->getSetting('sitepage.createFormFields', $createFormFields);
            }

            // Page title
            $createForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Title'),
                'hasValidator' => true
            );

            // Page Url
//            $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
            $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1);

            if ($show_url && $action != 'edit') {
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'profileur',
                    'label' => $this->translate('URL'),
                    'hasValidator' => true
                );
            }

            // Location
            $locationSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);

            if (isset($createFormFields) && !empty($createFormFields) && in_array('location', $createFormFields) && $locationSetting) {

                $locationDefault = $settings->getSetting('seaocore.locationdefault', '');
                $seaocore_locationspecific = $settings->getSetting('seaocore.locationspecific', '');
                $seaocore_locationspecificcontent = $settings->getSetting('seaocore.locationspecificcontent', '');

                if ($seaocore_locationspecific && $seaocore_locationspecificcontent) {
                    $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
                    $locationsArray = array();
                    $locationsArray[] = '';
                    foreach ($locations as $location) {
                        $locationsArray[$location->location] = $location->title;
                    }
                    if ($locations) {
                        $createForm[] = array(
                            'type' => 'Select',
                            'name' => 'location',
                            'description' => $this->translate('Eg: Fairview Park, Berkeley, CA'),
                            'label' => $this->translate('Enter a location'),
                            'multiOptions' => $locationsArray
                        );
                    }
                } else {
                    $createForm[] = array(
                        'type' => 'Text',
                        'name' => 'location',
                        'description' => $this->translate('Eg: Fairview Park, Berkeley, CA'),
                        'label' => $this->translate('Enter a location'),
                    );
                }
            }



//              get the packages
//		$packages = Engine_Api::_()->getDbTable('packages', 'sitepage')->fetchAll();
//		if($packages->count() > 0)
//		{
//			$packagesData = array();
//			foreach ($packages as $package) {
//				$packagesData[$package->package_id] = $package->title;
//			}
//			
//			$createForm[] = array(
//					'type' => 'Select',
//					'name' => 'package_id',
//					'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Select Package'),
//					'multiOptions' => $packagesData
//			);
//
//		}
            // Sending Categories

            $categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                $getCategories = array();
                $getCategories[0] = "";
                $categories_prepared[0] = "";
                $subCategories = array();
                $subcategoryData = array();
                $subsubCategories = array();
                foreach ($categories as $category) {
                    $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitepage')->getSubCategories($category->category_id);
                    if ($subCategoriesObj->count() > 0) {

                        unset($subCategories);
                        $subCategories[$category->category_id]['form'] = array(
                            "type" => "Select",
                            "name" => "subcategory_id",
                            "label" => "Sub-Category",
                            'multiOptions' => array(),
                        );
                        $subCategories[$category->category_id]['form']['multiOptions'][0] = "";
                        foreach ($subCategoriesObj as $subcategory) {
                            $subCategories[$category->category_id]['form']['multiOptions'][$subcategory->category_id] = $subcategory->category_name;
                            $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitepage')->getSubCategories($subcategory->category_id);
                            if ($subsubCategoriesObj->count() > 0) {
                                unset($subsubCategories);
                                $subsubCategories[$subcategory->category_id] = array(
                                    "type" => "Select",
                                    "name" => "subsubcategory_id",
                                    "label" => "3rd Level Category",
                                    "multiOptions" => array(),
                                );
                                $subsubCategories[$subcategory->category_id]['multiOptions'][0] = "";
                                foreach ($subsubCategoriesObj as $subsubcategory) {
                                    $subsubCategories[$subcategory->category_id]['multiOptions'][$subsubcategory->category_id] = $subsubcategory->category_name;
                                }
                                $subCategories[$category->category_id]['subsubCategories'] = $subsubCategories;
                            }
                        }
                        $subcategoryData[$category->category_id] = $subCategories[$category->category_id];
                    }
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);
                }
            }
            if (count($getCategories) > 0)
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                    'hasValidator' => 'true'
                );

            if (!empty($sitepage) && !empty($sitepage->category_id) && isset($subcategoryData[$sitepage->category_id]['form']))
                $createForm[] = $subcategoryData[$sitepage->category_id]['form'];

            // fields work
            $profileFields = $this->getProfileTypes();

            if (!empty($profileFields)) {
                $this->_profileFieldsArray = $profileFields;
            }
            $this->_create = 1;
            $fieldsArray = $this->_getProfileFields();
            $fieldsArrayMaps = array();
            $categoriesMaps = Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->fetchAll()->toArray();

            foreach ($categoriesMaps as $key => $value) {
                $fieldsArrayMaps[$value['category_id']] = $fieldsArray[$value['profile_type']];
                unset($fieldsArray[$value['profile_type']]);
            }

            if (!empty($sitepage->category_id)) {
                if($sitepage->subcategory_id){
                     $data = $fieldsArrayMaps[$sitepage->subcategory_id];
                }else{
                    $data = $fieldsArrayMaps[$sitepage->category_id]; 
                }
                foreach ($data as $row => $value)
                    $createForm[] = $value;
            }

            // Tags
            if (!empty($createFormFields) && in_array('tags', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.tags', 1)) {
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'tags',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
                    'description' => $this->translate('Separate tags with commas.'),
                );
            }

            // Page Body
            $createForm[] = array(
                'type' => 'Textarea',
                'name' => 'body',
                'label' => $this->translate('Description'),
                'hasValidator' => $settings->getSetting('sitepage.requried.description', 1)?true:false
            );

            // Photo
            // $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', "photo");
            if (in_array('photo', $createFormFields) && !$sitepage) {
                $createForm[] = array(
                    'type' => 'File',
                    'name' => 'photo',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo')
                );
            }


            // Package based checks
            if ($hasPackageEnable) {
                if (Engine_Api::_()->sitepagepaid()->allowPackageContent($package_id, "overview")) {
                    $allowOverview = 1;
                } else {
                    $allowOverview = 0;
                }
            } else {
                // AUthorization checks
                $allowOverview = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitepage_page', "overview");
            }

            // Package based checks
            $allowEdit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitepage_page', "edit");

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.overview', 1) && isset($createFormFields) && (!empty($createFormFields) && in_array('overview', $createFormFields)) && $allowOverview && $allowEdit) {
                $description = 'Short Description';
            } else {
                $description = 'Description';
            }

            if (!empty($createFormFields) && in_array('price', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price', 0)) {
                //   $localeObject = Zend_Registry::get('Locale');
                $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'price',
                    'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                );
            }

            $availableLabels = array(
                'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SitePage Guests Only'),
                'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner and Leaders Only')
            );
            
            if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
            $availableLabels['member'] = 'Page Members Only';
        } elseif (!empty($sitepageMemberEnabled) && $allowMemberInLevel) {
            $availableLabels['member'] = 'Page Members Only';
        }

            $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "auth_view");
            $view_options = array_intersect_key($availableLabels, array_flip($view_options));
            if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields) && count($view_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may see this Page?"),
                    'multiOptions' => $view_options,
                    'value' => key($view_options),
                );
            }

            $subpages_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "sspcreate");
            $subpages_options = array_intersect_key($availableLabels, array_flip($subpages_options));

            if (!empty($createFormFields) && in_array('subPagePrivacy', $createFormFields) && count($subpages_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sspcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub Page  Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may create sub pages in this page?"),
                    'multiOptions' => $subpages_options,
                );
            } elseif (count($subpages_options) == 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sspcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may create sub pages in this page?'),
                    'value' => $subpages_options,
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sspcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may create sub pages in this page?'),
                    'value' => 'member',
                );
            }

            // Privacy of pool creation in directory page
            $poll_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "splcreate");
            $poll_options = array_intersect_key($availableLabels, array_flip($poll_options));
            if (!empty($createFormFields) && in_array('pollPrivacy', $createFormFields) && count($poll_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'splcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Poll Create Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may create Poll in this page?"),
                    'multiOptions' => $poll_options,
                );
            } elseif (count($poll_options) == 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'splcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Poll Create Privacy'),
                    'value' => $poll_options,
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'splcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Poll Create Privacy'),
                    'value' => 'member',
                );
            }


            // Sitepage Event privacy
//            $event_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "secreate");
//            $event_options = array_intersect_key($availableLabels, array_flip($event_options));
            $availableLabels = array(
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'like_member' => 'Who Liked This Page',
                );
            
            $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'auth_secreate');
                $event_options = array_intersect_key($availableLabels, array_flip($options));
            if (!empty($createFormFields) && in_array('eventPrivacy', $createFormFields) && count($event_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'secreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Create Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may create Event for this page?"),
                    'multiOptions' => $event_options,
                );
            } elseif (count($event_options) == 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'secreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Create Privacy'),
                    'value' => $event_options,
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'secreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Create Privacy'),
                    'value' => 'member',
                );
            }


            // Music privacy
            $music_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "smcreate");
            $music_options = array_intersect_key($availableLabels, array_flip($music_options));
            if (!empty($createFormFields) && in_array('musicPrivacy', $createFormFields) && count($music_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'smcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Music Create Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may create Music in this page?"),
                    'multiOptions' => $music_options,
                );
            } elseif (count($music_options) == 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'smcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Music Create Privacy'),
                    'value' => $music_options,
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'smcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Music Create Privacy'),
                    'value' => 'member',
                );
            }




            $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "auth_comment");
            $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
            if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields) && count($comment_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may comment on this Page?"),
                    'multiOptions' => $comment_options,
                    'value' => key($comment_options),
                );
            }

            $availableLabels = array(
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner and Leaders Only')
            );

            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $this->_parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'sitepage')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'parent_member' => $shortTypeName . ' Members Only',
                        'like_member' => 'Who liked this ' . $shortTypeName,
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'sitepage')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'like_member' => 'Who liked this ' . $shortTypeName,
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'sitepage')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentItem->listingtype_id, 'item_module' => 'sitereview')))) {
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                }
            }
            // Discussion privacy
            $topic_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "sdicreate");

            $topic_options = array_intersect_key($availableLabels, array_flip($topic_options));

            if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields) && count($topic_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sdicreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Discussion Topic Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may post discussion topics for this Page?"),
                    'multiOptions' => $topic_options,
                );
            } elseif (count($topic_options) == 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sdicreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Discussion Topic Privacy'),
                    'value' => $topic_options,
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sdicreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Discussion Topic Privacy'),
                    'value' => 'member',
                );
            }

            //  Photo privacy 
            $photo_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "spcreate");
            $photo_options = array_intersect_key($availableLabels, array_flip($photo_options));
            if (!empty($createFormFields) && in_array('photoPrivacy', $createFormFields) && count($photo_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'spcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may upload photos for this Page?"),
                    'multiOptions' => $photo_options,
                );
            } elseif (count($photo_options) == 1 && $can_show_photo_list) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'spcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Privacy'),
                    'value' => $photo_options
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'spcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Privacy'),
                    'value' => 'member',
                );
            }

            // Document privacy
            $document_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "sdcreate");
            $document_options = array_intersect_key($availableLabels, array_flip($document_options));

            if (!empty($createFormFields) && in_array('documentPrivacy', $createFormFields) && count($document_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sdcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Dcoument Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may Create Documents for this Page?"),
                    'multiOptions' => $document_options,
                );
            } elseif (count($document_options) == 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sdcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Document Privacy'),
                    'value' => $document_options
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'sdcreate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Document Privacy'),
                    'value' => 'member',
                );
            }


            // Video privacy
            $videoEnable = $this->enableVideoPlugin();
            if ($videoEnable) {
                $video_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "svcreate");
                $video_options = array_intersect_key($availableLabels, array_flip($video_options));

                if (!empty($createFormFields) && in_array('videoPrivacy', $createFormFields) && count($video_options) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'svcreate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Privacy'),
                        'value' => 'member',
                        'description' => Zend_Registry::get('Zend_Translate')->_("Who may add videos for this Page?"),
                        'multiOptions' => $video_options,
                    );
                } else if (count($video_options) == 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'svcreate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Privacy'),
                        'value' => $video_options
                    );
                } else {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'svcreate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Privacy'),
                        'value' => 'member'
                    );
                }
            }


            // Notes privacy
            $notesEnable = $this->enableNotesPlugin();
            if ($notesEnable) {
                $notes_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, "sncreate");
                $notes_options = array_intersect_key($availableLabels, array_flip($video_options));

                // Package based checks
                if (!empty($createFormFields) && in_array('notePrivacy', $createFormFields) && count($video_options) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'sncreate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Notes Privacy'),
                        'value' => 'member',
                        'description' => Zend_Registry::get('Zend_Translate')->_("Who may add Notes for this Page?"),
                        'multiOptions' => $notes_options,
                    );
                } elseif (count($notes_options) == 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'sncreate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Notes Privacy'),
                        'value' => $notes_options,
                    );
                } else {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'sncreate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Notes Privacy'),
                        'value' => 'member',
                    );
                }
            }

            if ($action != 'edit') {
                $createForm[] = array(
                    'label' => $this->translate('Status'),
                    'name' => 'draft',
                    'type' => 'select',
                    'multiOptions' => array(
                        '1' => $this->translate("Submit for review"),
                        '0' => $this->translate("Saved As Draft")
                    ),
                    'value' => 1
                );
            }

            // Search
            if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.show.browse', 1)) {
                $createForm[] = array(
                    'type' => 'Checkbox',
                    'name' => 'search',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Show this Page on browse page and in various blocks."),
                    'value' => 1,
                );
            }

            if (isset($action) && !empty($action))
                $label = 'Save';
            else
                $label = 'Create';

            $createForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($label),
                'type' => 'Submit',
                'name' => 'submit'
            );

            $responseForm['form'] = $createForm;
            $responseForm['fields'] = $fieldsArrayMaps;

            if (isset($repeatForm) && !empty($repeatForm))
                $responseForm['repeatOccurences'] = $repeatForm;

            $responseForm['subCategories'] = $subcategoryData;

            return $responseForm;
        } catch (Exception $ex) {
            
        }
    }

    /**
     *
     * Message owner form
     *
     * @return array
     */
    public function getMessageOwnerForm() {
        $message = array();

        // Init title
        $message[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => $this->translate('Subject'),
            'hasValidators' => 'true'
        );

        // Init body - plain text
        $message[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => $this->translate('Message'),
        );

        $message[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Send Message'),
        );
        return $message;
    }

    /**
     * Enables video plugin
     *
     * 
     * @return boolean
     */
    public function enableVideoPlugin() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.show.video', 1)) {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
        } else {
            return 1;
        }
    }

    /**
     * Enables Notes plugin
     *
     * 
     * @return boolean
     */
    public function enableNotesPlugin() {
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.show.note', 1)) {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('note');
        } else {
            return 1;
        }
    }

    /**
     * Gets the Profile Types of a Page Based on category
     *
     * @param array object of profilefieldmaps
     *
     * @return array
     */
    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitepage_page');

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('sitepage_page');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.         
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }
        return $profileFields;
    }

    /**
     * 	Gets the profile fields for the directory page based on category
     *
     * 	@param array fieldsform
     * 	@return array
     */
    private function _getProfileFields($fieldsForm = array()) {
        $fieldsForm = array();
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitepage_page');
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


                    // Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

                        // In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
                            // If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
//                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    /**
     * Tell a friend Form
     *
     * @return array
     */
    public function getTellAFriendForm() {
        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
            'hasValidator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
            'has Validator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'receiver_emails',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate multiple addresses with commas'),
            'hasValidators' => 'true'
        );

        $tell[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can send a personal note in the mail.'),
            'hasValidator' => 'true',
        );

        $tell[] = array(
            'type' => 'Checkbox',
            'name' => 'send_me',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send a copy to my email address."),
        );


        $tell[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tell a Friend'),
        );

        $response = array();
        $response['form'] = $tell;
        return $response;
    }

    /**
     * Gets claim a page form
     *
     * @return array
     *
     */
    public function getClaimForm() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'terms'), 'sitepage_claimpages', true);
        $response = array();
        $response['info'] = array();
        $response['info']['title'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Claim a Page');
        $response['info']['description'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Below, you can file a claim for a page on this community that you believe should be owned by you. Your request will be sent to the administration.');
        $response['form'] = array();

        // claimer name
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'nickname',
            'label' => $this->translate('Your Name'),
            'has Validator' => 'true'
        );

        // claimer email
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'email',
            'label' => $this->translate('Your Email'),
            'has Validator' => 'true'
        );

        // claimer about
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'about',
            'label' => $this->translate('About you and the page'),
            'has Validator' => 'true'
        );

        // claimer contactno
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'contactno',
            'label' => $this->translate('Your Contact number.'),
            'has Validator' => 'true'
        );

        // claimer comments
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'usercomments',
            'label' => $this->translate('Comments'),
        );


        $description = $description = sprintf(Zend_Registry::get('Zend_Translate')->_("I have read and agree to the <a href='javascript:void(0);' onclick=window.open('%s','mywindow','width=500,height=500')>terms of service</a>."), $url);


        $response['form'][] = array(
            'type' => 'checkbox',
            'name' => 'terms',
            'label' => $this->translate('Terms of Service'),
            'description' => $description,
            'hasValidator' => true,
        );

        $response['form'][] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => $this->translate('Send'),
        );

        return $response;
    }

    public function getInfoFields($sitepage) {
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        return $this->getProfileInfo($sitepage);
    }

    /**
     * Get information of sitepage
     *
     * @param sitepage object
     * @return array 
     */
    public function getInformation($sitepage) {
        $information['profile_information'] = $this->getInfoFields($sitepage);

        $basicinfoarray = array();
        $basicinfoarray[$this->translate('Posted By:')] = array('link' => $sitepage->getParent()->getHref(), 'name' => $sitepage->getParent()->getTitle());
        $basicinfoarray[$this->translate('Posted:')] = $this->translate(gmdate('M d, Y', strtotime($sitepage->creation_date)));
        $basicinfoarray[$this->translate('Last Updated:')] = $this->translate(gmdate('M d, Y', strtotime($sitepage->modified_date)));

        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
        if (!empty($sitepage->member_count))
            $basicinfoarray[$this->translate('Members')] = $sitepage->member_count;

        $basicinfoarray[$this->translate('Views:')] = $sitepage->view_count;
        $basicinfoarray[$this->translate('Likes:')] = $sitepage->like_count;

        if (!empty($sitepage->follow_count))
            $basicinfoarray[$this->translate('Followers:')] = $sitepage->follow_count;

        // Category 
        $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitepage');
        if ($sitepage->category_id) {
            $categoriesNmae = $tableCategories->getCategory($sitepage->category_id);
            if (!empty($categoriesNmae->category_name)) {
                $category_name = $categoriesNmae->category_name;
            }

            if ($sitepage->subcategory_id) {
                $subcategory_name = $tableCategories->getCategory($sitepage->subcategory_id);
                if (!empty($subcategory_name->category_name)) {
                    $subcategory_name = $subcategory_name->category_name;
                }

                // Get sub-sub category
                if ($sitepage->subsubcategory_id) {
                    $subsubcategory_name = $tableCategories->getCategory($sitepage->subsubcategory_id);
                    if (!empty($subsubcategory_name->category_name)) {
                        $subsubcategory_name = $subsubcategory_name->category_name;
                    }
                }
            }
        }

        $categoryData = "";
        if ($category_name != '') {
            // $category = $this->htmlLink($this->url(array('category_id' => $sitepage->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($category_name)), 'sitepage_general_category'), $this->translate($category_name));
            $categoryData = $category_name;
            if ($subcategory_name != '') {
                // $subcategory = $this->htmlLink($this->url(array('category_id' => $sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($category_name)), 'sitepage_general_subcategory'), $this->translate($subcategory_name));
                $categoryData .= " >> " . $subcategory_name;
            }
            if ($subsubcategory_name != '') {
                // $subsubcategory = $this->htmlLink($this->url(array('category_id' => $sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($category_name)), 'sitepage_general_subsubcategory'), $this->translate($subsubcategory_name));
                $categoryData .= " >> " . $subsubcategory_name;
            }
        }

        if (strlen($categoryData) > 1)
            $basicinfoarray[$this->translate('Category:')] = $categoryData;

        $sitepagetags = $sitepage->tags()->getTagMaps();
        $tagsData = "";
        if (count($sitepagetags) > 0) {
            $tagcount = 0;
            foreach ($sitepageTags as $tag) {
                $tagsData .= " #" . $tag->getTag()->text;
            }
        }

        if (strlen($tagsData) > 1)
            $basicinfoarray[$this->translate('Tags:')] = $tagsData;

        $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);

        if ($enablePrice && $sitepage->price) {
            $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyObj = new Zend_Currency($currency);
            $basicinfoarray[$this->translate('Price:')] = $currencyObj->toCurrency($sitepage->price);
        }

        $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
        if ($enableLocation && $sitepage->location)
            $basicinfoarray[$this->translate('Location:')] = $sitepage->location;

        $basicinfoarray[$this->translate('Description:')] = $sitepage->body;
        $information['basic_information'] = $basicinfoarray;
        return $information;
    }

    /**
     * Gets default profile ids
     *
     * @param sitepage object
     * return array
     */
    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }
        if (empty($getFieldId)) {
            return;
        }

        return $getFieldId;
    }

    /**
     * Get the Profile Fields Information, which will show on profile page.
     *
     * @param sitepage object , setkeyasresponse boolean
     * @return array
     */
    public function getProfileInfo($subject, $setKeyAsResponse = false) {
        // Getting the default Profile Type id.
        $getFieldId = $this->getDefaultProfileTypeId($subject);
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);
        $fieldValues = array();
        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {
            // Set the default profile type.
            // $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            // $_getProfileFields = $this->_getProfileFields();
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields();
            $specificProfileFields[$getFieldId] = $_getProfileFields[$getFieldId];
            foreach ($specificProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

                            // In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields($getType);

            foreach ($_getProfileFields as $value) {
                $key = $value['name'];
                $label = $value['label'];
                $parts = @explode('_', $key);

                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $subject->getIdentity()
                ));

                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {
                        if (!empty($fieldRow->value)) {
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $fieldRow->value;
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }

// 	public function getReviewCreateForm($widgetSettingsReviews) {
// 		//GET VIEWER INFO
// 		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
// 		//GET EVENT ID
// 		$getItemPage = $widgetSettingsReviews['item'];
// 		$sitepagereview_proscons = $widgetSettingsReviews['settingsReview']['sitepagereview_proscons'];
// 		$sitepagereview_limit_proscons = $widgetSettingsReviews['settingsReview']['sitepagereview_limit_proscons'];
// 		$sitepagereview_recommend = $widgetSettingsReviews['settingsReview']['sitepagereview_recommend'];
// 		if ($sitepagereview_proscons) {
// 			if ($sitepagereview_limit_proscons) {
// 				$createReview[] = array(
// 					'type' => 'Textarea',
// 					'name' => 'pros',
// 					'label' => $this->translate('Pros'),
// 					'description' => $this->translate("What do you like about this Page?"),
// 					'hasValidator' => 'true'
// //                   
// 				);
// 			} else {
// 				$createReview[] = array(
// 					'type' => 'Textarea',
// 					'name' => 'pros',
// 					'label' => $this->translate->translate('Pros'),
// 					'description' => $this->translate("What do you like about this Page?"),
// 					'hasValidator' => 'true',
// 				);
// 			}
// 			if ($sitepagereview_limit_proscons) {
// 				$createReview[] = array(
// 					'type' => 'Textarea',
// 					'name' => 'cons',
// 					'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
// 					'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Page?"),
// 					'hasValidator' => 'true',
// 				);
// 			} else {
// 				$createReview[] = array(
// 					'type' => 'Textarea',
// 					'name' => 'cons',
// 					'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
// 					'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Page?"),
// 					'hasValidator' => 'true',
// 				);
// 			}
// 		}
// 		$createReview[] = array(
// 			'type' => 'Textarea',
// 			'name' => 'title',
// 			'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('One-line summary'),
// 		);
// //
// //        $profileTypeReview = $this->getProfileTypeReview();
// //        if (!empty($profileTypeReview)) {
// //            
// //            $customFields = $this->getSiteeventFormCustomStandard(array(
// //                'item' => 'siteevent_review',
// //                'topLevelId' => 1,
// //                'topLevelValue' => $profileTypeReview,
// //                'decorators' => array(
// //                    'FormElements'
// //            )));
// //
// //            $customFields->removeElement('submit');
// //
// //            $this->addSubForms(array(
// //                'fields' => $customFields
// //            ));
// //        }
// 		$createReview[] = array(
// 			'type' => 'Textarea',
// 			'name' => 'body',
// 			'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
// 		);
// 		if ($sitepagereview_recommend) {
// 			$createReview[] = array(
// 				'type' => 'Radio',
// 				'name' => 'recommend',
// 				'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recommended'),
// 				'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend this Page to a friend?")),
// 				'multiOptions' => array(
// 					1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Yes'),
// 					0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('No')
// 				),
// 			);
// 		}
// 		$createReview[] = array(
// 			'type' => 'Submit',
// 			'name' => 'submit',
// 			'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
// 		);
// 		return $createReview;
// 	}
// 	public function getReviewUpdateForm() {
// 		$updateReview[] = array(
// 			'type' => 'Textarea',
// 			'name' => 'body',
// 			'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
// 		);
// 		$updateReview[] = array(
// 			'type' => 'Submit',
// 			'name' => 'submit',
// 			'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add your Opinion'),
// 		);
// 		return $updateReview;
// 	}


    public function getcommentForm($type, $id) {
        $commentform = array();
        $commentform[] = array(
            'type' => "text",
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment'),
        );
    }

    public function setPhoto($photo, $subject, $needToUplode = false, $params = array()) {
        try {

            if ($photo instanceof Zend_Form_Element_File) {
                $file = $photo->getFileName();
            } else if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else if (is_string($photo) && file_exists($photo)) {
                $file = $photo;
            } else {
                throw new Group_Model_Exception('invalid argument passed to setPhoto');
            }
        } catch (Exception $e) {
            
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'siteevent_event',
            'parent_id' => $subject->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        if (empty($needToUplode)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->photo_id = $iMain->file_id;
            $subject->save();
        }

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
        if (isset($params['album_id']) && !empty($params['album_id'])) {
            $album = Engine_Api::_()->getItem('sitepage_album', $params['album_id']);
            if (!$album->toArray()) {
                $album = $subject->getSingletonAlbum(0);
                $album->owner_id = $viewer->getIdentity();
                $album->save();
            }
        } else {
            $album = $subject->getSingletonAlbum(0);
            $album->owner_id = $viewer->getIdentity();
            $album->save();
        }

        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'page_id' => $subject->getIdentity(),
            'album_id' => $album->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $album->getIdentity()
        ));
        $photoItem->save();

        return $subject;
    }

    /**
     * Gutter menu show on the sitepage manage/profile page.
     * 
     * @return array
     */
    public function gutterMenus($subject, $action = 'view') {

        $viewer = Engine_Api::_()->user()->getViewer();

        $menus = array();
        $viewer_id = $viewer->getIdentity();

        // EDIT PAGE DETAILS DASHBOARD
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'name' => 'edit',
                'label' => $this->translate('Edit Page Details'),
                'url' => 'sitepage/edit/' . $subject->getIdentity(),
            );
        }


        // Share Page
        if (!empty($viewer_id) && ($action == 'view')) {
            $menus[] = array(
                'name' => 'share',
                'label' => $this->translate('Share This Page'),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

         $showMessageOwner = 'none';
        //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
        if (isset($viewer->level_id) && !empty($viewer->level_id) && ($action == 'view')) {
            $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
            $canSendMessage =0;
            $owner = $subject->getOwner();
            if($showMessageOwner == 'friends' && $owner->membership()->isMember($viewer)){
             $canSendMessage = 1;

            }
            if($showMessageOwner == 'everyone'){
             $canSendMessage = 1;
            }


            //SHOW IF AUTHORIZED
            if ($subject->owner_id !== $viewer_id && !empty($viewer_id) && !empty($canSendMessage)) {

                if (!empty($viewer_id)) {

                    $menus[] = array(
                        'name' => 'messageowner',
                        'label' => $this->translate('Message Owner'),
                        'url' => 'sitepage/messageowner/' . $subject->getIdentity()
                    );
                }
            }
        }

        //TELL A FRIEND
        if (($action == 'view')) {
            $menus[] = array(
                'name' => 'tellafriend',
                'label' => $this->translate('Tell a friend'),
                'url' => 'sitepage/tellafriend/' . $subject->getIdentity()
            );
        }

        // write a review
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && $viewer_id) {
            $hasPostedReview = Engine_Api::_()->getDbtable('reviews', 'sitepagereview')->canPostReview($subject->getIdentity(), $viewer_id);
            if ($hasPostedReview) {
                $menus[] = array(
                    'name' => 'update_review',
                    'label' => $this->translate('Update Review'),
                    'url' => "sitepage/review/edit/" . $subject->getIdentity() . "/" . $hasPostedReview,
                );
            } else if ($viewer_id != $subject->owner_id && $viewer_id) {
                $menus[] = array(
                    'name' => 'create_review',
                    'label' => $this->translate('Write a Review '),
                    'url' => "sitepage/reviews/create/" . $subject->getIdentity(),
                );
            }
        }

        // get follow and unfollow

        if ($viewer_id != $subject->getOwner()->getIdentity() && $viewer_id) {
            $followsData = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollow($subject, $viewer);

            if ($followsData) {
                $menus[] = array(
                    'label' => $this->translate("Following"),
                    'name' => 'unfollow',
                    'url' => 'sitepage/follow/' . $subject->getIdentity(),
                );
            } else {
                $menus[] = array(
                    'label' => $this->translate("Follow"),
                    'name' => 'follow',
                    'url' => 'sitepage/follow/' . $subject->getIdentity(),
                );
            }


//            $membership = Engine_Api::_()->getDbTable('membership', 'sitepage')->getJoinMembers($subject->getIdentity() , $viewer);
//            if(!$membership)
//            {
//                $menus[] = array(
//                    'label' => $this->translate('Join Page'),
//                    'name' => 'join_page',
//                    'url' => 'sitepage/join/'.$subject->getIdentity(),
//                );
//            }
//            else
//            {
//                $menus[] = array(
//                    'label' => $this->translate('Leave Page'),                    
//                    'name' => 'leave_page',
//                    'url' => 'sitepage/join/'.$subject->getIdentity(),
//                );
//            }
        }

        // package work
//        if(Engine_Api::_()->sitepage()->canShowPaymentLink($subject->page_id))
//        {
//            $menus[] = array(
//                'name' => 'payment',
//                'label' => $this->translate('Payment page'),
//                'url' => 'sitepage/gateway/'.$subject->getIdentity(),
//            );
//        }
        // notification settings
        // $menus[] = array(
        //     'name' => 'notification',
        //     'label' => $this->translate('Notification Setting'),
        //     'url' => 'sitepage/notification-settings'
        // );
        // PUBLISH PAGE
        if ($sitepage->draft == 1 && ($viewer_id == $subject->owner_id)) {
            $menus[] = array(
                'name' => 'publish',
                'label' => $this->translate('Publish Page'),
                'url' => 'sitepage/publish/' . $subject->getIdentity(),
            );
        }

        // claim this page 
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'claim') && ($action == 'view')) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.claimlink', 1)) {
                $listmemberclaimsTable = Engine_Api::_()->getDbtable('listmemberclaims', 'sitepage');
                $listmemberclaimsTablename = $listmemberclaimsTable->info('name');
                $select = $listmemberclaimsTable->select()->from($listmemberclaimsTablename, array('count(*) as total_count'))
                        ->where('user_id = ?', $subject->owner_id);
                $row = $listmemberclaimsTable->fetchAll($select);

                if (isset($row[0]['total_count']) && !empty($row[0]['total_count'])) {
                    $total_count = 1;
                }

                if ($total_count && $subject->owner_id != $viewer_id && !empty($subject->userclaim)) {
                    $menus[] = array(
                        'name' => 'claim',
                        'label' => $this->translate('Claim Page'),
                        'url' => 'sitepage/claim/' . $subject->getIdentity(),
                    );
                }
            }
        }

        if ($action != "manage") {
            // CLOSE / OPEN AN Page
            if ($viewer->getIdentity() && $subject->closed != 1 && $subject->authorization()->isAllowed($viewer, 'edit')) {
                $menus[] = array(
                    'name' => 'close',
                    'label' => $this->translate('Close Page'),
                    'url' => 'sitepage/close/' . $subject->getIdentity(),
                );
            }

            // CLOSE / OPEN AN PAGE
            if ($viewer->getIdentity() && $subject->closed == 1 && $subject->authorization()->isAllowed($viewer, 'edit')) {
                $menus[] = array(
                    'name' => 'open',
                    'label' => $this->translate('Open Page'),
                    'url' => 'sitepage/close/' . $subject->getIdentity(),
                );
            }
        }


        // CLOSE / OPEN AN PAGE
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'name' => 'delete',
                'label' => $this->translate('Delete Page'),
                'url' => 'sitepage/delete/' . $subject->getIdentity(),
            );
        }

        if (!empty($viewer_id) && ($action == 'view')) {
            $menus[] = array(
                'name' => 'report',
                'label' => $this->translate('Report This Page'),
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        $isEnabledPackage = Engine_Api::_()->sitepage()->hasPackageEnable();
        $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");
        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
            if (Engine_Api::_()->sitepage()->canShowPaymentLink($subject->page_id)) {
                $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlP', "pageitems");
                $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
                $menus[] = array(
                    'label' => $this->translate('Make Payment'),
                    'name' => 'package_payment',
                    'url' => $getHost . '/' . $baseUrl . "/" . $routeStartP . "/payment?token=" . $getOauthToken['token'] . "&page_id=" . $subject->page_id . "&disableHeaderAndFooter=1"
                );
            }
        }

        $owner = $subject->getOwner();
        $owner_id = $owner->getIdentity();
        if (!empty($isEnabledPackage) && !empty($viewer_id) && !empty($owner_id) && $owner_id == $viewer_id && (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.8.7') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.8.0'))) {
            $menus[] = array(
                'label' => $this->translate('Upgrade Package'),
                'name' => 'upgrade_package',
                'url' => 'sitepages/upgrade-package',
                'urlParams' => array(
                    'page_id' => $subject->page_id
                )
            );
        }

        //Advanced video integration..........................
        if (method_exists('Siteapi_Api_Core','isSitevideoPluginEnabled') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '3.2.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION > '2.1.6') && $action == 'view') {
            $advVideoEnableArray = Engine_Api::_()->getApi('Core', 'siteapi')->isSitevideoPluginEnabled($subject->getType(), $subject->getIdentity());
            if (isset($advVideoEnableArray['sitevideoPluginEnabled']) && !empty($advVideoEnableArray['sitevideoPluginEnabled'])) {
                if (isset($advVideoEnableArray['canCreateVideo']) && !empty($advVideoEnableArray['canCreateVideo']))
                    $menus[] = array(
                        'name' => 'videoCreate',
                        'label' => $this->translate('Add Video'),
                        'url' => 'advancedvideos/create',
                        "subject_type" => $subject->getType(),
                        "subject_id" => $subject->getIdentity(),
                        'urlParams' => array(
                            "subject_type" => $subject->getType(),
                            "subject_id" => $subject->getIdentity(),
                            )
                    );
            }
        }
        
        $eventModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
        if (!empty($eventModuleEnabled) && method_exists('Siteevent_Api_Siteapi_Core', 'isSiteeventPluginEnabled') && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '55.5') || _CLIENT_TYPE == 'ios' && _IOS_VERSION > '2.4') && $action == 'view') {
            $advEventEnableArray = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->isSiteeventPluginEnabled($subject->getType(), $subject->getIdentity());
            if (isset($advEventEnableArray['siteeventPluginEnabled']) && !empty($advEventEnableArray['siteeventPluginEnabled'])) {
                if (isset($advEventEnableArray['canCreateEvent']) && !empty($advEventEnableArray['canCreateEvent'])){
                    if( _CLIENT_TYPE == 'ios' ) {
                        $menus[] = array(
                            'name' => 'create',
                            'label' => $this->translate('Create an Event'),
                            'url' => 'advancedevents/create',
                            'urlParams' => array(
                            "subject_type" => $subject->getType(),
                            "subject_id" => $subject->getIdentity(),
                            )
                        );
                    }
                    else{
                        $packageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
                        if(!empty($packageEnable))
                        {   $menus[] = array(
                                    'label' => $this->translate('Create an Event'),
                                    'name' => 'content_create_package',
                                    'url' => 'advancedevents/packages',
                                    'urlParams' => array(
                                    "subject_type" => $subject->getType(),
                                    "subject_id" => $subject->getIdentity(),
                                )
                            );
                        }
                        else{
                            $menus[] = array(
                                    'name' => 'create',
                                    'label' => $this->translate('Create an Event'),
                                    'url' => 'advancedevents/create',
                                    'urlParams' => array(
                                    "subject_type" => $subject->getType(),
                                    "subject_id" => $subject->getIdentity(),
                                )
                            );
                        }
                    }
                }  
            }
        }

        return $menus;
    }

    /*
     * Notofication setting form 
     *
     */
    public function getNotificationSettings($subject) {
        $form = array();

        $form[] = array(
            'name' => 'email',
            'type' => 'checkbox',
            'label' => $this->translate('Send email notifications to me when people post an update, or create various content on this Page.'),
        );

        $form[] = array(
            'name' => 'action_email',
            'label' => $this->translate('Select the options that you want to be recive notification when any member post, comment, like, follow and create content.'),
            'type' => 'MultiCheckbox',
            'multiOptions' => array(
                'posted' => 'People post updates on this page',
                'created' => 'People create various contents on this page'
            ),
        );
    }

    /**
     * Get page count based on category
     *
     * @param int $id
     * @param string $column_name
     * @param int $authorization
     * @return pages count
     */
    public function getPagesCount($id, $column_name, $foruser = null) {

        //MAKE QUERY
        $tableSitepage = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $select = $tableSitepage->select()
                ->from($tableSitepage->info('name'), array('COUNT(*) AS count'));

        if (!empty($foruser)) {
            $select->where('closed = ?', 0)
                    ->where('approved = ?', 1)
                    ->where('draft = ?', 1)
                    ->where('search = ?', 1);

            $select = $tableSitepage->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        if (!empty($column_name) && !empty($id)) {
            $select->where("$column_name = ?", $id);
        }
        $totalPages = $select->query()->fetchColumn();

        //RETURN PAGES COUNT
        return $totalPages;
    }

    /*
     * get photos count
     */
    private function countTotalPhotos($page_id) {
        $paramsAlbum = array();
        $paramsAlbum['page_id'] = $page_id;

        // Get album count
        $album_count = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);
        return $album_count;
    }

    /**
     * Gets all categories and subcategories
     *
     * @param string $category_id
     * @param string $fieldname
     * @param int $siteeventCondition
     * @param string $siteevent
     * @param  all categories and subcategories
     */
    public function getCategorieshaspages($category_id = null, $fieldname, $limit = null, $params = array(), $fetchColumns = array()) {

        //GET CATEGORY TABLE NAME
        $tableCategoriesName = Engine_Api::_()->getDbtable('categories', 'sitepage')->info('name');

        //GET EVENTS TABLE
        $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $tablePagesName = $tablePage->info('name');

        //MAKE QUERY
        $select = Engine_Api::_()->getDbtable('categories', 'sitepage')->select()->setIntegrityCheck(false);


        if (!empty($fetchColumns)) {
            $select->from($tableCategoriesName, $fetchColumns);
        } else {
            $select->from($tableCategoriesName);
        }



        $select = $select->join($tablePagesName, $tablePagesName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', null);

        $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
                ->group($tableCategoriesName . '.category_id')
                ->order('cat_order');



        if (!empty($limit)) {
            $select = $select->limit($limit);
        }

        $select->where($tablePagesName . '.approved = ?', 1)->where($tablePagesName . '.draft = ?', 0)->where($tablePagesName . '.search = ?', 1)->where($tablePagesName . '.closed = ?', 0);



        $select = $tablePage->getNetworkBaseSql($select, array('not_groupBy' => 1));

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //  $latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";




            $select->join($locationTableName, "$tablePagesName.event_id = $locationTableName.page_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            $select->order("distance");
            // $select->group("$tablePagesName.page_id");
        }
        // RETURN DATA
        return Zend_Paginator::factory($select);
    }

    /**
     * Translate the text from english to specified language by user
     *
     * 	@param message string
     *   @return string
     */
    private function translate($message) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }

    public function getCategories($home_page_display = 0) {
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $cateName = $tableCategory->info('name');
        $select = $tableCategory->select()->where('cat_dependency =?', 0)->order('cat_order');
        if (!empty($home_page_display)) {
            $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
            $rName = $table->info('name');
            $select->setIntegrityCheck(false)
                    ->from($cateName)
                    ->joinLeft($rName, $rName . '.category_id = ' . $cateName . '.category_id', array('count(' . $rName . '.category_id ) as count'))
                    ->group($cateName . '.category_id')
                    ->order('cat_order ASC');
        }
        return Zend_Paginator::factory($select);
    }

    public function getSubCategories($category_id) {
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitepage');
        if (empty($category_id)) {
            return;
        }
        $select = $tableCategory->select()->from($tableCategory->info('name'), array('category_name', 'category_id', 'cat_order', 'cat_dependency'))->where('cat_dependency = ?', $category_id)->order('cat_order');
        return $tableCategory->fetchAll($select);
    }

}
