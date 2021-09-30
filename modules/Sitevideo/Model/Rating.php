<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rating.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Rating extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;
    protected $_type = 'sitevideo_rating';

    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        }
        return $this->_table;
    }

    public function getOwner($recurseType = null) {
        return parent::getOwner();
    }

}
