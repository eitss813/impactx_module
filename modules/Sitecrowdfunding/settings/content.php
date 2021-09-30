<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

$enableSitealbum = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum');
//Categories element
$tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
$categories = $tableCategory->getCategories(array(), null, 0, 0, 1, 0);
$categoryParams = array();
$projectSubCategoryElement = array();
$projectCategoryElement = array();
if (count($categories) != 0) {
    $categoryParams[0] = '';
    foreach ($categories as $category) {
        $categoryParams[$category->category_id] = $category->category_name;
    }
    $projectCategoryElement = array(
        'Select',
        'category_id',
        array(
            'label' => 'Category',
            'multiOptions' => $categoryParams,
            'RegisterInArrayValidator' => false,
            'onchange' => 'addProjectOptions(this.value, "cat_dependency", "subcategory_id", 0); setProjectHiddenValues("category_id")'
    ));
    $projectSubCategoryElement = array(
        'Select',
        'subcategory_id',
        array(
            'RegisterInArrayValidator' => false,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => 'application/modules/Sitecrowdfunding/views/scripts/_category.tpl',
                        'class' => 'form element')))
    ));
}
$hiddenProjectCatElement = array(
    'Hidden',
    'hidden_project_category_id',
    array('order' => 996
        ));

$hiddenProjectSubCatElement = array(
    'Hidden',
    'hidden_project_subcategory_id',
    array('order' => 995
        ));
$hiddenProjectSubSubCatElement = array(
    'Hidden',
    'hidden_project_subsubcategory_id',
    array('order' => 994
        ));
$rowHeight = array(
    'Text',
    'rowHeight',
    array(
        'label' => 'Enter the row height of each photo block. (in pixels) [This row height is used as a base height to create justified view. Height of the resulting rows could be slightly lesser than your entered row height.]',
        'value' => 205,
    )
);
$maxRowHeight = array(
    'Text',
    'maxRowHeight',
    array(
        'label' => 'Enter the max row height of each photo block. (in pixels) [This is the maximum row height to be allowed to create justified view.  Height of the resulting rows could be higher / lesser than your entered maximum row height to fit any photo within limit.]',
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
$locationScript = "<script>

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

$daysFilterElement = array(
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
$backedPercentFilterElement = array(
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
$selectProjectScript = "<script>

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

$projectOptions = array(
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
$socialShareOptions = array(
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
    'linkedin' => 'Linkedin',
    'googleplus' => 'Google+'
);
$startDateOption = array(
    'startDate' => 'Start Date'
);
$detactLocationElement = array();
$truncationLocationElement = array();
$defaultLocationDistanceElement = array();
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
    $projectOptions = array_merge($projectOptions, array('location' => 'Location'));
    $truncationLocationElement = array(
        'Text',
        'truncationLocation',
        array(
            'label' => 'Truncation limit of location (Depend on location)',
            'value' => 35,
        )
    );
    $detactLocationElement = array(
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
        $locationDescription = "Choose the kilometers within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
        $locationLableS = "Kilometer";
        $locationLable = "Kilometers";
    } else {
        $locationDescription = "Choose the miles within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
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

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which projects will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}

$showContentElement = array(
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
$titleTruncationElement = array(
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
$titleTruncationGridViewElement = array(
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
$titleTruncationListViewElement = array(
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
$descriptionTruncationElement = array(
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
$loadByAjaxElement = array(
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
$viewTypeElement = array(
    'MultiCheckbox',
    'viewType',
    array(
        'label' => 'Select the view types for Projects',
        'multiOptions' => array(
            'gridView' => 'Grid view',
            'listView' => 'List view',
            'mapView' => 'Map view',
        ),
    )
);
$defaultViewTypeElement = array(
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
$showAllCategoriesElement = array(
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
$projectHeight = array(
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
$projectWidth = array(
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
$selectProjects = array(
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
$gridViewWidthElement = array(
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
$gridViewHeightElement = array(
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
$final_array = array(
    array(
        'title' => 'Project Profile: Overview',
        'description' => 'This widget forms the Overview tab on the  “Project View Page” and displays the overview of the project, which the owner has created using the editor in project dashboard. This widget should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-overview',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                $loadByAjaxElement,
                array(
                    'Radio',
                    'showComments',
                    array(
                        'label' => 'Enable Comments',
                        'description' => 'Do you want to enable comments in this widget? (If enabled, then users will be able to comment on the project being viewed.) ',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Project Profile: Quick Information (Profile Fields)',
        'description' => 'Displays the questions enabled to be shown in this widget from the "Profile Fields" section in the admin panel. This widget should be placed in the right / left column on the “Crowdfunding - Project Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.quick-specification-project',
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
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Project Profile: Information (Profile Fields)',
        'description' => 'Displays the questions added from the "Profile Fields" section in the admin panel. This widget should be placed in the tabbed block area of “Crowdfunding - Projects Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.specification-project',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                $loadByAjaxElement,
            )
        )
    ),

    array(
        'title' => 'Project Profile: Initiative Answers',
        'description' => 'This widget displays the project backstory. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-initiativeanswers',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => $view->translate('My Projects and I as Admin to Projects '),
        'description' => $view->translate("This widget lists user’s projects and projects where i am admin"),
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.user-admin-and-own-projects',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'My Projects',
        ),
    ),
    array(
        'title' => $view->translate('Explore Projects '),
        'description' => $view->translate("This widget lists projects randomly"),
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.explore-projects',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Explore Projects',
        ),
    ),
    array(
        'title' => 'Project Profile: Project Photos' . $onloadScript,
        'description' => 'This widget forms the Photos tab on the “Project Profile Page” and displays the photos of the project. This widget should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-photos',
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
                        'label' => 'Width of Project Photo',
                        'value' => 80,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Height of Project Photo',
                        'value' => 80,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                $loadByAjaxElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of photos to show)',
                        'value' => 20,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => 'Project Profile: Milestone',
        'description' => 'This widget displays the milestone detail of the project. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-milestone',
        'defaultParams' => array(
            'title' => 'Milestones',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Development Goal',
        'description' => 'This widget displays the united nations sustainable development goals. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.development-goals',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Development Goal Chart',
        'description' => 'This widget displays the united nations sustainable development goals as chart view. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.development-goal-chart',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Custom Organizations',
        'description' => 'This widget displays the organization connected this project. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-organizations',
        'defaultParams' => array(
            'title' => 'Organizations',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Map',
        'description' => 'This widget forms the Map tab on the “Project View Page”. It displays the map showing the Project position and the location details. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-location',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Project Discussions',
        'description' => 'This widget forms the Discussions tab on the “Project View Page” and displays the discussions of the Project. This widget should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-discussion',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                $loadByAjaxElement,
            )
        )
    ),
    array(
        'title' => 'Discussion Topic View: Discussion Topic',
        'description' => "Displays discussion topic of a project currently being viewed. This widget should be placed on the “Crowdfunding - Discussion Topic View” page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.discussion-content',
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
                        'label' => 'Enter the text to be displayed as link.',
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
        'title' => 'Additional',
        'description' => 'This widget displays a link allowing users to back the Project.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.additional',
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_Additional',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'backTitle' => 'Additional'
        ),
    ),
    array(
        'title' => 'Back Project',
        'description' => 'This widget displays a link allowing users to back the Project.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.back-project',
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_Backproject',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'backTitle' => 'Back This Project'
        ),
    ),

    array(
        'title' => 'Browse Projects' . $locationScript,
        'description' => 'Displays a list of all the Projects present on your website. This widget should be placed on “Browse Projects” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.browse-projects',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'projectOption' => array('title', 'owner', 'like', 'facebook', 'twitter', 'linkedin', 'googleplus'),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $selectProjects,
                $viewTypeElement,
                $defaultViewTypeElement,
                $gridViewWidthElement,
                $gridViewHeightElement,
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multioptions' => array_merge($projectOptions, $startDateOption, $socialShareOptions),
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
                $showContentElement,
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
                $titleTruncationGridViewElement,
                $titleTruncationListViewElement,
                $descriptionTruncationElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
                $truncationLocationElement,
            ),
        ),
    ),
    array(
        'title' => 'Search Form - Projects',
        'description' => 'Displays the form for searching Projects on the basis of various fields and filters.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.search-project-sitecrowdfunding',
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
                        'label' => 'Show search form',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'vertical'
                    )
                ),
                $showAllCategoriesElement,
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
        'title' => 'Popular Project Tags',
        'description' => "Displays popular tags. You can choose to display tags based on their frequency / alphabets from the Edit Settings of this widget. This widget should be placed in the left / right side bar on the “Crowdfunding - Browse Project's” page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.tagcloud-sitecrowdfunding-project',
        'defaultParams' => array(
            'title' => 'Popular Tags',
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
                        'label' => 'Do you want to show popular Project tags in alphabetical order?',
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
                        'description' => '(Number of tags to show)',
                        'value' => 25,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showMoreTag',
                    array(
                        'label' => 'Do you want to show Explore Tags link?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                $loadByAjaxElement,
            ),
        ),
    ),
    array(
        'title' => 'Special Projects',
        'description' => 'Displays few Projects on your site as Special Projects. You can choose these Projects by editing the settings of this widget. This widget should be placed in the left / right sidebar of the widgetized page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.special-projects',
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_Specialprojects',
        'defaultParams' => array(
            'title' => 'Special Projects',
        ),
    ),
    array(
        'title' => 'Browse Projects’ Location',
        'description' => "Displays a form to search Projects corresponding to various locations.  Search results can vary on the basis of various filters and Projects’ location. This widget must be placed on “Crowdfunding - Browse Projects' Location” page",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.browselocation-sitecrowdfunding',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $viewTypeElement,
                $defaultViewTypeElement,
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
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block',
                        'multioptions' => array_merge($projectOptions, $socialShareOptions),
                    )
                ),
                $titleTruncationGridViewElement,
                $titleTruncationListViewElement,
                $descriptionTruncationElement,
                $truncationLocationElement,
                $showAllCategoriesElement,
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
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 8,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Project Map',
        'description' => "Displays a form to search Projects corresponding to various locations.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-map',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $viewTypeElement,
                $defaultViewTypeElement,
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
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block',
                        'multioptions' => array_merge($projectOptions, $socialShareOptions),
                    )
                ),
                $titleTruncationGridViewElement,
                $titleTruncationListViewElement,
                $descriptionTruncationElement,
                $truncationLocationElement,
                $showAllCategoriesElement,
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
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 8,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Project Owner Information',
        'description' => 'Displays owners of a project currently being viewed. This widget should be placed in the left / right sidebar of the "Crowdfunding - Project Profile" page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-owner-information',
        'defaultParams' => array(
            'title' => 'Owned By',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'contactMeTitle',
                    array(
                        'label' => 'Enter title for “Contact Me” button.',
                        'value' => 'Contact Me',
                    )
                ),
                array(
                    'Text',
                    'seefullBioTitle',
                    array(
                        'label' => 'Enter title for “See full bio” button.',
                        'value' => 'See Full Bio',
                    )
                ),
            ),
        ),
    ),

    array(
        'title' => 'User Full Biography',
        'description' => 'Displays a Project owner’s biography on their profile page. This widget should be placed on “Member Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.user-biography',
        'requirements' => array(
            'subject' => 'user',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'userBioOption',
                    array(
                        'label' => 'Choose the options that you want to display in the user\'s full bio',
                        'multioptions' => array(
                            'email' => 'Email',
                            'phone' => 'Phone Number',
                            'biography' => 'Biography',
                            'facebook' => 'Facebook Profile',
                            'instagram' => 'Instagram Profile',
                            'twitter' => 'Twitter Profile',
                            'youtube' => 'Youtube Profile',
                            'vimeo' => 'Vimeo Profile',
                            'website' => 'Website URL',
                        ),
                        'value' => array('biography', 'facebook', 'instagram', 'twitter', 'youtube', 'vimeo', 'website'),
                    ),
                ),
                $titleTruncationElement,
            ),
        )
    ),

    array(
        'title' => 'Rewards Listing',
        'description' => 'Displays rewards slideshow of a Project. This widget should be placed on the “Crowdfunding - Project Profile” Page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.rewards-listing',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showSlide',
                    array(
                        'label' => 'Do you want to enable slideshow?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'slideHeight',
                    array(
                        'label' => 'Enter the height for each slide',
                        'value' => 250,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $descriptionTruncationElement,
            ),
        ),
    ),
    array(
        'title' => 'Landing Page: Featured Projects',
        'description' => 'Displays projects in an attractive slideshow. This widget should be placed on the landing page of the website.',
        'type' => 'widget',
        'category' => 'Crowdfunding',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.featured-fundraiser',
        'defaultParams' => array(
            'title' => 'Landing Page: Featured Projects',
            'titleCount' => true,
            'projectOption' => array('title', 'owner', 'like', 'facebook', 'twitter', 'linkedin', 'googleplus'),
        ),
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_Featuredprojects',
    ),
    array(
        'title' => 'Ajax Based Main Projects Home Widget' . $locationScript . $selectProjectScript,
        'description' => "Contains multiple Ajax based tabs showing Recently Posted, Most Liked, Most Commented, Most Backed and Random Projects in a block in separate ajax based tabs. You can configure various settings for this widget from the Edit section of this widget.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.ajax-based-projects-home',
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
        'title' => 'Content Type: Profile Projects',
        'description' => "Displays a list of Projects in the content currently being viewed. You can manage the content types for which Project owners will be able to manage Projects from the Manage Modules section of this plugin. This widget should be placed on content's Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.contenttype-projects',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Projects',
            'itemCountPerPage' => 40,
        ),
        'adminForm' => array(
            'elements' => array(
                $projectWidth,
                $projectHeight,
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multioptions' => array_merge($projectOptions, $socialShareOptions),
                    ),
                ),
                $showContentElement,
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of items to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                $descriptionTruncationElement,
                $titleTruncationElement,
            ),
        ),
    ),
    array(
        'title' => 'Project Profile: Announcements',
        'description' => 'Displays a list of announcements posted by Project owner for their Projects. This widget should be placed on the “Crowdfunding - Project Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.profile-announcements-sitecrowdfunding',
        'defaultParams' => array(
            'title' => 'Announcements',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showTitle',
                    array(
                        'label' => 'Show announcement title.',
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
                        'label' => 'Number of announcements to show',
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
        'title' => 'Project Categories: Displays Category with Background Image Slideshow',
        'description' => "Displays the Project categories with background image in a slideshow. This widget should be placed on the  “Project Categories Home” page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-categorybanner-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_ProjectCategoryBannerSlideshow',
        'autoEdit' => true,
    ),
    array(
        'title' => 'Project Categories: Displays Categories / Sub-categories in Grid View',
        'description' => 'Displays categories and sub-categories in grid view on “Project Categories Home” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.project-categories-grid-view',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'subCategoriesCount',
                    array(
                        'label' => 'Enter number of sub-categories / 3rd level categories to be shown on mouseover on a category / sub-category respectively.',
                        'value' => '5',
                        'maxlength' => 1
                    )
                ),
                array(
                    'Radio',
                    'showProjectCount',
                    array(
                        'label' => 'Show Project count along with the sub-category / 3rd level category name',
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
                        'value' => 234,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column height for Grid View.',
                        'value' => 216,
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
        'title' => 'Project Categories: Displays Categories / Sub-categories in Grid View with icons',
        'description' => 'Displays categories and sub-categories in grid view with icons on “Project Categories Home” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.project-categories-withicon-grid-view',
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
        'title' => 'Ajax Based Project Carousel' . $locationScript . $selectProjectScript,
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the Projects on your site. You can choose to show featured, sponsored Projects in this widget from the Edit section of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.project-carousel',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $projectCategoryElement,
                $projectSubCategoryElement,
                $hiddenProjectCatElement,
                $hiddenProjectSubCatElement,
                $hiddenProjectSubSubCatElement,
                $contentTypeElement,
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multioptions' => array(
                            'title' => 'Project Title',
                            'owner' => 'Owner',
                            'backer' => 'Backers',
                            'like' => 'Likes',
                            'favourite' => 'Favourites',
                            'comment' => 'Comment Count',
                            'endDate' => 'Remaining Days',
                            'featured' => 'Featured',
                            'sponsored' => 'Sponsored',
                            'facebook' => 'Facebook',
                            'twitter' => 'Twitter',
                            'linkedin' => 'Linkedin',
                            'googleplus' => 'Google+'
                        ),
                    )
                ),
                array(
                    'Select',
                    'showProject',
                    array(
                        'label' => 'Show Projects',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured only',
                            'sponsored' => 'Sponsored only',
                            'featuredSponsored' => 'Both Featured and Sponsored'
                        ),
                        'value' => 'recent',
                    )
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'start_date' => 'Recently Started',
                            'like' => 'Most Liked',
                            'backerCount' => 'Most Backed',
                            'comment' => 'Most Commented',
                            'random' => 'Random',
                            'mostFunded' => 'Most Funded'
                        ),
                        'value' => 'random',
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
                    'Radio',
                    'showPagination',
                    array(
                        'label' => 'Do you want to show next / previous pagination?',
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'projectWidth',
                    array(
                        'label' => 'Enter the width of each slideshow item.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'projectHeight',
                    array(
                        'label' => 'Enter the height of each slideshow item.',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showLink',
                    array(
                        'label' => "Do you want to show below link?",
                        'multiOptions' => array(1 => 'See All', 0 => 'Browse Button', '2' => 'None'),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'rowLimit',
                    array(
                        'label' => 'Enter number of Projects in a row for horizontal carousel type respectively as selected by you from the above setting.',
                        'value' => 3,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter total number of Projects to show in the horizontal carousel.',
                        'value' => 12,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(Transition interval between two slides in millisecs)',
                        'value' => 3500,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                $titleTruncationElement,
                $truncationLocationElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Project Categories: Displays Category with Background Image',
        'description' => "Displays the Project category with background image and other information. This widget should be placed on the “Crowdfunding - Project Category View” page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.project-categorybanner',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_ProjectCategorieBannerContent',
        'autoEdit' => true
    ),
    array(
        'title' => 'Project Category Navigation Bar',
        'description' => 'Displays different categories in this block. You can configure various settings for this widget from the Edit section.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-categories-navigation',
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
            )
        )
    ),
    array(
        'title' => 'Recent / Random / Popular Projects',
        'description' => 'Displays Projects based on the Popularity / Sorting Criteria and other settings that you have chosen for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement. This widget should be placed in the left / right sidebar of a widgetized page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.list-popular-projects',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Projects',
            'itemCountPerPage' => 4,
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $projectCategoryElement,
                $projectSubCategoryElement,
                $hiddenProjectCatElement,
                $hiddenProjectSubCatElement,
                $hiddenProjectSubSubCatElement,
                $selectProjects,
                array(
                    'Select',
                    'showProject',
                    array(
                        'label' => 'Show Projects',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured only',
                            'sponsored' => 'Sponsored only',
                            'featuredSponsored' => 'Both Featured and Sponsored'
                        ),
                        'value' => 'featuredSponsored',
                    )
                ),
                array(
                    'Text',
                    'projectWidth',
                    array(
                        'label' => 'Enter width of Project thumbnail image',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'projectHeight',
                    array(
                        'label' => 'Enter height of Project thumbnail image',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'start_date' => 'Recently Started',
                            'backed' => 'Most Backed',
                            'like' => 'Most Liked',
                            'rated' => 'Most Rated',
                            'comment' => 'Most Commented',
                            'mostFunded' => 'Most Funded'
                        ),
                        'value' => 'start_date',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to above selected Popularity / Sorting Criteria.)',
                        'multiOptions' => array(
                            'week' => '1 Week',
                            'month' => '1 Month',
                            'overall' => 'Overall'
                        ),
                        'value' => 'overall',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'projectInfo',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multiOptions' => array_merge($projectOptions, $socialShareOptions),
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
                $titleTruncationElement,
            )
        ),
    ),
    array(
        'title' => 'Create a Project',
        'description' => "Displays the start a project link .",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.create-project-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'create_button' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'create_button',
                    array(
                        'label' => 'How do you want to display Create a Project action in this widget ?',
                        'multiOptions' => array(
                            '1' => 'As a button',
                            '0' => 'As a link',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'create_button_title',
                    array(
                        'label' => 'Enter the text that displays on this button or link.',
                        'value' => 'Create a Project',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'People who Backed the Project',
        'description' => "Displays the list of people who backed a project. This widget should be placed in the left / right sidebar on the Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.people-who-backed',
        'defaultParams' => array(
            'title' => 'People who Backed the Project',
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
                    'MultiCheckbox',
                    'options',
                    array(
                        'label' => 'Choose the options that you want to display for the Backers in this block.',
                        'multiOptions' => array(
                            'name' => 'Name',
                            'amount' => 'Backed Amount',
                            'totalCount' => 'Backers Count'
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Sponsored Categories',
        'description' => 'Displays the Sponsored categories / sub-categories / 3rd level categories. You can make categories as Sponsored from "Categories" section of admin panel.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.project-categories-sponsored',
        'defaultParams' => array(
            'title' => 'Sponsored Project Categories',
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
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
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
        'title' => 'Main Project Information',
        'description' => 'Displays the project cover photo and information. You can choose various options from the Edit Settings of this widget.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.main-project-information',
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multiOptions' => array(
                            "title" => "Title",
                            "description" => "Project Description",
                            "owner" => "Project Owner",
                            "location" => "Location",
                            "fundingRatio" => "Funding Ratio",
                            "fundedAmount" => "Funded Amount",
                            "daysLeft" => "Days Left",
                            "backerCount" => "Backer Count",
                            "backButton" => "Back Button",
                            "category" => "Category",
                            "dashboardButton" => "Dashboard Button",
                            "shareOptions" => "Share Options (Facebook, Linkedin, Twitter, etc.)",
                            "optionsButton" => "Options Button (Edit Details, Add to Diary, etc.)"
                        ),
                    ),
                ),
                array('Text',
                    'columnHeight',
                    array(
                        'label' => 'Enter the Cover Photo/Video height (in px). (Minimum 150 px required.)',
                        'value' => 300,
                    )
                ),
                array(
                    'Text',
                    'titleTruncation',
                    array(
                        'label' => 'Title truncation limit of Project',
                        'value' => 120,
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
                        'value' => 450,
                    ),
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => 'Landing Page: Sponsored Categories With Image',
        'description' => 'This widget displays the sponsored categories with image.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.sponsored-categories-with-image',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter height for each block.',
                        'value' => 400,
                    )
                ),
            ))
    ),
    array(
        'title' => 'Project Profile: Backers',
        'description' => 'This widget forms the Backers tab on the “Project View Page” and displays the Backers of the Project. This widget should be placed in the tabbed block area of the “Crowdfunding - Project Profile” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-backers',
        'defaultParams' => array(
            'title' => 'Project Backers',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter height of the block.',
                        'value' => 220,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter width of the block.',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                $loadByAjaxElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of Backers to show)',
                        'value' => 5,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Featured Projects Slideshow',
        'description' => 'Displays Projects based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.featured-projects-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $projectCategoryElement,
                $projectSubCategoryElement,
                $hiddenProjectCatElement,
                $hiddenProjectSubCatElement,
                $hiddenProjectSubSubCatElement,
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
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'start_date' => 'Recently Started',
                            'like' => 'Most Liked',
                            'view' => 'Most Viewed',
                            'comment' => 'Most Commented',
                            'random' => 'Random',
                            'mostFunded' => 'Most Funded'
                        ),
                        'value' => 'random',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to above selected Popularity / Sorting Criteria.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
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
                $titleTruncationElement,
                $descriptionTruncationElement,
            )
        )
    ),
    array(
        'title' => 'Landing Page - Featured Projects Slideshow',
        'description' => 'Displays Projects based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.landing-page-featured-projects-slideshow',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $projectCategoryElement,
                $projectSubCategoryElement,
                $hiddenProjectCatElement,
                $hiddenProjectSubCatElement,
                $hiddenProjectSubSubCatElement,
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
                    'Select',
                    'popularType',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'start_date' => 'Recently Started',
                            'like' => 'Most Liked',
                            'view' => 'Most Viewed',
                            'comment' => 'Most Commented',
                            'random' => 'Random',
                            'mostFunded' => 'Most Funded'
                        ),
                        'value' => 'random',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to above selected Popularity / Sorting Criteria.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
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
                $titleTruncationElement,
                $descriptionTruncationElement,
            )
        )
    ),
    array(
        'title' => 'Landing Page: Best Projects Carousel',
        'description' => 'Displays few Projects as Best Projects in an attractive carousel. This widget should be placed on the landing page of website.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.best-projects',
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_BestProjects',
        'defaultParams' => array(
            'title' => 'Landing Page: Best Projects Carousel',
        ),
    ),
    array(
        'title' => 'My Projects',
        'description' => "This widget lists user’s Projects.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.my-projects',
        'defaultParams' => array(
            'title' => 'My Projects',
        ),
        'adminForm' => 'Sitecrowdfunding_Form_Admin_Widget_Content',
        'autoEdit' => true
    ),
    array(
        'title' => 'Navigation Tabs',
        'description' => 'Displays the Navigation tabs with links of Project Home, Browse Projects,My Projects, etc. This widget should be placed at the top of Projects Home page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.navigation',
        'defaultParams' => array(),
        'adminForm' => array(
            'elements' => array()
        ),
    ),
    array(
        'title' => 'Browse Projects: Pinboard View' . $locationScript,
        'description' => 'Displays a list of all the Projects on your site in attractive Pinboard View. You can also choose to display Projects based on user’s current location by using the edit section of this widget. It is recommended to place this widget on “Crowdfunding - Browse Project’s Pinboard View Page”.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.pinboard-browse-projects',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'projectOption' => array('title', 'owner', 'like', 'comment', 'backer', 'endDate')
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $projectCategoryElement,
                $projectSubCategoryElement,
                $hiddenProjectCatElement,
                $hiddenProjectSubCatElement,
                $hiddenProjectSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'projectOption',
                    array(
                        'label' => 'Choose the options that you want to display for the Projects in this block.',
                        'multiOptions' => array(
                            'title' => 'Project Title',
                            'owner' => 'Owner',
                            'backer' => 'Backers',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'endDate' => 'End Date and Time',
                            'location' => 'Location',
                        ),
                    ),
                ),
                array(
                    'Radio',
                    'userComment',
                    array(
                        'label' => 'Do you want to show user comments and enable user to post comment?',
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
                        'label' => 'Do you want to show a loading image when this widget renders on a page?',
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
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Projects displayed in this block.',
                        'multiOptions' => array_merge(
                                array(
                            "comment" => "Comment",
                            "like" => "Like / Unlike",
                            'favourite' => 'Favourites',
                                ), $socialShareOptions)
                    ),
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display Project images without stretching them to the width of each pinboard item?',
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
                        'label' => 'Default ordering in browse Projects.',
                        'multiOptions' => array(
                            'startDate' => 'All projects in descending order of start date.',
                            'startDateAsc' => 'All projects in ascending order of start date.',
                            'title' => 'All projects in alphabetical order.',
                            'sponsored' => 'Sponsored projects followed by others in ascending order of project start time.',
                            'featured' => 'Featured projects followed by others in ascending order of project start time.',
                            'sponsoredFeatured' => 'Sponsored & Featured projects followed by Sponsored projects followed by Featured projects followed by others in ascending order of project start time.',
                            'featuredSponsored' => 'Features & Sponsored projects followed by Featured projects followed by Sponsored projects followed by others in ascending order of project start time.',
                        ),
                        'value' => 'startDate',
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
                $truncationLocationElement,
                $titleTruncationElement,
                $descriptionTruncationElement,
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
            ),
        ),
    ),
    array(
        'title' => 'Horizontal Projects Search Form',
        'description' => "This widget searches over Project Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitecrowdfunding.searchbox-project',
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
                        'label' => 'Choose the options that you want to be displayed in this block.',
                        'description' => '(Note: Proximity Search will not display if location field is disabled.)',
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
                $showAllCategoriesElement,
                array(
                    'Text',
                    'textWidth',
                    array(
                        'label' => 'Width for Auto-suggest',
                        'value' => 275,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'locationWidth',
                    array(
                        'label' => 'Width for Location field',
                        'value' => 250,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'locationmilesWidth',
                    array(
                        'label' => 'Width for Proximity Search field',
                        'value' => 125,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'categoryWidth',
                    array(
                        'label' => 'Width for Category Filtering',
                        'value' => 150,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                $loadByAjaxElement,
            ),
        ),
    ),
    array(
        'title' => 'Browse Projects: Breadcrumb',
        'description' => 'Displays breadcrumb based on the categories searched in the “Search Form” widget. This widget should be placed on “Crowdfunding - Browse Projects” page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.browse-breadcrumb',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Reward: Project information',
        'description' => 'This widget shows basic information about the Project on “Reward Selection Page” when a user opt to back that Project. This widget should be placed on “Crowdfunding - Reward Selection” Page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-information',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $titleTruncationElement,
                $descriptionTruncationElement,
            ),
        ),
    ),
    array(
        'title' => 'Reward Information',
        'description' => 'This widget shows the amount backed and the reward selected while backing a Project. This widget should be placed on “Crowdfunding - Checkout” Page.',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.reward-information',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $titleTruncationElement,
                $descriptionTruncationElement,
            ),
        ),
    ),
    array(
        'title' => 'Project Profile: Project Status',
        'description' => "Displays Status of the project being currently viewed. This widget should be placed on Crowdfunding - Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-status',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Project Profile: Project Contact Details',
        'description' => "Displays contact details of the project",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-contact-details',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Project Profile: Project funding Status',
        'description' => "Displays Funding Status of the project being currently viewed. This widget should be placed on Crowdfunding - Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-funding-status',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Project Profile: Organizations',
        'description' => "Displays Organizations linked to this project being currently viewed. This widget should be placed on Crowdfunding - Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-pages',
        'defaultParams' => array(
            'title' => 'Organizations',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Project Profile: Navigator',
        'description' => "Navigator for project profile page. This widget should be placed on Crowdfunding - Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-profile-navigator',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Project Profile: Settings',
        'description' => "Settings for project profile page. This widget should be placed on Crowdfunding - Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-profile-settings',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Project Profile: Outcome and Output',
        'description' => "Navigator for project profile page. This widget should be placed on Crowdfunding - Project Profile page.",
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-outcome-output',
        'defaultParams' => array(
            'title' => 'Outcomes and Outputs',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Followers',
        'description' => 'Displays list of followers present in project',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-followers',
        'defaultParams' => array(
            'title' => 'Followers',
            'titleCount' => true
        ),
        'adminForm' => array(
            'elements' => array(
                $loadByAjaxElement,
            )
        )
    ),
    array(
        'title' => 'Project Profile: Peoples',
        'description' => 'Displays list of Peoples present in project',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-peoples',
        'defaultParams' => array(
            'title' => 'Peoples',
            'titleCount' => true
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'peopleNavigationLink',
                    array(
                        'label' => 'Choose the options that you want to display for the Peoples in this block.',
                        'multiOptions' => array(
                            'joined' => 'Joined Projects',
                            'followed' => 'Followed Projects',
                            'admin' => 'Admin Projects'
                        ),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => 'Display Projects in Map',
        'description' => 'Display Projects in Map',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.map',
        'defaultParams' => array(
            'title' => ''
        )
    ),
    array(
        'title' => 'Display Metrics',
        'description' => 'Display Metrics',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.metrics',
        'defaultParams' => array(
            'title' => ''
        )
    ),
    array(
        'title' => 'Project Profile: External funding',
        'description' => 'This widget displays the external funding details. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-external-funding',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Funding Chart',
        'description' => 'This widget displays the pie chart of funding details. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-funding-chart',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Backstory',
        'description' => 'This widget displays the project backstory. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-backstory',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Tags',
        'description' => 'This widget displays the project tags. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-profile-tags',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
    array(
        'title' => 'Project Profile: Category',
        'description' => 'This widget displays the project category. It should be placed in the tabbed block area of the “Crowdfunding - Projects Profile” page. ',
        'category' => 'Crowdfunding',
        'type' => 'widget',
        'name' => 'sitecrowdfunding.project-profile-category',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array()
    ),
);
return $final_array;
?>