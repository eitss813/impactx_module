<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: AdminCategoriesController.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_AdminCategoriesController extends Core_Controller_Action_Admin {

  public function indexAction() {

    if (isset($_POST['selectDeleted']) && $_POST['selectDeleted']) {
      if (isset($_POST['data']) && is_array($_POST['data'])) {
        $deleteCategoryIds = array();
        foreach ($_POST['data'] as $key => $valueSelectedcategory) {
          $categoryDelete = Engine_Api::_()->getItem('sesblog_category', $valueSelectedcategory);
          $deleteCategory = Engine_Api::_()->getDbtable('categories', 'sesblog')->deleteCategory($categoryDelete);
          if ($deleteCategory) {
            $deleteCategoryIds[] = $categoryDelete->category_id;
            $categoryDelete->delete();
          }
        }
        echo json_encode(array('diff_ids' => array_diff($_POST['data'], $deleteCategoryIds), 'ids' => $deleteCategoryIds));
        die;
      }
    }
    if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == 1) {
      $value['title'] = isset($_POST['title']) ? $_POST['title'] : '';
      $value['color'] = isset($_POST['color']) ? str_replace('#','',$_POST['color']) : '';
      $value['category_name'] = isset($_POST['category_name']) ? $_POST['category_name'] : '';
      $value['description'] = isset($_POST['description']) ? $_POST['description'] : '';
      $value['slug'] = isset($_POST['slug']) ? $_POST['slug'] : '';
      $value['profile_type'] = isset($_POST['profile_type']) ? $_POST['profile_type'] : '';
      $value['parent'] = $cat_id = isset($_POST['parent']) ? $_POST['parent'] : '';
      $slugExists = Engine_Api::_()->getDbtable('categories', 'sesblog')->slugExists($value['slug']);
      if (!$slugExists) {
        echo json_encode(array('slugError' => true));
        die;
      }
      if ($cat_id != -1) {
        $categoryData = Engine_Api::_()->getItem('sesblog_category', $cat_id);
        if ($categoryData->subcat_id == 0) {
          $value['subcat_id'] = $cat_id;
          $seprator = '&nbsp;&nbsp;&nbsp;';
          $tableSeprator = '-&nbsp;';
          $parentId = $cat_id;
          $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesblog')->orderNext(array('subcat_id' => $cat_id));
        } else {
          $value['subsubcat_id'] = $cat_id;
          $seprator = '3';
          $tableSeprator = '--&nbsp;';
          $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesblog')->orderNext(array('subsubcat_id' => $cat_id));
          $parentId = $cat_id;
        }
      } else {
        $parentId = 0;
        $seprator = '';
        $value['order'] = Engine_Api::_()->getDbtable('categories', 'sesblog')->orderNext(array('category_id' => true));
        $tableSeprator = '';
      }
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sesblog');
        //Create row in categories table
        $row = $categoriesTable->createRow();
        if(isset($_POST['member_levels']))
        $row->member_levels = implode(',',$_POST['member_levels']);
        $row->setFromArray($value);
        $row->save();
        //Upload categories icon
        if (isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != '') {
          $row->cat_icon = $this->setPhoto($_FILES['icon'], $row->category_id);
        }
        if (isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != '') {
          $row->thumbnail = $this->setPhoto($_FILES['thumbnail'], $row->category_id, true);
        }
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      if (isset($row->cat_icon) && $row->cat_icon != '') {
        $cat_icon = Engine_Api::_()->storage()->get($row->cat_icon);
        if($cat_icon) {
            $data = '<img  class="sesbasic-category-icon"  src="' . Engine_Api::_()->storage()->get($row->cat_icon)->getPhotoUrl('thumb.icon') . '" />';
        }
      } else {
        $data = "---";
      }
      $tableData = '<tr id="categoryid-' . $row->category_id . '"><td><input type="checkbox" name="delete_tag[]" class="checkbox" value="' . $parentId . '" /></td><td>' . $data . '</td><td>' . $tableSeprator . $row->category_name . ' <div class="hidden" style="display:none" id="inline_' . $row->category_id . '"><div class="parent">' . $parentId . '</div></div></td><td>' . $row->slug . '</td><td>' . $this->view->htmlLink(array("route" => "admin_default", "module" => "sesblog", "controller" => "categories", "action" => "edit-category", "id" => $row->category_id, "catparam" => "subsub"), $this->view->translate("Edit"), array()) . ' | ' . $this->view->htmlLink('javascript:void(0);', $this->view->translate("Delete"), array("class" => "deleteCat", "data-url" => $row->category_id)) . '</td></tr>';
      echo json_encode(array('seprator' => $seprator, 'tableData' => $tableData, 'id' => $row->category_id, 'name' => $row->category_name, 'slugError' => false));
      die;
    }
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_categories', array(), 'sesblog_admin_main_subcategories');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_categories');
    //profile types
    $profiletype = array();
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sesblog_blog');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $options = $profileTypeField->getElementParams('sesblog_blog');
      unset($options['options']['order']);
      unset($options['options']['multiOptions']['0']);
      $profiletype = $options['options']['multiOptions'];
    }
    $this->view->profiletypes = $profiletype;
    //Get all categories
    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sesblog')->getCategory(array('column_name' => '*', 'profile_type' => true));
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
      $checkTypeCategory = $dbObject->query("SELECT * FROM engine4_sesblog_categories WHERE category_id = " . $category_id)->fetchAll();
      if (isset($checkTypeCategory[0]['subcat_id']) && $checkTypeCategory[0]['subcat_id'] != 0) {
        $categoryType = 'subcat_id';
        $categoryTypeId = $checkTypeCategory[0]['subcat_id'];
      } else if (isset($checkTypeCategory[0]['subsubcat_id']) && $checkTypeCategory[0]['subsubcat_id'] != 0) {
        $categoryType = 'subsubcat_id';
        $categoryTypeId = $checkTypeCategory[0]['subsubcat_id'];
      } else
        $categoryType = 'category_id';
      if ($checkTypeCategory)
        $currentOrder = Engine_Api::_()->getDbtable('categories', 'sesblog')->order($categoryType, $categoryTypeId);
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
        echo "false";
        die;
      }
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesblog');
      //for ($i = count($order) - 1; $i>0; $i--) {
      for ($i = 0; $i < count($order); $i++) {
        $category_id = $order[$i - $start];
        $categoryTable->update(array(
            'order' => $i,
                ), array(
            'category_id = ?' => $category_id,
        ));
      }
      $checkCategoryChildrenCondition = $dbObject->query("SELECT * FROM engine4_sesblog_categories WHERE subcat_id = '" . $id . "' || subsubcat_id = '" . $id . "' || subcat_id = '" . $nextid . "' || subsubcat_id = '" . $nextid . "'")->fetchAll();
      if (empty($checkCategoryChildrenCondition)) {
        echo 'done';
        die;
      }
      echo "children";
      die;
    }
  }

  //Edit Category
  public function editCategoryAction() {

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_categories', array(), 'sesblog_admin_main_subcategories');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesblog_admin_main', array(), 'sesblog_admin_main_categories');

    $this->view->form = $form = new Sesblog_Form_Admin_Category_Edit();

    $cat_id = $this->_getParam('id');
    $category = Engine_Api::_()->getItem('sesblog_category', $cat_id);
    if($category->subcat_id == 0) {
      $category->member_levels = explode(",",$category->member_levels);
    }
    else
      $form->removeElement('member_levels');

    $form->populate($category->toArray());
    if ($category->subcat_id == 0 && $category->subsubcat_id == 0) {
      $form->setTitle('Edit This Category');
      $form->category_name->setLabel('Category Name');
    } elseif ($category->subcat_id != 0 && $category->subsubcat_id == 0) {
      $form->setTitle('Edit Sub Category');
      $form->category_name->setLabel('Sub Category Name');
    } elseif ($category->subsubcat_id != 0) {
      $form->setTitle('Edit 3rd Category');
      $form->category_name->setLabel('3rd Category Name');
    }

    //Check post
    if (!$this->getRequest()->isPost())
      return;

    //Check
    if (!$form->isValid($this->getRequest()->getPost())) {
      if (isset($_POST['slug'])) {
        $slugExists = Engine_Api::_()->getDbtable('categories', 'sesblog')->slugExists($value['slug'], $cat_id);
        if (!$slugExists) {
          $form->addError($this->view->translate("Slug not avilable."));
        }
      }
      if (empty($_POST['category_name'])) {
        $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
      }
      return;
    }

    $values = $form->getValues();
    if (empty($values['cat_icon']))
      unset($values['cat_icon']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $category->title = isset($_POST['title']) ? $_POST['title'] : '';
      $category->color = isset($_POST['color']) ? $_POST['color'] : '';
      $category->category_name = isset($_POST['category_name']) ? $_POST['category_name'] : '';
      $category->description = isset($_POST['description']) ? $_POST['description'] : '';
      $category->slug = isset($_POST['slug']) ? $_POST['slug'] : '';
      $category->profile_type = isset($_POST['profile_type']) ? $_POST['profile_type'] : '';
      $category->category_name = $values['category_name'];
      if(isset($values['member_levels']))
      $category->member_levels = implode(',',$values['member_levels']);
      $category->save();
      $deleteIc = $deleteTh = true;
      $previousCatIcon = $category->cat_icon;
      $previousThumbnailIcon = $category->thumbnail;
      if (isset($values['remove_cat_icon']) && !empty($values['remove_cat_icon'])) {
        //Delete categories icon
        $catIcon = Engine_Api::_()->getItem('storage_file', $previousCatIcon);
        $category->cat_icon = 0;
        $category->save();
        $catIcon->delete();
        $deleteIc = false;
      }
      if (isset($values['remove_thumbnail_icon']) && !empty($values['remove_thumbnail_icon'])) {
        //Delete categories icon
        $thumbnailIcon = Engine_Api::_()->getItem('storage_file', $previousThumbnailIcon);
        $category->thumbnail = 0;
        $category->save();
        $thumbnailIcon->delete();
        $deleteTh = false;
      }
      //Upload categories icon
      if (isset($_FILES['cat_icon']) && !empty($_FILES['cat_icon']['name'])) {
        $Icon = $this->setPhoto($form->cat_icon, $cat_id);
        if (!empty($Icon)) {
          if ($previousCatIcon && $deleteIc) {
            $catIcon = Engine_Api::_()->getItem('storage_file', $previousCatIcon);
            $catIcon->delete();
          }
          $category->cat_icon = $Icon;
          $category->save();
        }
      }
      // UPLOAD categories thumbnail
      if (isset($_FILES['thumbnail']) && !empty($_FILES['thumbnail']['name'])) {
        $Thumbnail = $this->setPhoto($form->thumbnail, $cat_id, true);
        if (!empty($Thumbnail)) {
          if ($previousThumbnailIcon && $deleteTh) {
            $ThumbnailIcon = Engine_Api::_()->getItem('storage_file', $previousThumbnailIcon);
            $ThumbnailIcon->delete();
          }
          $category->thumbnail = $Thumbnail;
          $category->save();
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'sesblog', 'action' => 'index', 'controller' => 'categories'), 'admin_default', true);
  }

  protected function setPhoto($photo, $cat_id, $resize = false) {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }
    if (!$fileName) {
      $fileName = $file;
    }
    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'sesblog_category',
        'parent_id' => $cat_id,
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    if ($resize) {
      // Resize image (main)
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_poster.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(800, 800)
              ->write($mainPath)
              ->destroy();
      // Resize image (normal) make same image for activity feed so it open in pop up with out jump effect.
      $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_thumb.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(500, 500)
              ->write($normalPath)
              ->destroy();
    } else {
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_poster.' . $extension;
      copy($file, $mainPath);
    }
    if ($resize) {
      // normal main  image resize
      $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_icon.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(100, 100)
              ->write($normalMainPath)
              ->destroy();
    } else {
      $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_icon.' . $extension;
      copy($file, $normalMainPath);
    }
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      if ($resize) {
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iMain->bridge($iIconNormal, 'thumb.thumb');
      }
      $iNormalMain = $filesTable->createFile($normalMainPath, $params);
      $iMain->bridge($iNormalMain, 'thumb.icon');
    } catch (Exception $e) {
      // Remove temp files
      @unlink($mainPath);
      if ($resize) {
        @unlink($normalPath);
      }
      @unlink($normalMainPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Sesblog_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    // Remove temp files
    @unlink($mainPath);
    if ($resize) {
      @unlink($normalPath);
    }
    @unlink($normalMainPath);
    // Update row
    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $iMain->file_id;
  }
}
