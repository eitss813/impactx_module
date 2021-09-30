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
class Sitepagemember_Widget_SitepageMemberController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

		$this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
				
		//GET THE BASE URL.
		$this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
    $sitepagememberBaseDir = Zend_Registry::isRegistered('sitepagememberBaseDir') ? Zend_Registry::get('sitepagememberBaseDir') : null;
		$front = Zend_Controller_Front::getInstance()->getRequest();
    $values = array();
    $category = $front->getParam('category_id', null);
    $category_id = $front->getParam('category', null);
    $subcategory = $front->getParam('subcategory_id', null);
    $subcategory_id = $front->getParam('subcategory', null);
    $categoryname = $front->getParam('categoryname', null);
    $subcategoryname = $front->getParam('subcategoryname', null);
    $subsubcategory = $front->getParam('subsubcategory_id', null);
    $subsubcategory_id = $front->getParam('subsubcategory', null);
    $subsubcategoryname = $front->getParam('subsubcategoryname', null);

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

    $form = new Sitepagemember_Form_Searchwidget(array('type' => 'sitepage_page'));
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

    $totalMembers = $this->_getParam('itemCount', 20);
		$this->view->friend = $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		
    //GET VIEWER FRIENDS DATA
    $this->view->friendpaginator = $friendpaginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getMembersPaginator($values, 'friend');

		$friendpaginator->setItemCountPerPage($totalMembers);
		//$this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
	  $this->view->friendpaginator = $friendpaginator->setCurrentPageNumber($front->getParam('page'));
		$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepagemember/View/Helper', 'Sitepagemember_View_Helper');
    //GET MEMBERS DATA
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getMembersPaginator($values, 'otherMember');

		$paginator->setItemCountPerPage($totalMembers);
		//$this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
		$this->view->paginator = $paginator->setCurrentPageNumber($front->getParam('page'));
    
    if( empty($sitepagememberBaseDir) ) {
      return $this->setNoRender();
    }
  }
}