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
class Sitecrowdfunding_Widget_ProjectCarouselController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $param = array();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $param['projectType'] = $contentType = $request->getParam('projectType', null);
        if (empty($contentType)) {
            $param['projectType'] = $param['projectType'] = $this->_getParam('projectType', 'All');
        }
        $this->view->projectType = $param['projectType'];
        $sitecrowdfundingProjectCarousel = Zend_Registry::isRegistered('sitecrowdfundingProjectCarousel') ? Zend_Registry::get('sitecrowdfundingProjectCarousel') : null;
        $this->view->showProject = $param['showProject'] = $this->_getParam('showProject');
        $this->view->category_id = $param['category_id'] = $this->_getParam('category_id');
        $this->view->subcategory_id = $param['subcategory_id'] = $this->_getParam('subcategory_id');
        $this->view->showPagination = $param['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->projectWidth = $param['projectWidth'] = $this->_getParam('projectWidth', 200);
        $this->view->projectHeight = $param['projectHeight'] = $this->_getParam('projectHeight', 200);
        $this->view->rowLimit = $param['rowLimit'] = $this->_getParam('rowLimit', 3);
        $this->view->daysFilter = $param['daysFilter'] = $this->_getParam('daysFilter', 20);
        $this->view->backedPercentFilter = $param['backedPercentFilter'] = $this->_getParam('backedPercentFilter', 50);
        $itemCount = $param['itemCount'] = $this->_getParam('itemCount', 12);
        $this->view->popularType = $param['popularType'] = $this->_getParam('popularType', 'start_date');
        $this->view->projectOption = $param['projectOption'] = $this->_getParam('projectOption');
        $this->view->selectProjects = $param['selectProjects'] = $this->_getParam('selectProjects', 'all');
        $this->view->interval = $param['interval'] = $this->_getParam('interval', 3500);
        $this->view->showLink = $this->_getParam('showLink', 1);
        $this->view->locationTruncation = $param['locationTruncation'] = $this->_getParam('locationTruncation', 250);
        $this->view->titleTruncation = $param['titleTruncation'] = $this->_getParam('titleTruncation', 22);
        $this->view->viewType = $param['viewType'] = $this->_getParam('viewType', 0);
        $this->view->detactLocation = $param['detactLocation'] = $this->_getParam('detactLocation', 0);
        if (empty($sitecrowdfundingProjectCarousel))
            return $this->setNoRender();
        if (!$this->view->detactLocation)
            $param['location'] = "";
        $this->view->latitude = $param['latitude'] = 0;
        $this->view->longitude = $param['longitude'] = 0;
        $this->view->defaultLocationDistance = $param['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);

        if ($this->view->detactLocation && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $this->view->latitude = $param['latitude'] = $cookieLocation['latitude'];
            $this->view->longitude = $param['longitude'] = $cookieLocation['longitude'];
        }

        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $param['projectOption'] = array();
        }

        switch ($param['popularType']) {
            case 'comment':
                $this->view->orderby = $param['orderby'] = 'comment_count';
                break;
            case 'like':
                $this->view->orderby = $param['orderby'] = 'like_count';
                break;
            case 'backerCount':
                $this->view->orderby = $param['orderby'] = 'backer_count';
                break;
            case 'random':
                $this->view->orderby = $param['orderby'] = 'random';
                break;
            case 'mostFunded':
                $this->view->orderby = $param['orderby'] = 'mostFunded';
                break;
            default :
                $this->view->orderby = $param['orderby'] = 'start_date';
        }
        $this->view->params = $param;
        $element = $this->getElement();
        $widgetTitle = $element->getTitle();
        if (empty($widgetTitle))
            $widgetTitle = "";
        $link = "";
        if (!empty($this->view->category_id)) {
            $this->view->category = $category = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategory($this->view->category_id);
            if ($this->view->category)
                $link = $this->view->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'browse', 'category_id' => $this->view->category_id), " + " . $this->view->translate('See all ') . $this->view->category->getTitle() . " Projects");
        }else {
            $link = $this->view->htmlLink(array('route' => 'sitecrowdfunding_project_general', 'action' => 'browse'), " + " . $this->view->translate('See all projects'));
        }
        if ($this->view->showLink == 1)
            $element->setTitle(sprintf($widgetTitle . " " . $link));
        $this->view->projects = $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($param);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator->setItemCountPerPage($itemCount);
        $this->view->totalCount = $count = $paginator->getTotalItemCount();
    }

}
