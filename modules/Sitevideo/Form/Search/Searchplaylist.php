<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchplaylist.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Search_Searchplaylist extends Engine_Form {

    protected $_widgetSettings;

    public function getWidgetSettings() {
        return $this->_widgetSettings;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function init() {

        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'sitvideos_browse_filters field_search_criteria',
            'method' => 'GET'
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators();
        $params = array();
        //$params = $request->_getParams('formElements');
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        if (in_array('playlistelement', $this->_widgetSettings['formElements'])) {
            $playlistWidth = $this->_widgetSettings['playlistWidth'];
            $this->addElement('Text', 'search', array(
                'label' => 'Playlist Title',
                'order' => 1,
                'autocomplete' => 'off',
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                'style' => "width:$playlistWidth" . "px;max-width:inherit;",
            ));
        }
        if (in_array('videoelement', $this->_widgetSettings['formElements'])) {
            $videoWidth = $this->_widgetSettings['videoWidth'];
            $this->addElement('Text', 'video_title', array(
                'label' => 'Video Title',
                'order' => 2,
                'autocomplete' => 'off',
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                'style' => "width:$videoWidth" . "px;max-width:inherit;",
            ));
        }

        if (isset($_GET['video_title'])) {
            $this->search->setValue($_GET['video_title']);
        }
        if (in_array('membername', $this->_widgetSettings['formElements'])) {
            $memberNameWidth = $this->_widgetSettings['memberNameWidth'];
            $this->addElement('Text', 'membername', array(
                'label' => 'Member\'s name',
                'order' => 3,
                'autocomplete' => 'off',
                'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
                'style' => "width:$memberNameWidth" . "px;max-width:inherit;",
            ));
        }
        $this->addElement('Hidden', 'viewFormat', array(
            'order' => 1000011,
        ));
        $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'onclick' => 'searchSitevideos();',
            'ignore' => true,
            'order' => 999999999
        ));
    }

}
