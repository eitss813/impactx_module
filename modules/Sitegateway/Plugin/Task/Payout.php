<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UpdateProjectStatus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Plugin_Task_Payout extends Core_Plugin_Task_Abstract {

    public function execute() {
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        date_default_timezone_set($timezone);
        $currentDate = date('Y-m-d');
        $setting = Engine_Api::_()->getApi('settings', 'core');
        //Automatic payout code for Crowdfunding module
        $isCrowdfundingModule = Engine_Api::_()->hasModuleBootstrap('sitecrowdfunding');
        if ($isCrowdfundingModule) {
            Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding_failed_project_payout');
            $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            $projects = $projectTable->getPendingPayoutProjects();
            foreach($projects as $project){
                if($project->state=='successful' || ($project->state=='failed')){
                    
                }
            }
        }
    }

}
