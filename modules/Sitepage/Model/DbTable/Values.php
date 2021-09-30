<?php

class Sitepage_Model_DbTable_Values extends Engine_Db_Table
{
	protected $_rowClass = 'Sitepage_Model_Value';

	public function updateValues($params = null)
	{
		$db = $this->getAdapter();
    	$db->beginTransaction();
		try {
			foreach ($params as $field_id => $arr) {
				foreach ($arr as $package_id => $value) {
					$value = Engine_Api::_()->getApi('core','sitepage')->toJsonReadableFormat($value);
					$row = $this->select()
						->where('field_id = ?', $field_id)
						->where('package_id = ?', $package_id)
						->query()
						->fetchAll();

					if (count($row) >= 1) {
						$this->update(array(
						      'value' => $value,
						    ), array(
						      '`field_id` = ?' => $field_id,
						      '`package_id` = ?' => $package_id,
						    ));
					} elseif (count($row) == 0 ) {
						$data = array('field_id' => $field_id, 'package_id' => $package_id, 'value' => $value);
						$this->insert($data);
					}
				}
			}
			$db->commit();
		} catch (Exception $e) {
	      $db->rollback();
	      throw $e;
	    }
	}


	public function deleteFieldValues($field_id = null)
	{
		$this->delete(array(
                'field_id = ?' => $field_id
            ));
	}

	public function getFieldValues($field_id = null)
	{
		if (empty($field_id)) 
			return false;

		$row = $this->select()
				->where('`field_id` = ?',$field_id)
				->query()
				->fetchAll();

		return $row;
	}
        public function addCustomValues($newfield_id,$package_option,$package_value)
	{   
			$data = array('field_id' => $newfield_id, 'package_id' => $package_option, 'value' => $package_value);
			$row = $this->insert($data);
		return $row;
	}
}



