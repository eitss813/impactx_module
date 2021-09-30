<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Aboutus.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Form_Admin_Settings_Aboutus extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this
            ->setTitle('About Us Details')
            ->setDescription('Below, you can enter some details about your website using the WYSIWYG Editor. Details entered here will display on the widgetized "About Us Page".');

    $this->addElement('Text', 'sesmultipleform_aboutusurlmanifest', array(
        'label' => '"about-us" Text in URL',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Enter the text which you want to show in place of "about-us" in the URL of About Us page of this plugin.',
        'value' => $settings->getSetting('sesmultipleform.aboutusurlmanifest', "about-us"),
    ));

    $this->addElement('Radio', 'sesmultipleform_aboutusposition', array(
        'label' => 'Display "About Us" Link',
        'description' => 'Choose from below where do you want to display "About Us" link on your website? [If you choose ‘No’ right now, then you can enable to show the links later on from "Layout" >> "Menu Editor" section.]',
        'multiOptions' => array(
            3 => 'In Main Navigation Menu Bar.',
            2 => 'In Mini Navigation Menu Bar.',
            1 => 'In Footer Menu Bar.',
            0 => 'No, do not show link.',
        ),
        'value' => $settings->getSetting('sesmultipleform.aboutusposition', 1),
    ));

    $upload_photo_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesbasic', 'controller' => 'manage', 'action' => "upload-image"), 'admin_default', true);

    $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr';
    $editorOptions = array(
        'upload_url' => $upload_photo_url,
        'html' => (bool) $allowed_html,
    );

    if (!empty($upload_photo_url)) {
      $editorOptions['plugins'] = array(
          'table', 'fullscreen', 'media', 'preview', 'paste',
          'code', 'image', 'textcolor', 'jbimages', 'link'
      );

      $editorOptions['toolbar1'] = array(
          'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
          'media', 'image', 'jbimages', 'link', 'fullscreen',
          'preview'
      );
    }

    //Add Elemnt: About Us
    $this->addElement('TinyMce', 'sesmultipleform_aboutus', array(
        'label' => "About Us Details",
        "description" => "Enter the details about your website in the WYSIWYG editor below.",
        'required' => true,
        'allowEmpty' => false,
        'editorOptions' => $editorOptions,
        'value' => $settings->getSetting('sesmultipleform.aboutus', '<p><span style="font-size: 12pt;">About Us</span></p><p>This page will contain the About Us details of your choice.</p>'),
    ));

    //Add Element: Submit Button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}