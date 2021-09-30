<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemOfDayday.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Admin_ItemOfDayday extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');
    $this->setTitle('Add a Item of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Item from the auto-suggest Item field. The selected Item will be displayed as "Item of the Day" for this duration and if more than one items are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');



    // init to
    $this->addElement('Hidden', 'resource_id', array(
        'order' => 985,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
    ));

    // Element: title
    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Item')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:300px;');

    $this->addElements(array(
        $label,
    ));

    // Element: start_date
    $starttime = new Engine_Form_Element_CalendarDateTime('start_date');
    $starttime->setLabel("Start Date");
    $starttime->setAllowEmpty(false);
    $starttime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($starttime);


    // Element: end_date
    $endtime = new Engine_Form_Element_CalendarDateTime('end_date');
    $endtime->setLabel("End Date");
    $endtime->setAllowEmpty(false);
    $endtime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($endtime);

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