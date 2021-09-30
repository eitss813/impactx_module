<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Controller.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Widget_LoginOrSignupPopupController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->pageIdentity = join('-', array(
            $request->getModuleName(),
            $request->getControllerName(),
            $request->getActionName()
        ));
        Zend_Registry::set('siteloginSignupPopUp', 1);
        $notRenderPages = array('user-signup-index', 'user-auth-login'); 
        if( Engine_Api::_()->user()->getViewer()->getIdentity() || in_array($this->view->pageIdentity, $notRenderPages) ) {
            $this->setNoRender();
            Zend_Registry::set('siteloginSignupPopUp', 0);
            return;
        }
        
    }
    
    public function getCacheKey()
    {
        return false;
    }

}
