<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_VideoCategoriesSponsoredController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1))
            return $this->setNoRender();

        $itemCount = $this->_getParam('itemCount', 0);
        $this->view->showIcon = $this->_getParam('showIcon', 1);
        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');
        //GET SPONSORED CATEGORIES
        $this->view->categories = $categories = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'file_id', 'category_slug', 'cat_dependency', 'subcat_dependency'), 'sponsored' => 1, 'cat_depandancy' => 0, 'limit' => $itemCount));
        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
        //GET SPONSORED CATEGORIES COUNT
        $this->view->totalCategories = Count($categories);

        if ($this->view->totalCategories <= 0) {
            return $this->setNoRender();
        }
    }

}
