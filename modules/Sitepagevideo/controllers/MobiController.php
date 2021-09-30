<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_MobiController extends Core_Controller_Action_Standard {

  public function init() {
  	
    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if ($this->_getParam('video_id') != null) {
        $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $this->_getParam('video_id'));
        $page_id = $sitepagevideo->page_id;
      }
    }
    
    //GET VIDEO ID
    $video_id = $this->_getParam('video_id');
    if ($video_id) {
      $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
      if ($sitepagevideo) {
        Engine_Api::_()->core()->setSubject($sitepagevideo);
      }
    }
  }

  //ACTION FOR VIEW THE VIDEO
  public function viewAction() {
    
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }
  
}

?>