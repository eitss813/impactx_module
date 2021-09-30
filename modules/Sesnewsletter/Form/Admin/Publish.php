<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Delete.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_Publish extends Engine_Form {

  public function init() {

    $this->addElement('Radio', 'publish_type', array(
        'multiOptions' => array(
            1 => 'Publish',
            2 => 'Schedule'
        ),
        'value' => 1,
        'onchange' => "showDate(this.value)",
    ));

    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Date");
    $start->setAllowEmpty(false);
    $start->setRequired(true);
    $this->addElement($start);

    $this->addElement('Button', 'submit', array(
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}
