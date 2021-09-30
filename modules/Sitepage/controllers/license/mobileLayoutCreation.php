<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$db = Engine_Db_Table::getDefaultAdapter();
	$select = new Zend_Db_Select($db);
	$page_id = $select
					->from("engine4_sitemobile_pages", array('page_id'))
					->where('name = ?', "sitepage_index_view")->query()->fetchColumn();

	if(empty($page_id)) 
		return false;

  $engine4_sitepage_mobileadmincontent = $db->query('SHOW TABLES LIKE \'engine4_sitepage_mobileadmincontent\'')->fetch(); 
  if(empty($engine4_sitepage_mobileadmincontent))
    return false;

	$admincontentTable = 'engine4_sitepage_mobileadmincontent';
	$select = new Zend_Db_Select($db);
	$select
					->from($admincontentTable)
					->where('page_id = ?', $page_id);
	$info = $select->query()->fetch();  
	if(empty($info)) {
		// containers
		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
		));
		$container_id = $db->lastInsertId($admincontentTable);

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
		));
		$middle_id = $db->lastInsertId($admincontentTable);

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.closepage-sitepage',
				'parent_content_id' => $middle_id,
				'order' => 1,
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.sitemobile-pagecover-photo-information',
				'parent_content_id' => $middle_id,
				'order' => 2,
				'params' => '{"title":"","titleCount":true,"showContent":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"strachPhoto":"0"}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitemobile.container-tabs-columns',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '{"max":6}',
		));
		$tab_id = $db->lastInsertId($admincontentTable);

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitemobile.sitemobile-advfeed',
				'parent_content_id' => $tab_id,
				'order' => 100,
				'params' => '{"title":"Updates"}',
		));
		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.sitemobile-info-sitepage',
				'parent_content_id' => $tab_id,
				'order' => 200,
				'params' => '{"title":"Info"}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.sitemobile-overview-sitepage',
				'parent_content_id' => $tab_id,
				'order' => 300,
				'params' => '{"title":"Overview","titleCount":true}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.sitemobile-location-sitepage',
				'parent_content_id' => $tab_id,
				'order' => 400,
				'params' => '{"title":"Map","titleCount":true}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'seaocore.sitemobile-people-like',
				'parent_content_id' => $tab_id,
				'order' => 3000,
				'params' => '{"title":"Member Likes","titleCount":true}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'seaocore.sitemobile-followers',
				'parent_content_id' => $tab_id,
				'order' => 3100,
				'params' => '{"title":"Followers","titleCount":true}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.featuredowner-sitepage',
				'parent_content_id' => $tab_id,
				'order' => 3200,
				'params' => '{"title":"Page Admins","titleCount":true}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.favourite-page',
				'parent_content_id' => $tab_id,
				'order' => 3300,
				'params' => '{"title":"Linked Pages","titleCount":true}',
		));

		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.subpage-sitepage',
				'parent_content_id' => $tab_id,
				'order' => 3400,
				'params' => '{"title":"Sub Pages of a Page","titleCount":true}',
		));

		//tab on profile
		$db->insert($admincontentTable, array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitemobile.profile-links',
				'parent_content_id' => $tab_id,
				'order' => 3500,
				'params' => '{"title":"Links","titleCount":true}',
		));
	}
 
?>