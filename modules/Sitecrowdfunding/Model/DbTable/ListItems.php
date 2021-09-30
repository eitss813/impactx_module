<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ListItems.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_ListItems extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_ListItem';

    public function checkLeader($sitecrowdfunding) {
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
        if ($sitecrowdfunding->owner_id == $viewer->getIdentity()) {
            $isLeader = 1;
        } else {
            $isLeader = ( null !== $sitecrowdfunding->getLeaderList()->get($viewer) );
        }
        return $isLeader;
    }

    public function getProjectLeadersLocation($id){

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project',$id);
        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
        $listItemTableName = $listItemTable->info('name');
        $selectLeaders = $listItemTable->select()
            ->from($listItemTableName, array('child_id'))
            ->where("list_id = ?", $project->project_id)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $selectLeaders[] = $project->owner_id;

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');
        $userSelect = $userTable->select()
            ->from($userTableName, array ( 'user_id' ))
            ->where("$userTableName.user_id IN (?)", (array) $selectLeaders)
            ->order('displayname ASC');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'user');
        $locationTableName = $locationTable->info( 'name' ) ;
        $locationSelect = $locationTable->select()
            ->from( $locationTableName, array('latitude','longitude','user_id') )
            ->where( 'user_id in ? ' , $userSelect );

        return $locationSelect->query()->fetchAll() ;

    }

    public function getProjectLeadersCount($id){

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project',$id);
        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding');
        $listItemTableName = $listItemTable->info('name');
        $selectLeaders = $listItemTable->select()
            ->from($listItemTableName, array('child_id'))
            ->where("list_id = ?", $project->project_id)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $selectLeaders[] = $project->owner_id;

        return count($selectLeaders);

    }



}