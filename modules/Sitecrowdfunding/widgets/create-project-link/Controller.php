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
class Sitecrowdfunding_Widget_CreateProjectLinkController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
    	//DO NOT RENDER THE WIDGET FOR THE NON-LOGGED IN USER
    	$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        $this->view->create_button = $this->_getParam('create_button', 1);
        $this->view->create_button_title = $this->_getParam('create_button_title', 'Create a Project');
        
    }

}
