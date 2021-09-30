<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_HtmlBlockAlbumsPhotosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $islanguage = $this->view->translate()->getLocale();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        if (!strstr($islanguage, '_')) {
            $islanguage = $islanguage . '_default';
        }
        $tempSitemenuLtype=0;
        $keyForSettings = str_replace('_', '.', $islanguage);
        $sitealbumLendingBlockValue = $coreSettings->getSetting('sitealbum.lending.block.languages.' . $keyForSettings, null);

        $sitealbumLendingBlockTitleValue = $coreSettings->getSetting('sitealbum.lending.block.title.languages.' . $keyForSettings, null);
        if (empty($sitealbumLendingBlockValue)) {
            $sitealbumLendingBlockValue = $coreSettings->getSetting('sitealbum.lending.block', null);
        }
        if (empty($sitealbumLendingBlockTitleValue)) {
            $sitealbumLendingBlockTitleValue = $coreSettings->getSetting('sitealbum.lending.block.title', null);
        }
        if (!empty($sitealbumLendingBlockValue))
            $this->view->sitealbumLendingBlockValue = @base64_decode($sitealbumLendingBlockValue);
        if (!empty($sitealbumLendingBlockTitleValue))
            $this->view->sitealbumLendingBlockTitleValue = @base64_decode($sitealbumLendingBlockTitleValue);

        $this->view->showButton = $this->_getParam('showButton', 1);
        $this->view->firstButton = $this->_getParam('firstButton', 1);
        $this->view->firstButtonTitle = $this->_getParam('firstButtonTitle', 'Browse Albums');
        $this->view->firstButtonTitleLink = $this->_getParam('firstButtonTitleLink');

        if(!$this->view->firstButtonTitleLink) {
            $this->view->firstButtonTitleLink = 'albums/browse';
        }
        
        $this->view->secondButton = $this->_getParam('secondButton', 1);
        $this->view->secondButtonTitle = $this->_getParam('secondButtonTitle', 'Browse Photos');
        $this->view->secondButtonTitleLink = $this->_getParam('secondButtonTitleLink');
        
        if(!$this->view->secondButtonTitleLink) {
            $this->view->secondButtonTitleLink = 'albums/photo/browse';
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            $this->view->uploadButton = $this->_getParam('uploadButton', 1);
            $this->view->uploadButtonTitle = $this->_getParam('uploadButtonTitle', 'Add New Photos');  
        }

      
    }

}
