<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_Model_DbTable_Locations extends Engine_Db_Table
{

    protected $_rowClass = 'User_Model_Location';

    public function getLocationByUserId($user_id){
        //MAKE QUERY
        $select = $this->select()
            ->where('user_id = ?', $user_id);
        //RETURN RESULTS
        return $this->fetchRow($select);
    }

    public function getLocationByUserIds($params = array()){

        //MAKE QUERY
        $select = $this->select()->where('user_id IN (?)', (array) $params['user_ids']);

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function getOrganisationUsersLocations($params = array()){

        $followersTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
        $followTableName = $followersTable->info('name');

        $locationTableName = $this->info('name');

        $select = $this->select()
            ->from($locationTableName)
            ->join($followTableName, "$followTableName.poster_id = $locationTableName.user_id", null)
            ->where('poster_type = ?', 'user')
            ->where('resource_type = ?', 'sitepage_page')
            ->where('resource_id = ?',  $params['page_id']);

        return $this->fetchAll($select);

    }

}