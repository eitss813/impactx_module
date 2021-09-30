<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Banner.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Banner extends Engine_Form {

  public function init() {

    //New File System Code
    $banner_options = array('' => '');
    $files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('gif', 'jpg', 'jpeg', 'png')));
    foreach( $files as $file ) {
      $banner_options[$file->storage_path] = $file->name;
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/files/';

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
    
    $this->addElement('Select', 'banner_image', array(
        'description' => 'Choose from below the banner image for your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>. Leave the field blank if you do not want to show logo.]',
        'multiOptions' => $banner_options,
        'escape' => false,
    ));
    $this->banner_image->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    
    $this->addElement('Text', 'banner_title', array(
        'label' => 'Caption',
        'description' => 'Enter the caption for this photo.',
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Text', 'title_button_color', array(
        'label' => 'Caption Color',
        'description' => 'Choose the color for the caption.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'description' => 'Enter the description for this photo.',
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Text', 'description_button_color', array(
        'label' => 'Description Color',
        'description' => 'Choose the color for the description.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
    ));
		//full width banner setting
		$this->addElement('Select', 'fullwidth', array(
        'label' => 'Want to show bannner in full width?',
        'description' => 'Want to show bannner in full width?',
        'multiOptions' => array('1' => 'Yes,want to show banner in full width', '0' => 'No,don\'t want to show banner in full width'),
        'value' => '1',
    ));
    //login button code
    $this->addElement('Select', 'button1', array(
        'label' => 'Show Button 1',
        'description' => 'Do you want to show button 1?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '1',
    ));
    $this->addElement('Text', 'button1_text', array(
        'label' => 'Button 1 Text',
        'description' => 'Enter the text for the button 1.',
        'allowEmpty' => true,
        'required' => false,
        'value' => 'Button - 1',
    ));
    $this->addElement('Text', 'button1_text_color', array(
        'label' => 'Button 1 Text Color',
        'description' => 'Choose the color for the button 1 text.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '0295FF',
    ));
    $this->addElement('Text', 'button1_color', array(
        'label' => 'Button 1 Color',
        'description' => 'Choose the color for the button 1.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#ffffff',
    ));
    $this->addElement('Text', 'button1_mouseover_color', array(
        'label' => 'Button 1 Mouse-over Color',
        'description' => 'Choose the color for the button when users mouse over on it.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#eeeeee',
    ));
    $this->addElement('Text', 'button1_link', array(
        'label' => 'Link for Button 1',
        'description' => 'Enter a link for the button 1.',
        'allowEmpty' => true,
        'required' => false,
    ));


    //signup button code
    $this->addElement('Select', 'button2', array(
        'label' => 'Show Button 2',
        'description' => 'Do you want to show button 2?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '1',
    ));
    $this->addElement('Text', 'button2_text', array(
        'label' => 'Button 2 Text',
        'description' => 'Enter the text for the button 2.',
        'allowEmpty' => true,
        'required' => false,
        'value' => 'Button - 2',
    ));
    $this->addElement('Text', 'button2_text_color', array(
        'label' => 'Button 2 Text Color',
        'description' => 'Choose the color for the button 2 text.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#ffffff',
    ));
    $this->addElement('Text', 'button2_color', array(
        'label' => 'Button 2 Color',
        'description' => 'Choose the color for the button 2.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#0295FF',
    ));
    $this->addElement('Text', 'button2_mouseover_color', array(
        'label' => 'Signup Button 2 Mouse-over Color',
        'description' => 'Choose the color for the button 2 when users mouse over on it.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#067FDE',
    ));
    $this->addElement('Text', 'button2_link', array(
        'label' => 'Link for Button 2',
        'description' => 'Enter a link for the button 2.',
        'allowEmpty' => true,
        'required' => false,
    ));

    //extra button code
    $this->addElement('Select', 'button3', array(
        'label' => 'Show Additional Button 3',
        'description' => 'Do you want to show an button 3 on this photo?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '0',
    ));
    $this->addElement('Text', 'button3_text', array(
        'label' => 'Button 3 Text',
        'description' => 'Enter the text for the button 3.',
        'allowEmpty' => true,
        'required' => false,
        'value' => 'Button - 3',
    ));
    $this->addElement('Text', 'button3_text_color', array(
        'label' => 'Button 3 Text Color',
        'description' => 'Choose the color for the button 3 text.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#ffffff',
    ));
    $this->addElement('Text', 'button3_color', array(
        'label' => 'Button 3 Color',
        'description' => 'Choose the color for the button 3.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#F25B3B',
    ));
    $this->addElement('Text', 'button3_mouseover_color', array(
        'label' => 'Button 3 Mouse-over Color',
        'description' => 'Choose the color for the button 3 when users mouse over on it.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
        'value' => '#EA350F',
    ));
    $this->addElement('Text', 'button3_link', array(
        'label' => 'Link for Button 3',
        'description' => 'Enter a link for the button 3.',
        'allowEmpty' => true,
        'required' => false,
    ));


    $this->addElement('Text', 'height', array(
        'label' => "Enter the height of this widget(in pixels).",
        'value' => '250',
    ));
  }

}
