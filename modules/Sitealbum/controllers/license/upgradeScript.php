<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upgrade_script.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();


//ADVANCED ALBUMS - ALBUM HOME PAGE
$select = new Zend_Db_Select($db);
$page_id = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitealbum_index_index')
				->limit(1)
				->query()
				->fetchColumn();
if ($page_id) {
  $widgetCount = 0;
  $db->delete('engine4_core_content', array('page_id =?' => $page_id));
  
  $db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Advanced Albums - Album Home Page' WHERE `engine4_core_pages`.`name` ='sitealbum_index_index' LIMIT 1 ;");
  $db->query("UPDATE `engine4_core_pages` SET `title` = 'Advanced Albums - Album Home Page' WHERE `engine4_core_pages`.`name` ='sitealbum_index_index' LIMIT 1 ;");
  
  // containers
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $top_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_id,
      'order' => 6,
      'params' => '',
  ));
  $top_middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  // Top Middle
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.navigation',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  // Middele
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.featured-photos-slideshow',
      'parent_content_id' => $middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Featured Photos Slideshow","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","popularType":"creation","interval":"overall","slideshow_type":"zndp","slideshow_height":"350","slideshow_width":"825","delay":"3500","duration":"750","showCaption":"0","showController":"0","showButtonSlide":"1","showThumbnailInZP":"1","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"1","slidesLimit":"10","captionTruncation":"200","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.featured-photos-slideshow"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-albums-tabs-view',
      'parent_content_id' => $middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Popular Albums","margin_photo":"3","showViewMore":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","photoHeight":"220","photoWidth":"225","columnHeight":"270","loaded_by_ajax":"1","albumInfo":["albumTitle","totalPhotos"],"ajaxTabs":["recentalbums","mostZZZlikedalbums","mostZZZviewedalbums","mostZZZcommentedalbums","featuredalbums","randomalbums","mostZZZratedalbums"],"recentalbums":"5","most_likedalbums":"2","most_viewedalbums":"1","most_commentedalbums":"4","featuredalbums":"3","randomalbums":"6","most_ratedalbums":"7","titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","limit":"9","truncationLocation":"35","albumTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-albums-tabs-view"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-photos-tabs-view',
      'parent_content_id' => $middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Popular Photos","margin_photo":"3","showViewMore":"1","category_id":"0","subcategory_id":null,"hidden_category_id":"0","hidden_subcategory_id":"0","photoHeight":"250","photoWidth":"225","columnHeight":"250","photoInfo":["ownerName","albumTitle"],"ajaxTabs":["mostZZZlikedphotos","mostZZZviewedphotos","mostZZZcommentedphotos","featuredphotos","mostZZZratedphotos"],"recentphotos":"1","most_likedphotos":"2","most_viewedphotos":"1","most_commentedphotos":"4","featuredphotos":"3","randomphotos":"6","most_ratedphotos":"7","limit":"9","truncationLocation":"35","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-photos-tabs-view"}',
  ));
  
  
  // Right Side
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.album-of-the-day',
      'parent_content_id' => $right_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Album of the Day","photoHeight":"255","photoWidth":"237","albumInfo":["ratingStar","totalPhotos"],"truncationLocation":"35","albumTitleTruncation":"100","nomobile":"0","name":"sitealbum.album-of-the-day"}',
  ));

  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.searchbox-sitealbum',
      'parent_content_id' => $right_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Search Albums","titleCount":"","locationDetection":"0","formElements":["textElement"],"categoriesLevel":"","showAllCategories":"0","textWidth":"200","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"sitealbum.searchbox-sitealbum"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-popular-albums',
      'parent_content_id' => $right_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Viewed Albums","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","popularType":"view","interval":"overall","photoHeight":"200","photoWidth":"232","albumInfo":["ownerName","viewCount"],"titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","truncationLocation":"35","albumTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-albums"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-popular-photos',
      'parent_content_id' => $right_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Viewed Photos","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","popularType":"view","interval":"overall","photoHeight":"200","photoWidth":"232","photoInfo":["ownerName","viewCount","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-popular-photos',
      'parent_content_id' => $right_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Liked Photos","itemCountPerPage":"3","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","popularType":"like","interval":"overall","photoHeight":"200","photoWidth":"232","photoInfo":["ownerName","likeCount","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
  ));
}


// ALBUM BROWSE PAGE
$page_id = $db->select()
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'sitealbum_index_browse')
			->limit(1)
			->query()
			->fetchColumn();
if (!$page_id) {

	//$db->delete('engine4_core_content', array('page_id =?' => $page_id));
	
// 	$db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Advanced Albums - Album Browse Page' WHERE `engine4_core_pages`.`name` ='sitealbum_index_browse' LIMIT 1 ;");
// 	$db->query("UPDATE `engine4_core_pages` SET `title` = 'Advanced Albums - Album Browse Page' WHERE `engine4_core_pages`.`name` ='sitealbum_index_browse' LIMIT 1 ;");
	$db->insert('engine4_core_pages', array(
		'name' => "sitealbum_index_browse",
		'displayname' => "Advanced Albums - Album Browse Page",
		'title' => "Advanced Albums - Album Browse Page",
		'description' => 'This is the album browse page.',
		'custom' => 0,
	));
	$page_id = $db->lastInsertId('engine4_core_pages');
	// Insert main
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'main',
			'page_id' => $page_id,
			'order' => 2,
	));
	$main_id = $db->lastInsertId();

	// Insert main-middle
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'middle',
			'page_id' => $page_id,
			'parent_content_id' => $main_id,
			'order' => 2,
	));
	$main_middle_id = $db->lastInsertId();

	// Insert menu
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.navigation',
			'page_id' => $page_id,
			'parent_content_id' => $main_middle_id,
			'order' => 1,
	));

	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.browse-breadcrumb-sitealbum',
			'page_id' => $page_id,
			'parent_content_id' => $main_middle_id,
			'order' => 2,
	));

	// Insert search
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.search-sitealbum',
			'page_id' => $page_id,
			'parent_content_id' => $main_middle_id,
			'order' => 3,
			'params' => '{"title":"","titleCount":true,"viewType":"horizontal","showAllCategories":"1","locationDetection":"0","whatWhereWithinmile":"0","advancedSearch":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
	));
	// Insert search
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.categories-banner-sitealbum',
			'page_id' => $page_id,
			'parent_content_id' => $main_middle_id,
			'order' => 4,
			'params' => '{"title":"","titleCount":true}',
	));

	// Insert content
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.browse-albums-sitealbum',
			'page_id' => $page_id,
			'parent_content_id' => $main_middle_id,
			'order' => 5,
			'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","margin_photo":"5","photoHeight":"240","photoWidth":"208","columnHeight":"310","albumInfo":["ownerName","albumTitle","totalPhotos"],"customParams":"1","orderby":"creation_date","enablePhotoRotation":"0","show_content":"3","truncationLocation":"35","albumTitleTruncation":"50","limit":"24","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}',
	));
}
//END ALBUM BROWSE PAGE


//START LOCATION OR MAP
$select = new Zend_Db_Select($db);
$select
			->from('engine4_core_pages')
			->where('name = ?', "sitealbum_index_map")
			->limit(1);
$info = $select->query()->fetch();

if (empty($info)) {
$containerCount = 0;
$widgetCount = 0;

$db->insert('engine4_core_pages', array(
		'name' => "sitealbum_index_map",
		'displayname' => "Advanced Albums - Browse Albums' Locations",
		'title' => "Browse Albums' Locations",
		'description' => 'This is the album browse locations page.',
		'custom' => 0,
));
$page_id = $db->lastInsertId('engine4_core_pages');

//TOP CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'top',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$top_container_id = $db->lastInsertId();

//MAIN CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'main',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $top_container_id,
		'order' => $containerCount++,
));
$top_middle_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $main_container_id,
		'order' => $containerCount++,
));
$main_middle_id = $db->lastInsertId();

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.navigation',
		'parent_content_id' => $top_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.bylocation-album',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Search","titleCount":true,"photoHeight":"195","photoWidth":"195","albumInfo":["ownerName","viewCount","likeCount","commentCount","location","directionLink","ratingStar","albumTitle","CategoryLink","totalPhotos"],"truncationLocation":"35","showAllCategories":"1","locationDetection":"0","nomobile":"0","name":"sitealbum.bylocation-album"}',
));
}
//END LOCATION OR MAP.


//ALBUM PINBOARD PAGE
$page_id = $db->select()
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', "sitealbum_index_pinboard")
			->limit(1)
			->query()
			->fetchColumn();
$containerCount = 0;
$widgetCount = 0;
if (empty($page_id)) {
//CREATE PAGE
$db->insert('engine4_core_pages', array(
		'name' => "sitealbum_index_pinboard",
		'displayname' => 'Advanced Albums - Browse Albums’ Pinboard View',
		'title' => '',
		'description' => 'This is the browse albums pinboard view page.',
		'custom' => 0,
));
$page_id = $db->lastInsertId();


//TOP CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'top',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$top_container_id = $db->lastInsertId();

//MAIN CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'main',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $top_container_id,
		'order' => $containerCount++,
));
$top_middle_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $main_container_id,
		'order' => $containerCount++,
));
$main_middle_id = $db->lastInsertId();

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.navigation',
		'parent_content_id' => $top_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'seaocore.scroll-top',
		'parent_content_id' => $top_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.search-sitealbum',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"","titleCount":true,"viewType":"horizontal","showAllCategories":"1","locationDetection":"0","whatWhereWithinmile":"1","advancedSearch":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.pinboard-browse',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"","show_buttons":["comment","like","facebook","twitter","pinit"],"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","albumInfo":["ownerName","viewCount","likeCount","commentCount","ratingStar","albumTitle","totalPhotos"],"customParams":"5","userComment":"1","autoload":"1","defaultLoadingImage":"1","itemWidth":"275","withoutStretch":"0","orderby":"view_count","itemCount":"12","truncationLocation":"35","albumTitleTruncation":"100","truncationDescription":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.pinboard-browse"}',
));
}


//START ALBUM MANAGE PAGE
$page_id = $db->select()
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'sitealbum_index_manage')
			->limit(1)
			->query()
			->fetchColumn();
if (!$page_id) {
// 	$db->delete('engine4_core_content', array('page_id =?' => $page_id));
// 	
// 	$db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Advanced Albums - Album Manage Page' WHERE `engine4_core_pages`.`name` ='sitealbum_index_manage' LIMIT 1 ;");
// 	$db->query("UPDATE `engine4_core_pages` SET `title` = 'Advanced Albums - Album Manage Page' WHERE `engine4_core_pages`.`name` ='sitealbum_index_manage' LIMIT 1 ;");
	//CREATE PAGE
	$db->insert('engine4_core_pages', array(
			'name' => "sitealbum_index_manage",
			'displayname' => 'Advanced Albums - Album Manage Page',
			'title' => 'Advanced Albums - Album Manage Page',
			'description' => 'This is the albums manage page.',
			'custom' => 0,
	));
	$page_id = $db->lastInsertId();
	// Insert top
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'top',
			'page_id' => $page_id,
			'order' => 1,
	));
	$top_id = $db->lastInsertId();

	// Insert main
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'main',
			'page_id' => $page_id,
			'order' => 2,
	));
	$main_id = $db->lastInsertId();

	// Insert top-middle
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'middle',
			'page_id' => $page_id,
			'parent_content_id' => $top_id,
	));
	$top_middle_id = $db->lastInsertId();

	// Insert main-middle
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'middle',
			'page_id' => $page_id,
			'parent_content_id' => $main_id,
			'order' => 2,
	));
	$main_middle_id = $db->lastInsertId();

	// Insert main-right
	$db->insert('engine4_core_content', array(
			'type' => 'container',
			'name' => 'right',
			'page_id' => $page_id,
			'parent_content_id' => $main_id,
			'order' => 1,
	));
	$main_right_id = $db->lastInsertId();

	// Insert menu
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.navigation',
			'page_id' => $page_id,
			'parent_content_id' => $top_middle_id,
			'order' => 1,
	));

	// Insert content
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.my-albums-sitealbum',
			'page_id' => $page_id,
			'parent_content_id' => $main_middle_id,
			'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","photoHeight":"195","photoWidth":"195","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","albumTitle","CategoryLink","totalPhotos"],"show_content":"1","limit":"12","truncationLocation":"35","albumTitleTruncation":"70","truncationDescription":"200","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.my-albums-sitealbum"}',
			'order' => 1,
	));

	// Insert search
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.search-sitealbum',
			'page_id' => $page_id,
			'parent_content_id' => $main_right_id,
			'order' => 1,
			'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","whatWhereWithinmile":"0","advancedSearch":"0","locationDetection":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
	));

	// Insert browse menu
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.browse-menu-quick',
			'page_id' => $page_id,
			'parent_content_id' => $main_right_id,
			'order' => 2,
	));

	// Insert search
	$db->insert('engine4_core_content', array(
			'type' => 'widget',
			'name' => 'sitealbum.list-popular-albums',
			'page_id' => $page_id,
			'parent_content_id' => $main_right_id,
			'order' => 3,
			'params' => '{"title":"Popular Albums","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"comment","interval":"overall","photoHeight":"205","photoWidth":"203","albumInfo":"","titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","truncationLocation":"35","albumTitleTruncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-albums"}',
	));
}
//END ALBUM MANAGE PAGE

//START CATEGORIES HOME PAGE
$page_id = $db->select()
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', "sitealbum_index_categories")
			->limit(1)
			->query()
			->fetchColumn();
if (!$page_id) {

$containerCount = 0;
$widgetCount = 0;

$db->insert('engine4_core_pages', array(
		'name' => "sitealbum_index_categories",
		'displayname' => 'Advanced Albums - Categories Home',
		'title' => 'Categories Home',
		'description' => 'This is the categories home page.',
		'custom' => 0,
));
$page_id = $db->lastInsertId();


//TOP CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'top',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$top_container_id = $db->lastInsertId();

//MAIN CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'main',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $top_container_id,
		'order' => $containerCount++,
));
$top_middle_id = $db->lastInsertId();

//LEFT CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'left',
		'page_id' => $page_id,
		'parent_content_id' => $main_container_id,
		'order' => $containerCount++,
));
$left_container_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $main_container_id,
		'order' => $containerCount++,
));
$main_middle_id = $db->lastInsertId();

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.navigation',
		'parent_content_id' => $top_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.categories-navigation',
		'parent_content_id' => $left_container_id,
		'order' => $widgetCount++,
		'params' => '{"viewDisplayHR":"0","title":"","nomobile":"0","name":"sitealbum.categories-navigation"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.list-popular-albums',
		'parent_content_id' => $left_container_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Most Liked Albums","itemCountPerPage":"3","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"like","interval":"overall","photoHeight":"210","photoWidth":"203","albumInfo":["likeCount","albumTitle"],"titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","truncationLocation":"35","albumTitleTruncation":"70","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-albums"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.list-popular-albums',
		'parent_content_id' => $left_container_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Most Rated Albums","itemCountPerPage":"3","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"rating","interval":"overall","photoHeight":"210","photoWidth":"203","albumInfo":["ratingStar","albumTitle"],"titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","truncationLocation":"35","albumTitleTruncation":"70","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-albums"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.list-popular-photos',
		'parent_content_id' => $left_container_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Most Popular Photos","itemCountPerPage":"3","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"1","popularType":"creation","interval":"overall","photoHeight":"191","photoWidth":"200","photoInfo":["ownerName","likeCount","commentCount","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'seaocore.change-my-location',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Select your Location","showSeperateLink":"1","nomobile":"0","name":"seaocore.change-my-location"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.searchbox-sitealbum',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Search","titleCount":"","locationDetection":"1","formElements":["textElement","categoryElement","locationElement","locationmilesSearch"],"categoriesLevel":["category","subcategory"],"showAllCategories":"1","textWidth":"270","locationWidth":"180","locationmilesWidth":"120","categoryWidth":"220","nomobile":"0","name":"sitealbum.searchbox-sitealbum"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.categories-grid-view',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Categories","titleCount":true,"showSubCategoriesCount":"5","showCount":"0","columnWidth":"250","columnHeight":"260","nomobile":"0","name":"sitealbum.categories-grid-view"}',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.list-albums-tabs-view',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '{"title":"Albums","margin_photo":"5","showViewMore":"1","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","photoHeight":"260","photoWidth":"278","columnHeight":"330","albumInfo":["ownerName","albumTitle","totalPhotos"],"ajaxTabs":["recentalbums","mostZZZlikedalbums","mostZZZviewedalbums","mostZZZcommentedalbums","featuredalbums","randomalbums","mostZZZratedalbums"],"recentalbums":"7","most_likedalbums":"2","most_viewedalbums":"5","most_commentedalbums":"3","featuredalbums":"1","randomalbums":"6","most_ratedalbums":"4","titleLink":"<a href=\"\/albums\/browse\">Explore Albums \u00bb<\/a>","limit":"12","truncationLocation":"70","albumTitleTruncation":"70","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-albums-tabs-view"}',
));
}
//END CATEGORIES HOME PAGE

// START ALBUM TAGS PAGE WORK 
$page_id = $db->select()
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', "sitealbum_index_tagscloud")
			->limit(1)
			->query()
			->fetchColumn();
if (empty($page_id)) {

$containerCount = 0;
$widgetCount = 0;

//CREATE PAGE
$db->insert('engine4_core_pages', array(
		'name' => "sitealbum_index_tagscloud",
		'displayname' => 'Advanced Albums - Album Tags',
		'title' => 'Popular Tags',
		'description' => 'This is the album tags page.',
		'custom' => 0,
));
$page_id = $db->lastInsertId();

//TOP CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'top',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$top_container_id = $db->lastInsertId();

//MAIN CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'main',
		'page_id' => $page_id,
		'order' => $containerCount++,
));
$main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $top_container_id,
		'order' => $containerCount++,
));
$top_middle_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
$db->insert('engine4_core_content', array(
		'type' => 'container',
		'name' => 'middle',
		'page_id' => $page_id,
		'parent_content_id' => $main_container_id,
		'order' => $containerCount++,
));
$main_middle_id = $db->lastInsertId();

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.navigation',
		'parent_content_id' => $top_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'seaocore.scroll-top',
		'parent_content_id' => $top_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));

$db->insert('engine4_core_content', array(
		'page_id' => $page_id,
		'type' => 'widget',
		'name' => 'sitealbum.tagcloud-sitealbum',
		'parent_content_id' => $main_middle_id,
		'order' => $widgetCount++,
		'params' => '',
));
}

//Album View Page.
$select = new Zend_Db_Select($db);
$select
				->from('engine4_core_pages')
				->where('name = ?', 'sitealbum_album_view')
				->limit(1);
$page_id = $select->query()->fetchObject()->page_id;
if (!empty($page_id)) {

	$db->delete('engine4_core_content', array('page_id =?' => $page_id));
	
	$db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Advanced Albums - Album View Page' WHERE `engine4_core_pages`.`name` ='sitealbum_album_view' LIMIT 1 ;");
	$db->query("UPDATE `engine4_core_pages` SET `title` = 'Advanced Albums - Album View Page' WHERE `engine4_core_pages`.`name` ='sitealbum_album_view' LIMIT 1 ;");
	
	// containers
	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'main',
			'parent_content_id' => null,
			'order' => 1,
			'params' => '',
	));
	$container_id = $db->lastInsertId('engine4_core_content');

	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'right',
			'parent_content_id' => $container_id,
			'order' => 5,
			'params' => '',
	));
	$right_id = $db->lastInsertId('engine4_core_content');

	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $container_id,
			'order' => 6,
			'params' => '',
	));
	$middle_id = $db->lastInsertId('engine4_core_content');

	// widgets entry
	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'sitealbum.top-content-of-album',
			'parent_content_id' => $middle_id,
			'order' => 3,
			'params' => '{"title":"","titleCount":true,"showInformationOptions":["title","owner","description","location","updateddate","likeButton","categoryLink","tags","editmenus","facebooklikebutton","checkinbutton"],"nomobile":"0","name":"sitealbum.top-content-of-album"}',
	));

	// widgets entry
	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'sitealbum.album-view',
			'parent_content_id' => $middle_id,
			'order' => 4,
			'params' => '{"titleCount":true,"itemCountPerPage":"24","margin_photo":"2","photoHeight":"200","photoWidth":"200","columnHeight":"200","photoInfo":["ownerName","likeCount","commentCount"],"show_content":"2","photoTitleTruncation":"100","title":"","nomobile":"0","name":"sitealbum.album-view"}',
	));

	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'sitealbum.user-ratings',
			'parent_content_id' => $right_id,
			'order' => 6,
			'params' => '{"title":"User Ratings","titleCount":true}',
	));

	$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'sitealbum.specification-sitealbum',
			'parent_content_id' => $right_id,
			'order' => 7,
			'params' => '{"title":"Additional Information","titleCount":true,"name":"sitealbum.specification-sitealbum"}',
	));

	$select = new Zend_Db_Select($db);
	$sitetagcheckinEnabled = $select
				->from('engine4_core_modules')
				->where('name = ?', 'sitetagcheckin')
				->where('enabled = ?', '1')
				->query()
				->fetchObject();
	if (!empty($sitetagcheckinEnabled)) {
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
				'parent_content_id' => $right_id,
				'order' => 8,
				'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
		));
	}
	
	$select = new Zend_Db_Select($db);
	$communityadEnabled = $select
				->from('engine4_core_modules')
				->where('name = ?', 'communityad')
				->where('enabled = ?', '1')
				->query()
				->fetchObject();
	if (!empty($communityadEnabled)) {
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'communityad.ads',
				'parent_content_id' => $right_id,
				'order' => 9,
				'params' => '{"loaded_by_ajax":"0","title":"","name":"communityad.ads","show_type":"all","itemCount":"4","showOnAdboard":"1","packageIds":"","nomobile":"0"}',
		));
	}
}

//ADVANCED ALBUMS - PHOTO VIEW PAGE
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitealbum_photo_view')
        ->limit(1);
$page_id = $select->query()->fetchObject()->page_id;
if ($page_id) {
  $db->delete('engine4_core_content', array('page_id =?' => $page_id));
  
	$db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Advanced Albums - Album Photo View Page' WHERE `engine4_core_pages`.`name` ='sitealbum_photo_view' LIMIT 1 ;");
	$db->query("UPDATE `engine4_core_pages` SET `title` = 'Advanced Albums - Album Photo View Page' WHERE `engine4_core_pages`.`name` ='sitealbum_photo_view' LIMIT 1 ;");
	
  // containers
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  // widgets entry
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.photo-view',
      'parent_content_id' => $middle_id,
      'order' => 3,
      'params' => '{"titleCount":"true","itemCountPerPage":4,"title":""}',
  ));

  	$select = new Zend_Db_Select($db);
	$sitetagcheckinEnabled = $select
				->from('engine4_core_modules')
				->where('name = ?', 'sitetagcheckin')
				->where('enabled = ?', '1')
				->query()
				->fetchObject();
	if (!empty($sitetagcheckinEnabled)) {
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
				'parent_content_id' => $right_id,
				'order' => 4,
				'params' => '{"title":"Add a Location to Your Photos","titleCount":false}',
		));
  }
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-popular-photos',
      'parent_content_id' => $right_id,
      'order' => 5,
      'params' => '{"title":"Popular Photos","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"comment","interval":"overall","photoHeight":"200","photoWidth":"200","photoInfo":["viewCount","likeCount","commentCount","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"22","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitealbum.list-popular-photos',
      'parent_content_id' => $right_id,
      'order' => 6,
      'params' => '{"title":"Recent Photos","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"modified","interval":"overall","photoHeight":"200","photoWidth":"200","photoInfo":["ownerName","viewCount","likeCount","commentCount","photoTitle"],"truncationLocation":"35","photoTitleTruncation":"22","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
  ));
}