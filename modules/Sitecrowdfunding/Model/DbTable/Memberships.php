<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Membership.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitecrowdfunding_Model_DbTable_Memberships extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_Membership';

    // Get members count
    public function getMembersCount($projectId) {

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info('name');

        $select = $membershipTable->select()
            ->from($membershipTableName, new Zend_Db_Expr('COUNT(*)'))
            ->where($membershipTableName . '.active = ?', 1)
            ->where('project_id = ?', $projectId);

        return	(integer) $select->query()->fetchColumn();
    }

    // check if viewer is joined or not
    public function isMemberJoined( $project_id ) {

        //GET THE VIEWER.
        $viewer = Engine_Api::_()->user()->getViewer() ;
        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info( 'name' ) ;
        $membershipSelect = $membershipTable->select()
            ->from( $membershipTableName , array ( 'membership_id' ) )
            ->where( 'resource_id = ?' , $project_id )
            ->where( 'project_id = ?' , $project_id )
            ->where( 'user_id = ?' , $viewer->getIdentity() )
            ->limit( 1 ) ;

        return $membershipSelect->query()->fetchAll() ;
    }

    // Add membership into project
    public function addMember($resource_id, $user_id)
    {
        $member_id_temp = $this->getMembersCount($resource_id);
        if ($member_id_temp[0]['membership_id']) {
            throw new Core_Model_Exception('Already Joined');
        }

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $row = $membershipTable->createRow();

        $row->resource_id = $resource_id;
        $row->project_id= $resource_id;
        $row->user_id = $user_id;
        $row->active = 1;
        $row->save();

        return $row;
    }

    // get list of joined members which are apporved
    public function listJoinedMembers($resource_id, $limit = null) {

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info( 'name' ) ;
        $membershipSelect = $membershipTable->select()
            ->from( $membershipTableName , array ( 'user_id' ) )
            ->where( 'resource_id = ?' , $resource_id )
            ->where( 'active = ?' , 1 )
            ->where( 'resource_approved = ?' , 1 )
            ->where( 'user_approved = ?' , 1 )
            ->where( 'project_id = ?' , $resource_id )
            ->order( 'membership_id DESC' );

        if($limit){
            $membershipSelect->limit( $limit ) ;
        }

        return $membershipSelect->query()->fetchAll() ;
    }

    // get list of joined members which are apporved
    public function listJoinedMemberLocation($resource_id) {

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info( 'name' ) ;
        $membershipSelect = $membershipTable->select()
            ->from( $membershipTableName , array ( 'user_id' ) )
            ->where( 'resource_id = ?' , $resource_id )
            ->where( 'active = ?' , 1 )
            ->where( 'resource_approved = ?' , 1 )
            ->where( 'user_approved = ?' , 1 )
            ->where( 'project_id = ?' , $resource_id )
            ->order( 'membership_id DESC' );

        $locationTable = Engine_Api::_()->getDbtable('locations', 'user');
        $locationTableName = $locationTable->info( 'name' ) ;
        $locationSelect = $locationTable->select()
            ->from( $locationTableName, array('latitude','longitude','user_id') )
            ->where( 'user_id in ? ' , $membershipSelect );

        return $locationSelect->query()->fetchAll() ;

    }

    // get list of joined members which are apporved
    public function listAllJoinedMembers($resource_id) {

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info( 'name' ) ;
        $membershipSelect = $membershipTable->select()
            ->from( $membershipTableName)
            ->where( 'resource_id = ?' , $resource_id )
            ->where( 'project_id = ?' , $resource_id )
            ->order( 'membership_id DESC' );

        return $membershipSelect->query()->fetchAll() ;
    }

    public function hasMembers($viewer_id, $project_id = NULL, $params = NULL) {

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info( 'name' ) ;
        $membershipSelect = $membershipTable->select()
            ->from($membershipTableName)
            ->where('user_id = ?', $viewer_id);

        if (!empty($project_id)) {
            $membershipSelect->where($membershipTableName . '.resource_id = ?', $project_id);
        }

        if ($params == 'Leave') {
            $membershipSelect->where('active = ?', 1);
        }

        if ($params == 'Cancel' || $params == 'Accept' || $params == 'Reject') {
            $membershipSelect->where('active = ?', 0);
        }
        if($params == 'Cancel') {
            $membershipSelect->where('resource_approved = ?', 0)
                ->where('user_approved = ?', 0);
        }

        if ($params == 'Accept') {
            $membershipSelect->where('resource_approved = ?', 1);
        }

        if ($params == 'Invite') {
            $membershipSelect->where('resource_approved = ?', 1)
                ->where('user_approved = ?', 1)
                ->where('active = ?', 1);
        }
        $membershipSelect = $this->fetchRow($membershipSelect);

        return $membershipSelect;
    }

    public function notificationSettings($params = array()) {

        $membershipTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');
        $membershipTableName = $membershipTable->info( 'name' ) ;
        $membershipSelect = $membershipTable->select()
            ->from($membershipTableName, $params['columnName'])
            ->where($membershipTableName . '.user_id = ?', $params['user_id'])
            ->where('project_id = ?', $params['project_id']);
        return $membershipSelect->query()->fetchColumn();
    }

}