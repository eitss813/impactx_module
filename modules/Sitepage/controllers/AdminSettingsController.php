<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminSettingsController extends Core_Controller_Action_Admin {

  protected $_ISFORMVALID = false;

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {

    $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitepage')->hasDirectoryPermissions();
    
    $oldLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.city', "World");
    $page = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.page", "page");
    $pages = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.pages", "pages");    $previousLocationFieldSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);

    $redirectionPrevious = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.redirection', 'home');        

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_settings');
    $this->view->form = $form = new Sitepage_Form_Admin_Global();
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main_settings', array(), 'sitepage_admin_main_general');
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      if ($values['sitepage_manifestUrlP'] == 'stores' || $values['sitepage_manifestUrlP'] == 'Stores') {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter the different words for page URL in place of "pageitems" respectively.'));
        return;
      }
      if ($values['sitepage_manifestUrlP'] == $values['sitepage_manifestUrlS']) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter two different words for page URLs in place of "pageitems" and "pageitem" respectively.'));
        return;
      }
      unset($values['sitepage_currency']);
      $sitepage_error = 0;
      if ($values['sitepage_page'] == 0) {
        $sitepage_error = 'Filled value can not be zero !';
      } elseif ($values['sitepage_title_truncation'] == 0) {
        $sitepage_error = 'Filled value can not be zero !';
      } elseif ($values['sitepage_showmore'] == 0) {
        $sitepage_error = 'Filled value can not be zero !';
      }
      $form->addNotice('Your changes have been saved.');
    }
    if (empty($sitepage_error)) {
      foreach ($values as $key => $value) {
      if (isset($values['sitepage_claimlink']) && $values['sitepage_claimlink']) {
        $menu_table = Engine_Api::_()->getDbtable('menuitems', 'core');

        if ($key == 'sitepage_claim_show_menu') {
        if ($value == 1) {
          $menu_table->update(array('menu' => 'core_footer', 'params' => '{"route":"sitepage_claimpages"}'), array('name =?' => 'sitepage_main_claim'));
        } else if ($value == 2) {
          $menu_table->update(array('menu' => 'sitepage_main', 'params' => '{"route":"sitepage_claimpages"}'), array('name =?' => 'sitepage_main_claim'));
        } else if (empty($value)) {
          $menu_table->update(array('menu' => '', 'params' => ''), array('name =?' => 'sitepage_main_claim'));
        }
        }
      }

      if ($key == 'sitepage_showmore') {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $params = '{"' . 'max' . '":' . '"' . $value . '"' . '}';
        if ($_POST['sitepage_layoutcreate'] == 1) {
          $table = Engine_Api::_()->getDbtable('content', 'sitepage');
          $pageArray = $db->query('SELECT `contentpage_id` FROM `engine4_sitepage_contentpages` WHERE `name` LIKE "sitepage_index_view"')->fetchAll();
          if($pageArray) {
            foreach($pageArray as $tempPageArray) {
              $page_id = !empty($tempPageArray) ? $tempPageArray['contentpage_id'] : null;
              $table->update(array('params' => $params), array('name = ?' => 'core.container-tabs', 'contentpage_id =?' => $page_id));
            }
          } else {
            $table = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
            $pageArray = $db->query('SELECT `page_id` FROM `engine4_core_pages` WHERE `name` LIKE "sitepage_index_view" LIMIT 1')->fetch();
            $page_id = !empty($pageArray) ? $pageArray['page_id'] : null;
            $table->update(array('params' => $params), array('name = ?' => 'core.container-tabs', 'page_id =?' => $page_id));
          }
        } else {
          $pageArray = $db->query('SELECT `page_id` FROM `engine4_core_pages` WHERE `name` LIKE "sitepage_index_view" LIMIT 1')->fetch();
          $page_id = !empty($pageArray) ? $pageArray['page_id'] : null;
          $table = Engine_Api::_()->getDbtable('content', 'core');
          $table->update(array('params' => $params), array('name = ?' => 'core.container-tabs', 'page_id =?' => $page_id));
        }
      }

      $menu_table = Engine_Api::_()->getDbtable('menuitems', 'core');
      if ($key == 'sitepage_show_menu') {
        if ($value == 3) {
        $menu_table->update(array('menu' => 'core_main', 'plugin' => '', 'params' => '{"route":"sitepage_general","action":"home"}'), array('name =?' => 'core_main_sitepage'));
        } elseif ($value == 2) {
        $menu_table->update(array('menu' => 'core_mini', 'plugin' => '', 'params' => '{"route":"sitepage_general","action":"home"}'), array('name =?' => 'core_main_sitepage'));
        } else if ($value == 1) {
        $menu_table->update(array('menu' => 'core_footer', 'plugin' => '', 'params' => '{"route":"sitepage_general","action":"home"}'), array('name =?' => 'core_main_sitepage'));
        } else if (empty($value)) {
        $menu_table->update(array('menu' => 'user_home', 'plugin' => 'Sitepage_Plugin_Menus', 'params' => '{"route":"sitepage_general","action":"home"}'), array('name =?' => 'core_main_sitepage'));
        }
      }
      if($key == 'sitepage_publish_facebook' && !empty($value)) {
       $value = serialize($value);
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $menu_table = Engine_Api::_()->getDbtable('menuitems', 'core');
      $menuitemsTable = Engine_Api::_()->getDbTable('menuitems', 'core');
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $menuitemsTable->update(array('params' => '{"route":"sitepage_packages"}'), array('name = ?' => 'sitepage_main_create', 'module = ?' => 'sitepage', 'menu = ?' => 'sitepage_main'));
      $menuitemsTable->update(array('params' => '{"route":"sitepage_packages","class":"buttonlink icon_sitepage_new"}'), array('name = ?' => 'sitepage_quick_create', 'module = ?' => 'sitepage', 'menu = ?' => 'sitepage_quick'));


        //package is enable, set enable level settings

      $level_values['contact_detail'] = array('phone', 'website', 'email');

      $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');

        //START SITEPAGEDOCUMENT PLUGIN WORK
      $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
      if ($sitepageDocumentEnabled) {
        $level_values['sdcreate'] = 1;
        if (!empty($sitepageMemberEnabled)) {
          $level_values['auth_sdcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'member', 'like_member', 'owner');
        } else {
          $level_values['auth_sdcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'like_member', 'owner');
        }
      }
        //END SITEPAGEDOCUMENT PLUGIN WORK
        //START SITEPAGEEVENT PLUGIN WORK
      $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
      if ($sitepageNoteEnabled) {
        $level_values['secreate'] = 1;
        if (!empty($sitepageMemberEnabled)) {
          $level_values['auth_secreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'member', 'like_member', 'owner');
        } else {
          $level_values['auth_secreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'like_member', 'owner');
        }
      }

        //END SITEPAGEEVENT PLUGIN WORK
        //START SITEPAGEOFFER PLUGIN WORK
      $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
      if ($sitepageFormEnabled) {
        $level_values['form'] = 1;
      }
        //END SITEPAGEOFFER PLUGIN WORK
        //START SITEPAGEINVITE PLUGIN WORK
      $sitepageInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite');
      if ($sitepageInviteEnabled) {
        $level_values['invite'] = 1;
      }

        //END SITEPAGEINVITE PLUGIN WORK
        //START SITEPAGENOTE PLUGIN WORK
      $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
      if ($sitepageNoteEnabled) {
        $level_values['sncreate'] = 1;
        if (!empty($sitepageMemberEnabled)) {
          $level_values['auth_sncreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'member', 'like_member', 'owner');
        } else {
          $level_values['auth_sncreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'like_member', 'owner');
        }
      }
        //END SITEPAGENOTE PLUGIN WORK
        //START SITEPAGEOFFER PLUGIN WORK
      $sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
      if ($sitepageOfferEnabled) {
        $level_values['offer'] = 1;
      }
        //END SITEPAGEOFFER PLUGIN WORK
        //START PHOTO PRIVACY WORK
      $sitepageAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
      if ($sitepageAlbumEnabled) {
        $level_values['spcreate'] = 1;
        if (!empty($sitepageMemberEnabled)) {
          $level_values['auth_spcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'member', 'like_member', 'owner');
        } else {
          $level_values['auth_spcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'like_member', 'owner');
        }
      }
        //END PHOTO PRIVACY WORK
        //START SITEPAGEPOLL PLUGIN WORK
      $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
      if ($sitepagePollEnabled) {
        $level_values['splcreate'] = 1;
        if (!empty($sitepageMemberEnabled)) {
          $level_values['auth_splcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'member', 'like_member', 'owner');
        } else {
          $level_values['auth_splcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'like_member', 'owner');
        }
      }
        //END SITEPAGEPOLL PLUGIN WORK
        //START SITEPAGEVIDEO PLUGIN WORK
      $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
      if ($sitepageVideoEnabled) {
        $level_values['svcreate'] = 1;
        if (!empty($sitepageMemberEnabled)) {
          $level_values['auth_svcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'member', 'like_member', 'owner');
        } else {
          $level_values['auth_svcreate'] = array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'like_member', 'owner');
        }
      }
        //END SITEPAGEVIDEO PLUGIN WORK

        //START SITEPAGEINTEGRATION PLUGIN WORK
      $sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
      if ($sitepageintegrationEnabled) {
        $mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' ,'sitepageintegration'
          )->getIntegrationItems();
        foreach($mixSettingsResults as $modNameValue) {
          $level_values[$modNameValue['resource_type']] = 1;
        }
      }
        //END SITEPAGEINTEGRATION PLUGIN WORK

      $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
      foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {

        if ($level->type != 'public') {
          $permissionsTable->setAllowed('sitepage_page', $level->level_id, $level_values);
        }
      }
    } else {
      $menuitemsTable->update(array('params' => '{"route":"sitepage_general","action":"create"}'), array('name = ?' => 'sitepage_main_create', 'module = ? ' => 'sitepage', 'menu = ?' => 'sitepage_main'));
      $menuitemsTable->update(array('params' => '{"route":"sitepage_general","action":"create","class":"buttonlink icon_sitepage_new"}'), array('name = ?' => 'sitepage_quick_create', 'module = ?' => 'sitepage', 'menu = ?' => 'sitepage_quick'));
    }
    }
    if ($this->getRequest()->isPost()) {

      if(!empty($_POST['sitepage_package_information'])) {
        if(Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitepage_package_information')) {
          Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitepage_package_information');
        }
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.package.information', $_POST['sitepage_package_information']);
      }
      
      if(isset($_POST['sitepage_locationfield']) && $previousLocationFieldSetting != $_POST['sitepage_locationfield']) {
        if (isset($_POST['sitepage_locationfield'] ) && $_POST['sitepage_locationfield'] == '0') {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_location' LIMIT 1 ;");
        } else {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_location' LIMIT 1 ;");
        }
      }


      if (isset($_POST['language_phrases_pages']) && $_POST['language_phrases_pages'] != $pages && isset($_POST['language_phrases_page']) && $_POST['language_phrases_page'] != $page && !empty($hasLanguageDirectoryPermissions)) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

				//Work for raplace facebook plugin.
        if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'facebooksefeed' ) && Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'facebookse' )) {
         $facebookseMixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');

         $select = $facebookseMixsettingstable->select()->from($facebookseMixsettingstable->info('name'), array('streampublish_message', 'streampublish_caption', 'streampublish_action_link_text', 'activityfeedtype_text', 'resource_type', 'module'))
         ->where('module LIKE ?', '%' . 'sitepage' . '%');
         $results = $select->query()->fetchAll();
         $replaceWord = ucfirst($_POST['language_phrases_page']);
         $orignal_word = ucfirst($page);
         foreach($results as $result) {

          $streampublish_message = str_replace(" $orignal_word", " $replaceWord", $result["streampublish_message"]);

          $streampublish_caption = str_replace(" $orignal_word", " $replaceWord", $result["streampublish_caption"]);

          $streampublish_action_link_text = str_replace(" $orignal_word", " $replaceWord", $result["streampublish_action_link_text"]);

          $activityfeedtype_text = str_replace(" $orignal_word", " $replaceWord", $result["activityfeedtype_text"]);

          $db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `streampublish_message` =  \''.$streampublish_message.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');

          $db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `streampublish_caption` =  \''.$streampublish_caption.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');

          $db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `streampublish_action_link_text` =  \''.$streampublish_action_link_text.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');

          $db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `activityfeedtype_text` =  \''.$activityfeedtype_text.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');
        }
      }

      $db->query('UPDATE  `engine4_core_menuitems` SET  `label` =  \''.ucfirst($_POST['language_phrases_pages']).'\' WHERE  `engine4_core_menuitems`.`name` ="core_main_sitepage";');


      $language_pharse = array('text_pages' => '$plural$' , 'text_page' => '$singular$');

      Engine_Api::_()->getApi('language', 'sitepage')->setTranslateForListType($language_pharse, '', $page, $pages);

      $language_pharse = array('text_pages' => $_POST['language_phrases_pages'] , 'text_page' => $_POST['language_phrases_page']);

      Engine_Api::_()->getApi('language', 'sitepage')->setTranslateForListType($language_pharse, '', '$singular$', '$plural$');
    }

    $redirectionNew = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.redirection', 'home');
    if($redirectionPrevious != $redirectionNew) {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $db->update('engine4_core_menuitems', array('params' => '{"route":"sitepage_general","action":"'.$redirectionNew.'"}'), array('name = ?' => 'core_main_sitepage'));
    }
  }
    //include_once APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/license1.php';
  $newLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.city', "World");
  $this->setDefaultMapCenterPoint($oldLocation, $newLocation);
  $this->view->isModsSupport = Engine_Api::_()->getApi('suggestion', 'sitepage')->isModulesSupport();
}

 
  //ACTION FOR GETTING THE CATGEORIES AND SUBCATEGORIES
public function sitepagecategoriesAction() {

    //GET NAVIGATION
  $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_sitepagecategories');

    //GET TASK
  if (isset($_POST['task'])) {
    $task = $_POST['task'];
  } elseif (isset($_GET['task'])) {
    $task = $_GET['task'];
  } else {
    $task = "main";
  }

    //GET STORAGE API

    $this->view->storage = Engine_Api::_()->storage();
    
    //GET CATEGORIES TABLE
  $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitepage');

    //GET CATEGORIES TABLE NAME
  $tableCategoriesName = $tableCategories->info('name');

    //GET PAGE TABLE
  $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');

  if ($task == "savecat") {
      //GET CATEGORY ID
    $cat_id = $_GET['cat_id'];

    $cat_title_withoutparse = $_GET['cat_title'];

      //GET CATEGORY TITLE
    $cat_title = Engine_Api::_()->sitepage()->parseString($_GET['cat_title']);

      //GET CATEGORY DEPENDANCY
    $cat_dependency = $_GET['cat_dependency'];
    $subcat_dependency = $_GET['subcat_dependency'];
    if ($cat_title == "") {
      if ($cat_id != "new") {
        if ($cat_dependency == 0) {
          $row_ids = Engine_Api::_()->getDbtable('categories', 'sitepage')->getSubCategories($cat_id);
          foreach ($row_ids as $values) {
            $tableCategories->delete(array('subcat_dependency = ?' => $values->category_id, 'cat_dependency = ?' => $values->category_id));
            $tableCategories->delete(array('category_id = ?' => $values->category_id));
          }

          $tablePage->update(array('category_id' => 0, 'subcategory_id' => 0), array('category_id = ?' => $cat_id));
          $tableCategories->delete(array('category_id = ?' => $cat_id));

            //START SITEPAGEREVIEW CODE
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
            Engine_Api::_()->sitepagereview()->deleteCategory($cat_id);
          }
            //END SITEPAGEREVIEW CODE
        } else {
          $tableCategories->update(array('category_name' => $cat_title), array('category_id = ?' => $cat_id, 'cat_dependency = ?' => $cat_dependency));
          $tablePage->update(array('category_id' => 0, 'subcategory_id' => 0), array('category_id = ?' => $cat_id));
          $tableCategories->delete(array('cat_dependency = ?' => $cat_id, 'subcat_dependency = ?' => $cat_id));
          $tableCategories->delete(array('category_id = ?' => $cat_id));
        }
      }
        //SEND AJAX CONFIRMATION
      echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
      echo "window.parent.removecat('$cat_id');";
      echo "</script></head><body></body></html>";
      exit();
    } else {
      if ($cat_id == 'new') {
        $row_info = $tableCategories->fetchRow($tableCategories->select()->from($tableCategoriesName, 'max(cat_order) AS cat_order'));
        $cat_order = $row_info['cat_order'] + 1;
        $row = $tableCategories->createRow();
        $row->category_name = $cat_title_withoutparse;
        $row->cat_order = $cat_order;
        $row->cat_dependency = $cat_dependency;
        $row->subcat_dependency = $subcat_dependency;
        $newcat_id = $row->save();
      } else {
        $tableCategories->update(array('category_name' => $cat_title_withoutparse), array('category_id = ?' => $cat_id));
        $newcat_id = $cat_id;
      }

        //SEND AJAX CONFIRMATION
      echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
      echo "window.parent.savecat_result('$cat_id', '$newcat_id', '$cat_title', '$cat_dependency', '$subcat_dependency');";
      echo "</script></head><body></body></html>";
      exit();
    }
  } elseif ($task == "changeorder") {
    $divId = $_GET['divId'];
    $sitepageOrder = explode(",", $_GET['sitepageorder']);
 
      //RESORT CATEGORIES
    if ($divId == "categories") {
      for ($i = 0; $i < count($sitepageOrder); $i++) {
        $cat_id = substr($sitepageOrder[$i], 4);
        $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
      }
 
    } elseif (substr($divId, 0, 7) == "subcats") {
      for ($i = 0; $i < count($sitepageOrder); $i++) {
        $cat_id = substr($sitepageOrder[$i], 4);
        $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
      }
    } elseif (substr($divId, 0, 11) == "treesubcats") {
      for ($i = 0; $i < count($sitepageOrder); $i++) {
        $cat_id = substr($sitepageOrder[$i], 4);
        $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
      }
    }
 
    }
    $categories = array();
        $category_info = $tableCategories->getCategories();
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $subcategories = $tableCategories->getSubCategories($value->category_id);
            foreach ($subcategories as $subresults) {
                $subsubcategories = $tableCategories->getSubCategories($subresults->category_id);
                $treesubarrays[$subresults->category_id] = array();

                foreach ($subsubcategories as $subsubcategoriesvalues) {

                    //GET TOTAL EVENT COUNT
                    $subsubcategory_sitepage_count = $tablePage->getPagesCount($subsubcategoriesvalues->category_id, 'subsubcategory_id');

                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
                        'count' => $subsubcategory_sitepage_count,
                        'order' => $subsubcategoriesvalues->cat_order);
                }

                //GET TOTAL PAGES COUNT
                $subcategory_sitepage_count = $tablePage->getPagesCount($subresults->category_id, 'subcategory_id');

                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $subcategory_sitepage_count,
                    'order' => $subresults->cat_order);
            }

            //GET TOTAL PAGES COUNT
            $category_sitepage_count = $tablePage->getPagesCount($value->category_id, 'category_id');

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $category_sitepage_count,
                'file_id' => $value->file_id,
                'banner_id' => $value->banner_id,
                'sponsored' => $value->sponsored,
                'sub_categories' => $sub_cat_array,
                'order' => $value->cat_order);
            
        }
        
    $this->view->categories = $categories;
    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitepage');
    $tableCategoryName = $tableCategory->info('name');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->category_id = $category_id = $request->getParam('category_id', 0);
    $perform = $request->getParam('perform', 'add');
    $cat_dependency = 0;
    $subcat_dependency = 0;
    if ($category_id) {
      $category = Engine_Api::_()->getItem('sitepage_category', $category_id);

      if ($category && empty($category->cat_dependency)) {
        $cat_dependency = $category->category_id;

      } elseif ($category && !empty($category->cat_dependency)) {
        $cat_dependency = $category->category_id;
        $subcat_dependency = $category->category_id;
      }
    }

    if ($perform == 'add') {
      $this->view->form = $form = new Sitepage_Form_Admin_Categories_Add();

      //CHECK POST
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK VALIDITY
      if (!$form->isValid($this->getRequest()->getPost())) {

        if (empty($_POST['category_name'])) {
          $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
        }
        return;
      }

      //PROCESS
      $values = $form->getValues();
     
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
        $cat_order = $row_info['cat_order'] + 1;


        //GET CATEGORY TITLE
        $category_name = str_replace("'", "\'", trim($values['category_name']));
        $values['cat_order'] = $cat_order;
        $values['category_name'] = $category_name;
        $values['cat_dependency'] = $cat_dependency;
        $values['subcat_dependency'] = $subcat_dependency;

        $row = $tableCategory->createRow();
        $row->setFromArray($values);

        include_once APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/license2.php';

        //UPLOAD ICON
        if (isset($_FILES['icon'])) {
          $photoFile = $row->setPhoto($form->icon);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->file_id = $photoFile->file_id;
          }
        }

        //UPLOAD CATEGORY PHOTO
        if (isset($_FILES['photo'])) {
          $photoFile = $row->setPhoto($form->photo, true);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->photo_id = $photoFile->file_id;
          }
        }

        //UPLOAD BANNER
        if (isset($_FILES['banner'])) {
          $photoFile = $row->setPhoto($form->banner);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->banner_id = $photoFile->file_id;
          }
        }

        $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

        if (empty($banner_url)) {
          if ($values['banner_url']) {
            $row->banner_url = "http://" . $values['banner_url'];
          } else {
            $row->banner_url = $values['banner_url'];
          }
        } else {
          $row->banner_url = $values['banner_url'];
        }

        $category_id = $row->save();
        
                
        // IF SITETHEME IS ENABLED, THEN INSERT NEW CREATED PARENT CATEGORY ENTRY IN CORE_MENUITEMS
        $isSitethemeEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme');
        if( !empty($isSitethemeEnable) && empty($row->cat_dependency) && empty($row->subcat_dependency) )
        {
          $categoryOrder = ++$row->cat_order;
          $db = Engine_Db_Table::getDefaultAdapter();//$this->getDb();
          $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitepage_category_$category_id', 'sitepage', '$row->category_name', 'Sitetheme_Plugin_Menus::categoryUrl', '', 'sitetheme_main', '', '1', '0', $categoryOrder)");
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_helper->redirector->gotoRoute(array('module' => 'sitepage','action' => 'sitepagecategories', 'controller' => 'settings', 'category_id' => $category_id,'perform' => 'edit'), 'admin_default', true);
    } else {
        
      $this->view->form = $form = new Sitepage_Form_Admin_Categories_Edit();
      $category = Engine_Api::_()->getItem('sitepage_category', $category_id);
      $form->populate($category->toArray());
      
    //CHECK POST
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK VALIDITY
      if (!$form->isValid($this->getRequest()->getPost())) {

        if (empty($_POST['category_name'])) {
          $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
        }
        return;
      }
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET CATEGORY TITLE
        $category_name = str_replace("'", "\'", trim($values['category_name']));

        $category->category_name = $category_name;
        $category->meta_title = $values['meta_title'];
        $category->meta_description = $values['meta_description'];
        $category->meta_keywords = $values['meta_keywords'];
        $category->sponsored = $values['sponsored'];
        $category->banner_title = $values['banner_title'];
        $category->banner_url_window = $values['banner_url_window'];
        $category->category_slug = $values['category_slug'];
        $category->top_content = $values['top_content'];
        $category->bottom_content = $values['bottom_content'];
        $cat_dependency = $category->cat_dependency;
        $subcat_dependency = $category->subcat_dependency;
        if ($category_id && empty($subcat_dependency) && !empty($cat_dependency)) {
          $cat_dependency = $cat_dependency;
          $subcat_dependency = 0;
        } elseif ($category_id && !empty($subcat_dependency) && !empty($cat_dependency)) {
          $cat_dependency = $cat_dependency;
          $subcat_dependency = $subcat_dependency;
        }

        $category->cat_dependency = $cat_dependency;
        $category->subcat_dependency = $subcat_dependency;

        //UPLOAD ICON
        if (isset($_FILES['icon'])) {
          $previous_file_id = $category->file_id;
          $photoFile = $category->setPhoto($form->icon);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->file_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY ICON
            if ($previous_file_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
              if(!empty($file))
                $file->delete();
            }
          }
        }

        if (isset($_FILES['photo'])) {
          $previous_photo_id = $category->photo_id;
          $photoFile = $category->setPhoto($form->photo, true);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->photo_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY ICON
            if ($previous_photo_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_photo_id);
              if(!empty($file))
                $file->delete();
            }
          }
        }

        //UPLOAD BANNER
        if (isset($_FILES['banner'])) {
          $previous_banner_id = $category->banner_id;
          $photoFile = $category->setPhoto($form->banner);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->banner_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY BANNER
            if ($previous_banner_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
              if(!empty($file))
                $file->delete();
            }
          }
        }

        $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

        if (empty($banner_url)) {
          if ($values['banner_url']) {
            $category->banner_url = "http://" . $values['banner_url'];
          } else {
            $category->banner_url = $values['banner_url'];
          }
        } else {
          $category->banner_url = $values['banner_url'];
        }

        $category_id = $category->save();

        if (isset($values['removephoto']) && !empty($values['removephoto'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->photo_id);          

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->photo_id = 0;
          $category->save();
          if(!empty($file))
            $file->delete();
        }

        if (isset($values['removeicon']) && !empty($values['removeicon'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->file_id);          

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->file_id = 0;
          $category->save();
          if(!empty($file))
            $file->delete();
        }

        if (isset($values['removebanner']) && !empty($values['removebanner'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->banner_id);          

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->banner_id = 0;
          $category->save();
          if(!empty($file))
            $file->delete();
        }


        if (empty($cat_dependency) && empty($subcat_dependency)) {
          // Engine_Api::_()->getApi('productType', 'sitestoreproduct')->categoriesPageCreate(array(0 => $category_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('module' => 'sitepage','action' => 'sitepagecategories', 'controller' => 'settings', 'category_id' => $category_id,'perform' => 'edit'), 'admin_default', true);
    }
  }

    public function createEditAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitepage_admin_main_settings', array(), 'sitepage_admin_main_createedit');

        $this->view->form = $form = new Sitepage_Form_Admin_CreateEdit();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            foreach ($values as $key => $value) {
                if ($coreSettings->hasSetting($key))
                  $coreSettings->removeSetting($key);
                $coreSettings->setSetting($key, $value);
            }
            $form->addNotice('Your changes have been saved.');
        }
    }  
  
   
//ACTION FOR FAQ
public function faqAction() {
  //TABS CREATION
  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_faq');
  $this->view->faq = 1;
  $this->view->faq_type = $this->_getParam('faq_type', 'general');
}

public function guidelinesAction() {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_settings');
}

public function readmeAction() {

  $this->view->faq = 0;
  $this->view->faq_type = $this->_getParam('faq_type', 'general');
}

  //ACTION FOR SHOWING THE PAGE STATISTICS
public function statisticAction() {

    //GET NAVIGATION
  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_statistic');

    //GET PAGE TABLE
  $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');

    //GET TOTAL PAGES    
  $this->view->totalSitepage = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalpage'))->totalpage;

    //GET PUBLISH PAGES
  $this->view->totalPublish = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalpublish')->where('draft =?', 1))->totalpublish;

    //GET DRAFTED PAGES
  $this->view->totalDrafted = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totaldrafted')->where('draft =?', 0))->totaldrafted;

    //Get CLOSED PAGES
  $this->view->totalClosed = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalclosed')->where('closed =?', 1))->totalclosed;

    //Get OPEN PAGES
  $this->view->totalopen = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalopen')->where('closed =?', 0))->totalopen;

    //GET APPROVED PAGES
  $this->view->totalapproved = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalapproved')->where('approved =?', 1))->totalapproved;

    //GET DISAPPROVED PAGES
  $this->view->totaldisapproved = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totaldisapproved')->where('approved =?', 0))->totaldisapproved;

    //GET FEATURED PAGES
  $this->view->totalfeatured = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalfeatured')->where('featured =?', 1))->totalfeatured;

    //GET SPONSORED PAGES	
  $this->view->totalsponsored = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'count(*) AS totalsponsored')->where('sponsored =?', 1))->totalsponsored;

    //GET TOTAL COMMENTS IN PAGES	
  $this->view->totalcommentpost = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'sum(comment_count) AS totalcomments'))->totalcomments;

    //GET TOTAL LIKES IN PAGES	
  $this->view->totallikepost = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'sum(like_count) AS totallikes'))->totallikes;

    //GET TOTAL VIEWS IN PAGES	
  $this->view->totalviewpost = $tablePage->fetchRow($tablePage->select()->from($tablePage->info('name'), 'sum(view_count) AS totalviews'))->totalviews;

    //CHECK THAT SITEPAGE REVIEW IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
      //GET REVIEW TABLE TABLE
    $tableReview = Engine_Api::_()->getDbtable('reviews', 'sitepagereview');

      //GET TOTAL REVIEWS IN PAGES	
    $this->view->totalreview = $tableReview->fetchRow(Engine_Api::_()->getDbtable('reviews', 'sitepagereview')->select()->from($tableReview->info('name'), 'count(*) AS totalreview'))->totalreview;
  }

    //CHECK THAT SITEPAGE DISCUSSION IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
      //GET DISCUSSION TABLE
    $tableDiscussion = Engine_Api::_()->getDbtable('topics', 'sitepage');

      //GET TOTAL DISCUSSION IN PAGES
    $this->view->totaldiscussion = $tableDiscussion->fetchRow($tableDiscussion->select()->from($tableDiscussion->info('name'), 'count(*) AS totaldiscussion'))->totaldiscussion;

      //GET DISCUSSION POST TABLE
    $tableDiscussionPost = Engine_Api::_()->getDbtable('posts', 'sitepage');

      //GET TOTAL DISCUSSION POST (REPLY)IN PAGES       
    $this->view->totaldiscussionpost = $tableDiscussionPost->fetchRow($tableDiscussionPost->select()->from($tableDiscussionPost->info('name'), 'count(*) AS totalpost'))->totalpost;
  }

    //GET PHOTO TABLE
  $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');

    //GET THE TOTAL PHOTO IN PAGES
  $this->view->totalphotopost = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'count(*) AS totalphoto')->where('collection_id <>?', 0))->totalphoto;

    //CHECK THAT SITEPAGE ALBUM IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
      //GET ALBUM TABLE
    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');

      //GET THE TOTAL ALBUM IN PAGES
    $this->view->totalalbumpost = $tableAlbum->fetchRow($tableAlbum->select()->from($tableAlbum->info('name'), 'count(*) AS totalalbum'))->totalalbum;
  }

    //CHECK THAT SITEPAGE NOTE IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
      //GET NOTE TABLE
    $tableNote = Engine_Api::_()->getDbtable('notes', 'sitepagenote');

      //GET THE TOTAL NOTE IN PAGES
    $this->view->totalnotepost = $tableNote->fetchRow($tableNote->select()->from($tableNote->info('name'), 'count(*) AS totalnotes'))->totalnotes;
  }

    //CHECK THAT SITEPAGE VIDEO IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
      //GET VIDEO TABLE
    $tableVideo = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');

      //GET THE TOTAL VIDEO IN PAGES
    $this->view->totalvideopost = $tableVideo->fetchRow($tableVideo->select()->from($tableVideo->info('name'), 'count(*) AS totalvideos'))->totalvideos;
  }

    //CHECK THAT SITEPAGE DOCUMENT IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
      //GET DOCUMENT TABLE
    $tableDocument = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');

      //GET THE TOTAL DOCUMENT IN PAGES
    $this->view->totaldocumentpost = $tableDocument->fetchRow($tableDocument->select()->from($tableDocument->info('name'), 'count(*) AS totaldocuments'))->totaldocuments;
  }

    //CHECK THAT SITEPAGE EVENT IS ENABLED OR NOT
  if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
   if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
				//GET EVENT TABLE
    $tableEvent = Engine_Api::_()->getDbtable('events', 'sitepageevent');

				//GET THE TOTAL EVENT IN PAGES
    $this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents'))->totalevents;
  } else {
				//GET EVENT TABLE
    $tableEvent = Engine_Api::_()->getDbtable('events', 'siteevent');

				//GET THE TOTAL EVENT IN PAGES
    $this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents')->where('parent_type =?', 'sitepage_page'))->totalevents;
  }
}

    //CHECK THAT SITEPAGE VIDEO IS ENABLED OR NOT
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
      //GET PLAYLIST TABLE
  $tablePlaylist = Engine_Api::_()->getDbtable('playlists', 'sitepagemusic');

      //GET THE TOTAL PLAYLIST IN PAGES
  $this->view->totalplaylists = $tablePlaylist->fetchRow($tablePlaylist->select()->from($tablePlaylist->info('name'), 'count(*) AS totalplaylists'))->totalplaylists;

      //GET PLAYLIST TABLE
  $tableSongs = Engine_Api::_()->getDbtable('playlistSongs', 'sitepagemusic');

      //GET THE TOTAL PLAYLIST IN PAGES
  $this->view->totalsongs = $tableSongs->fetchRow($tableSongs->select()->from($tableSongs->info('name'), 'count(*) AS totalsongs'))->totalsongs;
}

    //CHECK THAT SITEPAGE POLL IS ENABLED OR NOT
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
      //GET POLL TABLE
  $tablePoll = Engine_Api::_()->getDbtable('polls', 'sitepagepoll');

      //GET THE TOTAL POLL IN PAGES
  $this->view->totalpollpost = $tablePoll->fetchRow($tablePoll->select()->from($tablePoll->info('name'), 'count(*) AS totalpolls'))->totalpolls;
}

    //CHECK THAT SITEPAGE OFFER IS ENABLED OR NOT
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
      //GET OFFER TABLE
  $tableOffer = Engine_Api::_()->getDbtable('offers', 'sitepageoffer');

      //GET THE TOTAL OFFER IN PAGES
  $this->view->totalofferpost = $tableOffer->fetchRow($tableOffer->select()->from($tableOffer->info('name'), 'count(*) AS totaloffers'))->totaloffers;
}
}

public function graphAction() {

  $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_graph');
  $this->view->form = $form = new Sitepage_Form_Admin_Settings_Graph();

  if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
    $values = $form->getValues();
    foreach ($values as $key => $value) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
    }
  }
}

  //ACTION FOR EMAIL THE DETAIL
public function emailAction() {

  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_email');
  $this->view->form = $form = new Sitepage_Form_Admin_Settings_Email();

    //check if comments should be displayed or not
  $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();

  $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
  $rtasksName = $taskstable->info('name');
  $taskstable_result = $taskstable->select()
  ->from($rtasksName, array('processes', 'timeout'))
  ->where('title = ?', 'Sitepage Insight Mail')
  ->where('plugin = ?', 'Sitepage_Plugin_Task_InsightNotification')
  ->limit(1);
  $prefields = $taskstable->fetchRow($taskstable_result);

    //populate form
//     $form->populate(array(
//         'sitepage_insightemail' => $prefields->processes,
//     ));

  if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
    $values = $form->getValues();

      //check if Sitemailtemplates Plugin is enabled
    $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_insightemail', $values['sitepage_insightemail']);

    if(empty($sitemailtemplates)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_bg_color', $values['sitepage_bg_color']);
    }
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_insightmail_time', $values['sitepage_insightmail_options']);
    $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
    if(empty($sitemailtemplates)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_header_color', $values['sitepage_header_color']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_site_title', $values['sitepage_site_title']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_title_color', $values['sitepage_title_color']);
    }
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_demo', $values['sitepage_demo']);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage_admin', $values['sitepage_admin']);
    $taskstable->update(array('processes' => $values['sitepage_insightemail']), array('title = ?' => 'Sitepage Insight Mail', 'plugin = ?' => 'Sitepage_Plugin_Task_InsightNotification'));
    if ($values['sitepage_demo'] == 1 && $values['sitepage_insightemail'] == 1) {

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        //check if Sitemailtemplates Plugin is enabled
      $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
      $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

      $insights_string = '';
      $template_header = "";
      $template_footer = "";
      if(!$sitemailtemplates) {
       $site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.color', "#ffffff");
       $site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.header.color', "#79b4d4");

					//GET SITE "Email Body Outer Background" COLOR
       $site_bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.bg.color', "#f7f7f7");
       $insights_string.= "<table cellpadding='2'><tr><td><table cellpadding='2'><tr><td><span style='font-size: 14px; font-weight: bold;'>" . $view->translate("Sample Page") . "</span></td></tr>";

       $template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='$site_bg_color' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
       $template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";

       $template_footer.= "</td></tr></table></td></tr></td></table></td></tr></table>";
     }

     if ($values['sitepage_insightmail_options'] == 1) {
      $vals['days_string'] = $view->translate('week');
    } elseif ($values['sitepage_insightmail_options'] == 2) {
      $vals['days_string'] = $view->translate('month');
    }
    $path = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl();
    $insight_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Visit your Insights Page') . "</a>";
    $update_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Send an update to people who like this') . "</a>";

        //check if Communityad Plugin is enabled
    $sitepagecommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    $adversion = null;
    if ($sitepagecommunityadEnabled) {
      $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
      $adversion = $communityadmodulemodule->version;
      if (!empty(Engine_Api::_()->seaocore()->checkVersion($adversion, '4.1.5'))) {
        $promote_Ad_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Promote with %s Ads', $site_title) . "</a>";
      }
    }

    $insights_string.= "<table><tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $vals['days_string'] . $view->translate(array('ly active user', 'ly active users', 2), 2) . "</span></td></tr><tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" .  $view->translate(array('person likes this', 'people like this', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr>";
    if (!empty($show_comments)) {
      $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $view->translate(array('comment', 'comments', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr>";
    }
    $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . '10' . "</span>\t <span style='color: rgb(85, 85, 85);'>" . $view->translate(array('visit', 'visits', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '5' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr></table><table><tr><td>" . "<ul style=' padding-left: 5px;'><li>" . $insight_link . "</li><li>" . $update_link;

        //check if Communityad Plugin is enabled
    if ($sitepagecommunityadEnabled && !empty(Engine_Api::_()->seaocore()->checkVersion($adversion, '4.1.5'))) {
      $insights_string.= "</li><li>" . $promote_Ad_link;
    }
    $insights_string.= "</li></ul></td></tr></table>";
    $days_string = ucfirst($vals['days_string']);
    $owner_name = Engine_Api::_()->user()->getViewer()->getTitle();
    $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['sitepage_admin'], 'SITEPAGE_INSIGHTS_EMAIL_NOTIFICATION', array(
      'recipient_title' => $owner_name,
      'template_header' => $template_header,
      'message' => $insights_string,
      'template_footer' => $template_footer,
      'site_title' => $site_title,
      'days' => $days_string,
      'email' => $email,
      'queue' => true));
  }
  $form->addNotice('Your changes have been saved.');
}
}

  //ACTION FOR AD SHOULD BE DISPLAY OR NOT ON PAGES
public function adsettingsAction() {

    //GET NAVIGATION
  $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_adsettings');

    //FORM
  $this->view->form = $form = new Sitepage_Form_Admin_Adsettings();

    //CHECK THAT COMMUNITY AD PLUGIN IS ENABLED OR NOT
  $communityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
  if ($communityadEnabled) {
    $this->view->ismoduleenabled = $ismoduleenabled = 1;
  } else {
    $this->view->ismoduleenabled = $ismoduleenabled = 0;
  }

    //CHECK THAT SITEPAGE DOCUMENT PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') && $ismoduleenabled) {
    $this->view->isdocumentenabled = 1;
  } else {
    $this->view->isdocumentenabled = 0;
  }

    //CHECK THAT SITEPAGE NOTE PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') && $ismoduleenabled) {
    $this->view->isnoteenabled = 1;
  } else {
    $this->view->isnoteenabled = 0;
  }

    //CHECK THAT SITEPAGE ALBUM PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') && $ismoduleenabled) {
    $this->view->isalbumenabled = 1;
  } else {
    $this->view->isalbumenabled = 0;
  }

    //CHECK THAT SITEPAGE VIDEO PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && $ismoduleenabled) {
    $this->view->isvideoenabled = 1;
  } else {
    $this->view->isvideoenabled = 0;
  }

    //CHECK THAT SITEPAGE EVENT PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') && $ismoduleenabled) {
    $this->view->iseventenabled = 1;
  } else {
    $this->view->iseventenabled = 0;
  }

    //CHECK THAT SITEPAGE DISCUSSION PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') && $ismoduleenabled) {
    $this->view->isdiscussionenabled = 1;
  } else {
    $this->view->isdiscussionenabled = 0;
  }

    //CHECK THAT SITEPAGE POLL PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') && $ismoduleenabled) {
    $this->view->ispollenabled = 1;
  } else {
    $this->view->ispollenabled = 0;
  }

    //CHECK THAT SITEPAGE REVIEW PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && $ismoduleenabled) {
    $this->view->isreviewenabled = 1;
  } else {
    $this->view->isreviewenabled = 0;
  }

    //CHECK THAT SITEPAGE OFFER PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') && $ismoduleenabled) {
    $this->view->isofferenabled = 1;
  } else {
    $this->view->isofferenabled = 0;
  }

    //CHECK THAT SITEPAGE FORM PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform') && $ismoduleenabled) {
    $this->view->isformenabled = 1;
  } else {
    $this->view->isformenabled = 0;
  }

    //CHECK THAT SITEPAGE INVITE PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite') && $ismoduleenabled) {
    $this->view->isinviteenabled = 1;
  } else {
    $this->view->isinviteenabled = 0;
  }

    //CHECK THAT SITEPAGE BADGE PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') && $ismoduleenabled) {
    $this->view->isbadgeenabled = 1;
  } else {
    $this->view->isbadgeenabled = 0;
  }

    //CHECK THAT SITEPAGE NOTE PLUGIN IS ENABLED OR NOT
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') && $ismoduleenabled) {
    $this->view->ismusicenabled = 1;
  } else {
    $this->view->ismusicenabled = 0;
  }

    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') &&
    $ismoduleenabled) {
    $this->view->mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();
  $this->view->issitepageintegrationenabled = 1;
} else {
  $this->view->issitepageintegrationenabled = 0;
}
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

    //CHECK THAT SITEPAGE TWITTER PLUGIN IS ENABLED OR NOT
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter') && $ismoduleenabled) {
  $this->view->istwitterenabled = 1;
} else {
  $this->view->istwitterenabled = 0;
}

    //CHECK THAT SITEPAGE TWITTER PLUGIN IS ENABLED OR NOT
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') && $ismoduleenabled) {
  $this->view->ismemberenabled = 1;
} else {
  $this->view->ismemberenabled = 0;
}

    //CHECK FORM VALIDATION
if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
  $values = $form->getValues();
  foreach ($values as $key => $value) {
    if ($key != 'submit') {
      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
    }
  }
}
}

 	//ACTION FOR SET THE DEFAULT MAP CENTER POINT
public function setDefaultMapCenterPoint($oldLocation, $newLocation) {

  if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
    $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Directory / Pages'));
    if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
      $latitude = $locationResults['latitude'];
      $longitude = $locationResults['longitude'];
    }

    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.map.latitude', $latitude);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.map.longitude', $longitude);
  }
}

  //ACTINO FOR SEARCH
public function formSearchAction() {

    // GET NAVIGATION
  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_form_search');

  $table = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    //CHECK POST
  if ($this->getRequest()->isPost()) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $values = $_POST;
    $row = $table->getFieldsOptions('sitepage', 'profile_type');
    $defaultAddition = 0;
    $rowCategory = $table->getFieldsOptions('sitepage', 'category_id');
    $defaultCategory = 0;
    try {
      foreach ($values['order'] as $key => $value) {
        $table->update(array('order' => $defaultAddition + $defaultCategory + $key + 1), array('module = ?' => 'sitepage', 'searchformsetting_id =?' => (int) $value));
        if (!empty($row) && $value == $row->searchformsetting_id)
          $defaultAddition = 40;

        if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id)
          $defaultCategory = 1;
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
  $this->view->enableBadgePlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge');
  $this->view->enableReviewPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
  $this->view->enableGeoLocationPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagegeolocation');
  $this->view->searchForm = $table->fetchAll($table->select()->where('module = ?', 'sitepage')->order('order'));
}

  //ACTINO FOR ACTIVITY FEED
public function activityFeedAction() {
    // GET NAVIGATION
  $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_activity_feed');
  $aafmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');
  if(!empty ($aafmodule))
   $this->view->isAAFModule=true;
    //FILTER FORM
 $this->view->form = $form = new Sitepage_Form_Admin_Settings_ActivityFeed();
    //CHECK POST
 if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
  $values = $form->getValues();
  $api = Engine_Api::_()->getApi("settings", "core");
  foreach ($values as $key => $value) {
    $api->setSetting($key, $value);
  }
  $enable = $form->sitepage_feed_type->getValue();
  $db = Zend_Db_Table_Abstract::getDefaultAdapter();
  $activityfeed_array = array("sitepagealbum_admin_photo_new", "sitepagedocument_admin_new", "sitepageevent_admin_new", "sitepagemusic_admin_new", "sitepagenote_admin_new", "sitepageoffer_admin_new", "sitepagepoll_admin_new", "sitepagevideo_admin_new", "sitepage_admin_topic_create", "sitepage_admin_topic_reply");
  foreach ($activityfeed_array as $value) {
    $activit_type_sql = "UPDATE `engine4_activity_actiontypes` SET `enabled` = $enable WHERE `engine4_activity_actiontypes`.`type` = '$value' LIMIT 1";
    $db->query($activit_type_sql);
  }
  $form->addNotice('Your changes have been saved.');
}
}

public function overwriteAction() {
  $type = $this->_getParam('type');
  $this->view->error = null;
  $this->view->status = false;
  if (empty($type))
    return;

  $moduleActivity = Engine_Api::_()->getDbtable('modules', 'core')->getModule('activity');
  $activityVersion = $moduleActivity->version;
  $dirName = 'activity-' . $activityVersion;
  $sourcePath = $dirName;
  $destinationPath = null;
  $api = Engine_Api::_()->getApi("settings", "core");

  $api->setSetting('sitepage_feed_type', 1);

  $destinationPath = APPLICATION_PATH
  . '/application/modules/Activity/views/scripts/_activityText.tpl';
  $sourcePath .='/views/scripts/_activityText.tpl';

  $api->setSetting('sitepagefeed_likepage_dummy', 'b');


  if (is_file($destinationPath)) {
    @chmod($destinationPath, 0777);
  } else {
    $this->view->error = 'Target File does not exist.';
  }

  if (!is_writeable($destinationPath)) {
    $this->view->error = 'Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.';
  }

  $serverPath = 'http://www.socialengineaddons.com/SocialEngine/SocialengineModules/index.php?path=';
  $sourcePath = $serverPath . @urlencode($sourcePath);
  $ch = curl_init();
  $timeout = 0;
  @curl_setopt($ch, CURLOPT_URL, $sourcePath);
  @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  @ob_start();
  @curl_exec($ch);
  @curl_close($ch);
  if (empty($exe_status)) {
    $get_value = @ob_get_contents();
  }
  if (empty($get_value)) {
    $get_value = @file_get_contents($oposit_url);
  }
  @ob_end_clean();

  if (!empty($get_value)) {
    if (!@file_put_contents($destinationPath, $get_value)) {
      $this->view->status = false;
      $this->view->error = 'Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.';
      return;
    }
  } else {
    $this->view->error = 'It seems that you do not have any internet connection that\'s why you are not able to overwrite this file.';
  }

  @chmod($activityTextPath_Original, 0755);
  if (empty($this->view->error))
    $this->view->status = true;
}

	//ACTION FOR MAPPING OF LISTINGS
Public function mappingCategoryAction()
{
		//SET LAYOUT
  $this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
  $this->view->catid = $catid = $this->_getParam('category_id');

		//GET CATEGORY TITLE
  $this->view->oldcat_title = $oldcat_title = $this->_getParam('oldcat_title');

		//GET CATEGORY DEPENDANCY
  $this->view->subcat_dependency = $subcat_dependency = $this->_getParam('cat_dependency');

    //CREATE FORM
  $this->view->form = $form = new Sitepage_Form_Admin_Settings_Mapping();

  $this->view->close_smoothbox = 0;

  if( !$this->getRequest()->isPost() ) {
    return;
  }

  if( !$form->isValid($this->getRequest()->getPost()) ) {
    return;
  }

  if( $this->getRequest()->isPost()){ 

			//GET FORM VALUES
   $values = $form->getValues();

  if($values['new_category_id'] == 0) {
    $form->addError('Please Select atleast one category.');
    return;
  }
			//GET PAGES TABLE
   $tableSitepage = Engine_Api::_()->getDbtable('pages', 'sitepage');

			//GET CATEGORY TABLE
   $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitepage');

			//ON CATEGORY DELETE
   $rows = $tableCategory->getSubCategories($catid);
   foreach ($rows as $row) {
    $tableCategory->delete(array('subcat_dependency = ?' => $row->category_id, 'cat_dependency = ?' => $row->category_id));
    $tableCategory->delete(array('category_id = ?' => $row->category_id));
  }

  $previous_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'sitepage')->getProfileType($catid);
  $new_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'sitepage')->getProfileType($values['new_category_id']);

			//SELECT PAGES WHICH HAVE THIS CATEGORY
  if($previous_cat_profile_type != $new_cat_profile_type) {
    $rows = $tableSitepage->getCategorySitepage($catid);
    if (!empty($rows)) {
     foreach ($rows as $key => $page_ids) {
      $page_id = $page_ids['page_id'];

						//DELETE ALL MAPPING VALUES FROM FIELD TABLES
      Engine_Api::_()->fields()->getTable('sitepage_page', 'values')->delete(array('item_id = ?' => $page_id));
      Engine_Api::_()->fields()->getTable('sitepage_page', 'search')->delete(array('item_id = ?' => $page_id));

						//UPDATE THE PROFILE TYPE OF ALREADY CREATED PAGES
      $tableSitepage->update(array('profile_type' => $new_cat_profile_type), array('page_id = ?' => $page_id));
    }
  }
}

			//PAGE TABLE CATEGORY DELETE WORK
if(isset($values['new_category_id']) && !empty($values['new_category_id']) ) {
  $tableSitepage->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
}
else {
  $tableSitepage->update(array('category_id' => 0), array('category_id = ?' => $catid));
}

$tableCategory->delete(array('category_id = ?' => $catid));
$url = $this->_helper->url->url(array("module" => "sitepage","controller" => "settings","action" => "sitepagecategories"),'admin_default', true);
$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 300,
                'parentRedirect' => $url,
                'parentRedirectTime' => 200,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category successfully deleted.'))
            ));
}

}

	//ACTION FOR THE LANGUAGE FILE CHANGE DURING THE UPGRADE.
public function languageAction() {
	
		//START LANGUAGE WORK
  Engine_Api::_()->getApi('language', 'sitepage')->languageChanges();
		//END LANGUAGE WORK
  $redirect = $this->_getParam('redirect', false);
  if($redirect == 'install') {
   $this->_redirect('install/manage');
 } elseif($redirect == 'query') {
   $this->_redirect('install/manage/complete');
 }
}


  //ACTION FOR ADD THE CATEGORY ICON
Public function addIconAction()
{
		//SET LAYOUT
  $this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
  $this->view->category_id = $category_id = $this->_getParam('category_id');
  $category = Engine_Api::_()->getItem('sitepage_category', $category_id);

    //CREATE FORM
  $this->view->form = $form = new Sitepage_Form_Admin_Settings_Addicon();

  $this->view->close_smoothbox = 0;

  if( !$this->getRequest()->isPost() ) {
    return;
  }

  if( !$form->isValid($this->getRequest()->getPost()) ) {
    return;
  }

		//UPLOAD PHOTO
  if( isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) )
  {
			//UPLOAD PHOTO
   $photoFile = $category->setPhoto($_FILES['photo']);

			//UPDATE FILE ID IN CATEGORY TABLE
   if(!empty($photoFile->file_id)) {
    $category->file_id = $photoFile->file_id;
    $category->save();
  }
}

$this->view->close_smoothbox = 1;
}

	//ACTION FOR EDIT THE CATEGORY ICON
Public function editIconAction()
{
		//SET LAYOUT
  $this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
  $this->view->category_id = $category_id = $this->_getParam('category_id');

		//GET CATEGORY ITEM
  $category = Engine_Api::_()->getItem('sitepage_category', $category_id);

    //CREATE FORM
  $this->view->form = $form = new Sitepage_Form_Admin_Settings_Editicon();

  $this->view->close_smoothbox = 0;

  if( !$this->getRequest()->isPost() ) {
    return;
  }

  if( !$form->isValid($this->getRequest()->getPost()) ) {
    return;
  }

		//UPLOAD PHOTO
  if( isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) )
  {
			//UPLOAD PHOTO
   $photoFile = $category->setPhoto($_FILES['photo']);

			//UPDATE FILE ID IN CATEGORY TABLE
   if(!empty($photoFile->file_id)) {
    $previous_file_id = $category->file_id;
    $category->file_id = $photoFile->file_id;
    $category->save();

				//DELETE PREVIOUS CATEGORY ICON
    $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
    $file->delete();
  }
}

$this->view->close_smoothbox = 1;
}

  //ACTION FOR DELETE THE CATEGORY ICON
public function deleteIconAction()
{
		//SET LAYOUT
  $this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
  $this->view->category_id = $category_id = $this->_getParam('category_id');

		//GET CATEGORY ITEM
  $category = Engine_Api::_()->getItem('sitepage_category', $category_id);

  $this->view->close_smoothbox = 0;

  if( $this->getRequest()->isPost() && !empty($category->file_id)){

			//DELETE CATEGORY ICON
   $file = Engine_Api::_()->getItem('storage_file', $category->file_id);
   $file->delete();

			//UPDATE FILE ID IN CATEGORY TABLE
   $category->file_id = 0;
   $category->save();

   $this->view->close_smoothbox = 1;
 }
 $this->renderScript('admin-settings/delete-icon.tpl');
}

public function updateActivityAction() {
  $db = Engine_Db_Table::getDefaultAdapter();
  $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$object} added {var:$count} photo(s) to the album {itemChild:$object:sitepage_album:$child_id}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitepagealbum_admin_photo_new\' LIMIT 1 ;');

  $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$subject} added {var:$count} photo(s) to the album {itemChild:$object:sitepage_album:$child_id} of page {item:$object}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitepagealbum_photo_new\' LIMIT 1 ;');

  $select = new Zend_Db_Select($db);
  $select->from('engine4_activity_actions', array('object_id', 'params', 'action_id'))
  ->where('type =?', 'sitepagealbum_admin_photo_new')
  ->orWhere('type =?', 'sitepagealbum_photo_new');
  $results = $select->query()->fetchAll();

  foreach ($results as $result) {
    if (strstr($result['params'], 'slug')) {
      $action = Engine_Api::_()->getItem('activity_action', $result['action_id']);
      $decoded_cover_param = Zend_Json_Decoder::decode($result['params']);
      $count = $decoded_cover_param['count'];
      $select = new Zend_Db_Select($db);
      $album_id = $select->from('engine4_sitepage_albums', 'album_id')
      ->where('page_id =?', $result['object_id'])
      ->order('album_id DESC')
      ->limit(1)
      ->query()->fetchColumn();
                //echo $album_id;die;
                $action->params = array('child_id' => $album_id, 'count' => $count);
                $action->save();
            }
        }
        echo "Updated";die;
    }  
    //ACTION FOR DELETE THE EVENT
    public function deleteCategoryAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $category_id = $this->_getParam('category_id');

        $cat_dependency = $this->_getParam('cat_dependency');

        $this->view->category_id = $category_id;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitepage');
        $tableCategoryName = $tableCategory->info('name');

        //GET PAGE TABLE
        $tableSitepage = Engine_Api::_()->getDbtable('pages', 'sitepage');

        if ($this->getRequest()->isPost()) {
            //if($cat_dependency != 0) {
            //IF SUB-CATEGORY AND 3RD LEVEL CATEGORY IS MAPPED
            $previous_cat_profile_type = $tableCategory->getProfileType(null, $category_id);

            if ($previous_cat_profile_type) {

                //SELECT PAGES WHICH HAVE THIS CATEGORY
                $pages = $tableSitepage->getCategoryList($category_id, 'category_id');
                if ( count($pages) > 0 ) {

                  foreach ($pages as $page) {

                      //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                      Engine_Api::_()->fields()->getTable('sitepage_page', 'values')->delete(array('item_id = ?' => $page->page_id));
                      Engine_Api::_()->fields()->getTable('sitepage_page', 'search')->delete(array('item_id = ?' => $page->page_id));

                      //UPDATE THE PROFILE TYPE OF ALREADY CREATED EVENTS
                      $tableSitepage->update(array('profile_type' => 0), array('page_id = ?' => $page->page_id));

                      //GET REVIEW TABLE
                      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitepagereview');
                        $reviewTableName = $reviewTable->info('name');

                        //REVIEW PROFILE TYPE UPDATION WORK
                        $reviewIds = $reviewTable->select()
                                ->from($reviewTableName, 'review_id')
                                ->where('page_id = ?', $page->page_id)
                                ->query()
                                ->fetchAll(Zend_Db::FETCH_COLUMN)
                        ;
                        if (!empty($reviewIds)) {
                            foreach ($reviewIds as $reviewId) {
                                //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                                Engine_Api::_()->fields()->getTable('sitepage_review', 'values')->delete(array('item_id = ?' => $reviewId));
                                Engine_Api::_()->fields()->getTable('sitepage_review', 'search')->delete(array('item_id = ?' => $reviewId));

                                //UPDATE THE PROFILE TYPE OF ALREADY CREATED REVIEWS
                                $reviewTable->update(array('profile_type_review' => 0), array('resource_id = ?' => $reviewId));
                            }
                        }
                    }
                  }
                }
            }

            //SITEPAGE TABLE SUB-CATEGORY/3RD LEVEL DELETE WORK
            $tableSitepage->update(array('subcategory_id' => 0, 'subsubcategory_id' => 0), array('subcategory_id = ?' => $category_id));
            $tableSitepage->update(array('subsubcategory_id' => 0), array('subsubcategory_id = ?' => $category_id));

            $tableCategory->delete(array('cat_dependency = ?' => $category_id, 'subcat_dependency = ?' => $category_id));
            $tableCategory->delete(array('category_id = ?' => $category_id));

            //}
            //GET URL
            $url = $this->_helper->url->url(array("module" => "sitepage","controller" => "settings","action" => "sitepagecategories"),'admin_default', true);
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }

        $this->renderScript('admin-settings/delete-category.tpl');
    }

    public function notificationsAction(){

      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_notificationsettings');
      $this->view->form = $form = new Sitepage_Form_Admin_NotificationSettings();

      if (!$this->getRequest()->isPost()) {
        return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      $values = $form->getValues();

      foreach ($values as $key => $value) {
        if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key)) {
          Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
        }
        if (is_null($value)) {
          $value = "";
        }
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice($this->view->translate('Your changes have been saved successfully.'));

    }
}
?>
