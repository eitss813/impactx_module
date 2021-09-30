<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Global.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_Settings_Global extends Engine_Form {

  public function init() {
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    
    $this->setTitle('Global Settings')
          ->setDescription('These settings affect all members in your community.');
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $supportTicket = '<a href="https://socialnetworking.solutions/tickets" target="_blank">Support Ticket</a>';
    $sesSite = '<a href="https://socialnetworking.solutions" target="_blank">SocialNetworking.Solutions website</a>';
    $descriptionLicense = sprintf('Enter your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the %s section on %s. (Key Format: XXXX-XXXX-XXXX-XXXX)',$supportTicket,$sesSite);

    $this->addElement('Text', "sesblog_licensekey", array(
        'label' => 'Enter License key',
        'description' => $descriptionLicense,
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesblog.licensekey'),
    ));
    $this->getElement('sesblog_licensekey')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    if ($settings->getSetting('sesblog.pluginactivated')) {

//       $this->addElement('Radio', 'sesblog_changelanding', array(
//         'label' => 'Set Welcome Page as Landing Page',
//         'description' => 'Do you want to set the Default Welcome Page of this plugin as Landing page of your website? [This is a one time setting, so if you choose ‘Yes’ and save changes, then later you can manually make changes in the Landing page from Layout Editor.]',
//         'multiOptions' => array(
//             '1' => 'Yes',
//             '0' => 'No',
//         ),
//         'value' => $settings->getSetting('sesblog.changelanding', 0),
//       ));


      $this->addElement('Radio', 'sesblog_subscription', array(
        'label' => 'Enable Subscription / Follow',
        'description' => 'Do you want to allow members on your website to Subscribe / Follow Blog owners? If you choose Yes, then members will get notifications when new blogs are posted by Blog Owners they have subscribed.',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
        'value' => $settings->getSetting('sesblog.subscription', 0),
      ));

      $this->addElement('Text', 'sesblog_text_singular', array(
          'label' => 'Singular Text for "Blog"',
          'description' => 'Enter the text which you want to show in place of "Blog" at various places in this plugin like activity feeds, etc.',
          'value' => $settings->getSetting('sesblog.text.singular', 'blog'),
      ));

      $this->addElement('Text', 'sesblog_text_plural', array(
          'label' => 'Plural Text for "Blog"',
          'description' => 'Enter the text which you want to show in place of "Blogs" at various places in this plugin like search form, navigation menu, etc.',
          'value' => $settings->getSetting('sesblog.text.plural', 'blogs'),
      ));

      $this->addElement('Text', 'sesblog_blog_manifest', array(
          'label' => 'Singular "blog" Text in URL',
          'description' => 'Enter the text which you want to show in place of "blog" in the URLs of this plugin.',
          'value' => $settings->getSetting('sesblog.blog.manifest', 'blog'),
      ));

      $this->addElement('Text', 'sesblog_blogs_manifest', array(
          'label' => 'Plural "blogs" Text in URL',
          'description' => 'Enter the text which you want to show in place of "blogs" in the URLs of this plugin.',
          'value' => $settings->getSetting('sesblog.blogs.manifest', 'blogs'),
      ));

      $this->addElement('Radio', 'sesblog_check_welcome', array(
          'label' => 'Welcome Page Visibility',
          'description' => 'Choose from below the users who will see the Welcome page of this plugin?',
          'multiOptions' => array(
              0 => 'Only logged in users',
              1 => 'Only non-logged in users',
              2 => 'Both, logged-in and non-logged in users',
          ),
          'value' => $settings->getSetting('sesblog.check.welcome', 2),
      ));

      $this->addElement('Radio', 'sesblog_enable_welcome', array(
          'label' => 'Blog Main Menu Redirection',
          'description' => 'Choose from below where do you want to redirect users when Blogs Menu item is clicked in the Main Navigation Menu Bar.',
          'multiOptions' => array(
              1 => 'Blog Welcome Page',
              0 => 'Blog Home Page',
              2 => 'Blog Browse Page',
          ),
          'value' => $settings->getSetting('sesblog.enable.welcome', 1),
      ));


      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $this->addElement('Radio', 'sesblog_watermark_enable', array(
          'label' => 'Add Watermark to Photos',
          'description' => 'Do you want to add watermark to photos (from this plugin) on your website? If you choose Yes, then you can upload watermark image to be added to the photos from the <a href="' . $view->baseUrl() . "/admin/sesblog/level" . '">Member Level Settings</a>.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'show_position(this.value)',
          'value' => $settings->getSetting('sesblog.watermark.enable', 0),
      ));
      $this->addElement('Select', 'sesblog_position_watermark', array(
          'label' => 'Watermark Position',
          'description' => 'Choose the position for the watermark.',
          'multiOptions' => array(
              0 => 'Middle ',
              1 => 'Top Left',
              2 => 'Top Right',
              3 => 'Bottom Right',
              4 => 'Bottom Left',
              5 => 'Top Middle',
              6 => 'Middle Right',
              7 => 'Bottom Middle',
              8 => 'Middle Left',
          ),
          'value' => $settings->getSetting('sesblog.position.watermark', 0),
      ));
      $this->sesblog_watermark_enable->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Text', "sesblog_mainheight", array(
          'label' => 'Large Photo Height',
          'description' => 'Enter the maximum height of the large main photo (in pixels). [Note: This photo will be shown in the lightbox and on "Blog Photo View Page". Also, this setting will apply on new uploaded photos.]',
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesblog.mainheight', 1600),
      ));
      $this->addElement('Text', "sesblog_mainwidth", array(
          'label' => 'Large Photo Width',
          'description' => 'Enter the maximum width of the large main photo (in pixels). [Note: This photo will be shown in the lightbox and on "Blog Photo View Page". Also, this setting will apply on new uploaded photos.]',
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesblog.mainwidth', 1600),
      ));
      $this->addElement('Text', "sesblog_normalheight", array(
          'label' => 'Medium Photo Height',
          'description' => "Enter the maximum height of the medium photo (in pixels). [Note: This photo will be shown in the various widgets and pages. Also, this setting will apply on new uploaded photos.]",
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesblog.normalheight', 500),
      ));
      $this->addElement('Text', "sesblog_normalwidth", array(
          'label' => 'Medium Photo Width',
          'description' => "Enter the maximum width of the medium photo (in pixels). [Note: This photo will be shown in the various widgets and pages. Also, this setting will apply on new uploaded photos.]",
          'allowEmpty' => true,
          'required' => false,
          'value' => $settings->getSetting('sesblog.normalwidth', 500),
      ));

      $this->addElement('Radio', "sesblog_other_modulsesblogs", array(
          'label' => 'Blogs Created in Content Visibility',
          'description' => "Choose the visibility of the blogs created in a content to only that content (module) or show in Home page, Browse page and other places of this plugin as well? (To enable users to create blogs in a content or module, place the widget \"Content Profile Blogs\" on the profile page of the desired content.)",
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
          'value' => $settings->getSetting('sesblog.other.modulsesblogs', 1),
      ));

      //default photos
      //New File System Code
      $default_photos_main = array();
      $files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('gif', 'jpg', 'jpeg', 'png')));
      foreach( $files as $file ) {
        $default_photos_main[$file->storage_path] = $file->name;
      }
      
      $fileLink = $view->baseUrl() . '/admin/files/';
      //blog main photo
      if (count($default_photos_main) > 0) {
        $default_photos = array_merge(array('application/modules/Sesblog/externals/images/nophoto_blog_thumb_profile.png' => ''), $default_photos_main);
        $this->addElement('Select', 'sesblog_blog_default_photo', array(
            'label' => 'Main Default Photo for Blogs',
            'description' => 'Choose Main default photo for the blogs on your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a target="_blank" href="' . $fileLink . '">File & Media Manager</a>. Leave the field blank if you do not want to change blog default photo.]',
            'multiOptions' => $default_photos,
            'value' => $settings->getSetting('sesblog.blog.default.photo'),
        ));
      } else {
        $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo in the File & Media Manager for the main photo. Please upload the Photo to be chosen for main photo from the "Layout" >> "<a target="_blank" href="' . $fileLink . '">File & Media Manager</a>" section.') . "</span></div>";
        //Add Element: Dummy
        $this->addElement('Dummy', 'sesblog_blog_default_photo', array(
            'label' => 'Main Default Photo for Blogs',
            'description' => $description,
        ));
      }
      $this->sesblog_blog_default_photo->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sesblog_enable_location', array(
          'label' => 'Enable Location',
          'description' => 'Do you want to enable location for blogs on your website?',
          'multiOptions' => array(
              '1' => 'Yes,Enable Location',
              '0' => 'No,Don\'t Enable Location',
          ),
          'onchange' => 'changeenablelocation();',
          'value' => $settings->getSetting('sesblog.enable.location', 1),
      ));

      $this->addElement('Radio', 'sesblog_location_man', array(
        'label' => 'Make Location Mandatory',
        'description' => 'Do you want to make Location field mandatory when users create or edit their blogs?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.location.man', 1),
      ));

      $this->addElement('Radio', 'sesblog_search_type', array(
          'label' => 'Proximity Search Unit',
          'description' => 'Choose the unit for proximity search of location of blogs on your website.',
          'multiOptions' => array(
              1 => 'Miles',
              0 => 'Kilometres'
          ),
          'value' => $settings->getSetting('sesblog.search.type', 1),
      ));

			$this->addElement('Radio', 'sesblog_login_continuereading', array(
          'label' => 'Continue Reading Button Redirection for Non-logged in Users',
          'description' => 'Do you want to redirect non-logged in users to the login page of your website when they click on "Continue Reading" button on Blog view pages? If you choose No, then users can see Full Blog at the same page.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No',
          ),
          'value' => $settings->getSetting('sesblog.login.continuereading', 1),
      ));


      $this->addElement('Radio', 'sesblog_enable_subblog', array(
          'label' => 'Allow to create Sub Blogs',
          'description' => 'Do you want to allow users to create sub blogs on your website?',
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
          'value' => $settings->getSetting('sesblog.enable.subblog', 1),
      ));

      $this->addElement('Radio', 'sesblog_enable_favourite', array(
          'label' => 'Allow to Favourite Blogs',
          'description' => 'Do you want to allow users to favourite blogs on your website?',
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
          'value' => $settings->getSetting('sesblog.enable.favourite', 1),
      ));

      $this->addElement('Radio', 'sesblog_enable_report', array(
          'label' => 'Allow to Report Blogs',
          'description' => 'Do you want to allow users to report blogs on your website?',
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
          'value' => $settings->getSetting('sesblog.enable.report', 1),
      ));

      $this->addElement('Radio', 'sesblog_enable_sharing', array(
        'label' => 'Allow to Share Blogs',
        'description' => 'Do you want to allow users to share blogs on your website?',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
        'value' => $settings->getSetting('sesblog.enable.sharing', 1),
      ));

      $this->addElement('Radio', 'sesblog_enable_claim', array(
        'label' => 'Allow to Claim Blogs',
        'description' => 'Do you want to allow users to claim blogs on their website as their blogs?',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
        'value' => $settings->getSetting('sesblog.enable.claim', 1),
      ));
      
      $this->addElement('Radio', 'sesblog_enablereadtime', array(
        'label' => 'Enable Minute Read Count',
        'description' => 'Do you want to enable minute read count on your website?',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
        'value' => $settings->getSetting('sesblog.enablereadtime', 1),
      ));

      $this->addElement('Select', 'sesblog_taboptions', array(
        'label' => 'Menu Items Count in Main Navigation',
        'description' => 'How many menu items do you want to show in the Main Navigation Menu of this plugin?',
        'multiOptions' => array(
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
        ),
        'value' => $settings->getSetting('sesblog.taboptions', 6),
      ));

//      $this->addElement('Select', 'sesblog_enablsesblogdesignview', array(
//          'label' => 'Enable Blog Profile Views',
//          'description' => 'Do you want to enable users to choose views for their Blogs? (If you choose No, then you can choose a default layout for the Blog Profile pages on your website.)',
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No',
//          ),
//          'onchange' => "enablsesblogdesignview(this.value)",
//          'value' => $settings->getSetting('sesblog.enablsesblogdesignview', 0),
//      ));
//
//      $chooselayout = $settings->getSetting('sesblog.chooselayout', 'a:4:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";}');
//      $chooselayoutVal = unserialize($chooselayout);
//;
//      $this->addElement('MultiCheckbox', 'sesblog_chooselayout', array(
//          'label' => 'Choose Blog Profile Pages',
//          'description' => 'Choose layout for the blog profile pages which will be available to users while creating or editing their blogs.',
//          'multiOptions' => array(
//              1 => 'Design 1',
//              2 => 'Design 2',
//              3 => 'Design 3',
//              4 => 'Design 4',
//          ),
//          'value' => $chooselayoutVal,
//      ));
//
//      $this->addElement('Radio', 'sesblog_defaultlayout', array(
//          'label' => 'Default Blog Profile Page',
//          'description' => 'Choose default layout for the blog profile pages.',
//          'multiOptions' => array(
//              1 => 'Design 1',
//              2 => 'Design 2',
//              3 => 'Design 3',
//              4 => 'Design 4',
//          ),
//          'value' => $settings->getSetting('sesblog.defaultlayout', 1),
//      ));

      // Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    } else {
      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate Your Plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }
}
