<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/1/2016
 * Time: 5:30 PM
 */
class Yndynamicform_Form_Admin_EditForm_FormSettings extends Engine_Form
{
    protected $_form;

    public function setForm($form)
    {
        $this -> _form = $form;
    }

    public function getForm()
    {
        return $this -> _form;
    }

    public function init()
    {

        //@todo:client feedback  edit form contents in setting page
       // $this->addElement('Heading', 'form_content', array('label' => 'Form Content'));

        // Form Name - Required
        $this->addElement('Text', 'title',array(
            'label'     => 'Form Title',
            'required'  => true,
            'allowEmpty'=> false,
            'autocomplete' => 'off',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags'
            ),
        ));

        // Form description
        $this->addElement('Textarea', 'description', array(
            'label' => 'Form Description',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags'
            ),
        ));


        // Enable Form
        $this->addElement('Checkbox', 'enable', array(
            'label' => 'Enable this form',
            'value' => '1',
            'description' => 'Status',
        ));
        // Form avatar
        $this->addElement('File', 'photo', array(
            'label' => 'Photo',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        //edit form contents in setting page

        // BEGIN FORM LAYOUT @todo:client feedback  to hide
//        $this->addElement('Heading', 'form_layout', array('label' => 'Form Layout'));
//
        $this->addElement('Textarea', 'style', array(
            'label' => 'Inline CSS',
            'description' => 'CSS properties that only applied to main form element',
        ));



     //   $this->form_layout->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');
        // END FORM LAYOUT

        // BEGIN FORM BUTTON
        $this->addElement('Heading', 'form_button', array(
            'label' => 'Form Button',
            'description' => 'Customize this form\'s submission button as you want. You can either select Text button or Image.'
        ));

        $this->form_button->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');

        //@todo:client feedback hie content
//        $this->addElement('Radio', 'input_type', array(
//            'label' => 'Input Type',
//            'value' => 'txt',
//            'onclick' => 'switchInputType(this)',
//            'multiOptions' => array(
//                'txt' => 'Text',
//                'img' => 'Image',
//            ),
//        ));

        // Elements for text input type
//        $color = '#ffffff';
//
//        $this->addElement('Text', 'btn_text', array(
//            'label' => 'Button Text',
//            'value' => 'Submit',
//            'filters' => array(
//                'StripTags',
//                new Engine_Filter_Censor(),
//            ),
//        ));

        // Element Button Color
//        $this->addElement('Text', 'btn_color', array(
//            'label' => 'Button background color ',
//            'class' => 'yndform_color_input',
//            'value' => '#619dbe',
//        ));
//        $this->btn_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');
//
//        $this->addElement('Heading', 'btn_color_pick', array(
//            'class' => 'yndform_color_picker',
//            'value' => '<input value="#619dbe" type="color" id="btn_color_pick" name="btn_color"/>'
//        ));

//        $this->btn_color_pick->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');
        // Element Button Hover Color
//        $this->addElement('Text', 'btn_hover_color', array(
//            'label' => 'Button background color hover',
//            'class' => 'yndform_color_input',
//            'value' => '#7eb6d5',
//        ));
//        $this->btn_hover_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');
//
//        $this->addElement('Heading', 'btn_hover_color_pick', array(
//            'class' => 'yndform_color_picker',
//            'value' => '<input value="#7eb6d5" type="color" id="btn_hover_color_pick" name="btn_hover_color"/>'
//        ));
//        $this->btn_hover_color_pick->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');

        // Element Text Color
//        $this->addElement('Text', 'txt_color', array(
//            'label' => 'Button text color',
//            'class' => 'yndform_color_input',
//            'value' => $color,
//        ));
//        $this->txt_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');
//
//        $this->addElement('Heading', 'txt_color_pick', array(
//            'class' => 'yndform_color_picker',
//            'value' => '<input value="'.$color.'" type="color" id="txt_color_pick" name="txt_color"/>'
//        ));
//        $this->txt_color_pick->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');

        // Element Text Color
//        $this->addElement('Text', 'txt_hover_color', array(
//            'label' => 'Button text color hover',
//            'class' => 'yndform_color_input',
//            'value' => $color,
//        ));
//        $this->txt_hover_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');
//
//        $this->addElement('Heading', 'txt_hover_color_pick', array(
//            'class' => 'yndform_color_picker',
//            'value' => '<input value="'.$color.'" type="color" id="txt_hover_color_pick" name="txt_hover_color"/>'
//        ));
//        $this->txt_hover_color_pick->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');

        // Element for image input type
//        $this->addElement('Heading', 'heading_btn_image', array(
//            'description' => "Please go to <a href='admin/files'>File & Media Manager</a> to upload the images"
//        ));
//        $this->heading_btn_image->getDecorator('Description')->setEscape(false);

        // Get available files
        $logoOptions = array('' => 'Text-only');
        $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        foreach( $it as $file ) {
            if( $file->isDot() || !$file->isFile() ) continue;
            $basename = basename($file->getFilename());
            if( !($pos = strrpos($basename, '.')) ) continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if( !in_array($ext, $imageExtensions) ) continue;
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Select', 'btn_image', array(
            'label' => 'Button Image',
            'multiOptions' => $logoOptions,
        ));

        $this->addElement('Select', 'btn_hover_image', array(
            'label' => 'Button Hover Image',
            'multiOptions' => $logoOptions,
        ));

        $this->addElement('Heading', 'heading_btn_logic', array(
            'label' => 'Button Conditional logic',
            'value' => 'Create rule to dynamically display or hide the submit button based on values from other fields',
        ));
//        $this->heading_btn_logic->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');
        $this->addElement('Checkbox', 'conditional_enabled', array(
            'label' => 'Enable conditional logic',
//            'description' => 'Create rule to dynamically display or hide the submit button based on values from other fields',
            'onchange' => 'yndformToggleConditionalLogic(this)',
            'value' => 0,
        ));
        $this->addElement('hidden', 'conditional_logic', array(
            'order' => 100,
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
        // END FORM BUTTON

        // BEGIN RESTRICTION
        $this->addElement('Heading', 'heading_restriction', array('label' => 'Restriction'));
        // Element limit the maximum entries are generated in limited time
        $this->addElement('Text', 'entries_max', array(
            'label' => 'Limited Number Of Entries',
            'description' => 'Limit the number of entries of each user allow for this form. "1" is unlimited',

            'attributes' => array(
                'disabled' => 'disabled',
                'value' => '1',
            ),
            'value' => '1',
        ));

        $this->entries_max->setValue(1);
        $this->heading_restriction->getDecorator('HtmlTag2') -> setOption('class', 'yndform_setting_heading');
        $this->entries_max->getDecorator('Description')->setOption('placement', 'PREPEND');

        // Limited time @todo : hided as per client requirement
//        $this->addElement('Select', 'entries_max_per', array(
//            'multiOptions' => array(
//                'total' => 'Total Entries',
//                'day' => 'Per day',
//                'week' => 'Per week',
//                'month' => 'Per month',
//                'year' => 'Per year',
//            ),
//        ));

        // Message will show if number of the entries reached the maximum entries
        $this->addElement('Textarea', 'entries_max_message', array(
            'description' => 'Entry Limit Reached Message',
            'value' => 'You have reached the maximum number of entries allowed for this form. Please try again later.',
        ));

        // Element allow user can edit their own submitted entries ot not
//        $this->addElement('Radio', 'entries_editable', array(
//            'label' => 'Editing Entries',
//            'description' => 'Allow users to edit their own submitted entries',
//            'multiOptions' => array(
//                '1' => 'Yes',
//                '0' => 'No',
//            ),
//            'onclick' => 'showEditingPeriod(this)',
//            'value' => '0'
//        ));

//        $this->addElement('Text', 'entries_editable_within', array(
//            'label' => 'Editing Period',
//            'description' => 'YNDYNAMICFORM_VIEWS_SCRIPTS_SETTING_INDEX_DESCRIPTION',
//            'value' => '5'
//        ));

        $this->addElement('Select', 'time_unit', array(
            'multiOptions' => array(
                'min' => 'Minutes',
                'hour' => 'Hours',
            ),
            'value' => 'min',
        ));

        // Element require user logged in before submitting form
        $availableLabels = array(
            '2' => 'Registered User',
            '1' => 'Guest',
        );

        // Element privacy  : @todo:hide the privacy , require_login client feedback
        $this->addElement('MultiCheckbox', 'privacy', array(
            'label' => 'View Privacy',
            'description' => 'Select type of user who can see this form',
            'multiOptions' => $availableLabels,
            'value' => array_keys($availableLabels),
            'onclick' => 'isGuest(this)',
        ));

        $this->addElement('Radio', 'require_login', array(
            'label' => 'Require Logged In',
            'description' => 'Require user to be logged in to submit this form',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'onclick' => 'showEmailPopup(this)',
            'value' => '1'
        ));

        $this->addElement('Radio', 'show_email_popup', array(
            'label' => 'Show email popup',
            'description' =>   'YNDYNAMICFORM_VIEWS_SCRIPTS_SETTING_EMAIL_DESCRIPTION',
            'multiOptions' => array(
                '2' => 'Yes, show email popup but not required',
                '1' => 'Yes, show email popup and required',
                '0' => 'No, do not show email popup',
            ),
            'value' => '0',
        ));

        // Message will show if user not logged in
        $this->addElement('Textarea', 'require_login_message', array(
            'description' => 'Require Login Message',
            'value' => 'Please log in to able to submit this form.',
        ));

        // Element Valid From and To Date
        $date_validate = new Zend_Validate_Date("YYYY-MM-dd");
        $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);
        $this->addElement('Heading', 'heading_valid_time', array('label' => 'Valid Time'));

        // Element unlimited time
        $this->addElement('Checkbox', 'unlimited_time', array(
            'label' => 'Unlimited Time',
            'value' => '0',
            'onclick' => 'unlimitedTime(this)',
        ));

        // From Date
        $this->addElement('Text', 'valid_from_date', array(
            'placeholder' => 'From',
            'label' => 'Valid Date From',
            'allowEmpty' => true,
            'required' => false,
            'validator' => $date_validate,
        ));

        // To Date
        $this->addElement('Text', 'valid_to_date', array(
            'placeholder' => 'To',
            'label' => 'Valid Date To',
            'allowEmpty' => true,
            'validator' => $date_validate
        ));

        

        // END RESTRICTION

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Update',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

//        $this->addElement('Cancel', 'cancel', array(
//            'label' => 'cancel',
//            'link' => true,
//            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'admin-manage'), 'default', true),
//            'decorators' => array(
//                'ViewHelper'
//            )
//        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}