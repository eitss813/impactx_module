<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Videomap.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Videomap extends Core_Model_Item_Abstract {
    /*
     * THIS FUNCTION USED TO RETURN THE SITEVIDEO MODEL 
     */

    public function getVideoDetail() {
        $videos = new Sitevideo_Model_DbTable_Videos();
        $row = $videos->fetchRow($videos->select()->where('video_id = ?', $this->video_id));
        return $row;
    }

}
