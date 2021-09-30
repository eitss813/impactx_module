<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_InformationSitememberController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    //GET SETTING
    $sitemember_isInfoTypeEnabled = Zend_Registry::isRegistered('sitemember_isInfoTypeEnabled') ? Zend_Registry::get('sitemember_isInfoTypeEnabled') : null;
    $this->view->showContent = $showContent = $this->_getParam('showContent', array("viewCount", "likeCount", "location"));

    if(empty($sitemember_isInfoTypeEnabled))
      $this->view->setNoRender();
    
    if (isset($showContent) && Count($showContent) <= 0) {
      $this->view->setNoRender();
    }

    $this->view->customParams = $this->_getParam('customParams', 5);
    $this->view->custom_field_title = $this->_getParam('custom_field_title', 1);
    $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 0);
    $this->view->user = Engine_Api::_()->core()->getSubject('user');
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
  }

}