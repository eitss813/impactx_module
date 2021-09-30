<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Album extends Core_Model_Item_Collection {

    protected $_searchTriggers = false;
    protected $_modifiedTriggers = false;
    protected $_parent_type = 'sitevideo_channel';
    protected $_owner_type = 'user';
    protected $_children_types = array('sitevideo_photo');
    protected $_collectible_type = 'sitevideo_photo';

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        return $this->getOwner()->getHref($params);
    }

    public function getAuthorizationItem() {

        return $this->getParent('sitevideo_channel');
    }

    protected function _delete() {

        //DELTE ALL CHILD POST
        $photoTable = Engine_Api::_()->getItemTable('sitevideo_photo');
        $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
        foreach ($photoTable->fetchAll($photoSelect) as $sitevideoPhoto) {
            $sitevideoPhoto->delete();
        }
        parent::_delete();
    }

}
