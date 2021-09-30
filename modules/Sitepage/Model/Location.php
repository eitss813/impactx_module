<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Location.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Location extends Core_Model_Item_Abstract {

  public function getTable() {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('locations', 'sitepage');
    }

    return $this->_table;
  }

}

?>