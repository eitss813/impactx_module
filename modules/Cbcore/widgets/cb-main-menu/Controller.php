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
class Cbcore_Widget_CbMainMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  // Get numcount
    $itemcount = $this->_getParam('itemcount');
    $this->view->count = $itemcount;
    $this->view->search = $this->_getParam('search');
    $this->view->show_logo = $this->_getParam('show_logo');
    $this->view->logo = $this->_getParam('logo');	
    $this->view->navigation = $navigation = Engine_Api::_()
        ->getApi('menus', 'core')->getNavigation('core_main');

  }
}