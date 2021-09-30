<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
class Yndynamicform_Model_Category extends Yndynamicform_Model_Node
{
    protected $_searchTriggers = false;
    protected $_parent_type = 'user';

    protected $_owner_type = 'user';

    protected $_type = 'yndynamicform_category';

    public function getCategoryParent()
    {
        $parent_item = Engine_Api::_()->getItem('yndynamicform_category', $this->parent_id);
        return $parent_item;
    }

    public function getHref($params = array()) {

        $params = array_merge(array(
            'route' => 'yndynamicform_general',
            'controller' => 'index',
            'action' => 'list-forms',
            'category_id' => $this->getIdentity(),
        ), $params);

        $route = $params['route'];
        unset($params['route']);
        unset($params['type']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, true);
    }

    public function getTable() {
        if(is_null($this -> _table)) {
            $this -> _table = Engine_Api::_() -> getDbtable('categories', 'yndynamicform');
        }
        return $this -> _table;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function checkHasForm()
    {
        $list_categories = array();
        $table = Engine_Api::_() -> getItemTable('yndynamicform_form');
        Engine_Api::_()->getItemTable('yndynamicform_category') -> appendChildToTree($this, $list_categories);
        foreach($list_categories as $category)
        {
            $select = $table -> select() -> where('category_id = ?', $category -> category_id) -> limit(1);
            $row = $table -> fetchRow($select);
            if($row)
                return $category -> category_id;
        }
        return false;
    }

//    public function getMoveCategories()
//    {
//        $table = Engine_Api::_() -> getDbtable('categories', 'yndynamicform');
//        $select = $table -> select()
//            -> where('category_id <>  ?', 1) // not default
//            -> where('category_id <>  ?', $this->getIdentity());// not itseft
//        $result = $table -> fetchAll($select);
//        return $result;
//    }

    public function setTitle($newTitle) {
        $this -> title = $newTitle;
        $this -> save();
        return $this;
    }

    public function shortTitle() {
        return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
    }

    public function getChildList() {
        $table = Engine_Api::_()->getItemTable('yndynamicform_category');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }

    public function getNumOfForms() {
        $table = Engine_Api::_()->getItemTable('yndynamicform_form');
        $select = $table->select();
        $select
            ->where('category_id = ?', $this->getIdentity())
            ->where('status = ?', 'open')
            ->where('approved_status = ?', 'approved')
            ->where('search = ?', 1);
        $childList = $table->fetchAll($select);
        return count($childList);
    }
}