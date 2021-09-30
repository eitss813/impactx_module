<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Region.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Region extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

   /**
   * Delete the region and belongings
   * 
   */
  public function _delete() {

    $region_id = $this->region_id;
    $db = Engine_Db_Table::getDefaultAdapter();

    $db->beginTransaction();
    try {
     
    //  Engine_Api::_()->getDbtable('addresses', 'sitecrowdfunding')->delete(array('state = ?' => $region_id));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //DELETE PRODUCT
    parent::_delete();
  }
}