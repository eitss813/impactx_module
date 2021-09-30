<?php

class Sitepage_Model_DbTable_Fields extends Engine_Db_Table
{
	protected $_rowClass = 'Sitepage_Model_Field';

	public function getFields($structureType = null)
	{
		if ($structureType == null)
			return false;

		$rows = $this->select()
					->order('feature_order ASC')
					->where('`structure_type` = ?',$structureType)
					->query()
					->fetchAll();

		return $rows;
	}

	public function addNewField($params)
	{
		$lastFiled = $this->select()->order('feature_order DESC')->where('`structure_type` = ?',$params['structure_type'])->limit(1)->query()->fetch();
		$order = ( $lastFiled['feature_order'] == null || $lastFiled['feature_order'] == 0 ) ? 1 : ++$lastFiled['feature_order'];
		$params['feature_order'] = $order;
		$params['feature_label'] = Engine_Api::_()->getApi('core','sitepage')->toJsonReadableFormat($params['feature_label']);
		$row = $this->insert($params);
		return $row;
	}

	public function updateFields($params = null)
	{
		$db = $this->getAdapter();
    	$db->beginTransaction();
		try {
			foreach ($params as $field_id => $field_name) {
				$field_name = Engine_Api::_()->getApi('core','sitepage')->toJsonReadableFormat($field_name);
				$this->update(array(
				      'feature_label' => $field_name,
				    ), array(
				      '`field_id` = ?' => $field_id,
				    ));
			}
			$db->commit();
		}	catch (Exception $e) {
	      $db->rollback();
	      throw $e;
		}
	}
        public function addCustomField($fieldDetail,$structureDetail)
	{   $para = array();
		$lastFiled = $this->select()->order('feature_order DESC')->where('`structure_type` = ?',$structureDetail)->limit(1)->query()->fetch();
		$order = ( $lastFiled['feature_order'] == null || $lastFiled['feature_order'] == 0 ) ? 1 : ++$lastFiled['feature_order'];
                $para['structure_type'] = $structureDetail;  
                $para['feature_order'] = $order;
		$para['feature_label'] = Engine_Api::_()->getApi('core','sitepage')->toJsonReadableFormat($fieldDetail);
		$row = $this->insert($para);
		return $row;
	}
        
        public function removeField($type)
	{
		$lastFiled = $this->select()->where('`structure_type` = ?',$type)->query()->fetchAll();
                foreach ($lastFiled as $value) {
                    foreach ($value as $key => $case) {
                      //echo "Field_id".$case;  
                      $delete = Engine_Api::_()->getDbtable('values','sitepage')->deleteFieldValues($case);
                      break;
                    }
                    $delete = Engine_Api::_()->getDbtable('fields','sitepage')->deleteField($case);
                }
	}
        
        public function deleteField($field_id = null)
	{
		$this->delete(array(
                'field_id = ?' => $field_id
            ));
	}
        
        public function changeOrder($type)
	{
            $this->update(array(
                'feature_order' => 999,
                    ), array(
                '`feature_label` = ?' => 'Description',
                '`structure_type` = ?' => $type,
                ));
	}


}