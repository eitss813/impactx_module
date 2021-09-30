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
class Sitepage_Widget_AddButtonSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // check extension installed or not
    $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
    if (!$featureExtension) {
      return $this->setNoRender();
    }
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $this->view->page_id = $page_id = $sitepage->page_id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $owner_id = $sitepage->owner_id;

    if( !Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'add_button')) {
      return $this->setNoRender();
    }

    if($viewer_id != $owner_id) {
      if (Engine_Api::_()->getItemTable('sitepage_button')->hasButton($page_id)) {
       $this->view->isValue = true;
       $this->view->button = Engine_Api::_()->getItemTable('sitepage_button')->getPageButton($page_id);
     } else {
       return $this->setNoRender();
     }
   } else {
    if(Engine_Api::_()->getItemTable('sitepage_button')->hasButton($page_id))
      $this->view->buttonValue = true;
    $this->view->isValue = true;
    $this->view->button = Engine_Api::_()->getItemTable('sitepage_button')->getPageButton($page_id);
  }
}

}
?>