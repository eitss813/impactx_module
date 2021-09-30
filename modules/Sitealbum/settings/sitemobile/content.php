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
                          
$popularity_options_albums = array(
    'recentalbums' => $view->translate('Recent'),
    'most_likedalbums' => $view->translate('Most Liked'),
    'most_viewedalbums' => $view->translate('Most Viewed'),
    'most_commentedalbums' => $view->translate('Most Commented'),
    'featuredalbums' => $view->translate('Featured'),
    'randomalbums' => $view->translate('Random'),
    'most_ratedalbums' => $view->translate('Most Rated')
);
                        
$popularity_options_photos = array(
    'recentphotos' => $view->translate('Recent'),
    'most_likedphotos' => $view->translate('Most Liked'),
    'most_viewedphotos' => $view->translate('Most Viewed'),
    'most_commentedphotos' => $view->translate('Most Commented'),
    'featuredphotos' => $view->translate('Featured'),
    'randomphotos' => $view->translate('Random'),
    'most_ratedphotos' => $view->translate('Most Rated')
);                       

$albumTempOtherInfoElement = array(
    "ownerName" => "Owner Name",
    "creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
);

$photoTempOtherInfoElement = array(
    "ownerName" => "Like / Comment Strip",
    "creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location",
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
    "location" => "Location");

$myphotoArray = array("creationDate" => "Creation Date",
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    "location" => "Location");

$albumViewPageOptions = array('title' => 'Album Title', 'owner' => 'Album Owner', 'description' => 'Description', 'location' => 'Location', 'updateddate' => 'Updated Date', 'totalPhotos' => 'Total Photos', 'creationDate' => 'Creation Date','viewCount' => 'Views', 'likeCount' => 'Likes', 'commentCount' => 'Comments');

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {
  $albumTempOtherInfoElement = array_merge($albumTempOtherInfoElement, array("ratingStar" => "Rating"));
  $photoTempOtherInfoElement = array_merge($photoTempOtherInfoElement, array("ratingStar" => "Rating"));
  $popularTypeArray = array_merge($popularTypeArray, array('rating' => 'Most Rated'));
  $myalbumArray = array_merge($myalbumArray, array("ratingStar" => "Rating"));
  $myphotoArray = array_merge($myphotoArray, array("ratingStar" => "Rating"));
}

$informationOptions = array("totalPhotos" => "Total Photos", "creationDate" => "Creation Date", "updateDate" => "Updated Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "location" => "Location");

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0)) {
  $informationOptions = array_merge($informationOptions, array("tags" => "Tags"));
  $albumViewPageOptions = array_merge($albumViewPageOptions, array("tags" => "Tags"));
}

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {
  $informationOptions = array_merge($informationOptions, array("rating" => "Rating"));
  $albumViewPageOptions = array_merge($albumViewPageOptions, array("rating" => "Rating"));
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
        'multiOptions' => array_merge($albumTempOtherInfoElement, array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count"))
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

$truncationLocationElement = array(
    'Text',
    'truncationLocation',
    array(
        'label' => 'Truncation Limit of Location (Depend on Location)',
        'value' => 50,
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

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0)) {
  $locationDescription = "Choose the kilometers within which albums will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
  $locationLableS = "Kilometer";
  $locationLable = "Kilometers";
} else {
  $locationDescription = "Choose the miles within which albums will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
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
    array('order' => 2001
        ));

$hiddenSubCatElement = array(
    'Hidden',
    'hidden_subcategory_id',
    array('order' => 2002
        ));

return array(
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
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "ownerName"=> "Owner Name")
                    ),
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
        'title' => $view->translate('Popular / Recent Albums'),
        'description' => $view->translate('Displays Albums based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Advanced Albums'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.sitemobile-popular-albums',
        'defaultParams' => array(
            'title' => $view->translate('Albums'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array( 
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,     
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options_albums,
                        'value' => 'Recently Posted',
                    )
                ),
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of featured albums [Note: This setting will work only if selected Popularity Criteria is featured].',
                        'multiOptions' => array(
                            'random' => 'Random Albums',
                            'creation_date' => 'Recent Albums'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'albumInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the albums in this block.',
                        'multiOptions' => array("albumTitle" => "Album Title", "totalPhotos" => "Total Photos Count", "ownerName"=> "Owner Name")
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Albums to show)'),
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
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
        'title' => $view->translate('Popular / Recent Photos'),
        'description' => $view->translate('Displays Photos based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Advanced Albums'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.sitemobile-popular-photos',
        'defaultParams' => array(
            'title' => $view->translate('Photos'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array( 
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,     
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options_photos,
                        'value' => 'Recently Posted',
                    )
                ),
                array(
                    'Radio',
                    'orderBy',
                    array(
                        'label' => 'Default ordering of featured photos [Note: This setting will work only if selected Popularity Criteria is featured].',
                        'multiOptions' => array(
                            'random' => 'Random Photos',
                            'creation_date' => 'Recent Photos'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Photos to show)'),
                        'value' => 5,
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
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
//                $detactLocationElement,
//                $defaultLocationDistanceElement,
            )
        )
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
        ),
    ),
 array(
        'title' => 'Album Profile: Top Content of Album View Page',
        'description' => 'This widget displays the various contents such as action links, title, location, description to users viewing an Album. This widget should be placed on Album View page.',
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
                        'label' => "Select the information options that you want to be available in this block.",
                        'description' => '',
                        'MultiOptions' => $albumViewPageOptions,
                    ),
                ),
            ),
        ),
    ),
 array(
        'title' => 'Album Photos',
        'description' => 'Displays the photos of a particular album. This widget should be placed on the Album View Page.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.album-view',
        'isPaginated' => true,
        'defaultParams' => array(
            'titleCount' => true,
            'itemCountPerPage' => 20,
        ),
        'adminForm' => array(
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
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of pages in an expandable form. Clicking on them will redirect the viewer to the list of pages created in that category.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.categories-sitealbum',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
    ),
  array(
        'title' => 'Member Profile Albums',
        'description' => 'Displays a member\'s albums on their profile and the photos that they have been tagged in and your photos. This widget should be placed on the Member Profile Page. It contains a count setting for the number of albums to show at one go.',
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitealbum.profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Albums',
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
        'title' => $view->translate('Browse Albums: Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb based on the categories searched from the search form widget. This widget should be placed on Advanced Albums - Album Browse Page.'),
        'category' => 'Advanced Albums',
        'type' => 'widget',
        'name' => 'sitealbum.browse-breadcrumb-sitealbum',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
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
    )
 );
?>
