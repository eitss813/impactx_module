<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: SesblogController.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_SesblogController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // only show to member_level if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('sesblog', $viewer, 'view')->isValid() ) {
      return;
    }

    // Get subject
    if( ($sesblog_id = $this->_getParam('blog_id',  $this->_getParam('id'))) &&
        ($sesblog = Engine_Api::_()->getItem('sesblog')) instanceof Sesblog_Model_Sesblog ) {
      Engine_Api::_()->core()->setSubject($sesblog);
    } else {
      $sesblog = null;
    }

    // Must have a subject
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }

    // Must be allowed to view this sesblog
    if( !$this->_helper->requireAuth()->setAuthParams($sesblog, $viewer, 'view')->isValid() ) {
      return;
    }
  }
}
