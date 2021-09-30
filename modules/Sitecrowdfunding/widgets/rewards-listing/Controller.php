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
class Sitecrowdfunding_Widget_RewardsListingController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        $project_id = $project->project_id;
        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
        if (empty($project->is_gateway_configured)) {
            return $this->setNoRender();
        } elseif ($project->isExpired()) {
            return $this->setNoRender();
        } elseif ($project->status != 'active') {
            return $this->setNoRender();
        } elseif(strtotime($currentDate) < strtotime($projectStartDate)) {
            return $this->setNoRender();
        }
        $sitecrowdfundingRewardListings = Zend_Registry::isRegistered('sitecrowdfundingRewardListings') ? Zend_Registry::get('sitecrowdfundingRewardListings') : null;
        $this->view->showSlide = $this->_getParam('showSlide', 0);
        $this->view->slideHeight = $this->_getParam('slideHeight', 250);
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation', 150);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewardPaginator($project_id);
        $this->view->totalCount = $totalCount = $paginator->getTotalItemCount();
        if (empty($sitecrowdfundingRewardListings))
            return $this->setNoRender();
        // Do not render if nothing to show
        if (($totalCount <= 0)) {
            return $this->setNoRender();
        }
    }

}
