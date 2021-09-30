<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_LandingPageListingController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->content_id = $this->_getParam('widget_id', $this->view->identity);
    $this->view->crousalView = $itemType = $this->_getParam('crousalView', 0);
    $this->view->showInfo = $itemType = $this->_getParam('showInfo', 1);
    $this->view->itemType = $itemType = $this->_getParam('itemType');
    $this->view->viewType = $this->_getParam('viewType');
    $listingTypeId = 0;
    if (strpos($itemType, 'sitereview_listing_') !== false) {
      $listingTypeId = str_replace('sitereview_listing_', '', $itemType);
      $itemType = 'sitereview_listing';
    }
    if (!$itemType || !Engine_Api::_()->hasItemType($itemType)) {
      $this->setNoRender();
    }
    $limit = $this->_getParam('limit', 6);
    $sortBy = $this->_getParam('sortBy', 'creation_date');
    $searchTable = Engine_Api::_()->getDbtable('search', 'core');
    $searchTableName = $searchTable->info('name');
    $table = Engine_Api::_()->getItemTable($itemType);
    $keys = $table->info(Zend_Db_Table::PRIMARY);
    $primaryKey = array_shift($keys);
    $tableName = $table->info('name');
    if (!in_array($sortBy, $table->info('cols'))) {
      $sortBy = 'creation_date';
    }
//    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
//    $sort_column_exist = $db->query("SHOW COLUMNS FROM " . $tableName . " LIKE '" . $sortBy . "'")->fetch();
//    if (!$sort_column_exist) {
//      $sortBy = 'creation_date';
//    }
    $select = $table->select()->from($tableName)
        ->join($searchTableName, "$searchTableName.id = $tableName.$primaryKey", null)
        ->where("$searchTableName.type = ?", $itemType)
        ->order("$sortBy DESC")->limit($limit);
    if ($listingTypeId && $itemType === 'sitereview_listing') {
      $select->where("$tableName.listingtype_id = ?", $listingTypeId);
    }
     if ($sortBy !== 'creation_date') {
      $select->order("creation_date DESC");
    }
    $this->view->results = $results = $table->fetchAll($select);
    if (!count($results)) {
      $this->setNoRender();
    }
  }

}