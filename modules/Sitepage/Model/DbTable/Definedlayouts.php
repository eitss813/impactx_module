<?php

class Sitepage_Model_DbTable_DefinedLayouts extends Engine_Db_Table
{
	protected $_rowClass = 'Sitepage_Model_Definedlayout';

	public function getLayoutStructureType($layout_id) {
		$row = $this->select()->where('`layout_id` = ?',$layout_id)->query()->fetch();
		return $row['structure_type'];
	}

	public function getLayouts()
	{
    $select = $this->select();
    $rows = $this->fetchAll($select);
    
    return $rows;
  }

  public function getLayout($id)
  {
    $rows = $this->select()->where('definedlayout_id = ?',$id)->query()->fetch();
    return $rows;
  }

  public function getPageName($id)
  {
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageName = $pageTable->select()
    ->from($pageTable->info('name'), array('displayname'))
    ->where('page_id = ?',$id)
    ->query()
    ->fetchColumn();
    return $pageName;
  }

  public function deleteLayout($id)
  {

    //delete layout id from page table
    $pageTable = Engine_Api::_()->getItemTable('sitepage_page');
    $select = $pageTable->select()
    ->where('layout_id = ?',$id);
    $rows = $pageTable->fetchAll($select);
    foreach ($rows as $row) {
      $row->layout_id = "";
      $row->save();
    }

    //delete layout from category table
    $pageTable = Engine_Api::_()->getItemTable('sitepage_category');
    $select = $pageTable->select()
    ->where('layout_id = ?',$id);
    $rows = $pageTable->fetchAll($select);
    foreach ($rows as $row) {
      $row->layout_id = "";
      $row->save();
    }
    //delete layout from package table
    $table = Engine_Api::_()->getItemTable('sitepage_package');
    $select = $table->select()
    ->where('layout_id = ?',$id);
    $rows = $table->fetchAll($select);
    foreach ($rows as $row) {
      $row->layout_id = "";
      $row->save();
    }

    // delete data from core pages table
    $data = $this->getLayout($id);
    $table = Engine_Api::_()->getDbtable('pages', 'core');
    $select = $table->select()
    ->where('page_id = ?',$data['page_id']);
    $row = $table->fetchRow($select);
    $row->delete();

    // delete data from content table
    $table = Engine_Api::_()->getDbtable('content', 'core');
    $select = $table->select()
    ->where('page_id = ?',$data['page_id']);
    $rows = $table->fetchAll($select);
    foreach ($rows as $row) {
      $row->delete();
    }
    //delete layout
    $select = $this->select()->where('definedlayout_id=?',$id);
    $data = $this->fetchRow($select);
    $data->delete();
  }
}