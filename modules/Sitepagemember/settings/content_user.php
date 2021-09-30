<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);
$categories_member = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRolesAssoc(Engine_Api::_()->core()->getSubject('sitepage_page')->page_id);
$categoryOptions = array();
$categoryOptions['0'] = 'Un-categorized (Display members who have not selected their membership roles.)';
if (!empty($categories_member)) {
    asort($categories_member, SORT_LOCALE_STRING);
    foreach ($categories_member as $key => $v) {
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory(Engine_Api::_()->core()->getSubject('sitepage_page')->category_id);
        $categoryOptions[$key] = $v . '  [' . $row->category_name . ']';
    }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Members'),
        'description' => $view->translate('Displays your Page\'s members.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagemember.profile-sitepagemembers',
        'defaultParams' => array(
            'title' => $view->translate('Members'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_option',
                    array(
                        'description' => $view->translate('Do you want to display members on the basis of their roles?'),
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
                        'description' => $view->translate('Choose the member roles which you want to display in this block.'),
                        'multiOptions' => $categoryOptions,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Announcements'),
        'description' => $view->translate('Displays list of announcements posted by page admins for their Businesses. This widget should be placed on the Page Profile.'),
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagemember.profile-sitepagemembers-announcements',
        'defaultParams' => array(
            'title' => 'Announcements',
            'titleCount' => true,
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
        'title' => $view->translate('Page Profile Cover Photo and Members'),
        'description' => $view->translate('Displays the cover photo of a Page. From the Edit Settings section of this widget, you can also choose to display page member’s profile photos, if Page Admin has not selected a cover photo. It is recommended to place this widget on the Page Profile at the top.'),
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
                        'label' => $view->translate('Enter the cover photo height (in px). (Minimum 150 px required.)'),
                        'value' => '300',
                    )
                ),
                array(
                    'Select',
                    'memberCount',
                    array(
                        'label' => $view->translate('Select members to be displayed in a row.'),
                        'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'),
                        'value' => '8',
                    )
                ),
                array(
                    'Radio',
                    'onlyMemberWithPhoto',
                    array(
                        'label' => $view->translate('Do you want to show only those members who have uploaded their profile pictures?'),
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
                        'label' => $view->translate('Select the information options that you want to be available in this block.'),
                        'multiOptions' => array('title' => $view->translate('Page Title'), "followButton" => $view->translate("Follow"), "likeButton" => $view->translate("Like"), "joinButton" => $view->translate("Join Page"), "addButton" => $view->translate("Add People")),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Select the information options that you want to be available in this block.'),
                        'multiOptions' => array("followCount" => "Follow", "likeCount" => "Like", "memberCount" => "Member"),
                    ),
                ),
            ),
        ),
    )
        )
?>