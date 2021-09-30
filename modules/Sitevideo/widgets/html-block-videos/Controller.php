<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_HtmlBlockVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $islanguage = $this->view->translate()->getLocale();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        if (!strstr($islanguage, '_')) {
            $islanguage = $islanguage . '_default';
        }

        $keyForSettings = str_replace('_', '.', $islanguage);
        $sitevideoLendingBlockValue = $coreSettings->getSetting('sitevideo.lending.block.languages.' . $keyForSettings, null);

        $sitevideoLendingBlockTitleValue = $coreSettings->getSetting('sitevideo.lending.block.title.languages.' . $keyForSettings, null);
        if (empty($sitevideoLendingBlockValue)) {
            $sitevideoLendingBlockValue = $coreSettings->getSetting('sitevideo.lending.block', null);
        }

        if (empty($sitevideoLendingBlockTitleValue)) {
            $sitevideoLendingBlockTitleValue = $coreSettings->getSetting('sitevideo.lending.block.title', null);
        }

        $sitevideoBlockVideo = Zend_Registry::isRegistered('sitevideoBlockVideo') ? Zend_Registry::get('sitevideoBlockVideo') : null;
        if(empty($sitevideoBlockVideo))
            return $this->setNoRender();
        
        if (!empty($sitevideoLendingBlockValue))
            $this->view->sitevideoLendingBlockValue = @base64_decode($sitevideoLendingBlockValue);

        if (!empty($sitevideoLendingBlockTitleValue))
            $this->view->sitevideoLendingBlockTitleValue = @base64_decode($sitevideoLendingBlockTitleValue);
    }

}
