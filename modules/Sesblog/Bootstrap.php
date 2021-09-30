<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Bootstrap.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblog_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    /*for redirect old action for non login user start */
    if(Zend_Auth::getInstance()->getIdentity() && isset($_SESSION['redirect_url']) && !empty($_SESSION['redirect_url']))
    {
      $redirect_url=$_SESSION['redirect_url'];
      unset($_SESSION['redirect_url']);
      header("Location: ".$redirect_url);
    }
    /*for redirect old action for non login user end */
  }

  protected function _initRouter() {
  
    $router = Zend_Controller_Front::getInstance()->getRouter();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.pluginactivated')) {
      $integrateothermodulesTable = Engine_Api::_()->getDbTable('integrateothermodules', 'sesblog');
      $select = $integrateothermodulesTable->select();
      $results = $integrateothermodulesTable->fetchAll($select);
      if(count($results) > 0) {
        foreach ($results as $result) {
          $router->addRoute('sesblog_browssesblog_' . $result->getIdentity(), new Zend_Controller_Router_Route($result->content_url . '/browse-blogs', array('module' => 'sesblog', 'controller' => 'index', 'action' => 'browse-blogs', 'resource_type' => $result->content_type ,'integrateothermodule_id' => $result->integrateothermodule_id)));
        }
        return $router;
      }
    }
  }
  
  protected function _initFrontController() {
    $this->initActionHelperPath();
    include APPLICATION_PATH . '/application/modules/Sesblog/controllers/Checklicense.php';
  }
}
