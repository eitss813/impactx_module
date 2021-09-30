<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rating.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Model_Rating extends Core_Model_Item_Abstract {

  /**
   * Return video rating table object
   *
   * @return table object
   * */
  public function getTable() {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('ratings', 'sitepagevideo');
    }
    return $this->_table;
  }

}
?>