<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_BestProjectsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        $this->view->gridViewWidth = $this->_getParam('columnWidth', 150);
        $this->view->gridViewHeight = $this->_getParam('columnHeight', 150);
        $this->view->titleTruncation = $this->_getParam('titleTruncation');
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation', 40);
        $this->view->showPagination = $params['showPagination'] = $this->_getParam('showPagination', 0);
        $this->view->itemCount = $params['itemCount'] = $this->_getParam('itemCount', 3);
        $this->view->categoryAtTop = $params['categoryAtTop'] = $this->_getParam('categoryAtTop', 1);
        $this->view->rowLimit = $params['rowLimit'] = $this->_getParam('itemCountPerPage', 7);
        $this->view->category_id = $params['category_id'] = $this->_getParam('category_id', 0);
        $this->view->category_ids = $params['category_ids'] = $this->_getParam('category_ids', array());
        $this->view->popularType = $params['popularType'] = $this->_getParam('popularType', 'start_date');
        $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        $sitecrowdfundingBestProject = Zend_Registry::isRegistered('sitecrowdfundingBestProject') ? Zend_Registry::get('sitecrowdfundingBestProject') : null;

        //REMOVE WIDGET TITLE FOR AJAX REQUEST
        if ($this->view->is_ajax) {
            $element = $this->getElement();
            $widgetTitle = $this->view->heading = $element->getTitle();
            if (!empty($widgetTitle)) {
                $element->setTitle("");
            }
        } else if ($this->view->categoryAtTop) {
            if (empty($this->view->category_ids))
                $this->setNoRender();
            $categoryLimit = 8;
            $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding');
            $this->view->categories = $categories = $tableCategory->getCategories(array(), $this->view->category_ids, 0, 0, 1, $categoryLimit, 'cat_order');
            $params['category_id'] = reset($this->view->category_ids);
        }
        if (empty($sitecrowdfundingBestProject))
            return $this->setNoRender();
        $this->view->projectOption = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
        if (empty($params['showPagination']))
            $this->view->rowLimit = $params['rowLimit'] = $this->view->itemCount;
        $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);

        $paginator->setItemCountPerPage($params["itemCount"]);
        $this->view->params = $params;
        $this->view->paginator = $paginator;
        $this->view->totalCount = $paginator->getTotalItemCount();
    }

}
