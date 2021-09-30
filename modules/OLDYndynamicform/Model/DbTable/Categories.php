<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
class Yndynamicform_Model_DbTable_Categories extends Yndynamicform_Model_DbTable_Nodes
{
    protected $_rowClass = 'Yndynamicform_Model_Category';

    public function getFirstCategory()
    {
        $select = $this->select();
        $select -> order('category_id ASC');
        $select -> limit(2);
        $select -> where('category_id <> 1');
        $item  = $this->fetchRow($select);
        return $item;
    }

    public function deleteNode(Yndynamicform_Model_Node $node, $node_id = NULL) {
        parent::deleteNode($node);
    }

    public function getCategories($showAllCates = 1) {
        $table = Engine_Api::_() -> getDbTable('categories', 'yndynamicform');
        $tree = array();
        $node = $table -> getNode(1);
        $this->appendChildToTree($node, $tree);
        if (!$showAllCates) {
            unset($tree[0]);
        }
        return $tree;
    }

    public function appendChildToTree($node, &$tree) {
        array_push($tree, $node);
        $children = $node->getChilren();
        foreach ($children as $child_node) {
            $this->appendChildToTree($child_node, $tree);
        }
    }

    /**
     * @This function find the path tree of the inputed category
     */
    public function getPath($category)
    {
        while ($category -> level > 1)
        {
            $category = $category -> getCategoryParent();
        }
        $tree = array();
        $node = $this->getNode($category->category_id);
        Engine_Api::_()->getItemTable('yndynamicform_category')->appendChildToTree($node, $tree);
        return $tree;
    }
}