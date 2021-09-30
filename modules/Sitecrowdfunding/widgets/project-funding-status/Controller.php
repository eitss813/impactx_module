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
class Sitecrowdfunding_Widget_ProjectFundingStatusController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->startDate = $startDate = date('Y-m-d', strtotime($project->funding_start_date));

        $this->view->projectExpiryDate = date('M d, Y', strtotime($project->funding_end_date));
        $this->view->days = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_end_date);
        $this->view->daysToStart = $daysToStart = Engine_Api::_()->sitecrowdfunding()->findDays($project->funding_start_date);
    }
}
