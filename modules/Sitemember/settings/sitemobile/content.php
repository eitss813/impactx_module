<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$coreModules = Engine_Api::_()->getDbtable('modules', 'core');

if ($coreModules->isModuleEnabled('siteverify')) {
  $label = 'Popularity Duration (This duration will be applicable to these Popularity Criteria: Most Liked, Most Verified, Recently Signups and Recently Updated.)';
} else {
  $label = 'Popularity Duration (This duration will be applicable to these Popularity Criteria: Most Liked, Recently Signups and Recently Updated.)';
}



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
        'value' => '0'
    )
);

$customFieldTitleElement = array(
    'Radio',
    'custom_field_title',
    array(
        'label' => 'Do you want to show  “Title" of custom field?',
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

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proximity.search.kilometer', 0)) {
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

if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') != 3) {
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')) {
  $tempOtherInfoElement = array_merge($tempOtherInfoElement, array("reviewCount" => "Reviews"));
} elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0) {
  $tempOtherInfoElement = array_merge($tempOtherInfoElement, array("reviewCount" => "Votes"));
}
}

$statisticsPhotoElement = array(
    "featuredLabel" => "Featured Label",
    "sponsoredLabel" => "Sponsored Label",
);


$profileInformationElement = array("location" => "Location", "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)", "viewCount" => "Views", "likeCount" => "Likes", "memberCount" => "Friends", "mutualFriend" => "Mutual Friends", "memberStatus" => "Member Status (Online)", "joined" => "Joined (Duration after signed up)", "networks" => "Networks", "profileField" => "Profile Fields");

$show_buttons = array('facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it');

$LinksElelment = array("addfriend" => "Add Friend", 'message' => "Message");

//if ($coreModules->isModuleEnabled('sitelike')) {
//  $profileInformationElement['likebutton'] = "Like Button";
//  $show_buttons['like'] = "Like / Unlike";
//  $LinksElelment['likebutton'] = "Like Button";
//}
//
//if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke')) {
//  $LinksElelment['poke'] = "Poke";
//}
//if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion')) {
//  $LinksElelment['suggestion'] = 'Suggest to friend';
//}

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



if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')) {
  $statisticsMostTypesElement = array(
      'MultiCheckbox',
      'memberInfo',
      array(
          'label' => 'Choose the options that you want to be displayed for the members in this block.',
          'multiOptions' => array('ratingStar' => "Rating", "reviewCount" => "Reviews", 'recommendCount' => "Recommend")
      ),
  );
} elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0) {
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
        'title' => 'Browse Members',
        'description' => 'Displays a list of all the members on your site. This widget should be placed on Advanced Members - Browse Members page.',
        'category' => 'Advanced Members',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitemember.browse-members-sitemember',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'columnWidth' => '180',
            'truncationGrid' => 90
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'links',
                    array(
                        'label' => 'Choose the links that you want to be displayed for the members in this block.',
                        'multiOptions' => $LinksElelment,
                        'value' => array("addfriend", 'message')
                    )
                ),
                $hasPhotoElement,
//                array(
//                    'Radio',
//                    'showDetailLink',
//                    array(
//                        'label' => 'Do you want to display "Details" link in list view ?',
//                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
//                        'value' => 1,
//                    )
//                ),
//                $browseStatisticsElement,
//                array(
//                    'Text',
//                    'customParams',
//                    array(
//                        'label' => 'Custom Profile Fields',
//                        'description' => '(number of profile fields to show.)',
//                        'value' => 5,
//                    )
//                ),
//                $customFieldTitleElement,
//                $customFieldHeadingElement,
//                $titlePositionElement,
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
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    )
);
return $final_array;
?>