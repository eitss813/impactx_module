<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  protected function _initFrontController() {
    include APPLICATION_PATH . '/application/modules/Sitestaticpage/controllers/license/license.php';
  }
}