<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_MobiController extends Core_Controller_Action_Standard {

  public function indexAction() {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
      return;
    $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
    if (empty($getLightBox)) {
      return;
    }

    $this->_helper->content
            ->setNoRender()
            ->setEnabled()
    ;
  }

}