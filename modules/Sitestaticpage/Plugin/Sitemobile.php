<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaicpage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sitemobile.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    // Code to insert Entries in Sitemobile Tables for Existing Pages    
    $this->addExistingStaticPage();
  }

  public function addExistingStaticPage() {
    
    $corePagesTable = Engine_Api::_()->getDbtable('pages', 'core');
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_sitestaticpage_pages', array('page_id', 'menu'));
    $results = $select->query()->fetchAll();
    foreach ($results as $result) {
      if ($result['menu'] == 0 || $result['menu'] == 1 || $result['menu'] == 2) {
        $menu_exist = $db->select()
                ->from('engine4_sitemobile_menuitems', 'id')
                ->where('name = ?', "core_main_sitestaticpage_" . $result['page_id'])
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (empty($menu_exist)) {
          $page_title = $db->select()
                  ->from('engine4_core_menuitems', 'label')
                  ->where('name = ?', "core_main_sitestaticpage_" . $result['page_id'])
                  ->orwhere('name = ?', "core_mini_sitestaticpage_" . $result['page_id'])
                  ->orwhere('name = ?', "core_footer_sitestaticpage_" . $result['page_id'])
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
          $menu_order = $db->select()
                  ->from('engine4_sitemobile_menuitems', 'order')
                  ->where('name = ?', "core_main_separator_settings")
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
          $menu_order -= 1;
          $db->insert('engine4_sitemobile_menuitems', array(
              'name' => "core_main_sitestaticpage_" . $result['page_id'],
              'module' => 'sitestaticpage',
              'label' => $page_title,
              'plugin' => 'Sitestaticpage_Plugin_Menus::mainMenu',
              'params' => '{"route":"sitestaticpage_index_index_staticpageid_' . $result['page_id'] . ' ", "action":"index", "staticpage_id":"' . $result['page_id'] . '"}',
              'menu' => 'core_main',
              'submenu' => '',
              'order' => $menu_order,
              'enable_mobile' => 1,
              'enable_tablet' => 1
          ));
        }
      }
      $page_id_Exist = $db->select()
              ->from($this->_pagesTable, 'page_id')
              ->where('name = ?', 'sitestaticpage_index_index_staticpageid_' . $result['page_id'])
              ->limit(1)
              ->query()
              ->fetchColumn(); 
      $page_id = $db->select()
              ->from('engine4_core_pages', 'page_id')
              ->where('name = ?', 'sitestaticpage_index_index_staticpageid_' . $result['page_id'])
              ->limit(1)
              ->query()
              ->fetchColumn();
      if (empty($page_id_Exist) && !empty($page_id)) {
        $select = $corePagesTable->select()
                ->from('engine4_core_pages', array('displayname', 'title', 'description', 'keywords', 'search'))
                ->where('name = ?', 'sitestaticpage_index_index_staticpageid_' . $result['page_id']);
        $pageTable_values = $corePagesTable->fetchRow($select)->toArray();
        $db->insert($this->_pagesTable, array(
            'name' => "sitestaticpage_index_index_staticpageid_" . $result['page_id'],
            'displayname' => $pageTable_values['displayname'],
            'title' => $pageTable_values['title'],
            'description' => $pageTable_values['description'],
            'keywords' => $pageTable_values['keywords'],
            'search' => $pageTable_values['search'],
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId();
        //MAIN CONTAINER
        $db->insert($this->_contentTable, array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
        ));
        $main_container_id = $db->lastInsertId();

        //MAIN-MIDDLE CONTAINER
        $db->insert($this->_contentTable, array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => 1,
        ));
        $main_middle_id = $db->lastInsertId();

        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitestaticpage.page-content',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 1,
        ));
      }
    }
  }

}