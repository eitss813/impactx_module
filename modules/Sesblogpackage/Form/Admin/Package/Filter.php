<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Filter.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_Form_Admin_Package_Filter extends Engine_Form {

  public function init() {
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
    ;

    // Element: query
    $this->addElement('Text', 'query', array(
        'label' => 'Package Title',
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => null, 'placement' => 'PREPEND')),
            array('HtmlTag', array('tag' => 'div')),
        ),
    ));

    // Element: enabled
    $this->addElement('Select', 'enabled', array(
        'label' => 'Enabled',
        'multiOptions' => array(
            '' => '',
            '1' => 'Yes',
            '0' => 'No',
        ),
        'decorators' => array(
            'ViewHelper',
            array('Label', array('tag' => null, 'placement' => 'PREPEND')),
            array('HtmlTag', array('tag' => 'div')),
        ),
    ));


    // Element: order
    $this->addElement('Hidden', 'order', array(
        'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
        'order' => 10005,
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Search',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
            array('HtmlTag2', array('tag' => 'div')),
        ),
    ));
  }

}
