<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$coreModules = Engine_Api::_()->getDbtable('modules', 'core');
$complimentCategoryOptions = Engine_Api::_()->getDbtable('complimentCategories', 'sitemember')->getComplimentCategories();


if ($coreModules->isModuleEnabled('siteverify')) {
    $label = 'Popularity Duration (This duration will be applicable to these Popularity Criteria: Most Liked, Most Verified, Recently Signups and Recently Updated.)';
} else {
    $label = 'Popularity Duration (This duration will be applicable to these Popularity Criteria: Most Liked, Recently Signups and Recently Updated.)';
}

$showViewMoreContent = array(
    'Select',
    'show_content',
    array(
        'label' => 'What do you want for view more content?',
        'description' => '',
        'multiOptions' => array(
            '1' => 'Pagination',
            '2' => 'Show View More Link at Bottom',
            '3' => 'Auto Load Members on Scrolling Down'),
        'value' => 2,
    )
);

$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => 'Show Members',
        'multiOptions' => array('' => '',
            'featured' => 'Featured Only',
            'sponsored' => 'Sponsored Only',
            'fea_spo' => 'Both Featured and Sponsored'
        ),
        'value' => '',
    )
);
$hasPhotoElement = array(
    'Radio',
    'has_photo',
    array(
        'label' => 'Do you want to show only those member who have profile photo?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '1'
    )
);

$circularImageElement = array(
    'Radio',
    'circularImage',
    array(
        'label' => 'Do you want to show members photo in circular shape instead of square?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$listFullWidthElement = array(
    'Radio',
    'listFullWidthElement',
    array(
        'label' => 'Set Column Of List View.',
        'multiOptions' => array(
            1 => '1 Column',
            0 => '2 Column'
        ),
        'value' => '0'
    )
);

$circularImageHeightElement = array(
    'Text',
    'circularImageHeight',
    array(
        'label' => 'Circular Image Height For Grid View.',
        'value' => '180',
    )
);

$circularPinboardImageHeightElement = array(
    'Text',
    'circularPinboardImageHeight',
    array(
        'label' => 'Circular Image Height For Pinboard View.',
        'value' => '190',
    )
);

$commonColumnHeight = array(
    'Text',
    'commonColumnHeight',
    array(
        'label' => 'Column Height For List View. [One Item / Block Height]',
        'value' => '240',
    )
);

$customFieldTitleElement = array(
    'Radio',
    'custom_field_title',
    array(
        'label' => 'Do you want to show “Title" of custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$customFieldHeadingElement = array(
    'Radio',
    'custom_field_heading',
    array(
        'label' => 'Do you want to show "Heading" of custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to display members based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

if ($coreSettings->getSetting('sitemember.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which members will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which members will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}

$defaultLocationDistanceElement = array(
    'Select',
    'defaultLocationDistance',
    array(
        'label' => $locationDescription,
        'multiOptions' => array(
            '0' => '',
            '1' => '1 ' . $locationLableS,
            '2' => '2 ' . $locationLable,
            '5' => '5 ' . $locationLable,
            '10' => '10 ' . $locationLable,
            '20' => '20 ' . $locationLable,
            '50' => '50 ' . $locationLable,
            '100' => '100 ' . $locationLable,
            '250' => '250 ' . $locationLable,
            '500' => '500 ' . $locationLable,
            '750' => '750 ' . $locationLable,
            '1000' => '1000 ' . $locationLable,
        ),
        'value' => '1000'
    )
);

$popularity_options = array(
    'view_count' => 'Most Viewed',
    'like_count' => 'Most Liked',
    'creation_date' => 'Recently Signups',
    'modified_date' => 'Recently Updated',
    'rating' => "Top Rated"
);

if ($coreModules->isModuleEnabled('siteverify')) {
    $popularity_options['verify_count'] = "Most Verified";
}

$popularity_most_type_options = array(
    'rating_avg' => 'Most Rated',
    'review_count' => 'Most Reviewed',
    'recommend_count' => 'Most Recommend',
    'top_reviewers' => "Top Reviewers"
);

$tempOtherInfoElement = array(
    "ratingStar" => "Rating",
    "featuredLabel" => "Featured Label",
    "sponsoredLabel" => "Sponsored Label",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "memberCount" => "Friends",
    "mutualFriend" => "Mutual Friends",
    "memberStatus" => "Member Status (Online)",
    "joined" => "Joined (Duration after signed up)",
    "networks" => "Networks",
    "profileField" => "Profile Fields",
    'age' => "Age"
);
if ($coreSettings->getSetting('sitemember.reviews.ratings') != 3) {
    if ($coreSettings->getSetting('sitemember.reviews.ratings')) {
        $tempOtherInfoElement = array_merge($tempOtherInfoElement, array("reviewCount" => "Reviews"));
    } elseif ($coreSettings->getSetting('sitemember.reviews.ratings') == 0) {
        $tempOtherInfoElement = array_merge($tempOtherInfoElement, array("reviewCount" => "Votes"));
    }
}
$statisticsPhotoElement = array(
    "featuredLabel" => "Featured Label",
    "sponsoredLabel" => "Sponsored Label",
);


$profileInformationElement = array("location" => "Location", "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)", "viewCount" => "Views", "likeCount" => "Likes", "memberCount" => "Friends", "mutualFriend" => "Mutual Friends", "memberStatus" => "Member Status (Online)", "joined" => "Joined (Duration after signed up)", "networks" => "Networks", "profileField" => "Profile Fields");

$show_buttons = array('facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it');

$LinksElelment = array("addfriend" => "Add Friend", 'messege' => "Message");

if ($coreModules->isModuleEnabled('sitelike')) {
    $profileInformationElement['likebutton'] = "Like Button";
    $show_buttons['like'] = "Like / Unlike";
    $LinksElelment['likebutton'] = "Like Button";
}

if ($coreModules->isModuleEnabled('poke')) {
    $LinksElelment['poke'] = "Poke";
}
if ($coreModules->isModuleEnabled('suggestion')) {
    $LinksElelment['suggestion'] = 'Suggest to friend';
}

if ($coreModules->isModuleEnabled('siteverify')) {
    $tempOtherInfoElement['verifyCount'] = "Verifies";
    $tempOtherInfoElement['verifyLabel'] = "Verify Icon";
    $statisticsPhotoElement['verifyLabel'] = "Verify Icon";
    $profileInformationElement['verifyCount'] = "Verifies";
}

$statisticsElement = array(
    'MultiCheckbox',
    'memberInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the members in this block.',
        'multiOptions' => $tempOtherInfoElement,
    //'value' => array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField",),
    ),
);



if ($coreSettings->getSetting('sitemember.reviews.ratings')) {
    $statisticsMostTypesElement = array(
        'MultiCheckbox',
        'memberInfo',
        array(
            'label' => 'Choose the options that you want to be displayed for the members in this block.',
            'multiOptions' => array('ratingStar' => "Rating", "reviewCount" => "Reviews", 'recommendCount' => "Recommend")
        ),
    );
} elseif ($coreSettings->getSetting('sitemember.reviews.ratings') == 0) {
    $statisticsMostTypesElement = array(
        'MultiCheckbox',
        'memberInfo',
        array(
            'label' => 'Choose the options that you want to be displayed for the members in this block.',
            'multiOptions' => array('ratingStar' => "Rating", "reviewCount" => "Votes", 'recommendCount' => "Recommend")
        ),
    );
}

$browseStatisticsElement = array(
    'MultiCheckbox',
    'memberInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the members in this block.',
        'multiOptions' => array_merge($tempOtherInfoElement, array('distance' => 'Distance')),
    // 'value' => array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField", 'distance'),
    ),
);
$titlePositionElement = array(
    'Radio',
    'titlePosition',
    array(
        'label' => 'Do you want "Member Title" to be displayed in grid view?',
        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
        'value' => 1,
    ),
);

$final_array = array(
    array(
        'title' => 'Ajax based Tabbed widget',
        'description' => "Contains multiple AJAX based tabs showing Recently Signed Up, Featured, Popular, Liked and Sponsored member in a block in a separate AJAX based tab respectively. You can configure various settings for this widget from the edit settings.",
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.recently-popular-random-sitemember',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'layouts_views' => array("listZZZview", "gridZZZview", "mapZZZview"),
            'ajaxTabs' => array("mostZZZrecent", "mostZZZviewed", "mostZZZpopular", "mostZZZlike", "featured", "sponsored", "thisZZZmonth", "thisZZZweek", "today"),
            'user_id_order' => 1,
            'modified_date_order' => 2,
            'featured_order' => 3,
            'sponosred_order' => 4,
            'popular_order' => 5,
            'like_order' => 6,
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for members.',
                        'multiOptions' => array("listZZZview" => "List View", "gridZZZview" => "Grid View", "mapZZZview" => "Map View"),
                    // 'value' => array("listZZZview", "gridZZZview", "mapZZZview")
                    ),
                ),
                array(
                    'Radio',
                    'defaultOrder',
                    array(
                        'label' => 'Select a default view type for members',
                        'multiOptions' => array("listZZZview" => "List View", "gridZZZview" => "Grid View", "mapZZZview" => "Map View"),
                        'value' => "listZZZview",
                    )
                ),
                $titlePositionElement,
                array(
                    'Radio',
                    'showDetailLink',
                    array(
                        'label' => 'Do you want to display "Details" link in list view ?',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View. [One Item / Block Width]',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View. [One Item / Block Height]',
                        'value' => '328',
                    )
                ),
                $commonColumnHeight,
                $listFullWidthElement,
                $circularImageElement,
                $circularImageHeightElement,
                $hasPhotoElement,
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                    //'value' => array("addfriend", 'message')
                    )
                ),
                $statisticsElement,
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldHeadingElement,
                $customFieldTitleElement,
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'description' => '',
                        'multiOptions' => array(
                            '1' => 'Show View More Link at Bottom',
                            '2' => 'Auto Load Members on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array("mostZZZrecent" => "Recently Signups", "mostZZZviewed" => "Most Viewed", "mostZZZpopular" => "Most Popular", "mostZZZliked" => "Most Liked", "featured" => "Featured", "sponsored" => "Sponsored", "thisZZZmonth" => "This Month", "thisZZZweek" => "This Week", "today" => "Today"),
                    // 'value' => array("mostZZZrecent", "mostZZZviewed", "mostZZZpopular", "mostZZZlike", "featured", "sponsored"),
                    ),
                ),
                array(
                    'Text',
                    'upcoming_order',
                    array(
                        'label' => 'Recently Signups Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'views_order',
                    array(
                        'label' => 'Most Viewed Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'popular_order',
                    array(
                        'label' => 'Most Popular Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'like_order',
                    array(
                        'label' => 'Most Liked Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'featured_order',
                    array(
                        'label' => 'Featured Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'sponosred_order',
                    array(
                        'label' => 'Sponosred Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'month_order',
                    array(
                        'label' => 'This Month Tab (order)',
                        'value' => 7
                    ),
                ),
                array(
                    'Text',
                    'week_order',
                    array(
                        'label' => 'This Week Tab (order)',
                        'value' => 8
                    ),
                ),
                array(
                    'Text',
                    'today_order',
                    array(
                        'label' => 'Today Tab (order)',
                        'value' => 9
                    ),
                ),
                array(
                    'Radio',
                    'sitemember_map_sponsored',
                    array(
                        'label' => 'Sponsored Members with a Bouncing Animation',
                        'description' => 'Do you want the sponsored members to be shown with a bouncing animation in the Map?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'truncationList',
                    array(
                        'label' => 'Title Truncation Limit in List View',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            )
        ),
    ),
    array(
        'title' => 'Member Profile: Friends / Mutual Friends',
        'description' => 'Displays the common friends between the viewer and the member being currently viewed or all member’s friends. This widget should be placed on the Member Profile Page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-friends-mutual',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Mutual Friends'
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'show',
                    array(
                        'label' => 'What do want to show in this widget?',
                        'multiOptions' => array(
                            'friends' => 'Friends',
                            'mutualfriends' => 'Mutual Friends',
                        ),
                        'value' => 'friends',
                    )
                ),
                $titlePositionElement,
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Photo Width For Grid View.',
                        'value' => '64',
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Photo Height For Grid View.',
                        'value' => '64',
                    )
                ),
                $circularImageElement
            ),
        ),
    ),
    array(
        'title' => 'Member Profile: Member Information',
        'description' => 'Displays the views, and other information about an member. This widget should be placed on Members Profile page in the left column.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.information-sitemember',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => array("viewCount", "likeCount", "location")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array_merge($profileInformationElement, array('membertype' => 'Member Type', 'lastupdate' => 'Last Update')),
                    //'value' => array("location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField")
                    ),
                ),
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldHeadingElement,
                $customFieldTitleElement,
            ),
        ),
    ),
    array(
        'title' => 'Main Photo of Member',
        'description' => 'Displays member\'s photo on their profile and Member Home Page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-photo-sitemembers',
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the options that you want to be displayed in this block.',
                        'multiOptions' => $statisticsPhotoElement
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Members',
        'description' => 'Displays a list of all the members on your site. This widget should be placed on Advanced Members - Browse Members page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.browse-members-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("1", "2", "3", "4"),
            'layouts_order' => 1,
            'columnWidth' => '180',
            'truncationGrid' => 90
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for members.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View", "4" => "Pinboard View"),
                    //'value' => array("1", "2", "3", "4"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => 'Select a default view type for members.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View", "4" => "Pinboard View"),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View. [One Item / Block Width]',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View. [One Item / Block Height]',
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'pinboarditemWidth',
                    array(
                        'label' => 'Column Width For Pinboard View. [One Item / Block Width]',
                        'value' => 237,
                    )
                ),
                $commonColumnHeight,
                $listFullWidthElement,
                $circularImageElement,
                $circularImageHeightElement,
                $circularPinboardImageHeightElement,
                $hasPhotoElement,
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                    //'value' => array("addfriend", 'message')
                    )
                ),
                array(
                    'Radio',
                    'showDetailLink',
                    array(
                        'label' => 'Do you want to display "Details" link in list view ?',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                        'value' => 1,
                    )
                ),
                $browseStatisticsElement,
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldTitleElement,
                $customFieldHeadingElement,
                $titlePositionElement,
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Members. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'creationDate' => 'All members in descending order of Signups.',
                            'viewCount' => 'All members in descending order of views.',
                            'title' => 'All members in alphabetical order.',
                            'sponsored' => 'Sponsored members followed by others in descending order of Signups.',
                            'featured' => 'Featured members followed by others in descending order of Signups.',
                            'fespfe' => 'Sponsored & Featured members followed by Sponsored members followed by Featured members followed by others in descending order of Signups.',
                            'spfesp' => 'Featured & Sponsored members followed by Featured members followed by Sponsored members followed by others in descending order of Signups.',
                        //'verifylabel' => 'Members marked as Verify followed by others in descending order of creation.',
                        ),
                        'value' => 'creationDate',
                    ),
                ),
                $showViewMoreContent,
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the members displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => $show_buttons,
                    //'value' => array('facebook', 'twitter', 'pinit'),
                    ),
                ),
                array(
                    'Radio',
                    'sitemember_map_sponsored',
                    array(
                        'label' => 'Sponsored Members with a Bouncing Animation',
                        'description' => 'Do you want the sponsored members to be shown with a bouncing animation in the Map?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
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
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement
            ),
        ),
    ),
    array(
        'title' => 'Test widget',
        'description' => 'Displays a list of all the members on your site. This widget should be placed on Advanced Members - Browse Members page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.members-test',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("1", "2", "3", "4"),
            'layouts_order' => 1,
            'columnWidth' => '180',
            'truncationGrid' => 90
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for members.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View", "4" => "Pinboard View"),
                    //'value' => array("1", "2", "3", "4"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => 'Select a default view type for members.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View", "4" => "Pinboard View"),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View. [One Item / Block Width]',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View. [One Item / Block Height]',
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'pinboarditemWidth',
                    array(
                        'label' => 'Column Width For Pinboard View. [One Item / Block Width]',
                        'value' => 237,
                    )
                ),
                $commonColumnHeight,
                $listFullWidthElement,
                $circularImageElement,
                $circularImageHeightElement,
                $circularPinboardImageHeightElement,
                $hasPhotoElement,
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                    //'value' => array("addfriend", 'message')
                    )
                ),
                array(
                    'Radio',
                    'showDetailLink',
                    array(
                        'label' => 'Do you want to display "Details" link in list view ?',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                        'value' => 1,
                    )
                ),
                $browseStatisticsElement,
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldTitleElement,
                $customFieldHeadingElement,
                $titlePositionElement,
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Members. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'creationDate' => 'All members in descending order of Signups.',
                            'viewCount' => 'All members in descending order of views.',
                            'title' => 'All members in alphabetical order.',
                            'sponsored' => 'Sponsored members followed by others in descending order of Signups.',
                            'featured' => 'Featured members followed by others in descending order of Signups.',
                            'fespfe' => 'Sponsored & Featured members followed by Sponsored members followed by Featured members followed by others in descending order of Signups.',
                            'spfesp' => 'Featured & Sponsored members followed by Featured members followed by Sponsored members followed by others in descending order of Signups.',
                        //'verifylabel' => 'Members marked as Verify followed by others in descending order of creation.',
                        ),
                        'value' => 'creationDate',
                    ),
                ),
                $showViewMoreContent,
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the members displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => $show_buttons,
                    //'value' => array('facebook', 'twitter', 'pinit'),
                    ),
                ),
                array(
                    'Radio',
                    'sitemember_map_sponsored',
                    array(
                        'label' => 'Sponsored Members with a Bouncing Animation',
                        'description' => 'Do you want the sponsored members to be shown with a bouncing animation in the Map?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
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
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement
            ),
        ),
    ),
    array(
        'title' => 'Horizontal Search Members Form',
        'description' => "This widget searches over Member Titles, Locations and Member Type. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.searchbox-sitemember',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => 'Choose the options that you want to be displayed in this block.(Note:Proximity Search will not display if location field will be disabled.)',
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "profileTypeElement" => "Profile Type", "locationElement" => "Location field", "locationmilesSearch" => "Proximity Search"),
                    ),
                ),
                array(
                    'Text',
                    'textWidth',
                    array(
                        'label' => 'Width for AutoSuggest',
                        'value' => 275,
                    )
                ),
                array(
                    'Text',
                    'locationWidth',
                    array(
                        'label' => 'Width for Location field',
                        'value' => 250,
                    )
                ),
                array(
                    'Text',
                    'locationmilesWidth',
                    array(
                        'label' => 'Width for Proximity Search field',
                        'value' => 125,
                    )
                ),
                array(
                    'Text',
                    'categoryWidth',
                    array(
                        'label' => 'Width for Profile Type Filtering',
                        'value' => 150,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Search Members Form',
        'description' => 'Displays the form for searching Members on the basis of various fields and filters. Settings for this form can be configured from the Search Form Settings section.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.search-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Show Search Form',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'horizontal'
                    )
                ),
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'whatWhereWithinmile',
                    array(
                        'label' => 'Do you want to show "Who, What, Where and Within Miles" in single row and bold text label? [Note: This setting will not work when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No')
                    ),
                    'value' => 0,
                ),
                array(
                    'Radio',
                    'advancedSearch',
                    array(
                        'label' => 'Do you want to show all advanced search fields expanded [Note: This setting will not work if above setting set "No" and when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular / Recent / Random Members',
        'description' => 'Displays Members based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.recent-popular-random-members',
        'defaultParams' => array(
            'title' => 'Most Viewed Members',
            'titleCount' => true,
            'viewType' => 'listview',
            'viewtype' => 'vertical',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                $featuredSponsoredElement,
                $circularImageElement,
                $circularImageHeightElement,
                $hasPhotoElement,
                $titlePositionElement,
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Choose the View Type for members.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                        ),
                        'value' => 'listview',
                    )
                ),
                array(
                    'Radio',
                    'viewtitletype',
                    array(
                        'label' => 'Show Members (Note: This setting will work when you can choose Grid View from the above settings.)',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'vertical'
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View. [One Item / Block Width]',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View. [One Item / Block Height]',
                        'value' => '328',
                    )
                ),
                array(
                    'Select',
                    'orderby',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => array_merge($popularity_options, array('random' => 'Random')),
                        'value' => 'user_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $label,
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                    //'value' => array("addfriend", 'message')
                    )
                ),
                array(
                    'MultiCheckbox',
                    'memberInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the members in this block.',
                        'multiOptions' => array_merge($tempOtherInfoElement, array('title' => "Member Title")),
                    ),
                ),
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldTitleElement,
                $customFieldHeadingElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Recently Viewed',
        'description' => 'Displays members based on the recently viewed or recently viewed by me and other settings customizable for this widget. For the recently viewed by me setting, this widget can be placed at any page multiple times with different viewed criterion chosen for each placement whereas for the recently viewed setting it can be placed at the member profile page only.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.recently-viewed',
        'defaultParams' => array(
            'title' => 'Most Viewed Members',
            'titleCount' => true,
            'viewType' => 'listview',
            'viewtype' => 'vertical',
            'columnWidth' => '60'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                        'Select',
                        'viewed_by',
                        array(
                            'label' => 'Show Members',
                            'multiOptions' => array(
                                'viewed_by_user' => 'Recently Viewed',
                                'viewed_by_me' => 'Recently Viewed By Me',
                            ),
                            'value' => '',
                        )
                    ),
                $circularImageElement,
                array(
                    'Text',
                    'circularImageHeight',
                    array(
                        'label' => 'Circular Image Height For Grid / Icon View.',
                        'value' => '180',
                    )
                ),
                $hasPhotoElement,
                $titlePositionElement,
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Choose the View Type for members.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                            'iconview' => 'Icon View',
                        ),
                        'value' => 'listview',
                    )
                ),
                array(
                    'Radio',
                    'viewtitletype',
                    array(
                        'label' => 'Show Members (Note: This setting will work when you can choose Grid View from the above settings.)',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'vertical'
                    )
                ),
                array(
                    'Radio',
                    'siteusercoverphoto',
                    array(
                        'label' => 'Do you want to show in user coverphoto(This setting will only work with iconview)',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => '0'
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View. [One Item / Block Width]',
                        'value' => '60',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View. [One Item / Block Height]',
                        'value' => '60',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 10,
                    )
                ),
             ),
        ),
    ),
    array(
        'title' => 'Popular Members Slideshow',
        'description' => 'Displays Members based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.slideshow-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'viewType' => 'listview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                $featuredSponsoredElement,
                $circularImageElement,
                $hasPhotoElement,
                array(
                    'Select',
                    'orderby',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => array_merge($popularity_options, array('random' => 'Random')),
                        'value' => 'user_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $label,
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $statisticsElement,
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of Profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldHeadingElement,
                $customFieldTitleElement,
                array(
                    'Radio',
                    'showTitle',
                    array(
                        'label' => 'Do you want to show member Display Name?',
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'AJAX based Members Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the members on the site. You can choose to show sponsored / featured / new members in this widget from the settings of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.ajax-carousel-sitemember',
        'defaultParams' => array(
            'title' => 'AJAX based Members Carousel',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $featuredSponsoredElement,
                array(
                    'Radio',
                    'showPagination',
                    array(
                        'label' => 'Do you want to show next / previous pagination?',
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Carousel Type',
                        'multiOptions' => array(
                            '0' => 'Horizontal',
                            '1' => 'Vertical',
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height of each slideshow item.',
                        'value' => 240,
                    )
                ),
                array(
                    'Text',
                    'blockWidth',
                    array(
                        'label' => 'Enter the width of each slideshow item.',
                        'value' => 150,
                    )
                ),
                $circularImageElement,
                $circularImageHeightElement,
                $hasPhotoElement,
                $titlePositionElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of members in a Row / Column for Horizontal / Vertical Carousel Type respectively as selected by you from the above setting.',
                        'value' => 3,
                    )
                ),
                array(
                    'Select',
                    'orderby',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => array_merge($popularity_options, array('member_count' => 'Most Popular')),
                        'value' => 'user_id',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment
                    )
                ),
                $statisticsElement,
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of Profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldHeadingElement,
                $customFieldTitleElement,
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 300,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Member of the Day',
        'description' => 'Displays the Member of The day as selected by the admin from the edit setting of this widget.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.item-sitemember',
        'adminForm' => 'Sitemember_Form_Admin_Settings_Dayitem',
        'defaultParams' => array(
            'title' => 'Member of the Day',
        ),
    ),
    array(
        'title' => 'Member Profile: Left / Right Column Map',
        'description' => 'This widget displays the map showing location of the Member being currently viewed. It should be placed in the left / right column of the Advanced Members - Member Profile page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.location-sidebar-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the heigth of the map (in pixels).',
                        'value' => 200,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'User Profile: User Ratings',
        'description' => 'This widget displays the overall ratings given by members of your site on the listing being currently viewed. This widget should be placed in the right / left column on the Multiple Listing Types - Listing Profile page. (This widget will only display when you have chosen \'Yes, allow Users to only rate listings.\' value for the field \'Allow Only User Ratings\' for the associated listing type.)',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.user-ratings',
        'defaultParams' => array(
            'title' => 'User Ratings',
            'titleCount' => false,
        ),
    ),
    array(
        'title' => 'Review Profile: Review View',
        'description' => 'Displays the main Review. You can configure various setting from Edit Settings of this widget. This widget should be placed on Advanced Members - Review Profile page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-review-sitemember',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Review Profile: Breadcrumb',
        'description' => 'Displays the breadcrumb of the review based on the reviewers and the Member to which it belongs. This widget should be placed on the Advanced Members - Review Profile Page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-review-breadcrumb-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'User Profile: Owner Reviews',
        'description' => 'Displays the reviews given by the member to the other site members.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.owner-review-sitemember',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "true",
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemReviewsCount',
                    array(
                        'label' => 'Number of user reviews to show',
                        'value' => 3,
                    )
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No')
                    ),
                    'value' => 1,
                )
            ),
        ),
    ),
    array(
        'title' => 'User Profile: User Reviews',
        'description' => 'This widget forms the User Reviews tab on the Member Profile page and displays all the reviews written by the users of your site for the Member being viewed. This widget should be placed in the Tabbed Blocks area of the Member Profile page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.user-review-sitemember',
        'defaultParams' => array(
            'title' => "User Reviews",
            'titleCount' => "true",
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemProsConsCount',
                    array(
                        'label' => 'Number of reviews’ Pros and Cons to be displayed in the search results using \'Only Pros\' and \'Only Cons\' in the \'Show\' review search bar.',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'itemReviewsCount',
                    array(
                        'label' => 'Number of user reviews to show',
                        'value' => 3,
                    )
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Member Reviews: Member Reviews Statistics',
        'description' => 'Displays statistics for all the reviews written by the users of your site. This widget should be placed in the left column of the Advanced Members - Browse Member Reviews page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.reviews-statistics',
        'defaultParams' => array(
            'title' => 'Reviews Statistics',
        ),
    ),
    array(
        'title' => 'Browse Member Reviews: Reviewer Name',
        'description' => 'Displays the name of the poster.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.review-poster-name-sitemember',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Browse Member Reviews- Reviewed Members',
        'description' => 'This widget shows the reviews given by the member being currently viewed.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.reviewed-members-sitemember',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Members Options',
        'description' => 'Displays the action links for Most recommended, Top Rated, Most Reviewed and Top reviewers to the users. This widget should be placed on Left / Right column of Advanced Members - Popular Members Page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.options-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Navigation Tabs',
        'description' => "This widget displays the navigation tabs for 'Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin' having links of Browse Members, Popular Members, Top Rated Members, etc",
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.navigation-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Browse Reviews: Search Reviews Form',
        'description' => 'Displays the form for searching reviews. It is recommended to place this widget on Advanced Members - Browse Reviews page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.review-browse-search',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Member Profile: "Write a Review" Button',
        'description' => 'Displays a "Write a Review" button on Advanced Members - Member Profile page. When clicked, users will be redirected to write a review form for the member being viewed.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.review-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Member Profile: Member Rating',
        'description' => 'This widget displays the overall rating given to the Member by member of your site along with the rating parameters as configured by you from the Reviews & Ratings section in the Admin Panel. You can choose who should be able to give review from the Admin Panel. This widget should be placed in the left column on the Advanced Member - Member Profile page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.overall-ratings',
        'defaultParams' => array(
            'title' => 'Ratings',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'ratingParameter',
                    array(
                        'label' => 'Do you want to show Rating Parameters in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                $circularImageElement
            ),
        )
    ),
    array(
        'title' => 'Most Rated / Recommended / Reviewed Members',
        'description' => 'Displays Members based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.most-rated-reviewed-recommend',
        'defaultParams' => array(
            'title' => 'Top Rated Members',
            'titleCount' => true,
            'viewType' => 'listview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Choose the View Type for members.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                        ),
                        'value' => 'listview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View. [One Item / Block Width]',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View. [One Item / Block Height]',
                        'value' => '328',
                    )
                ),
                $circularImageElement,
                $circularImageHeightElement,
                array(
                    'Select',
                    'orderby',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $popularity_most_type_options,
                        'value' => 'rating_avg',
                    )
                ),
                $statisticsMostTypesElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Featured Reviews',
        'description' => 'Displays Featured Reviews as chosen by you from the Manage Ratings & Reviews section in the admin panel of this extension.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.featured-reviews',
        'defaultParams' => array(
            'title' => 'Featured Reviews',
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of reviews to show)',
                        'value' => 3,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Top Rated Members (Table View)',
        'description' => 'Displays Top Rated Members.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.top-rated-table-view',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Recommend Members (Table View)',
        'description' => 'Displays Most Recommend Members.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.most-recommend-table-view',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Reviewed Members (Table View)',
        'description' => 'Displays Most Reviewed Members.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.most-reviewed-table-view',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Top Reviewers (Table View)',
        'description' => 'Displays Top Reviewers.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.top-reviewers-table-view',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Top Rates (Table View)',
        'description' => 'Displays Top Raters.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.top-raters-table-view',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Member Profile: Profile Friends',
        'description' => 'Displays a member\'s friends on their profile.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-friends-sitemember',
        'defaultParams' => array(
            'title' => 'Friends',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No')
                    ),
                    'value' => 1,
                ),
                $circularImageElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of friends to show)',
                        'value' => 20,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Members Map View',
        'description' => 'This widget will display members based on their location on map.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.map-view-members',
        'adminForm' => array(
            'elements' => array(
                $statisticsElement,
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Custom Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                $customFieldHeadingElement,
                $customFieldTitleElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 100,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Profile Info',
        'description' => 'Displays a member\'s info (signup date, friend count, etc) on their profile.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-info',
        'defaultParams' => array(
            'title' => 'Member Info'
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Compliment Me',
        'description' => 'This widget displays the button or link to compliment. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.compliment-me',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                
                array(
                    'Text',
                    'compliment_button_title',
                    array(
                        'label' => 'Enter the text that displays on this button or link.',
                        'value' => 'Compliment Me !',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Profile Compliment Icon',
        'description' => 'This widget displays all the complimentented icons on user profile',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-compliments-icon',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'iconCount',
                    array(
                        'label' => 'Maximum Icon Count',
                        'description' => '(number of compliment icon, want to show)',
                        'value' => 10,
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Which type of view you want to show?',
                        'multiOptions' => array(
                            '0' => 'Grid View',
                            '1' => 'List View',
                        ),
                        'value' => '0',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Member Profile Compliments',
        'description' => 'This widget can be placed at the member profile page for displaying all the compliments received by that member along with Compliment Me button to give a new compliment.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.profile-compliments',
        'defaultParams' => array(
            'title' => 'Compliments',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $circularImageElement,
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                    //'value' => array("addfriend", 'message')
                    )
                ),
                array(
                        'Select',
                        'show_content',
                        array(
                            'label' => 'What do you want for view more content?',
                            'description' => '',
                            'multiOptions' => array(
                                '1' => 'Show View More Link at Bottom',
                                '2' => 'Auto Load Members on Scrolling Down'),
                            'value' => 1,
                        )
                    ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 10,
                    )
                ),
                
            ),
        ),
    ),
    array(
        'title' => 'AJAX based Compliments Members Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the members on the site having different compliments. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.ajax-carousel-compliments-sitemember',
        'defaultParams' => array(
            'title' => 'AJAX based Members Carousel',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                        'Select',
                        'compliment_category',
                        array(
                            'label' => 'Select Compliment Category',
                            'multiOptions' => $complimentCategoryOptions,
                            'value' => $complimentCategoryOptions[count($complimentCategoryOptions)-1],
                        )
                    ),
                array(
                    'Radio',
                    'showPagination',
                    array(
                        'label' => 'Do you want to show next / previous pagination?',
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Carousel Type',
                        'multiOptions' => array(
                            '0' => 'Horizontal',
                            '1' => 'Vertical',
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'itemViewType',
                    array(
                        'label' => 'Which type of view you want to show(In vertical Carousel only)',
                        'multiOptions' => array(
                            '0' => 'Grid View',
                            '1' => 'List View',
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height of each slideshow item.',
                        'value' => 240,
                    )
                ),
                array(
                    'Text',
                    'blockWidth',
                    array(
                        'label' => 'Enter the width of each slideshow item for vertical Carousel Type.',
                        'value' => 150,
                    )
                ),
                $circularImageElement,
                $circularImageHeightElement,
                $hasPhotoElement,
                $titlePositionElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of members in a Row / Column for Horizontal / Vertical Carousel Type respectively as selected by you from the above setting.',
                        'value' => 3,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment
                    )
                ),
                $statisticsElement,
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 300,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
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
        'title' => 'Recent Compliments',
        'description' => 'Displays all the recent compliments given among the users based on various customizable settings for this widget. You can place this widget multiple times on a page with different viewed criterion chosen for each placement. This widget should be placed in a column with appropriate width.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.recent-compliments',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'columnWidth' => '180',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                        'Select',
                        'compliment_category',
                        array(
                            'label' => 'Select Compliment Category',
                            'multiOptions' => array_merge(array('All'),$complimentCategoryOptions),
                            'value' => 0,
                        )
                    ),
                $circularImageElement,
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                    )
                ),
                array(
                        'Select',
                        'show_content',
                        array(
                            'label' => 'What do you want for view more content?',
                            'description' => '',
                            'multiOptions' => array(
                                '1' => 'Show View More Link at Bottom',
                                '2' => 'Auto Load Members on Scrolling Down'),
                            'value' => 1,
                        )
                    ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 10,
                    )
                ),
                
            ),
        ),
    ),
    array(
        'title' => 'Member Profile: Profile Followers',
        'description' => 'Displays a member\'s followers on their profile.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-followers-sitemember',
        'defaultParams' => array(
            'title' => 'Followers',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No')
                    ),
                    'value' => 1,
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 20,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Member Profile: Profile Following',
        'description' => 'Displays list of members a user is following on their profile.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'name' => 'sitemember.profile-following-sitemember',
        'defaultParams' => array(
            'title' => 'Following',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No')
                    ),
                    'value' => 1,
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of members to show)',
                        'value' => 20,
                    )
                ),
            ),
        ),
    ),
    array(
      'title' => 'Featured / Sponsored Member List',
      'description' => ' Displays the list of Featured or Sponsored Members for your site.',
      'category' => 'Advanced Members',
      'type' => 'widget',
      'name' => 'sitemember.list-featured',
      'defaultParams' => array(
        'title' => 'Popular Members',
        'titleCount' => true,
      ),
      'requirements' => array(
        'no-subject',
      ),
      'adminForm' => array(
        'elements' => array(
                array(
                    'Text',
                    'description',
                    array(
                        'label' => 'Description',
                        'value' => '',
                    )
                ),
                array(
                    'Select',
                    'fea_spo',
                    array(
                        'label' => 'Show Members',
                        'multiOptions' => array(
                            'featured' => 'Featured Only',
                            'sponsored' => 'Sponsored Only',
                            'fea_spo' => 'Both Featured and Sponsored'
                        ),
                        'value' => 'featured',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 5,
                    )
                ),
        ),
      ),
    ),
);
return $final_array;
