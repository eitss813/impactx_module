<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    public function __construct($application) {

        parent::__construct($application);
        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license.php';
    }

    protected function _initFrontController() {
        $this->initViewHelperPath();
        $this->initActionHelperPath();

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Sitevideo_Plugin_Core);

        $headScript = new Zend_View_Helper_HeadScript();
        $StaticBaseUrl = '';
        if (Zend_Registry::isRegistered('StaticBaseUrl')) {
            $StaticBaseUrl = Zend_Registry::get('StaticBaseUrl');
        }
        $headScript->appendFile($StaticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
                ->appendFile($StaticBaseUrl
                        . 'application/modules/Sitevideo/externals/scripts/core_video_lightbox.js')
                ->appendFile($StaticBaseUrl
                        . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');

        $headLink = new Zend_View_Helper_HeadLink();
        $headLink->appendStylesheet($StaticBaseUrl
                . 'application/modules/Seaocore/externals/styles/style_advanced_photolightbox.css');

      $manifest = Zend_Registry::get('Engine_Manifest');
      if ($manifest && isset($manifest['video']['items'])) {
        $manifest['video']['items'] = array_diff($manifest['video']['items'], array('video'));
        Zend_Registry::set('Engine_Manifest', $manifest);
      }
    }

}
