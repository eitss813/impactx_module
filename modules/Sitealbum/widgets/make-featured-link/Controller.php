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
class Sitealbum_Widget_MakeFeaturedLinkController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject() || !$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->album = $album = Engine_Api::_()->core()->getSubject('album');

    $viewer_id = $viewer->getIdentity();
    $this->view->allowView = $this->view->canMakeFeatured = false;
    if (!empty($viewer_id) && ($viewer->level_id == 1 || $viewer->level_id == 2)) {
      $this->view->canMakeFeatured = true;
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
    }
  }

}
