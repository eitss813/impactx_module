<?php

/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
class Cbcore_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Cbcore_Plugin_Core());
    }

    protected function _bootstrap($resource = null) {


        $headScript = new Zend_View_Helper_HeadScript();
        $headScript->appendFile('application/modules/Cbcore/externals/scripts/cbJquery.js');
    }

}
