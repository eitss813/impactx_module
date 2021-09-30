<?php

$db = Engine_Db_Table::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES 
("sitemember_gutter", "standard", "Member Gutter Navigation Menu", "999"),
("sitemember_review_main", "standard", "Advanced Members - Members Main Navigation Menu", "999");');

$db->query('INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES ("Recently Viewed Reset", "sitemember", "Sitemember_Plugin_Task_RecentlyViewed", "3600", "1", "0", "0", "0", "0", "0", "0", "0", "0", "0");');

$db->query('INSERT IGNORE INTO `engine4_authorization_permissions` SELECT level_id as `level_id`, "user" as `type`, "compliment" as `name`, "1" as `value`, NULL as `params` FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");');

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitemember_review_add", "sitemember", \'{item:$subject} rated and wrote a review for the member {item:$object}:\', "1", "3", "1", "1", "1", "1");');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ("SITEMEMBER_EMAIL_FRIEND", "sitemember", "[host],[email],[recipient_title],[recipient_link],[review_title],[review_title_with_link],[user_email],[userComment]");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitemember_admin_main_manage", "sitemember", "View Members", "", \'{"route":"admin_default","module":"sitemember","controller":"manage","action":"index"}\', "sitemember_admin_main", "",2),
("sitemember_admin_main_review", "sitemember", "Reviews & Ratings", "", \'{"route":"admin_default","module":"sitemember","controller":"review"}\', "sitemember_admin_main", "", "4"),
("sitemember_admin_main_userlocations","sitemember", "Member Location", "",\'{"route":"admin_default","module":"sitemember","controller":"settings","action":"userlocations"}\', "sitemember_admin_main", "", 5),
("sitemember_admin_main_searchformsettings", "sitemember", "Search Form Settings", "", \'{"route":"admin_default","module":"sitemember","controller":"settings","action":"form-search"}\', "sitemember_admin_main", "",3),
("sitemember_gutter_usereditlocation", "sitemember", "Edit My Location", "Sitemember_Plugin_Menus", "", "user_profile", "", 999),
("sitemember_admin_reviewmain_general", "sitemember", "Review Settings", "", \'{"route":"admin_default","module":"sitemember","controller":"review"}\', "sitemember_admin_reviewmain", "", "1"),
("sitemember_admin_reviewmain_manage", "sitemember", "Manage Reviews & Ratings", "", \'{"route":"admin_default","module":"sitemember","controller":"review", "action":"manage"}\', "sitemember_admin_reviewmain", "", "3"),
("sitemember_admin_reviewmain_ratingparams", "sitemember", "Rating Parameters", "", \'{"route":"admin_default","module":"sitemember","controller":"ratingparameters","action":"manage"}\', "sitemember_admin_reviewmain", "", "4"),
("sitemember_admin_reviewmain_level", "sitemember", "Member Level Settings", "", \'{"route":"admin_default","module":"sitemember","controller":"review","action":"level"}\', "sitemember_admin_reviewmain", "", "2"),
("sitemember_main_user", "sitemember", "Members", "", \'{"route":"sitemember_userbylocation"}\', "sitemember_review_main", "", "1"),
("sitemember_review_top-rated", "sitemember", "Popular Members", "", \'{"route":"sitemember_review_browse","action":"top-rated"}\', "sitemember_review_main", "",  "2"),
("user_edit_location", "user", "Edit Location", "", \'{"route":"sitemember_edituserspecific","module":"sitemember","controller":"location","action":"edit-location"}\', "user_edit", "", "3"),
("sitemember_admin_main_compliments", "sitemember", "Manage Compliments", "", \'{"route":"admin_default","module":"sitemember","controller":"compliment"}\', "sitemember_admin_main", "","5"),
("sitemember_compliment_browse", "sitemember", "Compliments","", \'{"route":"sitemember_compliment_browse"}\', "sitemember_review_main","","4");');


$isExist = $db->select()
                ->from('engine4_activity_notificationtypes', 'type')
                ->where('type = ?', 'sitemember_write_review')
                ->limit(1)->query()->fetchColumn();
if (empty($isExist)) {
    $db->insert('engine4_activity_notificationtypes', array(
        'type' => 'sitemember_write_review',
        'module' => 'sitemember',
        'body' => '{item:$subject} has written a {item:$object:review} for the {itemParent:$object::member}.',
        'is_request' => '0',
        'handler' => ''
    ));
}

$isExist = $db->select()
                ->from('engine4_core_menuitems', 'id')
                ->where('name = ?', 'sitemember_gutter_top-rated')
                ->limit(1)->query()->fetchColumn();
if (empty($isExist)) {
    $db->insert('engine4_core_menuitems', array(
        'name' => 'sitemember_gutter_top-rated',
        'module' => 'sitemember',
        'label' => 'Top Rated Members',
        'plugin' => 'Sitemember_Plugin_Menus::onMenuInitialize_SitememberGutterTopRated',
        'params' => '',
        'menu' => 'sitemember_gutter',
        'submenu' => '',
        'order' => '1',
    ));
}

$isExist = $db->select()
                ->from('engine4_core_menuitems', 'id')
                ->where('name = ?', 'sitemember_gutter_most-recommended')
                ->limit(1)->query()->fetchColumn();
if (empty($isExist)) {
    $db->insert('engine4_core_menuitems', array(
        'name' => 'sitemember_gutter_most-recommended',
        'module' => 'sitemember',
        'label' => 'Most Recommended Members',
        'plugin' => 'Sitemember_Plugin_Menus::onMenuInitialize_SitememberGutterMostRecommended',
        'params' => '',
        'menu' => 'sitemember_gutter',
        'submenu' => '',
        'order' => '2',
    ));
}

$isExist = $db->select()
                ->from('engine4_core_menuitems', 'id')
                ->where('name = ?', 'sitemember_gutter_most-reviewed')
                ->limit(1)->query()->fetchColumn();
if (empty($isExist)) {
    $db->insert('engine4_core_menuitems', array(
        'name' => 'sitemember_gutter_most-reviewed',
        'module' => 'sitemember',
        'label' => 'Most Reviewed Members',
        'plugin' => 'Sitemember_Plugin_Menus::onMenuInitialize_SitememberGutterMostReviewed',
        'params' => '',
        'menu' => 'sitemember_gutter',
        'submenu' => '',
        'order' => '3',
    ));
}

$isExist = $db->select()
                ->from('engine4_core_menuitems', 'id')
                ->where('name = ?', 'sitemember_gutter_top-reviewers')
                ->limit(1)->query()->fetchColumn();
if (empty($isExist)) {
    $db->insert('engine4_core_menuitems', array(
        'name' => 'sitemember_gutter_top-reviewers',
        'module' => 'sitemember',
        'label' => 'Top Reviewers',
        'plugin' => 'Sitemember_Plugin_Menus::onMenuInitialize_SitememberGutterTopReviewers',
        'params' => '',
        'menu' => 'sitemember_gutter',
        'submenu' => '',
        'order' => '4',
    ));
}


$isExist = $db->select()
                ->from('engine4_core_menuitems', 'id')
                ->where('name = ?', 'sitemember_review_browse')
                ->limit(1)->query()->fetchColumn();
if (empty($isExist)) {
    $db->insert('engine4_core_menuitems', array(
        'name' => 'sitemember_review_browse',
        'module' => 'sitemember',
        'label' => 'Browse Reviews',
        'plugin' => 'Sitemember_Plugin_Menus::canViewBrosweReview',
        'params' => '{"route":"sitemember_review_browse","action":"browse"}',
        'menu' => 'sitemember_review_main',
        'submenu' => '',
        'order' => '3',
    ));
}
    //CODE FOR COMPLiMENT WIDGET AND ICONS
    $this->createComplimentCategory();
    $this->complimentWidgetSetting();

$isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify');
if (!empty($isModEnabled)) {
    //START UPDATE BROWSE MEMBER PAGE WIDGET.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content', array('content_id', 'params'))
            ->where('type =?', 'widget')
            ->where('name =?', 'sitemember.browse-members-sitemember');
    $fetch = $select->query()->fetchAll();
    foreach ($fetch as $modArray) {
        $params = !empty($modArray['params']) ? Zend_Json::decode($modArray['params']) : array();
        if (is_array($params)) {
            if (!in_array("verifyCount", $params['memberInfo']))
                $params['memberInfo'][] = "verifyCount";

            if (!in_array("verifyLabel", $params['memberInfo']))
                $params['memberInfo'][] = "verifyLabel";

            $paramss = Zend_Json::encode($params);
            $tableObject = Engine_Api::_()->getDbtable('content', 'core');
            $tableObject->update(array("params" => $paramss), array("content_id =?" => $modArray['content_id']));
        }
    }
    //END UPDATE BROWSE MEMBER PAGE WIDGET.
}
