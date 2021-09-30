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
class Sitemember_Widget_NavigationSitememberController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->navigationTabTitle = $this->view->translate($this->_getParam('navigationTabTitle', 'Members'));

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitemember_review_main");

    $sitemember_isInfoTypeEnabled = Zend_Registry::isRegistered('sitemember_isInfoTypeEnabled') ? Zend_Registry::get('sitemember_isInfoTypeEnabled') : null;
    if (empty($sitemember_isInfoTypeEnabled))
      $this->view->setNoRender();
  }

}