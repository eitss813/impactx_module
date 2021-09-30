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
class Sitecrowdfunding_Widget_ProjectInformationController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        if($request->getParam('donationType', false)) {
            return $this->setNoRender();
        }
        $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data'); 
        if($session->donationType) {
            return $this->setNoRender();
        }

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->titleTruncation = $this->_getParam('titleTruncation',150);
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation',150);
        $this->view->owner = $owner = Engine_Api::_()->user()->getUser($project->owner_id);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->category = Engine_Api::_()->getItem('sitecrowdfunding_category', $project->category_id);

     }

}
