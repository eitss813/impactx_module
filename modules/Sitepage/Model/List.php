<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: List.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_List extends Core_Model_Item_Abstract {

  protected $_owner_type = 'sitepage';

  protected $_child_type = 'user';

  public $ignorePermCheck = true;
  protected $_searchTriggers = false;

  public function getListItemTable() {
    return Engine_Api::_()->getItemTable('core_like');
  }

  public function get($child) {
    $table = $this->getListItemTable();
    $select = $table->select()
      ->where('resource_id = ?', $this->page_id)
      ->where('resource_type = ?', 'sitepage_page')
      ->where('poster_id = ?', $child->getIdentity())
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function has(Core_Model_Item_Abstract $child)	{
    return ( null !== $this->get($child) );
  }
}