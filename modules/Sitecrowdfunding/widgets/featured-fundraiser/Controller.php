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
class Sitecrowdfunding_Widget_FeaturedFundraiserController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params['selectProjects'] = $this->_getParam('selectProjects', 'all');
        $params['showProject'] = $this->_getParam('showProject', 'featuredSponsored');
        $this->view->projectOption = $this->_getParam('projectOption');
        $this->view->projectOption = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array();
        }
        if ($params['showProject'] == 'special') {
            $params['project_ids'] = $this->_getParam('toValues', array());
            if (empty($params['project_ids']))
                return $this->setNoRender();
            $params['project_ids'] = explode(',', $params['project_ids']);
            $params['project_ids'] = array_unique($params['project_ids']);
            if (count($params['project_ids']) <= 0)
                return $this->setNoRender();
        }
        $sitecrowdfundingFeaturedFundraiser = Zend_Registry::isRegistered('sitecrowdfundingFeaturedFundraiser') ? Zend_Registry::get('sitecrowdfundingFeaturedFundraiser') : null;
        $params['orderby'] = $this->_getParam('orderby', 'startDate');
        $this->view->itemCount = $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 5);
        $this->view->projectHeight = $projectHeight = $this->_getParam('projectHeight', 300);
        $this->view->projectWidth = $projectWidth = $this->_getParam('projectWidth', 200);
        $this->view->viewProjectButton = $viewProjectButton = $this->_getParam('viewProjectButton', 1);
        $this->view->viewProjectTitle = $viewProjectTitle = $this->_getParam('viewProjectTitle', 'View Project');
        $this->view->titleTruncation = $titleTruncation = $this->_getParam('titleTruncation', 10);
        $this->view->descriptionTruncation = $descriptionTruncation = $this->_getParam('descriptionTruncation', 100);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getProjectPaginator($params);
        $this->view->totalCount = $totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($itemCount);
        if (empty($sitecrowdfundingFeaturedFundraiser))
            return $this->setNoRender();
        if ($totalCount <= 0) {
            return $this->setNoRender();
        }
    }

}
