<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Views.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_DbTable_Views extends Engine_Db_Table {

    public function insertView($params) {
        try {
            if (empty($params["viewer_id"]) || empty($params["user_id"])) {
                return;
            }

            $this->delete(array("viewer_id =?" => $params['viewer_id'], "user_id =?" => $params["user_id"]));
            $this->insert(array_merge($params, array("date" => date('Y-m-d H:i:s'))));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAllIdsViewedByMe($viewer_id) {
        if (empty($viewer_id)) {
            return;
        }
        $select = $this->select()->from($this->info('name'), array('user_id'))
                ->where("viewer_id =? ", $viewer_id)
                ->order("date DESC");

        $viewedIdsMe = $this->fetchAll($select);

        $ids = Engine_Api::_()->sitemember()->getArrayByColumn($viewedIds, "user_id");
        return $ids;
    }

    public function getAllIdsViewedByUsers($subject_id) {
        if (empty($subject_id)) {
            return;
        }
        $select = $this->select()->from($this->info('name'), array('viewer_id'))
                ->where("user_id =? ", $subject_id)
                ->order("date DESC");

        $viewedIdsUsers = $this->fetchAll($select);

        $ids = Engine_Api::_()->sitemember()->getArrayByColumn($viewedIdsUsers, "viewer_id");
        return $ids;
    }

}
