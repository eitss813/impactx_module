<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rating.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_Rating extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  
  public function getTable() {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('ratings', 'sitealbum');
    }
    return $this->_table;
  }

  public function getOwner($recurseType = null) {
    return parent::getOwner();
  }
}