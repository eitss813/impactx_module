<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDefaultlayoutController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminDefaultlayoutController extends Core_Controller_Action_Admin {

    //ACTION FOR SETTING THE DEFAULT LAYOUT
    public function indexAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_layoutdefault');

        //FORM
        $this->view->form = $form = new Sitepage_Form_Admin_Layoutdefault();

        //FORM
        if (!Engine_Api::_()->getApi("settings", "core")->getSetting('sitepage.layout.coverphotoenabled', 0)) {
            $this->view->coverForm = $coverForm = new Sitepage_Form_Admin_CoverPhotoLayout();
        }

        //GET PAGE PROFILE PAGE INFO
        $selectPage = Engine_Api::_()->sitepage()->getWidgetizedPage();
        if (!empty($selectPage)) {
            $this->view->page_id = $selectPage->page_id;
        }

        //GET PAGE PROFILE PAGE INFO
        //$selectPage = Engine_Api::_()->sitepage()->getMobileWidgetizedPage();
        if (Engine_Api::_()->sitepage()->getMobileWidgetizedPage()) {
            $this->view->mobile_page_id = Engine_Api::_()->sitepage()->getMobileWidgetizedPage()->page_id;
        }

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) || isset($_GET['page_reload'])) {

            //GET FORM VALUES
            $values = $form->getValues();

            //GET ADMIN CONTENT TABLE
            $contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');

            //GET ADMIN CONTENT TABLE NAME
            $contentTableName = $contentTable->info('name');

            //GET SITEPAGE CONTENT TABLE
            $sitepagecontentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

            //CORE CONTENT TABLE
            $corecontentTable = Engine_Api::_()->getDbtable('content', 'core');

            //GET SITEPAGE CONTENT PAGES TABLE
            $sitepagepageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');

            //GET HIDE PROFILE WODGET TABLE
            $hideprofilewidgetsTable = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitepage');

            //DELETING THE OLD ENTRIES
            $hideprofilewidgets = $hideprofilewidgetsTable->select()->query()->fetchAll();
            if (!empty($hideprofilewidgets)) {
                foreach ($hideprofilewidgets as $data) {
                    $hideprofilewidgetsTable->delete(array('hideprofilewidgets_id =?' => $data['hideprofilewidgets_id']));
                }
            }
            $totalPages = $sitepagepageTable->select()
                            ->from($sitepagepageTable->info('name'), array('count(*) as count'))
                            ->where('name =?', 'sitepage_index_view')->query()->fetchColumn();
            if (isset($_POST['sitepage_sitepage_layout_setting'])) {
                $layout_option = $_POST['sitepage_sitepage_layout_setting'];
            } else {
                $layout_option = $_GET['sitepage_sitepage_layout_setting'];
            }
            $sitepage_layout_cover_photo = 1;
            if (isset($_POST['sitepage_layout_cover_photo'])) {
                $sitepage_layout_cover_photo = $_POST['sitepage_layout_cover_photo'];
            }

            $limit = 300;
            $reload_count = round($totalPages / $limit);
            $page_reload = $this->_getParam('page_reload', 1);
            $offset = ($page_reload - 1) * $limit;

            $selectsitepagePage = $sitepagepageTable->select()
                    ->from($sitepagepageTable->info('name'), array('contentpage_id'))
                    ->where('name =?', 'sitepage_index_view')
                    ->limit($limit, $offset);
            $contentpages_id = $selectsitepagePage->query()->fetchAll();

            foreach ($contentpages_id as $key => $value) {
                if ($value['contentpage_id']) {

                    $sitepagecontentTable->delete(array('contentpage_id =?' => $value['contentpage_id']));
                    if (empty($layout_option)) {
                        Engine_Api::_()->getDbtable('content', 'sitepage')->setWithoutTabLayout($value['contentpage_id'], $sitepage_layout_cover_photo);
                    } else {
                        Engine_Api::_()->getDbtable('content', 'sitepage')->setTabbedLayout($value['contentpage_id'], $sitepage_layout_cover_photo);
                    }
                }
            }

            if ($page_reload == 1) {
                if (!empty($layout_option)) {
                    include_once APPLICATION_PATH . '/application/modules/Sitepage/controllers/AdminviewpagewidgetController.php';
                    if (!empty($selectPage)) {
                        $page_id = $selectPage->page_id;
                        $contentTable->delete(array('page_id =?' => $page_id));
                        Engine_Api::_()->getApi('layoutcore', 'sitepage')->setTabbedLayoutContent($page_id, $sitepage_layout_cover_photo);
                    }

                    if (!empty($page_id)) {
                        //INSERT MAIN CONTAINER
                        $mainContainer = $contentTable->createRow();
                        $mainContainer->page_id = $page_id;
                        $mainContainer->type = 'container';
                        $mainContainer->name = 'main';
                        $mainContainer->order = 2;
                        $mainContainer->save();
                        $container_id = $mainContainer->admincontent_id;

                        //INSERT MAIN-MIDDLE CONTAINER
                        $mainMiddleContainer = $contentTable->createRow();
                        $mainMiddleContainer->page_id = $page_id;
                        $mainMiddleContainer->type = 'container';
                        $mainMiddleContainer->name = 'middle';
                        $mainMiddleContainer->parent_content_id = $container_id;
                        $mainMiddleContainer->order = 6;
                        $mainMiddleContainer->save();
                        $middle_id = $mainMiddleContainer->admincontent_id;

                        //INSERT MAIN-LEFT CONTAINER
                        $mainLeftContainer = $contentTable->createRow();
                        $mainLeftContainer->page_id = $page_id;
                        $mainLeftContainer->type = 'container';
                        $mainLeftContainer->name = 'right';
                        $mainLeftContainer->parent_content_id = $container_id;
                        $mainLeftContainer->order = 4;
                        $mainLeftContainer->save();
                        $left_id = $mainLeftContainer->admincontent_id;
                        $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);

                        //INSERT MAIN-MIDDLE TAB CONTAINER
                        $middleTabContainer = $contentTable->createRow();
                        $middleTabContainer->page_id = $page_id;
                        $middleTabContainer->type = 'widget';
                        $middleTabContainer->name = 'core.container-tabs';
                        $middleTabContainer->parent_content_id = $middle_id;
                        $middleTabContainer->order = 10;
                        $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
                        $middleTabContainer->save();
                        $middle_tab = $middleTabContainer->admincontent_id;

                        //INSERTING THUMB PHOTO WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.thumbphoto-sitepage', $middle_id, 3, '', 'true');

                        if (empty($sitepage_layout_cover_photo)) {

                            //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

                            //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                                Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemember.pagecover-photo-sitepagemembers', $middle_id, 2, '', 'true');
                            }

                            //INSERTING TITLE WIDGET
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.title-sitepage', $middle_id, 4, '', 'true');

                            //INSERTING LIKE WIDGET
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

                            //INSERTING FOLLOW WIDGET
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6, '', 'true');

                            //INSERTING FACEBOOK LIKE WIDGET
                            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
                                Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', $middle_id, 7, '', 'true');
                            }

                            //INSERTING MAIN PHOTO WIDGET
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mainphoto-sitepage', $left_id, 10, '', 'true');
                        } else {
                            //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-cover-information-sitepage', $middle_id, 2, '', 'true');
                        }

                        //INSERTING CONTACT DETAIL WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.contactdetails-sitepage', $middle_id, 8, '', 'true');

//             //INSERTING PHOTO STRIP WIDGET
//             if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
//               Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.photorecent-sitepage', $middle_id, 9, '', 'true');
//             }
                        //INSERTING OPTIONS WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.options-sitepage', $left_id, 11, '', 'true');

                        //INSERTING INFORMATION WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.information-sitepage', $left_id, 10, 'Information', 'true');

                        //INSERTING WRITE SOMETHING ABOUT WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

                        //INSERTING RATING WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', $left_id, 16, 'Ratings', 'true');
                        }


                        //INSERTING BADGE WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', $left_id, 17, 'Badge', 'true');
                        }

                        //INSERTING YOU MAY ALSO LIKE WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.suggestedpage-sitepage', $left_id, 18, 'You May Also Like', 'true');

                        $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

                        //INSERTING SOCIAL SHARE WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.socialshare-sitepage', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//            //INSERTING FOUR SQUARE WIDGET
//            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.foursquare-sitepage', $left_id, 20, '', 'true');
                        //INSERTING INSIGHTS WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 21, 'Insights', 'true');

                        //INSERTING FEATURED OWNER WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 22, 'Owners', 'true');

                        //INSERTING ALBUM WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 23, 'Albums', 'true');
                        }

                        //INSERTING PAGE PROFILE PLAYER WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 24, '', 'true');
                        }

                        //INSERTING LINKED PAGES WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 25, 'Linked Pages', 'true');

                        //INSERTING ACTIVITY FEED WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                            $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
                        } else {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
                        }

                        //INSERTING INFORAMTION WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_tab, 3, 'Info', 'true');

                        //INSERTING OVERVIEW WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.overview-sitepage', $middle_tab, 4, 'Overview', 'true');

                        //INSERTING LOCATION WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.location-sitepage', $middle_tab, 5, 'Map', 'true');

                        //INSERTING LINKS WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

                        //INSERTING ALBUM WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
                        }

                        //INSERTING VIDEO WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
                        }

                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitevideo.contenttype-videos', $page_id, 'Videos', 'true', '117');
                        }

                        //INSERTING NOTE WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
                        }

                        //INSERTING REVIEW WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
                        }

                        //INSERTING FORM WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
                        }

                        //INSERTING DOCUMENT WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
                        }

                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('document.contenttype-documents', $page_id, 'Documents', 'true', '115');
                        }

                        //INSERTING OFFER WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepageoffer.profile-sitepageoffers', $page_id, 'Offers', 'true', '116');
                        }

                        //INSERTING EVENT WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
                        }

                        //INSERTING EVENT WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117');
                        }

                        //INSERTING POLL WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
                        }

                        //INSERTING DISCUSSION WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
                        }

                        //INSERTING MUSIC WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
                        }

                        //INSERTING TWITTER WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
                        }

                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagemember.profile-sitepagemembers', $page_id, 'Member', 'true', '122');
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');
                        }

                        //INSERTING INTEGRATION WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepageintegration.profile-items', $page_id, '', '', '999');
                        }
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '125');
                        }
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitemusic.contenttype-music', $page_id, 'Music', 'true', '126');
                        }
                    }
                } else {
                    //IF EMPTY THEN MAKE PAGE PROFILE PAGE
                    if (empty($selectPage)) {
                        $pageCreate = $pageTable->createRow();
                        $pageCreate->name = 'sitepage_index_view';
                        $pageCreate->displayname = 'Page Profile';
                        $pageCreate->title = 'Page Profile';
                        $pageCreate->description = 'This is the page view  page.';
                        $pageCreate->custom = 1;
                        $current_page_id = $pageCreate->save();
                    } else {
                        $current_page_id = $selectPage->page_id;
                    }

                    if (!empty($current_page_id)) {
                        $corecontentTable->delete(array('page_id =?' => $current_page_id));
                        Engine_Api::_()->getApi('layoutcore', 'sitepage')->setWithoutTabContent($current_page_id, $sitepage_layout_cover_photo);
                    }

                    if (!empty($selectPage)) {
                        $page_id = $selectPage->page_id;

                        $contentTable->delete(array('page_id =?' => $page_id));
                    }
                    //INSERT MAIN CONTAINER
                    $mainContainer = $contentTable->createRow();
                    $mainContainer->page_id = $page_id;
                    $mainContainer->type = 'container';
                    $mainContainer->name = 'main';
                    $mainContainer->order = 2;
                    $mainContainer->save();
                    $container_id = $mainContainer->admincontent_id;

                    //INSERT MAIN-MIDDLE CONTAINER.
                    $mainMiddleContainer = $contentTable->createRow();
                    $mainMiddleContainer->page_id = $page_id;
                    $mainMiddleContainer->type = 'container';
                    $mainMiddleContainer->name = 'middle';
                    $mainMiddleContainer->parent_content_id = $container_id;
                    $mainMiddleContainer->order = 6;
                    $mainMiddleContainer->save();
                    $middle_id = $mainMiddleContainer->admincontent_id;

                    //INSERT MAIN-LEFT CONTAINER.
                    $mainLeftContainer = $contentTable->createRow();
                    $mainLeftContainer->page_id = $page_id;
                    $mainLeftContainer->type = 'container';
                    $mainLeftContainer->name = 'right';
                    $mainLeftContainer->parent_content_id = $container_id;
                    $mainLeftContainer->order = 4;
                    $mainLeftContainer->save();
                    $left_id = $mainLeftContainer->admincontent_id;

                    //INSERTING TITLE WIDGET
                    if (empty($sitepage_layout_cover_photo)) {
                        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

                        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemember.pagecover-photo-sitepagemembers', $middle_id, 2, '', 'true');
                        }
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.title-sitepage', $middle_id, 3, '', 'true');

                        //INSERTING LIKE WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.like-button', $middle_id, 4, '', 'true');

                        //INSERTING FACEBOOK LIKE WIDGET
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
                            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', $middle_id, 5, '', 'true');
                        }

                        //INSERTING MAIN PHOTO WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mainphoto-sitepage', $left_id, 10, '', 'true');
                    } else {
                        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

                        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-cover-information-sitepage', $middle_id, 2, '', 'true');
                    }

                    //INSERTING CONTACT DETAIL WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.contactdetails-sitepage', $middle_id, 5, '', 'true');

//           //INSERTING PHOTO STRIP WIDGET
//           if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
//             Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.photorecent-sitepage', $middle_id, 6, '', 'true');
//           }
                    //INSERTING ACTIVITY FEED WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                        $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
                    } else {
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
                    }

                    //INSERTING INFORAMTION WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_id, 7, 'Info', 'true');

                    //INSERTING OVERVIEW WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.overview-sitepage', $middle_id, 8, 'Overview', 'true');

                    //INSERTING LOCATION WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.location-sitepage', $middle_id, 9, 'Map', 'true');

                    //INSERTING LINKS WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

                    //INSERTING WIDGET LINK WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.widgetlinks-sitepage', $left_id, 11, '', 'true');

                    //INSERTING OPTIONS WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.options-sitepage', $left_id, 12, '', 'true');

                    //INSERTING WRITE SOMETHING ABOUT WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.write-page', $left_id, 13, '', 'true');

                    //INSERTING INFORMATION WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.information-sitepage', $left_id, 10, 'Information', 'true');

                    //INSERTING LIKE WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

                    //INSERTING RATING WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', $left_id, 16, 'Ratings', 'true');
                    }

                    //INSERTING BADGE WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', $left_id, 17, 'Badge', 'true');
                    }

                    $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

                    //INSERTING SOCIAL SHARE WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.socialshare-sitepage', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//          //INSERTING FOUR SQUARE WIDGET
//          Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.foursquare-sitepage', $left_id, 20, '', 'true');
                    //INSERTING INSIGHTS WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 22, 'Insights', 'true');

                    //INSERTING FEATURED OWNER WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 23, 'Owners', 'true');

                    //INSERTING ALBUM WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 24, 'Albums');
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
                    }

                    //INSERTING PAGE PROFILE PLAYER WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 25, '', 'true');
                    }

                    //INSERTING LINKED PAGES WIDGET
                    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 26, 'Linked Pages', 'true');

                    //INSERTING VIDEO WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
                    }

                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitevideo.contenttype-videos', $page_id, 'Videos', 'true', '117');
                    }

                    //INSERTING NOTE WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
                    }

                    //INSERTING REVIEW WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
                    }

                    //INSERTING FORM WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
                    }

                    //INSERTING DOCUMENT WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
                    }

                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('document.contenttype-documents', $page_id, 'Documents', 'true', '115');
                        }

                    //INSERTING OFFER WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepageoffer.profile-sitepageoffers', $page_id, 'Offers', 'true', '116');
                    }

                    //INSERTING EVENT WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
                    }

                    //INSERTING EVENT WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('siteevent.contenttype-events', $page_id, 'Events', 'true', '117');
                    }

                    //INSERTING POLL WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
                    }

                    //INSERTING DISCUSSION WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
                    }

                    //INSERTING MUSIC WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
                    }
                    //INSERTING TWITTER WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
                    }

                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagemember.profile-sitepagemembers', $page_id, 'Members', 'true', '122');
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');
                    }

                    //INSERTING INTEGRATION WIDGET
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitepageintegration.profile-items', $page_id, '', '', '999');
                    }
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '125');
                    }
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic')) {
                        Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminContentDefaultInfoWithoutTab('sitemusic.contenttype-music', $page_id, 'Music', 'true', '126');
                    }
                }
            }

            Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage.layout.setting', $layout_option);
            Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage.layout.coverphotoenabled', 1);
            Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage.layout.cover.photo', $sitepage_layout_cover_photo);
            if ($page_reload < $reload_count) {
                $page_reload++;
                $this->_redirect("admin/sitepage/defaultlayout/index?page_reload=$page_reload&sitepage_sitepage_layout_setting=$layout_option");
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => false,
                'redirect' => $this->view->url(array('module' => 'sitepage', 'controller' => 'defaultlayout'), 'admin_default', true),
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin:10px auto;width:750px;"><span>Please do not close this page or navigate to another page till you see a default layout changes completion or error message.</span></div><div>
					<center><img src="application/modules/Sitepage/externals/images/layout/uploading.gif" alt="" /></center>
				</div>'))
            ));
        }
    }

    //ACTION FOR SAVING THE LAYOUT
    public function savelayoutAction() {

        $this->_helper->layout->setLayout('admin-simple');
    }

    public function saveCoverPageLayoutAction() {

        if (Engine_Api::_()->getApi("settings", "core")->getSetting('sitepage.layout.coverphotoenabled', 0) && !$this->getRequest()->isPost())
            return;

        $db = Engine_Db_Table::getDefaultAdapter();
        $coreContentTable = Engine_Api::_()->getDbtable('content', 'core');
        $coreContentTableName = $coreContentTable->info('name');
        $corePagesTable = Engine_Api::_()->getDbtable('pages', 'core');
        $corePagesTableName = $corePagesTable->info('name');
        $adminContentTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
        $adminContentTableName = $adminContentTable->info('name');
        $pageContentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
        $pageContentTableName = $pageContentTable->info('name');
        $coreContentPageID = $corePagesTable->select()->from($corePagesTableName, array('page_id'))->where('name =?', 'sitepage_index_view')->query()->fetchColumn();
        $adminContentId = $adminContentTable->select()->from($adminContentTableName, array('name'))->where('page_id =?', $coreContentPageID)->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
        $sitepageCoverPhoto = $_POST['sitepage_sitepage_layout_coverphoto'];
        if ($_POST['sitepage_sitepage_layout_coverphoto'] == 1) {
            $current = 'right';
            $prev = 'left';
        } else {
            $current = 'left';
            $prev = 'right';
        }
        if (empty($adminContentId)) {
            $adminContentCurrentColumn = $adminContentTable->select()->from($adminContentTableName, array('name'))->where('name =?', $prev)->where('page_id =?', $coreContentPageID)->query()->fetchColumn();
            if ($adminContentCurrentColumn) {
                $adminContentTable->update(array('name' => $current), array('name=?' => $prev, 'page_id =?' => $coreContentPageID));
            }

            $adminContentTable->delete(array('name =?' => 'sitepage.mainphoto-sitepage'));
            $adminContentTable->delete(array('name =?' => 'sitepage.photorecent-sitepage'));
            $adminContentTable->delete(array('name =?' => 'sitepagemember.profile-sitepagemembers-announcements'));
            $adminContentTable->delete(array('name =?' => 'seaocore.like-button'));
            $adminContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));
            $adminContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));
            $adminContentTable->delete(array('name =?' => 'sitepage.title-sitepage'));
        }

        $pageContentId = $pageContentTable->select()->from($pageContentTableName, array('name'))->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();

        if (empty($pageContentId)) {
            $pageContentCurrentColumn = $pageContentTable->select()->from($pageContentTableName, array('name'))->where('name =?', $prev)->query()->fetchColumn();
            if ($pageContentCurrentColumn) {
                $pageContentTable->update(array('name' => $current), array('name=?' => $prev));
            }

            $pageContentTable->delete(array('name =?' => 'sitepage.mainphoto-sitepage'));
            $pageContentTable->delete(array('name =?' => 'sitepage.photorecent-sitepage'));
            $pageContentTable->delete(array('name =?' => 'sitepagemember.profile-sitepagemembers-announcements'));
            $pageContentTable->delete(array('name =?' => 'seaocore.like-button'));
            $pageContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));
            $pageContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));
            $pageContentTable->delete(array('name =?' => 'sitepage.title-sitepage'));
        }

        $coreContentId = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentPageID)->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();

        if (empty($coreContentId)) {
            $coreContentCurrentColumn = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentPageID)->where('name =?', $prev)->where('name =?', $prev)->where('page_id =?', $coreContentPageID)->query()->fetchColumn();
            if ($coreContentCurrentColumn) {
                $coreContentTable->update(array('name' => $current), array('name=?' => $prev, 'page_id =?' => $coreContentPageID));
            }

            $coreContentTable->delete(array('name =?' => 'sitepage.mainphoto-sitepage'));
            $coreContentTable->delete(array('name =?' => 'sitepage.photorecent-sitepage'));
            $coreContentTable->delete(array('name =?' => 'sitepagemember.profile-sitepagemembers-announcements'));
            $coreContentTable->delete(array('name =?' => 'seaocore.like-button'));
            $coreContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));
            $coreContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));
            $coreContentTable->delete(array('name =?' => 'sitepage.title-sitepage'));
        }

        Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage.layout.coverphotoenabled', 1);
        Engine_Api::_()->getApi("settings", "core")->setSetting('sitepage.layout.coverphoto', $sitepageCoverPhoto);

        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();

        if (!empty($page)) {
            $page_id = $page[0]['page_id'];

            //INSERTING THE MEMBER WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
            $select = new Zend_Db_Select($db);
            $contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
            foreach ($contentpage_ids as $contentpage_id) {
                if (!empty($contentpage_id)) {

                    $select = new Zend_Db_Select($db);
                    $select_content = $select
                            ->from('engine4_sitepage_content')
                            ->where('contentpage_id = ?', $contentpage_id['contentpage_id'])
                            ->where('type = ?', 'widget')
                            ->where('name = ?', 'sitepage.page-cover-information-sitepage')
                            ->limit(1);
                    $content = $select_content->query()->fetchAll();
                    if (empty($content)) {
                        $select = new Zend_Db_Select($db);
                        $select_container = $select
                                ->from('engine4_sitepage_content', 'content_id')
                                ->where('contentpage_id = ?', $contentpage_id['contentpage_id'])
                                ->where('type = ?', 'container')
                                ->limit(1);
                        $container = $select_container->query()->fetchAll();
                        if (!empty($container)) {
                            $container_id = $container[0]['content_id'];
                            $select = new Zend_Db_Select($db);
                            $select_left = $select
                                    ->from('engine4_sitepage_content')
                                    ->where('parent_content_id = ?', $container_id)
                                    ->where('type = ?', 'container')
                                    ->where('name = ?', 'middle')
                                    ->limit(1);
                            $middle = $select_left->query()->fetchAll();
                            if (!empty($middle)) {
                                $middle_id = $middle[0]['content_id'];
                                $db->insert('engine4_sitepage_content', array(
                                    'contentpage_id' => $contentpage_id['contentpage_id'],
                                    'type' => 'widget',
                                    'name' => 'sitepage.page-cover-information-sitepage',
                                    'parent_content_id' => $middle_id,
                                    'order' => 1,
                                    'params' => '{"title":""}',
                                ));
                            }
                        }
                    }
                }
            }

            $select = new Zend_Db_Select($db);
            $select_content = $select
                    ->from('engine4_sitepage_admincontent')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitepage.page-cover-information-sitepage')
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = new Zend_Db_Select($db);
                $select_container = $select
                        ->from('engine4_sitepage_admincontent', 'admincontent_id')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $container_id = $container[0]['admincontent_id'];
                    $select = new Zend_Db_Select($db);
                    $select_left = $select
                            ->from('engine4_sitepage_admincontent')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_left->query()->fetchAll();
                    if (!empty($middle)) {
                        $middle_id = $middle[0]['admincontent_id'];
                        $db->insert('engine4_sitepage_admincontent', array(
                            'page_id' => $page_id,
                            'type' => 'widget',
                            'name' => 'sitepage.page-cover-information-sitepage',
                            'parent_content_id' => $middle_id,
                            'order' => 1,
                            'params' => '{"title":""}',
                        ));
                    }
                }
            }

            $select = new Zend_Db_Select($db);
            $select_content = $select
                    ->from('engine4_core_content')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitepage.page-cover-information-sitepage')
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = new Zend_Db_Select($db);
                $select_container = $select
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $container_id = $container[0]['content_id'];
                    $select = new Zend_Db_Select($db);
                    $select_left = $select
                            ->from('engine4_core_content')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_left->query()->fetchAll();
                    if (!empty($middle)) {
                        $middle_id = $middle[0]['content_id'];
                        $db->insert('engine4_core_content', array(
                            'page_id' => $page_id,
                            'type' => 'widget',
                            'name' => 'sitepage.page-cover-information-sitepage',
                            'parent_content_id' => $middle_id,
                            'order' => 1,
                            'params' => '{"title":""}',
                        ));
                    }
                }
            }
        }

        $this->_redirect("admin/sitepage/defaultlayout/index");
    }

    //ACTION FOR SAVING THE LAYOUT
    public function setCoverPageLayoutAction() {

        $this->_helper->layout->setLayout('admin-simple');
    }

}

?>