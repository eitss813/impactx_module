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
class Sitecrowdfunding_Widget_ListPopularProjectsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $param = array();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $param['projectType'] = $contentType = $request->getParam('projectType', null);
        if (empty($contentType)) {
            $param['projectType'] = $this->_getParam('projectType', 'All');
        }
        $this->view->projectType = $param['projectType'];

        $param['category_id'] = $this->_getParam('category_id');
        $param['subcategory_id'] = $this->_getParam('subcategory_id');

        $this->view->projectHeight = $param['projectHeight'] = $this->_getParam('projectHeight', 200);
        $this->view->projectWidth = $param['projectWidth'] = $this->_getParam('projectWidth', 200);

        $this->view->selectProjects = $param['selectProjects'] = $this->_getParam('selectProjects', 'all');
        $this->view->showProject = $param['showProject'] = $this->_getParam('showProject', 'featuredSponsored');
        $this->view->populartype = $param['popularType'] = $this->_getParam('popularType', 'creation');
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->projectInfo = $this->_getParam('projectInfo');
        if (empty($this->view->projectInfo) || !is_array($this->view->projectInfo)) {
            $this->view->projectInfo = array();
        }

        $param['interval'] = $interval = $this->_getParam('interval', 'overall');
        $sitecrowdfundingListPopularProjects = Zend_Registry::isRegistered('sitecrowdfundingListPopularProjects') ? Zend_Registry::get('sitecrowdfundingListPopularProjects') : null;

        switch ($param['popularType']) {
            case 'creation':
                $param['orderby'] = 'start_date';
                break;
            case 'backed':
                $param['orderby'] = 'backer_count';
                break;
            case 'like':
                $param['orderby'] = 'like_count';
                break;
            case 'comment':
                $param['orderby'] = 'comment_count';
                break;
        }

        $this->view->params = $param;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($param);

        // Do not render if nothing to show
        if (($paginator->getTotalItemCount() <= 0)) {
            return $this->setNoRender();
        }
        if (empty($sitecrowdfundingListPopularProjects))
            return $this->setNoRender();
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

}
