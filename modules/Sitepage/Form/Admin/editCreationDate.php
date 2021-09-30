<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editCreationDate.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_editCreationDate extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');
    $this->setTitle('Edit Creation Date Of The Page')
            ->setDescription('Select a creation date below.');
            
    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Creation Date");
    $start->setAllowEmpty(false);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $this->addElement($end);

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Edit Creation Date',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        //'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitepage_general', true),
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