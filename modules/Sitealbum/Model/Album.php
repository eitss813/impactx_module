<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_Album extends Core_Model_Item_Abstract {

    protected $_parent_type = 'user';
    protected $_owner_type = 'user';
    protected $_parent_is_owner = true;
    protected $_type = 'album';

    public function getType($inflect = false) {
        if ($inflect) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_type)));
        }

        return $this->_type;
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'sitealbum_entry_view',
            'reset' => true,
            'slug' => $this->getSlug(),
            'album_id' => $this->getIdentity(),
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $maxstrlen = 255) {

        if (null === $str) {
            $str = $this->title;
        }

        $maxstrlen = 225;

        return Engine_Api::_()->seaocore()->getSlug($str, $maxstrlen);
    }

    /**
     * Gets a url to the current photo representing this item. Return null if none
     * set
     *
     * @param string The photo type (null -> main, thumb, icon, etc);
     * @return string The photo url
     */
    public function getPhotoUrl($type = null) {
        if (empty($this->photo_id)) {
            $photoTable = Engine_Api::_()->getItemTable('album_photo');
            $photoInfo = $photoTable->select()
                    ->from($photoTable, array('photo_id', 'file_id'))
                    ->where('album_id = ?', $this->album_id)
                    ->order('order ASC')
                    ->limit(1)
                    ->query()
                    ->fetch();
            if (!empty($photoInfo)) {
                $this->photo_id = $photo_id = $photoInfo['photo_id'];
                $this->save();
                $file_id = $photoInfo['file_id'];
            } else {
                return;
            }
        } else {
            $photoTable = Engine_Api::_()->getItemTable('album_photo');
            $file_id = $photoTable->select()
                    ->from($photoTable, 'file_id')
                    ->where('photo_id = ?', $this->photo_id)
                    ->query()
                    ->fetchColumn();
        }

        if (!$file_id) {
            return;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, $type);
        if (!$file) {
            return;
        }

        return $file->map();
    }

    public function getFirstPhoto() {
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $select = $photoTable->select()
                ->where('album_id = ?', $this->album_id)
                ->order('order ASC')
                ->limit(1);
        return $photoTable->fetchRow($select);
    }

    public function getLastPhoto() {
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $select = $photoTable->select()
                ->where('album_id = ?', $this->album_id)
                ->order('order DESC')
                ->limit(1);
        return $photoTable->fetchRow($select);
    }

    public function count() {
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        return $photoTable->select()
                        ->from($photoTable, new Zend_Db_Expr('COUNT(photo_id)'))
                        ->where('album_id = ?', $this->album_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
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

    /**
     * Delete the album and belongings
     * 
     */
    protected function _postDelete() {

        $album_id = $this->album_id;

        try {
            Engine_Api::_()->fields()->getTable('album', 'search')->delete(array('item_id = ?' => $album_id));
            Engine_Api::_()->fields()->getTable('album', 'values')->delete(array('item_id = ?' => $album_id));

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $addlocationsTable = Engine_Api::_()->getDbTable('addlocations', 'sitetagcheckin');
                $addlocationsSelect = $addlocationsTable->select()
                        ->from($addlocationsTable->info('name'), array('addlocation_id'))
                        ->where('resource_id = ?', $this->getIdentity())
                        ->where('resource_type = ?', 'album');
                $addlocations = $addlocationsTable->fetchAll($addlocationsSelect);
                foreach ($addlocations as $addlocation) {
                    $addlocation->delete();
                }
            }

            $locationitemsTable = Engine_Api::_()->getDbTable('locationitems', 'seaocore');
            $locationitemsSelect = $locationitemsTable->select()
                    ->from($locationitemsTable->info('name'), array('locationitem_id'))
                    ->where('resource_id = ?', $this->getIdentity())
                    ->where('resource_type = ?', 'album');
            $locationitems = $locationitemsTable->fetchAll($locationitemsSelect);
            foreach ($locationitems as $locationitem) {
                $locationitem->delete();
            }

            // DELETE ALBUMS ENTRY FROM RATING TABLE
            $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitealbum');
            $ratingSelect = $ratingTable->select()
                    ->from($ratingTable->info('name'), array('rating_id'))
                    ->where('resource_id = ?', $this->getIdentity())
                    ->where('resource_type = ?', 'album')

            ;
            $ratings = $ratingTable->fetchAll($ratingSelect);
            foreach ($ratings as $rating) {
                $rating->delete();
            }

            //DELETE ALBUM ENTRY FROM ITEMOFTHEDAY TABLE
            $itemofthedays = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum');
            $itemofthedays->delete(array('resource_id =?' => $album_id, 'resource_type =?' => 'album'));

            //DELETE ALL PHOTOS RELATED TO ALBUMS
            $photoTable = Engine_Api::_()->getItemTable('album_photo');
            $photoSelect = $photoTable->select()
                    ->where('album_id = ?', $this->getIdentity())
            ;
            foreach ($photoTable->fetchAll($photoSelect) as $photo) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                    $addlocationsSelect = $addlocationsTable->select()
                            ->from($addlocationsTable->info('name'), array('addlocation_id'))
                            ->where('resource_id = ?', $photo->getIdentity())
                            ->where('resource_type = ?', 'album_photo');
                    $addlocations = $addlocationsTable->fetchAll($addlocationsSelect);
                    foreach ($addlocations as $addlocation) {
                        $addlocation->delete();
                    }
                }

                $photo->skipAlbumDeleteHook = true;
                $photo->delete();
            }
        } catch (Exception $e) {
            // Silence
        }

        parent::_postDelete();
    }

    public function isViewableByNetwork() {
        $regName = 'view_privacy_' . $this->getGuid();
        if (!Zend_Registry::isRegistered($regName)) {
            $flage = true;
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.network', 0);
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.networkprofile.privacy', 0);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($enableNetwork && $viewPricavyEnable && !$this->isOwner($viewer)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                if (Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {

                    if ($this->networks_privacy) {
                        if (!empty($viewerNetworkIds)) {
                            $albumNetworkId = explode(",", $this->networks_privacy);
                            $commanIds = array_intersect($albumNetworkId, $viewerNetworkIds);
                            if (empty($commanIds))
                                $flage = false;
                        } else {
                            $flage = false;
                        }
                    }
                } else {
                    if (!empty($viewerNetworkIds)) {
                        $ownerNetworkIds = $networkMembershipTable->getMembershipsOfIds($this->getOwner('user'));
                        if ($ownerNetworkIds) {
                            $commanIds = array_intersect($ownerNetworkIds, $viewerNetworkIds);
                            if (empty($commanIds))
                                $flage = false;
                        }
                    }
                }
            }
            Zend_Registry::set($regName, $flage);
        } else {
            $flage = Zend_Registry::get($regName);
        }
        return $flage;
    }

    public function checkPasswordProtection($password = null) {

        if (!$password)
            return false;

        $albumTable = Engine_Api::_()->getItemTable('album');
        $db = $albumTable->getAdapter();
        $select = $albumTable->select()
                ->from($albumTable, new Zend_Db_Expr('TRUE'))
                ->where('album_id = ?', $this->getIdentity())
                ->where('password = ?', $password)
                ->limit(1)
        ;
        
        $valid = $select
                ->query()
                ->fetchColumn()
        ;
        return $valid;
    }

}
