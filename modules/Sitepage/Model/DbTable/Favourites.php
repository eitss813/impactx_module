<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Favourites.php.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Model_DbTable_Favourites extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Favourite";

  //THIS IS FOR ADD fAVOURITE  LINK IS NOT SHOW 
  public function isShow($pageId) {

   $favouriteTableName = $this->info('name');
   $select = $this->select()
										->from($favouriteTableName, array('favourite_id'))
										->where('page_id = ?', $pageId)
										->limit(1);
    $findResults = $select->query()->fetchALL();
    if ( !empty($findResults) ) {
      return 1;
    } 
    else {
      return 0;
    }
  }

  //THIS FOR DELETE fAVOURITE  LINK IS NOT SHOW 
  public function isnotShow($pageId) 
  {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $favouriteTableName = $this->info('name');
    $select = $this->select()
										->from($favouriteTableName, array('favourite_id'))
										->where('page_id_for = ?', $pageId)
                    ->where('	owner_id = ?', $viewer_id)
										->limit(1);
    $findResults = $select->query()->fetchALL();

    if ((count($findResults) >= 1)) {
      return 1;
    }
    else {
			return 0;
    }
  }

  // DELETE LINK.
  public function deleteLink($page_id, $viewer_id) {

    $favouritesName = $this->info('name');
    $pagetable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pagetable->info('name');
    $select = $this->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($favouritesName, null)
            ->join($pageTableName, $favouritesName . '.page_id = ' . $pageTableName . '.page_id', array('page_id', 'title'))
            ->where($favouritesName . '.page_id_for = ?', $page_id)
            ->where($favouritesName . '.owner_id = ?', $viewer_id);
    return $this->fetchALL($select);
  }

	/**
   * Return linked pages result
   *
   * @param int $sitepage_id
	 * @param int $LIMIT
   */
  public function linkedPages($sitepage_id, $LIMIT,$params = array(), $flag = null) {

		$pagestable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pagesName = $pagestable->info('name');
    $favouritesName = $this->info('name');
    $select = $pagestable->select()
						->setIntegrityCheck(false);
						
		if (empty($flag)) {
			$select->from($pagestable, array('page_id', 'title','photo_id'))
			      ->join($favouritesName, $favouritesName . '.page_id_for = ' . $pagesName . '.page_id')
			      ->where($favouritesName . '.page_id =?', $sitepage_id);
		}
		
		//START WORK FOR PARENT PAGE AND SUB PAGE.
		if ($flag == 'subpage') {
			$select->from($pagestable, array('page_id', 'title','photo_id', 'owner_id'))
			->where($pagesName . '.	parent_id =?', $sitepage_id)
			->where($pagesName . '.	subpage =?', '1');
		}
		
	  if ($flag == 'parentpage') {
			$select->from($pagestable, array('page_id', 'title','photo_id', 'owner_id'))
			->where($pagesName . '.	page_id =?', $sitepage_id)
			->where($pagesName . '.	parent_id =?', '0')
			->where($pagesName . '.	subpage =?', '0');
		}
		//END WORK FOR PARENT PAGE AND SUB PAGE.
		
		$select->limit($LIMIT);
		if ( isset($params['category_id']) && !empty($params['category_id']) ) {
			$select = $select->where($pagesName . '.	category_id =?', $params['category_id']);
		}
		if ( isset($params['featured']) && ($params['featured'] == '1') ) {
			$select = $select->where($pagesName . '.	featured =?', '0');
		}
		elseif ( isset($params['featured']) && ($params['featured'] == '2') ) {
			$select = $select->where($pagesName . '.	featured =?', '1');
		}

		if ( isset($params['sponsored']) && ($params['sponsored'] == '1') ) {
			$select = $select->where($pagesName . '.	sponsored =?', '0');
		}
		elseif ( isset($params['sponsored']) && ($params['sponsored'] == '2') ) {
			$select = $select->where($pagesName . '.	sponsored =?', '1');
		}

    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

    return $userListings = $pagestable->fetchAll($select);
	}
}
?>