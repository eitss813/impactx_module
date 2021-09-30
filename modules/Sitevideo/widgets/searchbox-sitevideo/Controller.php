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
class Sitevideo_Widget_SearchboxSitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $widgetSettings = array();
        $widgetSettings['formElements'] = $this->_getParam('formElements', array("textElement", "categoryElement"));
        $widgetSettings['showAllCategories'] = $this->_getParam('showAllCategories', 0);
        $widgetSettings['textWidth'] = $this->_getParam('textWidth', 500);
        $widgetSettings['categoryWidth'] = $this->_getParam('categoryWidth', 150);
        $this->view->categoriesLevel = $widgetSettings['categoriesLevel'] = $this->_getParam('categoriesLevel', array("category"));
        $this->view->categoryElementExist = 0;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1) && !empty($widgetSettings['formElements']) && in_array("categoryElement", $widgetSettings['formElements']) && !empty($widgetSettings['categoriesLevel'])) {
            $this->view->categoryElementExist = 1;
        }

        if (empty($widgetSettings['formElements']) || (!in_array("textElement", $widgetSettings['formElements']) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1) && !in_array("categoryElement", $widgetSettings['formElements']))) {
            return $this->setNoRender();
        }

        $this->view->locationFieldEnabled = 1;
        if (empty($widgetSettings['formElements']) || (!in_array("locationElement", $widgetSettings['formElements']))) {
            $this->view->locationFieldEnabled = 0;
        }

        //PREPARE FORM
        $this->view->form = $form = new Sitevideo_Form_Searchbox(array('widgetSettings' => $widgetSettings));
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $form->populate($params);
    }

}
