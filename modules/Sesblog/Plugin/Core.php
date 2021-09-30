<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Core.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Plugin_Core {

  public function onStatistics($event) {
    $table  = Engine_Api::_()->getDbTable('blogs', 'sesblog');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'blog');
  }
  
	public function onRenderLayoutDefault($event){

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$viewer = Engine_Api::_()->user()->getViewer();		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$moduleName = $request->getModuleName();
		$actionName = $request->getActionName();
		$controllerName = $request->getControllerName();	
		
		$checkWelcomeEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.welcome',1);
		if($actionName == 'welcome' && $controllerName == 'index' && $moduleName == 'sesblog') {
		  $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			if(!$checkWelcomeEnable){ 
				$redirector->gotoRoute(array('module' => 'sesblog', 'controller' => 'index', 'action' => 'home'), 'sesblog_general', false);
			}
			elseif($checkWelcomeEnable == '2') {
				$redirector->gotoRoute(array('module' => 'sesblog', 'controller' => 'index', 'action' => 'browse'), 'sesblog_general', false);
			} else if($checkWelcomeEnable == '1') {
			
        $checkWelcomePage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.check.welcome',2);

        $checkWelcomePage = (($checkWelcomePage == 1 && $viewer->getIdentity() == 0) ? true : ($checkWelcomePage == 0 && $viewer->getIdentity() != 0) ? true : ($checkWelcomePage == 2) ? true : false);
        if(!$checkWelcomePage && $actionName == 'welcome' && $controllerName == 'index' && $moduleName == 'sesblog'){
          $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
          $redirector->gotoRoute(array('module' => 'sesblog', 'controller' => 'index', 'action' => 'home'), 'sesblog_general', false);
        }
			}
		}
		                          
		$script = '';
		if($moduleName == 'sesblog'){
			$script .=
"sesJqueryObject(document).ready(function(){
     sesJqueryObject('.core_main_sesblog').parent().addClass('active');
    });
";
		}
		$script .= "var blogURLsesblog = '" . Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.blogs.manifest', 'blogs') . "';";
		 $view->headScript()->appendScript($script);
	}
	public function onRenderLayoutMobileDefault($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutMobileDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
	public function onRenderLayoutDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete sesblogs
      $sesblogTable = Engine_Api::_()->getDbtable('blogs', 'sesblog');
      $sesblogSelect = $sesblogTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $sesblogTable->fetchAll($sesblogSelect) as $sesblog ) {
        Engine_Api::_()->sesblog()->deleteBlog($sesblog);;
      }
      // Delete subscriptions
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'sesblog');
      $subscriptionsTable->delete(array(
        'user_id = ?' => $payload->getIdentity(),
      ));
      $subscriptionsTable->delete(array(
        'subscriber_user_id = ?' => $payload->getIdentity(),
      ));			
    }
  }
}
