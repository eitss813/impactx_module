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
class Sitealbum_Widget_LikeButtonController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$photoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.phototype', null);
		$likeButtonType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.albumtype', null);
    if (!Engine_Api::_()->core()->hasSubject() || empty($viewer_id) || empty($photoType) || empty($likeButtonType)) {
      return $this->setNoRender();
    }
  }

}