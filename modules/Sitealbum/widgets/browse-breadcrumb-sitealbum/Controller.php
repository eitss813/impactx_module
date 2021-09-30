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
class Sitealbum_Widget_BrowseBreadcrumbSitealbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->formValues = $values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    //GET ALBUM CATEGORY TABLE
    $this->view->categoryTable = $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->category_id = $this->view->subcategory_id = 0;
    $this->view->category_name = $this->view->subcategory_name = '';

    $category_id = $request->getParam('category_id', null);

    if (!empty($category_id)) {
      $this->view->category_id = $category_id;
      $this->view->category_name = $tableCategory->getCategoryName($category_id);

      $subcategory_id = $request->getParam('subcategory_id', null);

      if (!empty($subcategory_id)) {
        $this->view->subcategory_id = $subcategory_id;
        $this->view->subcategory_name = $tableCategory->getCategoryName($subcategory_id);
      }
    }

    if (((isset($values['tag']) && !empty($values['tag']) && isset($values['tag_id']) && !empty($values['tag_id'])))) {
      $current_url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $current_url = explode("?", $current_url);
      if (isset($current_url[1])) {
        $current_url1 = explode("&", $current_url[1]);
        foreach ($current_url1 as $key => $value) {
          if (strstr($value, "tag=") || strstr($value, "tag_id=")) {
            unset($current_url1[$key]);
          }
        }
        $this->view->current_url2 = implode("&", $current_url1);
      }
    }
  }

}