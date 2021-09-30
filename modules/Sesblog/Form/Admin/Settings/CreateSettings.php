<?php
/**
* socialnetworking.solutions
*
* @category   Application_Modules
* @package    Sesblog
* @copyright  Copyright 2019-2020 SocialEngineSolutions
* @license    https://socialnetworking.solutions/license/
* @version    $Id: BlogCreateSettings.php 2019-08-20 00:00:00 socialnetworking.solutions $
* @author     socialnetworking.solutions
*/

class Sesblog_Form_Admin_Settings_CreateSettings extends Engine_Form {

  public function init() {
  
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this->setTitle('Blog Creation Settings')
          ->setDescription('Here, you can choose the settings which are related to the creation of blogs on your website. The settings enabled or disabled will effect Blog creation page, popup and Edit pages.');
    
    $this->addElement('Radio', 'sesblog_redirect_creation', array(
      'label' => 'Redirection After Blog Creation',
      'description' => 'Choose from below where do you want to redirect users after a blog is successfully created.',
      'multiOptions' => array('1' => 'On Blog Dashboard Page', '0' => 'On Blog Profile Page'),
      'value' => $settings->getSetting('sesblog.redirect.creation', 0),
    ));   
    $this->addElement('MultiCheckbox', 'sesblog_photouploadoptions', array(
      'label' => 'Photo Upload options',
      'description' => 'Choose options for Blog Image which will be available to the users while creating or editing their Blogs.',
      'multiOptions' => array(
        'dragdrop' => "Drag & Drop",
        'multiupload' => "Multi Upload",
        'fromurl' => "From URL",
      ),
      'value' => unserialize($settings->getSetting('sesblog.photouploadoptions', 'a:3:{i:0;s:8:"dragdrop";i:1;s:11:"multiupload";i:2;s:7:"fromurl";}')),
    ));

    $this->addElement('Select', 'sesblog_autoopenpopup', array(
      'label' => 'Auto-Open Advanced Share Popup',
      'description' => 'Do you want the "Advanced Share Popup" to be auto-populated after the blog is created? [Note: This setting will only work if you have placed Advanced Share widget on Blog View or Blog Dashboard, wherever user is redirected just after Blog creation.]',
      //  'class'=>'select2',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblog.autoopenpopup', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_ecust_url', array(
      'label' => 'Edit Custom URL',
      'description' => 'Do you want to allow users to edit the custom URL of their blogs once the blogs are created? If you choose Yes, then the URL can be edited from the dashboard of blog?',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.ecust.url', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_enable_tags', array(
      'label' => 'Enable Tags',
      'description' => 'Do you want to enable tags for the Blogs on your website?',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.enable.tags', 1),
    ));

    $this->addElement('Radio', 'sesblog_start_date', array(
      'label' => 'Enable Custom Blog Publish Date',
      'description' => 'Do you want to allow users to choose a custom publish date for their blogs. If you choose Yes, then blogs on your website will display in activity feeds, various pages and widgets on their publish dates.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => $settings->getSetting('sesblog.start.date', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_enb_category', array(
      'label' => 'Enable Blog Category',
      'description' => 'Do you want to enable categories for the blogs at the time of creation?',
      'onchange' => 'changeenablecategory();',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.enb.category', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_cat_req', array(
      'label' => 'Make Blog Categories Mandatory',
      'description' => 'Do you want to make Category field mandatory when users create or edit their Blogs?',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.cat.req', 1),
    ));

    $this->addElement('Radio', 'sesblog_cre_photo', array(
      'label' => 'Enable Blog Main Photo',
      'description' => 'Do you want to enable Blog Main Photo on your website?',
      'onchange' => 'changeenablephoto();',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblog.cre.photo', 1),
    ));

    $this->addElement('Radio', 'sesblog_photo_mandatory', array(
      'label' => 'Make Blogs Main Photo Mandatory',
      'description' => 'Do you want to make Main Photo field mandatory when users create or edit their Blogs?',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblog.photo.mandatory', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_enb_des', array(
      'label' => 'Enable Blog Description',
      'description' => 'Do you want to enable description of Blogs on your website?',
      'onchange' => 'changeenabledescriptition();',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.enb.des', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_des_req', array(
      'label' => 'Make Blog Description Mandatory',
      'description' => 'Do you want to make Description field mandatory when users create or    
      edit their Blogs?',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.des.req', 1),
    ));

    $this->addElement('Radio', 'sesblogcre_people_search', array(
      'label' => 'Enable “People can search for this Blog” Field.',
      'description' => 'Do you want to enable “People can search for this Blog” field while creating and editing Blogs on your website?',
      'multiOptions' => array(
        1 => "Yes",
        0 => "No",
      ),
      'value' => $settings->getSetting('sesblogcre.people.search', 1),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
