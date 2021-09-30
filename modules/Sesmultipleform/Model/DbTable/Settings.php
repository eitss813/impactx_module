<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Settings.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_DbTable_Settings extends Engine_Db_Table {

			protected $_rowClass = "Sesmultipleform_Model_Setting";
			protected $_name = 'sesmultipleform_form_settings';
			public function getSetting($params = array())
			{
						$tableName = $this->info('name');
						$select = $this->select()
             ->from($tableName);
            $select->where('form_id = ?', (int) $params['id']);
            return $this->fetchRow($select);
            
			}
}
