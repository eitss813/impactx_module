<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_BrowsenevigationSitepageController extends Engine_Content_Widget_Abstract {

  protected $_navigation;

  public function indexAction() {

    $front = Zend_Controller_Front::getInstance(); 
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controllerName = $front->getRequest()->getControllerName();
    if($controllerName == 'album' && $action == 'browse') {
      //GET NAVIGATION TABS 
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_album');
    }
    elseif($module == 'sitepagevideo' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_video');
    }
    elseif($module == 'sitepagedocument' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_document');
    }
    elseif($module == 'sitepageevent' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_event');
    }
    elseif($module == 'sitepagenote' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_note');
    }
    elseif($module == 'sitepagemusic' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_music');
    }
    elseif($module == 'sitepagemember' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_member');
    }
    elseif($module == 'sitepagereview' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_review');
    }
    elseif($module == 'sitepageoffer' && $action == 'browse') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_offer');
    }
    elseif($module == 'sitepageevent' && $action == 'by-locations') {
      //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main', array(), 'sitepage_main_event');
    }
    else {
    //GET NAVIGATION TABS 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');
    }
  }
}

?>