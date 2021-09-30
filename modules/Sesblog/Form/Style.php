<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Style.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Style extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Sesblog Styles')
      ->setMethod('post')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->removeDecorator('FormWrapper');

    // Element: style
    $this->addElement('Textarea', 'style', array(
      'label' => 'Custom Sesblog Styles',
      'description' => 'You can change the colors, fonts, and styles of your blog by adding CSS code below. The contents of the text area below will be output between <style> tags on your blog.'
    ));
//    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'href' => 'javascript:void(0);',
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      
    ));

    $this->addElement('Hidden', 'id');
  }
}
