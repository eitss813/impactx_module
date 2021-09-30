<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Keycontacts.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_DbTable_Keycontacts extends Engine_Db_Table {

  protected $_rowClass = "Sesmultipleform_Model_Keycontact";
protected $_name = 'sesmultipleform_keycontacts';
  public function getContacts($param = array()) {

    $tableName = $this->info('name');
    $select = $this->select()
            ->from($tableName)
            ->order('order DESC');            
    if (isset($param['fetchAll']))
      return $this->fetchAll($select);
    return Zend_Paginator::factory($select);
  }

  public function getContactsList($param = array()) {

    $tableName = $this->info('name');
    $select = $this
      ->select()
      ->from($tableName);
          if (isset($param['listtype'])) {
          if($param['listtype']== 'order')
          {
			   $select->order('order DESC');
		      }
		      elseif($param['listtype']== 'random')
		      {
		      $select->order('rand()');
		      }
		      else {
	      $select->order('keycontact_id DESC');
		    }
    }

        if (isset($param['fetchAll']))
      return $this->fetchAll($select);
    return Zend_Paginator::factory($select);

  }
}
