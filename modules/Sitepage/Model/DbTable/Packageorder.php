<?php

class Sitepage_Model_DbTable_Packageorder extends Engine_Db_Table
{
	protected $_rowClass = 'Sitepage_Model_Packageorder';

	public function getPackageOrder() {
		$select = $this->select()->query()->fetchAll();

		foreach ($select as $value)
			$returnData[$value['package_id']] = $value['order'];

		return $returnData;
	}
}