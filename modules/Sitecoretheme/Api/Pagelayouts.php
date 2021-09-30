<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Pagelayouts.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Api_Pagelayouts extends Core_Api_Abstract {

  
  public function getWidgetizedPageId($params = array()) {
    //GET CORE CONTENT TABLE
    $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
    $page_id = $tableNamePages->select()
      ->from($tableNamePages->info('name'), 'page_id')
      ->where('name =?', $params['name'])
      ->query()
      ->fetchColumn();
    return $page_id;
  }

  public function getBackupPageId($pageName) {
    //GET CORE CONTENT TABLE
    $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
    $page_id = $tableNamePages->select()
      ->from($tableNamePages->info('name'), 'page_id')
      ->where('name =?', $pageName)
      ->query()
      ->fetchColumn();
    return $page_id;
  }

  public function hideHeaderFooterOnPage($pageName) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("UPDATE `engine4_core_pages` SET `layout` = 'default-simple' WHERE `engine4_core_pages`.`name` = '" . $pageName . "';");
  }

  public function getWidgetizedPageRow($params = array()) {
    //GET CORE CONTENT TABLE
    $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
    $select = $tableNamePages->select()
      ->from($tableNamePages->info('name'), '*')
      ->where('name =?', $params['name']);
    $results = $tableNamePages->fetchRow($select);
    return $results;
  }

  public function restorePageBackup($params = array()) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $backup_page_id = $this->getBackupPageId($params['pageUrl']);

    if (!empty($backup_page_id)) {
      $pageRow = $this->getWidgetizedPageRow(array('name' => $params['name']));
      if (isset($pageRow->page_id) && !empty($pageRow->page_id)) {
        //delete the current page
        $db->query("DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`page_id` = $pageRow->page_id");
        $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $pageRow->page_id");


        $pagesTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pagesTable->update(array(
          'page_id' => $pageRow->page_id,
          'name' => $pageRow->name,
          'displayname' => $pageRow->displayname,
          'url' => NULL,
          'title' => $pageRow->title,
          'description' => $pageRow->description,
          'keywords' => $pageRow->keywords,
          'custom' => $pageRow->custom,
          'fragment' => $pageRow->fragment,
          'layout' => $pageRow->layout,
          'levels' => $pageRow->levels,
          'provides' => $pageRow->provides,
          'view_count' => $pageRow->view_count,
          'search' => $pageRow->search
          ), array(
          'page_id = ?' => $backup_page_id
        ));
        $db->query("UPDATE `engine4_core_content` SET `page_id` = " . $pageRow->page_id . " WHERE `engine4_core_content`.`page_id` = " . $backup_page_id . ";");
      }
    }
  }

  public function getBackupOfHomePage() {

    $page_id = $this->getWidgetizedPageId(array('name' => 'core_index_index'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $db = Engine_Db_Table::getDefaultAdapter();

    //CHECK PAGE EXIST OR NOT
    $home_backup_page_id = $this->getBackupPageId('landing_page_backup');
    if (!empty($home_backup_page_id)) {
      return;
    }

    //CREATE PAGE
    if (empty($home_backup_page_id)) {
      $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES ('landing_page_backup', 'Landing Page - Backup of Landing Page on Installation of Vertical Theme', 'landing_page_backup', 'Backup of Landing Page on Installation of Vertical Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
    }

    //GET EXISTING PAGE ID
    $home_backup_page_id = $this->getBackupPageId('landing_page_backup');

    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $home_backup_page_id");
    //GET MAIN CONTAINER WORK

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'main')
      ->where('type =?', 'container');

    $mainRow = $tableNameContent->fetchRow($select);

    if (!empty($mainRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $home_backup_page_id,
        'parent_content_id' => null,
        'order' => $mainRow->order,
        'params' => $mainRow->params ? json_encode($mainRow->params) : ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('name in (?)', array('left', 'middle', 'right'))
        ->where('type =?', 'container')
        ->where('parent_content_id =?', $mainRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => $values['type'],
          'name' => $values['name'],
          'page_id' => $home_backup_page_id,
          'parent_content_id' => $content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }

      //LEFT CONTAINER WIDGETS
      $select = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $mainRow->content_id)
        ->where('name =?', 'left')
        ->where('type =?', 'container');

      $leftRow = $tableNameContent->fetchRow($select);

      if (!empty($leftRow)) {
        $results = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $leftRow->content_id)
          ->where('type =?', 'widget')
          ->query()
          ->fetchAll();

        $select = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $home_backup_page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'left')
          ->where('type =?', 'container');

        $row = $tableNameContent->fetchRow($select);

        foreach ($results as $values) {
          $db->insert('engine4_core_content', array(
            'type' => $values['type'],
            'name' => $values['name'],
            'page_id' => $home_backup_page_id,
            'parent_content_id' => $row->content_id,
            'order' => $values['order'],
            'params' => $values['params']
          ));
        }
      }
      //END LEFT CONTAINER WIDGET
      //MIDDLE CONTAINER WIDGETS
      $select = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $mainRow->content_id)
        ->where('name =?', 'middle')
        ->where('type =?', 'container');

      $middleRow = $tableNameContent->fetchRow($select);

      if (!empty($middleRow)) {
        $results = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $middleRow->content_id)
          ->where('type =?', 'widget')
          ->query()
          ->fetchAll();

        $select = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $home_backup_page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'middle')
          ->where('type =?', 'container');

        $row = $tableNameContent->fetchRow($select);

        foreach ($results as $values) {
          $db->insert('engine4_core_content', array(
            'type' => $values['type'],
            'name' => $values['name'],
            'page_id' => $home_backup_page_id,
            'parent_content_id' => $row->content_id,
            'order' => $values['order'],
            'params' => $values['params']
          ));
        }
      }
      //END MIDDLE CONTAINER WIDGET
      //RIGHT CONTAINER WIDGETS
      $select = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $mainRow->content_id)
        ->where('name =?', 'right')
        ->where('type =?', 'container');

      $rightRow = $tableNameContent->fetchRow($select);

      if (!empty($rightRow)) {
        $results = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $rightRow->content_id)
          ->where('type =?', 'widget')
          ->query()
          ->fetchAll();

        $select = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $home_backup_page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'right')
          ->where('type =?', 'container');

        $row = $tableNameContent->fetchRow($select);

        foreach ($results as $values) {
          $db->insert('engine4_core_content', array(
            'type' => $values['type'],
            'name' => $values['name'],
            'page_id' => $home_backup_page_id,
            'parent_content_id' => $row->content_id,
            'order' => $values['order'],
            'params' => $values['params']
          ));
        }
      }
      //END RIGHT CONTAINER WIDGET
    }

    //TOP CONTAINER
    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'top')
      ->where('type =?', 'container');

    $topRow = $tableNameContent->fetchRow($select);

    if (!empty($topRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $home_backup_page_id,
        'parent_content_id' => null,
        'order' => $topRow->order,
        //'params' => json_encode($topRow->params)
        'params' => $topRow->params ? json_encode($topRow->params) : ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('name in (?)', array('left', 'middle', 'right'))
        ->where('type =?', 'container')
        ->where('parent_content_id =?', $topRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => $values['name'],
          'page_id' => $home_backup_page_id,
          'parent_content_id' => $content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }

      //MIDDLE CONTAINER WIDGETS
      $select = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $topRow->content_id)
        ->where('name =?', 'middle')
        ->where('type =?', 'container');

      $topMiddleRow = $tableNameContent->fetchRow($select);

      if (!empty($topMiddleRow)) {
        $results = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $topMiddleRow->content_id)
          ->where('type =?', 'widget')
          ->query()
          ->fetchAll();

        $select = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $home_backup_page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'middle')
          ->where('type =?', 'container');

        $row = $tableNameContent->fetchRow($select);

        foreach ($results as $values) {
          $db->insert('engine4_core_content', array(
            'type' => $values['type'],
            'name' => $values['name'],
            'page_id' => $home_backup_page_id,
            'parent_content_id' => $row->content_id,
            'order' => $values['order'],
            'params' => $values['params']
          ));
        }
      }
      //END MIDDLE CONTAINER WIDGET
    }


    //GET BOTTOM CONTAINER
    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'bottom')
      ->where('type =?', 'container');

    $bottomRow = $tableNameContent->fetchRow($select);

    if (!empty($bottomRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'bottom',
        'page_id' => $home_backup_page_id,
        'parent_content_id' => null,
        'order' => $bottomRow->order,
        'params' => $bottomRow->params ? json_encode($bottomRow->params) : ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('name in (?)', array('left', 'middle', 'right'))
        ->where('type =?', 'container')
        ->where('parent_content_id =?', $bottomRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => $values['name'],
          'page_id' => $home_backup_page_id,
          'parent_content_id' => $content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }

      //MIDDLE CONTAINER WIDGETS
      $select = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $bottomRow->content_id)
        ->where('name =?', 'middle')
        ->where('type =?', 'container');

      $bottomMiddleRow = $tableNameContent->fetchRow($select);

      if (!empty($bottomMiddleRow)) {
        $results = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $bottomMiddleRow->content_id)
          ->where('type =?', 'widget')
          ->query()
          ->fetchAll();

        $select = $tableNameContent->select()
          ->from($tableNameContentName, '*')
          ->where('page_id =?', $home_backup_page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'middle')
          ->where('type =?', 'container');

        $row = $tableNameContent->fetchRow($select);

        foreach ($results as $values) {
          $db->insert('engine4_core_content', array(
            'type' => $values['type'],
            'name' => $values['name'],
            'page_id' => $home_backup_page_id,
            'parent_content_id' => $row->content_id,
            'order' => $values['order'],
            'params' => $values['params']
          ));
        }
      }
      //END MIDDLE CONTAINER WIDGET
    }
  }

  public function getBackupOfHeaderPage() {
    $page_id = $this->getWidgetizedPageId(array('name' => 'header'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $db = Engine_Db_Table::getDefaultAdapter();

    //CHECK PAGE EXIST OR NOT
    $header_backup_page_id = $this->getBackupPageId('header_backup');
    if (!empty($header_backup_page_id)) {
      return;
    }
    //CREATE PAGE
    if (empty($header_backup_page_id)) {
      $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES ('header_backup', 'Header Page - Backup of Header Page on Installation of Vertical Theme', 'header_backup', 'Backup of Header Page on Installation of Vertical Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
    }
    //GET EXISTING PAGE ID
    $header_backup_page_id = $this->getBackupPageId('header_backup');

    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $header_backup_page_id");
    //GET MAIN CONTAINER WORK

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'main')
      ->where('type =?', 'container');

    $mainRow = $tableNameContent->fetchRow($select);

    if (!empty($mainRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $header_backup_page_id,
        'parent_content_id' => null,
        'order' => $mainRow->order,
        'params' => $mainRow->params ? json_encode($mainRow->params) : ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('type =?', 'widget')
        ->where('parent_content_id =?', $mainRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => $values['type'],
          'name' => $values['name'],
          'page_id' => $header_backup_page_id,
          'parent_content_id' => $content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }
    }
  }

  public function getBackupOfFooterPage() {
    $page_id = $this->getWidgetizedPageId(array('name' => 'footer'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $db = Engine_Db_Table::getDefaultAdapter();

    //CHECK PAGE EXIST OR NOT
    $footer_backup_page_id = $this->getBackupPageId('footer_backup');
    if (!empty($footer_backup_page_id)) {
      return;
    }
    //CREATE PAGE
    if (empty($footer_backup_page_id)) {
      $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES ('footer_backup', 'Footer Page - Backup of Footer Page on Installation of Vertical Theme', 'footer_backup', 'Backup of Footer Page on Installation of Vertical Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
    }

    //GET EXISTING PAGE ID
    $footer_backup_page_id = $this->getBackupPageId('footer_backup');

    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $footer_backup_page_id");
    //GET MAIN CONTAINER WORK

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'main')
      ->where('type =?', 'container');

    $mainRow = $tableNameContent->fetchRow($select);

    if (!empty($mainRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $footer_backup_page_id,
        'parent_content_id' => null,
        'order' => $mainRow->order,
        'params' => $mainRow->params ? json_encode($mainRow->params) : ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('type =?', 'widget')
        ->where('parent_content_id =?', $mainRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => $values['type'],
          'name' => $values['name'],
          'page_id' => $footer_backup_page_id,
          'parent_content_id' => $content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }
    }
  }

  public function getBackupOfSignInPage() {
    $page_id = $this->getWidgetizedPageId(array('name' => 'user_auth_login'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $db = Engine_Db_Table::getDefaultAdapter();

    //CHECK PAGE EXIST OR NOT
    $login_backup_page_id = $this->getBackupPageId('user_auth_login_backup');
    if (!empty($login_backup_page_id)) {
      return;
    }
    //CREATE PAGE
    if (empty($login_backup_page_id)) {
      $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES ('user_auth_login_backup', 'Login Page - Backup of Login Page on Installation of Vertical Theme', 'user_auth_login_backup', 'Backup of Login Page on Installation of Vertical Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
    }

    //GET EXISTING PAGE ID
    $login_backup_page_id = $this->getBackupPageId('user_auth_login_backup');

    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $login_backup_page_id");
    //GET MAIN CONTAINER WORK

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'main')
      ->where('type =?', 'container');

    $mainRow = $tableNameContent->fetchRow($select);

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'middle')
      ->where('type =?', 'container');

    $middleRow = $tableNameContent->fetchRow($select);

    if (!empty($mainRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $login_backup_page_id,
        'parent_content_id' => null,
        'order' => $mainRow->order,
        'params' => $mainRow->params ? json_encode($mainRow->params) : ''
      ));
      $main_content_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $login_backup_page_id,
        'parent_content_id' => $main_content_id,
        'order' => $middleRow->order,
        'params' => $middleRow->params ? json_encode($middleRow->params) : ''
      ));
      $middle_content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('type =?', 'widget')
        ->where('parent_content_id =?', $middleRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => $values['type'],
          'name' => $values['name'],
          'page_id' => $login_backup_page_id,
          'parent_content_id' => $middle_content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }
    }
  }

  public function getBackupOfSignInRequiredPage() {
    $page_id = $this->getWidgetizedPageId(array('name' => 'core_error_requireuser'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $db = Engine_Db_Table::getDefaultAdapter();

    //CHECK PAGE EXIST OR NOT
    $login_required_backup_page_id = $this->getBackupPageId('core_error_requireuser_backup');
    if (!empty($login_required_backup_page_id)) {
      return;
    }
    //CREATE PAGE
    if (empty($login_required_backup_page_id)) {
      $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES ('core_error_requireuser_backup', 'Sign-in Required Page - Backup of Sign-in Required Page on Installation of Vertical Theme', 'core_error_requireuser_backup', 'Backup of Sign-in Required Page on Installation of Vertical Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
    }

    //GET EXISTING PAGE ID
    $login_required_backup_page_id = $this->getBackupPageId('core_error_requireuser_backup');

    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $login_required_backup_page_id");
    //GET MAIN CONTAINER WORK

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'main')
      ->where('type =?', 'container');

    $mainRow = $tableNameContent->fetchRow($select);

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'middle')
      ->where('type =?', 'container');

    $middleRow = $tableNameContent->fetchRow($select);

    if (!empty($mainRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $login_required_backup_page_id,
        'parent_content_id' => null,
        'order' => $mainRow->order,
        'params' => $mainRow->params ? json_encode($mainRow->params) : ''
      ));
      $main_content_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $login_required_backup_page_id,
        'parent_content_id' => $main_content_id,
        'order' => $middleRow->order,
        'params' => $middleRow->params ? json_encode($middleRow->params) : ''
      ));
      $middle_content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('type =?', 'widget')
        ->where('parent_content_id =?', $middleRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => $values['type'],
          'name' => $values['name'],
          'page_id' => $login_required_backup_page_id,
          'parent_content_id' => $middle_content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }
    }
  }

  public function getBackupOfSignUpPage() {
    $page_id = $this->getWidgetizedPageId(array('name' => 'user_signup_index'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $db = Engine_Db_Table::getDefaultAdapter();

    //CHECK PAGE EXIST OR NOT
    $sign_up_backup_page_id = $this->getBackupPageId('user_signup_index_backup');
    if (!empty($sign_up_backup_page_id)) {
      return;
    }
    //CREATE PAGE
    if (empty($sign_up_backup_page_id)) {
      $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES ('user_signup_index_backup', 'Sign-up Page - Backup of Sign-up Page on Installation of Vertical Theme', 'user_signup_index_backup', 'Backup of Sign-up Page on Installation of Vertical Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
    }

    //GET EXISTING PAGE ID
    $sign_up_backup_page_id = $this->getBackupPageId('user_signup_index_backup');

    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $sign_up_backup_page_id");
    //GET MAIN CONTAINER WORK

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'main')
      ->where('type =?', 'container');

    $mainRow = $tableNameContent->fetchRow($select);

    $select = $tableNameContent->select()
      ->from($tableNameContentName, '*')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'middle')
      ->where('type =?', 'container');

    $middleRow = $tableNameContent->fetchRow($select);

    if (!empty($mainRow)) {

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $sign_up_backup_page_id,
        'parent_content_id' => null,
        'order' => $mainRow->order,
        'params' => $mainRow->params ? json_encode($mainRow->params) : ''
      ));
      $main_content_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $sign_up_backup_page_id,
        'parent_content_id' => $main_content_id,
        'order' => $middleRow->order,
        'params' => $middleRow->params ? json_encode($middleRow->params) : ''
      ));
      $middle_content_id = $db->lastInsertId('engine4_core_content');

      $results = $tableNameContent->select()
        ->from($tableNameContentName, '*')
        ->where('page_id =?', $page_id)
        ->where('type =?', 'widget')
        ->where('parent_content_id =?', $middleRow->content_id)
        ->query()
        ->fetchAll();

      foreach ($results as $values) {
        $db->insert('engine4_core_content', array(
          'type' => $values['type'],
          'name' => $values['name'],
          'page_id' => $sign_up_backup_page_id,
          'parent_content_id' => $middle_content_id,
          'order' => $values['order'],
          'params' => $values['params']
        ));
      }
    }
  }

  public function setHeaderLayout($obj) {
    $this->getBackupOfHeaderPage();
    $db = Engine_Db_Table::getDefaultAdapter();
    $isSitemenuModEnabled = Engine_Api::_()->hasModuleBootstrap('sitemenu');
    $isSeaocoreModEnabled = Engine_Api::_()->hasModuleBootstrap('seaocore');
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'header')
      ->limit(1)
      ->query()
      ->fetchColumn();
    if (!empty($page_id) && !empty($obj) && !empty($obj['sitecoretheme_header_page_layout'])) {
      $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');

      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.html-block',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
        'params' => '{"title":"","data":"<script type=\"text\/javascript\"> \r\nif(typeof(window.jQuery) !=  \"undefined\") {\r\njQuery.noConflict();\r\n}\r\n<\/script>","nomobile":"0","name":"core.html-block"}'
      ));

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.header',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 3,
        'params' => '{"title":"","name":"sitecoretheme.header"}'
      ));
//            $db->insert('engine4_core_content', array(
//                'type' => 'widget',
//                'name' => 'sitecoretheme.main-navigation',
//                'page_id' => $page_id,
//                'parent_content_id' => $main_id,
//                'order' => 4,
//                'params' => '{"title":"","name":"sitecoretheme.main-navigation"}'
//            ));
      if ($isSeaocoreModEnabled) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'seaocore.seaocores-lightbox',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 9,
        ));
      }

      if ($isSitemenuModEnabled) {

        $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
                ('sitecoretheme_core_mini_admin', 'core', 'Admin', 'Sitecoretheme_Plugin_Menus', '', 'user_settings', '', 1, 0, 10),
                ( 'sitecoretheme_core_mini_auth', 'user', 'Sign Out', 'Sitecoretheme_Plugin_Menus', '', 'user_settings', '', 1, 0, 11),
                ( 'sitecoretheme_core_mini_signin', 'user', 'Sign In', 'Sitecoretheme_Plugin_Menus', '', 'core_mini', '', 1, 0, 12);
                ");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'core_mini_auth';");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'core_mini_admin';");

        //CHECK THAT ALBUM PLUGIN IS INSTALLED OR NOT
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_modules')
          ->where('name = ?', 'siteeventticket')
          ->where('enabled = ?', 1);
        $check_siteeventticket = $select->query()->fetchObject();
        if (!empty($check_siteeventticket)) {
          $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'core_mini_siteeventticketmytickets';");

          $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
                    ("sitecoretheme_siteeventticket_main_ticket", "siteeventticket", "My Tickets", "Sitecoretheme_Plugin_Menus", \'{"route":"siteeventticket_order", "action":"my-tickets"}\', "user_settings", "", 1, 0, 9)');
        }
      }
    }
  }

  public function setFooterLayout($obj) {
    $this->getBackupOfFooterPage();
    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'footer')
      ->limit(1)
      ->query()
      ->fetchColumn();
    if (!empty($page_id) && !empty($obj) && !empty($obj['sitecoretheme_footer_page_layout'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.menu-footer',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      //THIS_CORE_WIDGETS_FUNCTIONALITY_ALREADY_ADDED_IN_THE_SITECORETHEME_FOOTER
      // $db->insert('engine4_core_content', array(
      //     'type' => 'widget',
      //     'name' => 'core.menu-footer',
      //     'page_id' => $page_id,
      //     'parent_content_id' => $main_id,
      //     'order' => 3,
      // ));
    }
  }

  public function setSignInPageLayout($obj) {
    $this->getBackupOfSignInPage();
    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'user_auth_login')
      ->limit(1)
      ->query()
      ->fetchColumn();
    if (!empty($page_id) && !empty($obj) && !empty($obj['sitecoretheme_login_page_layout'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 6,
      ));
      $middle_id = $db->lastInsertId();
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.landing-page-header',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'params' => '{"title":""}',
        'order' => 7,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.form-banner',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 8,
        'params' => '{"title":"","logo":"","description":"Happy to see you back to home. Fill up your credentials to enjoy the community again.","image":"","gradient_color_first":"","gradient_color_second":"","nomobile":"0","name":"sitecoretheme.form-banner"}',
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 9,
      ));
    }
    //hide header and footer
    //$this->hideHeaderFooterOnPage('user_auth_login');
  }

  public function setSignInRequiredPageLayout($obj) {
    $this->getBackupOfSignInRequiredPage();
    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'core_error_requireuser')
      ->limit(1)
      ->query()
      ->fetchColumn();
    if (!empty($page_id) && !empty($obj) && !empty($obj['sitecoretheme_login_required_page_layout'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 6,
      ));
      $middle_id = $db->lastInsertId();
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.landing-page-header',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'params' => '{"title":""}',
        'order' => 5,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.form-banner',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 8,
        'params' => '{"title":"","logo":"","description":"Happy to see you back to home. Fill up your credentials to enjoy the community again.","image":"","gradient_color_first":"","gradient_color_second":"","nomobile":"0","name":"sitecoretheme.form-banner"}',
      ));

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 10,
      ));
    }
    //hide header and footer
    //$this->hideHeaderFooterOnPage('core_error_requireuser');
  }

  public function setSignUpPageLayout($obj) {
    $this->getBackupOfSignUpPage();
    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'user_signup_index')
      ->limit(1)
      ->query()
      ->fetchColumn();
    if (!empty($page_id) && !empty($obj) && !empty($obj['sitecoretheme_signup_page_layout'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 6,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.landing-page-header',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'params' => '{"title":""}',
        'order' => 3,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.form-banner',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 5,
        'params' => '{"title":"","logo":"","description":"Want to make the best ever community? Join us. We provide numerous features tailored at one place.","image":"","gradient_color_first":"","gradient_color_second":"","nomobile":"0","name":"sitecoretheme.form-banner"}',
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 10,
      ));
    }
    //hide header and footer
    //$this->hideHeaderFooterOnPage('user_signup_index');
  }

  public function setDefaultLayout($obj) {
    $this->getBackupOfHomePage();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'core_index_index')
      ->limit(1)
      ->query()
      ->fetchColumn();
    if (!empty($page_id) && !empty($obj) && !empty($obj['sitecoretheme_landing_page_layout'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");

      $order = 1;
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => $order++,
      ));
      $top_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'page_id' => $page_id,
        'order' => $order++,
      ));
      $top_middle_id = $db->lastInsertId();


      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.landing-page-header',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":""}',
        'order' => $order++,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.images',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":""}',
        'order' => $order++,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => $order++,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => $order++,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.highlights-block',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":"Aided <span>For</span>","description":"An online community is for bringing people together to have some joyous moments. A true community is when people feel connected  responsible for whatever is happening around.","nomobile":"0"}',
        'order' => $order++,
      ));

      if (Engine_Api::_()->hasItemType('video')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.heading',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"name":"sitecoretheme.heading","title":"Upload, watch and share <span>videos</span>","description":"Post and share videos with your community members, friends, or with anyone, on computers, phones and tablets.","nomobile":"0"}',
          'order' => $order++,
        ));
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.two-content-blocks',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"title":"","description":"","itemType":"video","viewType":"image","sortBy":"view_count","readMoreText":"See More","limit":"5","itemType2":"video","viewType2":"image","sortBy2":"like_count","readMoreText2":"See More","limit2":"5","background_image":"","heading_color":"","background_overlay_color":"","background_overlay_opacity":"0","background_image_preview":null,"nomobile":"0","name":"sitecoretheme.two-content-blocks"}',
          'order' => $order++,
        ));
      }

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.heading',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"name":"sitecoretheme.heading","title":"Our <span>Services</span>","description":"We are providing you with aspects which are no doubt the must for the success of a social community.","nomobile":"0"}',
        'order' => $order++,
      ));

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.our-services',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":"Our <span>Services</span>","viewType":"_cards _round_icons","nomobile":"0","name":"sitecoretheme.our-services"}',
        'order' => $order++,
      ));

      if (Engine_Api::_()->hasModuleBootstrap('sitealbum')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitealbum.list-popular-albums',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"title":"Popular <span>Albums</span>","itemCountPerPage":"6","description":" Share joy, sorrow and every emotion of yourself with your friends through the medium of photos and cherish that forever.","category_id":"0","subcategory_id":null,"hidden_category_id":"0","hidden_subcategory_id":"0","featured":"1","popularType":"comment","interval":"overall","photoHeight":"280","photoWidth":"396","albumInfo":["ownerName","viewCount","likeCount","commentCount","albumTitle","totalPhotos"],"infoOnHover":"1","titleLink":"","truncationLocation":"35","albumTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-albums"}',
          'order' => $order++,
        ));
      } else if (Engine_Api::_()->hasItemType('album')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.content-blocks',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"viewType":"7","title":"Popular <span>Albums</span>","description":"Share joy, sorrow and every emotion of yourself with your friends through the medium of photos and cherish that forever.","itemType":"album","sortBy":"like_count","readMoreText":"Read More","limit":"4","background_image":"","heading_color":"","background_overlay_color":"","background_overlay_opacity":"100","background_image_preview":null,"nomobile":"0","name":"sitecoretheme.content-blocks"}',
          'order' => $order++,
        ));
      }

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.static-buttons',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":"Why Choose Us?","name":"sitecoretheme.static-buttons"}',
        'order' => $order++,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.stats-block',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":""}',
        'order' => $order++,
      ));

      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.text-banner',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":"", "nomobile":"0"}',
        'order' => $order++,
      ));


      if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'siteevent.list-popular-events',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"title":"Popular <span>Events</span>","titleCount":true,"showOptions":["category","rating","review"],"description":"It’s important to plan some events and turn your imagination into reality. Events are a great way to let people gather at a common place with some common interests where they can create some magical and beautiful moments to cherish. ","eventType":"0","fea_spo":"","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","showMorelink":"1","blockHeight":"350","blockWidth":"387","itemCount":"3","popularity":"event_id","showEventType":"upcoming","truncationLocation":"50","truncation":"50","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.list-popular-events"}',
          'order' => $order++,
        ));
      } elseif (Engine_Api::_()->hasItemType('event')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.content-blocks',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"viewType":"4","title":"Popular <span>Events</span>","description":"It’s important to plan some events and turn your imagination into reality. Events are a great way to let people gather at a common place with some common interests where they can create some magical and beautiful moments to cherish.","itemType":"event", "sortBy":"member_count","readMoreText":"Read More","limit":"4","background_image":"","heading_color":"","background_overlay_color":"","background_overlay_opacity":"100","background_image_preview":null,"nomobile":"0","name":"vertical.content-blocks"}',
          'order' => $order++,
        ));
      }
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.app-promotion',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":"", "nomobile":"0"}',
        'order' => $order++,
      ));
      if (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitereview.list-featured-listing',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"title":"Popular <span>Articles</span>","titleCount":true,"showOptions":["category","rating","review","wishlist"],"description":"Articles are the publication of personal thoughts, News and Web links. Share your meaningful articles to get users attention towards most needful topics.","listingtype_id":"18","ratingType":"rating_avg","fea_spo":"fea_spo","category_id":"0","hidden_category_id":"0","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","detactLocation":"0","defaultLocationDistance":"1000","blockHeight":"440","blockWidth":"350","itemCount":"3","popularity":"creation_date","featuredIcon":"1","sponsoredIcon":"1","newIcon":"1","truncation":"50","desc_truncation":"250","showMorelink":"0","nomobile":"0","name":"sitereview.list-featured-listing"}',
          'order' => $order++,
        ));
      } else if (Engine_Api::_()->hasItemType('blog')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.content-blocks',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"viewType":"8","title":"Recent <span>Blogs</span>","description":"You’re a part of this world and you’re responsible for your actions and reactions. Put forward your thoughts boldly and express your views on others’ thoughts. We all are the members of the family named “World”.","itemType":"blog","sortBy":"creation_date","readMoreText":"Read More","limit":"4","background_image":"","heading_color":"","background_overlay_color":"","background_overlay_opacity":"100","background_image_preview":null,"nomobile":"0","name":"vertical.content-blocks"}',
          'order' => $order++,
        ));
      }

      if (Engine_Api::_()->hasModuleBootstrap('sitemember')) {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitemember.list-featured',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"title":"Popular <span>Members</span>","titleCount":true,"description":"Every individual has a significant role whether it’s community or life.","fea_spo":"featured","itemCount":"4","nomobile":"0","name":"sitemember.list-featured"}',
          'order' => $order++,
        ));
      } else {
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.heading',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"name":"sitecoretheme.heading","title":"Popular <span>Members</span>","description":"Every individual has a significant role whether it’s community or life.","nomobile":"0"}',
          'order' => $order++,
        ));
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'sitecoretheme.landing-page-listing',
          'page_id' => $page_id,
          'parent_content_id' => $top_middle_id,
          'params' => '{"itemType":"user","sortBy":"like_count","limit":"6","crousalView":"0","title":"Popular <span>Members</span>","nomobile":"0","name":"sitecoretheme.landing-page-listing"}',
          'order' => $order++,
        ));
      }
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitecoretheme.scroll-content-menus',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
        'order' => $order++,
      ));
    }
    $member_home_page_id = $this->getWidgetizedPageId(array('name' => 'user_index_home'));
    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableNameContentName = $tableNameContent->info('name');
    $top_content_id = $tableNameContent->select()
      ->from($tableNameContentName, 'content_id')
      ->where('page_id =?', $member_home_page_id)
      ->where('name =?', 'top')
      ->query()
      ->fetchColumn();
    if (empty($top_content_id)) {
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $member_home_page_id,
        'parent_content_id' => null,
        'order' => 1,
        'params' => ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');
      $middle_content_id = $tableNameContent->select()
        ->from($tableNameContentName, 'content_id')
        ->where('page_id =?', $member_home_page_id)
        ->where('parent_content_id =?', $content_id)
        ->where('name =?', 'middle')
        ->query()
        ->fetchColumn();

      if (empty($middle_content_id)) {
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $member_home_page_id,
          'parent_content_id' => $content_id,
          'order' => 2,
          'params' => ''
        ));

        $content_id = $db->lastInsertId('engine4_core_content');

        $middle_banner_id = $tableNameContent->select()
          ->from($tableNameContentName, 'content_id')
          ->where('page_id =?', $member_home_page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'sitecoretheme.banner-images')
          ->query()
          ->fetchColumn();
        if (!$middle_banner_id) {
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitecoretheme.banner-images',
            'page_id' => $member_home_page_id,
            'parent_content_id' => $content_id,
            'order' => 1,
            'params' => '{"showBanners":"1","selectedBanners":"","width":"","height":"300","speed":"5000","order":"2","verticalHtmlTitle":"Enjoy the rhythm of life with us!","verticalHtmlDescription":"Stay connected and share your love, care and life experiences.","title":"","nomobile":"0","name":"sitecoretheme.banner-images"}'
          ));
        }
      }
      $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $member_home_page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1 ;");
    } else {
      $middle_content_id = $tableNameContent->select()
        ->from($tableNameContentName, 'content_id')
        ->where('page_id =?', $member_home_page_id)
        ->where('parent_content_id =?', $top_content_id)
        ->where('name =?', 'middle')
        ->query()
        ->fetchColumn();

      if (!empty($middle_content_id)) {

        $middle_banner_id = $tableNameContent->select()
          ->from($tableNameContentName, 'content_id')
          ->where('page_id =?', $member_home_page_id)
          ->where('parent_content_id =?', $middle_content_id)
          ->where('name =?', 'sitecoretheme.banner-images')
          ->query()
          ->fetchColumn();

        if (!$middle_banner_id) {
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitecoretheme.banner-images',
            'page_id' => $member_home_page_id,
            'parent_content_id' => $middle_content_id,
            'order' => 1,
            'params' => '{"showBanners":"1","selectedBanners":"","width":"","height":"300","speed":"5000","order":"2","verticalHtmlTitle":"Enjoy the rhythm of life with us!","verticalHtmlDescription":"Stay connected and share your love, care and life experiences.","title":"","nomobile":"0","name":"sitecoretheme.banner-images"}'
          ));
        }
        $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $member_home_page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1;");

        if (Engine_Api::_()->hasModuleBootstrap('spectacular')) {
          $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $member_home_page_id AND `engine4_core_content`.`name` = 'spectacular.banner-images' LIMIT 1;");
        }

        if (Engine_Api::_()->hasModuleBootstrap('captivate')) {
          $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $member_home_page_id AND `engine4_core_content`.`name` = 'captivate.banner-images' LIMIT 1;");
        }
      }
    }
  }

}