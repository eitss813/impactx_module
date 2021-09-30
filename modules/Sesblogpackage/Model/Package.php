<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Package.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_Model_Package extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  protected $_level;
  protected $_product;

  public function hasDuration() {
    return ( $this->duration > 0 && $this->duration_type != 'forever' );
  }

  public function isFree() {
    return ( $this->price <= 0 );
  }

  public function isOneTime() {
    return ( $this->recurrence <= 0 || $this->recurrence_type == 'forever' );
  }

  public function getLevel() {
    if (empty($this->level_id)) {
      return null;
    }
    if (null === $this->_level) {
      $this->_level = Engine_Api::_()->getItem('authorization_level', $this->level_id);
    }
    return $this->_level;
  }

  public function getProduct() {
    if (null === $this->_product) {
      $productsTable = Engine_Api::_()->getDbtable('products', 'payment');
      $this->_product = $productsTable->fetchRow($productsTable->select()
                      ->where('extension_type = ?', 'blog')
                      ->where('extension_id = ?', $this->getIdentity())
                      ->limit(1));
      // Create a new product?
      if (!$this->_product) {
        $this->_product = $productsTable->createRow();
        $this->_product->setFromArray($this->getProductParams());
        $this->_product->save();
      }
    }

    return $this->_product;
  }

  public function getProductParams() {
    return array(
        'title' => $this->title,
        'description' => $this->description,
        'price' => $this->price,
        'extension_type' => 'blog',
        'extension_id' => $this->package_id,
    );
  }

  public function getPackageDescription() {
    $translate = Zend_Registry::get('Zend_Translate');
    $view = Zend_Registry::get('Zend_View');
    $currency =  Engine_Api::_()->sesblogpackage()->getCurrentCurrency();
    $priceStr = Engine_Api::_()->sesblogpackage()->getCurrencyPrice($this->price,'','');

    // Plan is free
    if ($this->price == 0) {
      $str = $translate->translate('Free');
    }

    // Plan is recurring
    else if ($this->recurrence > 0 && $this->recurrence_type != 'forever') {

      // Make full string
      if ($this->recurrence == 1) { // (Week|Month|Year)ly
        if ($this->recurrence_type == 'day') {
          $typeStr = $translate->translate('daily');
        } else {
          $typeStr = $translate->translate($this->recurrence_type . 'ly');
        }
        $str = sprintf($translate->translate('%1$s %2$s'), $priceStr, $typeStr);
      } else { // per x (Week|Month|Year)s
        $typeStr = $translate->translate(array($this->recurrence_type, $this->recurrence_type . 's', $this->recurrence));
        if($this->recurrence == 1) { 
          $typeStr = $this->recurrence . $translate->translate($this->recurrence_type);
        } else {
          $typeStr = $translate->translate($this->recurrence_type . 's');
        }
        $str = sprintf($translate->translate('%1$s per %2$s %3$s'), $priceStr, $this->recurrence, $typeStr); // @todo currency
      }
    }
    // Plan is one-time
    else {
      $str = sprintf($translate->translate('One-time fee of %1$s'), $priceStr);
    }

    // Add duration, if not forever
    if ($this->duration > 0 && $this->duration_type != 'forever') {
      if($this->duration > 1)
        $typeStr = $this->duration_type.'s';
      else
        $typeStr = $this->duration_type;
      $str = sprintf($translate->translate('%1$s for %2$s %3$s'), $str, $this->duration, $typeStr);
    }

    return $str;
  }

  public function getGatewayIdentity() {
    return $this->getProduct()->sku;
  }

  public function getGatewayParams() {
    $params = array();

    // General
    $params['name'] = $this->title;
    $params['price'] = $this->price;
    $params['description'] = $this->description;
    $params['vendor_product_id'] = $this->getGatewayIdentity();
    $params['tangible'] = false;

    // Non-recurring
    if ($this->recurrence_type == 'forever') {
      $params['recurring'] = false;
    }

    // Recurring
    else {
      $params['recurring'] = true;
      $params['recurrence'] = $this->recurrence . ' ' . ucfirst($this->recurrence_type);

      // Duration
      if ($this->duration_type == 'forever') {
        $params['duration'] = 'Forever';
      } else {
        $params['duration'] = $this->duration . ' ' . ucfirst($this->duration_type);
      }
    }

    return $params;
  }

  public function getExpirationDate($rel = null) {
  
      if( null === $rel ) {
          $rel = time();
      }

      // If it's a one-time payment or a free package with no duration, there
      // is no expiration
      if( ($this->isOneTime() || $this->isFree()) && !$this->hasDuration() ) {
        return false;
      }

      // If this is a free or one-time package, the expiration is based on the
      // duration, otherwise the expirations is based on the recurrence
      $interval = null;
      $interval_type = null;
      if( $this->isOneTime() || $this->isFree() ) {
      $interval = $this->duration;
      $interval_type = $this->duration_type;
      } else {
      $interval = $this->recurrence;
      $interval_type = $this->recurrence_type;
      }

      // This is weird, it should have been handled by the statement at the top
      if( $interval == 'forever' ) {
      return false;
      }

      // Calculate when the next payment should be due
      switch( $interval_type ) {
      case 'day':
          $part = Zend_Date::DAY;
          break;
      case 'week':
          $part = Zend_Date::WEEK;
          break;
      case 'month':
          $part = Zend_Date::MONTH;
          break;
      case 'year':
          $part = Zend_Date::YEAR;
          break;
      default:
          throw new Engine_Payment_Exception('Invalid recurrence_type');
          break;
      }

      $relDate = new Zend_Date($rel);
      $relDate->add((int) $interval, $part);

      return $relDate->toValue();
  }

  public function getTotalBillingCycleCount() {
    // One-time
    if ($this->isOneTime()) {
      return 1;
    }
    // Indefinite
    else if (!$this->hasDuration()) {
      return null;
    }
    // Calculate
    else {
      $multiplier = null;
      switch ($this->recurrence_type . '-' . $this->duration_type) {
        case 'day-day':
        case 'week-week':
        case 'month-month':
        case 'year-year':
          $multiplier = 1;
          break;

        case 'day-week':
          $multiplier = 7;
          break;
        case 'day-month':
          $multiplier = 30; // Not accurate
          break;
        case 'day-year':
          $multiplier = 365; // Not accurate
          break;
        case 'week-month':
          $multiplier = 4; // Not accurate
          break;
        case 'week-year':
          $multiplier = 52; // Not accurate
          break;
        case 'month-year':
          $multiplier = 12;
          break;

        case 'week-day':
          $multiplier = 1 / 7;
          break;
        case 'month-day':
          $multiplier = 1 / 30;
          break;
        case 'month-week':
          $multiplier = 1 / 4;
          break;
        case 'year-day':
          $multiplier = 1 / 365;
          break;
        case 'year-week':
          $multiplier = 1 / 52;
          break;
        case 'year-month':
          $multiplier = 1 / 12;
          break;
        default:
          // Sigh, what should we do here?
          break;
      }

      return ceil($this->duration * $multiplier / $this->recurrence);
    }
  }

  public function getItemModule($modulename = 'photo') {
    return 1;
    $modulesEnable = json_decode($this->params, true);
    if ($modulesEnable && array_key_exists('modules', $modulesEnable) && in_array($modulename, $modulesEnable['modules']))
      return 1;
    return 0;
  }

  public function allowUploadVideo($item, $left = false) {
    if (is_int($item))
      $orderspackage_id = $item;
    else if ($item && $item instanceof Sesblog_Model_Blog)
      $orderspackage_id = $item->orderspackage_id;
    else if ($left)
      return 'unlimited';
    else
      return 0;
    $modulesEnable = json_decode($this->params, true);
    if ($modulesEnable && array_key_exists('modules', $modulesEnable) && in_array('video', $modulesEnable['modules'])) {
      $allowedTotal = $modulesEnable['video_count'];
      if (!$allowedTotal) {
        if ($left)
          return 'unlimited';
        else
          return 1;
      }
      $videoTable = Engine_Api::_()->getDbTable('videos', 'sesvideo');
      $videoTableName = $videoTable->info('name');
      $select = $videoTable->select()->from($videoTableName, array('total' => 'COUNT(video_id)'));
      //free package
      if ($orderspackage_id) {
        $itemTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
        $itemTableName = $itemTable->info('name');
        $subselect = $itemTable->select()->from($itemTableName, 'blog_id')->where('orderspackage_id =?', $orderspackage_id);
        $select->where('parent_id IN (?)', $subselect)->where('parent_type =?', 'blog');
      }
      $uploadedVideos = $videoTable->fetchRow($select);
      $uploadedVideos = $uploadedVideos->total;
      if (!$left) {
        if ($uploadedVideos >= $allowedTotal)
          return 0;
      }else {
        return abs($uploadedVideos - $allowedTotal);
      }
    }
    return 1;
  }

  public function allowUploadMusic($item, $left = false) {
    if (is_int($item))
      $orderspackage_id = $item;
    else if ($item && $item instanceof Sesblog_Model_Blog)
      $orderspackage_id = $item->orderspackage_id;
    else if ($left)
      return 'unlimited';
    else
      return 0;
    $modulesEnable = json_decode($this->params, true);
    if ($modulesEnable && array_key_exists('modules', $modulesEnable) && in_array('music', $modulesEnable['modules'])) {
      $allowedTotal = $modulesEnable['music_count'];
      if (!$allowedTotal) {
        if ($left)
          return 'unlimited';
        else
          return 1;
      }
      $albumSongTable = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic');
      $albumSongTableName = $albumSongTable->info('name');
      $albumTableName = Engine_Api::_()->getDbTable('albums', 'sesmusic')->info('name');
      $select = $albumSongTable->select()->from($albumSongTableName, array('total' => 'COUNT(albumsong_id)'))
              ->setIntegrityCheck(false)
              ->joinLeft($albumTableName, $albumSongTableName . '.album_id = ' . $albumTableName . '.album_id AND ' . $albumTableName . '.resource_type = "sesblog_blog"', null);
      //free package
      if ($orderspackage_id) {
        $itemTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
        $itemTableName = $itemTable->info('name');
        $subselect = $itemTable->select()->from($itemTableName, 'blog_id')->where('orderspackage_id =?', $orderspackage_id);
        $select->where('resource_id IN (?)', $subselect);
      }
      $uploadedMusics = $albumSongTable->fetchRow($select);
      $uploadedMusics = $uploadedMusics->total;
      if (!$left) {
        if ($uploadedMusics >= $allowedTotal)
          return 0;
      }else {
        return abs($uploadedMusics - $allowedTotal);
      }
    }
    return 1;
  }

  public function allowUploadPhoto($item, $left = false) {
    if (is_int($item))
      $orderspackage_id = $item;
    else if ($item && $item instanceof Sesblog_Model_Blog)
      $orderspackage_id = $item->orderspackage_id;
    else if ($left)
      return 'unlimited';
    else
      return 0;
    $modulesEnable = json_decode($this->params, true);
    if ($modulesEnable && array_key_exists('modules', $modulesEnable) && in_array('photo', $modulesEnable['modules'])) {
      $allowedTotal = $modulesEnable['photo_count'];
      if (!$allowedTotal) {
        if ($left)
          return 'unlimited';
        else
          return 1;
      }
      $photoTable = Engine_Api::_()->getDbTable('photos', 'sesblog');
      $photoTableName = $photoTable->info('name');
      $select = $photoTable->select()->from($photoTableName, array('total' => 'COUNT(photo_id)'));
      //free package
      if ($orderspackage_id) {
        $itemTable = Engine_Api::_()->getDbTable('blogs', 'sesblog');
        $itemTableName = $itemTable->info('name');
        $subselect = $itemTable->select()->from($itemTableName, 'blog_id')->where('orderspackage_id =?', $orderspackage_id);
        $select->where('blog_id IN (?)', $subselect);
      }
      $uploadedPhotos = $photoTable->fetchRow($select);
      $uploadedPhotos = $uploadedPhotos->total;
      if (!$left) {
        if ($uploadedPhotos >= $allowedTotal)
          return 0;
      }else {
        return abs($uploadedPhotos - $allowedTotal);
      }
    }
    return 1;
  }

  protected function _postInsert() {
    // Update product
    $product = $this->getProduct();
    $product->setFromArray($this->getProductParams());
    $product->save();

    parent::_postInsert();
  }

  protected function _postUpdate() {
    // Update product
    $product = $this->getProduct();
    $product->setFromArray($this->getProductParams());
    $product->save();

    parent::_postUpdate();
  }

}
