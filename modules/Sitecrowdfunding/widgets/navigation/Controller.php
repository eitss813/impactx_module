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
class Sitecrowdfunding_Widget_NavigationController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

    	$front = Zend_Controller_Front::getInstance(); 
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();

        //DO NOT RENDER THIS WIDGET IS THE PAGE IS REWARD SELECTION OR CHECKOUT PAGE
        if ($controller == 'backer' && $action == 'reward-selection' || $controller == 'backer' && $action == 'checkout') {
        	return $this->setNoRender();
        } 
        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_main');
        
    }

}
