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
class Sitecoretheme_Widget_TwoContentBlocksController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->itemType = $itemType = $this->_getParam('itemType');
    $limit = $this->_getParam('limit', 6);
    $sortBy = $this->_getParam('sortBy', 'creation_date');
    $results = $this->getResults($itemType, $limit, $sortBy);

    $this->view->itemType2 = $itemType2 = $this->_getParam('itemType2');
    ;
    $limit2 = $this->_getParam('limit2', 6);
    $sortBy2 = $this->_getParam('sortBy2', 'creation_date');
    $results2 = $this->getResults($itemType2, $limit2, $sortBy2);

    if (!count($results) && !count($results2)) {
      $this->setNoRender();
    }

    $data = array();
    if (count($results)) {
      $data[] = array(
        'itemType' => $this->view->itemType,
        'results' => $results,
        'viewType' => $this->_getParam('viewType', '1'),
        'readMoreText' => $this->_getParam('readMoreText'),
        'categoryTable' => $this->getCategoryTable($itemType)
      );
    }

    if (count($results2)) {
      $data[] = array(
        'itemType' => $this->view->itemType2,
        'results' => $results2,
        'viewType' => $this->_getParam('viewType2', '1'),
        'readMoreText' => $this->_getParam('readMoreText2'),
        'categoryTable' => $this->getCategoryTable($itemType2)
      );
    }
    $this->view->data = $data;
    $this->view->headingColor = $this->_getParam('heading_color', '');
    $this->view->backgroundImage = $this->_getParam('background_image', '');
    $this->view->backgroundOverlayColor = $this->_getParam('background_overlay_color', '');
    $this->view->backgroundOverlayOpacity = $this->_getParam('background_overlay_opacity', '0');
  }

  private function getResults($itemType, $limit, $sortBy) {
    $listingTypeId = 0;
    if (strpos($itemType, 'sitereview_listing_') !== false) {
      $listingTypeId = str_replace('sitereview_listing_', '', $itemType);
      $itemType = 'sitereview_listing';
    }
    if (!$itemType || !Engine_Api::_()->hasItemType($itemType)) {
      $this->setNoRender();
    }


    $searchTable = Engine_Api::_()->getDbtable('search', 'core');
    $searchTableName = $searchTable->info('name');
    $table = Engine_Api::_()->getItemTable($itemType);
    $keys = $table->info(Zend_Db_Table::PRIMARY);
    $primaryKey = array_shift($keys);
    $tableName = $table->info('name');

    if (!in_array($sortBy, $table->info('cols'))) {
      $sortBy = 'creation_date';
    }

    $select = $table->select()->from($tableName)
        ->join($searchTableName, "$searchTableName.id = $tableName.$primaryKey", null)
        ->where("$searchTableName.type = ?", $itemType)
        ->order("$sortBy DESC")->limit($limit);
    if ($listingTypeId && $itemType === 'sitereview_listing') {
      $select->where("$tableName.listingtype_id = ?", $listingTypeId);
    }
    $results = $table->fetchAll($select);
    return $results;
  }

  private function getCategoryTable($itemType) {
    if (strpos($itemType, 'sitereview_listing_') !== false) {
      $itemType = 'sitereview_listing';
    }
    $table = Engine_Api::_()->getItemTable($itemType);
    if (!in_array('category_id', $table->info('cols'))) {
      return;
    }

    $moduleName = $table->fetchNew()->getModuleName();
    try {
      return Engine_Api::_()->getDbtable('categories', $moduleName);
    } catch (Exception $e) {
      //ignore
    }
  }

}