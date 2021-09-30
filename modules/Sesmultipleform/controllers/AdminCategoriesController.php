<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminCategoriesController.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_AdminCategoriesController extends Core_Controller_Action_Admin {
  public function indexAction() {  
		$this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
		if(!$id){
			$forms = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm(array('fetchAll'=>true,'limit'=>1,'active'=>true));
			if(count($forms)){
				$form_id = $forms[0];
				return $this->_helper->redirector->gotoRoute(array('module' => 'sesmultipleform', 'action' => 'index', 'controller' => 'categories','id' => $form_id->form_id), 'admin_default', true);	
			}
		}
    if (isset($_POST['selectDeleted']) && $_POST['selectDeleted']) {
      if (isset($_POST['data']) && is_array($_POST['data'])) {
        $deleteCategoryIds = array();
        foreach ($_POST['data'] as $key => $valueSelectedcategory) {
          $categoryDelete = Engine_Api::_()->getItem('sesmultipleform_category', $valueSelectedcategory);
  
          $deleteCategory = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->deleteCategory($categoryDelete);
          if ($deleteCategory) {
            $deleteCategoryIds[] = $categoryDelete->category_id;
            $categoryDelete->delete();
          }
        }
        echo json_encode(array('diff_ids' => array_diff($_POST['data'], $deleteCategoryIds), 'ids' => $deleteCategoryIds));die;
      }
    }
    if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == 1) {
      $value['title'] = isset($_POST['title']) ? $_POST['title'] : '';
      $value['form_id'] = isset($_POST['form_id']) ? $_POST['form_id'] : '';
      $value['profile_type'] = isset($_POST['profile_type']) ? $_POST['profile_type'] : '';
      $value['parent'] = $cat_id = isset($_POST['parent']) ? $_POST['parent'] : '';
      if ($cat_id != -1) {
        $categoryData = Engine_Api::_()->getItem('sesmultipleform_category', $cat_id);
        if ($categoryData->subcat_id == 0) {
          $value['subcat_id'] = $cat_id;
          $seprator = '&nbsp;&nbsp;&nbsp;';
          $tableSeprator = '-&nbsp;';
          $parentId = $cat_id;
          $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->orderNext(array('subcat_id' => $cat_id));
        } else {
          $value['subsubcat_id'] = $cat_id;
          $seprator = '3';
          $tableSeprator = '--&nbsp;';
          $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->orderNext(array('subsubcat_id' => $cat_id));
          $parentId = $cat_id;
        }
      } else {
        $parentId = 0;
        $seprator = '';
        $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->orderNext(array('category_id' => true));
        $tableSeprator = '';
      }
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
        //Create row in categories table
        $row = $categoriesTable->createRow();
        $row->setFromArray($value);
        $row->save();
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $tableData = '<tr id="categoryid-' . $row->category_id . '" data-article-id="' . $row->category_id . '" style="cursor:move;"><td><input type="checkbox" name="delete_tag[]" class="checkbox check-column" value="' . $row->category_id . '" /></td><td>' . $tableSeprator . $row->title . ' <div class="hidden" style="display:none" id="inline_' . $row->category_id . '"><div class="parent">' . $parentId . '</div></div></td><td>' . $this->view->htmlLink(array("route" => "admin_default", "module" => "sesmultipleform", "controller" => "categories", "action" => "edit-category", "id" => $row->category_id, "catparam" => "subsub",'form_id'=>$value['form_id']), $this->view->translate("Edit"), array()) . ' | ' . $this->view->htmlLink('javascript:void(0);', $this->view->translate("Delete"), array("class" => "deleteCat", "data-url" => $row->category_id)) . '</td></tr>';
      echo json_encode(array('seprator' => $seprator, 'tableData' => $tableData, 'id' => $row->category_id, 'name' => $row->title));die;
    }
		$this->view->getForms = Engine_Api::_()->getDbtable('forms', 'sesmultipleform')->getForm(array('fetchAll'=>true,'active'=>true));
	  $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_categories');
    //profile types
     $profiletype = array();
     $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sesmultipleform_entry');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $options = $profileTypeField->getElementParams('sesmultipleform');
      unset($options['options']['order']);
      unset($options['options']['multiOptions']['0']);
      $profiletype = $options['options']['multiOptions'];
    }
    $this->view->profiletypes = $profiletype;    
		$this->view->id = $id = $this->_getParam('form_id',$this->_getParam('id'));
		//Get all categories
    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->getCategory(array('column_name' => '*', 'profile_type' => true,'id'=>$id));
  }
  public function changeOrderAction() {  
    if ($this->_getParam('id', false) || $this->_getParam('nextid', false)) {
      $id = $this->_getParam('id', false);
      $order = $this->_getParam('articleorder', false);
      $order = explode(',', $order);
      $nextid = $this->_getParam('nextid', false);
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      if ($id) {
        $category_id = $id;
      } else if ($nextid) {
        $category_id = $id;
      }
      $categoryTypeId = '';
      $checkTypeCategory = $dbObject->query("SELECT * FROM engine4_sesmultipleform_categories WHERE category_id = " . $category_id)->fetchAll();
      if (isset($checkTypeCategory[0]['subcat_id']) && $checkTypeCategory[0]['subcat_id'] != 0) {
        $categoryType = 'subcat_id';
        $categoryTypeId = $checkTypeCategory[0]['subcat_id'];
      } else if (isset($checkTypeCategory[0]['subsubcat_id']) && $checkTypeCategory[0]['subsubcat_id'] != 0) {
        $categoryType = 'subsubcat_id';
        $categoryTypeId = $checkTypeCategory[0]['subsubcat_id'];
      } else
        $categoryType = 'category_id';
      if ($checkTypeCategory)
        $currentOrder = Engine_Api::_()->getDbtable('categories', 'sesmultipleform')->order($categoryType, $categoryTypeId);
      // Find the starting point?
      $start = null;
      $end = null;
      $order = array_reverse(array_values(array_intersect($order, $currentOrder)));
      for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
        if (in_array($currentOrder[$i], $order)) {
          $start = $i;
          $end = $i + count($order);
          break;
        }
      }
      if (null === $start || null === $end) {
        echo "false"; die;
      }
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
      for ($i = 0; $i < count($order); $i++) {
        $category_id = $order[$i - $start];
        $categoryTable->update(array(
            'order' => $i,
                ), array(
            'category_id = ?' => $category_id,
        ));
      }
      $checkCategoryChildrenCondition = $dbObject->query("SELECT * FROM engine4_sesmultipleform_categories WHERE subcat_id = '" . $id . "' || subsubcat_id = '" . $id . "' || subcat_id = '" . $nextid . "' || subsubcat_id = '" . $nextid . "'")->fetchAll();
      if (empty($checkCategoryChildrenCondition)) {
        echo 'done'; die;
      }
      echo "children";die;
    }
  }
  //Edit Category
  public function editCategoryAction() {
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
    $this->view->form = $form = new Sesmultipleform_Form_Admin_Category_Edit();
		$this->view->form_id = $form_id = $this->_getParam('form_id');
    $cat_id = $this->_getParam('id');
    $category = Engine_Api::_()->getItem('sesmultipleform_category', $cat_id);
    $form->populate($category->toArray());
    if ($category->subcat_id == 0 && $category->subsubcat_id == 0) {
      $form->setTitle('Edit This Category');
      $form->title->setLabel('Category Title');
    } elseif ($category->subcat_id != 0) {
      $form->setTitle('Edit This 2nd-Level Category');
      $form->title->setLabel('2nd-Level Category Title');
    } else{
      $form->setTitle('Edit This 3rd-Level Category');
      $form->title->setLabel('3rd-Level Category Title');
    }
		$form->profile_type->setLabel('Map Profile Type');
    //Check post
    if (!$this->getRequest()->isPost())
      return;
    //Check 
    if (!$form->isValid($this->getRequest()->getPost())) {
      if (empty($_POST['title'])) {
        $form->addError($this->view->translate("Category Title * Please complete this field - it is required."));
      }
      return;
    }
    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $category->title = isset($_POST['title']) ? $_POST['title'] : '';
      $category->profile_type = isset($_POST['profile_type']) ? $_POST['profile_type'] : '';
      $category->title = $values['title'];
      $category->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'sesmultipleform', 'action' => 'index', 'controller' => 'categories','id' => $form_id), 'admin_default', true);
  }
}