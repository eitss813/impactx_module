<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberOfDay.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Form_Admin_MemberOfDay extends Engine_Form {

  protected $_field;

  public function init() {
  
    $this->setMethod('post');
    $this->setTitle('Add a Member of the Day')
        ->setDescription('Select a start date and end date below and the corresponding Member from the auto-suggest Member field. The selected Member will be displayed as "Member of the Day" for this duration and if more than one members are found to be displayed in the same duration then they will be displayed randomly one at a time.');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Member')
					->addValidator('NotEmpty')
					->setRequired(true)
					->setAttrib('class', 'text')
					->setAttrib('style', 'width:300px;');

    // init to
    $this->addElement('Hidden', 'member_id', array( 'order' => 600,));

    $this->addElements(array(
        $label,
    ));

    $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
    $starttime->setLabel("Start Date");
    $starttime->setAllowEmpty(false);
    $starttime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($starttime);

    //Start End date work
    $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
    $endtime->setLabel("End Date");
    $endtime->setAllowEmpty(false);
    $endtime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($endtime);
    //End End date work
    
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Add Member',
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