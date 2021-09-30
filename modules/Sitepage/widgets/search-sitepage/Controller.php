<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_SearchSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

    //FORM CREATION
    $this->view->locationDetection = $locationDetection = $this->_getParam('locationDetection', 0);
    $this->view->viewType = $this->_getParam('viewType', 'vertical');

    $widgetSettings = array(
        'locationDetection' => $this->view->locationDetection
    );

    $this->view->form = $form = new Sitepage_Form_Search(array('type' => 'sitepage_page', 'widgetSettings' => $widgetSettings));
    
    //$this->view->form = $form = Zend_Registry::isRegistered('Sitepage_Form_Search') ? Zend_Registry::get('Sitepage_Form_Search') : new Sitepage_Form_Search(array('type' => 'sitepage_page', 'widgetSettings' => $widgetSettings)); 

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $categoryTable = Engine_Api::_()->getDbTable('categories', 'sitepage');
    $p = $request->getParams();
    if ((!isset($p['category']) || empty($p['category'])) && (!isset($p['category_id']) || empty($p['category_id']))) {
      $content_id = $this->view->identity;
      $widgetname = 'sitepage.pages-sitepage';
      $filtercategory_id = Engine_Api::_()->sitepage()->getSitepageCategoryid($content_id, $widgetname);
      if (empty($filtercategory_id))
        $filtercategory_id = Engine_Api::_()->sitepage()->getSitepageCategoryid($content_id, 'sitepage.pinboard-browse');
      $category = $filtercategory_id;
    } else {
      $category = $request->getParam('category_id', null);
    }

    $subcategory = $request->getParam('subcategory_id', null);
    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);
    $subsubcategory = $request->getParam('subsubcategory_id', null);
    $subsubcategoryname = $request->getParam('subsubcategoryname', null);
    $cattemp = $request->getParam('category', null);

    if (!empty($cattemp)) {
      $this->view->category_id = $_GET['category'] = $request->getParam('category');
      $row = $categoryTable->getCategory($this->view->category_id);
      if (!empty($row->category_name)) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $row->category_name;
      }

      $categorynametemp = $request->getParam('categoryname', null);
      $subcategorynametemp = $request->getParam('subcategoryname', null);
      if (!empty($categorynametemp)) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
      }
      if (!empty($subcategorynametemp)) {
        $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
      }
    } else {
      if ($categoryname)
        $this->view->category_name = $_GET['categoryname'] = $categoryname;
      if ($category) {
        $this->view->category_id = $_GET['category_id'] = $category;
        $row = $categoryTable->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }
    }

    $subcattemp = $request->getParam('subcategory', null);

    if (!empty($subcattemp)) {
      $this->view->subcategory_id = $_GET['subcategory_id'] = $request->getParam('subcategory');
      $row = $categoryTable->getCategory($this->view->subcategory_id);
      if (!empty($row->category_name)) {
        $this->view->subcategory_name = $row->category_name;
      }
    } else {
      if ($subcategoryname)
        $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname;
      if ($subcategory) {
        $this->view->subcategory_id = $_GET['subcategory_id'] = $subcategory;
        $row = $categoryTable->getCategory($this->view->subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
        }
      }
    }

    $subsubcattemp = $request->getParam('subsubcategory', null);

    if (!empty($subsubcattemp)) {
      $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $request->getParam('subsubcategory');
      $row = $categoryTable->getCategory($this->view->subsubcategory_id);
      if (!empty($row->category_name)) {
        $this->view->subsubcategory_name = $row->category_name;
      }
    } else {
      if ($subsubcategoryname)
        $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname;

      if ($subsubcategory) {
        $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $subsubcategory;
        $row = $categoryTable->getCategory($this->view->subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
        }
      }
    }

    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = $categoryname;
      $_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }


    if (!isset($_POST['orderby']) || empty($_POST)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.browseorder', 1);
      switch ($order) {
        case "1":
          $form->orderby->setValue("creation_date");
          break;
        case "2":
          $form->orderby->setValue("view_count");
          break;
        case "3":
          $form->orderby->setValue("title");
          break;
      }
    }
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
    if ($stusShow == 0) {
      $form->removeElement('closed');
    }

    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
      if (!empty($_POST['badge_id'])) {
        $_GET['badge_id'] = $_POST['badge_id'];
      }
    }

    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $tag = $_GET['tag'];
      $page = 1;
      if (isset($_GET['page']) && !empty($_GET['page'])) {
        $page = $_GET['page'];
      }
      // unset($_GET);
      $_GET['tag'] = $tag;
      $_GET['page'] = $page;
    }
    $rating = $request->getParam('orderby', null);
    $_GET['orderby'] = $rating;
    if (!empty($_GET))
      $form->populate($_GET);

    if (!$viewer) {
      $form->removeElement('show');
    }


    //  $form->tag->setValue("");
  }

}