<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Widget_VideoOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->videoOfDay = $videoOfDay = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->videoOfDay();
    if (empty($videoOfDay)) {
      return $this->setNoRender();
    }
  }

}
?>