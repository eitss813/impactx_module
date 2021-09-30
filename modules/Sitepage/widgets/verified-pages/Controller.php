<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class  Sitepage_Widget_VerifiedPagesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $verifyenabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.enabled', 0);

    if (empty($verifyenabled)) 
        return $this->setNoRender();

    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', true);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->title = $this->_getParam('title',false);
    $this->view->verify_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3);

    $values['limit'] = $itemCount = $this->_getParam('itemCount', 5);
    $values['resource_type'] = 'page';
    $showContent = true; 
    $loadFlage = false;
    
    if(!empty($is_ajax)) {
      $showContent = false;
      if (!empty($_GET['loadFlage'])) {
        $loadFlage = 1;
        $showContent = true;
      }
    }
    
    $this->view->loadFlage = $loadFlage;
    $this->view->showContent = $showContent;
    
    if(!empty($showContent)) {
      $verifyTable = Engine_Api::_()->getDbtable('verifies', 'sitepage');
      $this->view->paginator = $paginator = $verifyTable->getMostVerified($values); 
      
      if(!COUNT($paginator))
        return $this->setNoRender();
    }    
  }
}