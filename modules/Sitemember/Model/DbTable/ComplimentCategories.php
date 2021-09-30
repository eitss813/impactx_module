<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ComplimentCategories.php 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemember_Model_DbTable_ComplimentCategories extends Engine_Db_Table {
  protected $_name = 'sitemember_compliment_categories';
  protected $_rowClass = 'Sitemember_Model_ComplimentCategory';
  protected $_searchTriggers = false;

  public function getComplimentCategories() {
    
    $complimentsTablename = $this->info('name');
    $select = $this->select()->from($complimentsTablename, array("complimentcategory_id","title"))
                             ->order("order");
    
    $categories = $this->fetchAll($select);
    $compliments = array();
    foreach ($categories as $category){
        $compliments[$category->complimentcategory_id] = $category->title;
    }
    return $compliments;
  }

}
