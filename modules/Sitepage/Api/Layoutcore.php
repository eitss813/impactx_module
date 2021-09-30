<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutcore.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_LayoutCore extends Core_Api_Abstract {

    /**
     * Sets the without tab widgets information in core content table
     *
     * @param int $page_id
     */
    public function setWithoutTabContent($page_id, $sitepage_layout_cover_photo) {

        //GET CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');

        //GET CONTENT TABLE NAME
        $contentTableName = $contentTable->info('name');

        //INSERTING MAIN CONTAINER
        $mainContainer = $contentTable->createRow();
        $mainContainer->page_id = $page_id;
        $mainContainer->type = 'container';
        $mainContainer->name = 'main';
        $mainContainer->order = 2;
        $mainContainer->save();
        $container_id = $mainContainer->content_id;

        //INSERTING MAIN-MIDDLE CONTAINER
        $mainMiddleContainer = $contentTable->createRow();
        $mainMiddleContainer->page_id = $page_id;
        $mainMiddleContainer->type = 'container';
        $mainMiddleContainer->name = 'middle';
        $mainMiddleContainer->parent_content_id = $container_id;
        $mainMiddleContainer->order = 6;
        $mainMiddleContainer->save();
        $middle_id = $mainMiddleContainer->content_id;

        //INSERTING MAIN-LEFT CONTAINER
        $mainLeftContainer = $contentTable->createRow();
        $mainLeftContainer->page_id = $page_id;
        $mainLeftContainer->type = 'container';
        $mainLeftContainer->name = 'right';
        $mainLeftContainer->parent_content_id = $container_id;
        $mainLeftContainer->order = 4;
        $mainLeftContainer->save();
        $left_id = $mainLeftContainer->content_id;

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

        //INSERTING ACTIVITY FEED WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
        } else {
            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.feed', $middle_id, 6, 'Updates', 'true');
        }

        //INSERTING INFORAMTION WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_id, 7, 'Info', 'true');

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

        //INSERTING INSIGHTS WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 22, 'Insights', 'true');

        //INSERTING FEATURED OWNER WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 23, 'Owners', 'true');

        //INSERTING ALBUM WIDGET
        $sitepageAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
        if ($sitepageAlbumEnabled) {
            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 24, 'Albums', 'true');
            $this->setDefaultInfoWithoutTab('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
        }

        //INSERTING LINKED PAGES WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 26, 'Linked Pages', 'true');

        //INSERTING VIDEO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
            Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 25, '', 'true');
        }

        //INSERTING VIDEO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $this->setDefaultInfoWithoutTab('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
        }

        //INSERTING EVENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
            $this->setDefaultInfoWithoutTab('sitevideo.contenttype-videos', $page_id, 'Videos', 'true', '111');
        }
        //INSERTING NOTE WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
            $this->setDefaultInfoWithoutTab('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
        }

        //INSERTING REVIEW WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
            $this->setDefaultInfoWithoutTab('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
        }

        //INSERTING FORM WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
            $this->setDefaultInfoWithoutTab('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
        }

        //INSERTING DOCUMENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
            $this->setDefaultInfoWithoutTab('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
        }

        //INSERTING DOCUMENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
            $this->setDefaultInfoWithoutTab('document.contenttype-documents', $page_id, 'Documents', 'true', '115');
        }

        //INSERTING OFFER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
            $this->setDefaultInfoWithoutTab('sitepageoffer.profile-sitepageoffers', $page_id, 'Offers', 'true', '116');
        }

        //INSERTING EVENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
            $this->setDefaultInfoWithoutTab('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
        }
        //INSERTING EVENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $this->setDefaultInfoWithoutTab('siteevent.contenttype-events', $page_id, 'Events', 'true', '117');
        }

        //INSERTING POLL WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
            $this->setDefaultInfoWithoutTab('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
        }

        //INSERTING DISCUSSION WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
            $this->setDefaultInfoWithoutTab('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
        }

        //INSERTING MUSIC WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
            $this->setDefaultInfoWithoutTab('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
        }

        //INSERTING TWITTER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
            $this->setDefaultInfoWithoutTab('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
        }

        //INSERTING MEMBER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $this->setDefaultInfoWithoutTab('sitepagemember.profile-sitepagemembers', $page_id, 'Member', 'true', '122');
            $this->setDefaultInfoWithoutTab('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');
        }

        //INSERTING MEMBER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
            $this->setDefaultInfoWithoutTab('sitepageintegration.profile-items', $page_id, '', '', 999);
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding')) {
            $this->setDefaultInfoWithoutTab('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '125');
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic')) {
            $this->setDefaultInfoWithoutTab('sitemusic.contenttype-music', $page_id, 'Music', 'true', '126');
        }
    }

    /**
     * Sets the tab widgets information in core content table
     *
     * @param int $page_id
     */
    public function setTabbedLayoutContent($page_id, $sitepage_layout_cover_photo) {

        //NOW INSERTING DEFUALT INFO OF OTHER SUB PLUGINS WHICH ARE DEPENDENTS ON THIS SITEPAGE PLUGIN ARE ENABLED.
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
            $this->setContentDefaultInfo('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
            $this->setContentDefaultInfo('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
        }

        //INSERTING VIDEO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
            $this->setContentDefaultInfo('sitevideo.contenttype-videos', $page_id, 'Videos', 'true', '111');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
            $this->setContentDefaultInfo('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
            $this->setContentDefaultInfo('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
            $this->setContentDefaultInfo('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
            $this->setContentDefaultInfo('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
        }

        //INSERTING DOCUMENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
            $this->setContentDefaultInfo('document.contenttype-documents', $page_id, 'Documents', 'true', '115');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
            $this->setContentDefaultInfo('sitepageoffer.profile-sitepageoffers', $page_id, 'Offers', 'true', '116');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
            $this->setContentDefaultInfo('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
        }

        //INSERTING EVENT WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $this->setContentDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
            $this->setContentDefaultInfo('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
            $this->setContentDefaultInfo('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
            $this->setContentDefaultInfo('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
        }
        //INSERTING TWITTER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
            $this->setContentDefaultInfo('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
        }
        //INSERTING MEMBER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $this->setContentDefaultInfo('sitepagemember.profile-sitepagemembers', $page_id, 'Member', 'true', '122');
            $this->setContentDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');
        }

        //INSERTING MEMBER WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
            $this->setContentDefaultInfo('sitepageintegration.profile-items', $page_id, '', '', 999);
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding')) {
            $this->setContentDefaultInfo('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '125');
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic')) {
            $this->setContentDefaultInfo('sitemusic.contenttype-music', $page_id, 'Music', 'true', '126');
        }
    }

    /**
     * Sets the tab widgets information in admin content and also content table for user layout
     *
     * @param int $page_id
     */
    public function setContentDefaultLayout($page_id) {

        //GET ADMIN CONTENT TABLE
        $admincontentTable = Engine_Api::_()->getDbtable('admincontent', 'sitepage');

        //GET CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

        //FETCH
        $corepageinfo = Engine_Api::_()->sitepage()->getWidgetizedPage();

        //SELECT ADMIN CONTENT
        $admincontentselected = $admincontentTable->select()->where('page_id =?', $corepageinfo->page_id)->where('type =?', 'container');

        //FETCH
        $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

        //DEFINE OLD CONTAINER
        $oldContener = array();

        //CREATING A ROW
        foreach ($admintableinfo as $value) {
            $mainContainer = $contentTable->createRow();
            $mainContainer->contentpage_id = $page_id;
            $mainContainer->type = 'container';
            $mainContainer->name = $value->name;
            $mainContainer->order = $value->order;
            $mainContainer->params = $value->params;
            if (isset($oldContener[$value->parent_content_id]))
                $mainContainer->parent_content_id = $oldContener[$value->parent_content_id];
            $mainContainer->save();
            $container_id = $mainContainer->content_id;
            $oldContener[$value->admincontent_id] = $container_id;
        }

        //SELECT ADMIN CONTENT
        $admincontentselected = $admincontentTable->select()->where('page_id =?', $corepageinfo->page_id)->where('type =?', 'widget')->where('name =?', 'core.container-tabs');

        //FETCH
        $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

        //CREATING A ROW
        foreach ($admintableinfo as $values) {
            $mainWidgets = $contentTable->createRow();
            $mainWidgets->contentpage_id = $page_id;
            $mainWidgets->type = 'widget';
            $mainWidgets->name = $values->name;
            $mainWidgets->order = $values->order;
            $mainWidgets->params = $values->params;
            if (isset($oldContener[$values->parent_content_id]))
                $mainWidgets->parent_content_id = $oldContener[$values->parent_content_id];
            $mainWidgets->save();
            $container_id = $mainWidgets->content_id;
            $oldContener[$values->admincontent_id] = $container_id;
        }

        //SELECT ADMIN CONTENT
        $admincontentselected = $admincontentTable->select()->where('page_id =?', $corepageinfo->page_id)->where('type =?', 'widget')->where('name <>?', 'core.container-tabs');

        //FETCH
        $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

        //CREATING A ROW
        foreach ($admintableinfo as $values) {
            $mainWidgets = $contentTable->createRow();
            $mainWidgets->contentpage_id = $page_id;
            $mainWidgets->type = 'widget';
            $mainWidgets->name = $values->name;
            $mainWidgets->order = $values->order;
            $mainWidgets->params = $values->params;
            if (isset($oldContener[$values->parent_content_id]))
                $mainWidgets->parent_content_id = $oldContener[$values->parent_content_id];
            $mainWidgets->save();
        }
    }

    /**
     * Set profile page default widget in core content table with tab
     *
     * @param string $name
     * @param int $page_id
     * @param string $title
     * @param int $titleCount
     * @param int $order
     */
    public function setContentDefaultInfo($name = null, $page_id, $title = null, $titleCount = null, $order = null, $params = null) {
        $db = Engine_Db_Table::getDefaultAdapter();
        if (!empty($name)) {
            $contentTable = Engine_Api::_()->getDbtable('content', 'core');
            $contentTableName = $contentTable->info('name');
            $select = $contentTable->select();
            $select_content = $select
                    ->from($contentTableName)
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $name)
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = $contentTable->select();
                $select_container = $select
                        ->from($contentTableName, array('content_id'))
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $container_id = $container[0]['content_id'];
                    $select = $contentTable->select();
                    $select_middle = $select
                            ->from($contentTableName)
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                        $middle_id = $middle[0]['content_id'];
                        $select = $contentTable->select();
                        $select_tab = $select
                                ->from($contentTableName)
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'core.container-tabs')
                                ->where('page_id = ?', $page_id)
                                ->limit(1);
                        $tab = $select_tab->query()->fetchAll();
                        $tab_id = '';
                        if (!empty($tab)) {
                            $tab_id = $tab[0]['content_id'];
                        } else {
                            $contentWidget = $contentTable->createRow();
                            $contentWidget->page_id = $page_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = 'core.container-tabs';
                            $contentWidget->parent_content_id = $middle_id;
                            $contentWidget->order = $order;
                            $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);
                            $contentWidget->params = "{\"max\":\"$showmaxtab\"}";
                            $tab_id = $contentWidget->save();
                        }

                        if ($name != 'sitepageintegration.profile-items') {
                            $contentWidget = $contentTable->createRow();
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
                                            ->from('engine4_core_content')
                                            ->where('parent_content_id = ?', $tab_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitepageintegration.profile-items')
                                            ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}');
                                    $info = $select->query()->fetch();
                                    if (empty($info)) {

                                        // tab on profile
                                        $db->insert('engine4_core_content', array(
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

                            $this->setpageintwidgetTab('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);

                            $this->setpageintwidgetTab('folder', '{"title":"Folder","resource_type":"folder_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $page_id);
                        }
                    }
                }
            }
        }
    }

    /**
     * Set profile page default widget in core content table without tab
     *
     * @param string $name
     * @param int $page_id
     * @param string $title
     * @param int $titleCount
     * @param int $order
     */
    public function setDefaultInfoWithoutTab($name = null, $page_id, $title = null, $titleCount = null, $order = null) {
        $db = Engine_Db_Table::getDefaultAdapter();
        if (!empty($name)) {
            $contentTable = Engine_Api::_()->getDbtable('content', 'core');
            $contentTableName = $contentTable->info('name');
            $select = $contentTable->select();
            $select_content = $select
                    ->from($contentTableName)
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $name)
                    ->limit(1);
            $content = $select_content->query()->fetchAll();
            if (empty($content)) {
                $select = $contentTable->select();
                $select_container = $select
                        ->from($contentTableName, array('content_id'))
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->limit(1);
                $container = $select_container->query()->fetchAll();
                if (!empty($container)) {
                    $container_id = $container[0]['content_id'];
                    $select = $contentTable->select();
                    $select_middle = $select
                            ->from($contentTableName)
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle = $select_middle->query()->fetchAll();
                    if (!empty($middle)) {
                        $middle_id = $middle[0]['content_id'];

                        if ($name != 'sitepageintegration.profile-items') {
                            $contentWidget = $contentTable->createRow();
                            $contentWidget->page_id = $page_id;
                            $contentWidget->type = 'widget';
                            $contentWidget->name = $name;
                            $contentWidget->parent_content_id = ($middle_id);
                            $contentWidget->order = $order;
                            $contentWidget->params = '{"title":"' . $title . '" , "titleCount":' . $titleCount . '}';
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
                                            ->from('engine4_core_content')
                                            ->where('parent_content_id = ?', $middle_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitepageintegration.profile-items')
                                            ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}');
                                    $info = $select->query()->fetch();
                                    if (empty($info)) {

                                        // tab on profile
                                        $db->insert('engine4_core_content', array(
                                            'page_id' => $page_id,
                                            'type' => 'widget',
                                            'name' => 'sitepageintegration.profile-items',
                                            'parent_content_id' => $middle_id,
                                            'order' => 999,
                                            'params' => '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}',
                                        ));
                                    }
                                    //}
                                }
                            }

                            $this->setpageintwidgetTab('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('folder', '{"title":"Folder","resource_type":"folder_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);

                            $this->setpageintwidgetTab('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $page_id);
                        }
                    }
                }
            }
        }
    }

    public function setpageintwidgetTab($module_name, $params, $tab_id, $page_id) {

        $db = Engine_Db_Table::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', $module_name);
        $module_enable = $select->query()->fetchObject();

        if (!empty($module_enable)) {

            $results = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();

            foreach ($results as $value) {

                // Check if it's already been placed
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_content')
                        ->where('parent_content_id = ?', $tab_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitepageintegration.profile-items')
                        ->where('params = ?', $params);
                $info = $select->query()->fetch();
                if (empty($info)) {
                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitepageintegration.profile-items',
                        'parent_content_id' => $tab_id,
                        'order' => 999,
                        'params' => $params,
                    ));
                }
            }
        }
        //END Document PLUGIN WORK
    }

}
