<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Album extends Core_Model_Item_Collection {

    protected $_searchTriggers = false;
    protected $_modifiedTriggers = false;
    protected $_parent_type = 'sitecrowdfunding_project';
    protected $_owner_type = 'user';
    protected $_children_types = array('sitecrowdfunding_photo');
    protected $_collectible_type = 'sitecrowdfunding_photo';

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        return $this->getOwner()->getHref($params);
    }

    public function getAuthorizationItem() {

        return $this->getParent('sitecrowdfunding_project');
    }

    protected function _delete() {

        //DELTE ALL CHILD POST
        $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
        $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
        foreach ($photoTable->fetchAll($photoSelect) as $projectPhoto) {
            $projectPhoto->delete();
        }
        parent::_delete();
    }

}