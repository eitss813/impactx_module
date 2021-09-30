<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_MobiController extends Core_Controller_Action_Standard {

  public function init() {

    //HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if (!$sitepagealbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();
    $page_id = $this->_getParam('page_id', $this->_getParam('id', null));

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);      
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum")) {
            return $this->_forward('requireauth', 'error', 'core');
          }
        } else {
          $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
          if (empty($isPageOwnerAllow)) {
            return $this->_forward('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $page_id = $album->page_id;
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