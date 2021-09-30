<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: content.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */


  $socialshare_enable_plusicon = array(
      'Select',
      'socialshare_enable_plusicon',
      array(
          'label' => "Enable More Icon for social share buttons?",
          'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
          ),
      )
  );
  $socialshare_icon_limit = array(
    'Text',
    'socialshare_icon_limit',
    array(
      'label' => 'Count (number of social sites to show). If you enable More Icon, then other social sharing icons will display on clicking this plus icon.',
      'value' => 2,
    ),
  );

$headScript = new Zend_View_Helper_HeadScript();
$headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
$headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');

$categories = array();
if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.pluginactivated')) {
  $categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategoriesAssoc(array('module'=>true));
}

$pagging = array(
  'Radio',
  'pagging',
  array(
    'label' => "Do you want the blogs to be auto-loaded when users scroll down the page?",
    'multiOptions' => array(
	'auto_load' => 'Yes, Auto Load',
	'button' => 'No, show \'View more\' link.',
	'pagging' => 'No, show \'Pagination\'.'
    ),
    'value' => 'auto_load',
  )
);
$imageType = array(
    'Select',
    'imageType',
    array(
        'label' => "Choose the shape of Photo.",
        'multiOptions' => array(
            'rounded' => 'Circle',
            'square' => 'Square',
        ),
        'value' => 'square',
    )
);
$photoHeight = array(
    'Text',
    'photo_height',
    array(
        'label' => 'Enter the height of main photo block in Grid Views (in pixels).',
        'value' => '160',
    )
);
$photowidth = array(
    'Text',
    'photo_width',
    array(
        'label' => 'Enter the width of main photo block in Grid Views (in pixels).',
        'value' => '250',
    )
);
$titleTruncationList = array(
  'Text',
  'title_truncation_list',
  array(
    'label' => 'Title truncation limit for List Views.',
    'value' => 45,
    'validators' => array(
      array('Int', true),
      array('GreaterThan', true, array(0)),
    )
  )
);
$titleTruncationGrid = array(
  'Text',
  'title_truncation_grid',
  array(
    'label' => 'Title truncation limit for Grid Views.',
    'value' => 45,
    'validators' => array(
      array('Int', true),
      array('GreaterThan', true, array(0)),
    )
  )
);
$titleTruncationPinboard = array(
  'Text',
  'title_truncation_pinboard',
  array(
    'label' => 'Title truncation limit for Pinboard View.',
    'value' => 45,
    'validators' => array(
      array('Int', true),
      array('GreaterThan', true, array(0)),
    )
  )
);
$DescriptionTruncationList = array(
  'Text',
  'description_truncation_list',
  array(
    'label' => 'Description truncation limit for List Views.',
    'value' => 45,
    'validators' => array(
      array('Int', true),
      array('GreaterThan', true, array(0)),
    )
  )
);
$DescriptionTruncationGrid = array(
  'Text',
  'description_truncation_grid',
  array(
    'label' => 'Description truncation limit for Grid Views.',
    'value' => 45,
    'validators' => array(
      array('Int', true),
      array('GreaterThan', true, array(0)),
    )
  )
);
$DescriptionTruncationPinboard = array(
  'Text',
  'description_truncation_pinboard',
  array(
    'label' => 'Description truncation limit for Pinboard View.',
    'value' => 45,
    'validators' => array(
      array('Int', true),
      array('GreaterThan', true, array(0)),
    )
  )
);
$heightOfContainerList = array(
  'Text',
  'height_list',
  array(
    'label' => 'Enter the height of main photo block in List Views (in pixels).',
    'value' => '300',
  )
);
$widthOfContainerList = array(
  'Text',
  'width_list',
  array(
    'label' => 'Enter the width of main photo block in List Views (in pixels).',
    'value' => '500',
  )
);
$heightOfContainerGrid = array(
  'Text',
  'height_grid',
  array(
    'label' => 'Enter the height of one block in Grid Views (in pixels).',
    'value' => '270',
  )
);
$widthOfContainerGrid = array(
  'Text',
  'width_grid',
  array(
    'label' => 'Enter the width of one block in Grid Views (in pixels).',
    'value' => '389',
  )
);
$widthOfContainerPinboard = array(
  'Text',
  'width_pinboard',
  array(
    'label' => 'Enter the width of one block in Pinboard View (in pixels).',
    'value' => '300',
  )
);
$heightOfContainer = array(
    'Text',
    'height',
    array(
        'label' => 'Enter the height of one block (in pixels).',
        'value' => '160',
    )
);
$widthOfContainer = array(
    'Text',
    'width',
    array(
        'label' => 'Enter the width of one block (in pixels).',
        'value' => '250',
    )
);

return array(
  array(
    'title' => 'SNS - Advanced Blog - People Like Blog',
    'description' => 'Placed on  a Blog view page.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.people-like-item',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'limit_data',
          array(
            'label' => 'Show view more after how much data?.',
            'value' => 11,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
      )
    )
  ),
	array(
    'title' => 'SNS - Advanced Blog - Popular / Featured / Sponsored / Verified Blogs Carousel',
    'description' => "Disaplys carousel of blogs as configured by you based on chosen criteria for this widget. You can also choose to show Blogs of specific categories in this widget.",
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.featured-sponsored-verified-category-carousel',
    'adminForm' => array(
      'elements' => array(
	array(
	  'Select',
	  'category',
	  array(
	    'label' => 'Choose the category.',
	    'multiOptions' => $categories
	  ),
	  'value' => ''
	),

	array(
	  'Select',
	  'criteria',
	  array(
	    'label' => "Display Content",
	    'multiOptions' => array(
			  '0' => 'All including Featured and Sponsored',
	      '1' => 'Only Featured',
	      '2' => 'Only Sponsored',
	      '6' => 'Only Verified',
	    ),
	    'value' => 5,
	  )
	),
	array(
		'Select',
		'order',
		array(
			'label' => 'Duration criteria for the blogs to be shown in this widget',
			'multiOptions' => array(
				'' => 'All Blogs',
				'week' => 'This Week Blogs',
				'month' => 'This Month Blogs',
			),
			'value' => '',
		)
	),
	array(
	  'Select',
	  'info',
	  array(
	    'label' => 'Choose Popularity Criteria.',
	    'multiOptions' => array(
	      "recently_created" => "Recently Created",
	      "most_viewed" => "Most Viewed",
	      "most_liked" => "Most Liked",
	      "most_rated" => "Most Rated",
	      "most_commented" => "Most Commented",
	      "most_favourite" => "Most Favourite",
	    )
	  ),
	  'value' => 'recently_created',
	),
	array(
	  'Select',
	  'isfullwidth',
	  array(
	    'label' => 'Do you want to show carousel in full width?',
	    'multiOptions'=>array(
	      1=>'Yes',
	      0=>'No'
	    ),
	    'value' => 1,
	  )
	),
		array(
	  'Select',
	  'autoplay',
	  array(
	    'label' => "Do you want to enable autoplay of blogs?",
	    'multiOptions' => array(
	      1=>'Yes',
	      0=>'No'
	    ),
	  ),
	),
	array(
	  'Text',
	  'speed',
	    array(
	    'label' => 'Delay time for next blog when you have enabled autoplay.',
	    'value' => '2000',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'MultiCheckbox',
	  'show_criteria',
	  array(
	    'label' => "Choose from below the details that you want to show in this widget.",
	    'multiOptions' => array(
	      'like' => 'Likes Count',
	      'comment' => 'Comments Count',
	      'favourite' => 'Favourites Count',
	      'view' => 'Views Count',
	      'title' => 'Blog Title',
	      'by' => 'Blog Owner\'s Photo',
				'rating' =>'Rating Count',
				'ratingStar' =>'Rating Stars',
				 'featuredLabel' => 'Featured Label',
				'sponsoredLabel' => 'Sponsored Label',
				'verifiedLabel' => 'Verified Label',
				'favouriteButton' => 'Favourite Button',
				'likeButton' => 'Like Button',
	      'category' => 'Category',
	      'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
	      'creationDate' => 'Show Publish Date',
	      'readtime' => "Minute Read Count",
	    ),
	    'escape' => false,
	  )
	),
	$socialshare_enable_plusicon,
	$socialshare_icon_limit,
	array(
	  'Text',
	  'title_truncation',
	  array(
	    'label' => 'Blog title truncation limit.',
	    'value' => 45,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'height',
	  array(
	    'label' => 'Enter the height of one block (in pixels).',
	    'value' => '300',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'limit_data',
	  array(
	    'label' => 'Count (number of blogs to show).',
	    'value' => 5,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
      )
    ),
	),
  array(
    'title' => 'SNS - Advanced Blog - Tabbed widget for Popular Blogs',
    'description' => 'Displays a tabbed widget for popular blogs on your website based on various popularity criterias. Edit this widget to choose tabs to be shown in this widget. This widget can be placed anywhere on your website.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.tabbed-widget-blog',
    'requirements' => array(
      'subject' => 'blog',
    ),
    'adminForm' => 'Sesblog_Form_Admin_Tabbed',
  ),
	array(
		'title' => 'SNS - Advanced Blog - Content Profile Blogs',
		'description' => 'This widget enables you to allow users to create blogs on different content on your website like Groups. Place this widget on the content profile page, for example SE Group to enable group owners to create blogs in their Groups. You can choose the visibility of the blogs created in a content to only that content or show in this plugin as well from the "Blogs Created in Content Visibility" setting in Global setting of this plugin.',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.other-modules-profile-sesblogs',
		'requirements' => array(
			'subject' => 'user',
		),
		'defaultParams' => array(
			'title' => 'Profile Blogs',
			'titleCount' => true,
		),
		'adminForm' => 'Sesblog_Form_Admin_OtherModulesTabbed',
	),
	array(
		'title' => 'SNS - Advanced Blog - Profile Blogs',
		'description' => 'Displays a member\'s blog entries on their profiles. The recommended page for this widget is "Member Profile Page"',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.profile-sesblogs',
		'requirements' => array(
			'subject' => 'user',
		),
		'adminForm' => 'Sesblog_Form_Admin_Tabbed',
	),
  array(
    'title' => 'SNS - Advanced Blog - Browse Blogs',
    'description' => 'Display all blogs on your website. The recommended page for this widget is "SNS - Advanced Blog - Browse Blogs Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.browse-blogs',
    'adminForm' => array(
      'elements' => array(
				array(
          'MultiCheckbox',
          'enableTabs',
          array(
              'label' => "Choose the View Type.",
              'multiOptions' => array(
              'list1' => 'List View 1',
              'list2' => 'List View 2',
              'list3' => 'List View 3',
              'list4' => 'List View 4',
              'grid1' => 'Grid View 1',
              'grid2' => 'Grid View 2',
              'grid3' => 'Grid View 3',
              'grid4' => 'Grid View 4',
              'pinboard' => 'Pinboard View',
              'map' => 'Map View',
            ),
          )
        ),
				array(
          'Select',
          'openViewType',
          array(
            'label' => "Choose the view type which you want to display by default. (Settings will apply, if you have selected more than one option in above tab.)",
            'multiOptions' => array(
              'list1' => 'List View 1',
              'list2' => 'List View 2',
              'list3' => 'List View 3',
              'list4' => 'List View 4',
              'grid1' => 'Grid View 1',
              'grid2' => 'Grid View 2',
              'grid3' => 'Grid View 3',
              'grid4' => 'Grid View 4',
              'pinboard' => 'Pinboard View',
              'map' => 'Map View',
            ),
            'value' => 'list1',
          )
        ),
				array(
          'MultiCheckbox',
          'show_criteria',
          array(
            'label' => "Choose the options that you want to be displayed in this widget.",
            'multiOptions' => array(
              'featuredLabel' => 'Featured Label (Not Supported in Grid View 4)',
              'sponsoredLabel' => 'Sponsored Label (Not Supported in Grid View 4)',
              'verifiedLabel' => 'Verified Label (Not Supported in Grid View 4)',
              'favouriteButton' => 'Favourite Button',
              'likeButton' => 'Like Button',
              'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
              'like' => 'Likes Count',
              'favourite' => 'Favorites Count',
              'comment' => 'Comments Count',
              'readtime' => "Minute Read Count",
              'ratingStar' => 'Ratings Star (except Grid View 1)',
              'rating' => 'Ratings Count (except Grid View 1)',
              'view' => 'Views Count',
              'title' => 'Blog Title',
              'category' => 'Category',
              'by' => 'Blog Owner Name',
              'ownerPhoto' => "Owner Photo",
              'readmore' => 'Read More Button (except List View 3, Grid View 1,Grid View 2,Grid View 3)',
              'creationDate' => 'Creation Date (except List View 4)',
              'location'=> 'Location (supported in List View 2, List View 3, List View 4, Grid View 3)',
              'descriptionlist' => 'Description (In List View 1)',
              'descriptionsimplelist' => 'Description (In List View 2)',
              'descriptionadvlist' => 'Description (In List View 3)',
              'description4list' => 'Description (In List View 4)',
              //'descriptiongrid' => 'Description (In Grid View 1)',
              'descriptionadvgrid' => 'Description (In Grid View 2)',
              'descriptionsupergrid' => 'Description (In Grid View 3)',
              'description4grid' => 'Description (In Grid View 4)',
              'descriptionpinboard' => 'Description (In Pinboard View)',
              //'enableCommentPinboard'=>'Enable comment in Pinboard View',
            ),
            'escape' => false,
          )
        ),
        array(
          'Select',
          'socialshare_enable_listview1plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in List View 1?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),
        array(
          'Text',
          'socialshare_icon_listview1limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in List View 1. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            ),
          ),
        ),

        array(
          'Text',
          'title_truncation_list',
          array(
            'label' => 'Title truncation limit for List 1 Views.',
            'value' => 45,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
				array(
          'Text',
          'description_truncation_list',
          array(
            'label' => 'Description truncation limit for List 1 Views.',
            'value' => 45,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
				array(
          'Text',
          'height_list',
          array(
            'label' => 'Enter the height of main photo block in List 1 Views (in pixels).',
            'value' => '300',
          )
        ),
				array(
          'Text',
          'width_list',
          array(
            'label' => 'Enter the width of main photo block in List 1 Views (in pixels).',
            'value' => '500',
          )
        ),

        array(
          'Select',
          'socialshare_enable_listview2plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in List View 2?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),

        array(
          'Text',
          'socialshare_icon_listview2limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in List View 2. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
          'Select',
          'socialshare_enable_listview3plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in List View 3?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),
        array(
          'Text',
          'socialshare_icon_listview3limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in List View 3. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
          'Select',
          'socialshare_enable_listview4plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in List View 4?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),
        array(
          'Text',
          'socialshare_icon_listview4limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in List View 4. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
          'Select',
          'socialshare_enable_gridview1plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in Grid View 1?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),
        array(
          'Text',
          'socialshare_icon_gridview1limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in Grid View 1. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
          'Select',
          'socialshare_enable_gridview2plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in Grid View 2?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),
        array(
          'Text',
          'socialshare_icon_gridview2limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in Grid View 2. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
          'Select',
          'socialshare_enable_gridview3plusicon',
          array(
            'label' => "Enable plus (+) icon for social share buttons in Grid View 3?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
          )
        ),
        array(
          'Text',
          'socialshare_icon_gridview3limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in Grid View 3. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
            'Select',
            'socialshare_enable_gridview4plusicon',
            array(
                'label' => "Enable plus (+) icon for social share buttons in Grid View 4?",
                'multiOptions' => array(
                  '1' => 'Yes',
                  '0' => 'No',
                ),
            )
        ),
        array(
          'Text',
          'socialshare_icon_gridview4limit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in Grid View 4. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),

        array(
            'Select',
            'socialshare_enable_pinviewplusicon',
            array(
                'label' => "Enable plus (+) icon for social share buttons in Pinboard View?",
                'multiOptions' => array(
                  '1' => 'Yes',
                  '0' => 'No',
                ),
            )
        ),
        array(
          'Text',
          'socialshare_icon_pinviewlimit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in Pinboard View. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),

        array(
            'Select',
            'socialshare_enable_mapviewplusicon',
            array(
                'label' => "Enable plus (+) icon for social share buttons in Map View?",
                'multiOptions' => array(
                  '1' => 'Yes',
                  '0' => 'No',
                ),
            )
        ),
        array(
          'Text',
          'socialshare_icon_mapviewlimit',
          array(
            'label' => 'Enter the number of Social Share Buttons after which plus (+) icon will come in Map View. Other social sharing icons will display on clicking this plus icon.',
            'value' => 2,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
          ),
        ),
        array(
        'Select',
        'category',
          array(
            'label' => 'Choose the category.',
            'multiOptions' => $categories
          ),
          'value' => ''
        ),
				array(
					'Select',
					'sort',
					array(
						'label' => 'Choose Blog Display Criteria.',
						'multiOptions' => array(
						"recentlySPcreated" => "Recently Created",
						"mostSPviewed" => "Most Viewed",
						"mostSPliked" => "Most Liked",
						"mostSPated" => "Most Rated",
						"mostSPcommented" => "Most Commented",
						"mostSPfavourite" => "Most Favourite",
						'featured' => 'Only Featured',
						'sponsored' => 'Only Sponsored',
						'verified' => 'Only Verified'
						),
					),
						'value' => 'most_liked',
				),
				array(
					'Select',
					'show_item_count',
					array(
						'label' => 'Do you want to show blogs count in this widget?',
						'multiOptions' => array(
							'1' => 'Yes',
							'0' => 'No',
						),
						'value' => '0',
					),
				),
				
				$titleTruncationGrid,
				$titleTruncationPinboard,
				array('Text', "title_truncation_simplelist", array(
					'label' => 'Title truncation limit for List View 2.',
					'value' => 45,
					'validators' => array(
						array('Int', true),
						array('GreaterThan', true, array(0)),
						)
					)
				),
				array('Text', "title_truncation_advlist", array(
          'label' => 'Title truncation limit for List View 3.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
		array('Text', "title_truncation_advlist2", array(
          'label' => 'Title truncation limit for List View 4.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "title_truncation_advgrid", array(
          'label' => 'Title truncation limit for Grid View 2.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "title_truncation_advgrid2", array(
          'label' => 'Title truncation limit for Grid View 3.',
          'value' => 25,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
		array('Text', "title_truncation_supergrid", array(
          'label' => 'Title truncation limit for Grid View 4.',
          'value' => 25,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				
				$DescriptionTruncationGrid,
				$DescriptionTruncationPinboard,
				array('Text', "description_truncation_simplelist", array(
          'label' => 'Description truncation limit for List View 2.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "description_truncation_advlist", array(
          'label' => 'Description truncation limit for List View 3.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
		array('Text', "description_truncation_advlist2", array(
          'label' => 'Description truncation limit for List View 4.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "description_truncation_advgrid", array(
          'label' => 'Description truncation limit for Grid View 2.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "description_truncation_supergrid", array(
          'label' => 'Description truncation limit for Grid View 4.',
          'value' => 45,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
        
				$heightOfContainerGrid,
				$widthOfContainerGrid,

				array('Text', "height_simplelist", array(
          'label' => 'Enter the height of main photo block in List View 2 (in pixels).',
          'value' => '230',
        )),
				array('Text', "width_simplelist", array(
          'label' => 'Enter the width of main photo block in List View 2 (in pixels).',
          'value' => '260',
        )),
				array('Text', "height_advgrid", array(
          'label' => 'Enter the height of main photo block in Grid View 2 (in pixels).',
          'value' => '230',
        )),
				array('Text', "width_advgrid", array(
          'label' => 'Enter the width of main photo block in Grid View 2 (in pixels).',
          'value' => '260',
        )),
		array('Text', "height_avdgrid2", array(
          'label' => 'Enter the height of main block in Grid View 3 (in pixels).',
          'value' => '400',
        )),
				array('Text', "width_advgrid2", array(
          'label' => 'Enter the width of main photo block in Grid View 3 (in pixels).',
          'value' => '454',
        )),
				array('Text', "height_supergrid", array(
          'label' => 'Enter the height of main block in Grid View 4 (in pixels).',
          'value' => '255',
        )),
				array('Text', "width_supergrid", array(
          'label' => 'Enter the width of main photo block in Grid View 4 (in pixels).',
          'value' => '309',
        )),
				$widthOfContainerPinboard,
        array(
					'Text',
					'limit_data_list1',
					array(
						'label' => 'Count for List 1 Views (number of blogs to show).',
						'value' => 20,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
				array('Text', "limit_data_list2", array(
          'label' => 'Count for List View 2 (number of blogs to show).',
          'value' => 10,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "limit_data_list3", array(
          'label' => 'Count for List View 3 (number of blogs to show).',
          'value' => 10,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "limit_data_list4", array(
          'label' => 'Count for List View 4 (number of blogs to show).',
          'value' => 10,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				
				array(
					'Text',
					'limit_data_grid1',
					array(
						'label' => 'Count for Grid Views 1 (number of blogs to show).',
						'value' => 20,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
				array('Text', "limit_data_grid2", array(
          'label' => 'Count for Grid View 2 (number of blogs to show).',
          'value' => 10,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
				array('Text', "limit_data_grid3", array(
          'label' => 'Count for Grid View 3 (number of blogs to show).',
          'value' => 10,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
        array('Text', "limit_data_grid4", array(
          'label' => 'Count for Grid View 4 (number of blogs to show).',
          'value' => 10,
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          )
        )),
        array(
					'Text',
					'limit_data_pinboard',
					array(
						'label' => 'Count for Pinboard View (number of blogs to show).',
						'value' => 10,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
	      $pagging,
      )
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Tabbed widget for Manage Blogs',
    'description' => 'This widget displays blogs created, favourite, liked, rated, etc by the member viewing the manage page. Edit this widget to configure various settings.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.manage-blogs',
    'requirements' => array(
      'subject' => 'blog',
    ),
    'adminForm' => 'Sesblog_Form_Admin_Tabbed',
  ),
  /*array(
    'title' => 'SNS - Advanced Blog - Blog Profile Gutter Search',
    'description' => 'Displays a search form in the blog profile gutter.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.gutter-search',
  ),*/
  array(
    'title' => 'SNS - Advanced Blog - Profile Options for Blogs',
    'description' => 'Displays a menu of actions (edit, report, add to favourite, share, subscribe, etc) that can be performed on a blog on its profile.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.gutter-menu',
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Owner Photo',
    'description' => 'Displays the owner\'s photo on the blog view page.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.gutter-photo',
    'adminForm' => array(
			'elements' => array (
        array(
          'Select',
          'photoviewtype',
          array(
            'label' => "Choose the shape of Photo.",
            'multiOptions' => array(
              'square' => 'Rounded Square',
              'circle' => 'Circle'
            ),
            'value' => 'circle',
          )
        ),
			),
		),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Browse Search',
    'description' => 'Displays a search form for the blogs as configured by you. Place this widget on Blogs Browse Page.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.browse-search',
    'autoEdit' => true,
    'adminForm' => array(
    'elements' => array(
	array(
	  'Select',
	  'view_type',
	  array(
	    'label' => "Choose the View Type.",
	    'multiOptions' => array(
	      'horizontal' => 'Horizontal',
	      'vertical' => 'Vertical'
	    ),
	    'value' => 'vertical',
	  )
	),
	array(
	  'MultiCheckbox',
	  'search_type',
	  array(
	    'label' => "Choose options to be shown in \'Browse By\' search fields.",
	    'multiOptions' => array(
	      'recentlySPcreated' => 'Recently Created',
	      'mostSPviewed' => 'Most Viewed',
	      'mostSPliked' => 'Most Liked',
	      'mostSPcommented' => 'Most Commented',
	      'mostSPfavourite' => 'Most Favourite',
	      'mostSPrated' => 'Most Rated',
	      'featured' => 'Only Featured',
	      'sponsored' => 'Only Sponsored',
	      'verified' => 'Only Verified'
	    ),
	  )
	),
	array(
	  'Select',
	  'default_search_type',
	  array(
	    'label' => "Default \'Browse By\' search field.",
	    'multiOptions' => array(
	      'recentlySPcreated' => 'Recently Created',
	      'mostSPviewed' => 'Most Viewed',
	      'mostSPliked' => 'Most Liked',
	      'mostSPcommented' => 'Most Commented',
	      'mostSPrated' => 'Most Rated',
	      'featured' => 'Only Featured',
	      'sponsored' => 'Only Sponsored'
	    ),
	  )
	),
	array(
	  'Radio',
	  'friend_show',
	  array(
	    'label' => "Show \'View\' search field?",
	    'multiOptions' => array(
	      'yes' => 'Yes',
	      'no' => 'No'
	    ),
	    'value' => 'yes',
	  )
	),
	array(
	  'Radio',
	  'search_title',
	  array(
	    'label' => "Show \'Search Blogs Keyword\' search field?",
	    'multiOptions' => array(
	      'yes' => 'Yes',
	      'no' => 'No'
	    ),
	    'value' => 'yes',
	  )
	),
	array(
	  'Radio',
	  'browse_by',
	  array(
	    'label' => "Show \'Browse By\' search field?",
	    'multiOptions' => array(
	      'yes' => 'Yes',
	      'no' => 'No'
	    ),
	    'value' => 'yes',
	  )
	),
	array(
	  'Radio',
	  'categories',
	  array(
	    'label' => "Show \'Categories\' search field?",
	    'multiOptions' => array(
	      'yes' => 'Yes',
	      'no' => 'No'
	    ),
	    'value' => 'yes',
	  )
	),
	array(
	  'Radio',
	  'location',
	  array(
	    'label' => "Show \'Location\' search field?",
	    'multiOptions' => array(
	      'yes' => 'Yes',
	      'no' => 'No'
	    ),
	    'value' => 'yes',
	  )
	),
	array(
	  'Radio',
	  'kilometer_miles',
	  array(
	    'label' => "Show \'Kilometer or Miles\' search field?",
	    'multiOptions' => array(
	      'yes' => 'Yes',
	      'no' => 'No'
	    ),
	    'value' => 'yes',
	  )
	),
	array(
		'Radio',
		'has_photo',
		array(
			'label' => "Show \'Blog With Photos\' search field?",
			'multiOptions' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'value' => 'yes',
		)
	),
      )
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Categories Main Navigation Menu',
    'description' => 'This widget displays all the blog categories in Main Navigation Menu of your website. Place this widget at Site Header Page.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.category-browse-menu',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array (
        array(
					'Select',
					'categoryShow',
					array(
            'label' => "Categories Count in Main Navigation",
            'description' => 'How many categories do you want to show in the Main Navigation Menu of your website? Choosing "0" will show all the categories.',
            'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
              5 => 5,
              6 => 6,
              7 => 7,
              8 => 8,
              9 => 9,
              10 => 10,
              11 => 11,
              12 => 12,
            ),
					)
				),
      ),
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blogs Navigation Menu',
    'description' => 'Displays a navigation menu bar in the Blog\'s pages for Blogs Home, Browse Blogs, Browse Categories, etc pages.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array (
        array(
					'Radio',
					'createButton',
					array(
            'label' => "Enable Create Blog Button? Note: You can  disable 'Create New Blog' menu from menu editor if you don't want it twice in navigation menu on your website.",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
            'value' => '1',
					)
				),
      ),
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Create New Blog Link',
    'description' => 'Displays a link to create new blog.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.browse-menu-quick',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
      'title' => 'SNS - Advanced Blog - Categories Cloud / Hierarchy View',
      'description' => 'Displays all categories of blogs in cloud or hierarchy view. Edit this widget to choose various other settings.',
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'name' => 'sesblog.tag-cloud-category',
      'autoEdit' => true,
      'adminForm' => 'Sesblog_Form_Admin_Tagcloudcategory',
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Similar Blogs',
    'description' => 'Displays blogs similar to the current blog based on the blog category. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
		'autoEdit' => true,
    'name' => 'sesblog.similar-blogs',
    'adminForm' => array(
      'elements' => array (
        array(
          'MultiCheckbox',
          'show_criteria',
          array(
            'label' => "Choose from below the details that you want to show for blog in this widget.",
            'multiOptions' => array(
              'like' => 'Likes Count',
              'comment' => 'Comments Count',
              'favourite' => 'Favourites Count',
              'view' => 'Views Count',
              'title' => 'Blog Title',
              'by' => 'Blog Owner\'s Name',
              'rating' =>'Rating Stars',
              'featuredLabel' => 'Featured Label',
              'sponsoredLabel' => 'Sponsored Label',
              'verifiedLabel' => 'Verified Label',
              'favouriteButton' => 'Favourite Button',
              'likeButton' => 'Like Button',
              'category' => 'Category',
              'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
            ),
            'escape' => false,
          )
        ),
        $socialshare_enable_plusicon,
        $socialshare_icon_limit,
        array(
          'Select',
          'showLimitData',
          array(
            'label' => 'Do you want to allow users to view more similar blogs in this widget? (If you choose Yes, then users will see Next & Previous buttons to view more blogs.)',
            'multiOptions' => array(
              "1" => "Yes, allow.",
              "0" => "No, do not allow.",
            )
          ),
          'value' => '1',
        ),

        array(
          'Text',
          'height',
          array(
            'label' => 'Enter the height of one block (in pixels).',
            'value' => '180',
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
        array(
          'Text',
          'width',
          array(
            'label' => 'Enter the width of one block (in pixels).',
            'value' => '180',
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
        array(
          'Text',
          'list_title_truncation',
          array(
            'label' => 'Title truncation limit.',
            'value' => 45,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
        array(
          'Text',
          'limit_data',
          array(
            'label' => 'Count (number of blogs to show).',
            'value' => 3,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
      ),
    ),
  ),
  array(
      'title' => 'SNS - Advanced Blog - Blog Profile - Tags',
      'description' => 'Displays all tags of the current blog on Blog Profile Page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'name' => 'sesblog.profile-tags',
      'autoEdit' => true,
      'adminForm' => array(
        'elements' => array(
          array(
            'Text',
            'itemCountPerPage',
            array(
              'label' => 'Count (number of tags to show).',
              'value' => 30,
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
    'title' => 'SNS - Advanced Blog - Tags Horizontal View',
    'description' => 'Displays all tags of blogs in horizantal view. Edit this widget to choose various other settings.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.tag-horizantal-blogs',
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
				array(
					'Radio',
					'viewtype',
					array(
            'label' => "Do you want to show widget in full width ?",
            'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
            ),
            'value' => '1',
					)
				),
        array(
          'Text',
          'widgetbgcolor',
          array(
            'class' => 'SEScolor',
            'label'=>'Choose widget background color.',
            'value' => '424242',
          )
        ),
        array(
          'Text',
          'buttonbgcolor',
          array(
            'class' => 'SEScolor',
            'label'=>'Choose background color of the button.',
            'value' => '000000',
          )
        ),
        array(
          'Text',
          'textcolor',
          array(
            'class' => 'SEScolor',
            'label'=>'Choose text color on the button.',
            'value' => 'ffffff',
          )
        ),
        array(
          'Text',
          'itemCountPerPage',
          array(
            'label' => 'Count (number of tags to show).',
            'value' => 30,
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
      'title' => 'SNS - Advanced Blog - Tags Cloud / Tab View',
      'description' => 'Displays all tags of blogs in cloud or tab view. Edit this widget to choose various other settings.',
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'name' => 'sesblog.tag-cloud-blogs',
      'autoEdit' => true,
      'adminForm' => 'Sesblog_Form_Admin_Tagcloudblog',
  ),
  array(
      'title' => 'SNS - Advanced Blog - Browse All Tags',
      'description' => 'Displays all blogs tags on your website. The recommended page for this widget is "SNS - Advanced Blog - Browse Tags Page".',
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'name' => 'sesblog.tag-albums',
  ),
  array(
		'title' => 'SNS - Advanced Blog - Top Blog Posters',
		'description' => 'Displays all top posters on your website.',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'name' => 'sesblog.top-bloggers',
		'adminForm' => array(
			'elements' => array(
				array(
					'MultiCheckbox',
					'show_criteria',
					array(
						'label' => "Choose the details that you want to be shown in this widget.",
						'multiOptions' => array(
							'count' => 'Blogs Count',
							'ownername' => 'Blog Owner\'s Name',
						),
					)
				),
				array(
					'Text',
					'height',
					array(
						'label' => 'Enter the height of one block [for Horizontal View (in pixels)].',
						'value' => '180',
					)
				),
				array(
					'Text',
					'width',
					array(
						'label' => 'Enter the width of one block [for Horizontal View (in pixels)].',
						'value' => '180',
					)
				),
				array(
					'Select',
					'showLimitData',
					array(
						'label' => 'Do you want to allow users to view more blog posters in this widget? (If you choose Yes, then users will see Next & Previous buttons to view more blog posters.)',
						'multiOptions' => array(
							"1" => "Yes, allow.",
							"0" => "No, do not allow.",
						)
					),
					'value' => '1',
				),
				array(
					'Text',
					'limit_data',
					array(
						'label' => 'Count (number of blog posters to show).',
						'value' => 5,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
			),
		),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Advanced Share Widget',
    'description' => 'This widget allow users to share the current blog on your website and on other social networking websites. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.advance-share',
    'autoEdit' => true,
    'adminForm' => 'Sesblog_Form_Admin_Share',
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog of the Day',
    'description' => "This widget displays blogs of the day as chosen by you from the Edit Settings of this widget.",
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.of-the-day',
    'adminForm' => array(
	'elements' => array(
			array(
			'Select',
			'viewType',
			array(
				'label' => 'Choose the view type.',
				'multiOptions' => array(
					"grid1" => "Grid View 1",
					"grid2" => "Grid View 2",
				)
			),
			'value' => 'grid1'
		),
	 array(
		'MultiCheckbox',
		'show_criteria',
		array(
		    'label' => "Choose from below the details that you want to show for blogs in this widget.",
				'multiOptions' => array(
					'title' => 'Blog Title',
					'like' => 'Likes Count',
					'view' => 'Views Count',
					'comment' => 'Comment Count',
					'favourite' => 'Favourites Count',
					'rating' => 'Rating Count',
					'ratingStar' => 'Rating Star',
					'by' => 'Owner\'s Name',
					'description'=>'Description',
					'favouriteButton' => 'Favourite Button',
					'likeButton' => 'Like Button',
					'featuredLabel' => 'Featured Label',
					'verifiedLabel' => 'Verified Label',
					'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
				),
				'escape' => false,
		)
	 ),
    $socialshare_enable_plusicon,
    $socialshare_icon_limit,
	    array(
		'Text',
		'title_truncation',
		array(
		    'label' => 'Blog title truncation limit.',
		    'value' => 45,
		    'validators' => array(
			array('Int', true),
			array('GreaterThan', true, array(0)),
		    )
		)
	    ),
	      array(
	  'Text',
	  'description_truncation',
	  array(
	    'label' => 'Blog description truncation limit.',
	    'value' => 60,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	    array(
		'Text',
		'height',
		array(
		    'label' => 'Enter the height of block (in pixels).',
		    'value' => '180',
		    'validators' => array(
			array('Int', true),
			array('GreaterThan', true, array(0)),
		    )
		)
	    ),
	    array(
		'Text',
		'width',
		array(
		    'label' => 'Enter the width of block (in pixels).',
		    'value' => '180',
		    'validators' => array(
			array('Int', true),
			array('GreaterThan', true, array(0)),
		    )
		)
	    ),
	)
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Popular / Featured / Sponsored / Verified Blogs',
    'description' => "Displays blogs as chosen by you based on chosen criteria for this widget. The placement of this widget depends on the criteria chosen for this widget.",
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.featured-sponsored',
    'adminForm' => array(
      'elements' => array(
	array(
	  'Select',
	  'viewType',
	  array(
	    'label' => 'Choose the view type.',
	    'multiOptions' => array(
	      "list" => "List View",
	      "grid" => "Grid View",
	    )
	  ),
	  'value' => 'list'
	),
	$imageType,
	array(
	  'Select',
	  'criteria',
	  array(
	    'label' => "Display Content",
	    'multiOptions' => array(
	      '5' => 'All including Featured and Sponsored',
	      '1' => 'Only Featured',
	      '2' => 'Only Sponsored',
	      '3' => 'Both Featured and Sponsored',
	      '6' => 'Only Verified',
	      '4' => 'All except Featured and Sponsored',
	    ),
	    'value' => 5,
	  )
	),
	array(
		'Select',
		'order',
		array(
			'label' => 'Duration criteria for the blogs to be shown in this widget',
			'multiOptions' => array(
				'' => 'All Blogs',
				'week' => 'This Week Blogs',
				'month' => 'This Month Blogs',
			),
			'value' => '',
		)
	),
	array(
	  'Select',
	  'info',
	  array(
	    'label' => 'Choose Popularity Criteria.',
	    'multiOptions' => array(
	      "recently_created" => "Recently Created",
	      "most_viewed" => "Most Viewed",
	      "most_liked" => "Most Liked",
	      "most_rated" => "Most Rated",
	      "most_commented" => "Most Commented",
	      "most_favourite" => "Most Favourite",
	    )
	  ),
	  'value' => 'recently_created',
	),
	array(
	  'MultiCheckbox',
	  'show_criteria',
	  array(
	    'label' => "Choose from below the details that you want to show for blog in this widget.",
	    'multiOptions' => array(
	      'like' => 'Likes Count for Grid view only',
	      'comment' => 'Comments Count for Grid view only',
	      'favourite' => 'Favourites Count  for Grid view only',
	      'view' => 'Views Count for Grid view only',
	      'title' => 'Blog Title',
	      'by' => 'Blog Owner\'s Photo for Grid view only',
	      'creationDate' => 'Show Publish Date',
	      'category' => 'Category for Grid view only',
	      'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a> for Grid view only',
	      'likeButton' => 'Like Button for Grid view only',
	      'favouriteButton' => 'Favourite Button for Grid view only',
	    ),
	    'escape' => false,
	  )
	),
	$socialshare_enable_plusicon,
	$socialshare_icon_limit,
	array(
		'Radio',
		'show_star',
		array(
				'label' => "Do you want to show rating stars in this widget? (Note: Please choose star setting yes, when you are selction \"Most Rated\" from above setting.)",
				'multiOptions' => array(
						'1' => 'Yes',
						'0' => 'No',
				),
				'value' => 0,
		)
  ),
  array(
	'Select',
	'showLimitData',
	array(
		'label' => 'Do you want to allow users to view more blog posters in this widget? (If you choose Yes, then users will see Next & Previous buttons to view more blog posters.)',
		'multiOptions' => array(
			"1" => "Yes, allow.",
			"0" => "No, do not allow.",
		)
	),
	'value' => '1',
),
	array(
	  'Text',
	  'title_truncation',
	  array(
	    'label' => 'Blog title truncation limit.',
	    'value' => 45,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
  array(
	  'Text',
	  'description_truncation',
	  array(
	    'label' => 'Blog description truncation limit.',
	    'value' => 60,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'height',
	  array(
	    'label' => 'Enter the height of one block (in pixels).',
	    'value' => '180',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'width',
	  array(
	    'label' => 'Enter the width of one block (in pixels).',
	    'value' => '180',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'limit_data',
	  array(
	    'label' => 'Count (number of blogs to show).',
	    'value' => 5,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
      )
    ),
  ),
      array(
        'title' => 'SNS - Advanced Blog - Blog Profile - Albums',
        'description' => 'Displays albums on blog profile page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
				'autoEdit' => true,
        'name' => 'sesblog.profile-photos',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => false,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'load_content',
                    array(
                        'label' => "Do you want the albums to be auto-loaded when users scroll down the page?",
                        'multiOptions' => array(
                            'auto_load' => 'Yes, Auto Load.',
                            'button' => 'No, show \'View more\' link.',
                            'pagging' => 'No, show \'Pagination\'.'
                        ),
                        'value' => 'auto_load',
                    )
                ),
                array(
                    'Radio',
                    'sort',
                    array(
                        'label' => 'Choose Album Display Criteria.',
                        'multiOptions' => array(
                            "recentlySPcreated" => "Recently Created",
                            "mostSPviewed" => "Most Viewed",
                            "mostSPliked" => "Most Liked",
                            "mostSPcommented" => "Most Commented",
                        ),
                        'value' => 'most_liked',
                    )
                ),
                array(
                    'Select',
                    'insideOutside',
                    array(
                        'label' => "Choose where do you want to show the statistics of albums.",
                        'multiOptions' => array(
                            'inside' => 'Inside Album Blocks',
                            'outside' => 'Outside Album Blocks',
                        ),
                        'value' => 'inside',
                    )
                ),
                array(
                    'Select',
                    'fixHover',
                    array(
                        'label' => "Show album statistics Always or when users Mouse-over on album blocks (this setting will work only if you choose to show information inside the Album block.)",
                        'multiOptions' => array(
                            'fix' => 'Always',
                            'hover' => 'On Mouse-over',
                        ),
                        'value' => 'fix',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'view' => 'Views Count',
                            'favouriteCount' => 'Favourites Count',
                            'title' => 'Album Title',
                            'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
                            'photoCount' => 'Photos Count',
                            'likeButton' => 'Like Button',
                            'favouriteButton' => 'Favourite Button',
                        ),
                        'escape' => false,
                    //'value' => array('like','comment','view','rating','title','by','socialSharing'),
                    )
                ),
                $socialshare_enable_plusicon,
                $socialshare_icon_limit,
                array(
                    'Text',
                    'title_truncation',
                    array(
                        'label' => 'Album title truncation limit.',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'Count (number of albums to show.)',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one album block (in pixels).',
                        'value' => 200,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one album block (in pixels).',
                        'value' => 236,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'requirements' => array(
            'subject' => 'sesblog_blog',
        ),
    ),

     array(
      'title' => 'SNS - Advanced Blog - Album View Page - Options',
      'description' => "This widget enables you to choose various options to be shown on album view page like Likes count, Like button, etc.",
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'autoEdit' => true,
      'name' => 'sesblog.album-view-page',
      'adminForm' => 'Sesblog_Form_Admin_Layout_Albumviewpage',
    ),

    		array(
        'title' => 'SNS - Advanced Blog - Photo View Page - Options',
        'description' => 'This widget enables you to choose various options to be shown on photo view page like Slideshow of other photos associated with same album as the current photo, etc.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'name' => 'sesblog.photo-view-page',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'criteria',
                    array(
                        'label' => 'Slideshow of other photos associated with same album?',
                        'multiOptions' =>
                        array(
                        	'1' => 'Yes',
				'0' =>'No'
                        ),
				'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'maxHeight',
                    array(
                        'label' => 'Enter the height of photo.',
                        'value' => 550,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            ),
        ),
    ),

//   array(
//     'title' => 'SNS - Advanced Blog - Blog Profile - Music Albums',
//     'description' => 'Displays music albums on blog profile page. Edit this widget to choose content type to be shown. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
//     'category' => 'SNS - Advanced Blog',
//     'type' => 'widget',
//     'name' => 'sesblog.profile-musicalbums',
//     'autoEdit' => true,
//     'adminForm' => array(
//         'elements' => array(
//             array(
//                 'MultiCheckbox',
//                 'informationAlbum',
//                 array(
//                     'label' => 'Choose from below the details that you want to show for "Music Albums" shown in this widget.',
//                     'multiOptions' => array(
//                         "featured" => "Featured Label",
//                         "sponsored" => "Sponsored Label",
//                         "hot" => "Hot Label",
//                         "postedBy" => "Music Album Owner\'s Name",
//                         "commentCount" => "Comments Count",
//                         "viewCount" => "Views Count",
//                         "likeCount" => "Likes Count",
//                         "ratingStars" => "Rating Stars",
//                         "songCount" => "Song Count",
//                         "favourite" => "Favorite Icon on Mouse-Over",
//                         "share" => "Share Icon on Mouse-Over",
//                     ),
//                 ),
//             ),
//             array(
//                 'Select',
//                 'pagging',
//                 array(
//                     'label' => "Do you want music albums to be auto-loaded when users scroll down the page?",
//                     'multiOptions' => array(
//                         'button' => 'No, show \'View more\'',
//                         'auto_load' => 'Yes',
//                     ),
//                     'value' => 'auto_load',
//                 )
//             ),
//             array(
//                 'Text',
//                 'Height',
//                 array(
//                     'label' => 'Enter the height of one block [for Grid View (in pixels)].',
//                     'value' => '180',
//                 )
//             ),
//             array(
//                 'Text',
//                 'Width',
//                 array(
//                     'label' => 'Enter the width of one block [for Grid View (in pixels)].',
//                     'value' => '180',
//                 )
//             ),
//             array(
//                 'Text',
//                 'limit_data',
//                 array(
//                     'label' => 'count (number of music albums to show)',
//                     'value' => 3,
//                 )
//             ),
//         )
//     ),
//   ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Videos',
    'description' => 'Displays videos on blog profile page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page". This widget is dependent on the Advanced Videos & Channels Plugin.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.profile-videos',
    'autoEdit' => true,
    'adminForm' => 'Sesblog_Form_Admin_Profilevideos',
  ),
array(
	'title' => 'SNS - Advanced Blog - Review Profile',
	'description' => 'Displays review and review statistics on "SNS - Advanced Blog - Review Profile Page".',
	'category' => 'SNS - Advanced Blog',
	'type' => 'widget',
	'name' => 'sesblog.review-profile',
	'autoedit' => 'true',
	'adminForm' => array(
	'elements' => array(
			array(
				'MultiCheckbox',
				'stats',
			array(
				'label' => 'Choose the options that you want to be displayed in this widget.',
				'multiOptions' => array(
							"likeCount" => "Likes Count",
							"commentCount" => "Comments Count",
							"viewCount" => "Views Count",
							"title" => "Review Title",
							"pros" => "Pros",
							"cons" => "Cons",
							"description" => "Description",
							"recommended" => "Recommended",
							'postedin' => "Posted In",
							"creationDate" => "Creation Date",
							'parameter'=>'Review Parameters',
							'rating' => 'Rating Stars',
							'customfields' => 'Form Questions',
							'likeButton' => 'Like Button',
							'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
							'share' => 'Share Review',
									),
									'escape' => false,
							),
					),
          $socialshare_enable_plusicon,
          $socialshare_icon_limit,
					),
			),
	),
  array(
      'title' => 'SNS - Advanced Blog - Review Profile - Breadcrumb',
      'description' => 'Displays breadcrumb for Reviews. This widget should be placed on the "SNS - Advanced Blog - Review Profile Page".',
      'category' => 'SNS - Advanced Blog',
      'autoEdit' => true,
      'type' => 'widget',
      'name' => 'sesblog.review-breadcrumb',
      'autoEdit' => true,
  ),
	array(
		'title' => 'SNS - Advanced Blog - Album Profile - Breadcrumb',
		'description' => 'Displays breadcrumb for Albums. This widget should be placed on the SNS - Advanced Blog - Album Profile Page.',
		'category' => 'SNS - Advanced Blog',
		'autoEdit' => true,
		'type' => 'widget',
		'name' => 'sesblog.album-breadcrumb',
		'autoEdit' => true,
  ),
  array(
      'title' => 'SNS - Advanced Blog - Review Profile - Options',
      'description' => 'Displays a menu of actions (edit, report, share, etc) that can be performed on reviews on its profile. The recommended page for this widget is "SNS - Advanced Blog - Review Profile Page".',
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'name' => 'sesblog.review-profile-options',
      'autoEdit' => true,
      'adminForm' => array(
          'elements' => array(
              array(
                  'Select',
                  'viewType',
                  array(
                      'label' => "Choose the View Type.",
                      'multiOptions' => array(
                          'horizontal' => 'Horizontal',
                          'vertical' => 'Vertical',
                      ),
                      'value' => 'vertical',
                  ),
              ),
          ),
      ),
  ),
  array(
      'title' => "SNS - Advanced Blog - Review Owner's Photo",
      'description' => 'This widget displays photo of the member who has written the current review. The recommended page for this widget is "SNS - Advanced Blog - Review View Page".',
      'category' => 'SNS - Advanced Blog',
      'type' => 'widget',
      'autoEdit' => true,
      'name' => 'sesblog.review-owner-photo',
      'defaultParams' => array(
          'title' => '',
      ),
      'adminForm' => array(
          'elements' => array(
              array(
                  'Select',
                  'showTitle',
                  array(
                      'label' => 'Do you want to show Member’s Name in this widget?',
                      'multiOptions' => array(
                          '1' => 'Yes',
                          '0' => 'No'
                      ),
                      'value' => 1,
                  )
              ),
          )
      ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Category Carousel',
    'description' => 'Displays categories in attractive carousel in this widget. The placement of this widget depends on the criteria chosen for this widget.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.category-carousel',
    'adminForm' => array(
      'elements' => array(
	array(
	  'Text',
	  'title_truncation_grid',
	  array(
	    'label' => 'Title truncation limit.',
	    'value' => 45,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'height',
	  array(
	    'label' => 'Enter the height of carousel.',
	    'value' => '100',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'width',
	  array(
	    'label' => 'Enter the width of thumbnail(in pixels).',
	    'value' => '50',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Select',
	  'autoplay',
	  array(
	    'label' => "Do you want to enable auto play of categories?",
	    'multiOptions' => array(
	      1=>'Yes',
	      0=>'No'
	    ),
	  ),
	),
	array(
	  'Text',
	  'speed',
	    array(
	    'label' => 'Delay time for next category when you have enabled autoplay',
	    'value' => '2000',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Select',
	  'criteria',
	  array(
	    'label' => "Choose Popularity Criteria.",
	    'multiOptions' => array(
	      'alphabetical' => 'Alphabetical order',
	      'most_blog' => 'Categories with maximum blogs first',
	      'admin_order' => 'Admin selected order for categories',
	    ),
	  ),
	),
	array(
	  'Select',
	  'isfullwidth',
	  array(
	    'label' => 'Do you want to show category carousel in full width?',
	    'multiOptions'=>array(
	      1=>'Yes',
	      0=>'No'
	    ),
	    'value' => 1,
	  )
	),
	array(
	  'Text',
	  'limit_data',
	  array(
	    'label' => 'Count (number of categories to show in this widget).',
	    'value' => 10,
	  )
	),
      )
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Categories Icon View',
    'description' => 'Displays all categories of blogs in icon view with their icon. Edit this widget to configure various settings.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.blog-category-icons',
    'adminForm' => array(
      'elements' => array(
	array(
	  'Text',
	  'titleC',
	  array(
	    'label' => 'Enter the title for this widget.',
	    'value' => 'Browse by Popular Categories',
	  )
	),
	array(
	  'Text',
	  'height',
	  array(
	    'label' => 'Enter the height of one block (in pixels).',
	    'value' => '60px',
	  )
	),
	array(
	'Text',
	'width',
	  array(
	    'label' => 'Enter the width of one block (in pixels).',
	    'value' => '302px',
	  )
	),
	array(
	  'Select',
	  'alignContent',
	  array(
	    'label' => "Where you want to show content of this widget?",
	    'multiOptions' => array(
	      'center' => 'In Center',
	      'left' => 'In Left',
	      'right' => 'In Right',
	    ),
	    'value' => 'center',
	  ),
	),
	array(
	  'Select',
	  'criteria',
	  array(
	    'label' => "Choose Popularity Criteria.",
	    'multiOptions' => array(
	      'alphabetical' => 'Alphabetical order',
	      'most_blog' => 'Categories with maximum blogs first',
	      'admin_order' => 'Admin selected order for categories',
	    ),
	  ),
	),
	array(
	  'MultiCheckbox',
	  'show_criteria',
	  array(
	    'label' => "Choose from below the details that you want to show on each block.",
	    'multiOptions' => array(
	      'title' => 'Category title',
	      'countBlogs' => 'Blog count in each category',
	    ),
	  )
	),
	array(
	  'Text',
	  'limit',
	  array(
	    'label' => 'Count (number of categories to show.)',
	    'value' => 10,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
      ),
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Alphabetic Filtering of Blogs',
    'description' => "This widget displays all the alphabets for alphabetic filtering of blogs which will enable users to filter blogs on the basis of selected alphabet.",
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.alphabet-search',
    'defaultParams' => array(
      'title' => "",
    ),
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Breadcrumb',
    'description' => 'Displays breadcrumb for Blog. This widget should be placed on the Blog - View page of the selected content type.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.breadcrumb',
    'autoEdit' => true,
  ),
	 array(
    'title' => 'SNS - Advanced Blog - Blog Custom Field Info',
    'description' => 'Displays blog custom fields for Blog. The widget should be placed on the profile page of the blog.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.blog-info',
		'defaultParams' => array(
      'title' => "Custom Fields",
    ),
    'autoEdit' => false,
  ),
    array(
    'title' => 'SNS - Advanced Blog - New Claim Request Form',
    'description' => 'Displays form to make new request to claim a blog. This widget should be placed on the "SNS - Advanced Blog - New Claims Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.claim-blog',
    'autoEdit' => true,
  ),
    array(
    'title' => 'SNS - Advanced Blog - Browse Claim Requests',
    'description' => 'Displays all claim requests made by the current member viewing the page. The recommended page for this widget is "SNS - Advanced Blog - Browse Claim Requests Page',
    'category' => 'SNS - Advanced Blog',
    'autoEdit' => true,
    'type' => 'widget',
    'name' => 'sesblog.claim-requests',
    'autoEdit' => true,
  ),
	array(
		'title' => 'SNS - Advanced Blog - Blog Profile - Reviews	',
		'description' => 'Displays reviews on blog profile page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'name' => 'sesblog.blog-reviews',
		'autoEdit' => true,
		'adminForm' => array(
			'elements' => array(
				array(
					'MultiCheckbox',
					'stats',
					array(
						'label' => 'Choose the options that you want to be displayed in this widget.',
						'multiOptions' => array(
							"likeCount" => "Likes Count",
							"commentCount" => "Comments Count",
							"viewCount" => "Views Count",
							"title" => "Review Title",
							"share" => "Share Button",
							"report" => "Report Button",
							"pros" => "Pros",
							"cons" => "Cons",
							"description" => "Description",
							"recommended" => "Recommended",
							'postedBy' => "Posted By",
							'parameter' => 'Review Parameters',
							"creationDate" => "Creation Date",
							'rating' => 'Rating Stars',
							'likeButton' => 'Like Button',
              'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
						),
						'escape' => false,
					),
				),
        $socialshare_enable_plusicon,
        $socialshare_icon_limit,
				$pagging,
				array(
					'Text',
					'limit_data',
					array(
						'label' => 'count (number of reviews to show).',
						'value' => 5,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
				array(
					'MultiCheckbox',
					'ratingreviews',
					array(
						'label' => "Choose options to be shown in \'Review Stars\' search fields.",
						'multiOptions' => array(
							'1star' => '1 Star',
							'2star' => '2 Star',
							'3star' => '3 Star',
							'4star' => '4 Star',
							'5star' => '5 Star',
						),
					)
				),
				array(
					'MultiCheckbox',
					'recommended_reviews',
					array(
						'label' => "Choose options to be shown in 'Recommended reviews only' search fields.",
						'multiOptions' => array(
							'allreviews' => 'All Reviews',
							'recommendedonly' => 'Recommended only',
						),
					)
				),
			),
		),
	),
	array(
		'title' => 'SNS - Advanced Blog - Popular / Featured / Verified Reviews',
		'description' => "Displays reviews as chosen by you based on chosen criteria for this widget. The placement of this widget depends on the criteria chosen for this widget.",
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.popular-featured-verified-reviews',
		'adminForm' => array(
			'elements' => array(
				array(
					'Select',
					'info',
					array(
						'label' => 'Choose Popularity Criteria.',
						'multiOptions' => array(
							"creation_date" => "Recently Created",
							"most_viewed" => "Most Viewed",
							"most_liked" => "Most Liked",
							"most_commented" => "Most Commented",
							"most_rated" => "Most Rated",
							"featured" => "Featured",
							"verified" => "Verified",
						)
					),
					'value' => 'recently_updated',
				),
				$imageType,
				array(
				'Select',
					'showLimitData',
					array(
						'label' => 'Do you want to allow users to view more reviews in this widget? (If you choose Yes, then users will see Next & Previous buttons to view more reviews.',
						'multiOptions' => array(
							"1" => "Yes",
							"0" => "No",
						)
					),
					'value' => '1',
				),
				array(
					'MultiCheckbox',
					'show_criteria',
					array(
						'label' => "Choose from below the details that you want to show for blog in this widget.",
						'multiOptions' => array(
							'title' => 'Review Title',
							'like' => 'Likes Count',
							'view' => 'Views Count',
							'comment' => 'Comments Count',
							'rating' => 'Ratings',
							'verifiedLabel' => 'Verified Label',
							'featuredLabel' => 'Featured Label',
							'description' => 'Description',
							'by' => 'By',
						),
					),
				),
				array(
					'Text',
					'title_truncation',
					array(
						'label' => 'Title truncation limit.',
						'value' => 45,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
				array(
					'Text',
					'review_description_truncation',
					array(
						'label' => 'Descripotion truncation limit.',
						'value' => 45,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
				array(
					'Text',
					'limit_data',
					array(
						'label' => 'Count (number of reviews to show).',
						'value' => 5,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
			)
		),
	),
	array(
		'title' => 'SNS - Advanced Blog - Review of the Day',
		'description' => "This widget displays review of the day as chosen by you from the \"Manage Reviews\" settings of this plugin.",
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.review-of-the-day',
		'adminForm' => array(
			'elements' => array(
				array(
					'MultiCheckbox',
					'show_criteria',
					array(
						'label' => "Choose from below the details that you want to show for member in this widget.",
						'multiOptions' => array(
							'title' => 'Display Review Title',
							'like' => 'Likes Count',
							'view' => 'Views Count',
							'rating' => 'Ratings',
							'featuredLabel' => 'Featured Label',
							'verifiedLabel' => 'Verified Label',
							'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
							'by' => 'Review Owner Name',
						),
						'escape' => false,
					),
				),
        $socialshare_enable_plusicon,
        $socialshare_icon_limit,
				array(
					'Text',
					'title_truncation',
					array(
						'label' => 'Title truncation limit.',
						'value' => 45,
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
				array(
					'Text',
					'height',
					array(
						'label' => 'Enter the height of photo block of review(in pixels).',
						'value' => '180',
						'validators' => array(
							array('Int', true),
							array('GreaterThan', true, array(0)),
						)
					)
				),
			)
		),
  ),
	array(
		'title' => 'SNS - Advanced Blog - Browse Reviews',
		'description' => 'Displays all reviews for blogs on your webiste. This widget is placed on "SNS - Advanced Blog - Browse Reviews Page".',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.browse-reviews',
		'defaultParams' => array(
		),
		'adminForm' => array(
				'elements' => array(
					array(
						'MultiCheckbox',
						'stats',
						array(
							'label' => 'Choose options to show in this widget.',
							'multiOptions' => array(
								"likeCount" => "Likes Count",
								"commentCount" => "Comments Count",
								"viewCount" => "Views Count",
								"title" => "Review Title",
								"share" => "Share Button",
								"report" => "Report Button",
								'likeButton' => 'Like Button',
								"pros" => "Pros",
								"cons" => "Cons",
								"description" => "Description",
								"recommended" => "Recommended",
								'postedBy' => "Posted On",
								'parameter' => 'Review Parameters',
								"creationDate" => "Creation Date",
								'rating' => 'Rating Stars',
                'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
							),
							'escape' => false,
						)
					),
          $socialshare_enable_plusicon,
          $socialshare_icon_limit,
					array(
						'MultiCheckbox',
						'show_criteria',
						array(
								'label' => "Choose from below the details that you want to show for blog in this widget.",
								'multiOptions' => array(
										'sponsoredLabel' => 'Sponsored Label',
										'featuredLabel' => 'Featured Label',
										'verifiedLabel' => 'Verified Label',
								),
						),
					),
					$pagging,
					array(
						'Text',
						'limit_data',
						array(
								'label' => 'Count (number of reviews to show).',
								'value' => 5,
								'validators' => array(
										array('Int', true),
										array('GreaterThan', true, array(0)),
								)
						)
					),
				),
		),
  ),
	array(
		'title' => 'SNS - Advanced Blog - Review Browse Search',
		'description' => 'Displays a search form in the review browse page as configured by you.',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'name' => 'sesblog.browse-review-search',
		'requirements' => array(
				'no-subject',
		),
		'autoEdit' => true,
		'adminForm' => array(
			'elements' => array(
				array(
					'Radio',
					'view_type',
					array(
						'label' => "Choose the View Type.",
						'multiOptions' => array(
								'horizontal' => 'Horizontal',
								'vertical' => 'Vertical'
						),
						'value' => 'vertical',
					)
				),
				array(
					'Radio',
					'review_title',
					array(
						'label' => "Show \'Review Title\' search field?",
						'multiOptions' => array(
								'1' => 'Yes',
								'0' => 'No'
						),
						'value' => '1',
					)
				),
				array(
					'Radio',
					'review_search',
					array(
						'label' => "Show \'Browse By\' search field?",
						'multiOptions' => array(
								'1' => 'Yes',
								'0' => 'No'
						),
						'value' => '1',
					)
				),
				array(
					'MultiCheckbox',
					'view',
					array(
						'label' => "Choose options to be shown in \'Browse By\' search fields.",
						'multiOptions' => array(
							'mostSPliked' => 'Most Liked',
							'mostSPviewed' => 'Most Viewed',
							'mostSPcommented' => 'Most Commented',
							'mostSPrated' => 'Most Rated',
							'verified' => 'Verified Only',
							'featured' => 'Featured Only',
						),
					)
				),
				array(
					'Radio',
					'review_stars',
					array(
						'label' => "Show \'Review Stars\' search field?",
						'multiOptions' => array(
								'1' => 'Yes',
								'0' => 'No'
						),
						'value' => '1',
					)
				),
				array(
					'Radio',
					'review_recommendation',
					array(
						'label' => "Show \'Recommended Review\' search field?",
						'multiOptions' => array(
								'1' => 'Yes',
								'0' => 'No',
						),
						'value' => '1',
					)
				),

				array(
					'MultiCheckbox',
					'ratingreviews',
					array(
						'label' => "Choose options to be shown in \'Review Stars\' search fields.",
						'multiOptions' => array(
							'1star' => '1 Star',
							'2star' => '2 Star',
							'3star' => '3 Star',
							'4star' => '4 Star',
							'5star' => '5 Star',
						),
					)
				),
				array(
					'MultiCheckbox',
					'recommended_reviews',
					array(
						'label' => "Choose options to be shown in \'Recommended reviews only\' search fields.",
						'multiOptions' => array(
							'allreviews' => 'All Reviews',
							'recommendedonly' => 'Recommended only',
						),
					)
				),

			)
		),
  ),
// 	  array(
//     'title' => 'SNS - Advanced Blog - Blog Cover',
//     'description' => 'This widget displaysblog cover photo on Blog Profile Page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
//     'category' => 'SNS - Advanced Blog',
//     'type' => 'widget',
//     'autoEdit' => true,
//     'name' => 'sesblog.blog-cover',
//     'requirements' => array(
//       'subject' => 'blog',
//     ),
//   ),
 array(
    'title' => 'SNS - Advanced Blog - Labels',
    'description' => 'This widget displays Featured, Sponsored and Verified labels on Blog Profile Page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.labels',
  ),
	 array(
    'title' => 'SNS - Advanced Blog - Tabs',
    'description' => 'This widget displays Tabs in the sidebar widget. Place it at Blog Profile Page.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.profile-sidebar-tabs',
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Sidebar Tabbed Widget',
    'description' => 'Displays a tabbed widget for blogs. You can place this widget anywhere on your site.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.sidebar-tabbed-widget',
    'requirements' => array(
      'subject' => 'blog',
    ),
    'adminForm' => 'Sesblog_Form_Admin_SidebarTabbed',
  ),
  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Content',
    'description' => 'Displays blog content according to the design choosen by the blog poster while creating or editing the blog. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'special' => 1,
    'name' => 'sesblog.view-blog',
		'adminForm' => array(
			'elements' => array(
				array(
					'MultiCheckbox',
					'show_criteria',
					array(
						'label' => "Choose from below the details that you want to show in this widget.",
						'multiOptions' => array(
							'title' => 'Title',
							'description' => 'Show Description (For Blog Profile Page 1)',
							'photo' => 'Blog Photo',
							'socialShare' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a> (For Blog Profile Page 1 & Profile Page 3)',
							'ownerOptions' => 'Owner Options',
							'ownername' => "Owner Name",
							'ownerPhoto' => "Owner Photo (Will only work for design 2 & 4)",
							"createDate" => "Creation Date",
							"readtime" => "Minute Read",
							'postComment' => 'Comment Button (For Blog Profile Page 1)',
							'rating' => 'Rating Star',
							'likeButton' => 'Like Button (For Blog Profile Page 1 & Profile Page 3)',
							'tags' => 'Tags (For Blog Profile Page 1)',
							'category' => "Category ",
							'favouriteButton' => 'Favourite Button (For Blog Profile Page 1 & Profile Page 3)',
							'view' => 'View Count',
							'like' => 'Like Count',
							'comment' => 'Comment Count',
							'review' => 'Review Count',
							'statics' => 'Show Statistics'
						),
						'escape' => false,
					)
				),
				array(
          'Select',
          'socialshare_enable_plusicon',
          array(
              'label' => "Enable More Icon for social share buttons? (This will only work with Blog Profile Page 1)",
              'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
              ),
          )
        ),
				array(
          'Text',
          'socialshare_icon_limit',
          array(
            'label' => 'Count (number of social sites to show). If you enable More Icon, then other social sharing icons will display on clicking this plus icon. (This will only work with Blog Profile Page 1)',
            'value' => 2,
          ),
        ),
				array(
          'Text',
          'heightss',
          array(
              'label' => 'Enter the height of blog image(in pixels).',
              'value' => '500',
          )
        ),
			),
		),
  ),
	array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Info',
    'description' => 'Displays blog content according to the design choosen by the blog poster while creating or editing the blog. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.view-blog-description',
		'adminForm' => array(
			'elements' => array(
				array(
					'MultiCheckbox',
					'show_criteria',
					array(
						'label' => "Choose from below the details that you want to show in this widget.",
						'multiOptions' => array(
							'description' => 'Show Description',
							'socialShare' => 'Social Share Buttons ( Not Supported in Profile page 3) <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
							'postComment' => 'Comment Button',
							'likeButton' => 'Like Button ( Not Supported in Profile page 3)',
							'favouriteButton' => 'Favourite Button ( Not Supported in Profile page 3)',
							'tags' => 'Tags'
						),
            'defaultParams' => array('title', 'description', 'photo', 'socialShare', 'ownerOptions', 'rating', 'postComment', 'likeButton', 'favouriteButton', 'view', 'like', 'comment', 'review', 'statics','shareButton','smallShareButton'),
						'escape' => false,
					)
				),
				$socialshare_enable_plusicon,
				$socialshare_icon_limit,
			),
		),
  ),
array(
		'title' => 'SNS - Advanced Blog - Category Banner Widget',
		'description' => 'Displays a banner for categories. You can place this widget at browse page of category on your site.',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.banner-category',
		'requirements' => array(
				'subject' => 'blog',
		),
		'adminForm' => 'Sesblog_Form_Admin_Categorywidget',
	),
	array(
		'title' => 'SNS - Advanced Blog - Calendar Widget',
		'description' => 'Displays calendar . You can place this widget at browse page of blog on your site.',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => false,
		'name' => 'sesblog.calendar'
	),
	    array(
        'title' => 'SNS - Advanced Blog - Categories Square Block View',
        'description' => 'Displays all categories of blogs in square blocks. Edit this widget to configure various settings.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesblog.blog-category',
        'requirements' => array(
            'subject' => 'blog',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one block (in pixels).',
                        'value' => '232px',
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one block (in pixels).',
                        'value' => '232px',
                    )
                ),
								 array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'count (number of categories to show).',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
								array(
                    'Select',
                    'blog_required',
                    array(
                        'label' => "Do you want to show only those categories under which atleast 1 blog is posted?",
                        'multiOptions' => array(
                            '1' => 'Yes, show only categories with blogs',
                            '0' => 'No, show all categories',
                        ),
                    ),
										'value' =>'1'
                ),
                array(
                    'Select',
                    'criteria',
                    array(
                        'label' => "Choose Popularity Criteria.",
                        'multiOptions' => array(
                            'alphabetical' => 'Alphabetical Order',
                            'most_blog' => 'Most Blogs Category First',
                            'admin_order' => 'Admin Order',
                        ),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show on each block.",
                        'multiOptions' => array(
                            'title' => 'Category Title',
                            'icon' => 'Category Icon',
                            'countBlogs' => 'Blog count in each category',
                        ),
                    )
                ),
            ),
        ),
    ),
        array(
        'title' => 'SNS - Advanced Blog - Category Based Blogs Block View',
        'description' => 'Displays blogs in attractive square block view on the basis of their categories. This widget can be placed any where on your website.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesblog.category-associate-blog',
        'requirements' => array(
            'subject' => 'blog',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show for albums in this widget.",
                        'multiOptions' => array(
                            'like' => 'Likes Count',
                            'comment' => 'Comments Count',
                            'rating' => 'Rating Count',
                            'ratingStar' => 'Rating Star',
                            'view' => 'Views Count',
                            'title' => 'Title Count',
                            'favourite' => 'Favourites Count',
                            'by' => 'Blog Owner\'s Name',
                            'featuredLabel' => 'Featured Label',
                            'sponsoredLabel' => 'Sponsored Label',
                            'creationDate' => 'Show Publish Date',
                            'readmore' => 'Read More',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'popularity_blog',
                    array(
                        'label' => 'Choose Blog Display Criteria.',
                        'multiOptions' => array(
                            "creation_date" => "Recently Created",
                            "view_count" => "Most Viewed",
                            "like_count" => "Most Liked",
                            "rating" => "Most Rated",
                            "comment_count" => "Most Commented",
                            "favourite_count" => "Most Favourite",
                            'featured' => 'Only Featured',
                            'sponsored' => 'Only Sponsored',
                        ),
                        'value' => 'like_count',
                    )
                ),
                $pagging,
                array(
                    'Select',
                    'count_blog',
                    array(
                        'label' => "Show blogs count in each category.",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                    ),
                ),
                array(
                    'Select',
                    'criteria',
                    array(
                        'label' => "Choose Popularity Criteria.",
                        'multiOptions' => array(
                            'alphabetical' => 'Alphabetical Order',
                            'most_blog' => 'Categories with maximum blogs first',
                            'admin_order' => 'Admin selected order for categories',
                        ),
                    ),
                ),
                array(
                    'Text',
                    'category_limit',
                    array(
                        'label' => 'count (number of categories to show).',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'blog_limit',
                    array(
                        'label' => 'count (number of blogs to show in each category. This settging will work, if you choose "Yes" for "Show blogs count in each category" setting above.").',
                        'value' => '8',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
								array(
									'Text',
									'blog_description_truncation',
									array(
										'label' => 'Descripotion truncation limit.',
										'value' => 45,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										)
									)
								),
                array(
                    'Text',
                    'seemore_text',
                    array(
                        'label' => 'Enter the text for "+ See All" link. Leave blank if you don\'t want to show this link. (Use[category_name] variable to show the associated category name).',
                        'value' => 'View all [category_name]',
                    )
                ),
                array(
                    'Select',
                    'allignment_seeall',
                    array(
                        'label' => "Choose alignment of \"+ See All\" field
",
                        'multiOptions' => array(
                            'left' => 'left',
                            'right' => 'right'
                        ),
                    ),
                ),
                $heightOfContainer,
                $widthOfContainer,
            )
        ),
    ),
        array(
        'title' => 'SNS - Advanced Blog - Category View Page Widget',
        'description' => 'Displays a view page for categories. You can place this widget at view page of category on your site.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'name' => 'sesblog.category-view',
        'requirements' => array(
            'subject' => 'blog',
        ),
        'adminForm' => array(
            'elements' => array(
								array(
									'Select',
									'viewType',
									array(
										'label' => 'Choose the view type.',
										'multiOptions' => array(
											"list" => "List View",
											"grid" => "Grid View",
										)
									),
									'value' => 'list'
								),
                array(
                    'Select',
                    'show_subcat',
                    array(
                        'label' => "Show 2nd-level or 3rd level categories blocks.",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'show_subcatcriteria',
                    array(
                        'label' => "Choose from below the details that you want to show on each category block.",
                        'multiOptions' => array(
                            'icon' => 'Category Icon',
                            'title' => 'Category Title',
                            'countBlog' => 'Blogs count in each category',
                        ),
                    )
                ),
                array(
                    'Text',
                    'heightSubcat',
                    array(
                        'label' => 'Enter the height of one 2nd-level or 3rd level category\'s block (in pixels).
',
                        'value' => '160px',
                    )
                ),
                array(
                    'Text',
                    'widthSubcat',
                    array(
                        'label' => 'Enter the width of one 2nd-level or 3rd level category\'s block (in pixels).
',
                        'value' => '250px',
                    )
                ),
								 array(
                    'Text',
                    'textBlog',
                    array(
                        'label' => 'Enter the text for \'heading\' of this widget.',
                        'value' => 'Blogs we like',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show on each category block.",
                        'multiOptions' => array(
                            'featuredLabel' => 'Featured Label',
                            'sponsoredLabel' => 'Sponsored Label',
                            'like' => 'Likes',
                            'comment' => 'Comments',
                            'rating' => 'Rating Count',
                            'ratingStar' => 'Rating Star',
                            'favourite'=>'Favourite',
                            'view' => 'Views',
                            'title' => 'Titles',
                            'by' => 'Item Owner Name',
                            'description' => 'Show Description',
                            'readmore' => 'Show Read More',
                            'creationDate' => 'Show Publish Date',
                        ),
                    )
                ),
                $pagging,
								array(
									'Text',
									'description_truncation',
									array(
										'label' => 'Description truncation limit.',
										'value' => 45,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										)
									)
								),
                array(
                    'Text',
                    'blog_limit',
                    array(
                        'label' => 'count (number of blogs to show).',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of one block (in pixels. This setting will effect after 3 designer blocks).',
                        'value' => '160px',
                    )
                ),
                array(
                    'Text',
                    'width',
                    array(
                        'label' => 'Enter the width of one block (in pixels. This setting will effect after 3 designer blocks).',
                        'value' => '160px',
                    )
                )
            )
        ),
    ),
    array(
        'title' => 'SNS - Advanced Blog - Blog tags',
        'description' => 'Displays all blog tags on your website. The recommended page for this widget is "SNS - Advanced Blog - Browse Tags Page".',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'name' => 'sesblog.tag-blogs',
    ),
    array(
		'title' => 'SNS - Advanced Blog - Popular / Featured / Sponsored / Verified 3 Blogs View',
		'description' => '',
		'category' => 'SNS - Advanced Blog',
		'type' => 'widget',
		'autoEdit' => true,
		'name' => 'sesblog.featured-sponsored-verified-random-blog',
		   'adminForm' => array(
      'elements' => array(
              array(
          'Text',
          'description',
          array(
            'label'=>'Enter Short Description',
          )
        ),
      	array(
	  'Select',
	  'category',
	  array(
	    'label' => 'Choose the category.',
	    'multiOptions' => $categories
	  ),
	  'value' => ''
	),
	array(
	  'Select',
	  'criteria',
	  array(
	    'label' => "Display Content",
	    'multiOptions' => array(
	      '5' => 'All including Featured and Sponsored',
	      '1' => 'Only Featured',
	      '2' => 'Only Sponsored',
	      '3' => 'Both Featured and Sponsored',
	      '6' => 'Only Verified',
	      '4' => 'All except Featured and Sponsored',
	    ),
	    'value' => 5,
	  )
	),
		array(
		'Select',
		'order',
		array(
			'label' => 'Duration criteria for the blogs to be shown in this widget.',
			'multiOptions' => array(
				'' => 'All',
				'week' => 'This Week',
				'month' => 'This Month',
			),
			'value' => '',
		)
	),
		array(
	  'MultiCheckbox',
	  'show_criteria',
	  array(
	    'label' => "Choose from below the details that you want to show for blog in this widget.",
	    'multiOptions' => array(
	      'like' => 'Likes Count',
	      'comment' => 'Comments Count',
	      'favourite' => 'Favourites Count',
	      'view' => 'Views Count',
	      'title' => 'Blog Title',
	      'by' => 'Blog Owner\'s Name',
		'rating' =>'Rating Count',
		'ratingStar' =>'Rating Stars',
		'featuredLabel' => 'Featured Label',
		'sponsoredLabel' => 'Sponsored Label',
		'verifiedLabel' => 'Verified Label',
		'favouriteButton' => 'Favourite Button',
		'likeButton' => 'Like Button',
		'category' => 'Category',
		'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
		'creationDate' => 'Show Publish Date',
	    ),
	    'escape' => false,
	  )
	),
	        $socialshare_enable_plusicon,
        $socialshare_icon_limit,
      ),
    ),

	),

	 array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Sub Blogs',
    'description' => 'Displays sub blogs on blog profile page. The recommended page for this widget is "SNS - Advanced Blog - Blog Profile Page".',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.related-blogs',
        'adminForm' => array(
			'elements' => array (
	array(
	  'MultiCheckbox',
	  'show_criteria',
	  array(
	    'label' => "Choose from below the details that you want to show for blog in this widget.",
	    'multiOptions' => array(
	      'like' => 'Likes Count',
	      'comment' => 'Comments Count',
	      'favourite' => 'Favourites Count',
	      'view' => 'Views Count',
	      'title' => 'Blog Title',
	      'by' => 'Blog Owner\'s Name',
				'rating' =>'Rating Count',
				'ratingStar' =>'Rating Stars',
				'featuredLabel' => 'Featured Label',
				'sponsoredLabel' => 'Sponsored Label',
				'verifiedLabel' => 'Verified Label',
				'favouriteButton' => 'Favourite Button',
				'likeButton' => 'Like Button',
	      'category' => 'Category',
	      'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
	    ),
	    'escape' => false,
	  )
	),
  $socialshare_enable_plusicon,
  $socialshare_icon_limit,
	array(
	'Select',
	'showLimitData',
	array(
		'label' => 'Do you want to allow users to view more sub blogs in this widget? (If you choose Yes, then users will see Next & Previous buttons to view more sub blogs.',
		'multiOptions' => array(
			"1" => "Yes",
			"0" => "No",
		)
	),
	'value' => '1',
),

	array(
	  'Text',
	  'height',
	  array(
	    'label' => 'Enter the height of one block (in pixels).',
	    'value' => '180',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'width',
	  array(
	    'label' => 'Enter the width of one block (in pixels).',
	    'value' => '180',
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
	array(
	  'Text',
	  'list_title_truncation',
	  array(
	    'label' => 'Blog title truncation limit.',
	    'value' => 45,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
		array(
	  'Text',
	  'limit_data',
	  array(
	    'label' => 'Count (number of blogs to show).',
	    'value' => 3,
	    'validators' => array(
	      array('Int', true),
	      array('GreaterThan', true, array(0)),
	    )
	  )
	),
			),
		),
  ),

      array(
        'title' => 'SNS - Advanced Blog - Blog Locations',
        'description' => 'This widget displays blogs based on their locations in Google Map.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'name' => 'sesblog.blog-location',
				'autoEdit' => true,
    		'adminForm' => 'Sesblog_Form_Admin_Location',
    ),

  array(
    'title' => 'SNS - Advanced Blog - Popular / Featured / Sponsored / Verified Blogs Slideshow',
    'description' => "Displays slideshow of blogs as chosen by you based on chosen criteria for this widget. You can also choose to show Blogs of specific categories in this widget. The placement of this widget depends on the criteria chosen for this widget.",
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'sesblog.featured-sponsored-verified-category-slideshow',
    'adminForm' => array(
      'elements' => array(
        array(
          'Select',
          'category',
          array(
            'label' => 'Choose the category.',
            'multiOptions' => $categories
          ),
          'value' => ''
        ),
        array(
          'Select',
          'criteria',
          array(
            'label' => "Display Content",
            'multiOptions' => array(
              '0' => 'All Blogs',
              '1' => 'Only Featured',
              '2' => 'Only Sponsored',
              '6' => 'Only Verified',
            ),
            'value' => 5,
          )
        ),
        array(
          'Select',
          'order',
          array(
            'label' => 'Duration criteria for the blogs to be shown in this widget.',
            'multiOptions' => array(
              '' => 'All',
              'week' => 'This Week',
              'month' => 'This Month',
            ),
            'value' => '',
          )
        ),
        array(
          'Select',
          'info',
          array(
            'label' => 'Choose Popularity Criteria.',
            'multiOptions' => array(
              "recently_created" => "Recently Created",
              "most_viewed" => "Most Viewed",
              "most_liked" => "Most Liked",
              "most_rated" => "Most Rated",
              "most_commented" => "Most Commented",
              "most_favourite" => "Most Favourite",
            )
          ),
          'value' => 'recently_created',
        ),
        array(
          'Select',
          'isfullwidth',
          array(
            'label' => 'Do you want to show category carousel in full width?',
            'multiOptions'=>array(
              1=>'Yes',
              0=>'No'
            ),
            'value' => 1,
          )
        ),
          array(
          'Select',
          'autoplay',
          array(
            'label' => "Do you want to enable autoplay of blogs?",
            'multiOptions' => array(
              1=>'Yes',
              0=>'No'
            ),
          ),
        ),
        array(
          'Text',
          'speed',
            array(
            'label' => 'Delay time for next blog when you have enabled autoplay.',
            'value' => '2000',
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
        array(
          'MultiCheckbox',
          'show_criteria',
          array(
            'label' => "Choose from below the details that you want to show for blogs in this widget.",
            'multiOptions' => array(
              'like' => 'Likes Count',
              'comment' => 'Comments Count',
              'favourite' => 'Favourites Count',
              'view' => 'Views Count',
              'title' => 'Blog Title',
              'by' => 'Blog Owner\'s Name',
              'creationDate' => "Creation Date",
              'readtime' => "Minute Read Count",
              'rating' =>'Rating Count',
              'ratingStar' =>'Rating Stars',
              'featuredLabel' => 'Featured Label',
              'sponsoredLabel' => 'Sponsored Label',
              'verifiedLabel' => 'Verified Label',
              'favouriteButton' => 'Favourite Button',
              'likeButton' => 'Like Button',
              'category' => 'Category',
              'description'=>'Description',
              'socialSharing' =>'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>',
            ),
            'escape' => false,

          )
        ),
        $socialshare_enable_plusicon,
        $socialshare_icon_limit,
        array(
          'Text',
          'title_truncation',
          array(
            'label' => 'Blog title truncation limit.',
            'value' => 45,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
        array(
          'Text',
          'height',
          array(
            'label' => 'Enter the height of one slide block (in pixels).',
            'value' => '500',
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
        array(
          'Text',
          'limit_data',
          array(
            'label' => 'Count (number of blogs to show).',
            'value' => 5,
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
            )
          )
        ),
      )
    ),
	),
	  array(
    'title' => 'SNS - Advanced Blog - Blog Content Widget',
    'description' => 'Displays a content widget for blog. You can place this widget on blog profile page in tab container only on your site.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.content',
    'requirements' => array(
      'subject' => 'blog',
    ),
  ),
  	  array(
    'title' => 'SNS - Advanced Blog - Blog Profile - Photo',
    'description' => 'Displays a blog photo widget. You can place this widget on blog profile page only on your site.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.blog-photo',
  ),
    	  array(
    'title' => 'SNS - Advanced Blog - Blog Title Widget',
    'description' => 'Displays a blog title widget. You can place this widget on blog profile page only on your site.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.blog-title',
    'requirements' => array(
      'subject' => 'blog',
    ),
  ),
    	  array(
    'title' => 'SNS - Advanced Blog - Blog Social Share Widget',
    'description' => 'Displays a blog social share widget. You can place this widget on blog profile page only on your site.',
    'category' => 'SNS - Advanced Blog',
    'type' => 'widget',
    'name' => 'sesblog.blog-socialshare',
    'adminForm' => array(
			'elements' => array(
				array(
					'Radio',
					'socialshare_design',
					array(
						'label' => "Do you want this social share widget on blog profile page ?",
						'multiOptions' => array(
							'1' => 'Social Share Design 1',
							'2' => 'Social Share Design 2',
							'3' => 'Social Share Design 3',
							'4' => 'Social Share Design 4',
						),
						'value' => 'design1',
					)
				),
			),
		),
    'requirements' => array(
      'subject' => 'blog',
    ),
  ),
      array(
        'title' => 'SNS - Advanced Blog - Blog Profile - Map',
        'description' => 'Displays a blog location on map on it\'s profile.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'name' => 'sesblog.blog-map',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
		
  		array(
        'title' => 'SNS - Advanced Blog - Blog Contact Information',
        'description' => 'Displays blog contact information in this widget. The placement of this widget depends on the blog profile page.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesblog.blog-contact-information',
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'show_criteria',
                    array(
                        'label' => "Choose from below the details that you want to show in this widget.",
                        'multiOptions' => array(
                            'name' => 'Contact Name',
														'email' => 'Contact Eamail',
                            'phone' => 'Contact Phone Number',
														'facebook' =>'Contact Facebook',
														'linkedin'=>'Contact Linkedin',
														'twitter'=>'Contact Twitter',
														'website'=>'Contact Website',
                        ),
                    )
                ),
            )
        ),
		),
		    array(
        'title' => 'SNS - Advanced Blog - Profile Blog\'s Like Button',
        'description' => 'Displays like button for blog. This widget is only placed on "Blog Profile Page" only.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesblog.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),

    		    array(
        'title' => 'SNS - Advanced Blog - Profile Blog\'s Favourite Button',
        'description' => 'Displays favourite button for blog. This widget is only placed on "Blog Profile Page" only.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesblog.favourite-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),

        array(
        'title' => 'SNS -  Advanced Members - Browse Contributors',
        'description' => 'Displays all members of your site based on criteria. This widgets is placed on "SNS -  Advanced Members - Browse Contributor Page" only.',
        'category' => 'SNS - Advanced Blog',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sesblog.browse-contributors',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'limit_data',
                    array(
                        'label' => 'count (number of members to show).',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                $pagging,
                	array(
	  'Select',
	  'info',
	  array(
	    'label' => 'Choose Popularity Criteria.',
	    'multiOptions' => array(
	      "recently_created" => "Recently Created",
	      "most_viewed" => "Most Viewed",
	      "most_liked" => "Most Liked",
	      "most_contributors" => "More Articles Written",
	    )
	  ),
	  'value' => 'recently_created',
	),
                $titleTruncationList,
                $photoHeight,
                $photowidth,
            )
        ),
    ),
);
