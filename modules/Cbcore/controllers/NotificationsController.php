<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */

class Cbcore_NotificationsController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $this->_helper->requireUser();
  }

  public function updateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      $this->view->notificationCount = $notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);

    $this->view->text = $this->view->translate($notificationCount);
  }

}