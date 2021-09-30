<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Blocks_Create extends Engine_Form
{

  public function init()
  {
    // Set form attributes
    $this->setTitle('Create New Block');
    $this->setDescription('Here you can create a new informative block for your website.');
    $this->setAttrib('id', 'form-upload');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));


    // Element: name
    $this->addElement('Text', 'title', array(
      'label' => 'Heading',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 256)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->addElement('Text', 'subheading', array(
      'label' => 'Taxonomy',
      'validators' => array(
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    $this->addElement('Textarea', 'body', array(
      'label' => 'Description',
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        //  'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    // Init file
    $this->addElement('File', 'photo', array(
      'label' => 'Image'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    $this->addElement('Text', 'video_uri', array(
      'label' => 'Image Video Button URL',
      'description' => 'Enter the URL of the video which you want to open in lightbox after users click on this button.',
    ));

    $this->addElement('Dummy', 'cta_1_heading', array(
      'label' => 'Action Button 1',
    ));

    $this->addElement('Text', 'cta_1_label', array(
      'label' => 'Label',
      'description' => 'Enter the label for this CTA button. This button will appear at the bottom right corner of your block.',
    ));

    $this->addElement('Text', 'cta_1_uri', array(
      'label' => 'URL',
      'description' => 'Enter the URL of the page where you want to redirect users after they click on this button.',
    ));

    $this->addElement('Select', 'cta_1_uri_target', array(
      'label' => 'Button Target',
      'description' => 'Do you want  to open the CAT Button URL in new window?',
      'multiOptions' => array(1 => 'Yes, open in new window', 0 => 'No, open in same window'),
      'value' => 0
    ));

    $this->addElement('Dummy', 'cta_2_heading', array(
      'label' => 'Action Button 2',
    ));

    $this->addElement('Text', 'cta_2_label', array(
      'label' => 'Label',
      'description' => 'Enter the label for this CTA button. This button will appear at the bottom right corner of your block.',
    ));

    $this->addElement('Text', 'cta_2_uri', array(
      'label' => 'URL',
      'description' => 'Enter the URL of the page where you want to redirect users after they click on this button.',
    ));

    $this->addElement('Select', 'cta_2_uri_target', array(
      'label' => 'Button Target',
      'description' => 'Do you want  to open the CAT Button URL in new window?',
      'multiOptions' => array(1 => 'Yes, open in new window', 0 => 'No, open in same window'),
      'value' => 0
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Block',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));


    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitecoretheme', 'controller' => 'blocks', 'action' => 'index'), 'admin_default', true),
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }

}