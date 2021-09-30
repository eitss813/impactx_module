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
class Sitemember_Widget_LocationSidebarSitememberController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1)) {
      return $this->setNoRender();
    }

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitemember = $sitemember = Engine_Api::_()->core()->getSubject('user');
    if (empty($sitemember->location)) {
      return $this->setNoRender();
    }

    $sitemember_isInfoTypeEnabled = Zend_Registry::isRegistered('sitemember_isInfoTypeEnabled') ? Zend_Registry::get('sitemember_isInfoTypeEnabled') : null;
    if (empty($sitemember_isInfoTypeEnabled))
      return $this->setNoRender();

    //GET LOCATION
    $this->view->location = $location = Engine_Api::_()->getItem('seaocore_locationitems', $sitemember->seao_locationid);

    if (empty($location)) {
      return $this->setNoRender();
    }
    $this->view->height = $this->_getParam('height', 200);
  }

}