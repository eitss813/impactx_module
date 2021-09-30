<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Album extends Core_Model_Item_Collection {

  protected $_parent_type = 'sitepage_page';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitepage_photo');
  protected $_collectible_type = 'sitepage_photo';
  protected $_searchTriggers = false;

  /**
   * Return page object
   *
   * @return page object
   * */
  public function getParent($recurseType = null) {
    
    if($recurseType == null) $recurseType = 'sitepage_page';
    
    return Engine_Api::_()->getItem($recurseType, $this->page_id);
  }
  
  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params 
   * @return string
   */
  public function getHref($params = array()) {
    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.sitemobile-photos-sitepage', $this->page_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $this->page_id, $layout);
		}
    $params = array_merge(array(
        'route' => 'sitepage_albumphoto_general',
        'reset' => true,
        'page_id' => $this->page_id,
        'album_id' => $this->getIdentity(),
        'slug' => $this->getSlug(),
        'tab' =>$tab_id
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
  }

  /**
   * Return a alubm slug
   *
   * @return slug
   * */
  public function getSlug($str = null, $maxstrlen = 255) {
    
    if( null === $str ) {
      $str = $this->getTitle();
    }

    return Engine_Api::_()->seaocore()->getSlug($str, 225);
  }

  /**
   * Return a album title
   *
   * @return title
   * */
  public function getTitle() {

    return $this->title;
  }

  /**
   * Return a truncate ownername
   *
   * @param int ownername 
   * @return truncate ownername
   * */
  public function truncateOwner($owner_name) {

    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

  /**
   * Delete Photos
   * */
  protected function _delete() {

    $photoSelect = Engine_Api::_()->getItemTable('sitepage_photo')->select()->where('album_id = ?', $this->getIdentity());
    foreach (Engine_Api::_()->getDbTable('photos', 'sitepage')->fetchAll($photoSelect) as $sitepagePhoto) {
      $sitepagePhoto->delete();
    }
    parent::_delete();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

}

?>