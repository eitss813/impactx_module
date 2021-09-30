<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
require_once realpath(dirname(__FILE__)) . '/seaocore_install.php';

class Siteloginconnect_Installer extends Sitecore_License_Installer {
    protected $_installConfig = array(
        'sku' => 'siteloginconnect',
    );

    function onInstall() {
        parent::onInstall();
        if($this->_databaseOperationType=='install') {
            $this->_pageCreation();
        }      

    }
    protected function _pageCreation() {
        
        $db = $this->getDb();

        // profile page
        $page_id = $db->select()
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'siteloginconnect_index_index')
          ->limit(1)
          ->query()
          ->fetchColumn();

        
        // insert if it doesn't exist yet
        if( !$page_id ) {
          // Insert page
          $db->insert('engine4_core_pages', array(
            'name' => 'siteloginconnect_index_index',
            'displayname' => 'Synchronize Data With Social Networks',
            'title' => 'Synchronize Data',
            'description' => 'Synchronize Data With Social Networks',
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
          
          // Insert menu
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'user.settings-menu',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
          ));
          
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'core.content',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 1,            
          ));
         
        }
        
        return $this;
    }
    protected $_deependencyVersion = array(
        'seaocore' => '4.10.3p10',
        'sitelogin' => '4.10.3p1',
    );


}
