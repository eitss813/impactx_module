<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Bootstrap.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesmultipleform_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
 protected function _initFrontController() {
    $this->initViewHelperPath();
    include APPLICATION_PATH . '/application/modules/Sesmultipleform/controllers/Checklicense.php';
  }
}