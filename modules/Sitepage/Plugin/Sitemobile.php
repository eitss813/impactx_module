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
class Sitepage_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;

  
  public function onIntegrated() {

    $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Page Plugin Main
    $this->addSitepageCreatePage();
    $this->addSitepageHomePage();
    $this->addSitepageBrowsePage();
    $this->addSitepageProfilePage();
    $this->addSitepageManagePage();
    $this->addSitepageManageAdminPage();
    $this->addSitepageManageLikePage();
    $this->addSitepageManageJoinedPage();
    $this->addSitepagePages();
    include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/mobileLayoutCreation.php";
  }

  //Page plugin main pages
  public function addSitepageProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitepage_index_view')
            ->limit(1);

    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_index_view',
          'displayname' => 'Directory / Pages - Page Profile',
          'title' => 'Page Profile',
          'description' => 'This is a page profile page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 2,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepage.closepage-sitepage',
					'parent_content_id' => $middle_id,
					'order' => 1,
			));

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepage.sitemobile-pagecover-photo-information',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '{"title":"","titleCount":true,"showContent":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"strachPhoto":"0"}',
			));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 100,
          'params' => '{"title":"Updates"}',
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.sitemobile-info-sitepage',
          'parent_content_id' => $tab_id,
          'order' => 200,
          'params' => '{"title":"Info"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.sitemobile-overview-sitepage',
          'parent_content_id' => $tab_id,
          'order' => 300,
          'params' => '{"title":"Overview","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.sitemobile-location-sitepage',
          'parent_content_id' => $tab_id,
          'order' => 400,
          'params' => '{"title":"Map","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'seaocore.sitemobile-people-like',
          'parent_content_id' => $tab_id,
          'order' => 3000,
          'params' => '{"title":"Member Likes","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'seaocore.sitemobile-followers',
          'parent_content_id' => $tab_id,
          'order' => 3100,
          'params' => '{"title":"Followers","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.featuredowner-sitepage',
          'parent_content_id' => $tab_id,
          'order' => 3200,
          'params' => '{"title":"Page Admins","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.favourite-page',
          'parent_content_id' => $tab_id,
          'order' => 3300,
          'params' => '{"title":"Linked Pages","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.subpage-sitepage',
          'parent_content_id' => $tab_id,
          'order' => 3400,
          'params' => '{"title":"Sub Pages of a Page","titleCount":true}',
      ));

      //tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-links',
          'parent_content_id' => $tab_id,
          'order' => 3500,
          'params' => '{"title":"Links","titleCount":true}',
      ));
    }
  }

  public function addSitepageCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_create');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_index_create',
          'displayname' => 'Directory / Pages - Create Page',
          'title' => 'Create new Page',
          'description' => 'This is page create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

  public function addSitepageHomePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_home');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_index_home',
          'displayname' => 'Directory / Pages - Pages Home',
          'title' => 'Pages Home',
          'description' => 'This is page home page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));   

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepage.categories-sitepage',
          'page_id' => $page_id,
          'params' => '{"title":"Categories","nomobile":"0","name":"sitepage.categories-sitepage"}',
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
            $db->insert($this->_contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemobile.container-tabs-columns',
                'parent_content_id' => $main_middle_id,
                'order' => 5,
                'params' => '{"max":6}',
                'module' => 'sitemobile'
            ));
            $tab_id = $db->lastInsertId($this->_contentTable);
            
            if($this->_contentTable == 'engine4_sitemobileapp_content' || $this->_contentTable == 'engine4_sitemobileapp_tablet_content')   {  
              $viewType = "gridview";
              $layout_views = '["2"]';
              $content_display = '["featured","sponsored","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location"]';
            }else{
              $viewType = "listview";
              $layout_views = '["1","2"]';
              $content_display = '["ratings","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location","price"]';
            }
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitepage.sitemobile-popular-pages',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'module' => 'sitepage',
                'params' => '{"title":"Recently Posted","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitepage.sitemobile-popular-pages","popularity":"Recently Posted","itemCount":"5","truncation":"16", "layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitepage.sitemobile-popular-pages',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'module' => 'sitepage',
                'params' => '{"title":"Most Viewed","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitepage.sitemobile-popular-pages","popularity":"Most Viewed","itemCount":"5","truncation":"16", "layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',                
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitepage.sitemobile-popular-pages',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 3,
                'module' => 'sitepage',
                'params' => '{"title":"Featured","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitepage.sitemobile-popular-pages","popularity":"Featured","itemCount":"5","truncation":"16","layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
                 ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitepage.sitemobile-popular-pages',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 4,
                'module' => 'sitepage',
                'params' => '{"title":"Sponsored","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitepage.sitemobile-popular-pages","popularity":"Sponsored",
                "itemCount":"5","truncation":"16","layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
                 ));
            
            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if ($sitepagememberEnabled) {
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitepage.sitemobile-popular-pages',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 5,
                'module' => 'sitepage',
                'params' => '{"title":"Most Joined","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitepage.sitemobile-popular-pages","popularity":"Most Joined",
                "itemCount":"5","truncation":"16","layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
                ));
            }
    }
  }

  public function addSitepageBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_index');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_index_index',
          'displayname' => 'Directory / Pages - Browse Pages',
          'title' => 'Browse Pages',
          'description' => 'This is page browse page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      //WE WILL NOT ADD THE ALPHABETICAL WIDGET TAB ON APP AND TABLET APP.
     if($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages')  {
        // Insert Alphabetic Filtering
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitepage.alphabeticsearch-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 2,
        ));
     }
 
       
          if($this->_contentTable == 'engine4_sitemobileapp_content' || $this->_contentTable == 'engine4_sitemobileapp_tablet_content') {
            //App Parameters for page listing
            $params = '{"title":"","titleCount":true,"layouts_views":["2"],"view_selected":"grid","layouts_oder":"2","columnHeight":"260","category_id":"0","content_display":["featured","sponsored","date","owner","likeCount","followCount","memberCount","reviewCount","location"],"name":"sitepage.sitemobile-pages-sitepage"}';        
          }else{
            //Mobile Browser Parameters for page listing
            $params = '{"title":"","titleCount":true,"layouts_views":["1","2"],"view_selected":"list","layouts_oder":"2","columnHeight":"260","category_id":"0","content_display":["ratings","likeCount","followCount","memberCount","reviewCount","location","price"],"name":"sitepage.sitemobile-pages-sitepage"}';   
          }
          
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepage.sitemobile-pages-sitepage',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => $params,
          'order' => 3,
      ));
    }
  }

  public function addSitepageManagePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_index_manage',
          'displayname' => 'Directory / Pages - Manage Pages',
          'title' => 'My Pages',
          'description' => 'This page lists a user\'s Pages\'s.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Pages i admin
  public function addSitepageManageAdminPage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_manageadmin_my-pages');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_manageadmin_my-pages',
          'displayname' => 'Directory / Pages - Manage Page (Pages I Admin)',
          'title' => 'Pages I Admin',
          'description' => 'This page lists a user\'s Pages\'s of which user\'s is admin.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Pages i like
  public function addSitepageManageLikePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_like_mylikes');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_like_mylikes',
          'displayname' => 'Directory / Pages - Manage Page (Pages I Like)',
          'title' => 'Pages I Like',
          'description' => 'This page lists a user\'s Pages\'s which user\'s likes.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Pages i joined
  public function addSitepageManageJoinedPage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_like_my-joined');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_like_my-joined',
          'displayname' => 'Directory / Pages - Manage Page (Pages I\'ve Joined)',
          'title' => "Pages I've Joined",
          'description' => 'This page lists a user\'s Pages\'s which user\'s have joined.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  public function addSitepagePages() {
//    $this->setDefaultWidgetForSitepage('content', 'pages');  //::- NOT ADD Widget on USER Profile Page at installation
//    $this->setDefaultWidgetForSitepage('tabletcontent', 'tabletpages');  //::- NOT ADD Widget on USER Profile Page at installation
  }

  public function setDefaultWidgetForSitepage($content, $pages) {
    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepage.profile-sitepage')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.profile-sitepage',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 11,
          'params' => '{"title":"Pages","titleCount":true}',
          'module' => 'sitepage'
      ));
    }
  }

}
