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
class Sitecrowdfunding_Widget_ProjectFundingChartController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $returndata = Engine_Api::_()->getDbtable('externalfundings','sitecrowdfunding')->getExternalFundingAmountChart($project->getIdentity());
        $this->view->fundingData = $returndata['fundingData'];
        $this->view->fundingAmount = $returndata['totalFundingAmount'];


        $fundingDatas2 = Engine_Api::_()->getDbTable('externalfundings','sitecrowdfunding')->getExternalFundingAmount($project->getIdentity());
        $this->view->totalFundingAmount = $fundingDatas2['totalFundingAmount'];
        $this->view->memberCount = $fundingDatas2['memberCount'];
        $this->view->orgCount = $fundingDatas2['orgCount'];
        $this->view->total_backer_count = $fundingDatas2['memberCount'] + $fundingDatas2['orgCount'];

        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
        $currentDate = date('Y-m-d');
        $flag = $project->isFundingApproved() && !$project->isExpired() && !(strtotime($currentDate) < strtotime($projectStartDate));
        if(!$flag){
            $this->setNoRender();
        }

    }
}
