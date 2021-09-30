<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectPayments.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 

class Sitecrowdfunding_Plugin_Job_ProjectPayments extends Core_Plugin_Job_Abstract {

protected function _execute() {
        // Get job and params
        $job = $this->getJob();
        set_time_limit(0);
        // No project id?
        if (!($project_id = $this->getParam('project_id'))) {
            $this->_setState('failed', 'No project identity provided.');
            $this->_setWasIdle();
            return;
        }

        if (!($type = $this->getParam('payment_type'))) {
            $this->_setState('failed', 'Payment type not defined');
            $this->_setWasIdle();
            return;
        }  

        // Get project object
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (!$project) {
            $this->_setState('failed', 'Project is missing.');
            $this->_setWasIdle();
            return;
        }
         // Process
        try {
            if($type == 'payout'){
                $message = $project->payout();
            } elseif($type == 'refund'){
                $message = $project->refund();
            }
            $project->message = $message;
            $project->save();
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());
        }
    }
}