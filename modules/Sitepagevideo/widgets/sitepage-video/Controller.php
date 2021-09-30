<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Widget_SitepageVideoController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $values = array();
    $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);
    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $subcategory_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategory_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);

    if ($category)
      $_GET['category'] = $category;
    if ($subcategory)
      $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
      $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
      $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
      $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;

    //GET VALUE BY POST TO GET DESIRED SITEPAGES
    if (!empty($_GET)) {
      $values = $_GET;
    }

    if (($category) != null) {
      $this->view->category = $values['category'] = $category;
      $this->view->subcategory = $values['subcategory'] = $subcategory;
      $this->view->subsubcategory = $values['subsubcategory'] = $subsubcategory;
    } else {
      $values['category'] = 0;
      $values['subcategory'] = 0;
      $values['subsubcategory'] = 0;
    }

    if (($category_id) != null) {
      $this->view->category_id = $values['category'] = $category_id;
      $this->view->subcategory_id = $values['subcategory'] = $subcategory_id;
      $this->view->subsubcategory_id = $values['subsubcategory'] = $subsubcategory_id;
    } else {
      $values['category'] = 0;
      $values['subcategory'] = 0;
      $values['subsubcategory'] = 0;
    }

    $form = new Sitepagevideo_Form_Searchwidget(array('type' => 'sitepage_page'));
    $values['order'] = $form->getValues();
    $this->view->assign($values);


    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id =  0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = $categoryname;
			$_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }
        
    $this->view->itemCount = $totalVideos = $this->_getParam('itemCount', 20);
    $values['ratedvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('ratedvideo', null);
    $values['likedvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('likedvideo', null);
    $values['viewedvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('viewedvideo', null);
    $values['commentedvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('commentedvideo', null);
    $values['sponsoredvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('sponsoredvideo', null);
    $values['featuredvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('featuredvideo', null);
    $values['highlightedvideo'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('highlightedvideo', null);
    //GET VIDEOS DATA
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getVideos($values);

     $paginator->setItemCountPerPage($totalVideos);
     //$this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
     $this->view->paginator = $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page'));
     
    //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->formValues = $form->getValues();
    $this->view->page = $this->_getParam('page', 1);
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalCount) /$totalVideos);

    //END - SCROLLING WORK
  }

}
?>