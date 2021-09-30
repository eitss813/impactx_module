<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Filter.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_Review_Filter extends Engine_Form {

  public function init() {

    $this->clearDecorators()
        ->addDecorator('FormElements')
        ->addDecorator('Form')
        ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
        ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    $this->setAttribs(array('id' => 'filter_form', 'class' => 'global_form_box'))->setMethod('GET');

    $this->addElement('Text', 'title', array(
        'label' => 'Review Title',
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => null, 'placement' => 'PREPEND')),
            array('HtmlTag', array('tag' => 'div'))
        ),
    ));

    $this->addElement('Text', 'owner_name', array(
        'label' => 'Owner Name',
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => null, 'placement' => 'PREPEND')),
            array('HtmlTag', array('tag' => 'div'))
        ),
    ));

    $this->addElement('Text', 'creation_date', array(
      'label' => 'Creation Date Ex(2015-03-02)',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div'))
      ),
    ));
    
		$this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'decorators' => array(
      'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div'))
      ),
      'multiOptions' => array(
        "" => 'Select',
          1 => 'Yes',
          0 => 'No'
      ),
      'value' => 0,
		));
		
		$this->addElement('Select', 'verified', array(
      'label' => 'Verified',
      'decorators' => array(
      'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div'))
      ),
      'multiOptions' => array(
        "" => 'Select',
          1 => 'Yes',
          0 => 'No'
      ),
      'value' => 0,
		));
		$this->addElement('Select', 'offtheday', array(
      'label' => 'Off The Day',
      'decorators' => array(
      'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div'))
      ),
      'multiOptions' => array(
          "" => 'Select',
          1 => 'Yes',
          0 => 'No'
      ),
      'value' => 0,
		));
    $this->addElement('Button', 'search', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div'))
        ),
    ));

    $this->addElement('Hidden', 'order', array(
        'order' => 10004,
    ));
    $this->addElement('Hidden', 'order_direction', array(
        'order' => 10002,
    ));

    $this->addElement('Hidden', 'review_id', array(
        'order' => 10003,
    ));

    //Set default action without URL-specified params
    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}
