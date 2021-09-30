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

class Sitemember_Widget_ProfilePhotoSiteMembersController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $front = Zend_Controller_Front::getInstance();
    $this->view->controller = $front->getRequest()->getControllerName();
    $this->view->action = $front->getRequest()->getActionName();
    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
    if ($this->view->controller == 'index' && $this->view->action == 'home') {
      $this->view->object = $subject = Engine_Api::_()->user()->getViewer();
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $this->view->featured = $tableUserInfo->getColumnValue($viewer_id, 'featured');
      $this->view->sponsored = $tableUserInfo->getColumnValue($viewer_id, 'sponsored');
    } else {
      $this->view->object = $subject = Engine_Api::_()->core()->getSubject('user');
      $object_id = Engine_Api::_()->core()->getSubject('user')->getIdentity();
      $this->view->featured = $tableUserInfo->getColumnValue($object_id, 'featured');
      $this->view->sponsored = $tableUserInfo->getColumnValue($object_id, 'sponsored');
    }
    
    $sitemember_profile_photo = Zend_Registry::isRegistered('sitemember_profile_photo') ? Zend_Registry::get('sitemember_profile_photo') : null;
    if(empty($sitemember_profile_photo))
      $this->view->setNoRender();
$this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
    $this->view->statistics = $this->_getParam('statistics', array("featuredLabel", "sponsoredLabel"));
  }

}