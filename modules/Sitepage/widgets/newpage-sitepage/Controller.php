<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_NewpageSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->creationLink = $this->_getParam('creationLink', 1);
    
    if($this->view->creationLink) {
    //GET QUICK NAVIGATION
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_quick');
    }
    else {
      //WHO CAN CREATE STORE
      $menusClass = new Sitepage_Plugin_Menus();
      $this->view->canCreatePages = $menusClass->canCreateSitepages();
      $this->view->canCreate = 1;
      if(!$this->view->canCreatePages || (!Engine_Api::_()->sitepage()->hasPackageEnable())) {
        $this->view->canCreate = 0;
      }            
    }
    
  }
}

?>