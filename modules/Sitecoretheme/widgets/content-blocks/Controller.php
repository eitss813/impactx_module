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
class Sitecoretheme_Widget_ContentBlocksController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->viewType = $this->_getParam('viewType', '1');
    $this->view->itemType = $itemType = $this->_getParam('itemType');
    $this->view->readMoreText = $this->_getParam('readMoreText');
    $listingTypeId = 0;
    if (strpos($itemType, 'sitereview_listing_') !== false) {
      $listingTypeId = str_replace('sitereview_listing_', '', $itemType);
      $itemType = 'sitereview_listing';
    }
    if (!$itemType || !Engine_Api::_()->hasItemType($itemType)) {
      $this->setNoRender();
    }
    $this->view->headingColor = $this->_getParam('heading_color', '');
    $this->view->backgroundImage = $this->_getParam('background_image', '');
    $this->view->backgroundOverlayColor = $this->_getParam('background_overlay_color', '');
    $this->view->backgroundOverlayOpacity = $this->_getParam('background_overlay_opacity', '0');
    $this->view->title = $this->_getParam('title', '');
    $this->view->description = $this->_getParam('description', '');
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
    if (in_array('category_id', $table->info('cols'))) {
      $moduleName = $table->fetchNew()->getModuleName();
      try {
        $this->view->categoryTable = Engine_Api::_()->getDbtable('categories', $moduleName);
      } catch (Exception $e) {
        //ignore
      }
    }

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