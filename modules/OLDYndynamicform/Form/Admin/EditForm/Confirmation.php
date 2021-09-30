<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/2/2016
 * Time: 3:09 PM
 */
class Yndynamicform_Form_Admin_EditForm_Confirmation extends Engine_Form
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setTitle('Add New Confirmation');
        $this->setAttrib('style', 'width: 768px');

        $this->addElement('Text', 'name', array(
            'label' => 'Confirmation Name',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Radio', 'type', array(
            'multiOptions' => array(
                'text' => 'Text',
                'url' => 'Redirect URL',
            ),
            'onclick' => 'switchConfirmationType(this)',
            'value' => 'text',
        ));

        // Confirmation URL
        $this->addElement('Text', 'confirmation_url', array(
            'label' => 'Return URL',
            'validators' => array(
                array('Regex', true, array('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i')),
            )
        ));

        // Confirmation Text
        $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'yndynamicform_general', true);
        $this->addElement('TinyMce', 'confirmation_text', array(
            'disableLoadDefaultDecorators' => true,
            'editorOptions' => array(
                'bbcode' => 1,
                'html' => 1,
                'theme_advanced_buttons1' => array(
                    'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
                ),
                'theme_advanced_buttons2' => array(
                     
                    'bold', 'italic', 'underline',
                    'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
                    'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
                ),
                'plugins' => array(
                ),
                'upload_url' => $upload_url,
            ),
            'allowEmpty' => true,
            'decorators' => array('ViewHelper'),
//            'filters' => array(
//                new Engine_Filter_Censor(),
//                new Engine_Filter_Html(array('AllowedTags' => $allowed_html))),
        ));

        $this->addElement('hidden', 'conditional_logic', array(
            'order' => 100,
        ));

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

        // Enable this confirmation.
        $this->addElement('Checkbox', 'enable', array(
            'label' => 'Enable This Confirmation',
            'value' => 1,
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Add confirmation',
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
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');

        // Hidden field
        $this->addElement('Hidden', 'order');
    }

}