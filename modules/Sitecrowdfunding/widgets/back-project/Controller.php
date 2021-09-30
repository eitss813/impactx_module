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
class Sitecrowdfunding_Widget_BackProjectController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {  
         
        $this->view->donationType = $this->_getParam('donationType', false);
        $defaultBackTitle = ($this->view->donationType) ? 'Donate Now' : 'Donate Now';
        $this->view->backTitle = $this->_getParam('backTitle', $defaultBackTitle);
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        
        $subjectType = null;
        if(Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $subjectId = $subject->getIdentity();
            $subjectType = $subject->getType();
        } 
        if($subjectType == 'siteevent_event') {
            $project_id = $coreSettings->getSetting("siteevent".$subjectId.".choosed.project", 0);
            $this->view->backTitle = $coreSettings->getSetting("siteevent".$subjectId.".back.project.label", 'Donate Now');
        } elseif($subjectType == 'sitepage_page') {
            $project_id = $coreSettings->getSetting("sitepage".$subjectId.".choosed.project", 0); 
            $this->view->backTitle = $coreSettings->getSetting("sitepage".$subjectId.".back.project.label", 'Donate Now');
        } elseif($subjectType == 'sitegroup_group') {
            $project_id = $coreSettings->getSetting("sitegroup".$subjectId.".choosed.project", 0); 
            $this->view->backTitle = $coreSettings->getSetting("sitegroup".$subjectId.".back.project.label", 'Donate Now');
        } elseif($subjectType == 'sitebusiness_business') {
            $project_id = $coreSettings->getSetting("sitebusiness".$subjectId.".choosed.project", 0);
            $this->view->backTitle = $coreSettings->getSetting("sitebusiness".$subjectId.".back.project.label", 'Donate Now');
        } elseif($subjectType == 'sitereview_listing') {
            $subject = Engine_Api::_()->core()->getSubject(); 
            $project_id = $coreSettings->getSetting("sitereview".$subjectId.".".$subject->listingtype_id.".choosed.project", 0); 
            $this->view->backTitle = $coreSettings->getSetting("sitereview".$subjectId.".".$subject->listingtype_id.".back.project.label", 'Donate Now'); 
        } else {
            $project_id = null;
        }
 
        if(Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
             //WHEN THE WIDGET IS PLACED ON THE PROJECT PROFILE PAGE
            $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        } elseif($this->_getParam('toValues', null)) {
            //WHEN PROJECT IS SELECTED FORM THE WIDGET SETTINGS
            $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->_getParam('toValues', null));
            $this->view->backTitle = $this->_getParam('backTitle', 'Donate Now'); 
            $this->view->donationType = true; 
        } elseif(!empty($project_id)) { 
            //WHEN PROJECT IS SELECTED FORM THE DASHBOARD OF CONTENT MODULE
            $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id); 
            $this->view->donationType = true; 
        } else { 
            //DONT RENDER IF SUBJECT IS NOT SET
            if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
                return $this->setNoRender();
            }  
        } 
        $sitecrowdfundingbackProject = Zend_Registry::isRegistered('sitecrowdfundingbackProject') ? Zend_Registry::get('sitecrowdfundingbackProject') : null;
        if (empty($sitecrowdfundingbackProject))
            return $this->setNoRender(); 
        
        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($project->funding_start_date));
        if (empty($project->is_gateway_configured)) {
            return $this->setNoRender();
        } elseif ($project->isExpired()) {
            return $this->setNoRender();
        } elseif ($project->status != 'active') {
            return $this->setNoRender();
        } elseif (strtotime($currentDate) < strtotime($projectStartDate)) {
            return $this->setNoRender();
        } 
    }

}
