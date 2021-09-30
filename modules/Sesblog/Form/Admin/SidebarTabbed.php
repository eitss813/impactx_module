<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: SidebarTabbed.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_SidebarTabbed extends Engine_Form {

  public function init() {
  
    $this->addElement('Select', "enableTabs", array(
      'label' => "Choose the View Type for Blogs.",
      'multiOptions' => array(
      'list' => 'List View',
      'grid' => 'Grid View',
      ),
			'value'=>'list'
    ));
    $this->addElement('MultiCheckbox', "show_criteria", array(
      'label' => "Choose from below the details that you want to show in this widget.",
      'multiOptions' => array(
				'featuredLabel' => 'Featured Label  for Grid View',
				'sponsoredLabel' => 'Sponsored Label  for Grid View',
				'verifiedLabel' => 'Verified Label  for Grid View',
				'favouriteButton' => 'Favourite Button  for Grid View',
				'location'=>'Location  for Grid View',
				'likeButton' => 'Like Button  for Grid View',
				'socialSharing' => 'Social Share Buttons <a class="smoothbox" href="admin/sesbasic/settings/faqwidget">[FAQ]</a>  for Grid View',
				'like' => 'Like Counts  for Grid View',
				'favourite' => 'Favourite Counts for Grid View',
				'comment' => 'Comment Counts for Grid View',
				'rating' => 'Rating Starts for Grid View',
				'view' => 'View Counts for Grid View',
				'title' => 'Blog Titles',
				'category' => 'Category for Grid View',
				'by' => 'Item Owner Name for Grid View',
				'creationDate' => 'Creation Date',
      ),
      'escape' => false,
    ));

    //Social Share Plugin work
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sessocialshare')) {
      
      $this->addElement('Select', "socialshare_enable_plusicon", array(
        'label' => "Enable More Icon for social share buttons?",
          'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'value' => 1,
      ));
      
      $this->addElement('Text', "socialshare_icon_limit", array(
          'label' => 'Count (number of social sites to show). If you enable More Icon, then other social sharing icons will display on clicking this plus icon.',
          'value' => 2,
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          )
      ));
    }
    //Social Share Plugin work
    $this->addElement('Text', "title_truncation_grid", array(
      'label' => 'Title truncation limit for Grid Views.',
      'value' => 45,
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
      )
    ));
    $this->addElement('Text', "title_truncation_list", array(
      'label' => 'Title truncation limit for List Views.',
      'value' => 45,
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
      )
    ));
    
    $this->addElement('Text', "limit_data_list", array(
      'label' => 'Count for List Views (number of blogs to show).',
      'value' => 10,
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
      )
    ));
    $this->addElement('Text', "limit_data_grid", array(
      'label' => 'Count for Grid Views (number of blogs to show).',
      'value' => 10,
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
      )
    ));
   
    $this->addElement('Text', "height_grid", array(
      'label' => 'Enter the height of one block in Grid Views (in pixels).',
      'value' => '270',
    ));
    $this->addElement('Text', "width_grid", array(
      'label' => 'Enter the width of one block in Grid Views (in pixels).',
      'value' => '389',
    ));
    $this->addElement('Text', "height_list", array(
      'label' => 'Enter the height of main photo block in List Views (in pixels).',
      'value' => '230',
    ));
    $this->addElement('Text', "width_list", array(
      'label' => 'Enter the width of main photo block in List Views (in pixels).',
      'value' => '260',
    ));
    
		$this->addElement('MultiCheckbox', "search_type", array(
			'label' => "Choose from below the Tabs that you want to show in this widget.",
			'multiOptions' => array(
				'recentlySPcreated' => 'Recently Created',
				'mostSPviewed' => 'Most Viewed',
				'mostSPliked' => 'Most Liked',
				'mostSPcommented' => 'Most Commented',
				'mostSPrated' => 'Most Rated',
				'mostSPfavourite' => 'Most Favourite',
				'week'=>'This Week',
				'month'=>'This Month',
				'featured' => 'Featured',
				'sponsored' => 'Sponsored',
				'verified' => 'Verified',
			),
		));
  	  // setting for Recently Created
		$this->addElement('Dummy', "dummy1", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Recently Created' Tab</span>",
    ));
		$this->getElement('dummy1')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

   		$this->addElement('Text', "recentlySPcreated_order", array(
			'label' => "Order of this Tab.",
  			'value' => '1',
    ));
  		$this->addElement('Text', "recentlySPcreated_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Recently Created',
    ));

 	   // setting for Most Viewed
		$this->addElement('Dummy', "dummy2", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Viewed' Tab</span>",
    ));
		$this->getElement('dummy2')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		$this->addElement('Text', "mostSPviewed_order", array(
			'label' => "Order of this Tab.",
			'value' => '2',
    ));
		$this->addElement('Text', "mostSPviewed_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Most Viewed',
    ));

  	  // setting for Most Liked
		$this->addElement('Dummy', "dummy3", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Liked' Tab</span>",
    ));
		$this->getElement('dummy3')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		$this->addElement('Text', "mostSPliked_order", array(
			'label' => "Order of this Tab.",
			'value' => '3',
    ));
		$this->addElement('Text', "mostSPliked_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Most Liked',
    ));

 	   // setting for Most Commented
		$this->addElement('Dummy', "dummy4", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Commented' Tab</span>",
    ));
		$this->getElement('dummy4')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

  		$this->addElement('Text', "mostSPcommented_order", array(
			'label' => "Order of this Tab.",
			'value' => '4',
    ));
		$this->addElement('Text', "mostSPcommented_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Most Commented',
    ));

  	  // setting for Most Rated
		$this->addElement('Dummy', "dummy5", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Rated' Tab</span>",
    ));
		$this->getElement('dummy5')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		$this->addElement('Text', "mostSPrated_order", array(
			'label' => "Order of this Tab.",
			'value' => '5',
    ));
		$this->addElement('Text', "mostSPrated_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Most Rated',
    ));

   	 // setting for Most Favourite
		$this->addElement('Dummy', "dummy6", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Favourite' Tab</span>",
    ));
		$this->getElement('dummy6')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
		
		$this->addElement('Text', "mostSPfavourite_order", array(
			'label' => "Order of this Tab.",
			'value' => '6',
    ));
		    $this->addElement('Text', "mostSPfavourite_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Most Favourite',
    ));

  	  // setting for Featured
		$this->addElement('Dummy', "dummy7", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Featured' Tab</span>",
    ));
		$this->getElement('dummy7')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		$this->addElement('Text', "featured_order", array(
			'label' => "Order of this Tab.",
			'value' => '7',
    ));
		$this->addElement('Text', "featured_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Featured',
    ));

   	 // setting for Sponsored
		$this->addElement('Dummy', "dummy8", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Sponsored' Tab</span>",
    ));
		$this->getElement('dummy8')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		$this->addElement('Text', "sponsored_order", array(
			'label' => "Order of this Tab.",
			'value' => '8',
    ));
		$this->addElement('Text', "sponsored_label", array(
     			'label' => 'Title of this Tab.',
			'value' => 'Sponsored',
    ));

	    // setting for Verified
		$this->addElement('Dummy', "dummy9", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'Most Verified' Tab</span>",
    ));
		$this->getElement('dummy9')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	
		$this->addElement('Text', "verified_order", array(
			'label' => "Order of this Tab.",
			'value' => '9',
    ));
		$this->addElement('Text', "verified_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'Verified',
    ));

	  // setting for This Week
		$this->addElement('Dummy', "dummy10", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'This Week' Tab</span>",
    ));
		$this->getElement('dummy10')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	
		$this->addElement('Text', "week_order", array(
			'label' => "Order of this Tab.",
			'value' => '10',
    ));
		$this->addElement('Text', "week_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'This Week',
    ));

	  // setting for This Month
		$this->addElement('Dummy', "dummy11", array(
			'label' => "<span style='font-weight:bold;'>Order and Title of 'This Month' Tab</span>",
    ));
		$this->getElement('dummy11')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
	
		$this->addElement('Text', "month_order", array(
			'label' => 'Order of this Tab.',
			'value' => '11',
    ));
		$this->addElement('Text', "month_label", array(
			'label' => 'Title of this Tab.',
			'value' => 'This Month',
    ));
  }

}
