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
class Sitepage_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

  protected $_type = 'sitepage_page';

   /**
   * Get pagemembers list
   *
   * @param array $params
   * @param string $request_count
   * @return array $paginator;
   */
  public function getSitepagemembersPaginator($params = array(), $request_count = null) {
 
    $paginator = Zend_Paginator::factory($this->getsitepagemembersSelect($params, $request_count));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage(20);
    }
     
    return $paginator;
  }
    public function getSitepagemembersCustomPaginator($params = array(), $request_count = null) {

        $paginator = Zend_Paginator::factory($this->getsitepagemembersSelect($params, $request_count));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        $paginator->setItemCountPerPage(100);

        return $paginator;
    }
    public function getAllSitepagemembersPaginator($params = array(), $request_count = null) {

        $paginator = Zend_Paginator::factory($this->getsitepagemembersSelect($params, $request_count));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage(100);
        }

        return $paginator;
    }

    /**
   * Get page member select query
   *
   * @param array $params
   * @param string $request_count
   * @return string $select;
   */
  public function getsitepagemembersSelect($params = array(), $request_count = null) {

    $membershipTableName = $this->info('name');

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
    $pagemanageadminsTableName = $pagemanageadminsTable->info('name');

    $select = $usersTable->select()
												->setIntegrityCheck(false)
												->from($usersTableName)
												->joinleft($membershipTableName, $usersTableName . ".user_id = " . $membershipTableName . '.user_id')
												->joinleft($pageTableName, $membershipTableName . ".page_id = " . $pageTableName . '.page_id', array('owner_id AS page_owner_id'))
												->where($membershipTableName . '.resource_id = ?', $params['page_id']);
					
		if ($request_count == 'request') {
			$select = $select->where($membershipTableName . '.active = ?', '0')
			                 ->where($membershipTableName . '.resource_approved = ?', '0')
			                 ->where($membershipTableName . '.user_approved = ?', '0');
		} else {
			$select = $select->where($membershipTableName . '.active = ?', '1')
					            ->where($membershipTableName . '.user_approved = ?', '1');
		}
		
		//GET THE FRIEND OF LOGIN USER.
		$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		if($request_count == 'friend') {			
			if(!empty($friendId)) {
				$select->where($membershipTableName . '.user_id IN (?)', (array) $friendId);
			}
		}
		
	  if($request_count == 'PAGEADMINS') {
			$select = $select->join($pagemanageadminsTableName, $membershipTableName . ".user_id = " . $pagemanageadminsTableName . '.user_id')
				->where($pagemanageadminsTableName . '.page_id = ?', $params['page_id']);
		}
		
		if ($request_count == 'otherMember' && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id NOT IN (?)', (array) $friendId);
		}
		
		if(!empty($params['search'])) {
			$select = $select->where($usersTableName . ".displayname LIKE ? ", '%' . $params['search'] . '%');
		}

		if (isset($params['roles_id']) && $params['roles_id']) {
			$toDelete='-1';
			$params['roles_id'] = array_diff($params['roles_id'], array($toDelete));
			$roleIDs = implode('","',$params['roles_id']);
			$select = $select->where($membershipTableName . '.role_id LIKE ?', '%' . $roleIDs . '%');
		}
		
		if (isset($params['category_roleid']) && !empty($params['category_roleid'])) {
			$select = $select->where($membershipTableName . '.role_id = ?', $params['category_roleid']);
		}
		
		if (isset($params['orderby']) && !empty($params['orderby'])) {
			if ($params['orderby'] == 'featured') {
				$select = $select->where($membershipTableName . '.featured = ?', '1');
			} elseif($params['orderby'] == 'highlighted') {
				$select = $select->where($membershipTableName . '.highlighted = ?', '1');
			} elseif($params['orderby'] == 'displayname') { 
				$select = $select->order($usersTableName . '.displayname ASC');
			} elseif($params['orderby'] == 'pageadmin') {
				$select = $select->join($pagemanageadminsTableName, $membershipTableName . ".user_id = " . $pagemanageadminsTableName . '.user_id', null)
				->where($pagemanageadminsTableName . '.page_id = ?', $params['page_id']);
			} elseif($params['orderby'] == 'myfriend') {
			  $user_id = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
				$select = $select->where($membershipTableName . '.user_id IN (?)', (array) $user_id);
			}
		}

		$select = $select->order($membershipTableName . '.highlighted DESC')
			              ->order($membershipTableName . '.join_date DESC');
    return $select;
  }

    /**
     * Get page member select query
     *
     * @param array $params
     * @param string $request_count
     * @return string $select;
     */
    public function getsitepageAllmembersSelect($params = array(), $request_count = null) {

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $membershipTableName = $membershipTable->info('name');

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pageTableName = $pageTable->info('name');

        $followersTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
        $followersTableName = $followersTable->info('name');

        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagemanageadminsTableName = $pagemanageadminsTable->info('name');

        // page members
        $page_members_select = $membershipTable->select()
            ->from($membershipTableName,'user_id')
            ->where($membershipTableName . '.resource_id = ?', $params['page_id']);
        $page_members_ids = $page_members_select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        // page admins
        $page_admins_select = $pagemanageadminsTable->select()
            ->from($pagemanageadminsTableName,'user_id')
            ->where($pagemanageadminsTableName . '.page_id = ?', $params['page_id']);
        $page_admins_ids = $page_admins_select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        // page followers
        $followers_select = $followersTable->select()
            ->from($followersTableName,'poster_id AS user_id')
            ->where($followersTableName . '.resource_type = ?', 'sitepage_page')
            ->where($followersTableName . '.resource_id = ?', $params['page_id'])
            ->where($followersTableName . '.poster_type = ?', 'user');
        $followers_ids = $followers_select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $user_ids = array_merge($page_admins_ids,$followers_ids,$page_members_ids);
        $user_ids = array_unique($user_ids);


        $select = $usersTable->select()
            ->setIntegrityCheck(false)
            ->from($usersTableName)
            ->where($usersTableName . '.user_id IN (?)', (array) $user_ids);

        return $this->fetchAll($select);

    }



    public function getallsitepagemembersSelect( $page_id) {
        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $membershipTableName = $this->info('name');

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pageTableName = $pageTable->info('name');

        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagemanageadminsTableName = $pagemanageadminsTable->info('name');

        $manageHistoriesQuery = $usersTable->select()
            ->setIntegrityCheck(false)
            ->from($usersTableName)
            ->joinleft($membershipTableName, $usersTableName . ".user_id = " . $membershipTableName . '.user_id')
            ->joinleft($pageTableName, $membershipTableName . ".page_id = " . $pageTableName . '.page_id', array('owner_id AS page_owner_id'))
            ->where($membershipTableName . '.resource_id = ?',$page_id);
       // return $membershipTable->fetchAll($manageHistoriesQuery);


//    $paginator = Zend_Paginator::factory($this->getsitepagemembersSelect($params, $request_count));
            return $usersTable->fetchAll($manageHistoriesQuery);;
    }
    public function getallsitepagemembersSelects( $page_id) {
        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $membershipTableName = $this->info('name');

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pageTableName = $pageTable->info('name');

        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagemanageadminsTableName = $pagemanageadminsTable->info('name');

        $manageHistoriesQuery = $usersTable->select()
            ->from($usersTableName,array('user_id'))
            ->joinleft($membershipTableName, $usersTableName . ".user_id = " . $membershipTableName . '.user_id')
            ->joinleft($pageTableName, $membershipTableName . ".page_id = " . $pageTableName . '.page_id', array('owner_id AS page_owner_id'))
            ->where($membershipTableName . '.resource_id = ?',$page_id);
        // return $membershipTable->fetchAll($manageHistoriesQuery);



         return   $paginator = $manageHistoriesQuery->query()->fetchAll();

    }
    public function getinvitedsitepagemembersSelect( $page_id) {
        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $membershipTableName = $this->info('name');

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pageTableName = $pageTable->info('name');

        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagemanageadminsTableName = $pagemanageadminsTable->info('name');

        $manageHistoriesQuery = $membershipTable->select()
            ->setIntegrityCheck(false)
            ->from($membershipTableName)
            ->where($membershipTableName . '.resource_id = ?',$page_id)
            ->where($membershipTableName . '.page_id = ?',$page_id)
            ->where($membershipTableName . '.user_id = ?',0);
        // return $membershipTable->fetchAll($manageHistoriesQuery);


//    $paginator = Zend_Paginator::factory($this->getsitepagemembersSelect($params, $request_count));
        return $usersTable->fetchAll($manageHistoriesQuery);;
    }
  /**
   * Return member data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function widgetMembersData($params = array()) {

		$tableMemberName = $this->info('name');
		
		$tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$tablePageName = $tablePage->info('name');
		
		$UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$UserTableName = $UserTable->info('name');

		$select = $UserTable->select()
				->setIntegrityCheck(false)
				->from($UserTableName, array('user_id', 'username', 'displayname', 'photo_id'));
				
		if ($params['widget_name'] == 'recent')	 {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS pagemember_title','COUNT("user_id") AS JOINP_COUNT'))->limit($params['limit'])->group("$tableMemberName.user_id")->order($tableMemberName . '.join_date DESC');
		}
		
		if ($params['widget_name'] == 'featured') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS pagemember_title', 'COUNT("user_id") AS JOINP_COUNT'))->where($tableMemberName . '.featured = ?', 1)->group("$tableMemberName.user_id")->limit($params['limit']);
		}
		
		if ($params['widget_name'] == 'mostvaluable') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS pagemember_title', 'COUNT("user_id") AS JOINP_COUNT'))
			->group("$tableMemberName.user_id")->limit($params['limit']); 
		}
		
	  if ($params['widget_name'] == 'mostvaluablepages') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS pagemember_title', 'COUNT("page_id") AS MEMBER_COUNT'))
			->group("$tableMemberName.resource_id")->limit($params['limit']);
		}
		
    $select->join($tablePageName, $tableMemberName . ".resource_id = " . $tablePageName . '.page_id', array('title AS page_title', 'page_id', 'owner_id'))
					->where($tableMemberName . '.active = ?', 1)
					->where($tablePageName . '.closed = ?', '0')
					->where($tablePageName . '.approved = ?', '1')
					->where($tablePageName . '.search = ?', '1')
					->where($tablePageName . '.declined = ?', '0')
					->where($tablePageName . '.draft = ?', '1');
					
		if ($params['widget_name'] == 'mostvaluablepages') {
			$select->order('MEMBER_COUNT DESC');
		}
   
		if ($params['widget_name'] == 'mostvaluable') {
			$select->order('JOINP_COUNT DESC');
		}

		//End Network work
		return $UserTable->fetchAll($select);
  }
  
  /**
   * Return member of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function memberOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $memberOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->info('name');

		//GET PAGE TABLE NAME
		$pageTableName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');

    $UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$userTableName = $UserTable->info('name');

		$pageMembershipTableName = $this->info('name');
		
    //MAKE QUERY
    $select = $UserTable->select()
												->setIntegrityCheck(false)
												->from($userTableName)
												->join($memberOfTheDayTableName, $userTableName . '.user_id = ' . $memberOfTheDayTableName . '.resource_id')
												->join($pageMembershipTableName, $userTableName . '.user_id = ' . $pageMembershipTableName . '.user_id', array(''))
												//->where($pageTableName.'.approved = ?', '1')
												//->where($pageTableName.'.declined = ?', '0')
												//->where($pageTableName.'.draft = ?', '1')
												->where($memberOfTheDayTableName . '.resource_type = ?', 'user')
												->where($memberOfTheDayTableName . '.start_date <= ?', $date)
												->where($memberOfTheDayTableName .'.end_date >= ?', $date)
												->order('Rand()');
    return $UserTable->fetchRow($select);
  }
  
  /**
   * Return page members
   *
   * @param array $params
   * @return Zend_Db_Table_Select
   */
  public function getMembersPaginator($params = array(), $showMember = true) {


		//GET THE FRIEND OF LOGIN USER.
		$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		
    //VIDEO TABLE NAME
    $membershipTableName = $this->info('name');
    
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
    $pagePackageTableName = $pagePackagesTable->info('name');

    //QUERY MAKING
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('photo_id', 'title as sitepage_title'))
                    ->join($membershipTableName, $membershipTableName . '.resource_id = ' . $pageTableName . '.page_id')
                    ->join($userTableName, $userTableName . '.user_id = ' . $membershipTableName . '.user_id', array('COUNT("user_id") AS JOINP_COUNT', 'displayname'))
                    ->join($pagePackageTableName, "$pagePackageTableName.package_id = $pageTableName.package_id",array('package_id', 'price'))
                    ->where($membershipTableName . '.active = ?', '1')
                    ->where($membershipTableName . '.user_approved = ?', '1');

		if ($showMember == 'friend' && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id IN (?)', (array) $friendId);
		}
		
		if ($showMember == 'otherMember'  && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id NOT IN (?)', (array) $friendId);
		}

    if (!empty($params['title'])) {
      $select->where($pageTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
    }

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		if ((isset($params['orderby']) && $params['orderby'] == 'join_date') || !empty($params['viewedvideo'])) {
			$select = $select
											->order($membershipTableName .'.join_date DESC');
											//->order($membershipTableName .'.creation_date DESC');
		} elseif ((isset($params['orderby']) && $params['orderby'] == 'featured_member') || !empty($params['commentedvideo'])) {
			$select = $select->where($membershipTableName . '.featured = ?', '1')
											->order($membershipTableName .'.featured DESC')
											->order($membershipTableName .'.join_date DESC');
		}
		elseif ((isset($params['orderby']) && $params['orderby'] == 'member_count')) {
			$select = $select->order('JOINP_COUNT DESC');
		}
		
    if (!empty($params['search_member'])) {
				$select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ?", '%' . $params['search_member'] . '%');
    }

		
    if (!empty($params['category'])) {
      $select->where($pageTableName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['category_id'])) {
      $select->where($pageTableName . '.category_id = ?', $params['category_id']);
    }

		if (!empty($params['subcategory'])) {
      $select->where($pageTableName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (!empty($params['subcategory_id'])) {
      $select->where($pageTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (!empty($params['subsubcategory'])) {
      $select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (!empty($params['subsubcategory_id'])) {
      $select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    
    $select = $select->where($pageTableName . '.closed = ?', '0')
                    ->where($pageTableName . '.approved = ?', '1')
                    ->where($pageTableName . '.search = ?', '1')
                    ->where($pageTableName . '.declined = ?', '0')
                    ->where($pageTableName . '.draft = ?', '1');

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $select->group($membershipTableName . '.user_id');

    return Zend_Paginator::factory($select);
  }
  
  /**
   * Return member object
   *
   * @param int $memberId
   * @return Zend_Db_Table_Select
   */
  public function getMembersObject($memberId) {
  
    $membershipTableName = $this->info('name');
    $select = $this->select()
              ->where($membershipTableName . '.member_id = ?', $memberId);
    return $this->fetchRow($select);
  }
  
  /**
   * Return joined pages
   *
   * @param int $user_id
   * @param string $params
   * @return Zend_Db_Table_Select and paginator
   */
  public function getJoinPages($user_id, $params) {

		$tableMemberName = $this->info('name');

		$tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$tablePageName = $tablePage->info('name');

		$select = $tablePage->select()
												->setIntegrityCheck(false)
												->from($tablePageName, array('page_id','title','page_url', 'body', 'owner_id','photo_id','price','creation_date','featured','sponsored','view_count','comment_count','like_count','closed'))
												->joinleft($tableMemberName, $tablePageName . ".page_id = " . $tableMemberName . '.resource_id', null)
												->where($tableMemberName . '.active = ?', 1)
												->where($tableMemberName . '.user_id = ?', $user_id)
												->where($tablePageName . '.approved = ?', '1')
												->where($tablePageName . '.draft = ?', '1')
												->where($tablePageName . '.search = ?', '1')
												->where($tablePageName . '.closed = ?', '0')
												->where($tablePageName . '.declined = ?', '0');
												
		if ($params == 'onlymember') {
			$select->where($tablePageName . '.owner_id <> ?', $user_id);
		}

		if ($params == 'memberOfDay' || $params == 'onlymember') {
			$result = $tablePage->fetchAll($select);
		} elseif ($params == 'pageJoin') {
			$result = Zend_Paginator::factory($select);
		} 
		return $result;
  }
  
  /**
   * Check member is join.
   *
   * @param int $viewer_id
   * @param int $page_id
   * @param string $params
   * @return Zend_Db_Table_Select and paginator
   */
  public function hasMembers($viewer_id, $page_id = NULL, $params = NULL) {

    $membershipTableName = $this->info('name');

    $select = $this->select()
						->from($membershipTableName)
						->where('user_id = ?', $viewer_id);
						
		if (!empty($page_id)) {
			$select->where($membershipTableName . '.resource_id = ?', $page_id);
		}
		
		if ($params == 'Leave') {
			$select->where('active = ?', 1);
		}
		
		if ($params == 'Cancel' || $params == 'Accept' || $params == 'Reject') {
			$select->where('active = ?', 0);
		}
		if($params == 'Cancel') {
		$select->where('resource_approved = ?', 0)
		      ->where('user_approved = ?', 0);
		}
		
	  if ($params == 'Accept') {
			$select->where('resource_approved = ?', 1);
		}
		
		if ($params == 'Invite') {
			$select->where('resource_approved = ?', 1)
							->where('user_approved = ?', 1)
							->where('active = ?', 1);
	  }
		$select = $this->fetchRow($select);

    return $select;
	}
	
	/**
   * Return join members
   *
   * @param int $page_id
   * @param int $viewer_id
   * @param int $ownerId
   * @return Zend_Db_Table_Select
   */
  public function getJoinMembers($page_id, $viewer_id = null, $ownerId = null, $onlyMemberWithPhoto = 0, $temp = null) {

		$tableMemberName = $this->info('name');
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
		$select = $this->select()
							->setIntegrityCheck(false)
							->from($tableMemberName, array('user_id', 'email AS email_notification', 'action_email'))
							->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
							->where($tableMemberName . '.active = ?', 1)
							->where($tableMemberName . '.resource_approved = ?', 1)
							->where($tableMemberName . '.user_approved = ?', 1)
							->where($tableMemberName . '.page_id = ?', $page_id);
							
		if (!empty($viewer_id))  {
			$select->where($tableMemberName . '.user_id <> ?', $ownerId)
			       ->where($tableMemberName . '.user_id <> ?', $viewer_id);
		}

    if($onlyMemberWithPhoto) {
      $select->where($userTableName . '.photo_id <> ?', 0);
    }
		
		$select->order($tableMemberName . '.join_date DESC');
		if(empty($temp)) {
			$result = Zend_Paginator::factory($select);
		} else {
			$result = $this->fetchAll($select);
		}

		return $result;
  }

    public function getJoinMemberLocation($page_id) {

        $locationTable = Engine_Api::_()->getDbtable('locations', 'user');
        $locationTableName = $locationTable->info( 'name' ) ;

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');

        $memberTableName = $this->info('name');

        $locationSelect = $locationTable->select()
            ->setIntegrityCheck(false)
            ->from( $locationTableName, '*')
            ->join($userTableName, $userTableName. '.user_id = ' . $locationTableName . '.user_id',array())
            ->join($memberTableName, $memberTableName. '.user_id = ' . $userTableName . '.user_id',array('active','resource_approved','user_approved','page_id'))
            ->where($memberTableName . '.active = ?', 1)
            ->where($memberTableName . '.resource_approved = ?', 1)
            ->where($memberTableName . '.user_approved = ?', 1)
            ->where($memberTableName . '.page_id = ?', $page_id);

        return $locationSelect->query()->fetchAll() ;

    }
  
  	/**
   * Return count pages
   *
   * @param int $user_id
   * @return count
   */
  public function countPages($user_id) {

    $tableMemberName = $this->info('name');
		$select = $this->select()
									->from($tableMemberName, new Zend_Db_Expr('COUNT(*)'))
									->where($tableMemberName . '.active = ?', 1)
									->where('user_id = ?', $user_id);

		return	(integer) $select->query()->fetchColumn();
  }
  
  	/**
   * Return count pages
   *
   * @param int $user_id
   * @return count
   */
  public function listMemeberTabWidget($activTab) {
  
    $table = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $tableMembershipName = $table->info('name');

    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage'); 
    $tablePageName = $tablePage->info('name');

    $userTable = Engine_Api::_()->getDbtable('users', 'user'); 
    $userTableName = $userTable->info('name');


		$select = $userTable->select()
			->setIntegrityCheck(false)
			->from($userTableName, array('user_id', 'username', 'displayname', 'photo_id'))
			->join($tableMembershipName, $userTableName . ".user_id = " . $tableMembershipName . '.user_id', array('COUNT("user_id") AS JOINP_COUNT'))
			->join($tablePageName, $tableMembershipName . ".resource_id = " . $tablePageName . '.page_id', array('title AS sitepage_title', 'page_id', 'owner_id'))
			->where($tableMembershipName . '.active = ?', 1)
			->where($tablePageName . '.closed = ?', '0')
			->where($tablePageName . '.approved = ?', '1')
			->where($tablePageName . '.search = ?', '1')
			->where($tablePageName . '.declined = ?', '0')
			->where($tablePageName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    
    switch ($activTab->name) {
      case 'recent_pagemembers':
       $select->order($tableMembershipName . '.join_date DESC');
      break;
      case 'featured_pagemembers':
        $select->where($tableMembershipName .'.featured = ?', 1);
        $select->order('Rand()');
      break;
    }

		$select->group("$tableMembershipName.user_id");

		return Zend_Paginator::factory($select);
  }
  
  public function userRoles($params = array()) {

    return $this->select()
            ->from($this->info('name'), 'title')
            ->where('page_id =?', $params['page_id'])
            ->where('active = ?', 1)
            ->where('user_id =?', $params['user_id'])
            ->query()
            ->fetchColumn();
  }
  

  public function notificationSettings($params = array()) {

    $tableMemberName = $this->info('name');
		$select = $this->select()
									->from($tableMemberName, $params['columnName'])
									->where($tableMemberName . '.user_id = ?', $params['user_id'])
									->where('page_id = ?', $params['page_id']);
		return $select->query()->fetchColumn();
  }
}