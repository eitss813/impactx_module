<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: List.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_List extends Core_Model_List {

    protected $_owner_type = 'sitecrowdfunding_project';
    protected $_child_type = 'user';
    public $ignorePermCheck = true;

    public function getListItemTable() {
        return Engine_Api::_()->getItemTable('sitecrowdfunding_list_item');
    }

}