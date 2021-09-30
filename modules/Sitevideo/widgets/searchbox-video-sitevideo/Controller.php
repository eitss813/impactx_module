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
class Sitevideo_Widget_SearchboxVideoSitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $widgetSettings = array();
        $widgetSettings['formElements'] = $this->_getParam('formElements', array("textElement", "categoryElement", "locationElement", "locationmilesSearch"));
        $this->view->showAllCategories = $widgetSettings['showAllCategories'] = $this->_getParam('showAllCategories', 0);
        $widgetSettings['textWidth'] = $this->_getParam('textWidth', 275);
        $widgetSettings['locationWidth'] = $this->_getParam('locationWidth', 250);
        $widgetSettings['locationmilesWidth'] = $this->_getParam('locationmilesWidth', 125);
        $widgetSettings['categoryWidth'] = $this->_getParam('categoryWidth', 150);
        $this->view->categoriesLevel = $widgetSettings['categoriesLevel'] = $this->_getParam('categoriesLevel', array("category"));
        $this->view->locationDetection = $widgetSettings['locationDetection'] = $this->_getParam('locationDetection', 1);

        $this->view->categoryElementExist = 0;
        if (!empty($widgetSettings['formElements']) && in_array("categoryElement", $widgetSettings['formElements']) && !empty($widgetSettings['categoriesLevel'])) {
            $this->view->categoryElementExist = 1;
        }

        if (empty($widgetSettings['formElements']) || (!in_array("textElement", $widgetSettings['formElements']) && !in_array("categoryElement", $widgetSettings['formElements']))) {
            return $this->setNoRender();
        }

        $this->view->locationFieldEnabled = 1;
        if (empty($widgetSettings['formElements']) || (!in_array("locationElement", $widgetSettings['formElements']))) {
            $this->view->locationFieldEnabled = 0;
        }
        $this->view->params = $this->_getAllParams();
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
        //PREPARE FORM
        $this->view->form = $form = new Sitevideo_Form_SearchboxVideo(array('widgetSettings' => $widgetSettings));
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $form->populate($params);
        
    }

}
