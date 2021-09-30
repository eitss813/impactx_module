<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 0);
if ( empty($isActive) ) {
  return;
}

$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$showContent_cover = array("mainPhoto" => "Page Profile Photo", "title" => "Page Title", "followButton" => "Follow Button", "likeButton" => "Like Button", "likeCount" => "Total Likes","followCount" => "Total Followers");
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
	$showContent_cover['memberCount'] = 'Total Members';
	$showContent_cover['addButton'] = 'Add People Button';
	$showContent_cover['joinButton'] = 'Join Page Button';
	$showContent_cover['leaveButton'] = 'Leave Page Button';
	$showContent_option[] = 'addButton';
	$showContent_option[] = 'joinButton';
	$showContent_option[] = 'leaveButton';
	$showContent_option[] = 'memberCount';
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => 'Page Profile Albums',
        'description' => 'This widget forms the Albums tab on the Page Profile and displays the albums of the Page. It also displays the photos added by the Page visitors other than the owner. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.photos-sitepage',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of albums to show)',
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
               array(
                    'Text',
                    'itemCount_photo',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show in album)',
                        'value' => 100,
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
        'title' => 'Page Profile Random Albums',
        'description' => 'Displays random albums and photos of Page on its Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.albums-sitepage',
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => '',
        ),
    ),
    array(
        'title' => 'Page Profile Most Commented Photos',
        'description' => 'Displays list of page’s most commented photos. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.photocomment-sitepage',
        'defaultParams' => array(
            'title' => 'Most Commented Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
        'title' => 'Page Profile Most Liked Photos',
        'description' => 'Displays list of page’s most liked photos. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.photolike-sitepage',
        'defaultParams' => array(
            'title' => 'Most Liked Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
        'title' => 'Page Profile Photos Strip',
        'description' => "Displays some photos out of all the albums of a Page in a strip. Page Admin can choose which photos to be shown in the strip by hiding the ones that should not be displayed. Hidden photos are replaced by new photos and so on. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.photorecent-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 7,
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
        'title' => 'Recent Photos',
        'description' => 'Displays list of recent photos of pages on the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.mostrecentphotos-sitepage',
        'defaultParams' => array(
            'title' => 'Recent Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
                array(
                    'Radio',
                    'showPageName',
                    array(
                        'label' => 'Do you want to show the Page name along with the photos ?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showUserName',
                    array(
                        'label' => "Do you want to show the 'display name' of user who has uploaded the photo ?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showFullPhoto',
                    array(
                        'label' => "Do you want to show large size thumbnails of photos ?",
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
			'title' => 'Search Page Albums form',
			'description' => 'Displays the form for searching Page Albums on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagealbum.search-sitepagealbum',
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
											'label' => 'Choose the fields that you want to be available in the Search Page Albums form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Page Title", "4" => "Album Title", "5" => "Page Category"),
									),
							),
					),
			)
    ),

    array(
        'title' => 'Page Albums',
        'description' => 'Displays the list of Albums from Pages created on your community. This widget should be placed in the widgetized Page Albums page. Results from the Search Page Albums form are also shown here.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.sitepage-album',
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
                        'description' => '(number of albums to show)',
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
        'title' => 'Sponsored Albums',
        'description' => 'Displays the Albums from Paid Pages. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.sitepage-sponsoredalbum',
        'defaultParams' => array(
            'title' => 'Sponsored Albums',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of albums to show)',
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
			'title' => 'Page Album View',
			'description' => "This widget should be placed on the Page Album View Page.",
      'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagealbum.album-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Radio',
									'photosorder',
									array(
											'label' => 'Select the order below to display the photos on your site.',
											'multiOptions' => array(
													1 => 'Newer to older',
													0 => 'Older to newer'
											),
											'value' => 1,
									)
							),
					),
			),
	),

  array(
        'title' => 'Page’s Photo of the Day',
        'description' => 'Displays the Photo of the Day as selected by the Admin from the Photo of the Day section of Directory / Pages - Albums Plugin.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.photo-of-the-day',
        'defaultParams' => array(
            'title' => 'Photo of the Day'
        ),
  ),

  array(
        'title' => 'Most Liked Photos',
        'description' => 'Displays list of page’s most liked photos.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.homephotolike-sitepage',
        'defaultParams' => array(
            'title' => 'Most Liked Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
                    'Radio',
                    'showPageName',
                    array(
                        'label' => 'Do you want to show the Page name along with the photos ?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showUserName',
                    array(
                        'label' => "Do you want to show the 'display name' of user who has uploaded the photo ?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showFullPhoto',
                    array(
                        'label' => "Do you want to show large size thumbnails of photos ?",
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
        'title' => 'Most Commented Photos',
        'description' => 'Displays list of page’s most commented photos.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.homephotocomment-sitepage',
        'defaultParams' => array(
            'title' => 'Most Commented Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
                    'Radio',
                    'showPageName',
                    array(
                        'label' => 'Do you want to show the Page name along with the photos ?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showUserName',
                    array(
                        'label' => "Do you want to show the 'display name' of user who has uploaded the photo ?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showFullPhoto',
                    array(
                        'label' => "Do you want to show large size thumbnails of photos ?",
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
        'title' => 'Most Commented Photos',
        'description' => 'Displays list of page’s most commented photos.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.homephotocomment-sitepage',
        'defaultParams' => array(
            'title' => 'Most Commented Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
        'title' => 'Page’s Featured Photos',
        'description' => 'Displays Featured Photos as chosen by you. You can set the count of the number of photos to show in this widget. If the total number of photos featured are more than that count, then the photos to be displayed will be randomly picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.featured-photos',
        'defaultParams' => array(
            'title' => 'Featured Photos',
            'itemCountPerPage' => 4,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
        'title' => 'Page’s Featured Photos Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured photos on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.featured-photos-carousel',
        'defaultParams' => array(
            'title' => 'Featured Photos',
        ),
        'adminForm' => array(
            'elements' => array(
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
                        'label' => 'Photos in a Row',
                        'description' => '(number of photos to show in one row. Note: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
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
        'title' => 'Page’s Ajax based Tabbed widget for Photos',
        'description' => ' Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Photos in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Directory / Pages - Albums Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.list-photos-tabs-view',
        'defaultParams' => array(
            'title' => 'Photos',
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
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
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
                )
            ),
        ),
    ),

    array(
        'title' => 'Page’s Ajax based Tabbed widget for Albums',
        'description' => ' Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Albums in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Directory / Pages - Albums Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.list-albums-tabs-view',
        'defaultParams' => array(
            'title' => 'Albums',
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
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
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
              )
            ),
        ),
    ),

    array(
        'title' => 'Page’s Featured Albums',
        'description' => 'Displays Featured Albums as chosen by you. You can set the count of the number of albums to show in this widget. If the total number of albums featured are more than that count, then the albums to be displayed will be randomly picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.featured-albums',
        'defaultParams' => array(
            'title' => 'Featured Albums',
            'itemCountPerPage' => 4,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(number of albums to show)',
                        'value' => 4,
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
        'title' => 'Page’s Featured Albums Slideshow',
        'description' => 'Displays featured albums in an attractive slideshow. You can set the count of the number of albums to show in this widget. If the total number of albums featured are more than that count, then the albums to be displayed will be sequentially picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.featured-albums-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Albums',
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
        'title' => 'Page’s Popular Albums',
        'description' => 'Displays most popular albums. There is a setting for the parameter for popularity such as Views, Comments and Likes.',
         'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.list-popular-albums',
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
                    'itemCountPerPage',
                    array(
                        'label' => 'Count',
                        'description' => '(number of albums to show)',
                        'value' => 4,
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
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'view' => 'Views',
                            'comment' => 'Comments',
                            'like' => 'Likes',
                        ),
                        'value' => 'comment',
                    )
                ),
            )
        ),
    ),

		array(
        'title' => 'Most Popular Photos',
        'description' => 'Displays list of most popular photos on the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.popularphotos-sitepage',
        'defaultParams' => array(
            'title' => 'Most Popular Photos',
            'titleCount' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 4,
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
                    'Radio',
                    'showPageName',
                    array(
                        'label' => 'Do you want to show the Page name along with the photos ?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showUserName',
                    array(
                        'label' => "Do you want to show the 'display name' of user who has uploaded the photo ?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
               array(
                    'Radio',
                    'showFullPhoto',
                    array(
                        'label' => "Do you want to show large size thumbnails of photos ?",
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
        'title' => 'Page Albums Browse Page',
        'description' => 'Displays the link to view Page’s Albums Browse page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.sitepagealbumlist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),

    array(
        'title' => 'Page’s Album of the Day',
        'description' => 'Displays the Album of the Day as selected by the Admin from the widget settings section of Directory / Pages - Albums Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagealbum.album-of-the-day',
        'defaultParams' => array(
            'title' => 'Album of the Day'
        ),
    ),

    array(
    'title' => 'Top Creators : Page Albums',
    'description' => 'Displays the Pages which have the most number of Page Albums added in them. Motivates Page Admins to add more content on your website.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagealbum.topcreators-sitepagealbum',
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
      'subject' => 'sitepagealbum',
    ),
  ),



    array(
        'title' => 'Page Profile Cover Photo and Information',
        'description' => 'Displays the page cover photo with page profile photo, title and various action links that can be performed on the page from their Profile page (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Page Profile page.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-cover-information-sitepage',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => $showContent_option
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => $showContent_cover,
                    ),
                ), 
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
                        'value' => '300',
                    )
                ),             
            ),
        ),
    ),
)
?>