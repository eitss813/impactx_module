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
class Sitealbum_Widget_CategoriesBannerSitealbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1))
      return $this->setNoRender();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $category_id = $request->getParam('subcategory_id', null);
    if (empty($category_id)) {
      $category_id = $request->getParam('category_id', null);
    }

    //SET NO RENDER
    if (empty($category_id))
      return $this->setNoRender();

    //GET CATEGORY ITEM
    $this->view->category = $category = Engine_Api::_()->getItem('album_category', $category_id);

    //SET NO RENDER
    if (empty($category->banner_id))
      return $this->setNoRender();

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();
  }

}