<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Subscription.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Subscription extends Core_Model_Item_Abstract {

    protected $_type = 'sitevideo_subscription';

    /*
     * THIS FUNCTION USED TO FIND THE PLAYLISTS TABLE MODEL
     */

    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        }
        return $this->_table;
    }

    public function getChannelModel() {

        $channelModel = Engine_Api::_()->getItem('sitevideo_channel', $this->channel_id);
        return $channelModel;
    }

}
