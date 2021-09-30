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
class Sitealbum_Form_ItemOfDayday extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');

    // init to
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

    // Element: resource_id
    $this->addElement('Hidden', 'resource_id', array(
        'order' => 9985,
        
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