<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Field.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Yndynamicform_Form_Admin_Field_PageBreak extends Engine_Form
{
  protected $_option;

  public function getOption()
  {
    return $this -> _option;
  }

  public function setOption($option)
  {
    $this -> _option = $option;
  }

  public function init()
  {
    $this->setAttrib('style', 'width: 768px');
    $this->setMethod('POST')
        ->setDescription('YNDYNAMICFORM_MANAGE_FIELDS_PAGE_BREAK_DESCRIPTION')
        ->setAttrib('class', 'global_form_popup')
        ->setTitle('Edit Form Field');

    // Add type
    $types = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');
    $availableTypes = array();
    foreach( $types as $fieldType => $info ) {
      $availableTypes[$fieldType] = $info['label'];
    }

    // Add label
    $this->addElement('Text', 'label', array(
        'label' => 'Field Label',
        'required' => true,
        'allowEmpty' => false,
    ));

    // Add description
    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'rows' => 4,
    ));

    // Limited time
    $this->addElement('Radio', 'progress_indicator', array(
        'label' => 'Progress Indicator',
        'data_section' => 'progress_indicator',
        'description' => 'Select type of visual progress indicator you would like to display on top of the form',
        'onchange' => 'yndformSwitchSection(this)',
        'class' => 'yndform_switchable',
        'multiOptions' => array(
            'none' => 'None',
            'bar' => 'Progress Bar',
            'step' => 'Steps',
        ),
        'value' => 'none'
    ));

    $this->addElement('dummy', 'progress_indicator_preview_tpl', array(
        'decorators' => array( array(
            'ViewScript',
            array(
                'viewScript' => '_progress-indicator_preview.tpl',
                'class' => 'form_element',
            )
        )),
    ));

    // colors
    $this->addElement('Text', 'text_color', array(
        'label' => 'Text color',
        'class' => 'yndform_color',
        'value' => '#000000',
        'disabled' => 'true',
    ));
      $this->text_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');

    $this->addElement('Heading', 'text_color_box', array(
        'class' => 'yndform_color_picker',
        'value' => '<input class="" value="'.$this->text_color->getValue().'" type="color" id="text_color_picker" name="text_color"/>'
    ));

    $this->addElement('Text', 'background_color', array(
        'label' => 'Background Color',
        'class' => 'yndform_color',
        'value' => '#5ba1cd',
        'disabled' => 'true',
    ));
      $this->background_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');

    $this->addElement('Heading', 'background_color_box', array(
        'class' => 'yndform_color_picker',
        'value' => '<input class="" value="'.$this->background_color->getValue().'" type="color" id="background_color_picker" name="background_color"/>'
    ));

    $this->addElement('hidden', 'total_pages', array(
      'order' => 100,
        'value' => '2',
    ));

    $this->addElement('hidden', 'page_names_hidden', array(
      'order' => 101,
        'value' => '',
    ));

    $this->addElement('Heading', 'page_names_heading', array(
        'label' => 'Page Names',
        'description' => 'Name each of the pages on your form. Page names are displayed with the selected progress indicator.'
    ));

    $this->addElement('dummy', 'page_names_tpl', array(
        'decorators' => array( array(
            'ViewScript',
            array(
                'viewScript' => '_page_names.tpl',
                'class' => 'form_element',
            )
        )),
    ));

    $this->addElement('Select', 'type', array(
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => $availableTypes,
        'style' => 'display:none',
        'onchange' => 'var form = this.getParent("form"); form.method = "get"; form.submit();',
    ));

    $this->type->removeDecorator('label');

    // Next pre buttons =========
    $this->addElement('Radio', 'next_button', array(
        'label' => 'Next Button',
        'onchange' => 'yndformSwitchSection(this)',
        'class' => 'yndform_switchable',
        'multiOptions' => array(
            'text' => 'Text',
            'image' => 'Image',
        ),
        'value' => 'text'
    ));

    $this->addElement('Text', 'next_button_text', array(
        'label' => '',
        'description' => 'Enter the text you would like to appear on the page next button',
        'value' => 'Next'
    ));

    // colors
    $this->addElement('Text', 'next_button_text_color', array(
        'label' => 'Text color',
        'class' => 'yndform_color',
        'value' => '',
        'disabled' => 'true',
    ));
      $this->next_button_text_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');

    $this->addElement('Heading', 'next_button_text_color_box', array(
        'class' => 'yndform_color_picker',
        'value' => '<input class="" value="'.$this->next_button_text_color->getValue().'" type="color" id="next_button_text_color_picker" name="next_button_text_color"/>'
    ));

    $this->addElement('Text', 'next_button_text_bg_color', array(
        'label' => 'Button Color',
        'class' => 'yndform_color',
        'value' => '',
        'disabled' => 'true',
    ));
      $this->next_button_text_bg_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');

    $this->addElement('Heading', 'next_button_text_bg_color_box', array(
        'class' => 'yndform_color_picker',
        'value' => '<input class="" value="'.$this->next_button_text_bg_color->getValue().'" type="color" id="next_button_color_picker" name="next_button_color"/>'
    ));

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

    $this->addElement('Heading', 'next_button_image_heading', array(
        'description' => "Please go to <a href='admin/files' target='_parent'>File & Media Manager</a> to upload the images"
    ));
    $this->next_button_image_heading->getDecorator('Description')->setEscape(false);
    $this->next_button_image_heading->removeDecorator('label');

    $this->addElement('Select', 'next_button_image', array(
        'label' => 'Next Button Image',
        'multiOptions' => $logoOptions,
    ));

    $this->addElement('Select', 'next_button_image_hover', array(
        'label' => 'Next Button Hover Image',
        'multiOptions' => $logoOptions,
    ));

    //

    $this->addElement('Checkbox', 'conditional_enabled', array(
        'label' => 'Enable conditional logic',
        'description' => 'Create rule to dynamically display or hide this Next Button based on values from the other form fields which on the same page of this button.',
        'onchange' => 'yndformToggleConditionalLogic(this)',
        'value' => 0,
    ));

    $this->conditional_enabled->getDecorator('Description')->setOption('placement', 'append');


    $this->addElement('hidden', 'conditional_logic', array(
        'order' => 105,
    ));

    //conditional logic
    $this->addElement('dummy', 'conditional_logic_tpl', array(
        'decorators' => array( array(
            'ViewScript',
            array(
                'viewScript' => '_conditional-logic.tpl',
                'class' => 'form_element',
            )
        )),
    ));

    // ======================
    $this->addElement('Radio', 'pre_button', array(
        'label' => 'Previous Button',
        'onchange' => 'yndformSwitchSection(this)',
        'class' => 'yndform_switchable',
        'multiOptions' => array(
            'text' => 'Text',
            'image' => 'Image',
        ),
        'value' => 'text',
    ));

    $this->addElement('Text', 'pre_button_text', array(
        'label' => '',
        'description' => 'Enter the text you would like to appear on the page previous button',
        'value' => 'Previous',
    ));

    // colors
    $this->addElement('Text', 'pre_button_text_color', array(
        'label' => 'Text color',
        'class' => 'yndform_color',
        'value' => '',
        'disabled' => 'true',
    ));
      $this->pre_button_text_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');

    $this->addElement('Heading', 'pre_button_text_color_box', array(
        'class' => 'yndform_color_picker',
        'value' => '<input class="" value="'.$this->pre_button_text_color->getValue().'" type="color" id="pre_button_text_color_picker" name="pre_button_text_color"/>'
    ));

    $this->addElement('Text', 'pre_button_text_bg_color', array(
        'label' => 'Button Color',
        'class' => 'yndform_color',
        'value' => '',
        'disabled' => 'true',
    ));
      $this->pre_button_text_bg_color->getDecorator('HtmlTag2') -> setOption('class', 'form-wrapper yndform_color_wrapper');

    $this->addElement('Heading', 'pre_button_text_bg_color_box', array(
        'class' => 'yndform_color_picker',
        'value' => '<input class="" value="'.$this->pre_button_text_bg_color->getValue().'" type="color" id="pre_button_color_picker" name="pre_button_color"/>'
    ));

    // previous button image
    $this->addElement('Heading', 'pre_button_image_heading', array(
        'description' => "Please go to <a href='admin/files' target='_parent'>File & Media Manager</a> to upload the images"
    ));
    $this->pre_button_image_heading->getDecorator('Description')->setEscape(false);
    $this->pre_button_image_heading->removeDecorator('label');


    $this->addElement('Select', 'pre_button_image', array(
        'label' => 'Previous Button Image',
        'multiOptions' => $logoOptions,
    ));

    $this->addElement('Select', 'pre_button_image_hover', array(
        'label' => 'Previous Button Hover Image',
        'multiOptions' => $logoOptions,
    ));

    // end buttons =====================
    // Add Css
    $this->addElement('Text', 'style', array(
        'label' => 'Inline CSS',
    ));

    // Display
    $this->addElement('hidden', 'display', array(
        'value' => 1,
        'order' => 106,
    ));

    $this->addElement('Hidden', 'show_registered', array(
      'order' => 103,
        'value' => 1,
    ));

    $this->addElement('Hidden', 'show_guest', array(
        'order' => 104,
        'value' => 1,
    ));

    // Add submit
    $this->addElement('Button', 'execute', array(
        'label' => 'Save',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10000,
        'ignore' => true,
    ));

    // Add cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10001,
        'ignore' => true,
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
        'order' => 10002,
    ));
  }
}