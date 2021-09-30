<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_SpecificationSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        } else {
            $this->view->sitevideo = $sitevideo = Engine_Api::_()->core()->getSubject();
        }

        //GET QUICK INFO DETAILS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitevideo);
        $sitevideoSpecificationVideos = Zend_Registry::isRegistered('sitevideoSpecificationVideos') ? Zend_Registry::get('sitevideoSpecificationVideos') : null;

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->show_fields = $this->view->fieldValueLoop($sitevideo, $this->view->fieldStructure);
        }

        if (empty($sitevideoSpecificationVideos))
            return $this->setNoRender();

        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }
        $this->view->showContent = true;
    }

}
