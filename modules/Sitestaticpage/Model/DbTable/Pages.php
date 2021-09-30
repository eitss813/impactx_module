<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Pages.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Model_DbTable_Pages extends Engine_Db_Table {

  protected $_rowClass = "Sitestaticpage_Model_Page";

  public function getStaticpagePaginator($params = array()) {
    return Zend_Paginator::factory($this->getStaticpageSelect($params));
  }

  /**
   * Create new columns in Sitestaticpage table for language support
   *
   * @param array $params
   * @return Zend_Db_Table_Select
   */
  public function getStaticpageSelect($params = array()) {
    $table = Engine_Api::_()->getDbTable('pages', 'sitestaticpage');
    if(isset($params['orderby']))
    $select = $table->select()->order($params['orderby'] . ' DESC');
    else
      $select = $table->select();
    return $select;
  }

  /**
   * Create new columns in Sitestaticpage table for language support
   *
   * @param array $columns
   */
  public function createColumns($columns = array()) {

    //RETURN IF COLUMNS ARRAY IS EMPTY
    if (empty($columns)) {
      return;
    }

    foreach ($columns as $key => $label) {

      if ($label == 'en') {
        continue;
      }

      $params_column = "'params_$label'";
      $body_column = "'body_$label'";

      $create_params_column = "`params_$label`";
      $create_body_column = "`body_$label`";

      $db = Engine_Db_Table::getDefaultAdapter();

      //CHECK COLUMNS ARE ALREADY EXISTS
      $params_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_pages LIKE $params_column")->fetch();
      $body_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestaticpage_pages LIKE $body_column")->fetch();

      //CREATE COLUMNS IF NOT EXISTS
      if (empty($params_column_exist) && empty($body_column_exist)) {
        $db->query("ALTER TABLE `engine4_sitestaticpage_pages` ADD $create_body_column LONGTEXT NOT NULL AFTER `body` , ADD $create_params_column TEXT NOT NULL AFTER $create_body_column ");
      }
    }  
  }

}