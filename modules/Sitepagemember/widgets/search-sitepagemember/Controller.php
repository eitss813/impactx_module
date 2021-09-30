<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Widget_SearchSitepagememberController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->showTabArray = $showTabArray = $this->_getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4","4" => "5"));

    $sitepagetable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $sitepageName = $sitepagetable->info('name');
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    //FORM CREATION
    $this->view->form = $form = new Sitepagemember_Form_Searchwidget(array('type' => 'sitepage_page'));
    $sitepagememberSearchType = Zend_Registry::isRegistered('sitepagememberSearchType') ? Zend_Registry::get('sitepagememberSearchType') : null;
    if( empty($sitepagememberSearchType) ) {
      return $this->setNoRender();
    }

    $sitepage_post = Zend_Registry::isRegistered('sitepage_post') ? Zend_Registry::get('sitepage_post') : null;
    if (!empty($sitepage_post)) {
      $this->view->sitepage_post = $sitepage_post;
    }
    $front = Zend_Controller_Front::getInstance()->getRequest();
    $category = $front->getParam('category_id', null);
    $subcategory = $front->getParam('subcategory_id', null);
    $categoryname = $front->getParam('categoryname', null);
    $subcategoryname = $front->getParam('subcategoryname', null);
    $subsubcategory = $front->getParam('subsubcategory_id', null);
    $subsubcategoryname = $front->getParam('subsubcategoryname', null);
    $cattemp = $front->getParam('category', null);

    if(!empty($cattemp)) 
    {
    	$this->view->category_id  = $_GET['category'] = $front->getParam('category');
    	$row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->category_id);
	    if (!empty($row->category_name)) {
	      $categoryname = $this->view->category_name  = $_GET['categoryname'] = $row->category_name;
	    }
	    
	    $categorynametemp = $front->getParam('categoryname', null);
	    $subcategorynametemp = $front->getParam('subcategoryname', null);
	    if (!empty($categorynametemp)) {
		    $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
	    }
			if (!empty($subcategorynametemp)) {
		    $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
	    }
	  } else {
      if($categoryname)
	    $this->view->category_name = $_GET['categoryname'] =  $categoryname;      
	    if($category) {
	      $this->view->category_id = $_GET['category_id'] =  $category;
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name  = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }	    
    }
    
    $subcattemp = $front->getParam('subcategory', null);

    if(!empty($subcattemp)) 
    {
    	$this->view->subcategory_id = $_GET['subcategory_id'] = $front->getParam('subcategory');
	    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subcategory_name = $row->category_name;
	    }
    } else {
        if($subcategoryname)
				  $this->view->subcategory_name = $_GET['subcategoryname'] =  $subcategoryname;        
        if($subcategory) {
          $this->view->subcategory_id = $_GET['subcategory_id'] =  $subcategory;
          $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subcategory_name  = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
          }
       }		   
    }

    $subsubcattemp = $front->getParam('subsubcategory', null);

    if(!empty($subsubcattemp))
    {
    	$this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $front->getParam('subsubcategory');
	    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subsubcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subsubcategory_name = $row->category_name;
	    }
    } else {
        if($subsubcategoryname)
				  $this->view->subsubcategory_name = $_GET['subsubcategoryname'] =  $subsubcategoryname;

        if($subsubcategory) {
          $this->view->subsubcategory_id = $_GET['subsubcategory_id'] =  $subsubcategory;
          $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subsubcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subsubcategory_name  = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
          }
       }
    }

    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id =  0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = $categoryname;
			$_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }

    if (!empty($_GET)) {
      $form->populate($_GET);
    }
  }
}