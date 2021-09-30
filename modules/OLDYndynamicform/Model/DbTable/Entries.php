<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/23/2016
 * Time: 5:36 PM
 */
class Yndynamicform_Model_DbTable_Entries extends Engine_Db_Table
{
    protected $_rowClass = 'Yndynamicform_Model_Entry';

    public function getAllSubmittedEntries($form_id)
    {
        $rName = $this->info('name');

        $select = $this->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');

        return $this->fetchAll($select);
    }

    public function getSubmittedEntries($form_id,$page_no)
    {
        $rName = $this->info('name');

        $select = $this->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');

        // return $this->fetchAll($select);

         $paginator = Zend_Paginator::factory($select);

        if (!empty($page_no)) {
            $paginator->setCurrentPageNumber($page_no);
        }
        $paginator->setItemCountPerPage(5);
        return $paginator;
    }
    public function getTotalSubmittedEntries($form_id)
    {
        $rName = $this->info('name');

        $select = $this->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = ?', 'submitted')
            ->order('creation_date DESC');

        return count($this->fetchAll($select));
    }
    public function getEntriesPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getEntriesSelect($params));
    }

    public function getEntriesSelect($params = array())
    {
        $rName = $this->info('name');
        $select = $this->select()->from($rName)->setIntegrityCheck(false);

        // Manage form
        if (!empty($params['owner_id']))
            $select->where("$rName.owner_id = ?", $params['owner_id']);

       //fetch only submitted result
        if (!empty($params['submission_status']))
            $select->where("$rName.submission_status = ?", $params['submission_status']);

        // Entries of a form
        if (!empty($params['form_id']))
            $select->where("$rName.form_id = ?", $params['form_id']);

        // project_id
        if (!empty($params['project_id']))
            $select->where("$rName.project_id = ?", $params['project_id']);

        // KEYWORD TO SEARCH FORM TITLE
        if (!empty($params['keyword'])) {
            $searchTable = Engine_Api::_() -> getDbtable('search', 'core');
            $sName = $searchTable -> info('name');
            $select
                -> joinRight($sName, $sName . '.id=' . $rName . '.form_id', null)
                -> where($sName . '.type = ?', 'yndynamicform_form')
                -> where($sName . '.title LIKE ?', "%{$params['keyword']}%")
            ;
        }

        // Entry ID ? OR Form ID?
        if (!empty($params['entry_id'])) {
            $userTable = Engine_Api::_() -> getItemTable('user');
            $uName = $userTable -> info('name');
            $select -> joinLeft($uName, $uName . '.user_id=' . $rName . '.owner_id', null);
            $keyword = '%'.$params['entry_id'].'%';
            $select->where("$rName.entry_id like ? OR $rName.user_email like ? OR $uName.username like ? OR $uName.displayname like ?" , $keyword, $keyword, $keyword, $keyword, $keyword);
        }

        // Time range
        if (isset($params['start_date']) && !empty($params['start_date'])) {
            $select -> where("creation_date >= ?", date("Y:m:d H:i:s", strtotime($params['start_date'])));
        }
        if (isset($params['to_date']) && !empty($params['to_date'])) {
            $select -> where("creation_date < ?", date("Y:m:d H:i:s", strtotime($params['to_date']) + 86399));
        }

        // PROCESS ADVANCED SEARCH
        if (!empty($params['advsearch']) && !empty($params['conditional_logic'])) {
            $valueTable = Engine_Api::_()->fields()->getTable('yndynamicform_entry', 'values');

            $filedArr = $params['conditional_logic']['field_id'];
            $opArr = $params['conditional_logic']['compare'];
            $valueArr = $params['conditional_logic']['value'];
            // TYPE IS USED TO PROCESS FILE UPLOAD FIELDS
            $typeArr = $params['conditional_logic']['type'];

            // ADDITIONAL JOIN TO SEARCH CONDITIONALLY
            foreach ($filedArr as $key => $field_id) {
                // PROCESS FILE UPLOAD
                $this->addCondition($select, $valueTable, $rName, $field_id, $opArr[$key], $valueArr[$key], $typeArr[$key]);
            }
        }

        if (!empty($params['fieldOrder']) && !empty($params['direction'])) {
            $select -> order("{$params['fieldOrder']} {$params['direction']}");
        } else {
            $select->order('entry_id DESC');
        }
        return $select;
    }

    public function getSubmittedFormsOfViewer($current_form)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($viewer -> getIdentity()) {
            return $this -> select()
                -> distinct(true)
                -> from($this->info('name'), 'COUNT(form_id)')
                -> where('form_id <> ?', $current_form)
                -> where('owner_id = ?', $viewer->getIdentity())
                -> where("DATE(creation_date) = ?", date("Y-m-d"))
                -> query()->fetchColumn();
        } else {
            $ipObj = new Engine_IP();
            $ip = bin2hex($ipObj->toBinary());
            return $this -> select()
                -> distinct(true)
                -> from($this->info('name'), 'COUNT(form_id)')
                -> where('form_id <> ?', $current_form)
                -> where('ip = ?', $ip)
                -> where('owner_id = ?', $viewer->getIdentity())
                -> where("DATE(creation_date) = ?", date("Y-m-d"))
                -> query()->fetchColumn();
        }
    }

    public function addCondition(&$select, $valueTable, $rName, $field_id, $operator, $value, $type) {
        $vName = $valueTable->info('name');
        // RANDOM STRING TO PAIR EACH COMPARE
        $random = rand();
        $select->joinLeft("$vName AS t$random$field_id", "$rName.entry_id = t$random$field_id.item_id");

        if ($type == 'file_upload') {
            $notEmptyFields = array();
            $fileSelect = $valueTable->select()->where('field_id = ? ', $field_id);
            $fileFields = $valueTable->fetchall($fileSelect);
            foreach ($fileFields as $fileField) {
                $file_ids = json_decode(html_entity_decode($fileField->value))->file_ids;

                if (!empty($file_ids)) {
                    $notEmptyFields[] = $fileField->item_id;
                }
            }
            $in = implode("','", $notEmptyFields);
            if ($value) {
                $select->where("t$random$field_id.item_id IN ('$in')");
            } else {
                $select->where("t$random$field_id.item_id NOT IN ('$in')");
            }
            $select->group("t$random$field_id.item_id");
        }
        else {
            $select->where("t$random$field_id.field_id = ?", $field_id);
            switch ($operator) {
                case 'is':
                    $select->where("t$random$field_id.value = ?", $value);
                    break;
                case 'is_not':
                    $select->where("t$random$field_id.value <> ?", $value);
                    break;
                case 'contains':
                    $select->where("t$random$field_id.value LIKE ?", '%' . $value . '%');
                    break;
                case 'starts_with':
                    $select->where("t$random$field_id.value LIKE ?", $value . '%');
                    break;
                case 'ends_with':
                    $select->where("t$random$field_id.value LIKE ?", '%' . $value);
                    break;
                case 'does_not_contain':
                    $select->where("t$random$field_id.value NOT LIKE ?", '%' . $value . '%');
                    break;
                case 'is':
                    $select->where("t$random$field_id.value = ?", $value);
                    break;
                case 'after':
                case 'greater_than':
                    $select->where("t$random$field_id.value > ?", $value);
                    break;
                case 'before':
                case 'less_than':
                    $select->where("t$random$field_id.value < ?", $value);
                    break;
            }
        }
    }

    public function getEntryIDByProjectIdAndFormId($form_id,$project_id){
        $rName = $this->info('name');

        $select = $this->select()-> from($rName, 'entry_id');

        $select
            ->where("$rName.form_id = ?", $form_id)
            ->where("$rName.project_id = ?", $project_id);
        $select->order('entry_id DESC');

        return $select-> query()->fetchColumn();

    }
    public function getEntryIDByUserIdAndFormId($form_id,$user_id){
        $rName = $this->info('name');

        $select = $this->select()-> from($rName, 'entry_id');

        $select
            ->where("$rName.form_id = ?", $form_id)
            ->where("$rName.user_id = ?", $user_id);
        $select->order('entry_id DESC');

        return $select-> query()->fetchColumn();

    }
    public function getEntriesCountByFormId($form_id){
        $rName = $this->info('name');

        $select = $this->select()-> from($rName, 'entry_id');

        $select
            ->where("$rName.form_id = ?", $form_id)
            ->where("$rName.submission_status = ?", 'submitted');


        return $select-> query()->fetchAll();

    }
    public function getEntriesByFormIdProjectId($form_id,$project_id){
        $rName = $this->info('name');

        $select = $this->select()-> from($rName, 'entry_id');

        $select
            ->where("$rName.form_id = ?", $form_id)
            ->where("$rName.project_id = ?", $project_id)
            ->where("$rName.submission_status = ?", 'submitted');


        return $select-> query()->fetchAll();

    }
    public function getEntriesByEntryId($entry_id){

        $rName = $this->info('name');
        $select = $this->select()-> from($rName, '*');
        $select->where("$rName.entry_id = ?", $entry_id);

        return $select-> query()->fetchAll();

    }
}