<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {
    $this->initActionHelperPath();
    // INCLUDE FILE.
    include_once APPLICATION_PATH . '/application/modules/Sitepagevideo/controllers/license/license.php';
  }

}
?>