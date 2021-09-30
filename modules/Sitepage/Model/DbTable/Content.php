<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Content extends Engine_Db_Table
{

  protected $_serializedColumns = array('params');

  /**
   * Set profile page default widget in user content table without tab
   *
   * @param string $name
   * @param int $page_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setDefaultInfo($name = null, $contentpage_id, $title = null, $titleCount = null, $order = null, $params = null)
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    if( !empty($name) ) {
      $select = $this->select();
      $select_content = $select
        ->from($this->info('name'))
        ->where('contentpage_id = ?', $contentpage_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', $name)
        ->limit(1);
      $content = $select_content->query()->fetchAll();
      if( empty($content) ) {
        $select = $this->select();
        $select_container = $select
          ->from($this->info('name'), array('content_id'))
          ->where('contentpage_id = ?', $contentpage_id)
          ->where('type = ?', 'container')
          ->limit(1);
        $container = $select_container->query()->fetchAll();
        if( !empty($container) ) {
          $select = $this->select();
          $container_id = $container[0]['content_id'];
          $select_middle = $select
            ->from($this->info('name'))
            ->where('parent_content_id = ?', $container_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'middle')
            ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if( !empty($middle) ) {
            $select = $this->select();
            $middle_id = $middle[0]['content_id'];
            $select_tab = $select
              ->from($this->info('name'))
              ->where('type = ?', 'widget')
              ->where('name = ?', 'core.container-tabs')
              ->where('contentpage_id = ?', $contentpage_id)
              ->limit(1);
            $tab = $select_tab->query()->fetchAll();
            if( !empty($tab) ) {
              $tab_id = $tab[0]['content_id'];
            }
            if( $name != 'sitepageintegration.profile-items' ) {
              $contentWidget = $this->createRow();
              $contentWidget->contentpage_id = $contentpage_id;
              $contentWidget->type = 'widget';
              $contentWidget->name = $name;
              $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
              $contentWidget->order = $order;
              if( $params ) {
                $contentWidget->params = $params;
              } else {
                $contentWidget->params = '{"title":"' . $title . '","titleCount":' . $titleCount . '}';
              }
              $contentWidget->save();
            } else {
              $select = new Zend_Db_Select($db);
              $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview');
              $check_list = $select->query()->fetchObject();
              if( !empty($check_list) ) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();

                foreach( $results as $value ) {
                  $item_title = $value['item_title'];
                  $resource_type = $value['resource_type'] . '_' . $value['listingtype_id'];

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                    ->from('engine4_sitepage_content')
                    ->where('parent_content_id = ?', $tab_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitepageintegration.profile-items')
                    ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if( empty($info) ) {

                    // tab on profile
                    $db->insert('engine4_sitepage_content', array(
                      'contentpage_id' => $contentpage_id,
                      'type' => 'widget',
                      'name' => 'sitepageintegration.profile-items',
                      'parent_content_id' => $tab_id,
                      'order' => 999,
                      'params' => '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}',
                    ));
                  }
                  //}
                }
              }
              $this->tabpageintwidgetlayout('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);

              $this->tabpageintwidgetlayout('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $tab_id, $contentpage_id);
            }
          }
        }
      }
    }
  }

  /**
   * Set profile page default widget in user content table with tab
   *
   * @param string $name
   * @param int $page_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setContentDefaultInfoWithoutTab($name = null, $contentpage_id, $title = null, $titleCount = null, $order = null)
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    if( !empty($name) ) {
      $select = $this->select();
      $select_content = $select
        ->from($this->info('name'))
        ->where('contentpage_id = ?', $contentpage_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', $name)
        ->limit(1);
      $content = $select_content->query()->fetchAll();
      if( empty($content) ) {
        $select = $this->select();
        $select_container = $select
          ->from($this->info('name'), array('content_id'))
          ->where('contentpage_id = ?', $contentpage_id)
          ->where('type = ?', 'container')
          ->limit(1);
        $container = $select_container->query()->fetchAll();
        if( !empty($container) ) {
          $select = $this->select();
          $container_id = $container[0]['content_id'];
          $select_middle = $select
            ->from($this->info('name'))
            ->where('parent_content_id = ?', $container_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'middle')
            ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if( !empty($middle) ) {
            $middle_id = $middle[0]['content_id'];

            if( $name != 'sitepageintegration.profile-items' ) {
              $contentWidget = $this->createRow();
              $contentWidget->contentpage_id = $contentpage_id;
              $contentWidget->type = 'widget';
              $contentWidget->name = $name;
              $contentWidget->parent_content_id = ($middle_id);
              $contentWidget->order = $order;
              $contentWidget->params = '{"title":"' . $title . '" , "titleCount":"' . $titleCount . '"}';
              $contentWidget->save();
            } else {
              $select = new Zend_Db_Select($db);
              $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview');
              $check_list = $select->query()->fetchObject();
              if( !empty($check_list) ) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();

                foreach( $results as $value ) {
                  $item_title = $value['item_title'];
                  $resource_type = $value['resource_type'] . '_' . $value['listingtype_id'];
                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                    ->from('engine4_sitepage_content')
                    ->where('parent_content_id = ?', $middle_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitepageintegration.profile-items')
                    ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if( empty($info) ) {

                    // tab on profile
                    $db->insert('engine4_sitepage_content', array(
                      'contentpage_id' => $contentpage_id,
                      'type' => 'widget',
                      'name' => 'sitepageintegration.profile-items',
                      'parent_content_id' => $middle_id,
                      'order' => 999,
                      'params' => '{"title":"' . $item_title . '","resource_type":"' . $resource_type . '","nomobile":"0","name":"sitepageintegration.profile-items"}',
                    ));
                  }
                  // }
                }
              }
              $this->tabpageintwidgetlayout('document', '{"title":"Docuemts","resource_type":"document_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('sitestoreproduct', '{"title":"Products","resource_type":"sitestoreproduct_product_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);

              $this->tabpageintwidgetlayout('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitepageintegration.profile-items"}', $middle_id, $contentpage_id);
            }
          }
        }
      }
    }
  }

  /**
   * Set default widget in content pages table
   *
   * @param object $table
   * @param string $tablename
   * @param int $page_id
   * @param string $type
   * @param string $widgetname
   * @param int $middle_id
   * @param int $order
   * @param string $title
   * @param int $titlecount
   */
  public function setDefaultDataUserWidget($table, $tablename, $contentpage_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null)
  {
    $selectWidgetId = $this->select()
      ->where('contentpage_id =?', $contentpage_id)
      ->where('type = ?', $type)
      ->where('name = ?', $widgetname)
      ->where('parent_content_id = ?', $middle_id)
      ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if( empty($fetchWidgetContentId) ) {
      $contentWidget = $this->createRow();
      $contentWidget->contentpage_id = $contentpage_id;
      $contentWidget->type = $type;
      $contentWidget->name = $widgetname;
      $contentWidget->parent_content_id = $middle_id;
      $contentWidget->order = $order;
      if( empty($advanced_activity_params) ) {
        $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":$titlecount}";
      } else {
        $contentWidget->params = "$advanced_activity_params";
      }
      $contentWidget->save();
    }
  }

  /**
   * Set default widget in content pages table without tab
   *
   * @param int $page_id
   */
  public function setWithoutTabLayout($page_id, $sitepage_layout_cover_photo = 1)
  {

    // GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

    // GET CONTENT TABLE NAME
    $contentTableName = $this->info('name');

    //INSERTING MAIN CONTAINER
    $mainContainer = $this->createRow();
    $mainContainer->contentpage_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERTING MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $this->createRow();
    $mainMiddleContainer->contentpage_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERTING MAIN-LEFT CONTAINER
    $mainLeftContainer = $this->createRow();
    $mainLeftContainer->contentpage_id = $page_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    if( empty($sitepage_layout_cover_photo) ) {

      //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

      //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemember.pagecover-photo-sitepagemembers', $middle_id, 2, '', 'true');
      }

      //INSERTING TITLE WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.title-sitepage', $middle_id, 3, '', 'true');

      //INSERTING LIKE WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.like-button', $middle_id, 4, '', 'true');

      //INSERTING FACEBOOK LIKE WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', $middle_id, 5, '', 'true');
      }

      //INSERTING MAIN PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mainphoto-sitepage', $left_id, 10, '', 'true');
    } else {
      //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

      //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-cover-information-sitepage', $middle_id, 2, '', 'true');
    }

    //INSERTING CONTACT DETAIL WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.contactdetails-sitepage', $middle_id, 5, '', 'true');

//     //INSERTING PHOTO STRIP WIDGET
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
//       $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.photorecent-sitepage', $middle_id, 6, '', 'true');
//     }
    //INSERTING ACTIVITY FEED WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') ) {
      $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
    } else {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.feed', $middle_id, 6, 'Updates', 'true');
    }

    //INSERTING INFORAMTION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_id, 7, 'Info', 'true');

    //INSERTING ACTIVITY FEED WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') ) {
      $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
    } else {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
    }

    //INSERTING OVERVIEW WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.overview-sitepage', $middle_id, 8, 'Overview', 'true');

    //INSERTING LOCATION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.location-sitepage', $middle_id, 9, 'Map', 'true');

    //INSERTING LINKS WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

    //INSERTING WIDGET LINK WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.widgetlinks-sitepage', $left_id, 11, '', 'true');

    //INSERTING OPTIONS WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.options-sitepage', $left_id, 12, '', 'true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.write-page', $left_id, 13, '', 'true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.information-sitepage', $left_id, 10, 'Information', 'true');

    //INSERTING LIKE WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

    //INSERTING RATING WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', $left_id, 16, 'Ratings', 'true');
    }

    //INSERTING BADGE WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', $left_id, 17, 'Badge', 'true');
    }

    $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

    //INSERTING SOCIAL SHARE WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.socialshare-sitepage', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

    //INSERTING INSIGHTS WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 22, 'Insights', 'true');

    //INSERTING FEATURED OWNER WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 23, 'Owners', 'true');

    //INSERTING ALBUM WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 24, 'Albums', 'true');
      $this->setContentDefaultInfoWithoutTab('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
    }

    //INSERTING PAGE PROFILE PLAYER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 25, '', 'true');
    }

    //INSERTING LINKED PAGES WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 26, 'Linked Pages', 'true');

    //INSERTING VIDEO WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
    }

    //INSERTING VIDEO WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo') ) {
      $this->setContentDefaultInfoWithoutTab('sitevideo.contenttype-videos', $page_id, 'Videos', 'true', '111');
    }

    //INSERTING NOTE WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
    }

    //INSERTING REVIEW WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
    }

    //INSERTING FORM WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform') ) {
      $this->setContentDefaultInfoWithoutTab('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
    }

    //INSERTING DOCUMENT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
    }

    //INSERTING OFFER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') ) {
      $this->setContentDefaultInfoWithoutTab('sitepageoffer.profile-sitepageoffers', $page_id, 'Offers', 'true', '116');
    }

    //INSERTING EVENT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') ) {
      $this->setContentDefaultInfoWithoutTab('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent') ) {
      $this->setContentDefaultInfoWithoutTab('siteevent.contenttype-events', $page_id, 'Events', 'true', '117');
    }

    //INSERTING POLL WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
    }

    //INSERTING DISCUSSION WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') ) {
      $this->setContentDefaultInfoWithoutTab('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
    }

    //INSERTING NOTE WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
    }

    //INSERTING TWITTER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
    }

    //INSERTING MEMBER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
      $this->setContentDefaultInfoWithoutTab('sitepagemember.profile-sitepagemembers', $page_id, 'Members', 'true', '122');
      $this->setContentDefaultInfoWithoutTab('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '122');
    }

    //INSERTING MEMBER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') ) {
      $this->setContentDefaultInfoWithoutTab('sitepageintegration.profile-items', $page_id, '', '', 999);
    }
    //INSERTING CROWDFUNDING PROJECT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding') ) {
      $this->setContentDefaultInfoWithoutTab('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '125');
    }
    //INSERTING SITEMUSIC PROJECT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic') ) {
      $this->setContentDefaultInfoWithoutTab('sitemusic.contenttype-music', $page_id, 'Music', 'true', '126');
    }
  }

  public function setTabbedLayout($page_id, $sitepage_layout_cover_photo = 1)
  {

    //SHOW HOW MANY TAB SHOULD BE SHOW IN THE PAGE PROFILE PAGE BEFORE MORE LINK
    $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);

    // GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

    // GET CONTENT TABLE NAME
    $contentTableName = $this->info('name');

    //INSERTING MAIN CONTAINER
    $mainContainer = $this->createRow();
    $mainContainer->contentpage_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERTING MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $this->createRow();
    $mainMiddleContainer->contentpage_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERTING MAIN-LEFT CONTAINER
    $mainLeftContainer = $this->createRow();
    $mainLeftContainer->contentpage_id = $page_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    //INSERTING MAIN-MIDDLE-TAB CONTAINER
    $middleTabContainer = $this->createRow();
    $middleTabContainer->contentpage_id = $page_id;
    $middleTabContainer->type = 'widget';
    $middleTabContainer->name = 'core.container-tabs';
    $middleTabContainer->parent_content_id = $middle_id;
    $middleTabContainer->order = 10;
    $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
    $middleTabContainer->save();
    $middle_tab = $middleTabContainer->content_id;

    //INSERTING THUMB PHOTO WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.thumbphoto-sitepage', $middle_id, 3, '', 'true');

    if( empty($sitepage_layout_cover_photo) ) {

      //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

      //INSERTING THUMB PHOTO WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemember.pagecover-photo-sitepagemembers', $middle_id, 2, '', 'true');
      }

      //INSERTING TITLE WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.title-sitepage', $middle_id, 4, '', 'true');

      //INSERTING LIKE WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

      //INSERTING FACEBOOK LIKE WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', $middle_id, 6, '', 'true');
      }

      //INSERTING MAIN PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mainphoto-sitepage', $left_id, 10, '', 'true');
    } else {


      //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

      //INSERTING THUMB PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-cover-information-sitepage', $middle_id, 2, '', 'true');
    }

    //INSERTING CONTACT DETAIL WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.contactdetails-sitepage', $middle_id, 7, '', 'true');

//     //INSERTING PHOTO STRIP WIDGET
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
//       $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.photorecent-sitepage', $middle_id, 8, '', 'true');
//     }
    //INSERTING OPTIONS WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.options-sitepage', $left_id, 11, '', 'true');

    //INSERTING INFORMATION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.information-sitepage', $left_id, 10, 'Information', 'true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

    //INSERTING RATING WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', $left_id, 16, 'Ratings', 'true');
    }

    //INSERTING BADGE WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', $left_id, 17, 'Badge', 'true');
    }

    //INSERTING YOU MAY ALSO LIKE WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.suggestedpage-sitepage', $left_id, 18, 'You May Also Like', 'true');

    $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

    //INSERTING SOCIAL SHARE WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.socialshare-sitepage', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

    //INSERTING INSIGHTS WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 21, 'Insights', 'true');

    //INSERTING FEATURED OWNER WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 22, 'Owners', 'true');

    //INSERTING ALBUM WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 23, 'Albums', 'true');
    }

    //INSERTING PAGE PROFILE PLAYER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 24, '', 'true');
    }

    //INSERTING LINKED PAGES WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 25, 'Linked Pages', 'true');

    //INSERTING ACTIVITY FEED WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') ) {
      $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
    } else {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.feed', $middle_tab, 2, 'Updates', 'true');
    }

    //INSERTING INFORAMTION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_tab, 3, 'Info', 'true');
    //INSERTING ACTIVITY FEED WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') ) {
      $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
    } else {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
    }

    //INSERTING OVERVIEW WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.overview-sitepage', $middle_tab, 4, 'Overview', 'true');

    //INSERTING LOCATION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.location-sitepage', $middle_tab, 5, 'Map', 'true');

    //INSERTING LINKS WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

    //INSERTING ALBUM WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
      $this->setDefaultInfo('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
    }

    //INSERTING VIDEO WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') ) {
      $this->setDefaultInfo('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
    }

    //INSERTING VIDEO WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo') ) {
      $this->setDefaultInfo('sitevideo.contenttype-videos', $page_id, 'Videos', 'true', '111');
    }

    //INSERTING NOTE WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') ) {
      $this->setDefaultInfo('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
    }

    //INSERTING REVIEW WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
      $this->setDefaultInfo('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
    }

    //INSERTING FORM WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform') ) {
      $this->setDefaultInfo('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
    }

    //INSERTING DOCUMENT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') ) {
      $this->setDefaultInfo('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
    }

    //INSERTING OFFER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') ) {
      $this->setDefaultInfo('sitepageoffer.profile-sitepageoffers', $page_id, 'Offers', 'true', '116');
    }

    //INSERTING EVENT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') ) {
      $this->setDefaultInfo('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent') ) {
      $this->setDefaultInfo('siteevent.contenttype-events', $page_id, 'Events', 'true', '117');
    }

    //INSERTING POLL WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') ) {
      $this->setDefaultInfo('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
    }

    //INSERTING DISCUSSION WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') ) {
      $this->setDefaultInfo('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
    }

    //INSERTING MUSIC WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
      $this->setDefaultInfo('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
    }

    //INSERTING TWITTER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter') ) {
      $this->setDefaultInfo('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
    }

    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
      $this->setDefaultInfo('sitepagemember.profile-sitepagemembers', $page_id, 'Members', 'true', '122');
      $this->setDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '122');
    }

    //INSERTING MEMBER WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') ) {

      $this->setDefaultInfo('sitepageintegration.profile-items', $page_id, '', '', 999);
    }
    //INSERTING CROWDFUNDING PROJECT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding') ) {
      $this->setDefaultInfo('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '125');
    }
    //INSERTING SITEMUSIC PROJECT WIDGET
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic') ) {
      $this->setDefaultInfo('sitemusic.contenttype-music', $page_id, 'Music', 'true', '126');
    }
  }

  public function setContentDefault($page_id, $sitepage_layout_cover_photo = 1)
  {

    $pagelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.setting', 1);
    $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);

    if( $pagelayout ) {
      //GET CONTENT TABLE
      $contentTable = Engine_Api::_()->getDbtable('content', 'sitepage');

      //GET CONTENT TABLE NAME
      $contentTableName = $this->info('name');

      //INSERTING MAIN CONTAINER
      $mainContainer = $this->createRow();
      $mainContainer->contentpage_id = $page_id;
      $mainContainer->type = 'container';
      $mainContainer->name = 'main';
      $mainContainer->order = 2;
      $mainContainer->save();
      $container_id = $mainContainer->content_id;

      //INSERTING MAIN-MIDDLE CONTAINER
      $mainMiddleContainer = $this->createRow();
      $mainMiddleContainer->contentpage_id = $page_id;
      $mainMiddleContainer->type = 'container';
      $mainMiddleContainer->name = 'middle';
      $mainMiddleContainer->parent_content_id = $container_id;
      $mainMiddleContainer->order = 6;
      $mainMiddleContainer->save();
      $middle_id = $mainMiddleContainer->content_id;

      //INSERTING MAIN-LEFT CONTAINER
      $mainLeftContainer = $this->createRow();
      $mainLeftContainer->contentpage_id = $page_id;
      $mainLeftContainer->type = 'container';
      $mainLeftContainer->name = 'right';
      $mainLeftContainer->parent_content_id = $container_id;
      $mainLeftContainer->order = 4;
      $mainLeftContainer->save();
      $left_id = $mainLeftContainer->content_id;

      //INSERTING MAIN-MIDDLE TAB CONTAINER
      $middleTabContainer = $this->createRow();
      $middleTabContainer->contentpage_id = $page_id;
      $middleTabContainer->type = 'widget';
      $middleTabContainer->name = 'core.container-tabs';
      $middleTabContainer->parent_content_id = $middle_id;
      $middleTabContainer->order = 10;
      $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
      $middleTabContainer->save();
      $middle_tab = $middleTabContainer->content_id;

      //INSERTING THUMB PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.thumbphoto-sitepage', $middle_id, 3, '', 'true');

      if( empty($sitepage_layout_cover_photo) ) {

        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

        //INSERTING THUMB PHOTO WIDGET
        if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
          $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemember.pagecover-info-sitepagemembers', $middle_id, 2, '', 'true');
        }

        //INSERTING TITLE WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.title-sitepage', $middle_id, 4, '', 'true');

        //INSERTING LIKE WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

        //INSERTING FACEBOOK LIKE WIDGET
        if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse') ) {
          $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', $middle_id, 6, '', 'true');
        }

        //INSERTING MAIN PHOTO WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mainphoto-sitepage', $left_id, 10, '', 'true');
      } else {
        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

        //INSERTING THUMB PHOTO WIDGET
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-cover-information-sitepage', $middle_id, 2, '', 'true');
      }

      //INSERTING CONTACT DETAIL WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.contactdetails-sitepage', $middle_id, 7, '', 'true');

//       //INSERTING PHOTO STRIP WIDGET
//       if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
//         $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.photorecent-sitepage', $middle_id, 8, '', 'true');
//       }
      //INSERTING OPTIONS WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.options-sitepage', $left_id, 11, '', 'true');

      //INSERTING INFORMATION WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.information-sitepage', $left_id, 10, 'Information', 'true');

      //INSERTING WRITE SOMETHING ABOUT WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

      //INSERTING RATING WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', $left_id, 16, 'Ratings', 'true');
      }

      //INSERTING BADGE WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', $left_id, 17, 'Badge', 'true');
      }

      $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

      //INSERTING YOU MAY ALSO LIKE WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.suggestedpage-sitepage', $left_id, 18, 'You May Also Like', 'true', $social_share_default_code);

      //INSERTING SOCIAL SHARE WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.socialshare-sitepage', $left_id, 19, 'Social Share', 'true');

      //INSERTING INSIGHTS WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 21, 'Insights', 'true');

      //INSERTING FEATURED OWNER WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 22, 'Owners', 'true');
      //INSERTING INSIGHTS WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 21, 'Insights', 'true');

      //INSERTING FEATURED OWNER WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 22, 'Owners', 'true');

      //INSERTING ALBUM WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 23, 'Albums', 'true');
      }

      //INSERTING PAGE PROFILE PLAYER WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 24, '', 'true');
      }

      //INSERTING LINKED PAGES WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 25, 'Linked Pages', 'true');

      //INSERTING ACTIVITY FEED WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') ) {
        $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
      } else {
        $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
      }

      //INSERTING INFORAMTION WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_tab, 3, 'Info', 'true');

      //INSERTING OVERVIEW WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.overview-sitepage', $middle_tab, 4, 'Overview', 'true');

      //INSERTING LOCATION WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.location-sitepage', $middle_tab, 5, 'Map', 'true');

      //INSERTING LINKS WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $page_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

      //INSERTING ALBUM WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
        $this->setDefaultInfo('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');
      }

      //INSERTING VIDEO WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') ) {
        $this->setDefaultInfo('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');
      }

      //INSERTING NOTE WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') ) {
        $this->setDefaultInfo('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');
      }

      //INSERTING REVIEW WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
        $this->setDefaultInfo('sitepagereview.profile-sitepagereviews', $page_id, 'Reviews', 'true', '113');
      }

      //INSERTING FORM WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform') ) {
        $this->setDefaultInfo('sitepageform.sitepage-viewform', $page_id, 'Form', 'false', '114');
      }

      //INSERTING DOCUMENT WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') ) {
        $this->setDefaultInfo('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
      }

      //INSERTING OFFER WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') ) {
        $this->setDefaultInfo('sitepageoffer.profile-sitepageoffers', $page_id, 'Offer', 'true', '116');
      }

      //INSERTING EVENT WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') ) {
        $this->setDefaultInfo('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');
      }

      //INSERTING POLL WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') ) {
        $this->setDefaultInfo('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
      }

      //INSERTING DISCUSSION WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') ) {
        $this->setDefaultInfo('sitepage.discussion-sitepage', $page_id, 'Discussions', 'true', '119');
      }

      //INSERTING MUSIC WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
        $this->setDefaultInfo('sitepagemusic.profile-sitepagemusic', $page_id, 'Music', 'true', '120');
      }
      //INSERTING TWITTER WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter') ) {
        $this->setDefaultInfo('sitepagetwitter.feeds-sitepagetwitter', $page_id, 'Twitter', 'true', '121');
      }

      //INSERTING MEMBER WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
        $this->setDefaultInfo('sitepagemember.profile-sitepagemembers', $page_id, 'Members', 'true', '122');
        $this->setDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');
      }

      //INSERTING MEMBER WIDGET
      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') ) {
        $this->setDefaultInfo('sitepageintegration.profile-items', $page_id, '', '', 999);
      }
    } else {
      $this->setWithoutTabLayout($page_id, $sitepage_layout_cover_photo);
    }
  }

  /**
   * Gets content id,parama,name
   *
   * @param int $contentpage_id
   * @return content id,parama,name
   */
  public function getContents($contentpage_id)
  {

    $selectPageAdmin = $this->select()
      ->from($this->info('name'), array('content_id', 'params', 'name'))
      ->where('contentpage_id =?', $contentpage_id)
      ->where("name IN ('sitepage.overview-sitepage', 'sitepage.photos-sitepage', 'sitepage.discussion-sitepage', 'sitepagenote.profile-sitepagenotes', 'sitepagepoll.profile-sitepagepolls', 'sitepageevent.profile-sitepageevents', 'sitepagevideo.profile-sitepagevideos', 'sitepageoffer.profile-sitepageoffers', 'sitepagereview.profile-sitepagereviews', 'sitepagedocument.profile-sitepagedocuments', 'sitepageform.sitepage-viewform','sitepage.info-sitepage', 'seaocore.feed','advancedactivity.home-feeds', 'activity.feed', 'sitepage.location-sitepage', 'core.profile-links', 'sitepagemusic.profile-sitepagemusic', 'sitepagemember.profile-sitepagemembers', 'sitepageintegration.profile-items','sitepagetwitter.feeds-sitepagetwitter', 'siteevent-contenttype-events')");
    return $this->fetchAll($selectPageAdmin);
  }

  /**
   * Gets content_id
   *
   * @param int $contentpage_id
   * @return $params
   */
  public function getContentId($contentpage_id, $sitepage)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if( !empty($viewer_id) ) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = 0;
    }
    $itemAlbumCount = 10;
    $itemPhotoCount = 100;
    $select = $this->select();
    $select_content = $select
      ->from($this->info('name'))
      ->where('contentpage_id = ?', $contentpage_id)
      ->where('type = ?', 'container')
      ->where('name = ?', 'main')
      ->limit(1);
    $content = $select_content->query()->fetchAll();
    if( !empty($content) ) {
      $select = $this->select();
      $select_container = $select
        ->from($this->info('name'), array('content_id'))
        ->where('contentpage_id = ?', $contentpage_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->where("name NOT IN ('	sitepage.title-sitepage', 'seaocore.like-button', 'sitepage.photorecent-sitepage')")
        ->limit(1);
      $container = $select_container->query()->fetchAll();
      if( !empty($container) ) {
        $select = $this->select();
        $container_id = $container[0]['content_id'];
        $select_middle = $select
          ->from($this->info('name'))
          ->where('parent_content_id = ?', $container_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->where('contentpage_id = ?', $contentpage_id)
          ->limit(1);
        $middle = $select_middle->query()->fetchAll();
        if( !empty($middle) ) {
          $content_id = $middle[0]['content_id'];
        } else {
          $content_id = $container_id;
        }
      }
    }

    if( !empty($content_id) ) {
      $select = $this->select();
      $select_middle = $select
        ->from($this->info('name'), array('content_id', 'name', 'params'))
        ->where('parent_content_id = ?', $content_id)
        ->where('type = ?', 'widget')
        ->where("name NOT IN ('sitepage.title-sitepage', 'seaocore.like-button', 'sitepage.photorecent-sitepage', 'Facebookse.facebookse-commonlike', 'sitepage.thumbphoto-sitepage')")
        ->where('contentpage_id = ?', $contentpage_id)
        ->order('order')
      ;

      $select = $this->select();
      $select_photo = $select
        ->from($this->info('name'), array('params'))
        ->where('parent_content_id = ?', $content_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'sitepage.photos-sitepage')->where('contentpage_id = ?', $contentpage_id)
        ->order('content_id ASC');

      $middlePhoto = $select_photo->query()->fetchColumn();
      if( !empty($middlePhoto) ) {
        $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
        if( isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray) ) {
          $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
        }
        if( isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray) ) {
          $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
        }
      }

      $middle = $select_middle->query()->fetchAll();
      $editpermission = '';
      $isManageAdmin = '';
      $content_ids = '';
      $content_names = '';
      $resource_type_integration = 0;
      $ads_display_integration = 0;
      $flag = false;
      foreach( $middle as $value ) {
        $content_name = $value['name'];
        switch( $content_name ) {
          case 'sitepage.overview-sitepage':
            if( !empty($sitepage) ) {
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
              if( !empty($isManageAdmin) ) {
                $editpermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                if( !empty($editpermission) && empty($sitepage->overview) ) {
                  $flag = true;
                } elseif( empty($editpermission) && empty($sitepage->overview) ) {
                  $flag = false;
                } elseif( !empty($editpermission) && !empty($sitepage->overview) ) {
                  $flag = true;
                } elseif( empty($editpermission) && !empty($sitepage->overview) ) {
                  $flag = true;
                }
              }
            }
            break;
          case 'sitepage.location-sitepage':
            if( !empty($sitepage) ) {
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
              if( !empty($isManageAdmin) ) {
                $value['id'] = $sitepage->getIdentity();
                $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
                if( !empty($location) ) {
                  $flag = true;
                }
              }
            }
            break;
          case 'core.html-block':
            $flag = true;
            break;
          case 'advancedactivity.home-feeds':
            $flag = true;
            break;
          case 'activity.feed':
            $flag = true;
            break;
          case 'seaocore.feed':
            $flag = true;
            break;
          case 'sitepage.info-sitepage':
            $flag = true;
            break;
          case 'core.profile-links':
            $flag = true;
            break;
          case 'sitepagenote.profile-sitepagenotes':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL NOTES
              $noteCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagenote', 'notes');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
              if( !empty($isManageAdmin) || !empty($noteCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepageevent.profile-sitepageevents':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL EVENTS
              $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageevent', 'events');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
              if( !empty($isManageAdmin) || !empty($eventCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'siteevent.contenttype-events':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL EVENTS
              $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'siteevent', 'events');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
              if( !empty($isManageAdmin) || !empty($eventCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'document.contenttype-documents':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL DOCUMENTS
              $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'document', 'documents');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
              if( !empty($isManageAdmin) || !empty($documentCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitevideo.contenttype-videos':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL videoS
              $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitevideo', 'videos');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
              if( !empty($isManageAdmin) || !empty($videoCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepage.discussion-sitepage':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL TOPICS
              $topicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'topics');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
              if( !empty($isManageAdmin) || !empty($topicCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepage.photos-sitepage':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL ALBUMS
              $albumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
              if( !empty($isManageAdmin) || !empty($albumCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepagemusic.profile-sitepagemusic':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemusic") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'samcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL PLAYLISTS
              $musicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagemusic', 'playlists');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'samcreate');
              if( !empty($isManageAdmin) || !empty($musicCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;

          case 'sitepagemember.profile-sitepagemembers':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL PLAYLISTS
              $memberCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagemember', 'membership');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smecreate');
              if( !empty($isManageAdmin) || !empty($memberCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;

          case 'sitepagedocument.profile-sitepagedocuments':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL DOCUMENTS
              $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagedocument', 'documents');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
              if( !empty($isManageAdmin) || !empty($documentCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepagereview.profile-sitepagereviews':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
              //TOTAL REVIEW
              $reviewCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagereview', 'reviews');
              $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepagereview_review', 'create');
              if( !empty($level_allow) || !empty($reviewCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepagevideo.profile-sitepagevideos':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL VIDEO
              $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagevideo', 'videos');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
              if( !empty($isManageAdmin) || !empty($videoCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepagepoll.profile-sitepagepolls':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL POLL
              $pollCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagepoll', 'polls');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
              if( !empty($isManageAdmin) || !empty($pollCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepageoffer.profile-sitepageoffers':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageoffer") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'offer');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL OFFERS
              $can_edit = 1;
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
              if( empty($isManageAdmin) ) {
                $can_edit = 0;
              }

              $can_offer = 1;
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'offer');

              if( empty($isManageAdmin) ) {
                $can_offer = 0;
              }

              $can_create_offer = '';

              //OFFER CREATION AUTHENTICATION CHECK
              if( $can_edit == 1 && $can_offer == 1 ) {
                $can_create_offer = 1;
              }

              //TOTAL OFFER
              $offerCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageoffer', 'offers');
              if( !empty($can_create_offer) || !empty($offerCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitepageform.sitepage-viewform':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageform") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'form');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
            }
            break;
          case 'sitepageintegration.profile-items':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') ) {
              $content_params = $value['params'];
              $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
              $resource_type_integration = $paramsDecodedArray['resource_type'];
              $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);

              //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", $resource_type_integration) ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, $resource_type_integration);
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
            }
            $resource_type = $resource_type_integration;

            $pieces = explode("_", $resource_type);
            if( $resource_type == 'document_0' || $resource_type == 'folder_0' || $resource_type == 'quiz_0' ) {
              $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[1];
              $paramsIntegration['resource_type'] = $resource_type = $pieces[0];
            } else {
              $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[2];
              $paramsIntegration['resource_type'] = $resource_type = $pieces[0] . '_' . $pieces[1];
            }

            $paramsIntegration['page_id'] = $sitepage->page_id;
            $paginator = Engine_Api::_()->getDbtable('contents', 'sitepageintegration')->getResults($paramsIntegration);
            if( $paginator->getTotalItemCount() <= 0 ) {
              $flag = false;
            } else {
              $flag = true;
            }
            break;
          case 'sitepagetwitter.feeds-sitepagetwitter':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter') ) {
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'twitter');
              if( !empty($isManageAdmin) ) {
                $flag = true;
              }
            }
            break;
          case 'sitecrowdfunding.contenttype-projects':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitecrowdfunding") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sprcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              //TOTAL videoS
              $projectCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitecrowdfunding', 'projects');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sprcreate');
              if( !empty($isManageAdmin) || !empty($projectCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }

            case 'sitemusic.contenttype-music':
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic') ) {
              if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
                if( Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitemusic") == 1 ) {
                  $flag = true;
                }
              } else {
                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'samcreate');
                if( !empty($isPageOwnerAllow) ) {
                  $flag = true;
                }
              }
              $musicAlbumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitemusic', 'music');
              $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'samcreate');
              if( !empty($isManageAdmin) || !empty($musicAlbumCount) ) {
                $flag = true;
              } else {
                $flag = false;
              }
            }

            $middle = $select_middle->query()->fetchAll();
            $editpermission = '';
            $isManageAdmin = '';
            $content_ids = '';
            $content_names = '';
            $resource_type_integration = 0;
            $ads_display_integration = 0;
            $flag = false;
            foreach ($middle as $value) {
                $content_name = $value['name'];
                switch ($content_name) {
                    case 'sitepage.overview-sitepage':
                        if (!empty($sitepage)) {
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
                            if (!empty($isManageAdmin)) {
                                $editpermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                                if (!empty($editpermission) && empty($sitepage->overview)) {
                                    $flag = true;
                                } elseif (empty($editpermission) && empty($sitepage->overview)) {
                                    $flag = false;
                                } elseif (!empty($editpermission) && !empty($sitepage->overview)) {
                                    $flag = true;
                                } elseif (empty($editpermission) && !empty($sitepage->overview)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'sitepage.location-sitepage':
                        if (!empty($sitepage)) {
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
                            if (!empty($isManageAdmin)) {
                                $value['id'] = $sitepage->getIdentity();
                                $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
                                if (!empty($location)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'core.html-block':
                        $flag = true;
                        break;
                    case 'advancedactivity.home-feeds':
                        $flag = true;
                        break;
                    case 'activity.feed':
                        $flag = true;
                        break;
                    case 'seaocore.feed':
                        $flag = true;
                        break;
                    case 'sitepage.info-sitepage':
                        $flag = true;
                        break;
                    case 'core.profile-links':
                        $flag = true;
                        break;
                    case 'sitepagenote.profile-sitepagenotes':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL NOTES
                            $noteCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagenote', 'notes');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
                            if (!empty($isManageAdmin) || !empty($noteCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepageevent.profile-sitepageevents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL EVENTS
                            $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageevent', 'events');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
                            if (!empty($isManageAdmin) || !empty($eventCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'siteevent.contenttype-events':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL EVENTS
                            $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'siteevent', 'events');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
                            if (!empty($isManageAdmin) || !empty($eventCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                         case 'document.contenttype-documents':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL DOCUMENTS
                            $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'document', 'documents');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
                            if (!empty($isManageAdmin) || !empty($documentCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitevideo.contenttype-videos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL videoS
                            $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitevideo', 'videos');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
                            if (!empty($isManageAdmin) || !empty($videoCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepage.discussion-sitepage':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL TOPICS
                            $topicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'topics');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
                            if (!empty($isManageAdmin) || !empty($topicCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepage.photos-sitepage':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL ALBUMS
                            $albumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
                            if (!empty($isManageAdmin) || !empty($albumCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagemusic.profile-sitepagemusic':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemusic") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PLAYLISTS
                            $musicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagemusic', 'playlists');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smcreate');
                            if (!empty($isManageAdmin) || !empty($musicCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                    case 'sitepagemember.profile-sitepagemembers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL PLAYLISTS
                            $memberCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'membership');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smecreate');
                            if (!empty($isManageAdmin) || !empty($memberCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                    case 'sitepagedocument.profile-sitepagedocuments':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL DOCUMENTS
                            $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagedocument', 'documents');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
                            if (!empty($isManageAdmin) || !empty($documentCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagereview.profile-sitepagereviews':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                            //TOTAL REVIEW
                            $reviewCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagereview', 'reviews');
                            $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepagereview_review', 'create');
                            if (!empty($level_allow) || !empty($reviewCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagevideo.profile-sitepagevideos':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL VIDEO
                            $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagevideo', 'videos');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
                            if (!empty($isManageAdmin) || !empty($videoCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepagepoll.profile-sitepagepolls':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL POLL
                            $pollCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagepoll', 'polls');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
                            if (!empty($isManageAdmin) || !empty($pollCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepageoffer.profile-sitepageoffers':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageoffer") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'offer');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL OFFERS
                            $can_edit = 1;
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                            if (empty($isManageAdmin)) {
                                $can_edit = 0;
                            }

                            $can_offer = 1;
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'offer');

                            if (empty($isManageAdmin)) {
                                $can_offer = 0;
                            }

                            $can_create_offer = '';

                            //OFFER CREATION AUTHENTICATION CHECK
                            if ($can_edit == 1 && $can_offer == 1) {
                                $can_create_offer = 1;
                            }

                            //TOTAL OFFER
                            $offerCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageoffer', 'offers');
                            if (!empty($can_create_offer) || !empty($offerCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;
                    case 'sitepageform.sitepage-viewform':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageform") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'form');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                        }
                        break;
                    case 'sitepageintegration.profile-items':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
                            $content_params = $value['params'];
                            $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
                            $resource_type_integration = $paramsDecodedArray['resource_type'];
                            $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);

                            //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", $resource_type_integration)) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, $resource_type_integration);
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
                        }
                        $resource_type = $resource_type_integration;

                        $pieces = explode("_", $resource_type);
                        if ($resource_type == 'document_0' || $resource_type == 'folder_0' || $resource_type == 'quiz_0') {
                            $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[1];
                            $paramsIntegration['resource_type'] = $resource_type = $pieces[0];
                        } else {
                            $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[2];
                            $paramsIntegration['resource_type'] = $resource_type = $pieces[0] . '_' . $pieces[1];
                        }

                        $paramsIntegration['page_id'] = $sitepage->page_id;
                        $paginator = Engine_Api::_()->getDbtable('contents', 'sitepageintegration')->getResults($paramsIntegration);
                        if ($paginator->getTotalItemCount() <= 0) {
                            $flag = false;
                        } else {
                            $flag = true;
                        }
                        break;
                    case 'sitepagetwitter.feeds-sitepagetwitter':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'twitter');
                            if (!empty($isManageAdmin)) {
                                $flag = true;
                            }
                        }
                        break;
                    case 'sitecrowdfunding.contenttype-projects':
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding')) {
                            if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitecrowdfunding") == 1) {
                                    $flag = true;
                                }
                            } else {
                                $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sprcreate');
                                if (!empty($isPageOwnerAllow)) {
                                    $flag = true;
                                }
                            }
                            //TOTAL videoS
                            $projectCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitecrowdfunding', 'projects');
                            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sprcreate');
                            if (!empty($isManageAdmin) || !empty($projectCount)) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        }
                        break;

                }
              }
        }
        if( !empty($flag) ) {
          $content_ids = $value['content_id'];
          $content_names = $value['name'];
          break;
        }
      }
    }

    return array('content_id' => $content_ids, 'content_name' => $content_names, 'itemAlbumCount' => $itemAlbumCount, 'itemPhotoCount' => $itemPhotoCount, 'resource_type_integration' => $resource_type_integration, 'ads_display_integration' => $ads_display_integration);
  }

  /**
   * Gets content_id, name
   *
   * @param int $contentpage_id
   * @return content_id, name
   */
  public function getContentInformation($contentpage_id)
  {
    $select = $this->select()->from($this->info('name'), array('content_id', 'name'))
        ->where("name IN ('sitepage.info-sitepage', 'seaocore.feed', 'advancedactivity.home-feeds','activity.feed', 'sitepage.location-sitepage', 'core.profile-links', 'core.html-block')")
        ->where('contentpage_id = ?', $contentpage_id)->order('content_id ASC');

    return $this->fetchAll($select);
  }

  /**
   * Gets content_id, name
   *
   * @param int $contentpage_id
   * @param int $name
   * @return content_id, name
   */
  public function getContentByWidgetName($name, $contentpage_id)
  {
    $select = $this->select()->from($this->info('name'), array('content_id', 'name'))
      ->where('name =?', $name)
      ->where('contentpage_id = ?', $contentpage_id)
      ->limit(1)
    ;
    return $this->fetchAll($select)->toarray();
  }

  /**
   * Gets name
   *
   * @param int $tab_main
   * @return name
   */
  public function getCurrentTabName($tab_main = null)
  {
    if( empty($tab_main) ) {
      return;
    }
    $current_tab_name = $this->select()
      ->from($this->info('name'), array('name'))
      ->where('content_id = ?', $tab_main)
      ->query()
      ->fetchColumn();
    return $current_tab_name;
  }

  public function checkWidgetExist($page_id = 0, $widgetName)
  {

    $params = $this->select()
        ->from($this->info('name'), 'params')
        ->where('contentpage_id = ?', $page_id)
        ->where('name = ?', $widgetName)
        ->where('type = ?', 'widget')
        ->query()->fetchColumn();
    return $params;
  }

  public function tabpageintwidgetlayout($module_name, $params, $tab_id, $page_id)
  {

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_modules')
      ->where('name = ?', $module_name);
    $module_enable = $select->query()->fetchObject();

    if( !empty($module_enable) ) {

      $results = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();

      foreach( $results as $value ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_sitepage_content')
          ->where('parent_content_id = ?', $tab_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sitepageintegration.profile-items')
          ->where('params = ?', $params);
        $info = $select->query()->fetch();
        if( empty($info) ) {

          // tab on profile
          $db->insert('engine4_sitepage_content', array(
            'contentpage_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepageintegration.profile-items',
            'parent_content_id' => $tab_id,
            'order' => 999,
            'params' => $params,
          ));
        }
      }
    }
  }

}

?>