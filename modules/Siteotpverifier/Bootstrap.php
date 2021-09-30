<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Bootstrap.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Siteotpverifier_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    public function __construct($application) {

        parent::__construct($application);
        $this->initViewHelperPath();
    }
    protected function _initFrontController() {

        $this->initViewHelperPath();
        $this->initActionHelperPath();
        //Initialize helper
        Zend_Controller_Action_HelperBroker::addHelper(new Siteotpverifier_Controller_Action_Helper_Standard());
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Siteotpverifier_Plugin_Core);
    }
}