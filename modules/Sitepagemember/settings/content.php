<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);

$categories_member = (array)Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getSiteAdminRoles(array(), 'adminParams');
$categoryOptions = array();
$categoryOptions['0'] = 'Un-categorized (Display members who have not selected their membership roles.)';
if (!empty($categories_member)) {
    asort($categories_member, SORT_LOCALE_STRING);

    foreach ($categories_member as $v) {
        if ($manageCategorySettings != 1) {
            $categoryOptions['pageadminRole'] = 'Roles created by Page Admins';
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($v['page_category_id']);
        $categoryOptions[$v['role_id']] = $v['role_name'] . '  [' . $row->category_name . ']';
    }
}

$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
if (count($categories) != 0) {
    $categories_prepared[0] = "";
    foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
    }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$final_array = array(
    array(
        'title' => 'Page Profile Members',
        'description' => 'This widget form the Member tab on the Page Profile and displays the members of the Page. You can choose to display all members or members based on their Roles by using the edit settings of this widget. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagemember.profile-sitepagemembers',
        'defaultParams' => array(
            'title' => 'Members',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_option',
                    array(
                        'label' => 'Do you want to display members on the basis of their roles?',
                        'multiOptions' => array(
                            '0' => 'Yes, display members based on their roles.',
                            '1' => 'No, display all members.',
                        ), 'value' => 1,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'roles_id',
                    array(
                        'label' => 'Choose the member roles which you want to display in this block.',
                        'multiOptions' => $categoryOptions,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Announcements',
        'description' => 'Displays list of announcements posted by page admins for their Pages. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagemember.profile-sitepagemembers-announcements',
        'defaultParams' => array(
            'title' => 'Announcements',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of announcements to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page’s Member of the Day',
        'description' => 'Displays the Member of the Day as selected by the Admin from the ‘Member of the Day’ section of this plugin.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.member-of-the-day',
        //'adminForm' => 'Sitepagemember_Form_Admin_Item',
        'defaultParams' => array(
            'title' => 'Member of the Day'
        ),
    ),
    array(
        'title' => 'Page’s Featured Members Slideshow',
        'description' => 'Displays featured members in an attractive slideshow. You can set the count of the number of members to show in this widget. If the total number of members featured are more than that count, then the members to be displayed will be sequentially picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.featured-members-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Members',
            'itemCountPerPage' => 10,
        ),
    ),
    array(
        'title' => 'Recent / Top Page Joiners',
        'description' => 'Displays the recent / top Page joiners on the site. You can place this widget multiple times on a page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.home-recent-mostvaluable-sitepagemember',
        'defaultParams' => array(
            'title' => 'Recent Page Joiners'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'select_option',
                    array(
                        'label' => 'Choose recent / top Page joiners to be shown in this block.',
                        'multiOptions' => array(
                            1 => 'Recent Page Joiners',
                            2 => 'Top Page Joiners',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Members',
        'description' => 'Displays the link to view Page’s Members Browse page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.sitepagememberlist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Search Page Members form',
        'description' => 'Displays the form for searching Page Members on the basis of various filters. You can edit the fields to be available in this form.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.search-sitepagemember',
        'defaultParams' => array(
            'title' => '',
            'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'search_column',
                    array(
                        'label' => 'Choose the fields that you want to be available in the Search Page Members form widget.',
                        'multiOptions' => array("2" => "Browse By", "3" => "Page Title", "4" => "Member Keywords", "5" => "Page Category"),
                    ),
                ),
            ),
        )
    ),
    array(
        'title' => 'Page Members',
        'description' => 'Displays the list of Members from Pages created on your community. This widget should be placed in the widgetized Page Members page. Results from the Search Page Members form are also shown here.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.sitepage-member',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page’s Ajax based Tabbed widget for Members',
        'description' => 'Displays the Recent Page Joiners and Featured Members in separate AJAX based tabs.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.list-members-tabs-view',
        'defaultParams' => array(
            'title' => 'Members',
            'margin_photo' => 12,
            'showViewMore' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showViewMore',
                    array(
                        'label' => 'Show "View More" link',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ), 'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Joined Pages',
        'description' => 'Displays a list of pages having maximum number of members. You can choose number of members to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.mostjoined-sitepage',
        'defaultParams' => array(
            'title' => 'Most Joined Pages',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of pages to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Popular Pages',
        'description' => 'Displays the list of Pages having maximum number of comments / likes / views / members. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagemember.mostactive-sitepage',
        'defaultParams' => array(
            'title' => 'Most Popular Pages',
            'titleCount' => true,
            'statistics' => array("members")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'active_pages',
                    array(
                        'label' => 'Select popularity criteria.',
                        'multiOptions' => array('comment_count' => 'Comments', 'like_count' => 'Likes', 'view_count' => 'Views', 'member_count' => 'Members'),
                        'value' => 'member_count',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("comments" => "Comments", "likes" => "Likes", "views" => "Views", "members" => "Members"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Cover Photo and Members',
        'description' => 'Displays the cover photo of a Page. From the Edit Settings section of this widget, you can also choose to display page member’s profile photos, if Page Admin has not selected a cover photo. It is recommended to place this widget on the Page Profile at the top.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagemember.pagecover-photo-sitepagemembers',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'showContent' => array("title", "followButton", "likeButton", "joinButton", "addButton"),
            'statistics' => array("followCount", "likeCount", "memberCount")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
                        'value' => '300',
                    )
                ),
                array(
                    'Select',
                    'memberCount',
                    array(
                        'label' => 'Select members to be displayed in a row.',
                        'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'),
                        'value' => '8',
                    )
                ),
                array(
                    'Radio',
                    'onlyMemberWithPhoto',
                    array(
                        'label' => 'Do you want to show only those members who have uploaded their profile pictures?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array('title' => 'Page Title', "followButton" => "Follow", "likeButton" => "Like", "joinButton" => "Join Page", "addButton" => "Add People"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("followCount" => "Follow", "likeCount" => "Like", "memberCount" => "Member"),
                    ),
                ),
            ),
        ),
    )
);

return $final_array;
