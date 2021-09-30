<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$url = $view->url(array('action' => 'browse'), "sitealbum_general", true);

$albumTempOtherInfoElement = array(
    "ownerName" => "Owner Name",
    "creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)",
);

$photoTempOtherInfoElement = array(
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

$myalbumArray = array("creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)");

$myphotoArray = array("creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)");
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
      
var val=$$('input[name=infoOnHover]:checked').map(function(e) { return e.value; });
      hideOrShowHowerlements(val);
});

function hideOrShowHowerlements(val) {
if(val == 0) {
    if($('albumColumnHeight-wrapper'))
            $('albumColumnHeight-wrapper').style.display = 'block';
    } 
    else {
    if($('albumColumnHeight-wrapper'))
            $('albumColumnHeight-wrapper').style.display = 'none';
    }
}

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
				if($('photoHeight-wrapper'))
        $('photoHeight-wrapper').style.display = 'none';
				if($('photoWidth-wrapper'))
        $('photoWidth-wrapper').style.display = 'none';
        if($('photoColumnHeight-wrapper'))
        $('photoColumnHeight-wrapper').style.display = 'none';
    } else {
			if($('photoHeight-wrapper'))
        $('photoHeight-wrapper').style.display = 'block';
				if($('photoWidth-wrapper'))
        $('photoWidth-wrapper').style.display = 'block';
				if($('rowHeight-wrapper'))
        $('rowHeight-wrapper').style.display = 'none';
				if($('maxRowHeight-wrapper'))
        $('maxRowHeight-wrapper').style.display = 'none';
				if($('margin-wrapper'))
        $('margin-wrapper').style.display = 'none';
				if($('lastRow-wrapper'))
        $('lastRow-wrapper').style.display = 'none';
        if($('photoColumnHeight-wrapper'))
        $('photoColumnHeight-wrapper').style.display = 'block';
    }
}
</script>";
$albumViewPageOptions = array('title' => 'Album Title', 'owner' => 'Albumn Owner', 'description' => 'Description', 'location' => 'Location', 'updateddate' => 'Updated Date', 'likeButton' => 'Like Button', 'editmenus' => 'Edit Menus');

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'))
    $albumViewPageOptions = array_merge($albumViewPageOptions, array('facebooklikebutton' => 'Facebook Like Button'));

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {
    $albumTempOtherInfoElement = array_merge($albumTempOtherInfoElement, array("ratingStar" => "Rating"));
    $photoTempOtherInfoElement = array_merge($photoTempOtherInfoElement, array("ratingStar" => "Rating"));
    $popularTypeArray = array_merge($popularTypeArray, array('rating' => 'Most Rated'));
    $myalbumArray = array_merge($myalbumArray, array("ratingStar" => "Rating"));
    $myphotoArray = array_merge($myphotoArray, array("ratingStar" => "Rating"));
}

$informationOptions = array("totalPhotos" => "Total Photos", "creationDate" => "Creation Date", "updateDate" => "Updated Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "location" => "Location", "directionLink" => "Get Directions Link (Dependent on Location)", "likeButton" => "Like Button", "socialShare" => "Social Share");

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0)) {
    $informationOptions = array_merge($informationOptions, array("tags" => "Tags"));
    $albumViewPageOptions = array_merge($albumViewPageOptions, array("tags" => "Tags"));
}

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
    $informationOptions = array_merge($informationOptions, array("categoryLink" => "Category"));
    $albumViewPageOptions = array_merge($albumViewPageOptions, array('categoryLink' => "Category"));
    $albumTempOtherInfoElement = array_merge($albumTempOtherInfoElement, array("categoryLink" => "Category"));
    $myalbumArray = array_merge($myalbumArray, array("categoryLink" => "Category"));
}

$albumElement = array(
    'MultiCheckbox',
    'albumInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "facebook" => "Facebook [Social Share Link]", "twitter" => "Twitter [Social Share Link]", "linkedin" => "LinkedIn [Social Share Link]", "google" => "Google + [Social Share Link]"))
    ),
);

$albumElementWithoutShareLinks = array(
    'MultiCheckbox',
    'albumInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title"))
    ),
);

$photoElement = array(
    'MultiCheckbox',
    'photoInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the photos in this block.',
        'multiOptions' => array_merge($photoTempOtherInfoElement, array("photoTitle" => "Photo Title", "albumTitle" => "Album Titile"))
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
            '3' => 'Auto Load Albums on Scrolling Down'),
        'value' => 2,
    )
);

$showViewMorePhotoContent = array(
    'Select',
    'show_content',
    array(
        'label' => 'What do you want for view more content?',
        'description' => '',
        'multiOptions' => array(
            '1' => 'Pagination',
            '2' => 'Show View More Link at Bottom',
            '3' => 'Auto Load Photos on Scrolling Down'),
        'value' => 2,
    )
);
$truncationLocationElement = array(
    'Text',
    'truncationLocation',
    array(
        'label' => 'Truncation Limit of Location (Depend on Location)',
        'value' => 35,
    )
);

$albumTitleTruncation = array(
    'Text',
    'albumTitleTruncation',
    array(
        'label' => 'Truncation limit for album title.',
        'value' => 100,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);

$photoTitleTruncation = array(
    'Text',
    'photoTitleTruncation',
    array(
        'label' => 'Truncation limit for photo title.',
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
        'label' => 'Do you want to display albums / photos based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$detactLocationPhotoElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to display photos based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$albumInfoOnHoverElement = array(
    'Radio',
    'infoOnHover',
    array(
        'label' => 'Do you want to show above selected options of album information on hover?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '1',
        'onclick' => "(function(e,obj){hideOrShowHowerlements(obj.value);})(event,this)"
    ),
     
);

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which albums will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which albums will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}


if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0)) {
    $locationPhotoDescription = "Choose the kilometers within which photos will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationPhotoDescription = "Choose the miles within which photos will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}

$defaultLocationPhotoDistanceElement = array(
    'Select',
    'defaultLocationDistance',
    array(
        'label' => $locationPhotoDescription,
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

$categories = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1));
if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
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
                'onchange' => 'subcategories(this.value, "", 0); setHiddenValues("category_id")'
        ));

        $subCategoryElement = array(
            'Select',
            'subcategory_id',
            array(
                'RegisterInArrayValidator' => false,
                'decorators' => array(array('ViewScript', array(
                            'viewScript' => 'application/modules/Sitealbum/views/scripts/_category.tpl',
                            'class' => 'form element')))
        ));
    }
}
$hiddenCatElement = array(
    'Hidden',
    'hidden_category_id',
    array('order' => 1001
        ));

$hiddenSubCatElement = array(
    'Hidden',
    'hidden_subcategory_id',
    array('order' => 1002
        ));

return array(
    array(
        'title' => 'Albums Navigation Tabs',
        'description' => 'Displays the Navigation tabs having links of Albums Home, Browse Albums, My Albums, etc. This widget should be placed at the top of Albums Home page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.navigation',
        'defaultParams' => array(),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => 'Featured Albums Slideshow',
        'description' => 'Displays albums based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.featured-albums-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Albums',
            'itemCountPerPage' => 4,
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Albums',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random', 'photos' => 'Most Photos')),
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
                $albumElement,
                array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you do not want to show title link, then simply leave this field empty.',
                        'value' => '<a href="' . $url . '">Explore Albums »</a>',
                    )
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => 'Description Truncation Limit.[Note: Enter 0 to hide the description.]',
                        'value' => 150,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement
            )
        ),
    ),
    array(
        'title' => 'Album of the Day',
        'description' => 'Displays the Album of the Day as selected by the Admin from the widget settings section of Advanced Photo Albums Plugin.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.album-of-the-day',
        'defaultParams' => array(
            'title' => 'Album of the Day'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of album photo.',
                        'value' => 255,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of album photo.',
                        'value' => 237,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count"))
                    ),
                ),
                $albumInfoOnHoverElement,
                $truncationLocationElement,
                $albumTitleTruncation,
            )
        ),
    ),
    array(
        'title' => 'Photo of the Day',
        'description' => 'Displays the Photo of the Day as selected by the Admin from the widget settings section of Advanced Photo Albums Plugin.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.photo-of-the-day',
        'defaultParams' => array(
            'title' => 'Photo of the Day'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of photo.',
                        'value' => 200,
                    )
                ),
                $photoElement,
                $truncationLocationElement,
                $photoTitleTruncation,
            )
        ),
    ),
    array(
        'title' => 'Featured Photos Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the photos on the site. You can choose to show featured photos in this widget from the settings of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.featured-photos-carousel',
        'defaultParams' => array(
            'title' => 'Featured Photos',
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Photos',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
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
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height for this block.',
                        'value' => 240,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of photos in a Row / Column for Horizontal / Vertical Carousel Type respectively as selected by you from the above setting.',
                        'value' => 3,
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
                $photoElement,
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 300,
                    )
                ),
                $photoTitleTruncation,
                $truncationLocationElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Ajax based Tabbed widget for Albums',
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented, Most Rated and Featured Albums in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Advanced Albums Plugin.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.list-albums-tabs-view',
        'defaultParams' => array(
            'title' => 'Albums',
            'margin_photo' => 5,
            'showViewMore' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each album photo.',
                        'value' => 220,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each album photo.',
                        'value' => 270,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For This Block.',
                        'value' => '270',
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
                            '2' => 'Auto Load Albums on Scrolling Down'
                        ),
                        'value' => 1,
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
                $albumElement,
                $albumInfoOnHoverElement,
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => "Column Height For One Item / Block [This setting will only work if above hover information is selected 'No'.]",
                        'value' => '270',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array("recentalbums" => "Recent", "mostZZZlikedalbums" => "Most Liked", "mostZZZviewedalbums" => "Most Viewed", "mostZZZcommentedalbums" => "Most Commented", "featuredalbums" => "Featured", "randomalbums" => "Random", "mostZZZratedalbums" => "Most Rated")
                    )
                ),
                array(
                    'Text',
                    'recentalbums',
                    array(
                        'label' => 'Recent Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'most_likedalbums',
                    array(
                        'label' => 'Most Liked Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'most_viewedalbums',
                    array(
                        'label' => 'Most Viewed Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'most_commentedalbums',
                    array(
                        'label' => 'Most Commented Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'featuredalbums',
                    array(
                        'label' => 'Featured Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'randomalbums',
                    array(
                        'label' => 'Random Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'most_ratedalbums',
                    array(
                        'label' => 'Most Rated Tab (order)',
                        'value' => 7
                    ),
                ),
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of featured albums tab.',
                        'multiOptions' => array(
                            'random' => 'Random Albums',
                            'creation_date' => 'Recent Albums'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you do not want to show title link, then simply leave this field empty.',
                        'value' => '<a href="' . $url . '">Explore Albums »</a>',
                    )
                ),
                array(
                    'Radio',
                    'showViewMore',
                    array(
                        'label' => 'Show "View More" link',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                    )
                ),
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Albums to show)',
                        'value' => 9,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Ajax based Tabbed widget for Photos' . $onloadScript,
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented, Most Rated and Featured Photos in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Advanced Albums Plugin.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.list-photos-tabs-view',
        'defaultParams' => array(
            'title' => 'Photos',
            'margin_photo' => 2,
            'showViewMore' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 3,
                    )
                ),
                $justifiedViewOption,
                $rowHeight,
                $maxRowHeight,
                $margin,
                $lastRow,
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 250,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 270,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For One Item / Block.',
                        'value' => '250',
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
                            '2' => 'Auto Load Albums on Scrolling Down'
                        ),
                        'value' => 1,
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
                $photoElement,
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array("recentphotos" => "Recent", "mostZZZlikedphotos" => "Most Liked", "mostZZZviewedphotos" => "Most Viewed", "mostZZZcommentedphotos" => "Most Commented", "featuredphotos" => "Featured", "randomphotos" => "Random", "mostZZZratedphotos" => "Most Rated")
                    )
                ),
                array(
                    'Text',
                    'recentphotos',
                    array(
                        'label' => 'Recent Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'most_likedphotos',
                    array(
                        'label' => 'Most Liked Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'most_viewedphotos',
                    array(
                        'label' => 'Most Viewed Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'most_commentedphotos',
                    array(
                        'label' => 'Most Commented Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'featuredphotos',
                    array(
                        'label' => 'Featured Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'randomphotos',
                    array(
                        'label' => 'Random Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'most_ratedphotos',
                    array(
                        'label' => 'Most Rated Tab (order)',
                        'value' => 7
                    ),
                ),
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of featured photos tab.',
                        'multiOptions' => array(
                            'random' => 'Random Photos',
                            'creation_date' => 'Recent Photos'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Radio',
                    'showViewMore',
                    array(
                        'label' => 'Show "View More" link',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'showPhotosInLightbox',
                    array(
                        'label' => 'Do you want to show the photos should be open in Lightbox while clicking on the Photo Title.',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                    )
                ),
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Photos to show)',
                        'value' => 9,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $truncationLocationElement,
                $photoTitleTruncation,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Album Photos' . $onloadScript,
        'description' => 'Displays the photos of a particular album. This widget should be placed on the Album View Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.album-view',
        'isPaginated' => true,
        'defaultParams' => array(
            'titleCount' => true,
            'itemCountPerPage' => 40,
            'margin_photo' => 2
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 2,
                    )
                ),
                $justifiedViewOption,
                $rowHeight,
                $maxRowHeight,
                $margin,
                $lastRow,
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For One Item / Block.',
                        'value' => '200',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'photoInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the photos in this block.',
                        'multiOptions' => array_merge($myphotoArray, array("likeCommentStrip" => "Like / Comment Strip"))
                    ),
                ),
                $showViewMoreContent,
            ),
        ),
    ),
    array(
        'title' => 'Album Profile: Top Content of Album View Page',
        'description' => 'This widget displays the various contents such as action links, title, location, description,like button to users viewing an Album. This widget should be placed in the middile column on the Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.top-content-of-album',
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
                        'label' => "Select the information options that you want to be available in this block.[Note: If you don't select Options, then below slide show settings will not be effective.]",
                        'description' => '',
                        'MultiOptions' => $albumViewPageOptions,
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
        'title' => 'Like Button for Photo',
        'description' => 'This is the Like Button for photos. It should be placed on the Photo View Page if needed.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.like-button',
        'defaultParams' => array(
            'titleCount' => false,
        ),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => 'Members tagged in this Album',
        'description' => 'Displays members who are tagged in the photos of an album. This widget should be placed on the Album View Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.inthis-album',
        'isPaginated' => true,
        'defaultParams' => array(
            'itemCountPerPage' => 3,
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => 'Tagged Photos of You and Owner',
        'description' => 'Displays the photos in which both the viewer and the album owner are tagged. This widget should be placed on the Album View Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.you-and-owner',
        'isPaginated' => true,
        'defaultParams' => array(
            'itemCountPerPage' => 2,
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => "Friends' Album Photos",
        'description' => 'Randomly displays albums and their photos from the member\'s friends\' albums.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.friends-photo-albums',
        'defaultParams' => array(
            'itemCountAlbum' => 2,
            'itemCountPhoto' => 2,
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Albums',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                $albumElementWithoutShareLinks,
                array(
                    'Text',
                    'itemCountAlbum',
                    array(
                        'label' => 'Count',
                        'description' => '(number of albums to show)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'itemCountPhoto',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show for each album)',
                        'value' => 2,
                    )
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
            ),
        ),
    ),
    array(
        'title' => "Friends' Photos",
        'description' => 'Randomly displays photos of the member\'s friends.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.friends-photos',
        'defaultParams' => array(
            'itemCountPhoto' => 2,
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Photos',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 200,
                    )
                ),
                $photoElement,
                array(
                    'Text',
                    'itemCountPhoto',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 2,
                    )
                ),
                $truncationLocationElement,
                $photoTitleTruncation,
            ),
        ),
    ),
    array(
        'title' => 'View Photo',
        'description' => 'This is the widget for the photo on the Photo View Page. It should be placed on the Photo View Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.photo-view',
        'defaultParams' => array(
            'titleCount' => false,
            'itemCountPerPage' => 4,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewDisplayHR',
                    array(
                        'label' => 'Select the placement position of the options in this widget.',
                        'multiOptions' => array(
                            1 => 'Horizontal',
                            0 => 'Vertical'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showbuttons',
                    array(
                        'label' => 'Do you want to show Like, Comment, Tag This Photo and Make Profile Photo Buttons above the photo?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Recent / Random / Popular Albums',
        'description' => 'Displays Albums based on the Popularity / Sorting Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.list-popular-albums',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Albums',
            'itemCountPerPage' => 4,
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
                        'label' => 'Enter the description',
                        'value' => '',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Albums',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random', 'photos' => 'Most Photos')),
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
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each album photo.',
                        'value' => 195,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each album photo.',
                        'value' => 195,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count"))
                    ),
                ),
                $albumInfoOnHoverElement,
                array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you do not want to show title link, then simply leave this field empty.',
                        'value' => '<a href="' . $url . '">Explore Albums »</a>',
                    )
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            )
        ),
    ),
    array(
        'title' => 'Recent / Random / Popular Photos' . $onloadScript,
        'description' => 'Displays Photos based on the Popularity / Sorting Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.list-popular-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Photos',
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
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Photos',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random', 'date_taken' => 'Recently Taken')),
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
                $justifiedViewOption,
                $rowHeight,
                $maxRowHeight,
                $margin,
                $lastRow,
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 200,
                    )
                ),
                $photoElement,
                $truncationLocationElement,
                $photoTitleTruncation,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            )
        ),
    ),
    array(
        'title' => "Member Profile Albums and Photos" . $onloadScript,
        'description' => 'Displays a member\'s albums on their profile and the photos that they have been tagged in and your photos. This widget should be placed on the Member Profile Page. It contains a count setting for the number of albums to show at one go.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
            'itemCountPerPage' => 8
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'MultiCheckbox',
                    'selectDispalyTabs',
                    array(
                        'label' => 'Select Tabs that you want to be shown in this block.',
                        'multiOptions' => array('yourphotos' => 'Your Photos', 'photosofyou' => 'Photos of You', 'albums' => 'Albums', 'likesphotos' => 'Like Photos'),
                    )
                ),                
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 2,
                    ),
                ),
                array(
                    'Text',
                    'albumPhotoHeight',
                    array(
                        'label' => 'Enter the height of each album.',
                        'value' => 195,
                    )
                ),
                array(
                    'Text',
                    'albumPhotoWidth',
                    array(
                        'label' => 'Enter the width of each album.',
                        'value' => 195,
                    )
                ),
                $justifiedViewOption,
                $rowHeight,
                $maxRowHeight,
                $margin,
                $lastRow,
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 205,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 205,
                    )
                ),
                array(
                    'Text',
                    'photoColumnHeight',
                    array(
                        'label' => 'Column Height For Photos.',
                        'value' => '250',
                    )
                ),
                array(
                    'Radio',
                    'showaddphoto',
                    array(
                        'label' => 'Show Add Photos Link',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showviewphotolink',
                    array(
                        'label' => 'Show "View Photo on Map" Link',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),

                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($myalbumArray, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count"))
                    ),
                ),
                $albumInfoOnHoverElement,
                array(
                    'Text',
                    'albumColumnHeight',
                    array(
                        'label' => 'Column Height For Albums.',
                        'value' => '250',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'photoInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the photos in this block.',
                        'multiOptions' => array_merge($myphotoArray, array("likeCommentStrip" => "Like / Comment Strip", "photoTitle" => "Photo  Title"))
                    ),
                ),
                array(
                    'Radio',
                    'showPhotosInLightbox',
                    array(
                        'label' => 'Do you want to show the photos should be open in Lightbox while clicking on the Photo Title.',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Truncation limit for abum / photo title.',
                        'value' => 16,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            )
        ),
    ),
    array(
        'title' => 'Member Profile Photos Strip',
        'description' => "Displays some photos out of all the albums of a member in a strip. Member can choose which photos to be shown in the strip by hiding the ones that should not be displayed. Hidden photos are replaced by new photos and so on. This widget should be placed on the Member Profile Page.",
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.photo-strips',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'itemCountPerPage' => 7
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Photos',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
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
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 100,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 100,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Advanced Album Browse Quick Menu',
        'description' => 'Displays a menu in the album gutter.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.browse-menu-quick',
    ),
    array(
        'title' => 'Category Navigation Bar',
        'description' => 'Displays categories in this block. You can configure various settings for this widget from the Edit settings.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.categories-navigation',
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
        'title' => 'Browse Albums’ Locations',
        'description' => 'Displays a list of all the Albums having location entered corresponding to them on the website. This widget should be placed on Browse Albums’ Locations Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.bylocation-album',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each album photo.',
                        'value' => 195,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each album photo.',
                        'value' => 195,
                    )
                ),
                $albumElement,
                $truncationLocationElement,
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories / sub-categories  to be shown to the users even if they have 0 albums in them?',
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
            ))
    ),
    array(
        'title' => 'Album Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the album based on the categories. This widget should be placed on the Advanced Album - Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Browse Albums',
        'description' => 'Displays a list of all the Albums having on the website. This widget should be placed on Browse Albums Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.browse-albums-sitealbum',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each album photo.',
                        'value' => 195,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each album photo.',
                        'value' => 195,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "profileField" => "Profile Fields", "facebook" => "Facebook [Social Share Link]", "twitter" => "Twitter [Social Share Link]", "linkedin" => "LinkedIn [Social Share Link]", "google" => "Google + [Social Share Link]"))
                    ),
                ),
                $albumInfoOnHoverElement,
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => "Column Height For One Item / Block [This setting will only work if above hover information is selected 'No'.]",
                        'value' => '250',
                    )
                ),
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Albums. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'creationDate' => 'All albums in descending order of creation date.',
                            'viewCount' => 'All albums in descending order of views.',
                            'title' => 'All albums in alphabetical order.',
                            'featured' => 'Featured albums followed by others in descending order of creation date.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                $showViewMoreContent,
                $truncationLocationElement,
                $albumTitleTruncation,
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of items to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ))
    ),
    array(
        'title' => 'Browse Photos' . $onloadScript,
        'description' => 'Displays a list of all the Photos having on the website. This widget should be placed on Browse Photos Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.browse-photos-sitealbum',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Browse Photos',
            'itemCountPerPage' => 40,
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
                $justifiedViewOption,
                $rowHeight,
                $maxRowHeight,
                $margin,
                $lastRow,
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each photo.',
                        'value' => 200,
                    )
                ),
                $photoElement,
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Photos. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'creationDate' => 'All photos in descending order of creation date.',
                            'takenDate' => 'All photos in descending order of taken date.',
                            'viewCount' => 'All photos in descending order of views.',
                            'title' => 'All photos in alphabetical order.',
                            'featured' => 'Featured photos followed by others in descending order of creation date.',
                            'featuredTakenBy' => 'Featured photos followed by others in descending order of taken date.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                $showViewMorePhotoContent,
                $truncationLocationElement,
                $photoTitleTruncation,
                $detactLocationPhotoElement,
                $defaultLocationPhotoDistanceElement,
            )
        ),
    ),
    array(
        'title' => 'My Albums',
        'description' => 'Displays a list of all the Albums of logged-in users having on the website. This widget should be placed on My Albums Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.my-albums-sitealbum',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal / Vertical Margin between Elements',
                        'description' => '(Horizontal / Vertical margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of each album photo.',
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of each album photo.',
                        'value' => 280,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For This Block.',
                        'value' => '270',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($myalbumArray, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "facebook" => "Facebook [Social Share Link]", "twitter" => "Twitter [Social Share Link]", "linkedin" => "LinkedIn [Social Share Link]", "google" => "Google + [Social Share Link]"))
                    ),
                ),
                array(
                    'Select',
                    'album_view_type',
                    array(
                        'label' => 'View Type',
                        'description' => '',
                        'multiOptions' => array(
                            '1' => 'List View',
                            '2' => 'Grid View',),
                        'value' => 2,
                    )
                ),
                $showViewMoreContent,
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of items to show)',
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => 'Description Truncation Limit.[Note: Enter 0 to hide the description.]',
                        'value' => 150,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            )
        )
    ),
    array(
        'title' => 'Featured Photos Slide Show',
        'description' => 'Displays photos based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.featured-photos-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'Select',
                    'whichslideshow',
                    array(
                        'label' => 'For which do you want to show slideshow?',
                        'multiOptions' => array('albumsslideshow' => 'Albums', 'photosslideshow' => 'Photos'),
                        'value' => 'photosslideshow',
                    )
                ),
                array(
                    'Radio',
                    'featured',
                    array(
                        'label' => 'Show Featured Albums / Photos',
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
                        'value' => 'creation',
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
                    'Select',
                    'slideshow_type',
                    array(
                        'label' => 'Slideshow Type',
                        'description' => '',
                        'MultiOptions' => array('zndp' => 'Zooming & Panning', 'noob' => 'Slides With Bullet Navigation'),
                    ),
                ), array(
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
                    'slideshow_width',
                    array(
                        'label' => 'Enter the width of the slideshow (in pixels).',
                        'value' => 825,
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
                        'label' => 'Delay',
                        'description' => 'What is the delay you want between slide changes?',
                        'value' => 3500,
                    )
                ),
                array(
                    'Text',
                    'duration',
                    array(
                        'label' => 'Duration',
                        'description' => 'What is the duration you want for slide effects?',
                        'value' => 750,
                    )
                ),
                array(
                    'Radio',
                    'showCaption',
                    array(
                        'label' => 'Do you want to show image description in this Slideshow?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showController',
                    array(
                        'label' => 'Do you want to show the slideshow controller on the slides? [Note : The slideshow controller has options like pause / play, forward, next, etc. The controller is only visible upon mouseover on the slideshow.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showButtonSlide',
                    array(
                        'label' => 'Do you want to show thumbnails for photos navigation in this Slideshow? [Note: This setting will work if Slideshow Type setting is selected "Slides With Bullet Navigation", If you select No, then small circles will be shown at Slideshow bottom for slides navigation.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showThumbnailInZP',
                    array(
                        'label' => 'Do you want to show thumbnails for photos navigation at the Slideshow bottom? [Note: This setting will work if Slideshow Type setting is selected "Zooming & Panning".]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mouseEnterEvent',
                    array(
                        'label' => 'By which action do you want slides navigation to occur from small circles? [Note: This setting will work if Slideshow Type setting is selected "Slides With Bullet Navigation".]',
                        'multiOptions' => array(
                            1 => 'Mouse-over',
                            0 => 'On-click'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'thumbPosition',
                    array(
                        'label' => 'Where do you want to show image thumbnails? [Note: This setting will work if Slideshow Type setting is selected "Slides With Bullet Navigation".]',
                        'multiOptions' => array(
                            'bottom' => 'In the bottom of Slideshow',
                            'left' => 'In the left of Slideshow',
                            'right' => 'In the right of Slideshow',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Radio',
                    'autoPlay',
                    array(
                        'label' => 'Do you want the Slideshow to automatically start playing when Album Home Page is opened? [Note: This setting will work if Slideshow Type setting is selected "Slides With Bullet Navigation".]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'slidesLimit',
                    array(
                        'label' => 'How many slides you want to show in slideshow?',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'captionTruncation',
                    array(
                        'label' => 'Truncation limit for slideshow description',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ))
    ),
    array(
        'title' => 'Horizontal Search Albums Form',
        'description' => "This widget searches over Album Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.searchbox-sitealbum',
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
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering", "locationElement" => "Location field", "locationmilesSearch" => "Proximity Search"),
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
                        'label' => 'Do you want all the categories, sub-categories to be shown to the users even if they have 0 albums in them?',
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
            ),
        ),
    ),
    array(
        'title' => 'Categories / Sub-categories in Grid View',
        'description' => 'Displays Categories and Sub-categories in Grid view on Categories Home page respectively.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.categories-grid-view',
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
                        'label' => 'Do you want all the categories, sub-categories to be shown to the users even if they have 0 albums in them?',
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
                        'label' => 'Show album count along with the sub-category name.',
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
                        'label' => 'Column Width For Grid View.',
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '216',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Categories Banner',
        'description' => 'Displays banners for categories / sub-categories on Advanced Albums - Browse Albums page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'sitealbum.categories-banner-sitealbum',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Categories Hierarchy for Albums (sidebar)',
        'description' => 'Displays the Categories / Sub-categories of Albums in an expandable form. Clicking on them will redirect the viewer to Advanced Albums - Browse Albums page displaying the list of albums created in that category. Multiple settings are available to customize this widget. It is recommended to place this widget in \'Full Width\'.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.categories-sidebar-sitealbum',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Sponsored Categories',
        'description' => 'Displays the Sponsored categories / sub-categories. You can make categories as Sponsored from "Categories" section of Admin Panel.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.categories-sponsored',
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
                        'label' => 'Count (number of categories to show. Enter 0 for displaying all categories.)',
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
                array(
                    'Text',
                    'columnPerRow',
                    array(
                        'label' => 'Number of columns per row to show the categories. [Max: 5]',
                        'value' => 5,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Album Profile: Album Options',
        'description' => 'Displays the various action link options to users viewing an Album. This widget should be placed on the Advanced Album - Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.profile-options',
        'requirements' => array(
            'subject' => 'no-subject',
        ),
    ),
    array(
        'title' => 'Make Featured Link',
        'description' => 'Displays a featured link to users viewing an Album. This widget should be placed on the Advanced Album - Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.make-featured-link',
        'requirements' => array(
            'subject' => 'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        )),
    array(
        'title' => 'Search Albums Form',
        'description' => 'Displays the form for searching Albums on the basis of various fields and filters.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.search-sitealbum',
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
                        'label' => 'Do you want all the categories / sub-categories to be shown to the users even if they have 0 albums in them?',
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
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'whatWhereWithinmile',
                    array(
                        'label' => 'Do you want to show "What, Where and Within Miles" in single row and bold text label? [Note: This setting will not work when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'advancedSearch',
                    array(
                        'label' => 'Do you want to show all advanced search fields expanded [Note: This setting will not work if above setting set "No" and when form is placed in right/left column.]',
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
        'title' => 'Search Photos Form',
        'description' => 'Displays the form for searching Photos on the basis of various fields and filters.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.search-sitephoto',
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
                        'label' => 'Do you want all the categories / sub-categories to be shown to the users even if they have 0 albums in them?',
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
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'whatWhereWithinmile',
                    array(
                        'label' => 'Do you want to show "What, Where and Within Miles" in single row and bold text label? [Note: This setting will not work when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'advancedSearch',
                    array(
                        'label' => 'Do you want to show all advanced search fields expanded [Note: This setting will not work if above setting set "No" and when form is placed in right/left column.]',
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
        'title' => 'Popular Albums Tags',
        'description' => "Displays popular tags. You can choose to display tags based on their frequency / alphabets from the Edit Settings of this widget. This widget should be placed on the 'Advanced Albums - Album View' / 'Advanced Albums - Browse Albums' / 'Advanced Albums - Albums Home' pages.",
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.tagcloud-sitealbum',
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
                        'description' => "Enter below the format in which you want to display the title of the widget. (Note: To display count of tags on albums browse and home pages, enter title as: Title (%s). To display album owner’s name on album view page, enter title as: %s's Tags.)",
                        'value' => 'Popular Tags (%s)',
                    )
                ),
                array(
                    'Radio',
                    'orderingType',
                    array(
                        'label' => 'Do you want to show popular album tags in alphabetical order?',
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
                        'description' => '(number of tags to show. Enter 0 for displaying all tags.)',
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
        'title' => 'Browse Albums: Breadcrumb',
        'description' => 'Displays breadcrumb based on the categories searched from the search form widget. This widget should be placed on Advanced Albums - Browse Albums page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.browse-breadcrumb-sitealbum',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Album Profile: Quick Information (Profile Fields)',
        'description' => 'Displays the Questions enabled to be shown in this widget from the \'Profile Fields\' section in the Admin Panel. This widget should be placed in the right / left column on the Advanced Albums - Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.quick-specification-sitealbum',
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
        'title' => 'Album Profile: Information (Profile Fields)',
        'description' => 'Displays the Questions to be shown in this widget. This widget should be placed in the right / left column on the Advanced Albums - Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.specification-sitealbum',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Popular Locations',
        'description' => 'Displays the popular locations of albums with frequency.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.popularlocation-sitealbum',
        'defaultParams' => array(
            'title' => 'Popular Locations',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of locations to show)',
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Albums: Pinboard View',
        'description' => 'Displays a list of all the albums on site in attractive Pinboard View. You can also choose to display albums based on user’s current location by using the Edit Settings of this widget. It is recommended to place this widget on “Browse Albums ‘s Pinboard View” page',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.pinboard-browse',
        'defaultParams' => array(
            'title' => 'Recent',
            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "profileField" => "Profile Fields"))
                    ),
                ),
                array(
                    'Text',
                    'customParams',
                    array(
                        'label' => 'Profile Fields',
                        'description' => '(number of profile fields to show.)',
                        'value' => 5,
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
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
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
                        'label' => 'Default ordering in Albums Pinboard. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'creationDate' => 'All albums in descending order of creation date.',
                            'viewCount' => 'All albums in descending order of views.',
                            'title' => 'All albums in alphabetical order.',
                            'featured' => 'Featured albums followed by others in descending order of creation date.',
                        ),
                        'value' => 'creationDate',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of albums to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Albums displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it')
                    ),
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the trucation limit for the Album Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Albums Home: Pinboard View',
        'description' => 'Displays albums in Pinboard View on the Albums Home page. Multiple settings are available to customize this widget.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.pinboard-albums-sitealbum',
        'defaultParams' => array(
            'title' => 'Recent',
            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random')),
                        'value' => 'event_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented, Most Rated and Recently Created.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "profileField" => "Profile Fields"))
                    ),
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
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Albums to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'Text',
                    'noOfTimes',
                    array(
                        'label' => 'Auto-Loading Count',
                        'description' => 'Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Albums displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it')
                    ),
                ),
                $truncationLocationElement,
                $albumTitleTruncation,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the trucation limit for the Album Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Album Profile: Album User Ratings',
        'description' => 'This widget displays the overall ratings given by members of your site on the album being currently viewed. This widget should be placed in the right / left column on the Album View page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.user-ratings',
        'defaultParams' => array(
            'title' => 'User Ratings',
            'titleCount' => true,
        ),
        'adminForm' => array(
        )
    ),
    array(
        'title' => 'Album Profile: Album Information',
        'description' => 'Displays the category, tags, views, and other information about an album. This widget should be placed on Advanced Albums - Album View page in the left column.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.information-sitealbum',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
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
        'title' => 'Add New Photos',
        'description' => 'This widget displays the button or link to Add New Photos.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.upload-photo-sitealbum',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'upload_button',
                    array(
                        'label' => 'How do you want to display Add New Photos action in this widget ?',
                        'multiOptions' => array(
                            '1' => 'As a button',
                            '0' => 'As a link',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'upload_button_title',
                    array(
                        'label' => 'Enter the text that displays on this button or link.',
                        'value' => 'Add New Photos',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Featured Albums and Photos Rotator',
        'description' => 'This widget displays the Featured Albums and Featured Photos. Featured Photos displays in rotations and Featured Albums displays in a block below the Featured Photos rotator. You can place this widget multiple times with different popularity criteria chosen for each placement.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'sitealbum.featured-photos',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of Featured Photos (in pixels) [Leave blank for 100% width]',
                        'value' => '',
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of Featured Photos (in pixels)',
                        'value' => '500',
                    )
                ),
                array(
                    'Radio',
                    'contentFullWidth',
                    array(
                        'label' => 'Do you want to display Featured Photos in full width?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'speed',
                    array(
                        'label' => 'Enter the delay you want between Featured Photos rotation. (in milliseconds)',
                        'value' => '5000',
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularTypeArray, array('random' => 'Random', 'date_taken' => 'Recently Taken')),
                        'value' => 'random',
                    )
                ),
                array(
                    'Text',
                    'featuredPhotositemCount',
                    array(
                        'label' => 'How many Featured Photos you want to show in rotator.',
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'featuredPhotosHtmlTitle',
                    array(
                        'label' => 'Enter the title that you want to display on all the Featured Photos showing in rotator.',
                        'value' => "The community for all your photos."
                    )
                ),
                array(
                    'Text',
                    'featuredPhotosHtmlDescription',
                    array(
                        'label' => 'Enter the description that you want to display on all the Featured Photos showing in rotator.',
                        'value' => 'Upload, access, organize, edit, and share your photos.'
                    )
                ),
                array(
                    'Radio',
                    'featuredPhotosSearchBox',
                    array(
                        'label' => 'Do you want to show the Search Box on Featured Photos rotator that displays below the title and description.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'featuredAlbums',
                    array(
                        'label' => 'Do you want to show the Featured Albums in the bottom part of Featured Photos rotator. [Only 3 featured album can be displayed here]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Albums and Photos HTML Block',
        'description' => 'This widget shows the HTML title & description along with options to configure important buttons that displays below the HTML title and description block. [If you want to edit the HTML title and description then, please <a target="_blank" href="admin/sitealbum/html-block">click here</a>.',
        'decorators' => array('ViewHelper', array('Description', array('placement' => 'PREPEND', 'escape' => false))),
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.html-block-albums-photos',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showButton',
                    array(
                        'label' => 'Do you want to show important buttons below the HTML title and description block. [If you select "No" then all the below settings will be not applicable.]',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'firstButton',
                    array(
                        'label' => 'Please enter the link for first important button. [ i.e, albums/browse for album browse page]',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'firstButtonTitle',
                    array(
                        'label' => 'Please enter the first important button title.',
                        'value' => 'Browse Albums',
                    )
                ),
                array(
                    'Text',
                    'firstButtonTitleLink',
                    array(
                        'label' => 'Please enter the link on this first important button.',
                        'value' => 'albums/browse',
                    )
                ),
                array(
                    'Radio',
                    'secondButton',
                    array(
                        'label' => 'Do you want to show second important button?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'secondButtonTitle',
                    array(
                        'label' => 'Please enter the second important button title.',
                        'value' => 'Browse Photos',
                    )
                ),
                array(
                    'Text',
                    'secondButtonTitleLink',
                    array(
                        'label' => 'Please enter the link for second important button. [ i.e, albums/photo/browse for photos browse page]',
                        'value' => 'albums/photo/browse',
                    )
                ),
                array(
                    'Radio',
                    'uploadButton',
                    array(
                        'label' => "Do you want to display 'Add New Photos' button in this block ?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'uploadButtonTitle',
                    array(
                        'label' => 'Enter the text that displays on this button.',
                        'value' => 'Add New Photos',
                    )
                ),
            ),
        ),
    ),
);
?>