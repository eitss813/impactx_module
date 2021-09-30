<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Tagcloudblog.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
class Sesblog_Form_Admin_Tagcloudblog extends Engine_Form {

  public function init() {
		
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->addElement('Text', "color", array(
      'label' => sprintf('%s to choose the color of tag text.',  sprintf('%s', '<a href="' . $view->baseUrl() . "/admin/sesbasic/settings/color-chooser" . '" target="_blank">Click Here</a>')),
      'value' => '#00f',
    ));
    
    $this->getElement('color')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    
    $this->addElement('Radio', "type", array(
      'label' => "Choose View Type.",
			'multiOptions' => array(
				'tab' => 'Tab View',
				'cloud' => 'Cloud View',
			),
			'value' => 'tab',
    ));
    
    $this->addElement('Text', "text_height", array(
      'label' => "Choose height of tag text in cloud view.",
      'value' => '15',
      'validators' => array(
	array('Int', true),
	array('GreaterThan', true, array(0)),
      )
    ));
		
    $this->addElement('Text', "height", array(
      'label' => "Choose height of tag container in cloud view (in pixels).",
      'value' => '300',
      'validators' => array(
	array('Int', true),
	array('GreaterThan', true, array(0)),
      )
    ));
	
    $this->addElement('Text', "itemCountPerPage", array(
      'label' => "Count (number of tags to show).",
      'value' => '50',
      'validators' => array(
	array('Int', true),
	array('GreaterThan', true, array(0)),
      )
    ));
  }

}
