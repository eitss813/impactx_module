<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Writes.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Writes extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Write";

  public function writeContent($page_id) {
    $select = $this->select()->where('page_id = ?', $page_id);
    return $this->fetchRow($select);
  }

  public function setWriteContent($page_id, $text) {

    $this->delete(array('page_id = ?' => $page_id));
    $row = $this->createRow();
    $row->text = $text;
    $row->page_id = $page_id;
    $row->save();
  }
}
?>