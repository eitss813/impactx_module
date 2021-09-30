<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    public function __construct($application) {

        parent::__construct($application);
        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license.php';
    }

    protected function _initFrontController() {
        $this->initViewHelperPath();
        $this->initActionHelperPath();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Sitecrowdfunding_Plugin_Core);
    }

}
