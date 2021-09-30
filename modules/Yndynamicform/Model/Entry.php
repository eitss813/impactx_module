<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/23/2016
 * Time: 5:36 PM
 */
class Yndynamicform_Model_Entry extends Core_Model_Item_Abstract
{
    protected $_type = 'yndynamicform_entry';

    public function isEditable()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        if ($viewer->isAdmin()) {
            return true;
        } else {
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $this -> form_id);
            if (!$yndform -> entries_editable || is_null($yndform) || ($this->owner_id && !$viewer -> isSelf($this -> getOwner()))) {
                return false;
            }
            $time_unit = $yndform -> time_unit == 'hour' ? 60 : 1;
            $entries_pass_time = floatval((strtotime(date("Y-m-d H:i:s")) - strtotime($this -> creation_date)) / (60*$time_unit));
            if ($yndform -> entries_editable_within > $entries_pass_time)
                return true;
            else
                return false;
        }
    }

    public function isDeletable()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        if ($viewer->isAdmin()) {
            return true;
        } else {
            return false;
        }
    }

    public function isViewable()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $allowGuest = false;
        if($this -> getIdentity() && !$this -> owner_id && !$viewer->getIdentity())
        {
            $ipObj = new Engine_IP();
            $ipExpr = bin2hex($ipObj->toBinary());
            $entryIP = bin2hex($this -> ip);
            if ($ipExpr == $entryIP)
                $allowGuest = true;
        }
        $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $this -> form_id);
        if ($allowGuest || $viewer->isAdmin() || $yndform -> isModerator($viewer) || ($this -> owner_id && $viewer -> isSelf($this -> getOwner()))) {
            return true;
        } else {
            return false;
        }
    }

    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'yndynamicform_entry_specific',
            'action' => 'view',
            'entry_id' => $this -> getIdentity(),
        ), $params);

        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, true);
    }

    public function getForm()
    {
        return Engine_Api::_() -> getItem('yndynamicform_form', $this -> form_id);
    }

    public function getFormOptionID()
    {
        return Engine_Api::_() -> getItem('yndynamicform_form', $this -> form_id) -> option_id;
    }

    public function saveFiles($files)
    {
        if (is_array($files) && !empty($files['tmp_name'])) {
            $file_names = $files['tmp_name'];
        } else if (is_string($files) && file_exists($files)) {
            $file_names = $files;
        } else {
            throw new Group_Model_Exception('invalid argument passed to saveFile');
        }

        $file_ids = array();

        $storage = Engine_Api::_() -> storage();

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        foreach ($file_names as $key => $file) {
            move_uploaded_file($file, "$path/". $files['name'][$key]);

            $params = array(
                'parent_type' => 'yndynamicform_entry',
                'parent_id' => $this -> getIdentity(),
                'type' => $files['type'][$key],
            );

            $fMain = $storage -> create("$path/". $files['name'][$key], $params);

            // Remove temp files
            @unlink("$path/". $files['name'][$key]);
            @unlink($file);

            array_push($file_ids, $fMain -> file_id);
        }

        // Return all file ids
        return $file_ids;
    }

    public function getFilesCount()
    {
        $fileTable = Engine_Api::_() -> getDbTable('files', 'storage');
        $select = $fileTable->select()
            ->from($fileTable, new Zend_Db_Expr("COUNT('file_id')"))
            ->where('parent_type = ?', 'yndynamicform_entry')
            ->where('parent_id = ?', $this->getIdentity());
        return $select->query()->fetchColumn(0);
    }

    public function updateView()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        // add view if current viewer has not view this entry
        if ($viewer && !$this->isViewed()) {
            $table = Engine_Api::_()->getDbTable('views', 'yndynamicform');
            $now =  date("Y-m-d H:i:s");
            $row = $table -> createRow();
            $row -> user_id = $viewer->getIdentity();
            $row -> entry_id = $this->getIdentity();
            $row -> creation_date = $now;
            $row -> save();
        }
    }

    public function isViewed()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        if (!$viewer->getIdentity())
            return false;

        $table = Engine_Api::_()->getDbTable('views', 'yndynamicform');

        $select = $table -> select()
            -> where('user_id = ?', $viewer->getIdentity())
            -> where('entry_id = ?', $this->getIdentity())
            -> limit(1);

        $row = $table -> fetchRow($select);

        return empty($row) ? false : true;
    }
}