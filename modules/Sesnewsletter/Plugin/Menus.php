<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Menus.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Plugin_Menus {

  public function canEnabled() {

    // Check subject
    if(!Engine_Api::_()->core()->hasSubject('user'))
      return false;

    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!$viewer || !$viewer->getIdentity())
      return false;

    $allowsubs = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sesnewsletter', 'allowsubs');
    if(empty($allowsubs))
      return false;

    $isExist = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->isExist($viewer->email);
    if (empty($isExist))
      return false;

    return true;
  }

  // core_main
  public function onMenuInitialize_SesnewsletterHeaderHome($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $route = array(
      'route' => 'default',
    );

    if( $viewer->getIdentity() ) {
      $route['route'] = 'user_general';
      $route['params'] = array(
        'action' => 'home',
      );
      if( 'user' == $request->getModuleName() &&
        'index' == $request->getControllerName() &&
        'home' == $request->getActionName() ) {
        $route['active'] = true;
      }
    }
    if( !empty($row->params['icon']) ) {
      return array_merge($row->params, $route);
    }
    return $route;
  }
}
