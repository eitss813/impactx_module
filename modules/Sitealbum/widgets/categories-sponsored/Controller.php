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
class Sitealbum_Widget_CategoriesSponsoredController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1))
      return $this->setNoRender();

    $itemCount = $this->_getParam('itemCount', 0);
    $this->view->showIcon = $this->_getParam('showIcon', 1);
		$this->view->columnPerRow = $this->_getParam('columnPerRow', 5);

    //GET CATEGORY TABLE
    $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitealbum');

    //GET SPONSORED CATEGORIES
    $this->view->categories = $categories = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'file_id', 'category_slug', 'cat_dependency'), 'sponsored' => 1, 'cat_depandancy' => 0, 'limit' => $itemCount));
    $sitealbum_category_sponsored = Zend_Registry::isRegistered('sitealbum_category_sponsored') ? Zend_Registry::get('sitealbum_category_sponsored') : null;

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();

    //GET SPONSORED CATEGORIES COUNT
    $this->view->totalCategories = Count($categories);

    if (empty($sitealbum_category_sponsored) || ($this->view->totalCategories <= 0)) {
      return $this->setNoRender();
    }
  }

}