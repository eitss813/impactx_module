<?php

class Sitepage_Model_DbTable_Templates extends Engine_Db_Table
{
	protected $_rowClass = 'Sitepage_Model_Template';

	public function getDefaultTemplates() {
		$rows = $this->select()
					->where('`default` = ?', 1)
					->query()
					->fetchAll();

		return $rows;
	}

	public function getFieldsJSON($id , $isLayoutId = null) {
		$select = $this->select();
		$isLayoutId ? $select->where('`layout` = ?',$id)->where('`default` = ?', 1) : $select->where('`template_id` = ?',$id);
		$row = $select->limit(1)->query()->fetch();

		return $row['template_values'];
	}
	
	public function getTemplates()
	{
		$rows = $this->select()
					->query()
					->fetchAll();

		return $rows;
	}

	public function getBaseTemplate($id)
	{
		$rows = $this->select()->where('`template_id` = ?',$id)->where('`default` = ?','1')->limit(1)->query()->fetch();
		return $rows;
	}

	public function getTemplate($id)
	{
		$rows = $this->select()->where('`template_id` = ?',$id)->query()->fetch();
		return $rows;
	}

	public function activateTemplate($id )
	{
		$this->update(array(
			      'active' => '0',
			    ), array());

		$this->update(array(
			      'active' => '1',
			    ), array(
			      '`template_id` = ?' => $id,
			    ));
	}

	public function setValues($params)
	{
	    $this->update($params, array(
			      '`template_id` = ?' => $params['template_id'],
			    ));
	}

	public function createTemplate($params = null)
	{
		$db = $this->getAdapter();
        $db->beginTransaction();

        try {
            $template = $this->createRow();
            $template->setFromArray($params);
            $template->save();
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
	}

	public function getActivatedTemplate()
	{
		$rows = $this->select()
					->where('active = ?','1')
					->limit(1)
					->query()
					->fetch();

		if (empty($rows)) 
		$rows = $this->select()->limit(1)->query()->fetch();

		return $rows;
	}
        public function customActiveTemplate($id)
	{
		$rows = $this->select()
                                        ->where('layout = ?', $id)
					->where('active = ?','1')
					->query()
					->fetchColumn();
		return $rows;
	}
}