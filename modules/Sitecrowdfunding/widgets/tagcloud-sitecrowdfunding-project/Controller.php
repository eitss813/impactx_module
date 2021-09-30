<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_TagcloudSitecrowdfundingProjectController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.tags', 1)) {
            return $this->setNoRender();
        }
        $params = array();
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();
        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
            $this->getElement()->removeDecorator('Container');
        } else {
            if (!$this->_getParam('loaded_by_ajax', 0)) {
                $this->view->is_ajax_load = true;
            } else {
                $this->getElement()->removeDecorator('Title');
            }
        }
        $this->view->action = $params['action'] = $action;
        //Param parameter to pass in getProjectTags function
        $itemCount = $this->_getParam('itemCount', 25);
        $params['orderingType'] = $this->_getParam('orderingType', '1');
        $params['showMoreTag'] = $this->view->showMoreTag = $this->_getParam('showMoreTag', '1');
        $sitecrowdfundingTagcloud = Zend_Registry::isRegistered('sitecrowdfundingTagcloud') ? Zend_Registry::get('sitecrowdfundingTagcloud') : null;
        //CONSTRUCTING TAG CLOUD
        $tag_array = array();
        $sitecrowdfunding_api = Engine_Api::_()->sitecrowdfunding();
        $totalCount = $this->view->totalCount = $sitecrowdfunding_api->getProjectTags(0, 1, $params);

        if ($this->view->totalCount <= 0) {
            return $this->setNoRender();
        }
        if (empty($sitecrowdfundingTagcloud))
            return $this->setNoRender();
        //If number of tag to be shown is greater than total tags than don't show Explore tag link  
        if ($itemCount >= $totalCount)
            $this->view->showLink = false;
        else
            $this->view->showLink = true;


        if (!$this->view->is_ajax_load)
            return;
        $this->view->allParams = $params;
        $element = $this->getElement();
        $title = $element->getTitle();

        if (empty($title))
            $title = 'Popular Project Tags';

        if ($this->view->owner_id == 0) {
            $element->setTitle(sprintf($this->view->translate('%s (%s)', array($title, (int)
                                $this->view->totalCount))));
        } else {
            $element->setTitle(sprintf($this->view->translate('%s (%s)', array($title, $this->view->owner->getTitle()))));
        }

        //FETCH TAGS
        $tag_cloud_array = $sitecrowdfunding_api->getProjectTags($itemCount, 0, $params);

        foreach ($tag_cloud_array as $values) {
            $tag_array[$values['text']] = $values['Frequency'];
            $tag_id_array[$values['text']] = $values['tag_id'];
        }

        if (!empty($tag_array)) {
            $max_font_size = 18;
            $min_font_size = 12;
            $max_frequency = max(array_values($tag_array));
            $min_frequency = min(array_values($tag_array));
            $spread = $max_frequency - $min_frequency;
            if ($spread == 0) {
                $spread = 1;
            }
            $step = ($max_font_size - $min_font_size) / ($spread);
            $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);
            $this->view->tag_data = $tag_data;
            $this->view->tag_id_array = $tag_id_array;
        }

        $this->view->tag_array = $tag_array;

        if (empty($this->view->tag_array)) {
            return $this->setNoRender();
        }
    }

}
