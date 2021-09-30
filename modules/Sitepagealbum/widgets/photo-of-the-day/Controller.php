<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Widget_PhotoOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->photoOfDay = $photoOfDay = Engine_Api::_()->getDbtable('photos', 'sitepage')->photoOfDay();

    if (empty($photoOfDay)) {
      return $this->setNoRender();
    }
    $parent = $photoOfDay->getParent();
    $canView = $parent->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'view');
    if (empty($canView)) {
      return $this->setNoRender();
    }
    $this->view->showLightBox = Engine_Api::_()->sitepage()->canShowPhotoLightBox();
  }

}