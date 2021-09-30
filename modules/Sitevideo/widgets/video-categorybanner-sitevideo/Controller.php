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
class Sitevideo_Widget_VideoCategorybannerSitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1))
            return $this->setNoRender();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $category_id = 0;

        if (isset($params['category_id']))
            $category_id = $params['category_id'];

        $this->view->backgroupImage = $this->_getParam('logo');
        $this->view->backgroundImageHeight = $this->_getParam('height', 555);
        $this->view->categoryImageHeight = $this->_getParam('categoryHeight', 400);
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 100);
        $this->view->showExplore = $this->_getParam('showExplore', 1);
        $this->view->taglineTruncation = $this->_getParam('taglineTruncation', 200);
        $this->view->fullWidth = $this->_getParam('fullWidth', 1);
        //SET NO RENDER
        if (empty($category_id))
            return $this->setNoRender();

        //GET CATEGORY ITEM
        $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_video_category', $category_id);
        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
    }

}
