<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {

    $this->initActionHelperPath();

    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitepage_Plugin_Core);
    
		Zend_Controller_Action_HelperBroker::addHelper(new Sitepage_Controller_Action_Helper_Pagefield());
  }

}
?>
