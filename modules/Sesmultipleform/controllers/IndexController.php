<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_IndexController extends Core_Controller_Action_Standard
{
	//get form captcha value for ajax form submit validation.
public function getcaptchavalueAction(){
	$id = $this->_getParam('id',false);
	if($id){
		$sessioncaptchavalue = $_SESSION['Zend_Form_Captcha_'.$id]['word'] ? $_SESSION['Zend_Form_Captcha_'.$id]['word'] : '';
		echo $sessioncaptchavalue;die;
	}
	echo false;die;
}
 //Subcategory action
public function subcategoryAction() {
    $category_id = $this->_getParam('category_id', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
      $category_select = $categoryTable->select()
              ->from($categoryTable->info('name'))
              ->where('subcat_id = ?', $category_id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      if (isset($_POST['selected']))
        $selected = $_POST['selected'];
      else
        $selected = '';
      $data = '';
      if ($subcategory && $count_subcat) {
        $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose a Sub Category") . '</option>';
        foreach ($subcategory as $category) {
          $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["title"]) . '</option>';
        }
      }
    } else
      $data = '';
    echo $data;
    die;
  }
  public function subsubcategoryAction() {
    $category_id = $this->_getParam('subcategory_id', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmultipleform');
      $category_select = $categoryTable->select()
              ->from($categoryTable->info('name'))
              ->where('subsubcat_id = ?', $category_id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      if (isset($_POST['selected']))
        $selected = $_POST['selected'];
      else
        $selected = '';
      $data = '';
      if ($subcategory && $count_subcat) {
        $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose a Sub Sub Category") . '</option>';
        foreach ($subcategory as $category) {
          $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '">' . Zend_Registry::get('Zend_Translate')->_($category["title"]) . '</option>';
        }
      }
    } else
      $data = '';
    echo $data;
    die;
  }
 public function aboutusAction() {
    $this->_helper->content->setEnabled();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->aboutus = $settings->getSetting('sesmultipleform.aboutus', $this->view->translate('<p><span style="font-size: 12pt;">About Us</span></p><p>This page will contain the About Us details of your choice.</p>'));
  }
}