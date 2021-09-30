<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function __call($method, $params)
  {
    /*
     * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
     * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
     * REMEMBER:
     *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
     *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
     */
    if( !empty($method) && $method == 'Sitemember_Form_Admin_Global' ) {
      if( !empty($params) && isset($params[0]) && isset($params[1]) && !empty($params[0]) ) {
        $form = $params[0];
        $isformvalid = $form->isValid($params[1]);
        if( !empty($isformvalid) ) {
          $this->_ISFORMVALID = true;
        } else {
          return false;
        }
      }
    }
    return true;
  }

  //ACTION FOR SAVE THE GOLBAL SETTINGS
  public function indexAction()
  {

    $previousProfileTemplate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.profiletemplate', 'default');

    $pluginName = 'sitemember';
    if( !empty($_POST[$pluginName . '_lsettings']) )
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);

    include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license1.php';


    if( $this->getRequest()->isPost() && !empty($this->_ISFORMVALID) ) {

      $db = Zend_Db_Table_Abstract::getDefaultAdapter();

      $containerCount = 0;
      $widgetCount = 0;
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $select = new Zend_Db_Select($db);
      $browsemember = $select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitemember_location_userby-locations')
        ->query()
        ->fetchObject();
      $page_id = $browsemember->page_id;
      if( $browsemember && $page_id ) {
        if( isset($_POST['sitemember_profiletemplate']) && !empty($_POST['sitemember_profiletemplate']) && !empty($previousProfileTemplate) && $_POST['sitemember_profiletemplate'] != $previousProfileTemplate && $_POST['sitemember_profiletemplate'] == 'default' ) {
          $db->delete('engine4_core_content', array('page_id =?' => $page_id));
          //CONTAINERS
          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'main',
            'parent_content_id' => Null,
            'order' => 2,
            'params' => '',
          ));
          $container_id = $db->lastInsertId('engine4_core_content');
          if( !empty($container_id) ) {
            //INSERT MAIN - MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => $container_id,
              'order' => 2,
              'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');
            if( !empty($middle_id) ) {
              //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
              $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemember.navigation-sitemember',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"0":"","title":"","titleCount":true}',
              ));

              $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemember.search-sitemember',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":true,"viewType":"horizontal","locationDetection":"0","whatWhereWithinmile":"1","advancedSearch":"0","nomobile":"0","name":"sitemember.search-sitemember"}',
              ));

              $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemember.browse-members-sitemember',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"","titleCount":true,"layouts_views":["1","2","3","4"],"layouts_order":"2","columnWidth":"202","truncationGrid":"50","columnHeight":"190","has_photo":"1","links":"","showDetailLink":"1","memberInfo":["ratingStar","location","directionLink","memberStatus","profileField","age"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"featured","show_content":"3","withoutStretch":"0","show_buttons":["facebook","twitter","pinit","like"],"pinboarditemWidth":"255","sitemember_map_sponsored":"1","itemCount":"20","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
              ));
            }
          }
        } elseif( isset($_POST['sitemember_profiletemplate']) && !empty($_POST['sitemember_profiletemplate']) && !empty($previousProfileTemplate) && $_POST['sitemember_profiletemplate'] != $previousProfileTemplate && $_POST['sitemember_profiletemplate'] == 'listview' ) {
          $db->delete('engine4_core_content', array('page_id =?' => $page_id));
          //TOP CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
          ));
          $top_container_id = $db->lastInsertId();

          //TOP-MIDDLE CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => 6,
          ));
          $top_middle_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.navigation-sitemember',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
          ));
          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.searchbox-sitemember',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","profileTypeElement","locationElement","locationmilesSearch"],"textWidth":"390","locationWidth":"300","locationmilesWidth":"200","categoryWidth":"200","nomobile":"0","name":"sitemember.searchbox-sitemember"}',
          ));
          //MAIN CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => 2,
          ));
          $main_container_id = $db->lastInsertId();

          //MAIN-MIDDLE CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 6,
          ));
          $main_middle_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.browse-members-sitemember',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","columnWidth":"180","truncationGrid":"16","columnHeight":"328","has_photo":"1","links":"","showDetailLink":"1","memberInfo":"","customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"creationDate","show_content":"2","withoutStretch":"0","show_buttons":"","pinboarditemWidth":"237","sitemember_map_sponsored":"1","itemCount":"10","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
          ));

          //RIGHT CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 5,
          ));
          $main_right_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.recent-popular-random-members',
            'parent_content_id' => $main_right_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Popular Members","titleCount":true,"viewType":"gridview","viewtype":"vertical","columnWidth":"60","fea_spo":"","has_photo":"1","titlePosition":"0","viewtitletype":"horizontal","columnHeight":"60","orderby":"view_count","interval":"overall","links":"","memberInfo":"","customParams":"5","custom_field_title":"0","custom_field_heading":"0","itemCount":"100","titleLink":"","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.recent-popular-random-members"}',
          ));
        } elseif( isset($_POST['sitemember_profiletemplate']) && !empty($_POST['sitemember_profiletemplate']) && !empty($previousProfileTemplate) && $_POST['sitemember_profiletemplate'] != $previousProfileTemplate && $_POST['sitemember_profiletemplate'] == 'gridview' ) {
          $db->delete('engine4_core_content', array('page_id =?' => $page_id));
          if( !empty($page_id) ) {
            //CONTAINERS
            $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'container',
              'name' => 'main',
              'parent_content_id' => Null,
              'order' => 2,
              'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');
            if( !empty($container_id) ) {
              //INSERT MAIN - MIDDLE CONTAINER
              $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
              ));
              $middle_id = $db->lastInsertId('engine4_core_content');
              if( !empty($middle_id) ) {
                //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
                $db->insert('engine4_core_content', array(
                  'page_id' => $page_id,
                  'type' => 'widget',
                  'name' => 'sitemember.navigation-sitemember',
                  'parent_content_id' => $middle_id,
                  'order' => 1,
                  'params' => '{"0":"","title":"","titleCount":true}',
                ));

                $db->insert('engine4_core_content', array(
                  'page_id' => $page_id,
                  'type' => 'widget',
                  'name' => 'sitemember.search-sitemember',
                  'parent_content_id' => $middle_id,
                  'order' => 3,
                  'params' => '{"title":"","titleCount":true,"viewType":"horizontal","locationDetection":"0","whatWhereWithinmile":"0","nomobile":"0","name":"sitemember.search-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                  'page_id' => $page_id,
                  'type' => 'widget',
                  'name' => 'sitemember.browse-members-sitemember',
                  'parent_content_id' => $middle_id,
                  'order' => 4,
                  'params' => '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","columnWidth":"202","truncationGrid":"50","columnHeight":"192","has_photo":"1","links":"","showDetailLink":"0","memberInfo":["featuredLabel"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"featured","show_content":"2","withoutStretch":"0","show_buttons":["facebook","twitter","pinit"],"pinboarditemWidth":"237","sitemember_map_sponsored":"1","itemCount":"20","truncation":"50","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
                ));
              }
            }
          }
        } elseif( isset($_POST['sitemember_profiletemplate']) && !empty($_POST['sitemember_profiletemplate']) && !empty($previousProfileTemplate) && $_POST['sitemember_profiletemplate'] != $previousProfileTemplate && $_POST['sitemember_profiletemplate'] == 'pinboardview' ) {
          $db->delete('engine4_core_content', array('page_id =?' => $page_id));
          //TOP CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
          ));
          $top_container_id = $db->lastInsertId();

          //TOP-MIDDLE CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => 6,
          ));
          $top_middle_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.navigation-sitemember',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
          ));
          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.search-sitemember',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":"horizontal","locationDetection":"0","whatWhereWithinmile":"1","nomobile":"0","name":"sitemember.search-sitemember"}',
          ));

          //MAIN CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => 2,
          ));
          $main_container_id = $db->lastInsertId();

          //MAIN-MIDDLE CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 6,
          ));
          $main_middle_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.browse-members-sitemember',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"layouts_views":["4"],"layouts_order":"4","columnWidth":"202","truncationGrid":"16","columnHeight":"328","has_photo":"1","links":"","showDetailLink":"0","memberInfo":["ratingStar","featuredLabel","sponsoredLabel","location","directionLink","memberCount","mutualFriend","profileField","distance"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"featured","show_content":"2","withoutStretch":"1","show_buttons":["facebook","twitter","pinit","like"],"pinboarditemWidth":"200","sitemember_map_sponsored":"1","itemCount":"12","truncation":"50","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
          ));

          //RIGHT CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 5,
          ));
          $main_right_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.item-sitemember',
            'parent_content_id' => $main_right_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Member of the Day","member_title":"","user_id":"","memberInfo":["featuredLabel"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","starttime":"","endtime":"","nomobile":"0","name":"sitemember.item-sitemember"}',
          ));

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.recent-popular-random-members',
            'parent_content_id' => $main_right_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Popular Members","titleCount":true,"viewType":"listview","viewtype":"vertical","columnWidth":"180","fea_spo":"","has_photo":"1","titlePosition":"1","viewtitletype":"vertical","columnHeight":"328","orderby":"view_count","interval":"overall","links":["addfriend","messege"],"memberInfo":["memberCount","mutualFriend"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","itemCount":"5","titleLink":"","truncation":"50","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.recent-popular-random-members"}',
          ));
        } elseif( isset($_POST['sitemember_profiletemplate']) && !empty($_POST['sitemember_profiletemplate']) && !empty($previousProfileTemplate) && $_POST['sitemember_profiletemplate'] != $previousProfileTemplate && $_POST['sitemember_profiletemplate'] == 'mapview' ) {
          $db->delete('engine4_core_content', array('page_id =?' => $page_id));
          //TOP CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
          ));
          $top_container_id = $db->lastInsertId();

          //TOP-MIDDLE CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => 6,
          ));
          $top_middle_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.navigation-sitemember',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
          ));
          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.browse-members-sitemember',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"layouts_views":["2","3"],"layouts_order":"3","columnWidth":"199","truncationGrid":"16","columnHeight":"210","has_photo":"1","links":["addfriend"],"showDetailLink":"1","memberInfo":["featuredLabel","sponsoredLabel","location","directionLink","memberStatus"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"spfesp","show_content":"2","withoutStretch":"0","show_buttons":["facebook","twitter","pinit"],"pinboarditemWidth":"237","sitemember_map_sponsored":"1","itemCount":"20","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
          ));
          //MAIN CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => 2,
          ));
          $main_container_id = $db->lastInsertId();

          //MAIN-MIDDLE CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 6,
          ));
          $main_middle_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.recent-popular-random-members',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Most Viewed Members","titleCount":true,"viewType":"gridview","viewtype":"vertical","columnWidth":"130","fea_spo":"","has_photo":"1","titlePosition":"0","viewtitletype":"horizontal","columnHeight":"125","orderby":"creation_date","interval":"overall","links":"","memberInfo":"","customParams":"5","custom_field_title":"0","custom_field_heading":"0","itemCount":"30","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.recent-popular-random-members"}',
          ));

          //RIGHT CONTAINER
          $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 5,
          ));
          $main_right_id = $db->lastInsertId();

          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemember.recently-popular-random-sitemember',
            'parent_content_id' => $main_right_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Most Liked Members","titleCount":"","layouts_views":["gridZZZview"],"ajaxTabs":["featured","sponsored","today"],"user_id_order":1,"modified_date_order":2,"featured_order":"5","sponosred_order":"6","popular_order":"3","like_order":"4","columnWidth":"248","defaultOrder":"gridZZZview","titlePosition":"1","showDetailLink":"0","columnHeight":"185","links":"","memberInfo":["ratingStar"],"customParams":"5","custom_field_heading":"0","custom_field_title":"0","has_photo":"1","upcoming_order":"1","views_order":"2","month_order":"7","week_order":"8","today_order":"9","sitemember_map_sponsored":"1","limit":"3","truncationList":"16","truncationGrid":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.recently-popular-random-sitemember"}',
          ));
        }
      }
    }
  }

  //MAKE FAQ ACTION
  public function faqAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_faq');
  }

  public function readmeAction()
  {
    
  }

  public function manageAction()
  {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_settings');

    //FETCH MAPPING DATA
    $tableCategory = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
    $tableCategoryName = $tableCategory->info('name');

    $optionsTable = Engine_Api::_()->fields()->getTable('user', 'options');
    $optionsTableName = $optionsTable->info('name');

    $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
    $metaTableName = $metaTable->info('name');

    $select = $optionsTable->select()
      ->setIntegrityCheck(false)
      ->from($optionsTableName, array('option_id', 'field_id', 'label'))
      ->joinLeft($tableCategoryName, "$optionsTableName.option_id = $tableCategoryName.option_id", array('profile_type', 'profilemap_id'))
      ->joinLeft($metaTableName, "$tableCategoryName.profile_type = $metaTableName.field_id", array('label as labelLocation'))
      ->where($optionsTableName . ".field_id = ?", 1);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(100);
  }

  //ACTION FOR MAP THE PROFILE WITH CATEGORY
  public function mapAction()
  {

    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE THE FORM
    $form = $this->view->form = new Sitemember_Form_Admin_Map();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $values = $form->getValues();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        //SAVE THE NEW MAPPING
        $row = Engine_Api::_()->getDbtable('profilemaps', 'sitemember')->createRow();
        $row->profile_type = $values['profile_type'];
        $row->option_id = $this->_getParam('option_id');
        $row->save();
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 300,
        'parentRefresh' => 300,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-settings/map.tpl');
  }

  //ACTION FOR DELETE MAPPING
  public function deleteAction()
  {

    $this->_helper->layout->setLayout('admin-simple');

    //GET MAPPING ID
    $this->view->profilemap_id = $this->_getParam('profilemap_id');

    if( $this->getRequest()->isPost() ) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $sitemember_profilemap = Engine_Api::_()->getItem('sitemember_profilemap', $this->view->profilemap_id);
        $sitemember_profilemap->delete();
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping deleted successfully !'))
      ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }

  //ACTION FOR SET THE DEFAULT MAP CENTER POINT
  public function setDefaultMapCenterPoint($oldLocation, $newLocation)
  {

    if( $oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world" ) {
      $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Advanced Members'));
      if( !empty($locationResults['latitude']) && !empty($locationResults['longitude']) ) {
        $latitude = $locationResults['latitude'];
        $longitude = $locationResults['longitude'];

        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemember.map.latitude', $latitude);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemember.map.longitude', $longitude);
      }
    }
  }

  //ACTION FOR SEARCH FORM TAB
  public function formSearchAction()
  {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_searchformsettings');

    $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    //CHECK POST
    if( $this->getRequest()->isPost() ) {

      //BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      $values = $_POST;
      $rowCountry = $tableSearchForm->getFieldsOptions('sitemember', 'country');
      $defaultCategory = 0;
      $defaultAddition = 0;
      $count = 1;
      try {
        foreach( $values['order'] as $key => $value ) {
          $multiplyAddition = $count * 5;
          $tableSearchForm->update(array('order' => $defaultAddition + $defaultCategory + $key + $multiplyAddition + 1), array('searchformsetting_id = ?' => (int) $value));

          if( !empty($rowCountry) && $value == $rowCountry->searchformsetting_id ) {
            $defaultCategory = 1;
            $defaultAddition = 10000000;
          }
          $count++;
        }
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }

    include APPLICATION_PATH . '/application/modules/Sitemember/controllers/license/license2.php';
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction()
  {

    $field_id = $this->_getParam('id');
    $name = $this->_getParam('name');
    $display = $this->_getParam('display');
    $searchformsetting = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    if( !empty($field_id) ) {

      if( $name == 'location' && $display == 0 ) {
        $searchformsetting->update(array('display' => $display), array('module = ?' => 'sitemember', 'name = ?' => 'proximity'));
      }
      $searchformsetting->update(array('display' => $display), array('module = ?' => 'sitemember', 'searchformsetting_id = ?' => (int) $field_id));
    }
    $this->_redirect('admin/sitemember/settings/form-search');
  }

  public function userlocationsAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_admin_main', array(), 'sitemember_admin_main_userlocations');

    $locationItemsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationItemsTableName = $locationItemsTable->info('name');


    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
    $profilemapsTablename = $profilemapsTable->info('name');

    //Delete entry from locationitems table which have latitude and longtitude is set to "0".
    $select = $locationItemsTable->select()
      ->from($locationItemsTableName, array('locationitem_id', 'resource_id'))
      ->where($locationItemsTableName . '.resource_type = ?', 'user')
      ->where($locationItemsTableName . '.latitude = ?', 0)
      ->where($locationItemsTableName . '.longitude = ?', 0);
    $results = $locationItemsTable->fetchAll($select);
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    if( !empty($results) ) {
      foreach( $results as $result ) {
        Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid' => '', 'location' => ''), array('user_id =?' => $result['resource_id']));

        $db->query("DELETE FROM `engine4_seaocore_locationitems` WHERE `engine4_seaocore_locationitems`.`locationitem_id` = '" . $result['locationitem_id'] . "' AND `resource_type` = 'user';");
      }
    }
    //End delete location work.  


    $select = $profilemapsTable->select()->from($profilemapsTablename);
    $this->view->option_id = $option_id = $profilemapsTable->fetchAll($select);
    $this->view->map_count = $locationMappingCount = count($option_id);

    if( $locationMappingCount != 0 ) {
      $option_id_location = array();

      foreach( $option_id as $optionId ) {
        $option_id_location[] = $optionId['profile_type'];
      }

      $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
      $valuesTableName = $valuesTable->info('name');

      $select = $valuesTable->select()->setIntegrityCheck(false)
        ->from($valuesTableName)
        ->join($usersTableName, "$valuesTableName.item_id = $usersTableName.user_id", null)
        ->where($valuesTableName . '.field_id IN (?)', (array) $option_id_location)
        ->where($valuesTableName . '.value <> ?', '')
        ->where($usersTableName . '.seao_locationid = ?', 0);
      $this->view->row = $valuesTable->fetchAll($select);
    }
  }

  //Sink the event location.
  public function usersinkLocationAction()
  {

    //PROCESS
    set_time_limit(0);
    ini_set("max_execution_time", "300");
    ini_set("memory_limit", "256M");

    $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
    $profilemapsTablename = $profilemapsTable->info('name');

    $select = $profilemapsTable->select()
      ->from($profilemapsTablename);
    $option_id = $profilemapsTable->fetchAll($select);

    $option_id_location = array();
    foreach( $option_id as $optionId ) {
      $option_id_location[] = $optionId['profile_type'];
    }

    $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
    $valuesTableName = $valuesTable->info('name');

    $select = $valuesTable->select()->setIntegrityCheck(false)
      ->from($valuesTableName)
      ->join($usersTableName, "$valuesTableName.item_id = $usersTableName.user_id", null)
      ->where($valuesTableName . '.field_id IN (?)', (array) $option_id_location)
      ->where($usersTableName . '.seao_locationid = ?', 0);
    $this->view->row = $row = $valuesTable->fetchAll($select);

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
    if( !empty($table_exist) ) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
    }

    $this->view->error = 0;
    $this->view->google_error = 0;

    if( $this->getRequest()->isPost() ) {
      $existInDatabase = 1;
      foreach( $row as $result ) {
        if( !empty($result['value']) ) {
          Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $result['item_id'], 'resource_type = ?' => 'user'));


          $getSEALocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $result['value']));


          if( empty($getSEALocation) ) {
            $getSEALocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocation(array('location' => $result['value']));
          }

          if( empty($getSEALocation) ) {
            $existInDatabase = 0;
          }


          if( !empty($getSEALocation) ) {
            $row = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->createRow();

            $addlocation['resource_type'] = 'user';
            $addlocation['resource_id'] = $result['item_id'];
            $addlocation['location'] = $result['value'];
            $addlocation['latitude'] = $getSEALocation['latitude'];
            $addlocation['longitude'] = $getSEALocation['longitude'];
            $addlocation['formatted_address'] = $getSEALocation['formatted_address'];
            ;
            $addlocation['country'] = $getSEALocation['country'];
            ;
            $addlocation['state'] = $getSEALocation['state'];
            ;
            $addlocation['zipcode'] = $getSEALocation['zipcode'];
            ;
            $addlocation['city'] = $getSEALocation['city'];
            ;
            $addlocation['address'] = $getSEALocation['address'];
            ;
            $addlocation['zoom'] = 16;
            $row->setFromArray($addlocation);
            $row->save();
            $seao_locationid = $row->locationitem_id;
          } else {
            $seao_locationid = $seLocationsTable->getLocationItemId($result['value'], '', 'user', $result['item_id']);
          }

          if( $seao_locationid ) {
            if( !empty($column_exist) ) {
              Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $result['value']), array('item_id =?' => $result['item_id']));
            }

            //member table entry of location id.
            Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid' => $seao_locationid, 'location' => $result['value']), array('user_id =?' => $result['item_id']));
          } else {
            if( $existInDatabase ) {
              continue;
            } else {
              $userWithWrongAddress = array();
              $singleUserError = Engine_Api::_()->user()->getUser($result['item_id']  );
              array_push( $userWithWrongAddress, $singleUserError );
              $this->view->error = 1;
              //return;
            }
          }
        }
      }
      $this->view->userWithWrongAddress = $userWithWrongAddress;
    }
  }

  public function updateMismatchReviewCountAction()
  {
    $table = Engine_Api::_()->getDbTable('ratings', 'sitemember');
    $select = $table->select()
      ->from($table->info('name'), array('resource_id', 'count(`rating_id`) as count'))
      ->where('ratingparam_id =?', 0)
      ->group('resource_id');
    $user_ids = $select->query()->fetchAll();
    $db = Engine_Db_Table::getDefaultAdapter();
    foreach( $user_ids as $ids ) {
      $count = $ids['count'];
      $user_id = $ids['resource_id'];
      $db->query("UPDATE `engine4_seaocore_userinfo` SET `review_count` = '$count' WHERE `engine4_seaocore_userinfo`.`user_id` = '$user_id' LIMIT 1");
    }

    echo "Successfully updated review count.";
    die;
  }

  public function activateComplimentAction()
  {
    if( $this->_getParam('flag') != "install" ) {
      $this->_redirect('admin/sitemember/settings');
    }
    $this->createComplimentCategory();
    $this->complimentWidgetSetting();
    $this->_redirect('install/manage');
  }

  private function createComplimentCategory()
  {
    // get the table
    $complimentIconsTable = Engine_Api::_()->getDbtable('complimentCategories', 'sitemember');
    $order = 1;
    $path = APPLICATION_PATH . '/application/modules/Sitemember/externals/complimentIcons';
    $compliments = array();
    if( is_dir($path) && $dh = opendir($path) ) {
      while( ($file = readdir($dh)) !== false ) {
        if( !($file == "." || $file == "..") ) {
          $compliments[] = substr($file, 0, -4);
        }
      }
      closedir($dh);
    }
    sort($compliments);
    foreach( $compliments as $compliment ) {
      $complimentIcon = $complimentIconsTable->createRow();
      $complimentIcon->setFromArray(array('title' => ucwords($compliment), 'order' => $order++));
      $complimentIcon->save();

      $fileName = $compliment . '.png';
      $Filedata = array(
        'tmp_name' => $path . '/' . $fileName,
        'name' => $fileName,
      );
      $complimentIcon->setPhoto($Filedata);
    }
  }

  private function complimentWidgetSetting()
  {

    $db = Engine_Db_Table::getDefaultAdapter();
    $containerCount = 1;
    $widgetCount = 1;
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', "sitemember_compliment_index")
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( !$page_id ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'sitemember_compliment_index',
        'displayname' => 'Advanced Member - Compliments',
        'title' => '',
        'description' => 'This is compliments browse page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      //TOP CONTAINER
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_container_id = $db->lastInsertId();
      //MAIN CONTAINER
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_container_id = $db->lastInsertId();
      //TOP MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_container_id,
        'order' => 6,
      ));
      $top_middle_container_id = $db->lastInsertId();
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.navigation-sitemember',
        'parent_content_id' => $top_middle_container_id,
        'order' => 3,
        'params' => '{"title":"","titleCount":true}',
      ));
      //MAIN MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => 6,
      ));
      $main_middle_container_id = $db->lastInsertId();
      //RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => 5,
      ));
      $right_container_id = $db->lastInsertId();
      //LEFT CONTAINER
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_container_id,
        'order' => 4,
      ));
      $left_container_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.ajax-carousel-compliments-sitemember',
        'parent_content_id' => $main_middle_container_id,
        'order' => 8,
        'params' => '{"title":"MOST BEAUTIFUL","titleCount":true,"compliment_category":"2","showPagination":"1","viewType":"0","itemViewType":"0","blockHeight":"220","blockWidth":"185","circularImage":"0","circularImageHeight":"180","has_photo":"1","titlePosition":"1","itemCount":"3","links":"","memberInfo":"","interval":"300","truncation":"16","nomobile":"0","name":"sitemember.ajax-carousel-compliments-sitemember"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.recent-compliments',
        'parent_content_id' => $main_middle_container_id,
        'order' => 12,
        'params' => '{"title":"Recent Compliments","titleCount":true,"columnWidth":"180","compliment_category":"0","circularImage":"1","circularImageHeight":"80","links":["addfriend","messege"],"show_content":"1","itemCount":"10","nomobile":"0","name":"sitemember.recent-compliments"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.ajax-carousel-compliments-sitemember',
        'parent_content_id' => $left_container_id,
        'order' => 15,
        'params' => '{"title":"MOST LOVED","titleCount":true,"compliment_category":"16","showPagination":"1","viewType":"1","itemViewType":"1","blockHeight":"240","blockWidth":"150","circularImage":"1","circularImageHeight":"180","has_photo":"1","titlePosition":"1","itemCount":"3","links":"","memberInfo":"","interval":"300","truncation":"16","nomobile":"0","name":"sitemember.ajax-carousel-compliments-sitemember"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.ajax-carousel-compliments-sitemember',
        'parent_content_id' => $left_container_id,
        'order' => 16,
        'params' => '{"title":"MOST FRIENDLY ONES","titleCount":true,"compliment_category":"9","showPagination":"1","viewType":"1","itemViewType":"0","blockHeight":"220","blockWidth":"150","circularImage":"0","circularImageHeight":"180","has_photo":"1","titlePosition":"1","itemCount":"3","links":"","memberInfo":"","interval":"300","truncation":"16","nomobile":"0","name":"sitemember.ajax-carousel-compliments-sitemember"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.recently-viewed',
        'parent_content_id' => $left_container_id,
        'order' => 17,
        'params' => '{"title":"Viewed By Me","titleCount":true,"viewType":"iconview","viewtype":"vertical","columnWidth":"60","viewed_by":"viewed_by_me","circularImage":"0","circularImageHeight":"180","has_photo":"1","titlePosition":"1","viewtitletype":"vertical","siteusercoverphoto":"0","columnHeight":"60","itemCount":"10","nomobile":"0","name":"sitemember.recently-viewed"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.ajax-carousel-compliments-sitemember',
        'parent_content_id' => $right_container_id,
        'order' => 9,
        'params' => '{"title":"MOST BOLD","titleCount":true,"compliment_category":"3","showPagination":"1","viewType":"1","itemViewType":"0","blockHeight":"220","blockWidth":"150","circularImage":"0","circularImageHeight":"180","has_photo":"1","titlePosition":"1","itemCount":"3","links":"","memberInfo":"","interval":"300","truncation":"16","nomobile":"0","name":"sitemember.ajax-carousel-compliments-sitemember"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitemember.ajax-carousel-compliments-sitemember',
        'parent_content_id' => $right_container_id,
        'order' => 10,
        'params' => '{"title":"MOST LOVED","titleCount":true,"compliment_category":"11","showPagination":"1","viewType":"1","itemViewType":"0","blockHeight":"220","blockWidth":"150","circularImage":"0","circularImageHeight":"180","has_photo":"1","titlePosition":"1","itemCount":"3","links":"","memberInfo":"","interval":"300","truncation":"16","nomobile":"0","name":"sitemember.ajax-carousel-compliments-sitemember"}',
      ));
    }
    $containerCount = 1;
    $widgetCount = 1;
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', "user_profile_index")
      ->limit(1)
      ->query()
      ->fetchColumn();

    if( $page_id ) {
      $column_container_id = $db->select()
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', $page_id)
        ->where('type =? ', "container")
        ->where('name =?', "left")
        ->limit(1)
        ->query()
        ->fetchColumn();

      if( empty($column_container_id) ) {
        $column_container_id = $db->select()
          ->from('engine4_core_content', 'content_id')
          ->where('page_id = ?', $page_id)
          ->where('type =? ', "container")
          ->where('name =?', "right")
          ->limit(1)
          ->query()
          ->fetchColumn();
      }
      if( $column_container_id ) {
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemember.compliment-me',
          'parent_content_id' => $column_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","compliment_button_title":"Compliment Me !","nomobile":"0","name":"sitemember.compliment-me"}',
        ));
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemember.profile-compliments-icon',
          'parent_content_id' => $column_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"User Compliments","name":"sitemember.profile-compliments-icon"}',
        ));
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemember.recently-viewed',
          'parent_content_id' => $column_container_id,
          'order' => $widgetCount++,
          'params' =>'{"title":"","titleCount":true,"viewType":"listview","viewtype":"vertical","columnWidth":"60","viewed_by":"viewed_by_user","circularImage":"1","circularImageHeight":"180","has_photo":"0","titlePosition":"1","viewtitletype":"vertical","siteusercoverphoto":"1","columnHeight":"55","itemCount":"10","nomobile":"0","name":"sitemember.recently-viewed"}',
        ));
      }
      $tab_container_id = $db->select()
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', $page_id)
        ->where('name =?', "core.container-tabs")
        ->limit(1)
        ->query()
        ->fetchColumn();
      if( $tab_container_id ) {
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemember.profile-compliments',
          'parent_content_id' => $tab_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Compliments","titleCount":true,"columnWidth":"180","commonColumnHeight":"240","circularImage":"0","circularImageHeight":"180","links":["addfriend","messege"],"show_content":"1","itemCount":"2","nomobile":"0","name":"sitemember.profile-compliments"}',
        ));
      }
    }
  }
}
