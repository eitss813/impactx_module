<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitepagemember_Form_Admin_Global') {

        }
        return true;
    }
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitepage')->hasDirectoryPermissions();
    $page = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.page", "page");
    $pages = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.pages", "pages"); 
    if (isset($_POST['language_phrases_pages']) && $_POST['language_phrases_pages'] != $pages && isset($_POST['language_phrases_page']) && $_POST['language_phrases_page'] != $page && !empty($this->view->hasLanguageDirectoryPermissions)) {
      $language_pharse = array('text_pages' => $_POST['language_phrases_pages'] , 'text_page' => $_POST['language_phrases_page']);
      Engine_Api::_()->getApi('language', 'sitepage')->setTranslateForListType($language_pharse);
    }

//     if (isset($_POST['sitepagemember_group_settings']) && $_POST['sitepagemember_group_settings'] == 1) {
//     
// 			$sitepage_package = Engine_Api::_()->sitepage()->hasPackageEnable();
// 			
// 			if (!(in_array('packagedisable', $_POST['sitepagemember_settings'])) && !empty($sitepage_package)) {
// 			
// 				$menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
// 			
// 				//if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
// 				$menuitemsTable->update(array("params" => '{"route":"sitepage_packages"}'), array('name = ?' => 'sitepage_main_create', 'module = ?' => "sitepage", "menu = ?" => "sitepage_main"));
// 				$menuitemsTable->update(array("params" => '{"route":"sitepage_packages","class":"buttonlink icon_sitepage_new"}'), array('name = ?' => 'sitepage_quick_create', 'module = ?' => "sitepage", "menu = ?" => "sitepage_quick"));
// 
// 
// 				//package is enable, set enable level settings
// 
// 				$level_values["contact_detail"] = array('phone', 'website', 'email');
// 				
// 				$sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
// 				
// 				//START SITEPAGEDOCUMENT PLUGIN WORK
// 				$sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
// 				if ($sitepageDocumentEnabled) {
// 					$level_values["sdcreate"] = 1;
// 					if (!empty($sitepageMemberEnabled)) {
// 						$level_values["auth_sdcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
// 					} else {
// 						$level_values["auth_sdcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
// 					}
// 				}
// 				//END SITEPAGEDOCUMENT PLUGIN WORK
// 				//START SITEPAGEEVENT PLUGIN WORK
// 				$sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
// 				if ($sitepageNoteEnabled) {
// 					$level_values["secreate"] = 1;
// 					if (!empty($sitepageMemberEnabled)) {
// 						$level_values["auth_secreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
// 					} else {
// 						$level_values["auth_secreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
// 					}
// 				}
// 
// 				//END SITEPAGEEVENT PLUGIN WORK
// 				//START SITEPAGEOFFER PLUGIN WORK
// 				$sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
// 				if ($sitepageFormEnabled) {
// 				$level_values["form"] = 1;
// 				}
// 				//END SITEPAGEOFFER PLUGIN WORK
// 				//START SITEPAGEINVITE PLUGIN WORK
// 				$sitepageInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite');
// 				if ($sitepageInviteEnabled) {
// 				$level_values["invite"] = 1;
// 				}
// 
// 				//END SITEPAGEINVITE PLUGIN WORK
// 				//START SITEPAGENOTE PLUGIN WORK
// 				$sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
// 				if ($sitepageNoteEnabled) {
// 					$level_values["sncreate"] = 1;
// 					if (!empty($sitepageMemberEnabled)) {
// 						$level_values["auth_sncreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
// 					} else {
// 						$level_values["auth_sncreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
// 					}
// 				}
// 				//END SITEPAGENOTE PLUGIN WORK
// 				//START SITEPAGEOFFER PLUGIN WORK
// 				$sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
// 				if ($sitepageOfferEnabled) {
// 				$level_values["offer"] = 1;
// 				}
// 				//END SITEPAGEOFFER PLUGIN WORK
// 				//START PHOTO PRIVACY WORK
// 				$sitepageAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
// 				if ($sitepageAlbumEnabled) {
// 					$level_values["spcreate"] = 1;
// 					if (!empty($sitepageMemberEnabled)) {
// 						$level_values["auth_spcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
// 					} else {
// 						$level_values["auth_spcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
// 					}
// 				}
// 				//END PHOTO PRIVACY WORK
// 				//START SITEPAGEPOLL PLUGIN WORK
// 				$sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
// 				if ($sitepagePollEnabled) {
// 					$level_values["splcreate"] = 1;
// 					if (!empty($sitepageMemberEnabled)) {
// 						$level_values["auth_splcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
// 					} else {
// 						$level_values["auth_splcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
// 					}
// 				}
// 				//END SITEPAGEPOLL PLUGIN WORK
// 				//START SITEPAGEVIDEO PLUGIN WORK
// 				$sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
// 				if ($sitepageVideoEnabled) {
// 					$level_values["svcreate"] = 1;
// 					if (!empty($sitepageMemberEnabled)) {
// 						$level_values["auth_svcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
// 					} else {
// 						$level_values["auth_svcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
// 					}
// 				}
// 				//END SITEPAGEVIDEO PLUGIN WORK
// 				
// 				//START SITEPAGEINTEGRATION PLUGIN WORK
// 				$sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
// 				if ($sitepageintegrationEnabled) {
// 							$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' ,'sitepageintegration'
// 				)->getIntegrationItems();
// 					foreach($mixSettingsResults as $modNameValue) {
// 						$level_values[$modNameValue["resource_type"]] = 1;
// 					}
// 				}
// 				//END SITEPAGEINTEGRATION PLUGIN WORK
// 				
// 				$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
// 					foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
// 					if ($level->type != "public") {
// 						$permissionsTable->setAllowed("sitepage_page", $level->level_id, $level_values);
// 					}
// 				}
// 				//}
// 				$db->query("UPDATE `engine4_core_settings` SET `value` = '0' WHERE `engine4_core_settings`.`name` = 'sitepage.package.enable' LIMIT 1 ;");
// 			}
// 
// 			//PACKAGE IS NOT DISABLED AND INSIGHT AND REPORT IS DISABLED.
// 			if (in_array('packagedisable', $_POST['sitepagemember_settings']) && !(in_array('reportinsight', $_POST['sitepagemember_settings']))) {
// 			
// 				$table = Engine_Api::_()->getItemtable('sitepage_package');
// 				$packages_select = $table->getPackagesSql()
// 								->where("enabled = ?", 1)
// 								->order('package_id DESC');
// 				$packages = $table->fetchAll($packages_select);
// 				foreach($packages as $package) {
// 					$db->query("UPDATE `engine4_sitepage_packages` SET `insights` = '0' WHERE `engine4_sitepage_packages`.`package_id` ='".$package->package_id."' LIMIT 1 ;");
// 				}
// 			} else {
// 				$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
// 				
// 				foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
// 					if ($level->type != "public") {
// 						$db->query("UPDATE `engine4_authorization_permissions` SET `value` = '0' WHERE `engine4_authorization_permissions`.`level_id` ='".$level->level_id."' AND `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = 'insight' LIMIT 1 ;");
// 					}
// 				}
// 			}
// 			
// 			$contentTable = Engine_Api::_()->getDbtable('content', 'core');
// 			$contentTableName = $contentTable->info('name');
// 			$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
// 			$pageTableName = $pageTable->info('name');
// 			
// 			//FOR LAYOUT PAGE HOME, BROWSE, PINBOARD.
// 		  if (!(in_array('layoutsetasdemo', $_POST['sitepagemember_settingsforlayout']))) {
// 
// 				$selectPage = $pageTable->select()
// 											->from($pageTableName, array('page_id'))
// 											->where('name =?', 'sitepage_index_home')
// 											->limit(1);
// 				$fetchPageId = $selectPage->query()->fetchAll();
// 				if (!empty($fetchPageId)) {
// 					$db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '". $fetchPageId[0]['page_id']."' AND `engine4_core_content`.`type` = 'widget';");
// 					
// 					$db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '". $fetchPageId[0]['page_id']."' AND `engine4_core_content`.`type` = 'container' AND `engine4_core_content`.`name` = 'right';");
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('page_id =?', $fetchPageId[0]['page_id'])
// 												->where('type =?', 'container')
// 												->where('name =?', 'left')
// 												->limit(1);
// 					$fetchLeftId = $selectPage->query()->fetchAll();
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('page_id =?', $fetchPageId[0]['page_id'])
// 												->where('type =?', 'container')
// 												->where('name =?', 'main')
// 												->limit(1); 
// 					$fetchMainMiddleId = $selectPage->query()->fetchAll();
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('page_id =?', $fetchPageId[0]['page_id'])
// 												->where('type =?', 'container')
// 												->where('name =?', 'top')
// 												->limit(1); 
// 					$fetchTopId = $selectPage->query()->fetchAll();
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('parent_content_id =?', $fetchMainMiddleId[0]['content_id'])
// 												->limit(1); 
// 					$fetchMiddleId = $selectPage->query()->fetchAll();
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('parent_content_id =?', $fetchTopId[0]['content_id'])
// 												->limit(1);
// 					$fetchTopMiddleId = $selectPage->query()->fetchAll();
// 					if (!empty($fetchTopMiddleId)) {
// 						//for top middle placed.
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.browsenevigation-sitepage', $fetchTopMiddleId[0]['content_id'], 25, '{"title":"","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.search-sitepage', $fetchTopMiddleId[0]['content_id'], 26, '{"title":"","titleCount":true,"viewType":"horizontal","nomobile":"0","name":"sitepage.search-sitepage"}');
// 					}
// 					
//           if (!empty($fetchLeftId)) {
// 						//for left side.
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.item-sitepage', $fetchLeftId[0]['content_id'], 10, '{"title":"Page of the day","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.newpage-sitepage', $fetchLeftId[0]['content_id'], 11, '{"title":"","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.mostdiscussion-sitepage', $fetchLeftId[0]['content_id'], 12, '{"title":"Most Discussed Pages","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.mostfollowers-sitepage', $fetchLeftId[0]['content_id'], 13, '{"title":"Most Followed Pages","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepagemember.mostjoined-sitepage', $fetchLeftId[0]['content_id'], 14, '{"title":"Most Joined Pages","titleCount":true}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.mostlikes-sitepage', $fetchLeftId[0]['content_id'], 15, '{"title":"Most Liked Pages","titleCount":"true"}');
// 
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.mostcommented-sitepage', $fetchLeftId[0]['content_id'], 16, '{"title":"Most Commented Pages","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.recentview-sitepage', $fetchLeftId[0]['content_id'], 17, '{"title":"Recently Viewed","titleCount":"true"}');
// 
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.recentfriend-sitepage', $fetchLeftId[0]['content_id'], 18, '{"title":"Recently Viewed By Friends","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.tagcloud-sitepage', $fetchLeftId[0]['content_id'], 19, '{"title":"","titleCount":"true"}');
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('suggestion')) {
// 							$this->widgetPlaced($fetchPageId[0]['page_id'], 'suggestion.common-suggestion', $fetchLeftId[0]['content_id'], 20, '{"title":"Recommended Groups","resource_type":null,"getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}');
// 						}
// 					}
// 					
// 					if (!empty($fetchMiddleId)) {
// 						//for main middle container.
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.zeropage-sitepage', $fetchMiddleId[0]['content_id'], 21, '{"title":"","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.ajax-carousel-sitepage', $fetchMiddleId[0]['content_id'], 22, '{"title":"Sponsored Groups","titleCount":true,"statistics":["followCount","memberCount"],"fea_spo":"sponsored","viewType":"0","blockHeight":"224","blockWidth":"170","popularity":"view_count","featuredIcon":"1","sponsoredIcon":"1","itemCount":"4","interval":"300","category_id":"0","truncation":"50","nomobile":"0","name":"sitepage.ajax-carousel-sitepage"}');
// 
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.categories', $fetchMiddleId[0]['content_id'], 23, '{"title":"Categories","titleCount":true,"showAllCategories":"0","show2ndlevelCategory":"1","show3rdlevelCategory":"0","nomobile":"0","name":"sitepage.categories"}');
// 						
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.slideshow-sitepage', $fetchMiddleId[0]['content_id'], 24, '{"title":"Featured Pages","titleCount":true,"itemCount":"4","category_id":"0","nomobile":"0","name":"sitepage.slideshow-sitepage"}');
// 									
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.category-pages-sitepage', $fetchMiddleId[0]['content_id'], 25, '{"title":"Popular Groups","titleCount":true,"itemCount":"9","pageCount":"3","popularity":"view_count","interval":"overall","columnCount":"3","nomobile":"0","name":"sitepage.category-pages-sitepage"}');
// 												
// 						$this->widgetPlaced($fetchPageId[0]['page_id'], 'sitepage.recently-popular-random-sitepage', $fetchMiddleId[0]['content_id'], 26, '{"title":"","titleCount":"","layouts_views":["1","2","3"],"layouts_oder":"2","layouts_tabs":["1","2","3","4","5","6"],"recent_order":"4","popular_order":"5","random_order":"6","featured_order":"1","sponosred_order":"2","list_limit":"10","grid_limit":"16","columnWidth":"198","columnHeight":"200","statistics":["likeCount","followCount","viewCount","memberCount"],"turncation":"40","showlikebutton":"0","showfeaturedLable":"0","showsponsoredLable":"0","showlocation":"0","showprice":"0","showpostedBy":"0","showdate":"0","category_id":"0","joined_order":"3","nomobile":"0","name":"sitepage.recently-popular-random-sitepage"}');
// 				  }
// 				}
// 			}
// 			
// 			if (!(in_array('layoutbrowsepage', $_POST['sitepagemember_settingsforlayout']))) {
// 			
// 				//FOR BROWSE PAGE WORK.
// 				$db->query("UPDATE `engine4_core_content` SET `order` = '880' WHERE `engine4_core_content`.`name` ='sitepage.popularlocations-sitepage' LIMIT 1 ;");
// 				
// 				$db->query("UPDATE `engine4_core_content` SET `order` = '999' WHERE `engine4_core_content`.`name` ='sitepage.tagcloud-sitepage' LIMIT 1 ;");
// 				
// 				$selectPage = $pageTable->select()
// 											->from($pageTableName, array('page_id'))
// 											->where('name =?', 'sitepage_index_index')
// 											->limit(1);
//         $page_id = $selectPage->query()->fetchAll();
//         
//         $db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"","titleCount":true,"layouts_views":["1","2","3"],"layouts_oder":"2","columnWidth":"195","statistics":["likeCount","followCount","viewCount","memberCount"],"columnHeight":"200","turncation":"40","showlikebutton":"0","showfeaturedLable":"0","showsponsoredLable":"0","showlocation":"0","showprice":"0","showpostedBy":"0","showdate":"0","category_id":"0","nomobile":"0","name":"sitepage.pages-sitepage"}\' WHERE `engine4_core_content`.`name` ="sitepage.pages-sitepage" AND  `engine4_core_content`.`page_id` ="'.$page_id[0]['page_id'].'" LIMIT 1 ;');
//         
// 				$selectPage = $contentTable->select()
// 											->from($contentTableName, array('content_id'))
// 											->where('page_id =?', $page_id[0]['page_id'])
// 											->where('type =?', 'container')
// 											->where('name =?', 'right')
// 											->limit(1);
// 				$fetchRightId = $selectPage->query()->fetchAll();
//         if (!empty($fetchRightId)) {
// 					$this->widgetPlaced($page_id[0]['page_id'], 'sitepagemember.mostjoined-sitepage', $fetchRightId[0]['content_id'], 700, '{"title":"Most Joined Groups","titleCount":true,"itemCount":"3","category_id":"0","featured":"0","sponsored":"0","nomobile":"0","name":"sitepagemember.mostjoined-sitepage"}');
// 
// 					$this->widgetPlaced($page_id[0]['page_id'], 'sitepage.mostfollowers-sitepage', $fetchRightId[0]['content_id'], 750, '{"title":"Most Followed Groups","titleCount":true,"itemCount":"3","category_id":"0","featured":"0","sponsored":"0","interval":"overall","nomobile":"0","name":"sitepage.mostfollowers-sitepage"}');
// 				}
// 			}
// 			
// 			//FOR BROWSE PINBOARD PAGE.
// 			if (!(in_array('sitepagemember_settingsforlayout', $_POST['sitepagemember_settings']))) {
// 				$selectPage = $pageTable->select()
// 										->from($pageTableName, array('page_id'))
// 										->where('name =?', 'sitepage_index_pinboard_browse')
// 										->limit(1);
//         $page_id = $selectPage->query()->fetchAll();
//         if (!empty($page_id)) {
// 					$db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"","titleCount":true,"postedby":"1","showoptions":"viewCount","likeCount","commentCount","price","location"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"274","withoutStretch":"1","itemCount":"12","show_buttons":"comment","like","share","facebook","twitter"],"truncationDescription":"100","nomobile":"0","name":"sitepage.pinboard-browse"}\' WHERE `engine4_core_content`.`name` ="sitepage.pinboard-browse" AND  `engine4_core_content`.`page_id` ="'.$page_id[0]['page_id'].'" LIMIT 1 ;');
//         }
// 			}
// 			
// 		  //FOR PROFILE PAGE LAYOUT.
// 		  $LayoutSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
// 		  if (empty($LayoutSettings) && !(in_array('layoutprofilepage', $_POST['sitepagemember_settingsforlayout']))) {
// 
// 				$select = new Zend_Db_Select($db);
// 				$select_page = $select
// 											->from('engine4_core_pages', 'page_id')
// 											->where('name = ?', 'sitepage_index_view')
// 											->limit(1);
// 				$page = $select_page->query()->fetchAll();
// 				if (!empty($page)) {
// 				
// 					$page_id = $page[0]['page_id'];
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('page_id =?', $page_id)
// 												->where('type =?', 'container')
// 												->where('name =?', 'left')
// 												->limit(1);
// 					$left_Id = $selectPage->query()->fetchAll();
// 					if (!empty($left_Id[0]['content_id'])) {
// 						$db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '". $page_id."' AND `engine4_core_content`.`type` = 'widget' AND `engine4_core_content`.`parent_content_id` = '".$left_Id[0]['content_id']."';");
// 					}
// 					
// 				  $selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('page_id =?', $page_id)
// 												->where('type =?', 'container')
// 												->where('name =?', 'right')
// 												->limit(1);
// 					$right_Id = $selectPage->query()->fetchAll();
// 					if (!empty($right_Id[0]['content_id'])) {
// 						$db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '". $page_id."' AND `engine4_core_content`.`type` = 'widget' AND `engine4_core_content`.`parent_content_id` = '".$right_Id[0]['content_id']."';");
// 					}
// 					
// 					$db->query("UPDATE `engine4_core_content` SET `name` = 'right' WHERE `engine4_core_content`.`name` ='left' AND  `engine4_core_content`.`page_id` ='".$page_id."' LIMIT 1 ;");
// 					
// 					$selectPage = $contentTable->select()
// 												->from($contentTableName, array('content_id'))
// 												->where('page_id =?', $page_id)
// 												->where('type =?', 'container')
// 												->where('name =?', 'right')
// 												->limit(1);
// 					$right_Id = $selectPage->query()->fetchAll();
// 					
// 					$right_Id = $right_Id[0]['content_id'];
// 					
// 					if (!empty($right_Id)) {
// 					
// 					  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepageevent')) {
// 							$this->widgetPlaced($page_id, 'sitepageevent.profile-events', $right_Id, 150, '{"title":"Upcoming Events"}');
// 						}
// 						
//             if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagealbum')) {
// 							$this->widgetPlaced($page_id, 'sitepage.photolike-sitepage', $right_Id, 151, '{"title":"Most Liked Photos","titleCount":""}');
// 						}
// 						
// 						$this->widgetPlaced($page_id, 'sitepage.write-page', $right_Id, 152, '{"title":"","titleCount":true}');
// 						
// 						$this->widgetPlaced($page_id, 'sitepage.information-sitepage', $right_Id, 153, '{"title":"Information","titleCount":"true","showContent":["ownerPhoto","ownerName","modifiedDate","viewCount","likeCount","commentCount","tags","location","price","memberCount","followerCount","categoryName"]}');
// 						
// 						$edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.setting', 0);
// 						if (empty($edit_layout_setting)) {
// 							$this->widgetPlaced($page_id, 'sitepage.widgetlinks-sitepage', $right_Id, 154, '{"title":"","titleCount":true}');
// 						}
// 						
// 						$this->widgetPlaced($page_id, 'sitepage.options-sitepage', $right_Id, 205, '{"title":"","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($page_id, 'sitepage.featuredowner-sitepage', $right_Id, 206, '{"title":"Owners","titleCount":"true"}');
// 						
// 						$this->widgetPlaced($page_id, 'sitepage.socialshare-sitepage', $right_Id, 207, '{"title":"Social Share","titleCount":"true"}');
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagereview')) {
// 							$this->widgetPlaced($page_id, 'sitepagereview.ratings-sitepagereviews', $right_Id, 8, '{"title":"Ratings","titleCount":"true"}');
// 						}
// 						
// 						$this->widgetPlaced($page_id, 'sitepage.favourite-page', $right_Id, 209, '{"title":"Linked Groups","titleCount":true,"itemCount":"3","category_id":"0","featured":"0","sponsored":"0","nomobile":"0","name":"sitepage.favourite-page"}');
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagedocument')) {
// 							$this->widgetPlaced($page_id, 'sitepagedocument.recent-sitepagedocuments', $right_Id, 210, '{"title":"Most Recent Documents"}');
// 						}
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagenote')) {
// 							$this->widgetPlaced($page_id, 'sitepagenote.recent-sitepagenotes', $right_Id, 211, '{"title":"Most Recent Notes","titleCount":true}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagepoll')) {
// 							$this->widgetPlaced($page_id, 'sitepagepoll.vote-sitepagepolls', $right_Id, 212, '{"title":"Most Voted Polls","titleCount":true}');
// 							
// 							$this->widgetPlaced($page_id, 'sitepagepoll.view-sitepagepolls', $right_Id, 213, '{"title":"Most Viewed Polls","titleCount":true,"itemCount":"3","nomobile":"0","name":"sitepagepoll.view-sitepagepolls"}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagevideo')) {
// 						$this->widgetPlaced($page_id, 'sitepagevideo.view-sitepagevideos', $right_Id, 214, '{"title":"Most Viewed Videos","titleCount":true}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagemusic')) {
// 							$this->widgetPlaced($page_id, 'sitepagemusic.like-sitepagemusic', $right_Id, 215, '{"title":"Most Liked Playlists","titleCount":true}');
// 						}
// 							
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagedocument')) {
// 							$this->widgetPlaced($page_id, 'sitepagedocument.like-sitepagedocuments', $right_Id, 216, '{"title":"Most Liked Documents","titleCount":true}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagealbum')) {
// 							$this->widgetPlaced($page_id, 'sitepage.photocomment-sitepage', $right_Id, 217, '{"title":"Most Commented Photos","titleCount":"","itemCount":"4","nomobile":"0","name":"sitepage.photocomment-sitepage"}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagepoll')) {
// 							$this->widgetPlaced($page_id, 'sitepagepoll.comment-sitepagepolls', $right_Id, 	218, '{"title":"Most Commented Polls","titleCount":true,"itemCount":"3","nomobile":"0","name":"sitepagepoll.comment-sitepagepolls"}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagereview')) {
// 							$this->widgetPlaced($page_id, 'sitepagereview.like-sitepagereviews', $right_Id, 219, '{"title":"Most Liked Reviews","titleCount":true,"itemCount":"3","nomobile":"0","name":"sitepagereview.like-sitepagereviews"}');
// 						}
// 						
// 						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitetagcheckin')) {
// 							$this->widgetPlaced($page_id, 'sitetagcheckin.checkinbutton-sitetagcheckin', $right_Id, 220, '{"title":"Check-in here","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}');
// 							
// 							$this->widgetPlaced($page_id, 'sitetagcheckin.checkinuser-sitetagcheckin', $right_Id, 221, '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}');
// 						}
// 						
// 						$this->widgetPlaced($page_id, 'seaocore.layout-width', $right_Id, 222, '{"title":"","layoutWidth":"225","layoutWidthType":"px","nomobile":"0","name":"seaocore.layout-width"}');
// 					}
// 				}
// 		  }
//     }

    $pluginName = 'sitepagemember';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    include APPLICATION_PATH . '/application/modules/Sitepagemember/controllers/license/license1.php';
		$pluginName = Engine_Api::_()->sitepagemember()->isModulesSupport();
		if( !empty($pluginName) ) {
			$this->view->supportingModules = $pluginName;
		}
  }

  public function widgetPlaced($page_id, $widgetname, $parent_content_id, $order, $params) {

  		$table = Engine_Api::_()->getDbtable('content', 'core');
	  $contentTableName = $table->info('name');
		$contentWidget = $table->createRow();
		$contentWidget->page_id = $page_id;
		$contentWidget->type = 'widget';
		$contentWidget->name = $widgetname;
		$contentWidget->parent_content_id = $parent_content_id;
		$contentWidget->order = $order;
		$contentWidget->params = "$params";
		$contentWidget->save();

  }
  
  
  //ACTION FOR FAQ
  public function faqAction() {

    //TABS CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagemember_admin_main', array(), 'sitepagemember_admin_main_faq');
  }
  
  public function readmeAction() {
    
  }
  
  //ACTION FOR CREATE NEW REVIEW PARAMETER
  public function createAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE FORM
    $form = $this->view->form = new Sitepagemember_Form_Admin_Create();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    $this->view->options = array();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        //CHECK ROLES
        $options = (array) $this->_getParam('optionsArray');
        $options = array_filter(array_map('trim', $options));
        $options = array_slice($options, 0, 100);
        $this->view->options = $options;
        if (empty($options) || !is_array($options) || count($options) < 1) {
          return $form->addError('You must add at least one roles.');
        }

				$rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');
				foreach ($options as $option) {
					$row = $rolesTable->createRow();
					$row->page_category_id = $this->_getParam('category_id');
					$row->role_name = $option;
					$row->is_admincreated = 1;
					$row->save();
				}

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }

    $this->renderScript('admin-settings/create.tpl');
  }
  
  //ACTION FOR EDITING THE REVIEW PARAMETER NAME
  public function editAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    if (!($category_id = $this->_getParam('category_id'))) {
      die('No identifier specified');
    }

    //FETCH ROLES ACCORDING TO THIS CATEGORY
    $categoryIdsArray = array();
    $categoryIdsArray[] = $category_id;
    $roleParams = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->rolesParams($categoryIdsArray);

    $this->view->options = array();
    $this->view->totalOptions = 1;

    //GENERATE A FORM
    $form = $this->view->form = new Sitepagemember_Form_Admin_Edit();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($roleParams);

    //CHECK ROLES
    $options = (array) $this->_getParam('optionsArray');
    $options = array_filter(array_map('trim', $options));
    $options = array_slice($options, 0, 100);
    $this->view->options = $options;
    $this->view->totalOptions = Count($options);

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        foreach ($values as $key => $value) {
          if ($key != 'options' && $key != 'dummy_text') {
            $role_id = explode('role_name_', $key);

            if (!empty($role_id)) {
              $role = Engine_Api::_()->getItem('sitepagemember_roles', $role_id[1]);

              if (!empty($role)) {
                $role->role_name = $value;
                $role->save();
              }
            }
          }
        }

        foreach ($options as $index => $option) {
          $row = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->createRow();
          $row->page_category_id = $this->_getParam('category_id');
          $row->role_name = $option;
          $row->is_admincreated = 1;
          $row->save();
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Roles has been edited successfully.')
      ));
    }

    $this->renderScript('admin-settings/edit.tpl');
  }

  //ACTION FOR MANAGE MEMBER CATEGORY.
  public function manageCategoryAction() {

    //TABS CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagemember_admin_main', array(), 'sitepagemember_admin_main_managecategory');
    
    $this->view->manageRoleSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);
    
    $this->view->form = $form = new Sitepagemember_Form_Admin_ManageCategorySettings();
    include APPLICATION_PATH . '/application/modules/Sitepagemember/controllers/license/license2.php';
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
    
      $values = $form->getValues();
      
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        // Okay, save
        foreach ($values as $key => $value) {
          if ($value != '') {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage-category'));
    }

    //GET ROLES TABLE NAME
    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');

    $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitepage');
    $categories = array();
    $category_info = $tableCategory->getCategories();
    foreach ($category_info as $value) {
      $role_params = array();
      $categoryIdsArray = array();
      $categoryIdsArray[] = $value->category_id;
      $getCatRolesParams = $rolesTable->rolesParams($categoryIdsArray);
      foreach ($getCatRolesParams as $roleParam) {
        $role_params[$value->category_id][] = array(
            'cat_role_id' => $roleParam->role_id,
            'role_name' => $roleParam->role_name,
        );
      }

      $categories[] = $category_array = array(
          'category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'role_params' => $role_params,
      );
    }
    $this->view->categories = $categories;
  }

  //ACTION FOR DELETING THE REVIEW PARAMETERS
  public function deleteAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    if (!($category_id = $this->_getParam('category_id'))) {
      die('No identifier specified');
    }

    //GENERATE FORM
    $form = $this->view->form = new Sitepagemember_Form_Admin_Delete();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        if ($value == 1) {
          $role_id = explode('role_name_', $key);
          $role = Engine_Api::_()->getItem('sitepagemember_roles', $role_id[1]);

          Engine_Api::_()->getDbtable('roles', 'sitepagemember')->delete(array('role_id = ?' => $role_id[1], 'is_admincreated =? ' => 1));

          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();

          try {
            $role->delete();
            $db->commit();
          } catch (Exception $e) {
            $db->rollBack();
            throw $e;
          }
        }
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Roles has been deleted successfully.')
      ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }
}