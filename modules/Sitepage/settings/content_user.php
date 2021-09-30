<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$categories_prepared = array();
$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$ads_Array = array();
$social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_preferred_5"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">
var addthis_config = {
          services_compact: "facebook, twitter, linkedin, google, digg, more",
          services_exclude: "print, email"
}
</script>
<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';
$final_array = array(
    array(
        'title' => $view->translate('Page Profile Overview'),
        'description' => $view->translate('Displays rich overview on Page\'s profile, created by you using the editor from Page Dashboard. This should be placed in the Tabbed Blocks area of Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.overview-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Overview'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Sub Pages of a Page'),
        'description' => $view->translate('Displays the sub pages created in the Page which is being viewed currently. This widget should be placed on the Page Profile page.'),
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.subpage-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Sub Pages of a Page'),
            'titleCount' => true,
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
        'title' => $view->translate('Parent Page of a Sub Page'),
        'description' => $view->translate('Displays the parent page in which the currently viewed sub pages is created. This widget should be placed on the Page Profile page.'),
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.parentpage-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Parent Page of a Sub Page'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the page based on the categories. This widget should be placed on the Page Profile page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.page-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
//    array(
//        'title' => $view->translate("Page Profile 'Save to foursquare' Button"),
//        'description' => $view->translate("This Button will enable Page visitors to add the Page's place or tip to their foursquare To-Do List. Note that this feature will be available to you based on your Page's Package and your Member Level."),
//        'category' => $view->translate('Page Profile'),
//        'type' => 'widget',
//        'name' => 'sitepage.foursquare-sitepage',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
		array(
			'title' => $view->translate('Page Profile Social Share Buttons'),
			'description' => $view->translate("Contains Social Sharing buttons and enables users to easily share Pages on their favorite Social Networks. You can personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
			'category' => $view->translate('Page Profile'),
			'type' => 'widget',
			'name' => 'sitepage.socialshare-sitepage',
			'defaultParams' => array(
					'title' => $view->translate('Social Share'),
					'titleCount' => true,
			),
			'requirements' => array(
				'subject' => 'sitepage_page',
			),
      'autoEdit' => true,
			'adminForm' => array(
				'elements' => array(
					array(
							'Textarea',
							'code',
							array(
									'description' => $view->translate("Social Sharing Buttons Code: You can personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
									'value' => $social_share_default_code,
									'decorators' => array('ViewHelper', array('Description', array('placement' => 'PREPEND','escape' => false)))
							),
					),
					array(
							'Hidden',
							'nomobile',
							array(
									'label' => '',
							)
					),
				),
			),
		),
    array(
        'title' => $view->translate('Page Profile Title'),
        'description' => $view->translate('Displays the Title of the Page. This widget should be placed on the Page Profile, in the middle column at the top.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.title-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Info'),
        'description' => $view->translate('This widget forms the Info tab on the Page Profile and displays the information of the Page. It should be placed in the Tabbed Blocks area of the Page Profile. You may enter content for this section from the Edit Info and Profile Info sections of the Page Dashboard.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.info-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Info'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Information Page'),
        'description' => $view->translate('Displays the owner, category, tags, views and other information about a Page. This widget should be placed on the Page Profile in the left column.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.information-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Information'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Photo'),
        'description' => $view->translate('Displays the main cover photo of a Page. This widget must be placed on the Page Profile at the top of left column.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.mainphoto-sitepage',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Map'),
        'description' => $view->translate('This widget forms the Map tab on the Page Profile. It displays the map showing the Page position as well as the location details of the page. It should be placed in the Tabbed Blocks area of the Page Profile. Location details can be entered from the Location section of the Dashboard. Note that this feature will be available to you based on your Page\'s Package and your Member Level.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.location-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Options'),
        'description' => $view->translate('Displays the various action link options to users viewing your Page. This widget should be placed on the Page Profile in the left column, below the Page profile photo.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.options-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Owner Page Tags'),
        'description' => $view->translate('Displays all the tags chosen by the owner of your Page for his Pages. This widget should be placed on the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.tags-sitepage',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Owner Pages'),
        'description' => $view->translate('Displays other Pages owned by the owner of your Page. This widget should be placed on the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.userpage-sitepage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile About Page'),
        'description' => $view->translate('Displays the About Us information for your Page. You can enter information for this widget simply by clicking on the widget.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.write-page',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
			'title' => $view->translate('Content Profile: Follow Button'),
			'description' => $view->translate('This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.'),
			'category' => $view->translate('Page Profile'),
			'type' => 'widget',
			'name' => 'seaocore.seaocore-follow',
			'defaultParams' => array(
					'title' => '',
			),
    ),
    array(
        'title' => $view->translate('Content Profile: Like Button for Content'),
        'description' => $view->translate('This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Page Profile You May Also Like'),
        'description' => $view->translate('Displays the other Pages that a user may like, based on your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.suggestedpage-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('You May Also Like'),
            'titleCount' => true,
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
        'title' => $view->translate('Content Profile: Content Likes'),
        'description' => $view->translate('Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'seaocore.people-like',
    ),
    array(
        'title' => $view->translate('Page Profile Page Insights'),
        'description' => $view->translate('Displays the insights of your Page to your Page Admins only. These insights include metrics like views, likes, comments and active users of the Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.insights-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Insights'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Apps Links'),
        'description' => $view->translate("Displays the Apps related links for your Page. This widget should be placed in the left column."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.widgetlinks-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
		array(
				'title' => $this->view->translate('Page Profile Linked Pages'),
				'description' => $this->view->translate('Displays Linked Pages of your Page.'),
				'category' => $this->view->translate('Page Profile'),
				'type' => 'widget',
				'name' => 'sitepage.favourite-page',
				'defaultParams' => array(
								'title' => $this->view->translate('Linked Pages'),
								'titleCount' => true,
				),
    ),
    array(
        'title' => $view->translate('Page Profile Featured Page Admins'),
        'description' => $view->translate("Displays the Featured Admins of your Page."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.featuredowner-sitepage',
        'defaultParams' => array(
            'title' => $view->translate("Page admins"),
            'titleCount' => "",
        ),
    ), 
    array(
        'title' => $view->translate('Page Profile Alternate Thumb Photo'),
        'description' => $view->translate('Displays the thumb photo of a Page. This works as an alternate profile photo when you have set the layout of Page Profile to be tabbed, from the Page Layout Settings, and have integrated with the "Advertisements / Community Ads Plugin" by SocialEngineAddOns. In that case, the left column of the Page Profile having the main profile photo gets hidden to accomodate Ads. This widget must be placed on the Page Profile at the top of middle column.'),
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
                        'label' => $view->translate('Show Page Profile Title.'),
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
        'title' => $view->translate('Page Profile Contact Details'),
        'description' => $view->translate("Displays the Contact Details of your Page."),
        'category' => $view->translate('Page Profile'),
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
                        'label' => $view->translate('Select the contact details you want to display'),
                        'multiOptions' => array("1" => "Phone", "2" => "Email", "3" => "Website"),
                    ),
                ),
                array(
									'Radio',
									'emailme',
									array(
											'label' => $view->translate('Do you want users to send emails to Pages via a customized pop up when they click on "Email Me" link?'),
											'multiOptions' => array(
													1 => $view->translate('Yes, open customized pop up'),
													0 => $view->translate('No, open browser`s default pop up')
											),
											'value' => '0'
									)
                ),
            ),
        ),
    ), 
);
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => $view->translate('Page Profile Alternate Cover Thumb Photo'),
          'description' => $view->translate('Displays the thumb photo of your Page. This widget must be placed on the Page Profile at the top of middle column. This photo shows up only in special cases.'),
          'category' => $view->translate('Page Profile'),
          'type' => 'widget',
          'name' => 'sitepage.thumbphoto-sitepage',
          'defaultParams' => '',
      ),
  );
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}

$fbpage_sitepage_Array = array(
      array(
          'title' => $view->translate('Facebook Like Box'),
          'description' => $view->translate('This widget contains the Facebook Like Box which enables Page Admins to gain Likes for their Facebook Page from this website. The edit popup contains the settings to customize the Facebook Like Box. This widget should be placed on the Page Profile.'),
          'category' => $view->translate('Page Profile'),
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
                        'label' => $view->translate('Title'),
                        'value' => '',
                    )
                ),
                
                 array(
                    "Text",
                    "fb_width",
                    array(
                        'label' => $view->translate('Width'),
                        'description' => $view->translate('Width of the Facebook Like Box in pixels.'),
                        'value' => '220',
                    )
                ),
              array(
                    "Text",
                    "fb_height",
                    array(
                        'label' => $view->translate('Height'),
                        'description' => $view->translate('Height of the Facebook Like Box in pixels (optional).'),
                        'value' => '588',
                    )
                ),
                array(
                    "Select",
                    "widget_color_scheme",
                    array(
                        'label' => $view->translate('Color Scheme'),
                        'description' => $view->translate('Color scheme of the Facebook Like Box in pixels.'),
                       'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                    )
                ),
                array(
                    "MultiCheckbox",
                    "widget_show_faces",
                    array(
                        //'label' => 'Show Profile Photos in this plugin.',
                        'description' => $view->translate('Show Faces'),
                        'multiOptions' => array('1' => $view->translate('Show profile photos of users who like the linked Facebook Page in the Facebook Like Box.'))
                         
                    )
                ),
                
                array(
                    "Text",
                    "widget_border_color",
                    array(
                        'label' => $view->translate('Border Color'),
                        'description' => $view->translate('The border color of the plugin')
                       
                         
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_stream",
                    array(
                        
                        'description' => $view->translate('Stream'),
                        'multiOptions' => array('1' => $view->translate('Show the Facebook Page profile stream for the public feeds in the Facebook Like Box.')),
                       
                        
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_header",
                    array(
                        
                        'description' => $view->translate('Header'),
                        'multiOptions' => array('1' => $view->translate("Show the 'Find us on Facebook' bar at top. Only shown when either stream or profile photos are present.")),
                    )
                ),
            )
        )
          
   ));
   
   if (!empty($fbpage_sitepage_Array)) {
  $final_array = array_merge($final_array, $fbpage_sitepage_Array);
}
return $final_array;
?>