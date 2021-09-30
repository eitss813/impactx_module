<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Type.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Form_Admin_Type extends Engine_Form {

    public function init() {

        //GET LIST OF MEMBER TYPES
        $db = Engine_Db_Table::getDefaultAdapter();
        $member_type_result = $db->select('option_id, label')
                ->from('engine4_user_fields_options')
                ->query()
                ->fetchAll();
        $member_type_count = count($member_type_result);
        $member_type_array = array('null' => 'No, Create Blank Form');
        $this->setMethod('POST')
                ->setAttrib('class', 'global_form_smoothbox');
        $this->setTitle("Create Form");

        //ADD LABEL
        $this->addElement('Text', 'label', array(
            'label' => 'Form Label',
            'required' => true,
            'allowEmpty' => false,
        ));

        //FORM HEADING
        $this->addElement('Text', 'form_heading', array(
            'label' => 'Form Heading',
            'description' => 'This will be a heading for the form when it is rendered. If you leave this blank, there will be no heading.'
        ));
        $this->form_heading->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

        //FORM DESCRIPTION
        $this->addElement('Textarea', 'form_description', array(
            'label' => 'Form Description',
            'description' => 'This will be shown below the Form Heading. If you leave this blank, no description will be shown.'
        ));
        $this->form_description->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

        $form_data_save = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.saveformdata', 0);
        $email_required = !empty($form_data_save) ? false : true;

        //MAIL BOX
        $this->addElement('Text', 'email', array(
            'label' => 'Email Addresses (comma separated)',
            'description' => 'Responses of filling these forms will be sent to these Email IDs.',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact'),
            'required' => $email_required,
            'allowEmpty' => false,
            'filters' => array(
                'StringTrim',
            ),
        ));
        $this->email->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));


        if (Engine_Api::_()->hasModuleBootstrap('siterecaptcha')) {
            $this->addElement('Radio', 'recaptcha', array(
                'label' => 'Activate reCAPTCHA in this form?',
                'multiOptions' => array(
                    1 => 'Yes, activate No CAPTCHA reCAPTCHA in this form.',
                    0 => 'No, do not activate  No CAPTCHA reCAPTCHA in this form.',
                ),
                'value' => 0,
            ));
        }
        
        

        $this->addElement('Text', 'button_text', array(
            'label' => 'Submit Button Text',
            'value' => 'Submit'
        ));

        //DUPLICATE EXISTING
        $this->addElement('Hidden', 'duplicate', array('order' => 6001,
            'value' => 'null',
        ));


        //ADD SUBMIT
        $this->addElement('Button', 'submit', array(
            'label' => 'Add Form',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        //ADD CANCEL
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => 'parent.Smoothbox.close();',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}
