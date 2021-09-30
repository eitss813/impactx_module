<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject('album') || !$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->album = $subject = Engine_Api::_()->core()->getSubject('album');
    
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('album_profile');
  }
}
