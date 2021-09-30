<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Otherinfo.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Otherinfo extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;
    protected $_rowClass = "Sitevideo_Model_Otherinfo";

    public function getOtherinfo($channel_id) {

        $rName = $this->info('name');
        $select = $this->select()
                ->where($rName . '.channel_id = ?', $channel_id);

        $row = $this->fetchRow($select);

        if (empty($row))
            return;

        return $row;
    }

}
