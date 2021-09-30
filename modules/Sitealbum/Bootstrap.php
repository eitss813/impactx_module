<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Sitealbum_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {

    parent::__construct($application);
    include APPLICATION_PATH . '/application/modules/Sitealbum/controllers/license/license.php';
  }

  protected function _initFrontController() {
    $this->initViewHelperPath();
    $this->initActionHelperPath();
    
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitealbum_Plugin_Core);
  }

}