<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
class Yndynamicform_AdminCategoriesController extends Core_Controller_Action_Admin
{
    protected $_paginate_params = array();
    public function init() {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yndynamicform_admin_main', array(), 'yndynamicform_admin_main_categories');
    }

    public function getDbTable() {
        return Engine_Api::_() -> getDbTable('categories', 'yndynamicform');
    }

    public function indexAction() {
        $table = $this -> getDbTable();
        $node = $table -> getNode($this -> _getParam('parent_id', 0));
        $this -> view -> categories = $node -> getChilren();
        $this -> view -> category = $node;
    }

    public function addCategoryAction() {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');

        // Generate and assign form
        $parentId = $this -> _getParam('parent_id', 0);
        $form = $this -> view -> form = new Yndynamicform_Form_Admin_Category();
        $form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $table = $this -> getDbTable();
        $node = $table -> getNode($parentId);
        //maximum 3 level category
        if ($node -> level > 2) {
            throw new Zend_Exception('Maximum 3 levels of category.');
        }
        // Check post
        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
            // we will add the category
            $values = $form -> getValues();
            $user = Engine_Api::_() -> user() -> getViewer();
            $data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
            $table -> addChild($node, $data);
            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
        }

        // Output
        $this -> renderScript('admin-categories/form.tpl');
    }

    public function deleteCategoryAction() {

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('id');
        $this -> view -> category_id = $id;
        $table = $this -> getDbTable();
        $node = $table -> getNode($id);

        // Get all category
        $categories = $table -> getCategories(0);

        // Get all categories can move to
        $this->view->moveCates = $categories;
        $this->view->moveNode = $node->getTitle();
        $this->view->moveNodeID = $node->category_id;

        $this->view->hasForms = $hasForms = $node -> checkHasForm();
        $tableForm = Engine_Api::_() -> getItemTable('yndynamicform_form');
        // Check post
        if ($this -> getRequest() -> isPost()) {
            // go through logs and see which classified used this category and set it to ZERO
            if (is_object($node)) {
                //set video category to 0
                if ($hasForms) {
                    $forms = $tableForm -> getAllChildrenFormsByCategory($node);
                    foreach ($forms as $items) {
                        foreach ($items as $formItem) {
                            $db = $tableForm -> getAdapter();
                            try {
                                $db -> beginTransaction();
                                $formItem -> category_id = $this->_getParam('move_category', 0);
                                $formItem -> save();

                                $db -> commit();
                            } catch(Exception $e) {
                                $db -> rollBack();
                                throw $e;
                            }
                        }
                    }
                }
                $table -> deleteNode($node);
            }
            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
        }
    }

    public function editCategoryAction() {

        // Must have an id
        if (!($id = $this -> _getParam('id'))) {
            throw new Zend_Exception('No identifier specified');
        }
        // Generate and assign form
        $category = Engine_Api::_() -> getItem('yndynamicform_category', $id);

        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $form = $this -> view -> form = new Yndynamicform_Form_Admin_Category( array('category' => $category));
        $form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
        $isSub = false;
        if ($category -> parent_id != '1') {
            $isSub = true;
        }

        // Check post
        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
            // Ok, we're good to add field
            $values = $form -> getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                // edit category in the database
                // Transaction
                $row = Engine_Api::_() -> getItem('yndynamicform_category', $values["id"]);
                $row -> title = $values["label"];
                $row -> save();
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
            }
            $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
        }

        $form -> setField($category, $isSub);

        // Output
        $this -> renderScript('admin-categories/form.tpl');
    }

    public function ajaxUseParentCategoryAction() {
        $categoryId = $this -> _getParam('id');
        $category = Engine_Api::_() -> getItem('yndynamicform_category', $categoryId);
        $category -> save();
    }

    public function sortAction() {
        $table = $this -> getDbTable();
        $node = $table -> getNode($this -> _getParam('parent_id', 0));
        $categories = $node -> getChilren();
        $order = explode(',', $this -> getRequest() -> getParam('order'));
        foreach ($order as $i => $item) {
            $category_id = substr($item, strrpos($item, '_') + 1);
            foreach ($categories as $category) {
                if ($category -> category_id == $category_id) {
                    $category -> order = $i;
                    $category -> save();
                }
            }
        }
    }
}