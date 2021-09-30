<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlP', "pageitems");
$ads_Array = $categories_prepared = array();
$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}
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

$showProfileField = array(
    'Radio',
    'showProfileField',
    array(
        'label' => 'Do you want to show custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    ),
);
$customFieldHeading = array(
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

$customFieldTitle = array(
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

$customParamsCount = array(
    'Text',
    'customFieldCount',
    array(
        'label' => $view->translate('Custom Profile Fields'),
        'description' => $view->translate('(number of profile fields to show.)'),
        'value' => 2,
    )
);

$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => 'Show Pages',
        'multiOptions' => array(
            '' => '',
            'featured' => 'Featured Only',
            'sponsored' => 'Sponsored Only',
            'fea_spo' => 'Both Featured and Sponsored',
        ),
        'value' => 'sponsored',
    )
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
            '3' => 'Auto Load Pages on Scrolling Down'),
        'value' => 2,
    )
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {

  $popularity_options = array(
      'view_count' => 'Most Viewed',
      'like_count' => 'Most Liked',
      'comment_count' => 'Most Commented',
      'review_count' => 'Most Reviewed',
      'rating' => 'Most Rated',
      'page_id' => 'Most Recent',
      'modified_date' => 'Recently Updated',
  );
} else {
  $popularity_options = array(
      'view_count' => 'Most Viewed',
      'like_count' => 'Most Liked',
      'comment_count' => 'Most Commented',
      'page_id' => 'Most Recent',
      'modified_date' => 'Recently Updated',
  );
}

$category_pages_multioptions = array(
    'view_count' => 'Views',
    'like_count' => 'Likes',
    'comment_count' => 'Comments',
);
$pinboardShowsOptions = array(
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    'followCount' => 'Followers',
    "price" => 'Price',
    "location" => "Location"
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
  $pinboardShowsOptions['reviewsRatings'] = "Reviews & Ratings";
}

$pinboardPopularityOptions = array(
    'view_count' => 'Most Viewed',
    'like_count' => 'Most Liked',
    'comment_count' => 'Most Commented',
    'follow_count' => 'Most Following',
    'page_id' => 'Most Recent',
    'modified_date' => 'Recently Updated',
);

$showContent_timeline = array("mainPhoto" => "Page Profile Photo", "title" => "Page Title", "followButton" => "Follow Button", "likeButton" => "Like Button", "likeCount" => "Total Likes", "followCount" => "Total Followers");
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");

$layouts_tabs = array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5');
$layouts_tabs_options = array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored");
$joined_order = array();


$linkDescription = 'Displays various links like  "Pages I Admin", "Pages I\'ve Claimed" and "Pages I Like" on your site. This widget should be placed on Directory / Pages - Manage Pages page.';       
$linksOptions = array("pageAdmin" => "Pages I Admin", "pageClaimed" => "Pages I\'ve Claimed", 'pageLiked' => 'Pages I Like');
$linksValues = array("pageAdmin","pageClaimed","pageLiked");
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
  $showContent_timeline['memberCount'] = 'Total Members';
  $showContent_timeline['addButton'] = 'Add People Button';
  $showContent_timeline['joinButton'] = 'Join Page Button';

  $showContent_option[] = 'addButton';
  $showContent_option[] = 'joinButton';
  $showContent_option[] = 'memberCount';

  $pinboardShowsOptions['memberCount'] = 'Members';
  $pinboardPopularityOptions['member_count'] = "Most Joined Pages";
  $layouts_tabs['5'] = "6";
  $layouts_tabs_options["6"] = "Most Joined Pages";
  $joined_order = array(
      'Text',
      'joined_order',
      array(
          'label' => 'Most Joined Pages Tab (order)',
      ),
  );
  
  $linksOptions = array_merge($linksOptions, array('pagesJoined' => 'Pages I\'ve Joined'));
  $linksValues[] =  "pagesJoined";  
  $linkDescription = 'Displays various links like  "Pages I Admin", "Pages I\'ve Claimed", "Pages I Like" and "Pages I\'ve Joined" on your site. This widget should be placed on Directory / Pages - Manage Pages page.';  
}

$statisticsElement = array("likeCount" => "Likes", "followCount" => "Followers", "viewCount" => "Views", "commentCount" => "Comments");
$statisticsElementValue = array("viewCount", "likeCount", "followCount", "commentCount");


$statisticsBrowseElement = $statisticsElement;
$statisticsBrowseElementValue = $statisticsElementValue;

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
  $statisticsElement['reviewCount'] = "Reviews";
  $statisticsElementValue[] = "reviewCount";
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
  $statisticsElement['memberCount'] = "Members";
  $statisticsElementValue[] = "memberCount";

  $statisticsMemmberElement['memberApproval'] = "Join immediately / Must be approved";
  $statisticsMemmberElementstatisticsElementValue[] = "memberApproval";

  $statisticsBrowseElement = array_merge($statisticsElement, $statisticsMemmberElement);
  $statisticsBrowseElementValue = array_merge($statisticsElementValue, $statisticsMemmberElementstatisticsElementValue);
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
  $category_pages_multioptions['member_count'] = 'Members';
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
  $category_pages_multioptions['review_count'] = 'Reviews';
}
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0)) {
  $locationDescription = "Choose the kilometers within which pages will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
  $locationLableS = "Kilometer";
  $locationLable = "Kilometers";
} else {
  $locationDescription = "Choose the miles within which pages will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
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

//Custom code

$enableSitealbum1 = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
//Categories element
$tableCategory1 = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
$categories1 = $tableCategory1->getCategories(array(), null, 0, 0, 1, 0);
$categoryParams1 = array();
$projectSubCategoryElement1 = array();
$projectCategoryElement1 = array();
if (count($categories1) != 0) {
    $categoryParams1[0] = '';
    foreach ($categories1 as $category) {
        $categoryParams1[$category->category_id] = $category->category_name;
    }
    $projectCategoryElement1 = array(
        'Select',
        'category_id',
        array(
            'label' => 'Category',
            'multiOptions' => $categoryParams1,
            'RegisterInArrayValidator' => false,
            'onchange' => 'addProjectOptions(this.value, "cat_dependency", "subcategory_id", 0); setProjectHiddenValues("category_id")'
        ));
    $projectSubCategoryElement1 = array(
        'Select',
        'subcategory_id',
        array(
            'RegisterInArrayValidator' => false,
            'decorators' => array(array('ViewScript', array(
                'viewScript' => 'application/modules/Sitecrowdfunding/views/scripts/_category.tpl',
                'class' => 'form element')))
        ));
}
$hiddenProjectCatElement1 = array(
    'Hidden',
    'hidden_project_category_id',
    array('order' => 996
    ));

$hiddenProjectSubCatElement1 = array(
    'Hidden',
    'hidden_project_subcategory_id',
    array('order' => 995
    ));
$hiddenProjectSubSubCatElement1 = array(
    'Hidden',
    'hidden_project_subsubcategory_id',
    array('order' => 994
    ));
$rowHeight1 = array(
    'Text',
    'rowHeight',
    array(
        'label' => 'Enter the row height of each photo block. (in pixels) [This row height is used as a base height to create justified view. Height of the resulting rows could be slightly lesser than your entered row height.]',
        'value' => 205,
    )
);
$maxRowHeight1 = array(
    'Text',
    'maxRowHeight',
    array(
        'label' => 'Enter the max row height of each photo block. (in pixels) [This is the maximum row height to be allowed to create justified view.  Height of the resulting rows could be higher / lesser than your entered maximum row height to fit any photo within limit.]',
        'value' => 0,
    )
);
$margin1 = array(
    'Text',
    'margin',
    array(
        'label' => 'Enter the margin between two photos block, vertically and horizontally.(in pixels)',
        'value' => 5,
    )
);
$lastRow1 = array(
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
$justifiedViewOption1 = array(
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
$onloadScript1 = " <script>
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
$locationScript1 = "<script>

 window.addEvent('domready', function () {
    hideDefaultLocationDistance();
    });  

    function hideDefaultLocationDistance(){
        var value = document.getElementById('detactLocation');          
        if (value && !value.selectedIndex){
            document.getElementById('defaultLocationDistance-wrapper').style.display='none'; 
            return ;                
        }
        if(document.getElementById('defaultLocationDistance-wrapper'))
        document.getElementById('defaultLocationDistance-wrapper').style.display='block'; 
     }
    </script>";

$daysFilterElement1 = array(
    'Text',
    'daysFilter',
    array(
        'label' => 'Enter the number of days left to back the projects.',
        'value' => 20,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
        array('LessThan', true, array(93)),
    ),
);
$backedPercentFilterElement1 = array(
    'Text',
    'backedPercentFilter',
    array(
        'label' => 'Enter the percentage of fund collected for the projects.',
        'value' => 40,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);
$selectProjectScript1 = "<script>

    window.addEvent('domready', function () { 
        if($('selectProjects')) { 
            showOngoingOptions($('selectProjects').getSelected()[0].value);
        }
    });  

    function showOngoingOptions(value){ 
        if(value == 'ongoing') {
            if($('daysFilter-wrapper'))
                $('daysFilter-wrapper').style.display = 'block';
            if($('backedPercentFilter-wrapper'))
                $('backedPercentFilter-wrapper').style.display = 'block';
                
        } else {
            if($('daysFilter-wrapper'))
                $('daysFilter-wrapper').style.display = 'none';
            if($('backedPercentFilter-wrapper'))
                $('backedPercentFilter-wrapper').style.display = 'none';
        }
     }
    </script>";

$projectOptions1 = array(
    'title' => 'Project Title',
    'owner' => 'Owner',
    'backer' => 'Backers',
    'like' => 'Likes',
    'favourite' => 'Favourites',
    'comment' => 'Comment Count',
    'endDate' => 'End Date and Time',
    'featured' => 'Featured',
    'sponsored' => 'Sponsored',
);
$socialShareOptions1 = array(
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
    'linkedin' => 'Linkedin',
    'googleplus' => 'Google+'
);
$startDateOption1 = array(
    'startDate' => 'Start Date'
);
$detactLocationElement1 = array();
$truncationLocationElement1 = array();
$defaultLocationDistanceElement1 = array();
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
    $projectOptions1 = array_merge($projectOptions1, array('location' => 'Location'));
    $truncationLocationElement1 = array(
        'Text',
        'truncationLocation',
        array(
            'label' => 'Truncation limit of location (Depend on location)',
            'value' => 35,
        )
    );
    $detactLocationElement1 = array(
        'Select',
        'detactLocation',
        array(
            'label' => "Do you want to display Projects based on user’s current location?",
            'multiOptions' => array(
                0 => 'No',
                1 => 'Yes',
            ),
            'value' => 0,
            'onchange' => "hideDefaultLocationDistance()",
        ),
    );

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0)) {
        $locationDescription1 = "Choose the kilometers within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
        $locationLableS1 = "Kilometer";
        $locationLable1 = "Kilometers";
    } else {
        $locationDescription1 = "Choose the miles within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
        $locationLableS1 = "Mile";
        $locationLable1 = "Miles";
    }

    $defaultLocationDistanceElement1 = array(
        'Select',
        'defaultLocationDistance',
        array(
            'label' => $locationDescription1,
            'multiOptions' => array(
                '0' => '',
                '1' => '1 ' . $locationLableS1,
                '2' => '2 ' . $locationLable1,
                '5' => '5 ' . $locationLable1,
                '10' => '10 ' . $locationLable1,
                '20' => '20 ' . $locationLable1,
                '50' => '50 ' . $locationLable1,
                '100' => '100 ' . $locationLable1,
                '250' => '250 ' . $locationLable1,
                '500' => '500 ' . $locationLable1,
                '750' => '750 ' . $locationLable1,
                '1000' => '1000 ' . $locationLable1,
            ),
            'value' => '1000'
        )
    );
}


$contentTypes = 0;
if (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration'))
    $contentTypes = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1));
$contentTypeArray = array();
if (!empty($contentTypes)) {

    if (!empty($contentTypes))
        $contentTypeArray[] = 'All';
    $moduleTitle = '';
    foreach ($contentTypes as $contentType) {
        if ($contentType['item_title']) {
            $contentTypeArray['user'] = 'Member Projects';
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
                $contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getModuleTitle($contentType['item_module']);
            }
        }
    }
}

if (!empty($contentTypeArray)) {
    $contentTypeElement = array(
        'Select',
        'projectType',
        array(
            'label' => 'Project Type',
            'multiOptions' => $contentTypeArray,
        ),
        'value' => '',
    );
} else {
    $contentTypeElement = array(
        'Hidden',
        'projectType',
        array(
            'label' => 'Project Type',
            'value' => 'All',
            'order' => 1001
        )
    );
}

$contentTypes1 = 0;
if (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration'))
    $contentTypes1 = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1));
$contentTypeArray1 = array();
if (!empty($contentTypes1)) {

    if (!empty($contentTypes1))
        $contentTypeArray1[] = 'All';
    $moduleTitle1 = '';
    foreach ($contentTypes1 as $contentType) {
        if ($contentType['item_title']) {
            $contentTypeArray1['user'] = 'Member Projects';
            $contentTypeArray1[$contentType['item_type']] = $contentType['item_title'];
        } else {
            if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                $moduleTitle1 = 'Reviews & Ratings - Multiple Listing Types';
            } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                $moduleTitle1 = 'Multiple Listing Types Plugin Core (Reviews & Ratings Plugin)';
            }
            $explodedResourceType1 = explode('_', $contentType['item_type']);
            if (isset($explodedResourceType1[2]) && $moduleTitle1) {
                $listingtypesTitle1 = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType1[2])->title_plural;
                $listingtypesTitle1 = $listingtypesTitle1 . ' ( ' . $moduleTitle1 . ' ) ';
                $contentTypeArray1[$contentType['item_type']] = $listingtypesTitle1;
            } else {
                $contentTypeArray1[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getModuleTitle($contentType['item_module']);
            }
        }
    }
}

if (!empty($contentTypeArray1)) {
    $contentTypeElement1 = array(
        'Select',
        'projectType',
        array(
            'label' => 'Project Type',
            'multiOptions' => $contentTypeArray1,
        ),
        'value' => '',
    );
} else {
    $contentTypeElement1 = array(
        'Hidden',
        'projectType',
        array(
            'label' => 'Project Type',
            'value' => 'All',
            'order' => 1001
        )
    );
}

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0)) {
    $locationDescription1 = "Choose the kilometers within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS1 = "Kilometer";
    $locationLable1 = "Kilometers";
} else {
    $locationDescription1 = "Choose the miles within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS1 = "Mile";
    $locationLable1 = "Miles";
}

$showContentElement1 = array(
    'Select',
    'show_content',
    array(
        'label' => 'What do you want for view more content?',
        'multiOptions' => array(
            '2' => 'Show View More Link at Bottom',
            '3' => 'Auto Load Content on Scrolling Down'),
        'value' => 2,
    )
);
$titleTruncationElement1 = array(
    'Text',
    'titleTruncation',
    array(
        'label' => 'Title truncation limit of Project',
        'value' => 20,
    ),
    'validators' => array(
        array('Int', true),
    ),
);
$titleTruncationGridViewElement1 = array(
    'Text',
    'titleTruncationGridView',
    array(
        'label' => 'Title truncation limit of Grid View',
        'value' => 25,
    ),
    'validators' => array(
        array('Int', true),
    ),
);
$titleTruncationListViewElement1 = array(
    'Text',
    'titleTruncationListView',
    array(
        'label' => 'Title truncation limit of List View',
        'value' => 40,
    ),
    'validators' => array(
        array('Int', true),
    ),
);
$descriptionTruncationElement1 = array(
    'Text',
    'descriptionTruncation',
    array(
        'label' => 'Description truncation limit of Project',
        'value' => 100,
    ),
    'validators' => array(
        array('Int', true),
    ),
);
$loadByAjaxElement1 = array(
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
);
$viewTypeElement1 = array(
    'MultiCheckbox',
    'viewType',
    array(
        'label' => 'Select the view types for Projects',
        'multiOptions' => array(
            'gridView' => 'Grid view',
            'listView' => 'List view',
        ),
    )
);
$defaultViewTypeElement1 = array(
    'Select',
    'defaultViewType',
    array(
        'label' => 'Select a default view type for Projects',
        'multiOptions' => array(
            'gridView' => 'Grid view',
            'listView' => 'List view',
        ),
        'value' => 'gridView'
    )
);
$showAllCategoriesElement1 = array(
    'Radio',
    'showAllCategories',
    array(
        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 Projects in them?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    )
);
$projectHeight1 = array(
    'Text',
    'projectHeight',
    array(
        'label' => 'Enter the height of each Project.',
        'value' => 300,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);
$projectWidth1 = array(
    'Text',
    'projectWidth',
    array(
        'label' => 'Enter the width of each Project.',
        'value' => 200,
    ),
    'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
    ),
);
$selectProjects1 = array(
    'Radio',
    'selectProjects',
    array(
        'label' => 'Select Projects based on status.',
        'multiOptions' => array(
            'all' => 'All',
            'ongoing' => 'Ongoing',
            'successful' => 'Successful'
        ),
        'value' => 'all',
    )
);
$gridViewWidthElement1 = array(
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
);
$gridViewHeightElement1 = array(
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
);
//Custome code



$final_array = array(
    array(
        'title' => 'Page Archives',
        'description' => 'Displays the month-wise archives for the pages posted on your site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.archives-sitepage',
        'defaultParams' => array(
            'title' => 'Archives',
            'titleCount' => true,
        )
    ),
    array(
        'title' => 'Navigation Tabs',
        'description' => 'Displays the Navigation tabs pages having links of Pages Home, Browse Pages, etc. This widget should be placed at the top of Pages Home and Browse Pages pages.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.browsenevigation-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories (sidebar)',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of pages in an expandable form. Clicking on them will redirect the viewer to the list of pages created in that category.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.categories-sitepage',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of pages in an expandable form. Clicking on them will redirect the viewer to the list of pages created in that category.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.categories',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 pages in them? (Note: Selecting "Yes" will display all the categories WITHOUT  the count of the pages created in them if "Browse by Networks" are enabled  from the Global Settings of Directory / Pages Plugin and display all the categories with count if selected "No" for Browse by Networks. While selecting "No" here will display categories with count of the pages.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'show2ndlevelCategory',
                    array(
                        'label' => 'Do you want to show sub-categories in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show3rdlevelCategory',
                    array(
                        'label' => 'Do you want to show 3rd level category to the viewer? (This setting will depend on above setting.)',
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
        'title' => 'Category Navigation Bar',
        'description' => 'Displays categories in this block. You can configure various settings for this widget from the Edit settings.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.listtypes-categories',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
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
                array(
                    'Hidden',
                    'nomobile',
                    array(
                        'label' => '',
                    )
                ),    
            ))
    ),    
    array(
        'title' => 'Profile Pages',
        'description' => 'Displays members\' pages on their profile.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.profile-sitepage',
        'defaultParams' => array(
            'title' => 'Pages',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'pageAdmin',
                    array(
                        'label' => 'Which all Pages related to the user do you want to display in this tab widget on their profile?',
                        'multiOptions' => array(
                            1 => 'Pages Owned by the user. (Page Owner)',
                            2 => 'Pages Administered by the user. (Page Admin)'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Verify Page Button & Stats',
        'description' => 'Displays the verify button to verify the page, and the stats for number of verifications for the page. This widget should be placed on Pages Profile Page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.verify-button',
        'defaultParams' => array(
                'title' => '',
        ),
    ),
    array(
        'title' => 'Page Videos',
        'description' => 'This widget display page videos. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-videos',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Most Verified Pages',
        'description' => 'Displays Most Verified Pages.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.verified-pages',
        'defaultParams' => array(
                'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'is_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of pages to show)',
                        'value' => 5,
                    )
                ),
            ),
        )
    ),
    array(
        'title' => 'Featured Pages Slideshow',
        'description' => 'Displays the Featured Pages in the form of an attractive Slideshow with interactive controls.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.slideshow-sitepage',
        'defaultParams' => array(
            'title' => 'Featured Pages',
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
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Services',
        'description' => 'Add services offered by your Page. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-services',
        'defaultParams' => array(
            'title' => 'Page Services',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of serives to show)',
                        'value' => 5,
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
        'title' => 'Sponsored Pages Slideshow',
        'description' => 'Displays the Sponsored Pages in the form of an attractive Slideshow with interactive controls.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.sponsored-slideshow-sitepage',
        'defaultParams' => array(
            'title' => 'Sponsored Pages',
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
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Post a New Page',
        'description' => 'Displays the link to Post a New Page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.newpage-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        )
    ),
    array(
        'title' => 'Most Commented Pages',
        'description' => 'Displays the list of Pages having maximum number of comments.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.mostcommented-sitepage',
        'defaultParams' => array(
            'title' => 'Most Commented Pages',
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
                    )
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Followed Pages',
        'description' => 'Displays a list of pages having maximum number of followers. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.mostfollowers-sitepage',
        'defaultParams' => array(
            'title' => 'Most Followed Pages',
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Liked Pages',
        'description' => 'Displays list of pages having maximum number of likes.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.mostlikes-sitepage',
        'defaultParams' => array(
            'title' => 'Most Liked Pages',
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Page Tags',
        'description' => 'Shows popular tags with frequency.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.tagcloud-sitepage',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1101
                    )
                ),
                array(
                    'hidden',
                    'nomobile',
                    array(
                        'label' => '',
                        'order' => 1102
                    )
                ),
//          array(
//            'hidden',
//            'execute',
//            array(
//              'label' => ''
//            )
//          ),
//          array(
//            'hidden',
//            'cancel',
//            array(
//              'label' => ''
//            )
//          ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
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
            )
        ),
    ),
    array(
        'title' => 'Recent Pages',
        'description' => 'Displays list of recently created Pages.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.recentlyposted-sitepage',
        'defaultParams' => array(
            'title' => 'Recent',
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
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
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
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Pages',
        'description' => 'Displays list of popular pages on the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.mostviewed-sitepage',
        'defaultParams' => array(
            'title' => 'Most Popular',
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
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Pages: Pinboard View',
        'description' => 'Displays a list of all the pages on site in attractive Pinboard View. You can also choose to display pages based on user’s current location by using the Edit Settings of this widget. It is recommended to place this widget on "Browse Pages\'s Pinboard View" page. ',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.pinboard-browse',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the page.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Pages to be displayed in block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Pages to be displayed in block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'showoptions',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $pinboardShowsOptions,
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display pages based on user’s current location?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
                    'Select',
                    'defaultlocationmiles',
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
                        'description' => '(number of Pages to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Pages displayed in this block.',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the truncation limit for the Page Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                array(
                    'Select',
                    'commentSection',
                    array(
                        'label' => 'Do you want to display comments?',
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
                        'value' => '0'
                    )
                ),
// 						array(
// 							'Radio',
// 							'getdirection',
// 							array(
// 									'label' => 'Show get direction link.',
// 									'multiOptions' => array(
// 											1 => 'Yes',
// 											0 => 'No'
// 									),
// 									'value' => 1,
// 							),
// 						),
            ),
        )
    ),
//     array(
//         'title' => 'Page Profile  Cover Photo and Information',
//         'description' => 'Displays the page cover photo with page profile photo, title and various action links that can be performed on the page from their Profile page (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Page Profile page.',
//         'category' => 'Page Profile',
//         'type' => 'widget',
//         'name' => 'sitepage.page-cover-information-sitepage',
//         'defaultParams' => array(
//             'title' => 'Information',
//             'titleCount' => true,
//             'showContent' => $showContent_option
//         ),
//         'adminForm' => array(
//             'elements' => array(
//                 array(
//                     'MultiCheckbox',
//                     'showContent',
//                     array(
//                         'label' => 'Select the information options that you want to be available in this block.',
//                         'multiOptions' => $showContent_timeline,
//                     ),
//                 ), 
//                 array(
//                     'Text',
//                     'columnHeight',
//                     array(
//                         'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
//                         'value' => '300',
//                     )
//                 ),             
//             ),
//         ),
//     ),
    array(
        'title' => 'Browse Pages’ Locations',
        'description' => 'Displays a list of all the pages having location entered corresponding to them on the site. This widget should be placed on Browse Pages’ Locations page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.browselocation-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
// 						array(
// 							'Radio',
// 							'getdirection',
// 							array(
// 									'label' => 'Show get direction link.',
// 									'multiOptions' => array(
// 											1 => 'Yes',
// 											0 => 'No'
// 									),
// 									'value' => 1,
// 							),
// 						),
            ),
        )
    ),
    array(
        'title' => 'Browse Pages',
        'description' => 'Displays a list of all the pages on site. This widget should be placed on the Browse Pages page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.pages-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'columnWidth' => 100,
            'statistics' => $statisticsBrowseElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for pages on the pages home and browse pages.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    ),
                ),
                array(
                    'Radio',
                    'layouts_oder',
                    array(
                        'label' => 'Select a default view type for Directory Items / Pages.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '212',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '270',
                    )
                ),
                array(
                    'Text',
                    'turncation',
                    array(
                        'label' => 'Title Truncation Limit For Grid View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Radio',
                    'showlikebutton',
                    array(
                        'label' => 'Do you want to show “Like Button” when users mouse over on Pages in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showlocation',
                    array(
                        'label' => 'Do you want “Location” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showgetdirection',
                    array(
                        'label' => 'Do you want “Get Direction” link for the pages having location to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showprice',
                    array(
                        'label' => 'Do you want “Price” of the Pages to be displayed?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showpostedBy',
                    array(
                        'label' => 'Do you want “Posted By” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showdate',
                    array(
                        'label' => 'Do you want “Creation Date” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showContactDetails',
                    array(
                        'label' => 'Do you want “Contact Details” of the Pages to be displayed in list and map view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsBrowseElement,
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
                    'Text',
                    'listview_turncation',
                    array(
                        'label' => 'Title Truncation Limit For List View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Text',
                    'turncation',
                    array(
                        'label' => 'Title Truncation Limit For Grid View.',
                        'value' => '40',
                    )
                ),
                $showProfileField,
                $customFieldHeading,
                $customFieldTitle,
                $showViewMoreContent,
                $customParamsCount,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        )
    ),
    array(
        'title' => 'Search Pages form',
        'description' => 'Displays the form for searching Pages on the basis of various filters.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.search-sitepage',
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
            ),
        ),
    ),
    array(
        'title' => 'Random Pages',
        'description' => 'Displays list of Pages randomly.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.random-sitepage',
        'defaultParams' => array(
            'title' => 'Random',
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
                    )
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
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Sponsored Pages Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the sponsored Pages on the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.sponsored-sitepage',
        'defaultParams' => array(
            'title' => 'Sponsored Pages',
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
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Sponsored Carousel Speed',
                        'description' => '(What maximum Carousel Speed should be applied to the sponsored widget?)',
                        'value' => 300,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
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
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 18,
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
        'title' => 'AJAX based Pages Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the pages on the site. You can choose to show sponsored / featured in this widget from the settings of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.ajax-carousel-sitepage',
        'defaultParams' => array(
            'title' => 'Pages Carousel',
            'titleCount' => true,
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
                $featuredSponsoredElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
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
                        'value' => '1',
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
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $popularity_options,
                        'value' => 'listing_id',
                    )
                ),
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => 'Do you want to show the featured icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => 'Do you want to show the sponsored icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of pages to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Sponsored Carousel Speed',
                        'description' => '(What maximum Carousel Speed should be applied to the sponsored widget?)',
                        'value' => 300,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
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
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 50,
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
        'title' => 'AJAX based Pages Owl Carousel',
        'description' => 'Displays your page listings attractively with no delay.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.owl-carousel-sitepage',
        'defaultParams' => array(
            'title' => 'Pages OWl Carousel',
            'titleCount' => true,
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
                $featuredSponsoredElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $popularity_options,
                        'value' => 'listing_id',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height of each slideshow item.',
                        'value' => 220,
                    )
                ),
                array(
                    'Text',
                    'blockWidth',
                    array(
                        'label' => 'Enter the width of each slideshow item.',
                        'value' => 250,
                    )
                ),
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => 'Do you want to show the featured icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => 'Do you want to show the sponsored icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Select',
                    'tabItem',
                    array(
                        'label' => 'Items in Tablet view',
                        'multiOptions' => array('1' => '1','2' => '2'),
                        'description' => 'Please select number of items in a slide(Tablet view)',
                    )
                ),
                array(
                    'Select',
                    'deskItem',
                    array(
                        'label' => 'Items in Desktop view',
                        'multiOptions' => array('1' => '1','2' => '2', '3' => '3', '4' =>'4'),
                        'description' => 'Please select number of items in a slide(Desktop view)',
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Carousel Speed(In milliSec. and minimum 3000ms)',
                        'description' => '(What maximum Carousel Speed should be applied to the widget?)',
                        'value' => 3000,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
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
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 50,
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
        'title' => 'Add Button',
        'description' => 'Add a Call to Action Button to your Page. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.add-button-sitepage',
    ),
    array(
        'title' => 'Page of the Day',
        'description' => 'Displays the Page of the Day as selected by the Admin from the widget settings section of this plugin.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.item-sitepage',
        'defaultParams' => array(
            'title' => 'Page of the Day',
            'titleCount' => true,
            'contacts' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'contacts',
                    array(
                        'label' => 'Select the contact details you want to display',
                        'multiOptions' => array("1" => "Total Likes", "2" => "Total Comments", "3" => "Total views", "4" => "Total Followers"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Recently Viewed Pages',
        'description' => 'Displays list of recently viewed Pages on the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.recentview-sitepage',
        'defaultParams' => array(
            'title' => 'Recently Viewed',
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
                    )
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
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Recently Viewed By Friends',
        'description' => 'Displays list of Pages recently viewed by friends.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.recentfriend-sitepage',
        'defaultParams' => array(
            'title' => 'Recently Viewed By Friends',
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
                    )
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
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Message for Zero Pages',
        'description' => 'This widget should be placed in the top of the middle column of the Pages Home page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.zeropage-sitepage',
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
        'title' => 'Close Page Message',
        'description' => 'If a Page is closed, then show this message.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.closepage-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Popular Locations',
        'description' => 'Displays list of popular locations of Pages.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.popularlocations-sitepage',
        'defaultParams' => array(
            'title' => 'Popular Locations',
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of locations to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Overview',
        'description' => 'Displays rich overview on Page\'s profile, created by its admin using the editor from Page Dashboard. This should be placed in the Tabbed Blocks area of Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.overview-sitepage',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Breadcrumb',
        'description' => 'Displays breadcrumb of the page based on the categories. This widget should be placed on the Page Profile page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Sub Pages of a Page',
        'description' => 'Displays the sub pages created in the Page which is being viewed currently. This widget should be placed on the Page Profile page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.subpage-sitepage',
        'defaultParams' => array(
            'title' => 'Sub Pages of a Page',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of sub-pages to show)',
                        'value' => 3,
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
        'title' => 'Parent Page of a Sub Page',
        'description' => 'Displays the parent page in which the currently viewed sub pages is created. This widget should be placed on the Page Profile page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.parentpage-sitepage',
        'defaultParams' => array(
            'title' => 'Parent Page of a Sub Page',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of sub-pages to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
//    array(
//        'title' => "Page Profile 'Save to foursquare' Button",
//        'description' => "This Button will enable page visitors to add the Page\'s place or tip to their foursquare To-Do List. There is also Member Level and Package setting for this button.",
//        'category' => 'Page Profile',
//        'type' => 'widget',
//        'name' => 'sitepage.foursquare-sitepage',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => 'Page Profile Social Share Buttons',
        'description' => "Contains Social Sharing buttons and enables users to easily share Pages on their favorite Social Networks. This widget should be placed on the Page View Page. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.socialshare-sitepage',
        'defaultParams' => array(
            'title' => 'Social Share',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'sitepage_page',
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Hidden',
                    'nomobile',
                    array(
                        'label' => '',
                        'order' => 1103
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Title',
        'description' => 'Displays the Title of the Page. This widget should be placed on the Page Profile, in the middle column at the top.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.title-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Info',
        'description' => 'This widget forms the Info tab on the Page Profile and displays the information of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.info-sitepage',
        'defaultParams' => array(
            'title' => 'Info',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Projects',
        'description' => 'This widget forms the Profile on the Page Profile and displays the linked project to the organizations. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-projects',
        'defaultParams' => array(
            'title' => 'Projects',
            'titleCount' => true,
            'projectOption' => array('title', 'owner', 'like', 'facebook', 'twitter', 'linkedin', 'googleplus'),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement1,
                $selectProjects1,
                $viewTypeElement1,
                $defaultViewTypeElement1,
                $gridViewWidthElement1,
                $gridViewHeightElement1,
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multioptions' => array_merge($projectOptions1, $startDateOption1, $socialShareOptions1),
                    ),
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in browse Projects.',
                        'multiOptions' => array(
                            'startDate' => 'All projects in descending order of start time.',
                            'startDateAsc' => 'All projects in ascending order of start time.',
                            'backerCount' => 'All projects in descending order of backers.',
                            'backerCountAsc' => 'All projects in ascending order of backers.',
                            'title' => 'All projects in alphabetical order.',
                            'sponsored' => 'Sponsored projects followed by others in ascending order of project start time.',
                            'featured' => 'Featured projects followed by others in ascending order of project start time.',
                            'sponsoredFeatured' => 'Sponsored & Featured projects followed by Sponsored projects followed by Featured projects followed by others in ascending order of project start time.',
                            'featuredSponsored' => 'Features & Sponsored projects followed by Featured projects followed by Sponsored projects followed by others in ascending order of project start time.',
                        ),
                        'value' => 'startDate'
                    ),
                ),
                $showContentElement1,
                array(
                    'Text',
                    'gridItemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of Projects to show in Grid View)',
                        'value' => 8,
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
                        'description' => '(Number of Projects to show in List View)',
                        'value' => 8,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $titleTruncationGridViewElement1,
                $titleTruncationListViewElement1,
                $descriptionTruncationElement1,
                $detactLocationElement1,
                $defaultLocationDistanceElement1,
                $truncationLocationElement1,
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Followers',
        'description' => 'This widget will list all followers of this Page ',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-followers',
        'defaultParams' => array(
            'title' => 'Followers',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Page Profile peoples',
        'description' => 'This widget will list all type of peoples(eg: followers, admin, members) of this Page ',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-peoples',
        'defaultParams' => array(
            'title' => 'Peoples',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'peopleNavigationLink',
                    array(
                        'label' => 'Choose the options that you want to display for the Peoples in this block.',
                        'multiOptions' => array(
                            'all' => 'All',
                            'creator' => 'Creator',
                            'joined' => 'Joined',
                            'followed' => 'Followed',
                            'admin' => 'Admin'
                        ),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => 'Page Profile Category',
        'description' => 'This widget will list all type of categories',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-category',
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
                $showAllCategoriesElement,
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
        'title' => 'Page Profile Information Page',
        'description' => 'Displays the owner, category, tags, views and other information about a Page. This widget should be placed on the Page Profile in the left column.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.information-sitepage',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => array("ownerPhoto", "ownerName", "modifiedDate", "viewCount", "likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("ownerPhoto" => "Page Owner's Photo", "ownerName" => "Owner's Name", "modifiedDate" => "Modified Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "tags" => "Tags", "location" => "Location", "price" => "Price", "memberCount" => 'Member', "followerCount" => 'Follower', "categoryName" => "Category"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Photo',
        'description' => 'Displays the main cover photo of a Page. This widget must be placed on the Page Profile at the top of left column.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.mainphoto-sitepage',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Map',
        'description' => 'This widget forms the Map tab on the Page Profile. It displays the map showing the Page position as well as the location details of the page. It should be placed in the Tabbed Blocks area of the Page Profile. This feature will be available to Pages based on their Package and Member Level of their owners.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.location-sitepage',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Options',
        'description' => 'Displays the various action link options to users viewing a Page. This widget should be placed on the Page Profile in the left column, below the Page profile photo.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.options-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Owner Page Tags',
        'description' => 'Displays all the tags chosen by the Page owner for his Pages. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.tags-sitepage',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Owner Pages',
        'description' => 'Displays list of other pages owned by the page owner.This widget should be placed on Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.userpage-sitepage',
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
                        'description' => '(number of pages to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
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
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => array(
                            'view_count' => 'Views',
                            'like_count' => 'Likes',
                        ),
                        'value' => 'view_count',
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
        'title' => 'Page Profile About Page',
        'description' => 'Displays the About Us information for pages. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.write-page',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Content Profile: Follow Button',
        'description' => 'This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'seaocore.seaocore-follow',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Content Profile: Like Button for Content',
        'description' => 'This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Page Profile You May Also Like',
        'description' => 'Displays list of pages that might be liked by user based on the page being currently viewed.This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.suggestedpage-sitepage',
        'defaultParams' => array(
            'title' => 'You May Also Like',
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
        'title' => 'Content Profile: Content Likes',
        'description' => 'Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'seaocore.people-like',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of users to show)',
                        'value' => 3,
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
        'title' => 'Page Profile Page Insights',
        'description' => 'Displays the insights of a Page to its Page Admins. These insights include metrics like views, likes, comments and active users of the Page. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.insights-sitepage',
        'defaultParams' => array(
            'title' => 'Insights',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Alternate Thumb Photo',
        'description' => 'Displays the thumb photo of a Page. This works as an alternate profile photo when you have set the layout of Page Profile to be tabbed, from the Page Layout Settings, and have integrated with the "Advertisements / Community Ads Plugin" by SocialEngineAddOns. In that case, the left column of the Page Profile having the main profile photo gets hidden to accomodate Ads. This widget must be placed on the Page Profile at the top of middle column.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.thumbphoto-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showTitle',
                    array(
                        'label' => 'Show Page Profile Title.',
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
        'title' => 'Page Profile Apps Links',
        'description' => "Displays the Apps related links like Documents, Form, Photos, Poll, etc on Page Profile, depending on the Pages related apps installed on your site. This widget should be placed on the Page Profile in the left column.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.widgetlinks-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Page Profile Linked Pages',
        'description' => 'Displays list of pages linked to a page. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.favourite-page',
        'defaultParams' => array(
            'title' => 'Linked Pages',
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
                    )
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
        'title' => 'Page Profile Featured Page Admins',
        'description' => "Displays the Featured Admins of a page. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.featuredowner-sitepage',
        'defaultParams' => array(
            'title' => "Page admins",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Horizontal Search Pages Form',
        'description' => "This widget searches over Page Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Pages',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitepage.horizontal-searchbox-sitepage',
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
                        'multiOptions' => array("category" => "Category", "subcategory" => "Sub-category", "subsubcategory" => "3rd level category"),
                    ),
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 pages in them?',
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
        'title' => 'AJAX Search for Pages',
        'description' => "This widget searches over Page Titles via AJAX. The search interface is similar to Facebook search.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.searchbox-sitepage',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Alphabetic Filtering of Pages',
        'description' => "This widget enables users to alphabetically filter the directory items / pages on your site by clicking on the desired alphabet. The widget shows all the alphabets for filtering.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.alphabeticsearch-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Categorically Popular Pages',
        'description' => 'This attractive widget categorically displays the most popular pages on your site. It displays 5 Pages for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.category-pages-sitepage',
        'defaultParams' => array(
            'title' => 'Popular Pages',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Category Count',
                        'description' => 'No. of Categories to show. Enter 0 for showing all categories.',
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'pageCount',
                    array(
                        'label' => 'Pages Count per Category',
                        'description' => 'No. of Pages to be shown in each Category.',
                        'value' => 5,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $category_pages_multioptions,
                        'value' => 'view_count',
// 											'onchange'=>'javascript:if($("popularity").value=="view_count"){ $("interval-wrapper").style.display = "none";}else{$("interval-wrapper").style.display = "block"; }',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to all Popularity Criteria except Views.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Select',
                    'columnCount',
                    array(
                        'label' => 'Select categories to be displayed in a row.',
                        'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'),
                        'value' => '2',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Contact Details',
        'description' => "Displays the Contact Details of a page. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.contactdetails-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'contacts' => array("0" => "1", "1" => "2", "2" => "3"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'contacts',
                    array(
                        'label' => 'Select the contact details you want to display',
                        'multiOptions' => array("1" => "Phone", "2" => "Email", "3" => "Website"),
                    ),
                ),
                array(
                    'Radio',
                    'emailme',
                    array(
                        'label' => 'Do you want users to send emails to Pages via a customized pop up when they click on "Email Me" link?',
                        'multiOptions' => array(
                            1 => 'Yes, open customized pop up',
                            0 => 'No, open browser`s default pop up'
                        ),
                        'value' => '0'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Profile Render Layout',
        'description' => "Displays the layout of the page when site header and footer are not rendered.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.onrender-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Popular / Recent / Random / Location Based Pages: Pinboard View',
        'description' => 'Displays pages based on popularity criteria, location and other settings that you want to choose for this widget in attractive Pinboard View. You can place this widget multiple times with different popularity criterion chosen for each placement. You can also choose to display pages based on user’s current location by using the Edit Settings of this widget.',
        'category' => 'Pages',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitepage.pinboard-pages',
        'defaultParams' => array(
            'title' => 'Recent',
            'statistics' => array("likeCount", "commentCount"),
            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
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
                    'fea_spo',
                    array(
                        'label' => 'Show Pages',
                        'multiOptions' => array(
                            '' => '',
                            'random' => 'Random',
                            'featured' => 'Featured Only',
                            'sponsored' => 'Sponsored Only',
                            'fea_spo' => 'Both Featured and Sponsored',
                        ),
                        'value' => '',
                    )
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display pages based on user’s current location? (Note:- For this you must be enabled the auto-loading.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
                    'Select',
                    'locationmiles',
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
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $pinboardPopularityOptions,
                        'value' => 'page_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the page.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'showoptions',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $pinboardShowsOptions,
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
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
                        'description' => '(number of Pages to show)',
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
                        'label' => 'Choose the action links that you want to be available for the Pages displayed in this block.',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the truncation limit for the Page Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
            ),
        ),
    ),
//    
//        array(
//        'title' => 'Page Home: Pinboard View',
//        'description' => 'Displays pages in Pinboard View on the Pages Home page. Multiple settings are available to customize this widget.',
//        'category' => 'Pages',
//        'type' => 'widget',
//        'autoEdit' => true,
//        'name' => 'sitepage.pinboard-pages-sitepage',
//        'defaultParams' => array(
//            'title' => 'Recent',
//            'statistics' => array("likeCount", "reviewCount"),
//            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                //$listingTypeCategoryElement,
//               // $ratingTypeElement,
//                $featuredSponsoredElement,
//                array(
//                    'Select',
//                    'popularity',
//                    array(
//                        'label' => 'Popularity Criteria',
//                        'multiOptions' => $popularity_options,
//                        'value' => 'page_id',
//                    )
//                ),
//                array(
//                    'Select',
//                    'interval',
//                    array(
//                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)',
//                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall',
//                        'value' => 'overall',
//                    )
//                ),
//                //$categoryElement,
//                //$hiddenCatElement,
//                //$hiddenSubCatElement,
//                //$hiddenSubSubCatElement,
//                //$statisticsElement,
//                array(
//                    'Radio',
//                    'postedby',
//                    array(
//                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the listing.)',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '1',
//                    )
//                ),
//                array(
//                    'Select',
//                    'autoload',
//                    array(
//                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '1'
//                    )
//                ),
//                array(
//                    'Text',
//                    'itemWidth',
//                    array(
//                        'label' => 'One Item Width',
//                        'description' => 'Enter the width for each pinboard item.',
//                        'value' => 237,
//                    )
//                ),
//                array(
//                    'Radio',
//                    'withoutStretch',
//                    array(
//                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '0',
//                    )
//                ),
//                array(
//                    'Text',
//                    'itemCount',
//                    array(
//                        'label' => 'Count',
//                        'description' => '(number of Listings to show)',
//                        'value' => 3,
//                    )
//                ),
//                array(
//                    'Text',
//                    'noOfTimes',
//                    array(
//                        'label' => 'Auto-Loading Count',
//                        'description' => 'Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
//                        'value' => 0,
//                    )
//                ),
//                array(
//                    'MultiCheckbox',
//                    'show_buttons',
//                    array(
//                        'label' => 'Choose the action links that you want to be available for the Listings displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
//                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
//                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
//                    ),
//                ),
//                array(
//                    'Text',
//                    'truncationDescription',
//                    array(
//                        'label' => "Enter the trucation limit for the Listing Description. (If you want to hide the description, then enter '0'.)"),
//                        'value' => 100,
//                    )
//                ),
//            ),
//        ),
//    ),
    array(
        'title' => 'Search Pages Location Form',
        'description' => 'Displays the form for searching Pages corresponding to location on the basis of various filters.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.location-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'advancedsearchLink',
                    array(
                        'label' => 'Show Advanced Search option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => 'Show street option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'city',
                    array(
                        'label' => 'Show city option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'state',
                    array(
                        'label' => 'Show state option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'country',
                    array(
                        'label' => 'Show country option.',
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
        'title' => 'Ajax Based Recently Posted, Popular, Random, Featured and Sponsored Pages',
        'description' => "Displays the recently posted, popular, random, featured and sponsored pages in a block in separate ajax based tabs respectively.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.recently-popular-random-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'layouts_tabs' => $layouts_tabs,
            'recent_order' => 1,
            'popular_order' => 2,
            'random_order' => 3,
            'featured_order' => 4,
            'sponosred_order' => 5,
            'list_limit' => 10,
            'grid_limit' => 15,
            'columnWidth' => '188',
            'columnHeight' => '350',
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you want to show the "Explore All" link and redirect it to "Browse Page" then please use this code <a href="/'.$routeStartP.'/index">Explore All</a> Otherwise simply leave this field empty, if you do not want to show any link.',
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
//                array(
//                    'Text',
//                    'photoHeight',
//                    array(
//                        'label' => 'Enter the height of image.',
//                    )
//                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of image.',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for pages on the pages home and browse pages.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_oder',
                    array(
                        'label' => 'Select a default view type for Directory Items / Pages.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                    )
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display pages based on user’s current location? (Note:- For this you must be enabled the auto-loading.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
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
                ),
                array(
                    'Text',
                    'list_limit',
                    array(
                        'label' => 'List View (Limit)',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Text',
                    'grid_limit',
                    array(
                        'label' => 'Grid View (Limit)',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '188',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '350',
                    )
                ),
                array(
                    'Text',
                    'listview_turncation',
                    array(
                        'label' => 'Title Truncation Limit For List View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Text',
                    'turncation',
                    array(
                        'label' => 'Title Truncation Limit For Grid View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Radio',
                    'showlikebutton',
                    array(
                        'label' => 'Do you want to show “Like Button” when users mouse over on Pages in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showlocation',
                    array(
                        'label' => 'Do you want “Location” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showgetdirection',
                    array(
                        'label' => 'Do you want “Get Direction” link for the pages having location to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showprice',
                    array(
                        'label' => 'Do you want “Price” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showpostedBy',
                    array(
                        'label' => 'Do you want “Posted By” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showdate',
                    array(
                        'label' => 'Do you want “Creation Date” of the Pages to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'layouts_tabs',
                    array(
                        'label' => 'Choose the ajax tabs that you want to be there in the Main Pages Home Widget.',
                        'multiOptions' => $layouts_tabs_options,
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
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Pages in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
                array(
                    'Text',
                    'recent_order',
                    array(
                        'label' => 'Recent Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'popular_order',
                    array(
                        'label' => 'Most Popular Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'random_order',
                    array(
                        'label' => 'Random Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'featured_order',
                    array(
                        'label' => 'Featured Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'sponosred_order',
                    array(
                        'label' => 'Sponosred Tab (order)',
                    ),
                ),
                $joined_order,
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
          'title' => "My Pages: Pages' links",
          'description' => $linkDescription,
          'category' => 'Pages',
          'type' => 'widget',
          'name' => 'sitepage.links-sitepage',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
              'showLinks' => $linksValues
          ),
      'adminForm' => array(
          'elements' => array(
            array(
                'MultiCheckbox',
                'showLinks',
                array(
                    'label' => 'Choose the action links that you want to be available for the Pages displayed in this block.',
                    'multiOptions' => $linksOptions,
                  //  'value' => $linksValues
                ),
            ),
          ),
        ),
    ),
    array(
         'title' => 'My Pages: User’s Pages',
        'description' => 'Displays a list of all the pages joined, owned, admin, etc. of a user on your site. This widget should be placed on Directory / Pages - Manage Pages page.',
          'category' => 'Pages',
          'type' => 'widget',
          'name' => 'sitepage.manage-sitepage',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
          ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'pageAdminJoined',
                    array(
                        'label' => 'Which all Pages related to the user do you want to display in this widget?',
                        'multiOptions' => array(
                              1 => 'Both Pages Administered and Joined by user',
                              2 => 'Only Pages Administered by user'
                        ),
                        'value' => '2'
                    )
                ),
                array(
                    'Radio',
                    'showOwnerInfo',
                    array(
                        'label' => 'Do you want to show the member information (You are owner / admin / member) for the page. If enabled this information will be shown to you on Directory / Pages - Manage Pages page.',
                        'multiOptions' => array(
                              1 => 'Yes',
                              0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
            ),
        ),
    ),    
    array(
          'title' => 'My Pages: Search Pages Form',
          'description' => 'Displays the form for searching Pages on the basis of various fields and filters. Settings for this form can be configured from the Search Form Settings section. This widget should be placed on Directory / Pages - Manage Pages page.',
          'category' => 'Pages',
          'type' => 'widget',
          'name' => 'sitepage.manage-search-sitepage',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
          ),
    ),     
    array(
         'title' => 'Operating Hours',
        'description' => 'Add the Operating Hours of your Page. This widget should be placed on the Page Profile.',
          'category' => 'Page Profile',
          'type' => 'widget',
          'name' => 'sitepage.timing-sitepage',
          'defaultParams' => array(
              'title' => 'Operating Hours',
              'titleCount' => true,
          ),
    ),
    array(
         'title' => 'Page Status',
        'description' => 'Display the status of particular page',
          'category' => 'Pages',
          'type' => 'widget',
          'name' => 'sitepage.status-sitepage',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
          ),
    ),
    array(
        'title' => 'Search Pages Form (Pinboard Results)',
        'description' => 'Displays the form for searching Pages based on location and various filters. (Note: This widget supports both Pinboard and normal layouts for search results.)',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.horizontal-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'browseredirect',
                    array(
                        'label' => 'Choose the layout of browse page where you want to display the search results. (If you are placing this widget on a page having Pinboard layout, then choose Pinboard layout below.)',
                        'multiOptions' => array(
                            'pinboard' => 'Pinboard Layout',
                            'default' => 'Normal Layout'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Radio',
                    'advancedsearchLink',
                    array(
                        'label' => 'Show Advanced Search option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => 'Show street option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'city',
                    array(
                        'label' => 'Show city option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'state',
                    array(
                        'label' => 'Show state option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'country',
                    array(
                        'label' => 'Show country option.',
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
        'title' => $view->translate('My Pages'),
        'description' => $view->translate("This widget lists user’s pages."),
        'category' => 'Pages',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitepage.my-pages',
        'defaultParams' => array(
            'title' => 'My Pages',
         ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'userPageNavigationLink',
                    array(
                        'label' => 'Choose the options that you want to display for the user in this block.',
                        'multiOptions' => array(
                            'created_pages' => 'My Pages',
                            'followed_pages' => 'Followed Pages',
                            'joined_pages' => 'Joined Pages'
                        ),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'layout_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for pages on the pages home and browse pages.',
                        'multiOptions' => array("GridView" => "Grid View", "ListView" => "List View", "MapView" => "Map View"),
                    ),
                ),
                array(
                    'Radio',
                    'selected_layout_view',
                    array(
                        'label' => 'Select a default view type for Directory Items / Pages.',
                        'multiOptions' => array("GridView" => "Grid View", "ListView" => "List View", "MapView" => "Map View"),
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '188',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '350',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('My Pages and I as Admin to Pages '),
        'description' => $view->translate("This widget lists user’s pages and pages where i am admin"),
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.user-admin-and-own-pages',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'My Pages',
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Map Markers'),
        'description' => $view->translate("This widget forms the Map tab on the Page Profile. It displays the map showing the Page position as well its followers,members,projects,admins. "),
        'category' => 'Page Profile',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitepage.sitepage-map',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        )
    ),
    array(
        'title' => $view->translate('Page Profile: Navigator'),
        'description' => $view->translate("Navigator for page profile page. This widget should be placed on Page Profile page."),
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-profile-navigator',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile: Settings'),
        'description' => $view->translate("Settings for page profile page. This widget should be placed on Page Profile page."),
        'category' => 'Page Settings',
        'type' => 'widget',
        'name' => 'sitepage.page-settings',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile: Partner Pages'),
        'description' => $view->translate("This widget list of partner organisations."),
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-profile-partners',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Page - Initiatives',
        'description' => 'Page Profile Initiatives',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-profile-initiatives',
        'defaultParams' => array(
            'title' => 'Initiatives'
        ),
    ),
    array(
        'title' => 'Ajax Based Main Projects Home Widget' . $locationScript . $selectProjectScript,
        'description' => "Contains multiple Ajax based tabs showing Recently Posted, Most Liked, Most Commented, Most Backed and Random Projects in a block in separate ajax based tabs. You can configure various settings for this widget from the Edit section of this widget.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitepage.ajax-based-projects-home',
        'defaultParams' => array(
            'title' => "",
            "defaultViewType" => "listZZZview",
            'ajaxTabs' => array('random','mostZZZrecent', 'mostZZZcommented', 'mostZZZbacked' ,'mostZZZfunded', 'mostZZZliked','mostZZZfavourite')
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Ajax Based Main Projects Home Widget',
                    )
                ),
                $projectCategoryElement,
                $projectSubCategoryElement,
                $hiddenProjectCatElement,
                $hiddenProjectSubCatElement,
                $hiddenProjectSubSubCatElement,
                $gridViewWidthElement,
                $gridViewHeightElement,
                array(
                    'MultiCheckbox',
                    'viewType',
                    array(
                        'label' => 'Select the view type for Projects',
                        'multiOptions' => array(
                            'gridZZZview' => 'Grid view',
                            'listZZZview' => 'List view',
                            'mapZZZview' => 'Map view',
                        ),
                    )
                ),
                array(
                    'Select',
                    'defaultViewType',
                    array(
                        'label' => 'Select a default view type for Projects',
                        'multiOptions' => array(
                            'gridZZZview' => 'Grid view',
                            'listZZZview' => 'List view',
                        ),
                        'value' => 'listZZZview',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multioptions' => array_merge($projectOptions, $socialShareOptions),
                    )
                ),
                array(
                    'Select',
                    'selectProjects',
                    array(
                        'label' => 'Select Projects based on status.',
                        'multiOptions' => array(
                            'all' => 'All',
                            'ongoing' => 'Ongoing',
                            'successful' => 'Successful'
                        ),
                        'value' => 'all',
                        'onchange' => "showOngoingOptions(this.value)"
                    )
                ),
                $daysFilterElement,
                $backedPercentFilterElement,
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array(
                            "random" => "Random",
                            "mostZZZrecent" => "Most Recent",
                            "mostZZZcommented" => "Most Commented",
                            "mostZZZbacked" => "Most Backed",
                            "mostZZZfunded" => "Most Funded",
                            "mostZZZliked" => "Most Liked",
                            "mostZZZfavourite" => "Most Favourite",
                        )
                    )
                ),
                array(
                    'Text',
                    'randomOrder',
                    array(
                        'label' => 'Random Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'recentOrder',
                    array(
                        'label' => 'Most Recent Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'commentedOrder',
                    array(
                        'label' => 'Most Commented Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'backedOrder',
                    array(
                        'label' => 'Most Backed Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'fundedOrder',
                    array(
                        'label' => 'Most Funded Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'likedOrder',
                    array(
                        'label' => 'Most Liked Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'favouriteOrder',
                    array(
                        'label' => 'Most Favourite Tab (order)',
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
                $titleTruncationGridViewElement,
                $titleTruncationListViewElement,
                $descriptionTruncationElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
                $truncationLocationElement,
                $loadByAjaxElement,
            )
        ),
    ),
    array(
        'title' => 'Page - Metrics',
        'description' => 'Page Profile Metrics',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-profile-metrics',
        'defaultParams' => array(
            'title' => 'Metrics'
        ),
    ),
    array(
        'title' => 'Page Slideshow',
        'description' => 'Displays Pages based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Pages',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitepage.pages-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showNavigationButton',
                    array(
                        'label' => "Do you want to enable the 'Prev' and 'Next' arrows on slideshows?",
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
                        'multiOptions' => array(
                            'start_date' => 'Most Recent',
                            'view' => 'Most Viewed',
                            'random' => 'Random',
                        ),
                        'value' => 'start_date',
                    )
                ),
                array(
                    'Radio',
                    'fullWidth',
                    array(
                        'label' => "Do you want to display the slideshow in full width?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'height',
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
                        'label' => 'What is the time delay you want between slide changes (in millisecs)?',
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
                        'label' => 'Title truncation limit of Project',
                        'value' => 20,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
                array(
                    'Text',
                    'descriptionTruncation',
                    array(
                        'label' => 'Description truncation limit of Project',
                        'value' => 100,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => 'Page Profile: Development Goal',
        'description' => 'This widget displays the united nations sustainable development goals. It should be placed in the tabbed block area of the “Page Profile” page. ',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.development-goals',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => 'Page Ads Widget',
          'description' => 'Displays community ads in various widgets and view pages of this plugin.',
          'category' => 'Pages',
          'type' => 'widget',
          'name' => 'sitepage.page-ads',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => true,
          ),
  ));
}


if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
  $joined_array = array(
      array(
          'title' => 'Joined / Owned Pages',
          'description' => 'Displays pages administered and joined by members on their profiles. This widget should be placed on the Member Profile page.',
          'category' => 'Pages',
          'type' => 'widget',
          'name' => 'sitepage.profile-joined-sitepage',
          'defaultParams' => array(
              'title' => 'Pages',
              'titleCount' => true,
          ),
          'adminForm' => array(
              'elements' => array(
                  array(
                      'Radio',
                      'pageAdminJoined',
                      array(
                          'label' => 'Which all Pages related to the user do you want to display in this tab widget on their profile?',
                          'multiOptions' => array(
                              1 => 'Both Pages Administered and Joined by user',
                              2 => 'Only Pages Joined by user'
                          ),
                          'value' => 1,
                      )
                  ),
                  array(
                      'Text',
                      'textShow',
                      array(
                          'label' => 'Enter the verb to be displayed for the page admin approved members. (If you do not want to display any verb, then simply leave this field blank.)',
                          'value' => 'Verified',
                      ),
                  ),
                  array(
                      'Radio',
                      'showMemberText',
                      array(
                          'label' => 'Show Member Text?',
                          'multiOptions' => array(
                              1 => 'Yes',
                              0 => 'No'
                          ),
                          'value' => 1,
                      )
                  ),
                  array(
                      'Select',
                      'category_id',
                      array(
                          'label' => 'Category',
                          'multiOptions' => $categories_prepared,
                      )
                  ),
              )
          ),
      )
  );
}

$fbpage_sitepage_Array = array(
    array(
        'title' => 'Page Profile: Facebook Like Box',
        'description' => 'This widget contains the Facebook Like Box which enables Page Admins to gain Likes for their Facebook Page from this website. The edit popup contains the settings to customize the Facebook Like Box. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.fblikebox-sitepage',
        'defaultParams' => array(
            'title' => ''
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    "Text",
                    "title",
                    array(
                        'label' => 'Title',
                        'value' => '',
                    )
                ),
                array(
                    "Text",
                    "fb_width",
                    array(
                        'label' => 'Width',
                        'description' => 'Width of the Facebook Like Box in pixels.',
                        'value' => '220',
                    )
                ),
                array(
                    "Text",
                    "fb_height",
                    array(
                        'label' => 'Height',
                        'description' => 'Height of the Facebook Like Box in pixels (optional).',
                        'value' => '588',
                    )
                ),
                array(
                    "Select",
                    "widget_color_scheme",
                    array(
                        'label' => 'Color Scheme',
                        'description' => 'Color scheme of the Facebook Like Box in pixels.',
                        'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                    )
                ),
                array(
                    "MultiCheckbox",
                    "widget_show_faces",
                    array(
                        //'label' => 'Show Profile Photos in this plugin.',
                        'description' => 'Show Faces',
                        'multiOptions' => array('1' => 'Show profile photos of users who like the linked Facebook Page in the Facebook Like Box.')
                    )
                ),
                array(
                    "Text",
                    "widget_border_color",
                    array(
                        'label' => 'Border Color',
                        'description' => 'Border color of the Facebook Like Box'
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_stream",
                    array(
                        'description' => 'Stream',
                        'multiOptions' => array('1' => 'Show the Facebook Page profile stream for the public feeds in the Facebook Like Box.'),
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_header",
                    array(
                        'description' => 'Header',
                        'multiOptions' => array('1' => "Show the 'Find us on Facebook' bar at top. Only shown when either stream or profile photos are present."),
                    )
                ),
            )
        )
        ));

// if (!empty($recentPopularWidgt)) {
//   $final_array = array_merge($final_array, $recentPopularWidgt);
// }
if (!empty($joined_array)) {
  $final_array = array_merge($final_array, $joined_array);
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}
if (!empty($fbpage_sitepage_Array)) {
  $final_array = array_merge($final_array, $fbpage_sitepage_Array);
}
return $final_array;
?>
