<?php

class Sitelogin_Controller_Action_Helper_Sociallogins  extends Zend_Controller_Action_Helper_Abstract
{
   function preDispatch() {

    $front = Zend_Controller_Front::getInstance();
    $request = $front->getRequest();
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName(); 
    $zend_view=new Zend_View_Helper_Action();   
    
    if ($module == 'user' && $action == 'login') {
        $view = $this->getActionController()->view;
        $view->addScriptPath(APPLICATION_PATH . '/application/modules/Sitelogin/views/scripts');
        
    }
  }
 
}