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
class Sitevideo_Widget_CategoriesBannerSitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1))
            return $this->setNoRender();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $category_id = $request->getParam('subsubcategory_id', null);
        if (empty($category_id)) {
            $category_id = $request->getParam('subcategory_id', null);
            if (empty($category_id)) {
                $category_id = $request->getParam('category_id', null);
            }
        }

        //SET NO RENDER
        if (empty($category_id))
            return $this->setNoRender();

        //GET CATEGORY ITEM
        $sitevideoCategoryBanner = Zend_Registry::isRegistered('sitevideoCategoryBanner') ? Zend_Registry::get('sitevideoCategoryBanner') : null;
        $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);

        //SET NO RENDER
        if (empty($category->banner_id))
            return $this->setNoRender();
        
        if(empty($sitevideoCategoryBanner))
            return $this->setNoRender();

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
    }

}
