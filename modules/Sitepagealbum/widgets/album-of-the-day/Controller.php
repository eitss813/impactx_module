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
class Sitepagealbum_Widget_AlbumOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->albumOfDay = $albumOfDay = Engine_Api::_()->getDbtable('albums', 'sitepage')->albumOfDay();
    if (empty($albumOfDay)) {
      return $this->setNoRender();
    }
  }

}
?>