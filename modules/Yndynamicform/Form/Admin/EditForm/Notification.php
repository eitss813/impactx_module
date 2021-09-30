<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/3/2016
 * Time: 1:57 PM
 */
class Yndynamicform_Form_Admin_EditForm_Notification extends Engine_Form
{
    /**
     *
     */
    public function init()
    {
        $this -> setMethod('POST');
        $this -> setTitle('Email Notification');
        $this->setAttrib('style', 'width: 768px')->setDescription('This email will be sent to site admin and all moderators of this form.');

        $this -> addElement('Text', 'name', array(
            'label' => 'Notification Name',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $this->name->getDecorator('Description')->setOption('placement', 'APPEND');

        // Elements for email
        $this -> addElement('Text', 'notification_email_subject', array(
            'label' => 'Subject',
            'value' => 'Form has been submitted',
        ));

        $this -> addElement('Textarea', 'notification_email_body', array(
            'label' => 'Message Body',
            'value' => Zend_Registry::get('Zend_View')->translate('YNDYNAMICFORM_MAILTEMPLATE_BODY'),
            'description' => 'Available Placeholders:
                              [website_name],[website_link],[form_name],[form_link]'
        ));

        $this -> notification_email_body -> getDecorator('Description') -> setOption('placement', 'APPEND');

        $this->addElement('hidden', 'conditional_logic', array(
            'order' => 100,
        ));

        // Enable conditional logic.
        $this->addElement('Checkbox', 'conditional_enabled', array(
            'label' => 'Enable Conditional Logic',
            'value' => 0,
            'onchange' => 'yndformToggleConditionalLogic(this)',
        ));
        $this->addElement('dummy', 'conditional_logic_tpl', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_conditional-logic.tpl',
                    'class' => 'form_element',
                )
            )),
        ));

        // Enable this notification.
        $this->addElement('Checkbox', 'enable', array(
            'label' => 'Enable This Notification',
            'value' => 1,
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Add notification',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick'=> 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}