<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Entries.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_DbTable_Entries extends Engine_Db_Table {
  protected $_rowClass = 'Sesmultipleform_Model_Entry';
  protected $_name = 'sesmultipleform_entries';
	public function totalEntries($params = array()){
		$rName = $this->info('name');
    $select = $this->select()
            ->from($rName, new Zend_Db_Expr('COUNT(entry_id)'))
            ->group('form_id');
						
	if(isset($params['form_id']))
		$select->where('form_id =?',$params['form_id']);
 $entries = $select->query()
            ->fetchColumn();
						
    if (!$entries)
      return 0;
    return $entries;
	}
}