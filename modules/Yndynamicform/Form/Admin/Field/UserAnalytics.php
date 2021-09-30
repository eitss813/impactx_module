<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Field.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Yndynamicform_Form_Admin_Field_UserAnalytics extends Engine_Form
{
  protected $_option;

  public function getOption()
  {
    return $this -> _option;
  }

  public function setOption($option)
  {
    $this -> _option = $option;
  }

  public function init()
  {
    $this->setMethod('POST')
        ->setAttrib('class', 'global_form_popup')
        ->setTitle('Edit Form Field');

    // Add type
    $types = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');
    $availableTypes = array();
    foreach( $types as $fieldType => $info ) {
      $availableTypes[$fieldType] = $info['label'];
    }

    $this->addElement('Select', 'type', array(
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => $availableTypes,
      'style' => 'display:none',
      'onchange' => 'var form = this.getParent("form"); form.method = "get"; form.submit();',
    ));

    $this->type->removeDecorator('label');

    // Add label
    $this->addElement('hidden', 'label', array(
        'label' => 'User Analytics',
    ));

    // Display
    $this->addElement('hidden', 'display', array(
        'value' => 1,
        'order' => 100,
    ));

    $this->addElement('hidden', 'show_registered', array(
        'value' => '1',
        'order' => 102,
    ));

    $this->addElement('hidden', 'show_guest', array(
        'value' => '1',
        'order' => 103,
    ));

    // Add submit
    $this->addElement('Button', 'execute', array(
        'label' => 'Save',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10000,
        'ignore' => true,
    ));

    // Add cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10001,
        'ignore' => true,
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
        'order' => 10002,
    ));
  }
}