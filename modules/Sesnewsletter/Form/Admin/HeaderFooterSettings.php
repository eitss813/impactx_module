<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: HeaderFooterSettings.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_HeaderFooterSettings extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this
        ->setTitle('Header & Footer Design Settings')
        ->setDescription('These settings will affect color settings of Header & Footer of all the templates.');

        $this->addElement('Text', "sesnewsletter_emailtemwidth", array(
            'label' => 'Email Templates Width',
            'description' => "Enter the width of the Email Templates (in px).",
            'allowEmpty' => false,
            'required' => true,
            'value' => $settings->getSetting('sesnewsletter.emailtemwidth', '600'),
        ));

        $this->addElement('Dummy', 'header_settings', array(
            'label' => 'Header Styling Settings',
        ));
				 $this->addElement('Text', "sesnewsletter_tophebgcolor", array(
            'label' => 'Top Header Background Color',
            'allowEmpty' => false,
            'class' => 'SEScolor',
            'value' => $settings->getSetting('sesnewsletter.tophebgcolor', '000'),
        ));
				 $this->addElement('Text', "sesnewsletter_topheaderfontcolor", array(
            'label' => 'Top Header Font Color',
            'allowEmpty' => false,
            'class' => 'SEScolor',
            'value' => $settings->getSetting('sesnewsletter.topheaderfontcolor', 'FFF'),
        ));

        $this->addElement('Text', "sesnewsletter_hebgcolor", array(
            'label' => 'Header Background Color',
            'allowEmpty' => false,
            'required' => true,
            'class' => 'SEScolor',
            'value' => $settings->getSetting('sesnewsletter.hebgcolor', 'FFF'),
        ));

        $this->addElement('Text', "sesnewsletter_headerfontcolor", array(
            'label' => 'Header Font Color',
            'allowEmpty' => false,
            'required' => true,
            'class' => 'SEScolor',
            'value' => $settings->getSetting('sesnewsletter.headerfontcolor', '000'),
        ));

        $this->addElement('Radio', 'sesnewsletter_headermenu', array(
            'label' => 'Enable Header Menu',
            'description' => 'Do you want enable header menu?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => $settings->getSetting('sesnewsletter.headermenu', 1),
        ));
        $this->addElement('Text', "sesnewsletter_phonenumber", array(
            'label' => 'Phone Number',
            'description' => 'Enter your phone number. This will be displayed in the newsletter.',
            'value' => $settings->getSetting('sesnewsletter.phonenumber', ''),
        ));
        $this->addElement('Text', "sesnewsletter_email", array(
            'label' => 'Email',
            'description' => 'Enter your email. This will be displayed in the newsletter.',
            'value' => $settings->getSetting('sesnewsletter.email', ''),
        ));

        $this->addElement('Dummy', 'sesnewsletter_headermenu', array(
          'label' => 'Manage Header Menu',
          'description' => "<div><span>".Zend_Registry::get('Zend_Translate')->_("Please <a href='admin/menus?name=sesnewsletter_header' target='_blank'>click here</a> to update newsletter header menu.") . "</span></div>",
        ));
        $this->sesnewsletter_headermenu->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Radio', 'sesnewsletter_enablelogo', array(
            'label' => 'Enable Logo / Site Title',
            'description' => 'Do you want to enable logo or site title of your website in the newsletters.',
            'multiOptions' => array(
                '1' => 'Logo',
                '0' => 'Site Title',
            ),
            'onchange' => 'changeHeaderLogo(this.value)',
            'value' => $settings->getSetting('sesnewsletter.enablelogo', '0'),
        ));

        $this->addElement('Text', "sesnewsletter_logositetext", array(
            'label' => 'Site Title',
			'description' => 'Enter the title of your site. This will be displayed in the newsletter.',
//             'allowEmpty' => false,
//             'required' => true,
            'value' => $settings->getSetting('sesnewsletter.logositetext', $settings->getSetting('core.general.site.title', '')),
        ));

        //New File System Code
        $banner_options = array('' => '');
        $files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('gif', 'jpg', 'jpeg', 'png')));
        foreach( $files as $file ) {
          $banner_options[$file->storage_path] = $file->name;
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $fileLink = $view->baseUrl() . '/admin/files/';
        $this->addElement('Select', 'sesnewsletter_helogo', array(
            'label' => 'Choose Header Logo',
            'description' => 'Choose from below the header logo for newsletter. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
            'multiOptions' => $banner_options,
            'escape' => false,
            'value' => $settings->getSetting('sesnewsletter.helogo', ''),
        ));
        $this->sesnewsletter_helogo->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));



        $this->addElement('Dummy', 'footer_settings', array(
            'label' => 'Footer Styling Settings',
        ));

        $this->addElement('Text', "sesnewsletter_fotrbgcolor", array(
            'label' => 'Footer Background Color',
            'allowEmpty' => false,
            'required' => true,
            'class' => 'SEScolor',
            'value' => $settings->getSetting('sesnewsletter.fotrbgcolor', '000'),
        ));

        $this->addElement('Text', "sesnewsletter_footerfontcolor", array(
            'label' => 'Footer Font Color',
            'allowEmpty' => false,
            'required' => true,
            'class' => 'SEScolor',
            'value' => $settings->getSetting('sesnewsletter.footerfontcolor', 'FFF'),
        ));

        $this->addElement('Radio', 'sesnewsletter_footermenu', array(
            'label' => 'Enable Footer Menu',
            'description' => 'Do you want enable footer menu?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => $settings->getSetting('sesnewsletter.footermenu', 1),
        ));

        $this->addElement('Dummy', 'sesnewsletter_footermenu', array(
          'label' => 'Manage Footer Menu',
          'description' => "<div><span>".Zend_Registry::get('Zend_Translate')->_("Please <a href='admin/menus?name=sesnewsletter_footer' target='_blank'>click here</a> to update newsletter footer menu.") . "</span></div>",
        ));
        $this->sesnewsletter_footermenu->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Radio', 'sesnewsletter_fotrenablelogo', array(
            'label' => 'Enable Logo / Site Title for Footer',
            'description' => 'Enable Logo / Site Title for Footer',
            'multiOptions' => array(
                '1' => 'Logo',
                '0' => 'Site Title',
            ),
            'onchange' => 'changeFooterLogo(this.value)',
            'value' => $settings->getSetting('sesnewsletter.fotrenablelogo', '0'),
        ));

        $this->addElement('Text', "sesnewsletter_fotrlogositetext", array(
            'label' => 'Site Title',
//             'allowEmpty' => false,
//             'required' => true,
            'value' => $settings->getSetting('sesnewsletter.fotrlogositetext', $settings->getSetting('core.general.site.title', '')),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $fileLink = $view->baseUrl() . '/admin/files/';
        $this->addElement('Select', 'sesnewsletter_fotrlogo', array(
            'label' => 'Choose Footer Logo',
            'description' => 'Choose from below the footer logo for newsletter. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
            'multiOptions' => $banner_options,
            'escape' => false,
            'value' => $settings->getSetting('sesnewsletter.fotrlogo', ''),
        ));
        $this->sesnewsletter_fotrlogo->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));


        $this->addElement('Text', "sesnewsletter_facebook", array(
            'label' => 'Facebook URL',
            'value' => $settings->getSetting('sesnewsletter.facebook', ''),
        ));

        $this->addElement('Text', "sesnewsletter_twitter", array(
            'label' => 'Twitter URL',
            'value' => $settings->getSetting('sesnewsletter.twitter', ''),
        ));
        $this->addElement('Text', "sesnewsletter_linkedin", array(
            'label' => 'Linkedin URL',
            'value' => $settings->getSetting('sesnewsletter.linkedin', ''),
        ));
        $this->addElement('Text', "sesnewsletter_pinterest", array(
            'label' => 'Pinterest URL',
            'value' => $settings->getSetting('sesnewsletter.pinterest', ''),
        ));
        $this->addElement('Text', "sesnewsletter_youtube", array(
            'label' => 'YouTube URL',
            'value' => $settings->getSetting('sesnewsletter.youtube', ''),
        ));
        $this->addElement('Text', "sesnewsletter_websiteurl", array(
            'label' => 'WebSite URL',
            'value' => $settings->getSetting('sesnewsletter.websiteurl', ''),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
  }
}
