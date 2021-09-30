<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Level.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_Level_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {
  
    parent::init();

    $this->setTitle('Member Level Settings')
        ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Blogs?',
      'description' => 'Do you want to let members view blogs? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow members to view all blogs, even private ones.',
        1 => 'Yes, allow members to view their own blogs.',
        0 => 'No, do not allow blogs to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if($this->isPublic() )
    {
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Blogs?',
        'description' => 'Do you want to let members of this level to create blogs? If set to Yes, then members of this level can view “Create New Page” tab and will get redirected to login page after clicking on it.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of blogs.',
          0 => 'No, do not allow blogs to be created.'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Blogs?',
        'description' => 'Do you want to let members of this level to comment on blogs? If set to Yes, then members of this level can view “comments post” box and will get redirected to the login page of your website if try to do comment on the blogs.',
        'multiOptions' => array(
          1 => 'Yes, allow members to comment on blogs.',
          0 => 'No, do not allow blogs to be created.'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'cotinuereading', array(
        'label' => 'Continue Reading Button Redirection',
        'description' => 'Do you want to redirect member of this level to the login page of your website when they click on "Continue Reading" button on Blog view pages? If you choose No, then users can see Full Blog at the same page.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'claim', array(
        'label' => 'Allow to Claim Blogs',
        'description' => 'Do you want to allow users of this level to claim blogs on their website? If set to Yes, then members of this level can view “Claim For Blogs” tab in the navigation menu of this plugin and will get redirected to the login page of your website if try to do claiming on the blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'favourite', array(
        'label' => 'Allow to Favourite Blogs',
        'description' => ' Do you want to allow users of this level to favourite blogs on your website?  If set to Yes, then members of this level can view “Favourite” button at blog view page & will get redirected to the login page of your website if try to Favourite blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'like', array(
        'label' => 'Allow to like Blogs',
        'description' => ' Do you want to allow users of this level to like blogs on your website?  If set to Yes, then members of this level can view “Like” button at blog view page & will get redirected to the login page of your website if try to Like blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => 1,
      ));

    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Blogs?',
        'description' => 'Do you want to let members create blogs? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view blogs, but only want certain levels to be able to create blogs.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of blogs.',
          0 => 'No, do not allow blogs to be created.'
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Blogs?',
        'description' => 'Do you want to let members edit blogs? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all blogs.',
          1 => 'Yes, allow members to edit their own blogs.',
          0 => 'No, do not allow members to edit their blogs.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Blogs?',
        'description' => 'Do you want to let members delete blogs? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all blogs.',
          1 => 'Yes, allow members to delete their own blogs.',
          0 => 'No, do not allow members to delete their blogs.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Blogs?',
        'description' => 'Do you want to let members of this level comment on blogs?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all blogs, including private ones.',
          1 => 'Yes, allow members to comment on blogs.',
          0 => 'No, do not allow members to comment on blogs.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }


      // Element: watermark
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
//New File System Code
$banner_options = array('' => '');
$files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('gif', 'jpg', 'jpeg', 'png')));
foreach( $files as $file ) {
  $banner_options[$file->storage_path] = $file->name;
}
			$fileLink = $view->baseUrl() . '/admin/files/';
			if (count($banner_options) > 1) {
				$this->addElement('Select', 'watermark', array(
						'label' => 'Add Watermark to Main Photos',
						'description' => 'Choose a photo which you want to be added as watermark on the main photos upload by the members of this level on your website.',
						'multiOptions' => $banner_options,
				));
			} else {
				$description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo for watermark. Photo to be chosen for watermark should be first uploaded from the "Layout" >> "<a href="' . $fileLink . '" target="_blank">File & Media Manager</a>" section.') . "</span></div>";
				//Add Element: Dummy
				$this->addElement('Dummy', 'watermark', array(
						'label' => 'Add Watermark to Main Photos',
						'description' => $description,
				));
				$this->watermark->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      }
  
			 // Element: thumb watermark
			if (count($banner_options) > 1) {
				$this->addElement('Select', 'watermarkthumb', array(
						'label' => 'Add Watermark to Thumb Photos',
						'description' => 'Choose a photo which you want to be added as watermark on the thumb photos upload by the members of this level on your website.',
						'multiOptions' => $banner_options,
				));
			} else {
				$description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo for watermark. Photo to be chosen for watermark should be first uploaded from the "Layout" >> "<a href="' . $fileLink . '" target="_blank">File & Media Manager</a>" section.') . "</span></div>";
				//Add Element: Dummy
				$this->addElement('Dummy', 'watermarkthumb', array(
						'label' => 'Add Watermark to Thumb Photos',
						'description' => $description,
				));
				$this->watermarkthumb->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
		}


      //element for event approve
      $this->addElement('Radio', 'blog_approve', array(
        'description' => 'Do you want blogs created by members of this level to be auto-approved?',
        'label' => 'Auto Approve Blogs',
        'multiOptions' => array(
            1=>'Yes, auto-approve blogs.',
            0=>'No, do not auto-approve blogs.'
        ),
        'value' => 1,
       ));


      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Blog Privacy',
        'description' => 'Your members can choose from any of the options checked below when they decide who can see their blog entries. These options appear on your members\' "Add Entry" and "Edit Entry" pages. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner','registered'),
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Blog Comment Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their entries. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner','registered'),
      ));

			$this->addElement('Radio', 'cotinuereading', array(
        'label' => 'Allow to Enable "Continue Reading" Button',
        'description' => 'Do you want to allow members to enable "Continue Reading" button for their Blogs on your website? If you choose Yes, then a Continue Reading button will be shown on Blog view page to read the full blog.',
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
				'onchange' => 'continuereadingbutton(this.value)',
        'value' => '1',
      ));
			$this->addElement('Radio', 'cntrdng_dflt', array(
        'label' => 'Default "Continue Reading" Button',
        'description' => 'Do you want to enable "Continue Reading" button for Blogs on your website for the blogs created by members of this level?',
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'onchange' => 'showHideHeight(this.value)',
        'value' => '1',
      ));

      $this->addElement('Text', 'continue_height', array(
        'label' => 'Enter Truncation limit',
        'description' => 'Enter the truncation limit after you want to show continue reading button. 0 for unlimited.',
        'value' => '0'
      ));

      // Element: auth_html
      $this->addElement('Text', 'auth_html', array(
        'label' => 'HTML in Blog Entries?',
        'description' => 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
        'value' => 'script[language|type|src|id],strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, iframe',
      ));

      $this->addElement('Radio', 'allow_claim', array(
          'label' => 'Allow Claim in Blogs',
          'description' => 'Do you want to let members claim in blogs?',
          'multiOptions' => array(
              1 => 'Yes, allow members to claim blogs.',
              0 => 'No, do not allow members to claim blogs.'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'allow_levels', array(
          'label' => 'Allow to choose "Blog View Privacy Based on Member Levels"',
          'description' => 'Do you want to allow the members of this level to choose View privacy of their Pages based on Member Levels on your website? If you choose Yes, then users will be able to choose the visibility of their Pages to members of selected member levels only.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No',
          ),
          'value' => 0,
      ));

      $this->addElement('Radio', 'allow_network', array(
          'label' => 'Allow to choose "Blog View Privacy Based on Networks"',
          'description' => 'Do you want to allow the members of this level to choose View privacy of their Pages based on Networks on your website? If you choose Yes, then users will be able to choose the visibility of their Pages to members who have joined selected networks only.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No',
          ),
          'value' => 0,
      ));



      // Element: max
      $this->addElement('Text', 'max', array(
        'label' => 'Maximum Allowed Blog Entries?',
        'description' => 'Enter the maximum number of allowed blog entries. The field must contain an integer between 1 and 999, or 0 for unlimited.',
        'required'=>'true',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      ));

      $this->addElement('radio', 'sesblog_endes', array(
        'label' => 'Enable Blog Profile Views',
        'description' => 'Do you want to enable users to choose views for their Blogs? (If you choose No, then you can choose a default layout for the Blog Profile pages on your website.)',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'onchange' => "enablsesblogdesignview()",
        'value' => 1,
      ));

      $this->addElement('MultiCheckbox', 'sesblog_cholay', array(
        'label' => 'Choose Blog Profile Pages',
        'description' => 'Choose layout for the blog profile pages which will be available to users while creating or editing their blogs.',
        'multiOptions' => array(
          1 => 'Design 1',
          2 => 'Design 2',
          3 => 'Design 3',
          4 => 'Design 4',
        ),
        'value' => array('1', '2', '3', '4'),
      ));

      $this->addElement('Radio', 'sesblog_deflay', array(
        'label' => 'Default Blog Profile Page',
        'description' => 'Choose default layout for the blog profile pages.',
        'multiOptions' => array(
          1 => 'Design 1',
          2 => 'Design 2',
          3 => 'Design 3',
          4 => 'Design 4',
        ),
        'value' => 1,
      ));

      $this->addElement('radio', 'autofeatured', array(
        'label' => 'Automatically Mark Blogs as Featured',
        'description' => 'Do you want Blogs created by members of this level to be automatically marked as Featured? If you choose No, then you can manually mark Blogs as Featured from Manage Blogs section of this plugin.',
        'multiOptions' => array(
          1 => 'Yes, automatically mark Blogs as Featured',
          0 => 'No, do not automatically mark Blogs as Featured.',
        ),
        'value' => 0,
      ));

      $this->addElement('radio', 'autosponsored', array(
        'label' => 'Automatically Mark Blogs as Sponsored',
        'description' => 'Do you want Blogs created by members of this level to be automatically marked as Sponsored? If you choose No, then you can manually mark Blogs as Sponsored from Manage Blogs section of this plugin.',
        'multiOptions' => array(
          1 => 'Yes, automatically mark Blogs as Sponsored.',
          0 => 'No, do not automatically mark Blogs as Sponsored.',
        ),
        'value' => 0,
      ));

      $this->addElement('radio', 'autoverified', array(
        'label' => 'Automatically Mark Blogs as Verified',
        'description' => 'Do you want Blogs created by members of this level to be automatically marked as Verified? If you choose No, then you can manually mark Blogs as Verified from Manage Blogs section of this plugin.',
        'multiOptions' => array(
          1 => 'Yes, automatically mark Blogs as Verified.',
          0 => 'No, do not automatically mark Blogs as Verified.',
        ),
        'value' => 0,
      ));

      $this->addElement('radio', 'auth_changeowner', array(
        'label' => 'Allow to Transfer Ownership',
        'description' => 'Do you want to allow members of this level to transfer ownership of their Blogs to other members on your website? If you choose Yes, then members will be able to transfer ownership from dashboard of their Blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'value' => 1,
      ));

      $this->addElement('radio', 'blogrolesman', array(
        'label' => 'Allow to Manage Blog Roles',
        'description' => 'Do you want to allow members of this level to manage Roles in their Blogs on your website? If you choose Yes, then members will be able to manage Roles from dashboard of their Blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'value' => 1,
      ));

      $this->addElement('radio', 'contactinfo', array(
        'label' => 'Enable Contact Info',
        'description' => 'Do you want to enable the "Contact Info" functionality for the Blogs created by members of this member level? If you choose Yes, then members will be able to enter the contact details from the dashboard of their Blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'value' => 1,
      ));

      $this->addElement('radio', 'seofields', array(
        'label' => 'Enable SEO Fields',
        'description' => 'Do you want to enable the "SEO" fields for the Blogs created by members of this level? If you choose Yes, then members will be able to enter the details from the dashboard of their Blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'value' => 1,
      ));

      $this->addElement('radio', 'enablestyle', array(
        'label' => 'Enable Edit Style',
        'description' => 'Do you want to enable "Edit CSS Style" for the Blogs created by members of this level? If you choose Yes, then members will be able to edit the CSS Style from dashboard of their Blogs.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
        ),
        'value' => 1,
      ));
    }
  }
}
