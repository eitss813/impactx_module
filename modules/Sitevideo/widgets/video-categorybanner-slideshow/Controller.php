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
class Sitevideo_Widget_VideoCategorybannerSlideshowController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1))
            return $this->setNoRender();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = array();
        $category_id = $params['category_id'] = $this->_getParam('category_id', null);
        $this->view->showExporeButton = $this->_getParam('showExplore');
        $this->view->backgroupImage = $this->_getParam('logo');
        $this->view->backgroundImageHeight = $this->_getParam('height', 555);
        $this->view->categoryImageHeight = $this->_getParam('categoryHeight', 400);
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 100);
        $this->view->taglineTruncation = $this->_getParam('taglineTruncation', 200);
        $this->view->fullWidth = $this->_getParam('fullWidth', 1);
        //SET NO RENDER & GET CATEGORY ITEM
        if (empty($category_id))
            return $this->setNoRender();

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getCategoriesPaginator($params);
        $this->view->totalCount = $totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($totalCount);

        // Do not render if nothing to show
        if (($totalCount <= 0)) {
            return $this->setNoRender();
        }

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();
    }

}
