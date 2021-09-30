<?php
class Yndynamicform_Form_Admin_Entries_Delete extends Engine_Form
{
  public function init()
  {
    //Set form attributes
    $this->setTitle('Delete Entries')
      ->setDescription('Are you sure you want to delete selected entries?')
      ->setAttrib('class', 'global_form_popup')
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'button',
      'ignore' => true,
        'onclick' => 'parent.submitForm();',
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