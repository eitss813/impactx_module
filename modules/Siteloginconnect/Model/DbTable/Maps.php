<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Google.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_Model_DbTable_Maps extends Engine_Db_Table {

    public function getMapping($site)
    {   
        if(empty($site)) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $db = Engine_Db_Table::getDefaultAdapter();
        $profileTypeMeta = $db->query(" SELECT `field_id` FROM `engine4_user_fields_meta` WHERE `type` = 'profile_type' ")->fetch();
        $profileTypeMeta = $profileTypeMeta["field_id"];

        $viewerProfileType = $db->query("SELECT `value` FROM `engine4_user_fields_values` WHERE `item_id` = {$viewer->getIdentity()} AND `field_id` = {$profileTypeMeta} ")->fetch();

        return $this->fetchAll($this->select()
                    ->where("profile_type = ? ", $viewerProfileType["value"])
                    ->where("social_site = ?", $site)
                )->toarray();
    }
}