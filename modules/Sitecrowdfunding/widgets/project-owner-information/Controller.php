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
class Sitecrowdfunding_Widget_ProjectOwnerInformationController extends Engine_Content_Widget_Abstract {

    public function indexAction() {    

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $subject = Engine_Api::_()->core()->getSubject(); 
        $this->view->$owner_id = $owner_id = $subject->owner_id;
        $this->view->project_id = $subject->project_id;
        $this->view->projectOwner = $projectOwner = Engine_Api::_()->getItem('user',$owner_id);

        $this->view->contactMeTitle=$this->_getParam('contactMeTitle','Contact');
        $this->view->seefullBioTitle=$this->_getParam('seefullBioTitle','See Full Bio'); 
        if (empty($this->view->ownerOptions) || !is_array($this->view->ownerOptions)) {
            $this->view->ownerOptions = $params['ownerOptions'] = array();
        }
        //DO NOT SHOW CONTACT ME BUTTON TO THE PROJECT OWNER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->showContactButton = true;
        if($owner_id == $viewer_id) {
            $this->view->showContactButton = false;
        } 
        $this->view->ownerName = $projectOwner->username;
        $this->view->location = $projectOwner->timezone; 
    }    

}