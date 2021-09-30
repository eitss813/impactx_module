<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditTab.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Form_Admin_EditTab extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');
    $this->setTitle('Edit Tab Settings')
            ->setDescription('');
    // Element: title
    $this->addElement('Text', 'title', array(
        'label' => 'Tab Title',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        )
    ));

    // Element: limit
    $this->addElement('Text', 'limit', array(
        'label' => 'No. of Items',
        'maxlength' => '3',
        'description' => 'How many items will be shown in this tab (value can not be empty or zero) ?',
        'required' => true,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // Element: enabled
    $this->addElement('Select', 'enabled', array(
        'label' => 'Enabled',
        'description' => 'Display this tab',
        'MultiOptions' => array('1' => 'Enable', '0' => 'Disable')
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
?>