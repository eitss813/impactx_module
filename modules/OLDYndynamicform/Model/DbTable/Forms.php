<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
class Yndynamicform_Model_DbTable_Forms extends Engine_Db_Table
{
    protected $_rowClass = 'Yndynamicform_Model_Form';

    protected $_serializedColumns = array(
        'page_break_config'
    );

    // get organisation's forms
    public function getOrganisationFormsPaginator($params  = array())
    {
        return Zend_Paginator::factory($this -> getOrganisationFormsSelect($params));
    }

    public function getOrganisationFormsSelect($params  = array())
    {
        $table = Engine_Api::_() -> getDbtable('forms', 'yndynamicform');
        $rName = $table -> info('name');

        $categoryTbl = Engine_Api::_() -> getDbTable('categories', 'yndynamicform');
        $catName = $categoryTbl -> info('name');

        $projectFormTable = Engine_Api::_()->getDbTable('projectforms', 'sitepage');
        $projectFormName = $projectFormTable->info('name');

        $tmTable = Engine_Api::_() -> getDbtable('TagMaps', 'core');
        $tmName = $tmTable -> info('name');

        $select = $this -> select() -> from($rName) -> setIntegrityCheck(false);

        // Keyword
        if (!empty($params['keyword'])) {
            $searchTable = Engine_Api::_() -> getDbtable('search', 'core');
            $sName = $searchTable -> info('name');
            $select
                -> joinRight($sName, $sName . '.id=' . $rName . '.form_id', null)
                -> where($sName . '.type = ?', 'yndynamicform_form')
                -> where($sName . '.title LIKE ?', "%{$params['keyword']}%");
        }

        // Title
        if (!empty($params['title'])) {
            $select -> where("$rName.title LIKE ?", "%{$params['title']}%");
        }

        // page_id
        if (!empty($params['page_id'])) {
            $select -> where("$rName.page_id = ?", $params['page_id']);
        }

        // Isenable ?
        if (isset($params['status']) && is_numeric($params['status'])) {
            $select -> where($rName . '.enable = ?', $params['status']);
        }

        // Category
        if (!empty($params['category_id']) && $params['category_id'] != 'all') {
            $node = $categoryTbl -> getNode($params['category_id']);
            if ($node) {
                $tree = array();
                Engine_Api::_() -> getItemTable('yndynamicform_category') -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node -> category_id);
                }
                $select -> where('category_id IN (?)', $categories);
            }
        }

        // valid form
        if (empty($params['valid_form'])) {
            $select->where("DATE(valid_from_date) <= DATE(?) AND (DATE(valid_to_date) >= DATE(?) OR unlimited_time = 1)", date("Y-m-d"), date("Y-m-d"));
        }

        // Endtime
        if (isset($params['start_date']) && !empty($params['start_date'])) {
            $select -> where("creation_date >= ?", date("Y-m-d 00-00-01", strtotime($params['start_date'])));
        }
        if (isset($params['to_date']) && !empty($params['to_date'])) {
            $select -> where("creation_date < ?", date("Y-m-d 23-59-59", strtotime($params['to_date'])));
        }

        if (isset($params['moderated_forms']) && is_numeric($params['moderated_forms'])) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            $moderatedForms = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform') -> getModeratedForms($viewer);
            $select -> where("$rName.form_id IN (?)", $moderatedForms);
        }

        if (isset($params['privacy'])) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($viewer -> getIdentity()) {
                $select -> where("$rName.privacy = 2 or $rName.privacy = 3");
            }
            else {
                $select -> where("$rName.privacy = 1 or $rName.privacy = 3");
            }
        }

        if (isset($params['form_id'])) {
            $select -> where("$rName.form_id <> ?", $params['form_id']);
        }

        // get forms assigned
        $projectFormSelect = $projectFormTable ->select()
            ->from($projectFormName,array('count(*)'))
            ->where("$projectFormName.form_id = $rName.form_id");

        $select->columns(array(
            "projects_assigned" => new Zend_Db_Expr(
                '('.new Zend_Db_Expr( 'COALESCE('.new Zend_Db_Expr('('.$projectFormSelect.')').',0)').')'
            )
        ));

        // Order.
        /*
        $order = 'creation_date';
        if (!empty($params['order'])) {
            $order = $params['order'];
        }
        switch ($order) {
            case 'title_asc':
                $select -> order("$rName.title ASC");
                break;
            case 'title_desc':
                $select -> order("$rName.title DESC");
                break;
            case 'most_entries':
                $select -> order("$rName.total_entries DESC");
                break;
            case 'most_view':
                $select -> order("$rName.view_count DESC");
                break;
            default:
                $select -> order("$rName.form_id DESC");
                break;
        }
        */
        if (!empty($params['fieldOrder'])) {
            switch ($params['fieldOrder']) {
                case 'category':
                    $select -> join($catName, "$rName.category_id = $catName.category_id", null)
                        -> order("$catName.title {$params['direction']}");
                    break;
                case 'conversation_rate':
                    $select -> group("$rName.form_id") -> from($rName, array("($rName.total_entries/$rName.view_count) as conversation"))->order("conversation {$params['direction']}");
                    break;
                default:
                    $select -> order("{$params['fieldOrder']} {$params['direction']}");
                    break;
            }
        }


        if (isset($params['limit'])) {
            $select -> limit($params['limit']);
        }

        return $select;
    }


    public function getFormsPaginator($params  = array())
    {
        return Zend_Paginator::factory($this -> getFormsSelect($params));
    }

    public function getFormsSelect($params  = array())
    {
        $table = Engine_Api::_() -> getDbtable('forms', 'yndynamicform');
        $rName = $table -> info('name');

        $categoryTbl = Engine_Api::_() -> getDbTable('categories', 'yndynamicform');
        $catName = $categoryTbl -> info('name');

        $tmTable = Engine_Api::_() -> getDbtable('TagMaps', 'core');
        $tmName = $tmTable -> info('name');

        $select = $this -> select() -> from($rName) -> setIntegrityCheck(true);

        // Keyword
        if (!empty($params['keyword'])) {
            $searchTable = Engine_Api::_() -> getDbtable('search', 'core');
            $sName = $searchTable -> info('name');
            $select
                 -> joinRight($sName, $sName . '.id=' . $rName . '.form_id', null)
                 -> where($sName . '.type = ?', 'yndynamicform_form')
                 -> where($sName . '.title LIKE ?', "%{$params['keyword']}%")
            ;
        }

        // Title
        if (!empty($params['title'])) {
            $select -> where("$rName.title LIKE ?", "%{$params['title']}%");
        }

        // page_id
        if (!empty($params['page_id'])) {
            $select -> where("$rName.page_id = ?", $params['page_id']);
        }

        // Isenable ?
        if (isset($params['status']) && is_numeric($params['status'])) {
            $select -> where($rName . '.enable = ?', $params['status']);
        }

        // Category
        if (!empty($params['category_id']) && $params['category_id'] != 'all') {
            $node = $categoryTbl -> getNode($params['category_id']);
            if ($node) {
                $tree = array();
                Engine_Api::_() -> getItemTable('yndynamicform_category') -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node -> category_id);
                }
                $select -> where('category_id IN (?)', $categories);
            }
        }

        if (!empty($params['fieldOrder'])) {
            switch ($params['fieldOrder']) {
                case 'category':
                    $select -> join($catName, "$rName.category_id = $catName.category_id", null)
                            -> order("$catName.title {$params['direction']}");
                    break;
                case 'conversation_rate':
                    $select -> group("$rName.form_id") -> from($rName, array("($rName.total_entries/$rName.view_count) as conversation"))->order("conversation {$params['direction']}");
                    break;
                default:
                    $select -> order("{$params['fieldOrder']} {$params['direction']}");
                    break;
            }
        }

        if (empty($params['valid_form'])) {
            $select->where("DATE(valid_from_date) <= DATE(?) AND (DATE(valid_to_date) >= DATE(?) OR unlimited_time = 1)", date("Y-m-d"), date("Y-m-d"));
        }

        // Endtime
        if (isset($params['start_date']) && !empty($params['start_date'])) {
            $select -> where("creation_date >= ?", date("Y-m-d 00-00-01", strtotime($params['start_date'])));
        }
        if (isset($params['to_date']) && !empty($params['to_date'])) {
            $select -> where("creation_date < ?", date("Y-m-d 23-59-59", strtotime($params['to_date'])));
        }

        if (isset($params['moderated_forms']) && is_numeric($params['moderated_forms'])) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            $moderatedForms = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform') -> getModeratedForms($viewer);
            $select -> where("$rName.form_id IN (?)", $moderatedForms);
        }

        if (isset($params['privacy'])) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($viewer -> getIdentity()) {
                $select -> where("$rName.privacy = 2 or $rName.privacy = 3");
            }
            else {
                $select -> where("$rName.privacy = 1 or $rName.privacy = 3");
            }
        }

        if (isset($params['form_id'])) {
            $select -> where("$rName.form_id <> ?", $params['form_id']);
        }

        // Order.
        $order = 'creation_date';
        if (!empty($params['order'])) {
            $order = $params['order'];
        }
        switch ($order) {
            case 'title_asc':
                $select -> order("$rName.title ASC");
                break;
            case 'title_desc':
                $select -> order("$rName.title DESC");
                break;
            case 'most_entries':
                $select -> order("$rName.total_entries DESC");
                break;
            case 'most_view':
                $select -> order("$rName.view_count DESC");
                break;
            default:
                $select -> order("$rName.creation_date DESC");
                break;
        }

        if (isset($params['limit'])) {
            $select -> limit($params['limit']);
        }
        return $select;
    }

    public function getAllChildrenFormsByCategory($node)
    {
        $return_arr = array();
        $cur_arr = array();
        $list_categories = array();
        Engine_Api::_() -> getItemTable('yndynamicform_category') -> appendChildToTree($node, $list_categories);
        foreach($list_categories as $category)
        {
            $select = $this -> select() -> where('category_id = ?', $category -> category_id);
            $cur_arr = $this -> fetchAll($select);
            if(count($cur_arr) > 0)
            {
                $return_arr[] = $cur_arr;
            }
        }
        return $return_arr;
    }
}