<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Manageadmins.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Manageadmins extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Manageadmin";

	/**
   * Return manage admins which as user_id
   *
   * @param int $page_id
   */
  public function getManageAdminUser($page_id) {

    $usertable = Engine_Api::_()->getDbtable('users', 'user');
    $userName = $usertable->info('name');
    $manageName = $this->info('name');
    $manageHistoriesQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from($manageName)
            ->joinleft($userName, $manageName . '.page_id = ' . $userName . '.user_id', array('displayname', 'photo_id'))
            ->where('page_id = ?', $page_id)
            ->where($manageName.'.member_email is null')
            ->where($manageName.'.user_id IS NOT NULL');

    return Zend_Paginator::factory($manageHistoriesQuery);
  }

    /**
     * Return manage admins which has no user_id
     *
     * @param int $page_id
     */
    public function getInvitedAdminUsers($page_id) {
        $manageName = $this->info('name');
        $manageHistoriesQuery = $this->select()
            ->from($manageName)
            ->where('page_id = ?', $page_id)
            ->where($manageName.'.user_id =?',0)
            ->where($manageName.'.member_email IS NOT NULL');

        return Zend_Paginator::factory($manageHistoriesQuery);
    }


    public function getManageAdminUserLocation($page_id) {

        $usertable = Engine_Api::_()->getDbtable('users', 'user');
        $userName = $usertable->info('name');
        $manageName = $this->info('name');
        $manageHistoriesSelect = $this->select()
            ->setIntegrityCheck(false)
            ->from($manageName,array ( 'user_id' ))
            ->joinleft($userName, $manageName . '.page_id = ' . $userName . '.user_id',array())
            ->where('page_id = ?', $page_id);

        $locationTable = Engine_Api::_()->getDbtable('locations', 'user');
        $locationTableName = $locationTable->info( 'name' ) ;
        $locationSelect = $locationTable->select()
            ->from( $locationTableName, array('latitude','longitude','user_id') )
            ->where( 'user_id in ? ' , $manageHistoriesSelect );

        return $locationSelect->query()->fetchAll() ;

    }

	/**
   * Return manage admin ids
   *
   * @param int $page_id
   */
	public function getManageAdmin($page_id, $user_id = null) {

    $select = $this->select()
                    ->from($this->info('name'))
                    ->where('page_id = ?', $page_id);
    if (!empty($user_id)) {
			$select->where('user_id <> ?', $user_id);
    }
    return $this->fetchAll($select);
	}

	/**
   * Return manage admin pages_id
   *
   * @param int $viewer_id
   */
	public function getManageAdminPages($viewer_id) {

		$select = $this->select()
						->from($this->info('name'), 'page_id')
						->where('user_id = ?', $viewer_id);
		return $this->fetchAll($select);
	}

	/**
   * Return linked pages result
   *
   * @param int $page_id
   */
  public function linkedPages($page_id) {

    $manageAdminName = $this->info('name');
    $select = $this->select()->from($manageAdminName, 'user_id')->where('page_id = ?', $page_id);
    return $user_idarray = $this->fetchAll($select)->toArray();
	}

	/**
   * Return featured manage admins
   *
   * @param int $page_id
   */
	public function featuredAdmins($page_id) {
		
		$select = $this->select()
						->where('featured = ?', 1)
						->where('page_id = ?', $page_id);

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

		return $this->fetchAll($select);
	}

	/**
   * Return page admin.
   *
   * @param int $user_id
   * @param int $page_id
   */
	public function isPageAdmins($user_id, $page_id) {

    $select = $this->select()
						->from($this->info('name'))
						->where('user_id = ?', $user_id)
						->where('page_id = ?', $page_id);
    return $this->fetchRow($select);
	}
	
	/**
   * Return manage admin ids array
   *
   * @param int $page_id
   */
	public function getManageAdminIds($page_id, $param = null) {

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
							->from($this->info('name'), array('user_id'));
		if(!empty($param) && $param == 'pageintergration') { 
			$select->where('user_id <> ?', $viewer_id);
		}
		$user_ids = $select->where('page_id = ?', $page_id)
											->query()
											->fetchAll(Zend_Db::FETCH_COLUMN);
    return $user_ids;
	}
  
  public function getCountUserAsAdmin($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $pageTable = Engine_Api::_()->getDbTable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    
    $manageAdminTableName = $this->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($manageAdminTableName, array("COUNT(engine4_sitepage_manageadmins.page_id) AS adminCount"))
                    ->joinLeft($pageTableName, "$pageTableName.page_id = $manageAdminTableName.page_id", array())
       ->where("$manageAdminTableName.user_id = ?", $viewer_id)
       ->where("$pageTableName.owner_id != ?", $viewer_id)     
       ->group("$pageTableName.page_id")     
       ;  

    return $select->query()->fetchColumn();
  }  
  

}