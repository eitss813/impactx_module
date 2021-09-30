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
class Sitecrowdfunding_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

    protected $_type = 'sitecrowdfunding_project';

    public function getJoinedCount($projectId) {

        $usersTable = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding');

        $usersTableName = $usersTable->info('name');

        $select = $this->select()
            ->from($tableMemberName, new Zend_Db_Expr('COUNT(*)'))
            ->where($tableMemberName . '.active = ?', 1)
            ->where('project_id = ?', $projectId);

        return	(integer) $select->query()->fetchColumn();
    }

}