<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$enableSitealbum = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
$contentTypes = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1));
$contentTypeArray = array();
if (!empty($contentTypes)) {
    if (!empty($contentTypes))
        $contentTypeArray[] = 'All';
    $moduleTitle = '';
    foreach ($contentTypes as $contentType) {
        if ($contentType['item_title']) {
            $contentTypeArray['user'] = 'Member Videos';
            $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
        } else {
            if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                $moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
            } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                $moduleTitle = 'Multiple Listing Types Plugin Core (Reviews & Ratings Plugin)';
            }
            $explodedResourceType = explode('_', $contentType['item_type']);
            if (isset($explodedResourceType[2]) && $moduleTitle) {
                $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                $listingtypesTitle = $listingtypesTitle . ' ( ' . $moduleTitle . ' ) ';
                $contentTypeArray[$contentType['item_type']] = $listingtypesTitle;
            } else {
                $contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getModuleTitle($contentType['item_module']);
            }
        }
    }
}
if (!empty($contentTypeArray)) {
    $contentTypeElement = array(
        'Select',
        'videoType',
        array(
            'label' => 'Video Type',
            'multiOptions' => $contentTypeArray,
        ),
        'value' => '',
    );
} else {
    $contentTypeElement = array(
        'Hidden',
        'videoType',
        array(
            'label' => 'Video Type',
            'value' => 'All',
            'order' => 991,
        )
    );
}

$topNavigationLink = array(
    'video' => 'Videos',
    'channel' => 'Channels',
    'createVideo' => 'Post New Video',
    'createChannel' => 'Create New Channel'
);
if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
    $topNavigationLink = array(
        'video' => 'Videos',
        'createVideo' => 'Post New Video',
    );
}

$videoViewOption = array(
    'title' => 'Video Title',
    'owner' => 'Owner',
    'lightbox' => 'Open in Lightbox Link',
    'share' => 'Share',
    'suggest' => 'Suggest to Friends',
    'like' => 'Like ',
    'dislike' => 'Dislike',
    'favourite' => 'Favourite',
    'comment' => 'Comment',
    'view' => 'View counts',
    'report' => 'Report',
    'hashtags' => 'Hashtags'
);
$yourStuffOptions = array(
    "videocount" => "Uploaded Video's Count",
    "likedvideocount" => "Liked Video's Count",
    "favvideocount" => "Favourite Video's Count",
    "channelscreated" => "Created Channel's Count",
    "channelsliked" => "Liked Channel's Count",
    "channelsfavourited" => "Favourite Channel's Count",
);

$channelTempOtherInfoElement = array(
    "ownerName" => "Owner Name",
    "creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
);

$videoTempOtherInfoElement = array(
    "ownerName" => "Like / Comment Strip",
    "creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)",
);

$popularTypeArray = array(
    'creation' => 'Recently Created',
    'modified' => 'Recently Updated',
    'view' => 'Most Viewed',
    'like' => 'Most Liked',
    'comment' => 'Most Commented',
);
$channelPopularTypeArray = array(
    'creation' => 'Recently Created',
    'modified' => 'Recently Updated',
    'like' => 'Most Liked',
    'comment' => 'Most Commented',
);

$mychannelArray = array("creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)");

$myvideoArray = array("creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)");

$channelViewPageOptions = array('title' => 'Channel Title', 'owner' => 'Channel Owner', 'description' => 'Description', 'updateddate' => 'Updation Date', 'likeButton' => 'Like Button', 'editmenus' => 'Edit Menus');

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'))
    $channelViewPageOptions = array_merge($channelViewPageOptions, array('facebooklikebutton' => 'Facebook Like Button'));

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1)) {
    $channelTempOtherInfoElement = array_merge($channelTempOtherInfoElement, array("ratingStar" => "Rating"));
    $videoTempOtherInfoElement = array_merge($videoTempOtherInfoElement, array("ratingStar" => "Rating"));
    $popularTypeArray = array_merge($popularTypeArray, array('rating' => 'Most Rated'));
    $mychannelArray = array_merge($mychannelArray, array("ratingStar" => "Rating"));
    $myvideoArray = array_merge($myvideoArray, array("ratingStar" => "Rating"));
    $videoViewOption = array_merge($videoViewOption, array('ratings' => 'Ratings'));
    $yourStuffOptions = array_merge($yourStuffOptions, array("ratedvideocount" => "Rated Video's Count", "channelsrated" => "Rated Channel's Count",));
}
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) {
    $videoViewOption = array_merge($videoViewOption, array('watchlater' => 'Watch Later'));
    $yourStuffOptions = array_merge($yourStuffOptions, array("watchlatercount" => "Watch Later's Count",));
}
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
    $videoViewOption = array_merge($videoViewOption, array('playlist' => 'Add to Playlist',));
    $yourStuffOptions = array_merge($yourStuffOptions, array("playlistcount" => "Playlist's Count",));
}
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1)) {
    $videoViewOption = array_merge($videoViewOption, array('subscribe' => 'Subscribe'));
    $yourStuffOptions = array_merge($yourStuffOptions, array("channelsubscribed" => "Subscribed Channel's Count",));
}

$informationOptions = array("totalVideos" => "Total Videos", "creationDate" => "Creation Date", "updateDate" => "Updation Date", "likeCount" => "Likes", "commentCount" => "Comments", "socialShare" => "Social Share", "description" => "Description");

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1)) {
    $informationOptions = array_merge($informationOptions, array("tags" => "Tags"));
    $channelViewPageOptions = array_merge($channelViewPageOptions, array("tags" => "Tags"));
}

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
    $informationOptions = array_merge($informationOptions, array("categoryLink" => "Category"));
    $channelViewPageOptions = array_merge($channelViewPageOptions, array('categoryLink' => "Category"));
    $channelTempOtherInfoElement = array_merge($channelTempOtherInfoElement, array("categoryLink" => "Category"));
    $mychannelArray = array_merge($mychannelArray, array("categoryLink" => "Category"));
}

$channelElement = array(
    'MultiCheckbox',
    'channelInfo',
    array(
        'label' => 'Choose the options that you want to display for the channels in this block.',
        'multiOptions' => array_merge($channelTempOtherInfoElement, array("channelTitle" => "Channel Title", "totalVideos" => "Total Videos Count", "facebook" => "Facebook", "twitter" => "Twitter", "linkedin" => "LinkedIn", "google" => "Google +"))
    ),
);

$videoElement = array(
    'MultiCheckbox',
    'videoInfo',
    array(
        'label' => 'Choose the options that you want to display for the videos in this block.',
        'multiOptions' => array_merge($videoTempOtherInfoElement, array("videoTitle" => "Video Title", "channelTitle" => "Channel Title"))
    ),
);

$showViewMoreContent = array(
    'Select',
    'show_content',
    array(
        'label' => 'What do you want for view more content?',
        'description' => '',
        'multiOptions' => array(
            '1' => 'Pagination',
            '2' => 'Show View More Link at Bottom',
            '3' => 'Auto Load Channels on Scrolling Down'),
        'value' => 2,
    )
);
$truncationLocationElement = array(
    'Text',
    'truncationLocation',
    array(
        'label' => 'Truncation limit of location (Depend on Location)',
        'value' => 35,
    )
);

$channelTitleTruncation = array(
    'Text',
    'channelTitleTruncation',
    array(
        'label' => 'Truncation limit for channel title.',
        'value' => 100,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);

$videoTitleTruncation = array(
    'Text',
    'videoTitleTruncation',
    array(
        'label' => 'Truncation limit for video title.',
        'value' => 100,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);

$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to display videos based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$channelInfoOnHoverElement = array(
    'Radio',
    'infoOnHover',
    array(
        'label' => 'Do you want to show the channel information on mouse hover?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '1'
    )
);

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which videos will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which videos will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
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

$categories = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1));
if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) {
    $categoryElement = array();
    $subCategoryElement = array();
} else {
    $categories_prepared = array();
    if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
            $categories_prepared[$category->category_id] = $category->category_name;
        }

        $categoryElement = array(
            'Select',
            'category_id',
            array(
                'label' => 'Category',
                'multiOptions' => $categories_prepared,
                'RegisterInArrayValidator' => false,
                'onchange' => 'addOptions(this.value, "cat_dependency", "subcategory_id", 0); setHiddenValues("category_id")'
        ));

        $subCategoryElement = array(
            'Select',
            'subcategory_id',
            array(
                'RegisterInArrayValidator' => false,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => 'application/modules/Sitevideo/views/scripts/_category.tpl',
                            'class' => 'form element')))
        ));
    }
}
$hiddenCatElement = array(
    'Hidden',
    'hidden_category_id',
    array('order' => 999
        ));

$hiddenSubCatElement = array(
    'Hidden',
    'hidden_subcategory_id',
    array('order' => 998
        ));
$hiddenSubSubCatElement = array(
    'Hidden',
    'hidden_subsubcategory_id',
    array('order' => 997
        ));

$videoCategories = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1));
$videoSubCategoryElement = array();
$videoCategoryElement = array();
if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
    $videoCategoryElement = array();
    $videoSubCategoryElement = array();
} else {
    $video_categories_prepared = array();
    if (count($videoCategories) != 0) {
        $video_categories_prepared[0] = "";
        foreach ($videoCategories as $category) {
            $video_categories_prepared[$category->category_id] = $category->category_name;
        }

        $videoCategoryElement = array(
            'Select',
            'category_id',
            array(
                'label' => 'Category',
                'multiOptions' => $video_categories_prepared,
                'RegisterInArrayValidator' => false,
                'onchange' => 'addVideoOptions(this.value, "cat_dependency", "subcategory_id", 0); setVideoHiddenValues("category_id")'
        ));

        $videoSubCategoryElement = array(
            'Select',
            'subcategory_id',
            array(
                'RegisterInArrayValidator' => false,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => 'application/modules/Sitevideo/views/scripts/_videoCategory.tpl',
                            'class' => 'form element')))
        ));
    }
}
$hiddenVideoCatElement = array(
    'Hidden',
    'hidden_video_category_id',
    array('order' => 996
        ));

$hiddenVideoSubCatElement = array(
    'Hidden',
    'hidden_video_subcategory_id',
    array('order' => 995
        ));
$hiddenVideoSubSubCatElement = array(
    'Hidden',
    'hidden_video_subsubcategory_id',
    array('order' => 994
        ));

$rowHeight = array(
    'Text',
    'rowHeight',
    array(
        'label' => 'Enter the row height of each photo block. (in pixels) [This row height uses as a base height to create justified view. The resulting rows height could be slightly lesser than your entered row height.]',
        'value' => 205,
    )
);
$maxRowHeight = array(
    'Text',
    'maxRowHeight',
    array(
        'label' => 'Enter the max row height of each photo block. (in pixels) [This is the maximum row height to be allowed to create justified view. The resulting rows height could be higher / lesser than your entered maximum row height to fit any photo within limit.]',
        'value' => 0,
    )
);
$margin = array(
    'Text',
    'margin',
    array(
        'label' => 'Enter the margin between two photos block, vertically and horizontally.(in pixels)',
        'value' => 5,
    )
);
$lastRow = array(
    'Radio',
    'lastRow',
    array(
        'label' => 'Choose the option to justify the last row if the last row may not have enough photos to fill the entire width.',
        'multiOptions' => array(
            'nojustify' => 'No Justify',
            'justify' => 'Justify',
            'hide' => 'Hide'
        ),
        'value' => 'nojustify',
    )
);
$justifiedViewOption = array(
    'Radio',
    'showPhotosInJustifiedView',
    array(
        'label' => 'Do you want to show photos in justified view?',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
        'value' => 0,
        'onclick' => "(function(e,obj){hideOrShowJustifiedElements(obj.value);})(event,this)"
    )
);
$onloadScript = " <script>
 window.addEvent('domready', function () {
      var val=$$('input[name=showPhotosInJustifiedView]:checked').map(function(e) { return e.value; });
      hideOrShowJustifiedElements(val);
    });  
function hideOrShowJustifiedElements(val)
{
    if(val==1){
        if($('rowHeight-wrapper'))
        $('rowHeight-wrapper').style.display = 'block';
        
        if($('maxRowHeight-wrapper'))
        $('maxRowHeight-wrapper').style.display = 'block';
        
        if($('margin-wrapper'))
        $('margin-wrapper').style.display = 'block';
        
        if($('lastRow-wrapper'))
        $('lastRow-wrapper').style.display = 'block';
        
        if($('height-wrapper'))
        $('height-wrapper').style.display = 'none';
        
        if($('width-wrapper'))
        $('width-wrapper').style.display = 'none';
        
    } else {
        if($('height-wrapper'))
        $('height-wrapper').style.display = 'block';
        
        if($('width-wrapper'))
        $('width-wrapper').style.display = 'block';
        
        if($('rowHeight-wrapper'))
        $('rowHeight-wrapper').style.display = 'none';
        
        if($('maxRowHeight-wrapper'))
        $('maxRowHeight-wrapper').style.display = 'none';
        
        if($('margin-wrapper'))
        $('margin-wrapper').style.display = 'none';
        
        if($('lastRow-wrapper'))
        $('lastRow-wrapper').style.display = 'none';
    }
}
</script>";

return array(
    array(
        'title' => 'Videos / Channels: Navigation Tabs',
        'description' => 'Displays the Navigation tabs with links of Videos Home, Browse Videos, Channels Home, Browse Channels, My Videos, etc. This widget should be placed at the top of Videos / Channels Home page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.navigation',
        'defaultParams' => array(),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => 'Create New Channel',
        'description' => 'Displays a button “Create New Channel” to create a new channel on your website.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.create-new-channel',
        'defaultParams' => array(
            'title' => ''
        ),
    ),
    array(
        'title' => 'Channel Category Navigation Bar',
        'description' => 'Displays different categories in this block. You can configure various settings from the Edit section of this widget.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.categories-navigation',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of Categories in Grid View.',
                        'multiOptions' => array(
                            'category_name' => 'Category Name',
                            'cat_order' => 'Category order according to creation'
                        ),
                        'value' => 'cat_order',
                    )
                ),
                array(
                    'Radio',
                    'viewDisplayHR',
                    array(
                        'label' => 'Select the placement position of the navigation bar',
                        'multiOptions' => array(
                            1 => 'Horizontal',
                            0 => 'Vertical'
                        ),
                        'value' => 1,
                    )
                ),
            ))
    ),
    array(
        'title' => 'Video Category Navigation Bar',
        'description' => 'Displays different categories in this block. You can configure various settings for this widget from the Edit section.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.video-categories-navigation',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of Categories in Grid View.',
                        'multiOptions' => array(
                            'category_name' => 'Category Name',
                            'cat_order' => 'Category Order according to creation'
                        ),
                        'value' => 'cat_order',
                    )
                ),
                array(
                    'Radio',
                    'viewDisplayHR',
                    array(
                        'label' => 'Select the placement position of the navigation bar',
                        'multiOptions' => array(
                            1 => 'Horizontal',
                            0 => 'Vertical'
                        ),
                        'value' => 1,
                    )
                ),
            ))
    ),
    array(
        'title' => 'Channel Categories Banner',
        'description' => 'Displays banners for categories / sub-categories on categories / sub-categories profile page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'sitevideo.categories-banner-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Video Categories Banner',
        'description' => 'Displays banners for categories / sub-categories on categories / sub-categories profile page',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'sitevideo.video-categories-banner-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Categories Hierarchy for Channels (sidebar)',
        'description' => 'Displays the Categories / Sub-categories of Channels in an expandable form. Clicking on them will redirect the viewer to Advanced Videos - Browse Channels page displaying the list of channels created in that category. Multiple settings are available to customize this widget. ',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.categories-sidebar-sitevideo',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Categories Hierarchy for Videos (sidebar)',
        'description' => 'Displays the Categories / Sub-categories / 3rd level sub-categories of Videos in an expandable form. Clicking on them will redirect the viewer to Advanced Videos - Browse Videos page displaying the list of videos created in that category. Multiple settings are available to customize this widget.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-categories-sidebar-sitevideo',
        'defaultParams' => array(
            'title' => 'Video Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Sponsored Channel Categories',
        'description' => 'Displays the Sponsored categories / sub-categories. You can make categories as Sponsored from "Categories" section of Admin Panel.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.categories-sponsored',
        'defaultParams' => array(
            'title' => 'Sponsored Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of categories to show. Enter 0 for displaying all categories.)',
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showIcon',
                    array(
                        'label' => 'Do you want to display the icons along with the categories in this block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Sponsored Video Categories',
        'description' => 'Displays the Sponsored categories / sub-categories / 3rd level sub-categories. You can make categories as Sponsored from "Categories" section of Admin Panel.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-categories-sponsored',
        'defaultParams' => array(
            'title' => 'Sponsored Video Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of categories to show. Enter 0 for displaying all categories.)',
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showIcon',
                    array(
                        'label' => 'Do you want to display the icons along with the categories in this block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Channel Categories: Displays Category with Background Image',
        'description' => "Displays the channel category with background image and other information. This widget should be placed on the “Channel Category Profile” page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.channel-categorybanner-sitevideo',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => 'Sitevideo_Form_Admin_Widget_CategorieBannerContent',
        'autoEdit' => true
    ),
    array(
        'title' => 'Channel Categories: Displays Category with Background Image Slideshow',
        'description' => "Displays the channel categories with background image in a slideshow. This widget should be placed on the “Channel Categories Home” page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.channel-categorybanner-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => 'Sitevideo_Form_Admin_Widget_CategorieBannerContentSlideshow',
        'autoEdit' => true
    ),
    array(
        'title' => 'Channel Categories: Displays Categories / Sub-categories in Grid View',
        'description' => 'Displays Categories and Sub-categories in Grid view on “Channel Categories Home” page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.categories-grid-view',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of Categories in Grid View.',
                        'multiOptions' => array(
                            'category_name' => 'Category Name',
                            'cat_order' => 'Category Order according to creation'
                        ),
                        'value' => 'cat_order',
                    )
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories to be shown to the users even if they have 0 channels in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'showSubCategoriesCount',
                    array(
                        'label' => 'Show number of sub-categories to be shown on mouseover on a category respectively.',
                        'value' => '5',
                        'maxlength' => 1
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => 'Show channel count along with the sub-category name.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => '216',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Channel Categories: Displays Categories in Grid View with Icons',
        'description' => 'Displays channel categories with icons in grid view on Channel Categories Home page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.categories-withicon-grid-view',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of Categories in Grid View.',
                        'multiOptions' => array(
                            'category_name' => 'Category Name',
                            'cat_order' => 'Category Order according to creation'
                        ),
                        'value' => 'cat_order',
                    )
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories to be shown to the users even if they have 0 channels in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => '216',
                    )
                ),
                array(
                    'Radio',
                    'showIcon',
                    array(
                        'label' => 'Do you want to show categories icon ?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Channels Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the channels on the site. You can choose to show Featured, Sponsored Channels in this widget from the settings of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.channel-carousel',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Select',
                    'showChannel',
                    array(
                        'label' => 'Show Channels',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured only',
                            'sponsored' => 'Sponsored only',
                            'featuredSponsored' => 'Either Featured or Sponsored'
                        ),
                        'value' => 'recent',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favorite',
                            'numberOfVideos' => 'Number of videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
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
                    'showLink',
                    array(
                        'label' => "Do you want to show link ?",
                        'multiOptions' => array(1 => 'See All', 0 => 'Browse Button', '2' => 'None'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'channelWidth',
                    array(
                        'label' => 'Enter the width of each channel.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'channelHeight',
                    array(
                        'label' => 'Enter the height of each channel.',
                        'value' => 150,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'creation_date' => 'Recently Created',
                            'like' => 'Most Liked',
                            'subscribe' => 'Most Subscribed',
                            'comment' => 'Most Commented',
                            'rating' => 'Most Rated',
                            'favourite' => 'Most Favourited',
                            'random' => 'Random',
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(Transition interval between two slides in millisecs)',
                        'value' => 3500,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of channels in a Row.',
                        'description' => '[Items displayed in first block would be always less than 3 items than what you have entered here, so it is recommended always to enter +3 items count than what you want to display, e.g.- If you want to display 7 items in first block then, enter 10 counts here. The second block would be displayed with same item counts.]',
                        'value' => 7,
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Number of channels to show.',
                        'description' => '[It is recommended to enter item counts in the pattern of 7, 17, 27, 37, 47, etc. to showcase the objects in attractive manner.]',
                        'value' => 50,
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'description' => '',
                        'value' => 300,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Best Channels',
        'description' => 'This widget allows you to make any channels as “Best Channels” based on different Popularity criterias like Most Liked, Most Rated etc. You can choose the channels to be shown in this widget by editing the settings of this widget. Other settings are also available. This widget should be placed in the main container not in right / left side bar on the respective widgetized page. <br/>
[Recommendation: At least 10 channels should be present in the selected criteria to have attractive view of this widget.]',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.best-channels',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Select',
                    'showChannel',
                    array(
                        'label' => 'Show Channels',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured only',
                            'sponsored' => 'Sponsored only',
                            'featuredSponsored' => 'Either Featured or Sponsored'
                        ),
                        'value' => 'recent',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favourite',
                            'numberOfVideos' => 'Number of videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Text',
                    'channelHeight',
                    array(
                        'label' => 'Enter the height of each channel.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'channelWidth',
                    array(
                        'label' => 'Enter the width of each channel.',
                        'value' => 150,
                    )
                ),
                array(
                    'Radio',
                    'showLink',
                    array(
                        'label' => "Do you want to show Browse Button ?",
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'buttonTitle',
                    array(
                        'label' => 'Enter the browse button title',
                        'description' => '',
                        'value' => 'See All Channels',
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'creation_date' => 'Recently Created',
                            'like' => 'Most Liked',
                            'subscribe' => 'Most Subscribed',
                            'comment' => 'Most Commented',
                            'rating' => 'Most Rated',
                            'favourite' => 'Most Favourited',
                            'random' => 'Random',
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'description' => '',
                        'value' => 300,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Videos Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the videos on your site. You can choose to show featured, sponsored videos in this widget from the Edit section of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-carousel',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'Select',
                    'showVideo',
                    array(
                        'label' => 'Show Videos',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured only',
                            'sponsored' => 'Sponsored only',
                            'featuredSponsored' => 'Either Featured or Sponsored'
                        ),
                        'value' => 'recent',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'view' => 'Views',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'duration' => 'Duration',
                            'watchlater' => 'Add to Watchlater',
                            'favourite' => 'Favourite',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
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
                    'showLink',
                    array(
                        'label' => "Do you want to show link ?",
                        'multiOptions' => array(1 => 'See All', 0 => 'Browse Button', '2' => 'None'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'videoWidth',
                    array(
                        'label' => 'Enter the width of each video.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'videoHeight',
                    array(
                        'label' => 'Enter the height of each video.',
                        'value' => 150,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'creation_date' => 'Recently Created',
                            'like' => 'Most Liked',
                            'view' => 'Most Viewed',
                            'comment' => 'Most Commented',
                            'rating' => 'Most Rated',
                            'random' => 'Random',
                        ),
                        'value' => 'random',
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(Transition interval between two slides in millisecs)',
                        'value' => 3500,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of videos in a Row.',
                        'description' => '[Items displayed in first block would be always less than 3 items than what you have entered here, so it is recommended always to enter +3 items count than what you want to display, e.g.- If you want to display 7 items in first block then, enter 10 counts here. The second block would be displayed with same item counts.]',
                        'value' => 7,
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Number of videos to show.',
                        'description' => '[It is recommended to enter item counts in the pattern of 7, 17, 27, 37, 47, etc. to showcase the objects in attractive manner.]',
                        'value' => 50,
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'description' => '',
                        'value' => 300,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Best Videos',
        'description' => 'This widget allows you to make any videos as  “Best Videos” based on different Popularity criterias like Most Liked, Most Rated etc. You can choose the videos to be shown in this widget by editing the settings of this widget. This widget should be placed in the main container not in right / left side bar on the respective widgetized page. <br/>
[Recommendation: At least 10 videos should be present in the selected criteria to have attractive view of this widget.]',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.best-videos',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'Select',
                    'showVideo',
                    array(
                        'label' => 'Show Videos',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured only',
                            'sponsored' => 'Sponsored only',
                            'featuredSponsored' => 'Either Featured or Sponsored'
                        ),
                        'value' => 'recent',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'view' => 'Views',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'duration' => 'Duration',
                            'watchlater' => 'Add to Watch Later',
                            'favourite' => 'Favourite',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Text',
                    'videoHeight',
                    array(
                        'label' => 'Enter the height of each video.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'videoWidth',
                    array(
                        'label' => 'Enter the width of each video.',
                        'value' => 150,
                    )
                ),
                array(
                    'Radio',
                    'showLink',
                    array(
                        'label' => "Do you want to show Browse Button?",
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'buttonTitle',
                    array(
                        'label' => 'Enter the browse button title',
                        'description' => '',
                        'value' => 'See All Videos',
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'creation_date' => 'Recently Created',
                            'like' => 'Most Liked',
                            'view' => 'Most Viewed',
                            'comment' => 'Most Commented',
                            'rating' => 'Most Rated',
                            'random' => 'Random',
                        ),
                        'value' => 'random',
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'description' => '',
                        'value' => 300,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Special Channels',
        'description' => 'Displays channels as special channels. You can choose the channels to be shown in this widget by editing the settings of this widget. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.special-channels',
        'adminForm' => 'Sitevideo_Form_Admin_Widget_Specialchannels',
        'defaultParams' => array(
            'title' => 'Special Channels',
        ),
    ),
    array(
        'title' => 'Recent / Random / Popular Channels',
        'description' => 'Displays Channels based on the Popularity / Sorting Criteria and other settings that you have chosen for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.list-popular-channels',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Channels',
            'itemCountPerPage' => 4,
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you do not want to show title link, then simply leave this field empty.',
                        'value' => '',
                    )
                ),
                array(
                    'Radio',
                    'titleLinkPosition',
                    array(
                        'label' => 'Enter Title Link Position',
                        'description' => 'Please select the position of the title link. Setting will work only if above setting "Enter Title Link" is not empty.',
                        'multiOptions' => array(
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Channels',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'channelHeight',
                    array(
                        'label' => 'Enter height of channel thumbnail image',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'channelWidth',
                    array(
                        'label' => 'Enter width of channel thumbnail image',
                        'value' => 200,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'creation' => 'Recently Created',
                            'like' => 'Most Liked',
                            'subscribe' => 'Most Subscribed',
                            'comment' => 'Most Commented',
                            'rating' => 'Most Rated',
                            'favourite' => 'Most Favourite',
                            'random' => 'Random',
                        ),
                        'value' => 'comment',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented and Recently Created.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'channelInfo',
                    array(
                        'label' => 'Choose the options that you want to display for the channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favourite',
                            'rating' => 'Rating',
                            'numberOfVideos' => 'Number of Videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            )
        ),
    ),
    array(
        'title' => 'Channels Home: Channels Slideshow',
        'description' => 'Displays a list of all the Channels on your website. This widget should be placed on “Channels Home Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.channels-slideshow',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            "channelOption" => array("title", "owner", "like", "comment", "favourite", "numberOfVideos", "subscribe", "facebook", "twitter", "linkedin", "googleplus"),
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favourites',
                            'numberOfVideos' => 'Number of Videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Radio',
                    'showLink',
                    array(
                        'label' => 'Do you want to show link ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Radio',
                    'channelOrderby',
                    array(
                        'label' => 'Choose the criteria for Channels to be displayed in this block.',
                        'multiOptions' => array(
                            'creation_date' => 'Recently Created',
                            'liked' => 'Most Liked',
                            'rating' => 'Most Rated',
                            'featured' => 'Featured',
                            'sponsored' => 'Sponsored',
                            'random' => 'Random',
                        ),
                        'value' => 'random'
                    )
                ),
                array(
                    'Text',
                    'channelCount',
                    array(
                        'label' => "Enter number of channels that you want to show in one category.",
                        'value' => 8,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'videoOrderby',
                    array(
                        'label' => 'Choose the options that you want to display for the Videos in this block.',
                        'multiOptions' => array(
                            'creation_date' => 'Recently Created',
                            'liked' => 'Most Liked',
                            'rating' => 'Most Rated',
                            'featured' => 'Featured',
                            'sponsored' => 'Sponsored',
                            'random' => 'Random',
                        ),
                        'value' => 'random'
                    )
                ),
                array(
                    'Text',
                    'videoCount',
                    array(
                        'label' => "Enter number of videos that you want to show in one channel.",
                        'value' => 5,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'multiOptions' => array(
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Content on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of categories to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ))
    ),
    array(
        'title' => 'Browse Channels',
        'description' => 'Displays a list of all the Channels present on your website. This widget should be placed on “Browse Channels Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.browse-channels-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            "viewType" => array("videoView", "gridView", "listView"),
            "defaultViewType" => "videoView",
            "channelOption" => array("title", "owner", "like", "comment", "favourite", "numberOfVideos", "subscribe", "facebook", "twitter", "linkedin", "googleplus")
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for channels',
                        'multiOptions' => array(
                            'videoView' => 'Card view',
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                    )
                ),
                array(
                    'Select',
                    'defaultViewType',
                    array(
                        'label' => 'Select a default view type for Channels',
                        'multiOptions' => array(
                            'videoView' => 'Card view',
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                        'value' => 'videoView',
                    )
                ),
                array(
                    'Text',
                    'videoViewWidth',
                    array(
                        'label' => 'Column width for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'videoViewHeight',
                    array(
                        'label' => 'Column height for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favourites',
                            'rating' => 'Rating',
                            'numberOfVideos' => 'Number of Videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'multiOptions' => array(
                            '1' => 'Pagination',
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Content on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Channels.',
                        'multiOptions' => array(
                            'creationDate' => 'All channels in descending order of creation date.',
                            'creationDateAsc' => 'All channels in ascending order of creation date.',
                            'title' => 'All channels in alphabetical order.',
                            'sponsored' => 'Sponsored channels followed by others in ascending order of channel creation time.',
                            'featured' => 'Featured channels followed by others in ascending order of channel creation time.',
                            'sponsoredFeatured' => 'Sponsored & Featured channels followed by Sponsored channels followed by Featured channels followed by others in ascending order of channel creation time.',
                            'featuredSponsored' => 'Features & Sponsored channels followed by Featured channels followed by Sponsored channels followed by others in ascending order of channel creation time.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'titleTruncationGridNVideoView',
                    array(
                        'label' => 'Title truncation limit of Grid View and Card View',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ))
    ),
    array(
        'title' => 'Featured Channels Slideshow',
        'description' => 'Displays channels based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.featured-channels-slideshow',
        'defaultParams' => array(
            'title' => '',
            'channelOption' => array('title')
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'subscribe' => 'Subscribe Channel'
                        ),
                    )
                ),
                array(
                    'Radio',
                    'showTagline1',
                    array(
                        'label' => 'Do you want to show Tagline1 ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showTagline2',
                    array(
                        'label' => 'Do you want to show Tagline2 ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showTaglineDesc',
                    array(
                        'label' => 'Do you want to show Tagline description ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showNavigationButton',
                    array(
                        'label' => "Do you want to enable the 'Prev' and 'Next' arrows on slideshows ?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'fullWidth',
                    array(
                        'label' => "Do you want to display the slideshow in full width ?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($channelPopularTypeArray, array('random' => 'Random')),
                        'value' => 'random',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria: Most Liked, Most Commented, Most Rated and Recently Posted)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Text',
                    'slideshow_height',
                    array(
                        'label' => 'Enter the height of the slideshow (in pixels).',
                        'value' => 350,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'delay',
                    array(
                        'label' => 'What is the delay you want between slide changes (in millisecs)?',
                        'value' => 3500,
                    )
                ),
                array(
                    'Text',
                    'slidesLimit',
                    array(
                        'label' => 'How many slides do you want to show in slideshow?',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Truncation limit for channels title',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'taglineTruncation',
                    array(
                        'label' => 'Truncation limit for taglines',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Truncation limit for tagline description.',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                )
            ))
    ),
    array(
        'title' => 'Horizontal Search Form - Channels',
        'description' => "This widget performs search on the basis of Channel Titles and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the Edit section of this widget.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.searchbox-sitevideo',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => 'Choose the options that you want to display in this block.',
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'categoriesLevel',
                    array(
                        'label' => 'Select the category level belonging to which categories will be displayed in the category drop-down of this widget.',
                        'multiOptions' => array("category" => "Category", "subcategory" => "Sub-category"),
                    ),
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories to be shown to the users even if they have 0 channels in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'textWidth',
                    array(
                        'label' => 'Width for Auto Suggest',
                        'value' => 275,
                    )
                ),
                array(
                    'Text',
                    'categoryWidth',
                    array(
                        'label' => 'Width for Category Filtering',
                        'value' => 150,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Horizontal Search Videos Form',
        'description' => "This widget searches over Video Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.searchbox-video-sitevideo',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
            'loaded_by_ajax' => 0
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
                        'value' => 1,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => 'Choose the options that you want to be displayed in this block.(Note:Proximity Search will not display if location field will be disabled.)',
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering", "locationElement" => "Location field", "locationmilesSearch" => "Proximity Search"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'categoriesLevel',
                    array(
                        'label' => 'Select the category level belonging to which categories will be displayed in the category drop-down of this widget.',
                        'multiOptions' => array("category" => "Category", "subcategory" => "Sub-category", "subsubcategory" => "3rd level category"),
                    ),
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 videos in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
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
                        'label' => 'Width for Category Filtering',
                        'value' => 150,
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
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Search Form - Channels',
        'description' => 'Displays the form for searching Channels on the basis of various fields and filters.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.search-sitevideo',
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
                        'value' => 'vertical'
                    )
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 channels in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),),
        ),
    ),
    array(
        'title' => 'Popular Channel Tags',
        'description' => "Displays popular tags. You can choose to display tags based on their frequency / alphabets from the Edit section of this widget. This widget should be placed in the left / right side bar on the “Advanced Videos - Channel View” / “Advanced Videos - Browse Channels” / “Advanced Videos - Channels Home” page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.tagcloud-sitevideo-channel',
        'defaultParams' => array(
            'title' => 'Popular Tags (%s)',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Popular Tags ',
                    )
                ),
                array(
                    'Radio',
                    'orderingType',
                    array(
                        'label' => 'Do you want to show popular channel tags in alphabetical order?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of tags to show. Enter 0 for displaying all tags.)',
                        'value' => 25,
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
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Channels: Breadcrumb',
        'description' => 'Displays breadcrumb based on the categories searched in the “Search Form” widget. This widget should be placed on “Advanced Videos - Browse Channels” page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.browse-breadcrumb-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'My Channels',
        'description' => 'This page lists user\'s channels.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.my-channels-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            "topNavigationLink" => array("video", "channel", "createVideo", "createChannel"),
            "channelNavigationLink" => array("channel", "liked", "favourite", "subscribed", "rated"),
            "viewType" => array("videoView", "gridView", "listView"),
            "channelOption" => array("title", "like", "comment", "favourite", "numberOfVideos", "subscribe", "facebook", "twitter", "linkedin", "googleplus")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'topNavigationLink',
                    array(
                        'label' => 'Choose the action links that you want to display in this block.',
                        'multiOptions' => $topNavigationLink,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'channelNavigationLink',
                    array(
                        'label' => 'Choose the channel navigation links that you want to display for the channels on this page.',
                        'multiOptions' => array(
                            'channel' => 'Channels',
                            'liked' => 'Liked',
                            'favourite' => 'Favourites',
                            'subscribed' => 'Subscribed',
                            'rated' => 'Rated'
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for channels',
                        'multiOptions' => array(
                            'videoView' => 'Card view',
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                    )
                ),
                array(
                    'Select',
                    'defaultViewType',
                    array(
                        'label' => 'Select a default view type for Channels',
                        'multiOptions' => array(
                            'videoView' => 'Card view',
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                        'value' => 'videoView',
                    )
                ),
                array(
                    'Radio',
                    'searchButton',
                    array(
                        'label' => 'Do you want to show search for channels in this block ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'videoViewWidth',
                    array(
                        'label' => 'Column width for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'videoViewHeight',
                    array(
                        'label' => 'Column height for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favourite',
                            'rating' => 'Rating',
                            'numberOfVideos' => 'Number of videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'multiOptions' => array(
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Content on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 12,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => 'Channel Profile: Information (Profile Fields)',
        'description' => 'Displays the Questions added from the "Profile Fields" section in the Admin Panel. This widget should be placed in the Tabbed Blocks area of Advanced Videos - Channels Profile page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.specification-sitevideo',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'loaded_by_ajax' => 1
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
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'Channel Profile: Quick Information (Profile Fields)',
        'description' => 'Displays the Questions enabled to be shown in this widget from the \'Profile Fields\' section in the Admin Panel. This widget should be placed in the right / left column on the Advanced Videos - Channel View page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.quick-specification-sitevideo',
        'defaultParams' => array(
            'title' => 'Quick Information',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of information to show',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Channel Profile: Channel Videos',
        'description' => 'Displays the videos of a particular channel. This widget should be placed on the “Channel View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.channel-view',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Videos',
            'itemCountPerPage' => 40,
            'margin_video' => 2,
            "videoOption" => array("title", "owner", "creationDate", "view", "like", "comment", "ratings", "favourite", "watchlater", "facebook", "twitter", "linkedin", "googleplus"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'margin_video',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'videoHeight',
                    array(
                        'label' => 'Enter the height of each video.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'videoWidth',
                    array(
                        'label' => 'Enter the width of each video.',
                        'value' => 200,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array('title' => 'Title', 'owner' => 'Owner', 'creationDate' => 'Creation Date', 'view' => 'View', 'like' => 'Like', 'comment' => 'Comment', 'ratings' => 'Rating', 'favourite' => 'Favourite', 'watchlater' => 'Watch Later', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'linkedin' => 'LinkedIn', 'googleplus' => 'Google+')
                    ),
                ),
                $showViewMoreContent,
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 40,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Channel Profile: Channel Photos' . $onloadScript,
        'description' => 'This widget forms the Photos tab on the “Channel View Page” and displays the photos of the channel. This widget should be placed in the Tabbed Blocks area of the “Advanced Videos - Channel View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.channel-photos',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                $enableSitealbum ? $justifiedViewOption : array(),
                $enableSitealbum ? $rowHeight : array(),
                $enableSitealbum ? $maxRowHeight : array(),
                $enableSitealbum ? $margin : array(),
                $enableSitealbum ? $lastRow : array(),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Width of Channel Photo',
                        'value' => 80,
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Height of Channel Photo',
                        'value' => 80,
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
                        'description' => '(Number of photos to show)',
                        'value' => 20,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Channel Profile: Top Content of Channel View Page',
        'description' => 'This widget displays the various contents such as Action Links, Title, Description,Like Button to users viewing an Channel. This widget should be placed in the middile column on the Channel View page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.top-content-of-channel',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showInformationOptions',
                    array(
                        'label' => "Select the information options that you want to be available in this block.",
                        'description' => '',
                        'MultiOptions' => $channelViewPageOptions,
                    ),
                ),
                array(
                    'Radio',
                    'showLayout',
                    array(
                        'label' => 'Where do you want to show content of this widget?',
                        'multiOptions' => array(
                            'center' => 'Center',
                            'left' => 'Left'
                        ),
                        'value' => 'center',
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Channel Profile: Overview',
        'description' => 'This widget forms the Overview tab on the “Channel View Page” and displays the overview of the channel, which the owner has created using the editor in channel’s dashboard. This widget should be placed in the Tabbed Blocks area of the “Advanced Videos - Channel View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.overview-channel',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'autoEdit' => true,
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
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Channel Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the channel based on the categories. This widget should be placed on the Advanced Channel - Channel View page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Channel Profile: Channel Options',
        'description' => 'Displays the various action link options to users viewing an Channel. This widget should be placed on the Advanced Channel - Channel View page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.profile-options',
        'requirements' => array(
            'subject' => 'no-subject',
        ),
    ),
    array(
        'title' => 'Channel Profile: Channel User Ratings',
        'description' => 'This widget displays the overall ratings given by members of your site on the channel being currently viewed. This widget should be placed in the right / left column on the Channel View page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.user-ratings',
        'defaultParams' => array(
            'title' => 'User Ratings',
            'titleCount' => true,
        ),
        'adminForm' => array(
        )
    ),
    array(
        'title' => 'Channel Profile: Channel Information',
        'description' => 'Displays the category, tags, and other information about a channel. This widget should be placed on “Advanced Videos - Channel View Page” in the Tabbed Blocks area.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.information-sitevideo',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => array("totalVideos", "creationDate", "updateDate", "likeCount", "commentCount", "socialShare", "description", "tags", "categoryLink")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => $informationOptions,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Channel Profile: Channel Discussions',
        'description' => 'This widget forms the Discussions tab on the “Channel View Page” and displays the discussions of the channel. This widget should be placed in the Tabbed Blocks area of the “Advanced Videos - Channel View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.discussion-sitevideo',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
            'loaded_by_ajax' => 1
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
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'Channel Profile: Subscribers',
        'description' => 'This widget forms the Subscribers tab on the “Channel View Page” and displays the subscribers of the channel. This widget should be placed in the Tabbed Blocks area of the “Advanced Videos - Channel View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.channel-subscribers',
        'defaultParams' => array(
            'title' => 'Subscribers',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter height of the block.',
                        'value' => 20,
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter width of the block.',
                        'value' => 20,
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
                        'description' => '(Number of subscribers to show)',
                        'value' => 20,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Video Categories: Displays Categories in Grid View with Icons',
        'description' => 'Displays categories with icons in grid view on “Video Categories Home” page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-categories-withicon-grid-view',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default Ordering of Categories in Grid View.',
                        'multiOptions' => array(
                            'category_name' => 'Category Name',
                            'cat_order' => 'Category Order according to creation'
                        ),
                        'value' => 'cat_order',
                    )
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories to be shown to the users even if they have 0 videos in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => '216',
                    )
                ),
                array(
                    'Radio',
                    'showIcon',
                    array(
                        'label' => 'Do you want to show categories icon ?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Video Categories: Displays Categories / Sub-categories in Grid View',
        'description' => 'Displays categories and sub-categories in grid view on “Video Categories Home” page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-categories-grid-view',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default Ordering of Categories in Grid View.',
                        'multiOptions' => array(
                            'category_name' => 'Category Name',
                            'cat_order' => 'Category Order according to creation'
                        ),
                        'value' => 'cat_order',
                    )
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories to be shown to the users even if they have 0 videos in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'showSubCategoriesCount',
                    array(
                        'label' => 'Show number of sub-categories to be shown on mouseover on a category respectively.',
                        'value' => '5',
                        'maxlength' => 1
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => 'Show channel count along with the sub-category name.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => '216',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Video Categories: Displays Category with Background Image',
        'description' => "Displays the video category with background image and other information. This widget should be placed on the “Video Categories Home” page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-categorybanner-sitevideo',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => 'Sitevideo_Form_Admin_Widget_VideoCategorieBannerContent',
        'autoEdit' => true
    ),
    array(
        'title' => 'Video Categories: Displays Category with Background Image Slideshow',
        'description' => "Displays the video categories with background image in a slideshow. This widget should be placed on the  “Video Categories Home” page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-categorybanner-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => 'Sitevideo_Form_Admin_Widget_VideoCategorieBannerContentSlideshow',
        'autoEdit' => true
    ),
    array(
        'title' => 'Browse Videos',
        'description' => 'Displays a list of all the Videos present on your website. This widget should be placed on “Browse Videos” page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.browse-videos-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            "viewType" => array("videoView", "gridView", "listView"),
            "defaultViewType" => "videoView",
            'videoOption' => array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'playlist', 'duration', 'rating', 'facebook', 'twitter', 'linkedin', 'googleplus'),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for videos',
                        'multiOptions' => array(
                            'videoView' => 'Card view',
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                    )
                ),
                array(
                    'Select',
                    'defaultViewType',
                    array(
                        'label' => 'Select a default view type for videos',
                        'multiOptions' => array(
                            'videoView' => 'Card view',
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                        'value' => 'videoView',
                    )
                ),
                array(
                    'Text',
                    'videoViewWidth',
                    array(
                        'label' => 'Column width for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'videoViewHeight',
                    array(
                        'label' => 'Column height for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'view' => 'Views',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'location' => 'Location',
                            'duration' => 'Duration',
                            'rating' => 'Rating',
                            'watchlater' => 'Add to Watch Later',
                            'favourite' => 'Favourite',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'multiOptions' => array(
                            '1' => 'Pagination',
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Content on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default Ordering in Browse Videos.',
                        'multiOptions' => array(
                            'creationDate' => 'All videos in descending order of creation date.',
                            'creationDateAsc' => 'All videos in ascending order of creation date.',
                            'title' => 'All videos in alphabetical order.',
                            'rating' => 'All videos in most rated order.',
                            'sponsored' => 'Sponsored videos followed by others in ascending order of video creation time.',
                            'featured' => 'Featured videos followed by others in ascending order of video creation time.',
                            'sponsoredFeatured' => 'Sponsored & Featured videos followed by Sponsored videos followed by Featured videos followed by others in ascending order of video creation time.',
                            'featuredSponsored' => 'Features & Sponsored videos followed by Featured videos followed by Sponsored videos followed by others in ascending order of video creation time.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'titleTruncationGridNVideoView',
                    array(
                        'label' => 'Title truncation limit of Grid View and Card View',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ))
    ),
    array(
        'title' => 'Browse Videos: Pinboard View',
        'description' => 'Displays a list of all the videos on your site in attractive Pinboard View. You can also choose to display videos based on user’s current location by using the Edit section of this widget. It is recommended to place this widget on “Browse Video’s Pinboard View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.pinboard-browse-videos-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'videoOption' => array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'playlist', 'duration', 'rating', 'facebook', 'twitter', 'linkedin', 'googleplus'),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'like' => 'Like',
                            'comment' => 'Comment',
                            'view' => 'Views',
                            'duration' => 'Duration',
                            'rating' => 'Rating',
                            'location' => 'Location',
                        ),
                    )),
                array(
                    'Radio',
                    'userComment',
                    array(
                        'label' => 'Do you want to show user comments and enable user to post comment or not?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    ),
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => 'Do you want to show a Loading image when this widget renders on a page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One Item Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Videos displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' =>
                        array("comment" => "Comment",
                            "like" => "Like / Unlike",
                            'favourite' => 'Favourite',
                            'watchlater' => 'Add to Watch Later',
                            'facebook' => 'Facebook',
                            'twitter' => 'Twitter',
                            'linkedin' => 'LinkedIn',
                            'googleplus' => 'Google+'
                        )
                    ),
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the video images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default Ordering in Browse Videos.',
                        'multiOptions' => array(
                            'creationDate' => 'All videos in descending order of creation date.',
                            'creationDateAsc' => 'All videos in ascending order of creation date.',
                            'title' => 'All videos in alphabetical order.',
                            'rating' => 'All videos in most rated order.',
                            'sponsored' => 'Sponsored videos followed by others in ascending order of video creation time.',
                            'featured' => 'Featured videos followed by others in ascending order of video creation time.',
                            'sponsoredFeatured' => 'Sponsored & Featured videos followed by Sponsored videos followed by Featured videos followed by others in ascending order of video creation time.',
                            'featuredSponsored' => 'Features & Sponsored videos followed by Featured videos followed by Sponsored videos followed by others in ascending order of video creation time.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
                $truncationLocationElement,
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ))
    ),
    array(
        'title' => 'Browse Channels: Pinboard View',
        'description' => 'Displays a list of all the channels on your site in attractive Pinboard View. It is recommended to place this widget on “Browse Channels ‘s Pinboard View” page',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.pinboard-browse-channels-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            "channelOption" => array("title", "owner", "like", "comment", "favourite", "numberOfVideos", "subscribe", "facebook", "twitter", "linkedin", "googleplus")
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'numberOfVideos' => 'Number of Videos',
                            'like' => 'Like',
                            'comment' => 'Comment'
                        ),
                    )
                ),
                array(
                    'Radio',
                    'userComment',
                    array(
                        'label' => 'Do you want to show user comments and enable user to post comment or not?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    ),
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => 'Do you want to show a Loading image when this widget renders on a page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One Item Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the channel images without stretching them to the width of each pinboard item?',
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
                        'label' => 'Choose the action links that you want to be available for the Channels displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' =>
                        array("comment" => "Comment",
                            "like" => "Like / Unlike",
                            'favourite' => 'Favourite',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook',
                            'twitter' => 'Twitter',
                            'linkedin' => 'LinkedIn',
                            'googleplus' => 'Google+',
                            'pinit' => 'Pin it',
                        )
                    ),
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Channels.',
                        'multiOptions' => array(
                            'creationDate' => 'All channels in descending order of creation date.',
                            'creationDateAsc' => 'All channels in ascending order of creation date.',
                            'title' => 'All channels in alphabetical order.',
                            'sponsored' => 'Sponsored channels followed by others in ascending order of channel creation time.',
                            'featured' => 'Featured channels followed by others in ascending order of channel creation time.',
                            'sponsoredFeatured' => 'Sponsored & Featured channels followed by Sponsored channels followed by Featured channels followed by others in ascending order of channel creation time.',
                            'featuredSponsored' => 'Features & Sponsored channels followed by Featured channels followed by Sponsored channels followed by others in ascending order of channel creation time.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
            ))
    ),
    array(
        'title' => 'Featured Videos Slideshow',
        'description' => 'Displays video based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.featured-videos-slideshow',
        'defaultParams' => array(
            'title' => '',
            'videoOption' => array('title', 'watchlater')
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'watchlater' => 'Add to Watch Later',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'showTagline1',
                    array(
                        'label' => 'Do you want to show Tagline1 ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showTagline2',
                    array(
                        'label' => 'Do you want to show Tagline2 ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showTaglineDesc',
                    array(
                        'label' => 'Do you want to show Tagline Description ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showNavigationButton',
                    array(
                        'label' => "Do you want to enable the 'Prev' and 'Next' arrows on slideshows ?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'fullWidth',
                    array(
                        'label' => "Do you want to display the slideshow in full width ?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random')),
                        'value' => 'random',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria: Most Liked, Most Commented, Most Rated and Recently Posted.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Text',
                    'slideshow_height',
                    array(
                        'label' => 'Enter the height of the slideshow (in pixels).',
                        'value' => 350,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'delay',
                    array(
                        'label' => 'What is the delay you want between slide changes (in millisecs)?',
                        'value' => 3500,
                    )
                ),
                array(
                    'Text',
                    'slidesLimit',
                    array(
                        'label' => 'How many slides do you want to show in a slideshow?',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Truncation limit for slideshow title',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'taglineTruncation',
                    array(
                        'label' => 'Tagline truncation limit for slideshow title',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit.[Note: Enter 0 to hide the description]',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            ))
    ),
    array(
        'title' => 'Recent / Random / Popular Videos',
        'description' => 'Displays Videos based on the Popularity / Sorting Criteria and other settings that you have chosen for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.list-popular-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Videos',
            'itemCountPerPage' => 4,
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you do not want to show title link, then simply leave this field empty.',
                        'value' => '',
                    )
                ),
                array(
                    'Radio',
                    'titleLinkPosition',
                    array(
                        'label' => 'Enter Title Link Position',
                        'description' => 'Please select the position of the title link. Setting will work only if above setting "Enter Title Link" is not empty.',
                        'multiOptions' => array(
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Videos',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'videoWidth',
                    array(
                        'label' => 'Enter width of video thumbnail image',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'videoHeight',
                    array(
                        'label' => 'Enter height of video thumbnail image',
                        'value' => 200,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random')),
                        'value' => 'comment',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented and Recently Created.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'videoInfo',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'view' => 'Views',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'duration' => 'Duration',
                            'rating' => 'Rating',
                            'location' => 'Location',
                            'watchlater' => 'Add to Watchlater',
                            'favourite' => 'Favourite',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $videoTitleTruncation,
                $truncationLocationElement
            )
        ),
    ),
    array(
        'title' => 'Special Videos',
        'description' => 'Displays videos as special videos. You can choose the videos to be shown as special videos in this widget by editing the settings of this widget. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.special-videos',
        'adminForm' => 'Sitevideo_Form_Admin_Widget_Specialvideos',
        'defaultParams' => array(
            'title' => 'Special Videos',
        ),
    ),
    array(
        'title' => 'Ajax based main Videos Home Widget',
        'description' => "Contains multiple Ajax based tabs showing Recently Posted, Most Liked, Most Viewed, Most Commented videos in a block in separate ajax based tabs. You can configure various settings for this widget from the Edit section of this widget.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.recently-view-random-sitevideo',
        'defaultParams' => array(
            'title' => "",
            "viewType" => array("videoZZZview", "gridZZZview", "listZZZview"),
            "defaultViewType" => "videoZZZview",
            'videoOption' => array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'duration', 'rating', 'facebook', 'twitter', 'linkedin', 'googleplus'),
            'ajaxTabs' => array('mostZZZrecent', 'mostZZZliked', 'mostZZZviewed', 'mostZZZcommented', 'mostZZZrated', 'mostZZZfavourites', 'random')
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for videos',
                        'multiOptions' => array(
                            'videoZZZview' => 'Card view',
                            'gridZZZview' => 'Grid view',
                            'listZZZview' => 'List view',
                        ),
                    )
                ),
                array(
                    'Select',
                    'defaultViewType',
                    array(
                        'label' => 'Select a default view type for Videos',
                        'multiOptions' => array(
                            'videoZZZview' => 'Card view',
                            'gridZZZview' => 'Grid view',
                            'listZZZview' => 'List view',
                        ),
                        'value' => 'videoZZZview',
                    )
                ),
                array(
                    'Text',
                    'videoViewWidth',
                    array(
                        'label' => 'Column width for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'videoViewHeight',
                    array(
                        'label' => 'Column height for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'view' => 'Views',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'location' => 'Location',
                            'duration' => 'Duration',
                            'rating' => 'Rating',
                            'watchlater' => 'Add to Watch Later',
                            'favourite' => 'Favourite',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array(
                            "mostZZZrecent" => "Most Recent",
                            "mostZZZliked" => "Most Liked",
                            "mostZZZviewed" => "Most Viewed",
                            "mostZZZcommented" => "Most Commented",
                            "mostZZZrated" => "Most Rated",
                            "mostZZZfavourites" => "Most Favourited",
                            "random" => "Random"
                        )
                    )
                ),
                array(
                    'Text',
                    'recent_order',
                    array(
                        'label' => 'Most Recent Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'liked_order',
                    array(
                        'label' => 'Most Liked Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'viewed_order',
                    array(
                        'label' => 'Most Viewed Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'commented_order',
                    array(
                        'label' => 'Most Commented Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'rated_order',
                    array(
                        'label' => 'Most Rated Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'favourites_order',
                    array(
                        'label' => 'Most Favourited Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'random_order',
                    array(
                        'label' => 'Random Tab (order)',
                        'value' => 7
                    ),
                ),
                array(
                    'Radio',
                    'showViewMore',
                    array(
                        'label' => 'Show "View More".',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show in card view)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'gridItemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show in grid view)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'listItemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show in list view)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'titleTruncationGridNVideoView',
                    array(
                        'label' => 'Title truncation limit of Grid View and Card View',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Ajax based main Channels Home Widget',
        'description' => "Contains multiple Ajax based tabs showing Recently Posted, Most Liked, Most Commented channels in a block in separate ajax based tabs respectively. You can configure various settings for this widget from the Edit settings.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.channel-recently-view-random-sitevideo',
        'defaultParams' => array(
            'title' => "",
            "viewType" => array("videoZZZview", "gridZZZview", "listZZZview"),
            "defaultViewType" => "videoZZZview",
            'channelOption' => array("title", "owner", "like", "comment", "favourite", "numberOfVideos", "rating", "subscribe", "facebook", "twitter", "linkedin", "googleplus"),
            'ajaxTabs' => array('mostZZZrecent', 'mostZZZliked', 'mostZZZsubscribed', 'mostZZZcommented', 'mostZZZrated', 'mostZZZfavourites', 'random')
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for channels',
                        'multiOptions' => array(
                            'videoZZZview' => 'Card view',
                            'gridZZZview' => 'Grid view',
                            'listZZZview' => 'List view',
                        ),
                    )
                ),
                array(
                    'Select',
                    'defaultViewType',
                    array(
                        'label' => 'Select a default view type for channels',
                        'multiOptions' => array(
                            'videoZZZview' => 'Card view',
                            'gridZZZview' => 'Grid view',
                            'listZZZview' => 'List view',
                        ),
                        'value' => 'videoZZZview',
                    )
                ),
                array(
                    'Text',
                    'videoViewWidth',
                    array(
                        'label' => 'Column width for Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'videoViewHeight',
                    array(
                        'label' => 'Column height For Card View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'gridViewHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'channelOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Channels in this block.',
                        'multiOptions' => array(
                            'title' => 'Channel Title',
                            'owner' => 'Owner',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'favourite' => 'Favourites',
                            'rating' => 'Rating',
                            'numberOfVideos' => 'Number of Videos',
                            'subscribe' => 'Subscribe',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array(
                            "mostZZZrecent" => "Most Recent",
                            "mostZZZliked" => "Most Liked",
                            "mostZZZsubscribed" => "Most Subscribed",
                            "mostZZZcommented" => "Most Commented",
                            "mostZZZrated" => "Most Rated",
                            "mostZZZfavourites" => "Most Favourited",
                            "random" => "Random"
                        )
                    )
                ),
                array(
                    'Text',
                    'recent_order',
                    array(
                        'label' => 'Most Recent Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'liked_order',
                    array(
                        'label' => 'Most Liked Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'subscribed_order',
                    array(
                        'label' => 'Most Subscribed Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'commented_order',
                    array(
                        'label' => 'Most Commented Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'rated_order',
                    array(
                        'label' => 'Most Rated Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'favourites_order',
                    array(
                        'label' => 'Most Favourited Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'random_order',
                    array(
                        'label' => 'Random Tab (order)',
                        'value' => 7
                    ),
                ),
                array(
                    'Radio',
                    'showViewMore',
                    array(
                        'label' => 'Show "View More".',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show in card view)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'gridItemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show in grid view)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'listItemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show in list view)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'titleTruncationGridNVideoView',
                    array(
                        'label' => 'Title truncation limit of Grid View and Card View',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Popular Video Tags',
        'description' => "Displays popular tags. You can choose to display tags based on their frequency / alphabets from the Edit Settings of this widget. This widget should be placed in the left / right side bar on the 'Advanced Videos - Video View' / 'Advanced Videos - Browse Video' / 'Advanced Videos - Videos Home' pages.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.tagcloud-sitevideo-video',
        'defaultParams' => array(
            'title' => 'Popular Tags (%s)',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Popular Tags',
                    )
                ),
                array(
                    'Radio',
                    'orderingType',
                    array(
                        'label' => 'Do you want to show popular video tags in alphabetical order?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of tags to show. Enter 0 for displaying all tags.)',
                        'value' => 25,
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
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Search Form - Videos',
        'description' => 'Displays the form for searching Videos on the basis of various fields and filters.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.search-video-sitevideo',
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
                        'value' => 'vertical'
                    )
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 videos in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
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
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'AJAX based Horizontal / Vertical Videos Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the videos on the site.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.horizontal-vertical-videos',
        'defaultParams' => array(
            'title' => 'Videos Carousel',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $videoCategoryElement,
                $videoSubCategoryElement,
                $hiddenVideoCatElement,
                $hiddenVideoSubCatElement,
                $hiddenVideoSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array('title' => 'Title', 'owner' => 'Owner', 'creationDate' => 'Creation Date', 'view' => 'View', 'like' => 'Like', 'comment' => 'Comment')
                    ),
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
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of videos in a Row / Column for Horizontal / Vertical Carousel Type respectively as selected by you from the above setting.',
                        'value' => 3,
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default Ordering in Browse Videos.',
                        'multiOptions' => array(
                            'creationDate' => 'All videos in descending order of creation date.',
                            'creationDateAsc' => 'All videos in ascending order of creation date.',
                            'title' => 'All videos in alphabetical order.',
                            'rating' => 'All videos in most rated order.',
                            'sponsored' => 'Sponsored videos followed by others in ascending order of video creation time.',
                            'featured' => 'Featured videos followed by others in ascending order of video creation time.',
                            'sponsoredFeatured' => 'Sponsored & Featured videos followed by Sponsored videos followed by Featured videos followed by others in ascending order of video creation time.',
                            'featuredSponsored' => 'Features & Sponsored videos followed by Featured videos followed by Sponsored videos followed by others in ascending order of video creation time.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 300,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'My Videos',
        'description' => "This page lists user's videos.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.my-videos-sitevideo',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'My Videos',
            'itemCountPerPage' => 12,
            'topNavigationLink' => array('video', 'channel', 'createVideo', 'createChannel'),
            'videoNavigationLink' => array('video', 'playlist', 'watchlater', 'liked', 'favourite', 'rated'),
            'viewType' => array('videoView', 'gridView', 'listView'),
            'videoOption' => array('title', 'creationDate', 'view', 'like', 'comment', 'favourite', 'watchlater', 'playlist', 'duration', 'rating', 'facebook', 'twitter', 'linkedin', 'googleplus'),
        ),
        'adminForm' => 'Sitevideo_Form_Admin_Widget_Content',
        'autoEdit' => true
    ),
    array(
        'title' => 'Like Button for Video',
        'description' => 'This is the Like Button for videos. It should be placed on the Video View Page if needed.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.like-button',
        'defaultParams' => array(
            'titleCount' => false,
        ),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => 'Play All Videos',
        'description' => 'This widget is used to play all videos of a playlist. It should be placed on the "Playlist Play All Videos Page".',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.playlist-playall',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'playlistOptions',
                    array(
                        'label' => 'Do you want to display playlist title above the video player ?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Video Player Height',
                        'description' => 'Enter the height of video player (in pixels).'
                    ),
                    'value' => '540',
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit',
                        'value' => 35,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Video View',
        'description' => 'This widget is used to play a video on a page. It should be placed on the “Video View Page”.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.video-view',
        'defaultParams' => array(
            'title' => '',
            'viewOptions' => array("title", "owner", "subscribe", "ratings", "lightbox", "playlist", "watchlater", "share", "suggest", "like", "dislike", "comment", "view", "report", "hashtags"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'viewOptions',
                    array(
                        'label' => 'Choose the action links that you want to display for the Videos.',
                        'multiOptions' => array_merge($videoViewOption, array('download' => 'Download Video (Only for videos added from My computer)')),
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Video Player Width',
                        'description' => 'Enter the width (in pixel) of Video Player.'
                    ),
                    'value' => '0',
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Video Player Height',
                        'description' => 'Enter the height (in pixel) of Video Player.'
                    ),
                    'value' => '540',
                ),
            ),
        ),
    ),
    array(
        'title' => 'Video Profile: Owners',
        'description' => "Displays owners of the video being currently viewed. This widget should be placed in the left / right side bar on the Advanced Videos - Videos Profile page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.owned-by-sitevideo',
        'defaultParams' => array(
            'title' => 'Owned By',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'People Who Like',
        'description' => "Displays list of people who like this channel / video. This widget should be placed in the left / right side bar on the respective widgetized page. ",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.people-who-like',
        'defaultParams' => array(
            'title' => 'People who like ',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count

                                    (number of items to show)',
                    ),
                    'value' => 9,
                ),
            ),
        ),
    ),
    array(
        'title' => 'People Who Favourite',
        'description' => "Displays list of people who favourited this video / channel. This widget should be placed in the left / right side bar on Video / Channel Profile page. ",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.people-who-favourite',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count
                                    (Number of items to show)',
                    ),
                    'value' => 9,
                ),
            ),
        ),
    ),
    array(
        'title' => 'Your Stuff',
        'description' => 'Choose the stuff that you want to display in this block. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.your-stuff',
        'autoEdit' => true,
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to display for the channel/video in this block.',
                        'multiOptions' => $yourStuffOptions
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Discussion Topic View: Discussion Topic',
        'description' => "Displays channel discussion topic currently being viewed. This widget should be placed on the “Advanced Videos - Discussion Topic View Page”.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.discussion-content',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'postorder',
                    array(
                        'label' => 'Select the order of posts to be displayed in this block.',
                        'multiOptions' => array(
                            1 => 'Newer to older',
                            0 => 'Older to newer'
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Content Type: Profile Videos',
        'description' => "Displays a list of videos in the content being currently viewed. You can manage the content types for which video owners will be able to manage videos from the Manage Modules section of Advanced Videos plugin. This widget should be placed on content's Profile page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        // 'autoEdit' => true,
        'name' => 'sitevideo.contenttype-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Videos',
            'itemCountPerPage' => 40,
            'margin_video' => 2,
            "videoOption" => array("title", "owner", "creationDate", "view", "like", "comment", "ratings", "favourite", "watchlater", "facebook", "twitter", "linkedin", "googleplus"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'margin_video',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements can be set in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'videoHeight',
                    array(
                        'label' => 'Enter the height of each video.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'videoWidth',
                    array(
                        'label' => 'Enter the width of each video.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column height for this block.',
                        'value' => '200',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array('title' => 'Title', 'owner' => 'Owner', 'creationDate' => 'Creation Date', 'view' => 'View', 'like' => 'Like', 'comment' => 'Comment', 'ratings' => 'Rating', 'favourite' => 'Favourite', 'watchlater' => 'Watch Later', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'linkedin' => 'LinkedIn', 'googleplus' => 'Google+')
                    ),
                ),
                $showViewMoreContent,
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 40,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit.',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'My Playlists Page',
        'description' => 'This page lists user\'s playlists. ',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.my-playlists-sitevideo',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'My Playlists',
            'itemCountPerPage' => 4,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'playlistOrder',
                    array(
                        'label' => 'Default ordering of playlist.',
                        'multiOptions' => array(
                            'random' => 'Random Playlists',
                            'creation_date' => 'Recent Playlists'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Radio',
                    'playlistVideoOrder',
                    array(
                        'label' => 'Default ordering of videos.',
                        'multiOptions' => array(
                            'random' => 'Random Videos',
                            'creation_date' => 'Recent Videos'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Text',
                    'playlistGridViewWidth',
                    array(
                        'label' => 'Column width for Playlist Grid View',
                        'value' => 150,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                array(
                    'Text',
                    'playlistGridViewHeight',
                    array(
                        'label' => 'Column height for Playlist Grid View',
                        'value' => 150,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                $showViewMoreContent,
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                array(
                    'Text',
                    'videoShowLinkCount',
                    array(
                        'label' => 'How many video links do you want to show in a Playlist',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Playlist Profile Page',
        'description' => 'Displays details of a playlist. ',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.playlist-view',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Playlist View',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default order videos in a playlist.',
                        'multiOptions' => array(
                            'random' => 'Random Videos',
                            'creation_date' => 'Recent Videos'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                $showViewMoreContent,
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Browse Playlists',
        'description' => "Displays a list of all the playlists present on your website. This widget should be placed on “Browse Playlists” page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.browse-playlist',
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for playlists',
                        'multiOptions' => array(
                            'gridView' => 'Grid view',
                            'listView' => 'List view',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'viewFormat',
                    array(
                        'label' => 'Select a default view type for playlists',
                        'multiOptions' => array(
                            'gridView' => 'Grid View',
                            'listView' => 'List View',
                        ),
                    )
                ),
                array(
                    'Text',
                    'playlistGridViewWidth',
                    array(
                        'label' => 'Column width for Grid View.',
                    )
                ),
                array(
                    'Text',
                    'playlistGridViewHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'playlistOption',
                    array(
                        'label' => 'Choose the options that you want to display for the playlists in this block.',
                        'multiOptions' => array(
                            'owner' => 'Owner',
                            'videosCount' => 'Videos Count',
                            'like' => 'Like Button'
                        ),
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'description' => '',
                        'multiOptions' => array(
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Playlists on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit.',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Horizontal Search Form - Playlists',
        'description' => 'This widget performs search on the basis of Playlist Title, Video Title and Member\'s Name.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.playlist-search',
        'autoEdit' => true,
        'adminForm' => array(
            array(
                'Text',
                'title',
                array(
                    'label' => 'Title',
                )
            ),
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => 'Choose the options that you want to display in this block.',
                        'multiOptions' => array(
                            'playlistelement' => 'Playlist Title',
                            'videoelement' => 'Video Title',
                            'membername' => 'Member\'s Name',
                        )
                    ),
                ),
                array(
                    'Text',
                    'playlistWidth',
                    array(
                        'label' => 'Width of Playlist title search box',
                    )
                ),
                array(
                    'Text',
                    'videoWidth',
                    array(
                        'label' => 'Width for Video title search box',
                    )
                ),
                array(
                    'Text',
                    'memberNameWidth',
                    array(
                        'label' => 'Width for Member\'s name search box',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Playlist Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the playlist. This widget should be placed on the Advanced Channel - Playlists Profile page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.playlist-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'My Subscriptions',
        'description' => 'This page lists user\'s subscriptions. ',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.my-subscriptions-sitevideo',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'My Subscriptions',
            'itemCountPerPage' => 15,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of subscription.',
                        'multiOptions' => array(
                            'random' => 'Random Subscriptions',
                            'creation_date' => 'Recent Subscriptions'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 15,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                $showViewMoreContent,
            )
        ),
    ),
    array(
        'title' => 'My Watch Laters Page',
        'description' => 'This page lists user\'s watch later videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.my-watchlaters-sitevideo',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'My Watch Later',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'watchlaterOrder',
                    array(
                        'label' => 'Default order of Watch Later.',
                        'multiOptions' => array(
                            'random' => 'Random Watch Later',
                            'creation_date' => 'Recent Watch Later'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                $showViewMoreContent,
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Browse Videos Location',
        'description' => "Displays the form for searching Videos corresponding to location on the basis of various filters and a list of all the videos having location entered corresponding to them on the site. This widget must be placed on Advanced Videos - Browse Videos' Location page.",
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.browselocation-sitevideo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 videos in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
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
                        'value' => 1,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'videoOption',
                    array(
                        'label' => 'Choose the options that you want to display for the videos in this block.',
                        'multiOptions' => array(
                            'title' => 'Video Title',
                            'owner' => 'Owner',
                            'creationDate' => 'Creation Date',
                            'view' => 'Views',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'location' => 'Location',
                            'duration' => 'Duration',
                            'rating' => 'Rating',
                            'watchlater' => 'Add to Watch Later',
                            'favourite' => 'Favourite',
                            'facebook' => 'Facebook [Social Share Link]',
                            'twitter' => 'Twitter [Social Share Link]',
                            'linkedin' => 'LinkedIn [Social Share Link]',
                            'googleplus' => 'Google+ [Social Share Link]'
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Post New Video',
        'description' => 'This widget displays the button or link to Post New Video. This widget should be placed in the left / right side bar on the respective widgetized page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitevideo.post-new-video',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'upload_button' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'upload_button',
                    array(
                        'label' => 'How do you want to display Post New Video action in this widget ?',
                        'multiOptions' => array(
                            '1' => 'As a button',
                            '0' => 'As a link',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'upload_button_title',
                    array(
                        'label' => 'Enter the text that displays on this button or link.',
                        'value' => 'Post New Video',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Share via Badge',
        'description' => 'This widget displays the button to share video via badge. This widget should be placed in the left / right side bar on the channel profile page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.share-via-badge',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Channels and Videos HTML Block',
        'description' => 'This widget shows the HTML title and description of Channels and Videos. Following are the options to configure important buttons that are displayed below the HTML title and description block. [If you want to edit the HTML title and description then, please <a target="_blank" href="admin/sitevideo/html-block">click here</a>. ]',
        'decorators' => array('ViewHelper', array('Description', array('placement' => 'PREPEND', 'escape' => false))),
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'sitevideo.html-block-videos',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
);
