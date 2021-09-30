<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.isActivate', 0);
if (empty($isActive)) {
  return;
}

$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => 'Page Profile Videos',
        'description' => 'This widget forms the Videos tab on the Page Profile and displays the videos of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.profile-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Most Commented Videos',
        'description' => "Displays list of a Page's most commented videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.comment-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Commented Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Page Profile Most Recent Videos',
        'description' => "Displays list of a Page's most recent videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.recent-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Recent Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Page Profile Top Rated Videos',
        'description' => "Displays list of a Page's top rated videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.rate-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Top Rated Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Page Profile Most Liked Videos',
        'description' => "Displays list of a Page's most liked videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.like-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Liked Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Page Profile Featured Videos',
        'description' => "Displays list of page's featured videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.featurelist-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Featured Page Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Page Profile Highlighted Videos',
        'description' => "Displays list of page's highlighted videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.highlightlist-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Highlighted Page Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Page Profile Most Viewed Videos',
        'description' => "Displays list of a Page's most viewed videos. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagevideo.view-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Viewed Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Recent Videos',
        'description' => 'Displays the recent videos of the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.homerecent-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Recent Videos'
        ),
         'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
					),
        ),
    ),

    array(
			'title' => 'Search Page Videos form',
			'description' => 'Displays the form for searching Page Videos on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagevideo.search-sitepagevideo',
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
											'label' => 'Choose the fields that you want to be available in the Search Page Videos form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Page Title", "4" => "Video Keywords", "5" => "Page Category"),
									),
							),
					),
			)
    ),

     array(
        'title' => 'Page Videos',
        'description' => 'Displays the list of Videos from Pages created on your community. This widget should be placed in the widgetized Page Videos page. Results from the Search Page Videos form are also shown here.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.sitepage-video',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
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
        'title' => 'Sponsored Videos',
        'description' => 'Displays the Videos from Paid Pages. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.sitepage-sponsoredvideo',
        'defaultParams' => array(
            'title' => 'Sponsored Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
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
            ),
        ),
    ),
 
    array(
        'title' => 'Featured Videos',
        'description' => "Displays Featured Page Videos. You can mark Page Videos as Featured from the “Manage Page Videos” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.homefeaturelist-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Featured Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
					),
        ),
    ),
   
//     array(
//         'title' => 'Highlighted Videos',
//         'description' => "Displays Highlighted Page Videos. You can mark Page Videos as Highlighted from the “Manage Page Videos” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
//         'category' => 'Pages',
//         'type' => 'widget',
//         'name' => 'sitepagevideo.homehighlightlist-sitepagevideos',
//         'defaultParams' => array(
//             'title' => 'Highlighted Videos',
//             'titleCount' => true,
//         ),
//         'adminForm' => array(
// 					'elements' => array(
// 						array(
// 								'Text',
// 								'itemCount',
// 								array(
// 										'label' => 'Count',
// 										'description' => '(number of videos to show)',
// 										'value' => 3,
// 										'validators' => array(
// 											array('Int', true),
// 											array('GreaterThan', true, array(0)),
// 										),
// 								),
// 						),
// 					),
//         ),
//     ),

    array(
        'title' => 'Most Commented Videos',
        'description' => "Displays the Most Commented Page Videos. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.homecomment-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Commented Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
					),
        ),
    ),
    
     array(
        'title' => 'Most Viewed Videos',
        'description' => "Displays the Most Viewed Page Videos. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.homeview-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Viewed Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
					),
        ),
    ),
 
    array(
        'title' => 'Most Liked Videos',
        'description' => "Displays the Most Liked Page Videos. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.homelike-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Most Liked Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
					),
        ),
    ),

    array(
        'title' => 'Top Rated Videos',
        'description' => "Displays the Top Rated Page Videos. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.homerate-sitepagevideos',
        'defaultParams' => array(
            'title' => 'Top Rated Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
					),
        ),
    ),

	array(
			'title' => 'Page Video View',
			'description' => "This widget should be placed on the Page Video View Page.",
      'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagevideo.video-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),

   array(
    'title' => 'People Also Liked',
    'description' => 'Displays a list of other Page Videos that the people who liked this Page Video also liked. You can choose the number of entries to be shown. This widget should be placed on Page Video View Page.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagevideo.show-also-liked',
    //'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'People Also Liked',
    ),
    'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
										'value' => 3,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
					),
        ),
    'requirements' => array(
      'subject' => 'sitepagevideo',
    ),
  ),
  array(
    'title' => 'Other Videos From Page',
    'description' => 'Displays a list of other Page Videos corresponding to the Page of which the video is being viewed. You can choose the number of entries to be shown. This widget should be placed on Page Video View Page.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagevideo.show-same-poster',
    //'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Other Videos From Page',
    ),
    'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
										'value' => 3,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
					),
        ),
    'requirements' => array(
      'subject' => 'sitepagevideo',
    ),
  ),
  array(
    'title' => 'Similar Videos',
    'description' => 'Displays Page Videos similar to the Page Video being viewed based on tags. You can choose the number of entries to be shown. This widget should be placed on Page Video View Page.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagevideo.show-same-tags',
   // 'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Similar Videos',
    ),
     'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
										'value' => 3,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
					),
        ),
    'requirements' => array(
      'subject' => 'sitepagevideo',
    ),
  ),

  array(
        'title' => 'Page’s Featured Videos Slideshow',
        'description' => 'Displays featured videos in an attractive slideshow. You can set the count of the number of videos to show in this widget. If the total number of videos featured are more than that count, then the videos to be displayed will be sequentially picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.featured-videos-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Videos',
            'itemCountPerPage' => 10,
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
        'title' => 'Page’s Featured Videos Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured videos on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.featured-videos-carousel',
        'defaultParams' => array(
            'title' => 'Featured Videos',
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
                    'Radio',
                    'vertical',
                    array(
                        'label' => 'Carousel Type',
                        'multiOptions' => array(
                            '0' => 'Horizontal',
                            '1' => 'Vertical',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'inOneRow',
                    array(
                        'label' => 'Videos in a Row',
                        'description' => '(number of videos to show in one row. Video: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'noOfRow',
                    array(
                        'label' => 'Rows',
                        'description' => '(number of rows in one view)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 250,
                    )
                ),
            ),
        ),
    ),

  array(
        'title' => 'Page’s Ajax based Tabbed widget for Videos',
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Videos in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Directory / Pages - Videos Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.list-videos-tabs-view',
        'defaultParams' => array(
            'title' => 'Videos',
            'margin_photo'=>12,
            'showViewMore'=>1
        ),
         'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal Margin between Elements',
                        'description' => '(Horizontal margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 12,
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
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
							)
            ),
        ),
    ),

  array(
        'title' => 'Browse Videos',
        'description' => 'Displays the link to view Page’s Videos Browse page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.sitepagevideolist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),

  array(
        'title' => 'Page’s Video of the Day',
        'description' => 'Displays the Video of the Day as selected by the Admin from the widget settings section of Directory / Pages - Videos Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagevideo.video-of-the-day',
        'defaultParams' => array(
            'title' => 'Video of the Day'
        ),
    ),

  array(
  'title' => 'Popular Video Tags',
  'description' => 'Shows popular tags with frequency.',
  'category' => 'Pages',
  'type' => 'widget',
  'name' => 'sitepagevideo.tagcloud-sitepagevideo',
  'adminForm' => array(
       'elements' => array(
         array(
           'hidden',
           'title',
           array(
             'label' => '',
             'order' => 1051
           )
         ),
         array(
           'hidden',
           'nomobile',
           array(
             'label' => '',
             'order' => 1052
           )
         ),
         array(
           'hidden',
           'execute',
           array(
             'label' => '',
             'order' => 1053
           )
         ),
         array(
           'hidden',
           'cancel',
           array(
             'label' => '',
             'order' => 1054
           )
         ),
       )
     ),
    ),

    array(
    'title' => 'Top Creators : Page Videos',
    'description' => 'Displays the Pages which have the most number of Page Videos added in them. Motivates Page Admins to add more content on your website.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagevideo.topcreators-sitepagevideo',
   // 'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Top Creators',
    ),
     'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of elements to show)',
										'value' => 5,
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
					),
        ),
    'requirements' => array(
      'subject' => 'sitepagevideo',
    ),
  ),
)
?>