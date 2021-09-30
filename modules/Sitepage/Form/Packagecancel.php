<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Siteeventpaid
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Cancel.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
class Sitepage_Form_Packagecancel extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Cancel Package')
            ->setDescription('Note: this will attempt to cancel the ' .
                    'recurring payment regardless of current package status.')
            ->setAttrib('class', 'global_form_popup')
    ;

    // Token
    $this->addElement('Hash', 'token');

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Cancel Package',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
