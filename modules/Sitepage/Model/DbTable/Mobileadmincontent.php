<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Writes.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Mobileadmincontent extends Engine_Db_Table {

    protected $_serializedColumns = array('params');

    /**
     * Gets name
     *
     * @param int $tab_main
     * @return name
     */
    public function getCurrentTabName($tab_main = null) {
        if (empty($tab_main)) {
            return;
        }
        $current_tab_name = $this->select()
                ->from($this->info('name'), array('name'))
                ->where('mobileadmincontent_id = ?', $tab_main)
                ->query()
                ->fetchColumn();
        return $current_tab_name;
    }

    /**
     * Gets content_id, name
     *
     * @param int $contentpage_id
     * @param int $name
     * @return content_id, name
     */
    public function getContentByWidgetName($name, $page_id) {
        $select = $this->select()->from($this->info('name'), array('mobileadmincontent_id', 'name'))
                ->where('name =?', $name)
                ->where('page_id = ?', $page_id)
                ->limit(1);
        return $this->fetchAll($select)->toarray();
    }

    /**
     * Gets content_id
     *
     * @param int $contentpage_id
     * @return $params
     */
    public function getContentId($mobilecontentpage_id, $sitepage) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = 0;
        }
        $itemAlbumCount = 10;
        $itemPhotoCount = 100;
        $select = $this->select();
        $select_content = $select
                ->from($this->info('name'))
                ->where('page_id = ?', $mobilecontentpage_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (!empty($content)) {
            $select = $this->select();
            $select_container = $select
                    ->from($this->info('name'), array('mobileadmincontent_id'))
                    ->where('page_id = ?', $mobilecontentpage_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where("name NOT IN ('	sitepage.title-sitepage', 'seaocore.like-button', 'sitepage.photorecent-sitepage')")
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
                $select = $this->select();
                $container_id = $container[0]['mobileadmincontent_id'];
                $select_middle = $select
                        ->from($this->info('name'))
                        ->where('parent_content_id = ?', $container_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitemobile.container-tabs-columns')
                        ->where('page_id = ?', $mobilecontentpage_id)
                        ->limit(1);
                $middle = $select_middle->query()->fetchAll();
                if (!empty($middle)) {
                    $mobilecontent_id = $middle[0]['mobileadmincontent_id'];
                } else {
                    $mobilecontent_id = $container_id;
                }
            }
        }

        if (!empty($mobilecontent_id)) {
            $select = $this->select();
            $select_middle = $select
                    ->from($this->info('name'), array('mobileadmincontent_id', 'name', 'params'))
                    ->where('parent_content_id = ?', $content_id)
                    ->where('type = ?', 'widget')
                    ->where("name NOT IN ('sitepage.title-sitepage', 'seaocore.like-button', 'sitepage.photorecent-sitepage', 'Facebookse.facebookse-sitepageprofilelike', 'sitepage.thumbphoto-sitepage')")
                    ->where('page_id = ?', $mobilecontentpage_id)
                    ->order('order')
            ;

            $select = $this->select();
            $select_photo = $select
                    ->from($this->info('name'), array('params'))
                    ->where('parent_content_id = ?', $mobilecontent_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitepage.photos-sitepage')->where('page_id = ?', $mobilecontentpage_id)
                    ->order('mobileadmincontent_id ASC');

            $middlePhoto = $select_photo->query()->fetchColumn();
            if (!empty($middlePhoto)) {
                $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
                if (isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray)) {
                    $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
                }
                if (isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray)) {
                    $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
                }
            }
            $middle = $select_middle->query()->fetchAll();
            $editpermission = '';
            $isManageAdmin = '';
            $content_ids = '';
            $content_names = '';
            $resource_type_integration = 0;
            $ads_display_integration = 0;
            $flag = false;
            foreach ($middle as $value) {
                $content_name = $value['name'];
                switch ($content_name) {
                    case 'sitepage.overview-sitepage':
                        if (!empty($sitepage)) {
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
                            if (!empty($isManageAdmin)) {
                                $editpermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                                if (!empty($editpermission) && empty($sitepage->overview)) {
                                    $flag = true;
                                } elseif (empty($editpermission) && empty($sitepage->overview)) {
                                    $flag = false;
                                } elseif (!empty($editpermission) && !empty($sitepage->overview)) {
                                    $flag = true;
                                } elseif (empty($editpermission) && !empty($sitepage->overview)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'sitepage.location-sitepage':
                        if (!empty($sitepage)) {
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
                            if (!empty($isManageAdmin)) {
                                $value['id'] = $sitepage->getIdentity();
                                $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
                                if (!empty($location)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'core.html-block':
                        $flag = true;
                        break;
                    case 'activity.feed':
                        $flag = true;
                        break;
                    case 'seaocore.feed':
                        $flag = true;
                        break;
                    case 'advancedactivity.home-feeds':
                        $flag = true;
                        break;
                    case 'sitepage.info-sitepage':
                        $flag = true;
                        break;
                    case 'core.profile-links':
                        $flag = true;
                        break;
                    case 'sitepagenote.profile-sitepagenotes':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL NOTES
                            $noteCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagenote', 'notes');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
                            if (!empty($isManageAdmin) || !empty($noteCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepageevent.profile-sitepageevents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL EVENTS
                            $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageevent', 'events');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
                            if (!empty($isManageAdmin) || !empty($eventCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'siteevent.contenttype-events':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL EVENTS
                            $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'siteevent', 'events');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
                            if (!empty($isManageAdmin) || !empty($eventCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepage.discussion-sitepage':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL TOPICS
                            $topicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'topics');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
                            if (!empty($isManageAdmin) || !empty($topicCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepage.photos-sitepage':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL ALBUMS
                            $albumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
                            if (!empty($isManageAdmin) || !empty($albumCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagemusic.profile-sitepagemusic':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemusic") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PLAYLISTS
                            $musicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagemusic', 'playlists');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smcreate');
                            if (!empty($isManageAdmin) || !empty($musicCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                    case 'sitepagemember.profile-sitepagemembers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PLAYLISTS
                            $memberCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagem', 'membership');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smecreate');
                            if (!empty($isManageAdmin) || !empty($memberCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                    case 'sitepagedocument.profile-sitepagedocuments':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL DOCUMENTS
                            $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagedocument', 'documents');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
                            if (!empty($isManageAdmin) || !empty($documentCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagereview.profile-sitepagereviews':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                            //TOTAL REVIEW
                            $reviewCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagereview', 'reviews');
                            $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepagereview_review', 'create');
                            if (!empty($level_allow) || !empty($reviewCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagevideo.profile-sitepagevideos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL VIDEO
                            $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagevideo', 'videos');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
                            if (!empty($isManageAdmin) || !empty($videoCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                         case 'document.contenttype-documents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL DOCUMENTS
                            $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'document', 'documents');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
                            if (!empty($isManageAdmin) || !empty($documentCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitevideo.contenttype-videos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL VIDEOS
                            $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitevideo', 'videos');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
                            if (!empty($isManageAdmin) || !empty($videoCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagepoll.profile-sitepagepolls':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL POLL
                            $pollCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagepoll', 'polls');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
                            if (!empty($isManageAdmin) || !empty($pollCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepageoffer.profile-sitepageoffers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageoffer") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'offer');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL OFFERS
                            $can_edit = 1;
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                            if (empty($isManageAdmin)) {
                                $can_edit = 0;
                            }

                            $can_offer = 1;
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'offer');

                            if (empty($isManageAdmin)) {
                                $can_offer = 0;
                            }

                            $can_create_offer = '';

                            //OFFER CREATION AUTHENTICATION CHECK
                            if ($can_edit == 1 && $can_offer == 1) {
                                $can_create_offer = 1;
                            }

                            //TOTAL OFFER
                            $offerCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageoffer', 'offers');
                            if (!empty($can_create_offer) || !empty($offerCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepageform.sitepage-viewform':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageform") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'form');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'sitepageintegration.profile-items':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
                            $content_params = $value['params'];
                            $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
                            $resource_type_integration = $paramsDecodedArray['resource_type'];
                            $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);

                            //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", $resource_type_integration)) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, $resource_type_integration);
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
                        }
                        break;
                    case 'sitepagetwitter.feeds-sitepagetwitter':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'twitter');
                            if (!empty($isManageAdmin)) {
                                $flag = true;
                            }
                        }
                        break;
                    case 'sitecrowdfunding.contenttype-projects':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitecrowdfunding") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sprcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PROJECTS
                            $projectCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitecrowdfunding', 'projects');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sprcreate');
                            if (!empty($isManageAdmin) || !empty($projectCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                        case 'sitemusic.contenttype-music':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitemusic") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'samcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL MUSIC ALBUM
                            $musicAlbumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitemusic', 'music');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'samcreate');
                            if (!empty($isManageAdmin) || !empty($musicAlbumCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                }

                if (!empty($flag)) {
                    $content_ids = $value['mobileadmincontent_id'];
                    $content_names = $value['name'];
                    break;
                }
            }
        }

        return array('mobilecontent_id' => $content_ids, 'content_name' => $content_names, 'itemAlbumCount' => $itemAlbumCount, 'itemPhotoCount' => $itemPhotoCount, 'resource_type_integration' => $resource_type_integration, 'ads_display_integration' => $ads_display_integration);
    }

    /**
     * Gets content_id, name
     *
     * @param int $contentpage_id
     * @return content_id, name
     */
    public function getContentInformation($page_id) {
        $select = $this->select()->from($this->info('name'), array('mobileadmincontent_id', 'name'))
                        ->where("name IN ('sitepage.info-sitepage', 'seaocore.feed', 'advancedactivity.home-feeds','activity.feed', 'sitepage.location-sitepage', 'core.profile-links', 'core.html-block')")
                        ->where('page_id = ?', $page_id)->order('mobileadmincontent_id ASC');

        return $this->fetchAll($select);
    }

    public function prepareContentArea($content, $current = null) {

        //GET PARENT CONTENT ID
        $parent_content_id = null;
        if (null !== $current) {
            $parent_content_id = $current->mobileadmincontent_id;
        }

        //GET CHILDREN
        $children = $content->getRowsMatching('parent_content_id', $parent_content_id);
        if (empty($children) && null === $parent_content_id) {
            $children = $content->getRowsMatching('parent_content_id', 0);
        }

        //GET STRUCT
        $struct = array();
        foreach ($children as $child) {
            $elStruct = $this->createElementParams($child);
            $elStruct['elements'] = $this->prepareContentArea($content, $child);
            $struct[] = $elStruct;
        }

        return $struct;
    }

    public function createElementParams($row) {

        $data = array(
            'identity' => $row->mobileadmincontent_id,
            'type' => $row->type,
            'name' => $row->name,
            'order' => $row->order,
        );
        $params = (array) $row->params;
        if (isset($params['title']))
            $data['title'] = $params['title'];
        $data['params'] = $params;
        return $data;
    }

    /**
     * Gets content id,parama,name
     *
     * @param int $contentpage_id
     * @return content id,parama,name
     */
    public function getContents($mobilecontentpage_id) {

        $selectPageAdmin = $this->select()
                ->from($this->info('name'), array('mobileadmincontent_id', 'params', 'name'))
                ->where('page_id =?', $mobilecontentpage_id)
                ->where("name IN ('sitepage.overview-sitepage', 'sitepage.photos-sitepage', 'sitepage.discussion-sitepage', 'sitepagenote.profile-sitepagenotes', 'sitepagepoll.profile-sitepagepolls', 'sitepageevent.profile-sitepageevents', 'sitepagevideo.profile-sitepagevideos', 'sitepageoffer.profile-sitepageoffers', 'sitepagereview.profile-sitepagereviews', 'sitepagedocument.profile-sitepagedocuments', 'sitepageform.sitepage-viewform','sitepage.info-sitepage', 'seaocore.feed','advancedactivity.home-feeds', 'activity.feed', 'sitepage.location-sitepage', 'core.profile-links', 'sitepagemusic.profile-sitepagemusic', 'sitepagemember.profile-sitepagemembers', 'sitepageintegration.profile-items','sitepagetwitter.feeds-sitepagetwitter', 'siteevent.contenttype-events','sitevideo.contenttype-videos', 'sitemobile.sitemobile-advfeed', 'document.contenttype-documents','sitecrowdfunding.contenttype-projects','sitemusic.contenttype-music')");
        return $this->fetchAll($selectPageAdmin);
    }

    /**
     * Set profile page default widget in admin content table
     *
     * @param string $name
     * @param string $contentpage_id
     * @param string $title
     * @param string $titleCount
     * @param string $order
     */
    public function setAdminDefaultInfo($name = null, $page_id, $title = null, $titleCount = null, $order = null, $params = null) {
        $db = Engine_Db_Table::getDefaultAdapter();
        if (!empty($name)) {
            $select = $this->select();
            $select_content = $select
                    ->from($this->info('name'))
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $name)
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = $this->select();
                $select_container = $select
                        ->from($this->info('name'), array('mobileadmincontent_id'))
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $container_id = $container[0]['mobileadmincontent_id'];

                    $select = $this->select();
                    $select_middle = $select
                            ->from($this->info('name'))
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                        $middle_id = $middle[0]['mobileadmincontent_id'];

                        $select = $this->select();
                        $select_tab = $select
                                ->from($this->info('name'))
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'sitemobile.container-tabs-columns')
                                ->where('page_id = ?', $page_id)
                                ->limit(1);
                        $tab = $select_tab->query()->fetchAll();
                        if (!empty($tab)) {
                            $tab_id = $tab[0]['mobileadmincontent_id'];
                        }

                        if ($name != 'sitepageintegration.profile-items') {
                            $contentWidget = $this->createRow();
                            $contentWidget->page_id = $page_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = $name;
                            $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
                            $contentWidget->order = $order;
                            if ($params) {
                                $contentWidget->params = $params;
                            } else {
                                $contentWidget->params = '{"title":"' . $title . '" , "titleCount":' . $titleCount . '}';
                            }
                            $contentWidget->save();
                        } else {
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_modules')
                                    ->where('name = ?', 'sitereview');
                            $check_list = $select->query()->fetchObject();
                            if (!empty($check_list)) {
                                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();

                                foreach ($results as $value) {
                                    $item_title = $value['item_title'];
                                    $resource_type = $value['resource_type'] . '_' . $value['listingtype_id'];

                                    // Check if it's already been placed
                                    $select = new Zend_Db_Select($db);
                                    $select
                                            ->from('engine4_sitepage_mobileadmincontent')
                                            ->where('parent_content_id = ?', $tab_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitepageintegration.profile-items')
                                            ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}');
                                    $info = $select->query()->fetch();
                                    if (empty($info)) {

                                        // tab on profile
                                        $db->insert('engine4_sitepage_mobileadmincontent', array(
                                            'page_id' => $page_id,
                                            'type' => 'widget',
                                            'name' => 'sitepageintegration.profile-items',
                                            'parent_content_id' => $tab_id,
                                            'order' => 999,
                                            'params' => '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}',
                                        ));
                                    }
                                    //}
                                }
                            }

                            $this->settabpageintwidget('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->settabpageintwidget('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);
                        }
                    }
                }
            }
        }
    }

    public function settabpageintwidget($module_name, $params, $middle_id, $page_id) {

        $db = Engine_Db_Table::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', $module_name);
        $module_enable = $select->query()->fetchObject();

        if (!empty($module_enable)) {

            $results = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();

            foreach ($results as $value) {
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_sitepage_mobileadmincontent')
                        ->where('parent_content_id = ?', $middle_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitepageintegration.profile-items')
                        ->where('params = ?', $params);
                $info = $select->query()->fetch();

                if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_sitepage_mobileadmincontent', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitepageintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => $params,
                    ));
                }
            }
        }
    }

}
