<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_AlbumlocationSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    return $this->setNoRender();
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();

    if ($module != 'sitealbum' || $controller != 'index' || $action != 'map') {
      return $this->setNoRender();
    }

    $sitealbum_albumlocation_search = Zend_Registry::isRegistered('sitealbum_albumlocation_search') ? Zend_Registry::get('sitealbum_albumlocation_search') : null;
    $valueArray = array('street' => $this->_getParam('street', 1), 'city' => $this->_getParam('city', 1), 'country' => $this->_getParam('country', 1), 'state' => $this->_getParam('state', 1));
    $list_street = serialize($valueArray);    
    if(empty($sitealbum_albumlocation_search))
      return $this->setNoRender();

    // Make form
    $this->view->form = $form = new Sitealbum_Form_AlbumLocationsearch(array('value' => $list_street, 'type' => 'album'));

    if (!empty($_POST)) {
      $this->view->category_id = $_POST['category_id'];
      $this->view->subcategory_id = $_POST['subcategory_id'];
      $this->view->category_name = $_POST['categoryname'];
      $this->view->subcategory_name = $_POST['subcategoryname'];
    }

    if (!empty($_POST)) {
      $this->view->advanced_search = $_POST['advanced_search'];
    }

    $categories = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'sponsored' => 0, 'cat_depandancy' => 1));
    $categories_slug[0] = "";
    if (count($categories) != 0) {
      foreach ($categories as $category) {
        $categories_slug[$category->category_id] = $category->getCategorySlug();
      }
    }
    
    $this->view->categories_slug = $categories_slug;
  }

}