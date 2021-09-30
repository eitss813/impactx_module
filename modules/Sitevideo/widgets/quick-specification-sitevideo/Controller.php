<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_QuickSpecificationSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1))
            return $this->setNoRender();

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        } else {
            $this->view->sitevideo = $sitevideo = Engine_Api::_()->core()->getSubject();
        }

        //LISITNG SHOULD BE MAPPED WITH PROFILE
        if (empty($this->view->sitevideo->profile_type)) {
            return $this->setNoRender();
        }

        $itemCount = $this->_getParam('itemCount', 5);
        //GET QUICK INFO DETAILS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitevideo/View/Helper', 'Sitevideo_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitevideo);
        $sitevideoSpecificationVideos = Zend_Registry::isRegistered('sitevideoSpecificationVideos') ? Zend_Registry::get('sitevideoSpecificationVideos') : null;

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSitevideo($sitevideo, $this->view->fieldStructure, $itemCount);
        }

        if (empty($sitevideoSpecificationVideos))
            return $this->setNoRender();

        if (empty($this->view->show_fields)) {
            return $this->setNoRender();
        }
    }

}
