<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Member Level Settings')
            ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below. (Note: If packages are enabled from global settings, then some member level settings will not be available as those feature settings for pages will now depend on packages.)");

    $isEnabledPackage = Engine_Api::_()->sitepage()->hasPackageEnable();

    // Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Pages?',
        'description' => 'Do you want to let members view pages? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all pages, even private ones.',
            1 => 'Yes, allow viewing of pages.',
            0 => 'No, do not allow pages to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {
      // check extension installed or not
      $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
      if ($featureExtension) {
        // Element: add a button
        $this->addElement('Radio', 'add_button', array(
            'label' => 'Allow Users to add button on their page?',
            'description' => 'Do you want to let members to create a button on their page?',
            'multiOptions' => array(
                1 => 'Yes, allow creation of button.',
                0 => 'No, do not allow button to be created.'
            ),
            'value' => 1,
        ));

        $this->addElement('Radio', 'service', array(
            'label' => 'Page Services Moderation',
            'description' => 'Allow members of this level to create Services for their pages.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }
      // Element: create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Pages?',
          'description' => 'Do you want to let members create pages? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view pages, but only certain levels to be able to create pages.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of pages.',
              0 => 'No, do not allow pages to be created.'
          ),
          'value' => 1,
      ));

      // Element: verify page
      $this->addElement('Radio', 'page_verify', array(
          'label' => 'Allow Verification of Pages?',
          'description' => 'Do you want to let members of this level to verify pages?',
          'multiOptions' => array(
              1 => 'Yes, allow verification of pages.',
              0 => 'No, do not allow pages to be verified.'
          ),
          'value' => 0,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Pages?',
          'description' => 'Do you want to let members edit pages? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all pages.',
              1 => 'Yes, allow members to edit their own pages.',
              0 => 'No, do not allow members to edit their pages.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Pages?',
          'description' => 'Do you want to let members delete pages? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all pages.',
              1 => 'Yes, allow members to delete their own pages.',
              0 => 'No, do not allow members to delete their pages.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
          'label' => 'Allow Commenting on Pages?',
          'description' => 'Do you want to let members of this level comment on pages?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all pages, including private ones.',
              1 => 'Yes, allow members to comment on pages.',
              0 => 'No, do not allow members to comment on pages.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1)) {
        $ownerTitle = "Page Admins";
      } else {
        $ownerTitle = "Just Me";
      }

      $privacyArray = array(
          'everyone' => 'Everyone',
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
              //	'owner' => $ownerTitle,
      );
      $privacyValueArray = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if ($sitepagememberEnabled) {
        $privacyArray['member'] = 'Page Members Only';
        $privacyValueArray[] = 'member';
      }
      $privacyArray['owner'] = $ownerTitle;
      $privacyValueArray[] = 'owner';

      //START SUBPAGE WORK.

      // Element:sub create
      $this->addElement('Radio', 'sspcreate', array(
          'label' => 'Allow Creation of Sub Pages?',
          'description' => 'Do you want to let members create sub pages? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to create sub pages, but only certain levels to be able to create sub pages.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of sub pages.',
              0 => 'No, do not allow sub pages to be created.'
          ),
          'value' => 1,
      ));

      $privacy_array = array(
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
              //	'owner' => $ownerTitle,
      );
      $privacy_value_array = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if ($sitepagememberEnabled) {
        $privacy_array['member'] = 'Page Members Only';
        $privacy_value_array[] = 'member';
      }
      $privacy_array['like_member'] = 'Who Liked This Page';
      $privacy_value_array[] = 'like_member';

      $privacy_array['owner'] = $ownerTitle;
      $privacy_value_array[] = 'owner';

      // Element: auth_subpage create
      $this->addElement('MultiCheckbox', 'auth_sspcreate', array(
          'label' => 'Sub-Page Creation Options',
          'description' => 'Your users can choose from any of the options checked below when they decide who can create the sub-pages in their pages. If you do not check any options, everyone will be allowed to create sub-pages.',
          'multiOptions' => $privacy_array
      ));
      //Element: subpage


      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
          'label' => 'Page Privacy',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their pages. These options appear on your members\' "Create New Page" and "Edit Page" pages. If you do not check any options, everyone will be allowed to view pages.',
          'multiOptions' => $privacyArray,
          'value' => $privacyValueArray
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
          'label' => 'Page Comment Options',
          'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their pages. These options appear on your members\' "Create New Page" and "Edit Page" pages. If you do not check any options, everyone will be allowed to post comments on pages.',
          'multiOptions' => $privacyArray,
          'value' => $privacyValueArray
      ));
    }

    if (!$this->isPublic() && empty($isEnabledPackage)) {

      $privacy_array = array(
          'registered' => 'All Registered Members',
          'owner_network' => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member' => 'Friends Only',
              //	'owner' => $ownerTitle,
      );
      $privacy_value_array = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if ($sitepagememberEnabled) {
        $privacy_array['member'] = 'Page Members Only';
        $privacy_value_array[] = 'member';
      }
      $privacy_array['like_member'] = 'Who Liked This Page';
      $privacy_value_array[] = 'like_member';

      $privacy_array['owner'] = $ownerTitle;
      $privacy_value_array[] = 'owner';


      //Element: approved
      $this->addElement('Radio', 'approved', array(
          'label' => 'Page Approval Moderation',
          'description' => 'Do you want new page to be automatically approved?',
          'multiOptions' => array(
              1 => 'Yes, automatically approve page.',
              0 => 'No, site admin approval will be required for all pages.'
          ),
          'value' => 1,
      ));

      //Element: sponsored
      $this->addElement('Radio', 'sponsored', array(
          'label' => 'Page Sponsored Moderation',
          'description' => 'Do you want new page to be automatically made sponsored?',
          'multiOptions' => array(
              1 => 'Yes, automatically make page sponsored.',
              0 => 'No, site admin will be making page sponsored.'
          ),
          'value' => 0,
      ));
      //Element: featured
      $this->addElement('Radio', 'featured', array(
          'label' => 'Page Featured Moderation',
          'description' => 'Do you want new page to be automatically made featured?',
          'multiOptions' => array(
              1 => 'Yes, automatically make page featured.',
              0 => 'No, site admin will be making page featured.'
          ),
          'value' => 0,
      ));

      $this->addElement('Radio', 'tfriend', array(
          'label' => 'Tell a friend',
          'description' => 'Do you want to show "Tell a friend" link on the Profile Page of pages created by members of this level? (Using this feature, viewers will be able to email the page to their friends.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'print', array(
          'label' => 'Print',
          'description' => 'Do you want to show "Print Page" link on the Profile Page of pages created by members of this level? (If set to no, viewers will not to be able to print information of the pages.)',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));


      $this->addElement('Radio', 'overview', array(
          'label' => 'Overview',
          'description' => 'Do you want to enable Overview for pages created by members of this level? (If set to no, neither the overview widget will be shown on the Page Profile nor members will be able to compose or edit the overview of their pages.)',
          'multiOptions' => array(
              //2 => 'Yes, show overview of the pages, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'map', array(
          'label' => 'Location Map',
          'description' => 'Do you want to enable Location Map for pages created by members of this level? (If set to no, neither the map widget will be shown on the Page Profile nor members will be able to specify location of their pages to be shown in the map.)',
          'multiOptions' => array(
              //2 => 'Yes show map of the pages, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));


      $this->addElement('Radio', 'insight', array(
          'label' => 'Insights',
          'description' => 'Do you want to allow members of this level to view insights of their pages? (Insights for pages show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. If set to no, neither insights will be shown nor the periodic, auto-generated emails containing Page insights will be send to the page admins who belong to this level.)',
          'multiOptions' => array(
              //2 => 'Yes, allow them to view the insights of the pages, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));


      $this->addElement('Radio', 'contact', array(
          'label' => 'Contact Details',
          'description' => 'Do you want to enable Contact Details for the pages created by members of this level? (If set to no, neither the contact details will be shown on the info and browse pages nor members will be able to mention them for their pages\' entity.)',
          'multiOptions' => array(
              //2 => 'Yes, enable contact details for the pages, including private ones.',
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'contactoption(this.value)',
          //'value' => ( $this->isModerator() ? 2 : 1 ),
          'value' => 1,
      ));


      $this->addElement('MultiCheckbox', 'contact_detail', array(
          'label' => 'Specific Contact Details',
          'description' => 'Which of the following contact details you want to be specified by members of this level in the "Contact Details" section of the Page Dashboard?',
          'multiOptions' => array(
              'phone' => 'Phone',
              'website' => 'Website',
              'email' => 'Email',
          ),
          'value' => array('phone', 'website', 'email')
      ));

//      $this->addElement('Radio', 'foursquare', array(
//          'label' => 'Save To Foursquare Button',
//          'description' => "Do you want to enable 'Save to foursquare' buttons for the pages created by members of this level? (Using this, 'Save to foursquare' buttons will be shown on profiles of pages having location information. These buttons will enable page visitors to add the page's place or tip to their foursquare To-Do List. Page Admins will get this option in the \"Marketing\" section of their Dashboard.)",
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          'value' => 1,
//      ));
      // Element:Twitter
      $sitepagetwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter');
      if ($sitepagetwitterEnabled) {
        $this->addElement('Radio', 'twitter', array(
            'label' => 'Display Twitter Updates',
            'description' => "Enable displaying of Twitter Updates for pages of this package. (Using this, page admins will be able to display their Twitter Updates on their Page profile. Page Admins will get the option for entering their Twitter username in the \"Marketing\" section of their Dashboard. From the Layout Editor, you can choose to place the Twitter Updates widget either in the Tabs container or in the sidebar on Page Profile.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }

      $this->addElement('Radio', 'sendupdate', array(
          'label' => 'Send an Update',
          'description' => "Do you want to enable 'Send an Update' for the pages created by members of this level? (Using this, page admins will be able to send an update for their pages' entity. Page Admins will get this option in the \"Marketing\" section of their Dashboard.)",
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));
      $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagelikebox');
      if ($sitepageFormEnabled) {
        $this->addElement('Radio', 'likebox', array(
            'label' => 'External Embeddable Badge / Like Box',
            'description' => "Do you want page admins to be able to generate code for Embeddable Badges / Like Boxes for pages created by a member of this level? (If enabled, page admins of such pages will be able to generate code to embed their external page badges in other websites / blogs to promote their page from Marketing section of page dashboard. Page Admins will also have to belong to this member level to generate code.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }
      //START SITEPAGEBADGES PLUGIN WORK
      $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge');
      if ($sitepageFormEnabled) {
        $this->addElement('Radio', 'badge', array(
            'label' => 'Badge Requesting',
            'description' => 'Do you want page admins to be able to request a badge for their page created by a member of this level? (If enabled, page admins of such pages will be able to request a badge from their page dashboard. You will be able to manage badge requests and assign badges from the admin panel of Badges Extension. Page Admins will also have to belong to this member level to request a badge.)',
            'multiOptions' => array(
                //2 => 'Yes, Private ones also',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));
      }

      //START SITEPAGEDOCUMENT PLUGIN WORK
      $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
      if ((Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || $sitepageDocumentEnabled) {
        $this->addElement('Radio', 'sdcreate', array(
            'label' => 'Documents in Pages',
            'description' => 'Do you want Documents to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Documents in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create documents in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_sdcreate', array(
            'label' => 'Document Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the documents in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITEPAGEDOCUMENT PLUGIN WORK
      //START SITEPAGEEVENT PLUGIN WORK
			if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
        $this->addElement('Radio', 'secreate', array(
            'label' => 'Events in Pages',
            'description' => 'Do you want Events to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Events in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create events in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        //START SITEPAGEEVENT PLUGIN WORK
        $this->addElement('MultiCheckbox', 'auth_secreate', array(
            'label' => 'Event Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the events in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle
//                 )
        ));
      }
      //END SITEPAGEEVENT PLUGIN WORK
      //START SITEPAGEOFFER PLUGIN WORK
      $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
      if ($sitepageFormEnabled) {
        $this->addElement('Radio', 'form', array(
            'label' => 'Form',
            'description' => 'Do you want Forms to be available to Pages created by members of this level? (The Form on a Page will contain questions added by page admins. If set to No, neither the form widget will be shown on the Page Profile nor the page admins will be able to add questions to the Form from Page Dashboard. Page Admins will also have to belong to this member level to manage form.)',
            'multiOptions' => array(
                //2 => 'Yes, Private ones also',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));
      }
      //END SITEPAGEOFFER PLUGIN WORK
      //START SITEPAGEINVITE PLUGIN WORK
      $sitepageInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite');
      if ($sitepageInviteEnabled) {
        $this->addElement('Radio', 'invite', array(
            'label' => 'Invite & Promote',
            'description' => 'Do you want members of this level to be able to invite their friends to the pages? (If set to no, "Invite your Friends" link will not appear on the Page Profile of their pages.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
      }
      //END SITEPAGEINVITE PLUGIN WORK
      //START SITEPAGENOTE PLUGIN WORK
      $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
      if ($sitepageNoteEnabled) {
        $this->addElement('Radio', 'sncreate', array(
            'label' => 'Notes in Pages',
            'description' => 'Do you want Notes to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Notes in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create notes in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));


        $this->addElement('MultiCheckbox', 'auth_sncreate', array(
            'label' => 'Note Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the notes in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITEPAGENOTE PLUGIN WORK
      //START SITEPAGEOFFER PLUGIN WORK
      $sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
      if ($sitepageOfferEnabled) {
        $this->addElement('Radio', 'offer', array(
            'label' => 'Offer',
            'description' => 'Do you want to let members of this level to show offers for their pages? (If set to no, neither the offer widget will be shown on their Page Profiles nor they will be able to create them for their pages.)',
            'multiOptions' => array(
                //2 => 'Yes, allow them to create offers in the pages, including private ones.',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));
      }
      //END SITEPAGEOFFER PLUGIN WORK

       //START DISCUSSION PRIVACY WORK
      $sitepageDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
      if ($sitepageDiscussionEnabled) {
        $this->addElement('Radio', 'sdicreate', array(
            'label' => 'Discussion Topics in Pages',
            'description' => 'Do you want Discussion Topics to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to post discussion topics in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow photo uploading to all pages, including private ones.',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_sdicreate', array(
            'label' => 'Discussion Topics Post Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can post the discussion topics in their page. If you do not check any options, everyone will be allowed to post.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END DISCUSSION PRIVACY WORK

      //START PHOTO PRIVACY WORK
      $sitepageAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
      if ($sitepageAlbumEnabled) {
        $this->addElement('Radio', 'spcreate', array(
            'label' => 'Photos in Pages',
            'description' => 'Do you want Photos to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to upload Photos in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow photo uploading to all pages, including private ones.',
                1 => 'Yes',
                0 => 'No'
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));


        $this->addElement('MultiCheckbox', 'auth_spcreate', array(
            'label' => 'Photo Upload Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can upload the photos in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END PHOTO PRIVACY WORK
      //START SITEPAGEPOLL PLUGIN WORK
      $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
      if ($sitepagePollEnabled) {
        $this->addElement('Radio', 'splcreate', array(
            'label' => 'Polls in Pages',
            'description' => 'Do you want Polls to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Polls in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create polls in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_splcreate', array(
            'label' => 'Poll Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the polls in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITEPAGEPOLL PLUGIN WORK
      //START SITEPAGEVIDEO PLUGIN WORK
      $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
      if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) || $sitepageVideoEnabled) {
        $this->addElement('Radio', 'svcreate', array(
            'label' => 'Videos in Pages',
            'description' => 'Do you want Videos to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Videos in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create videos in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_svcreate', array(
            'label' => 'Video Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the videos in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));

        //END SITEPAGEVIDEO PLUGIN WORK

      }

      //START SITEPAGEMUSIC PLUGIN WORK
      $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
      if ($sitepageMusicEnabled) {
        $this->addElement('Radio', 'samcreate', array(
            'label' => 'Music in Pages',
            'description' => 'Do you want Music to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Music in Pages.',
            'multiOptions' => array(
                //2 => 'Yes, allow members to create notes in all pages, including private ones.',
                1 => 'Yes',
                0 => 'No',
            ),
            //'value' => ( $this->isModerator() ? 2 : 1 ),
            'value' => 1,
        ));


        $this->addElement('MultiCheckbox', 'auth_samcreate', array(
            'label' => 'Music Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the music in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
        ));
      }
      //END SITEPAGEMUSIC PLUGIN WORK

      //START SITEPAGEINTREGRATION PLUGIN WORK//
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
        $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems();
        foreach ($mixSettingsResults as $modNameValue) {

          $Params = Engine_Api::_()->sitepageintegration()->integrationParams($modNameValue['resource_type'], $modNameValue['listingtype_id'], '', $modNameValue['item_title']);

          $title = $Params['level_setting_title'];
          	$singular = $Params['singular'];
          	$plugin_name = $Params['plugin_name'];

					$description = 'Do you want to let members of this level to add ' . $singular . ' from "'.$plugin_name .'" to Directory Items / Pages? (If set to Yes, then page admins will get this option in the “Apps” section of their dashboard.)';

					$description = Zend_Registry::get('Zend_Translate')->_($description);

          $this->addElement('Radio', $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'], array(
              'label' => $title,
              'description' => $description,
              'multiOptions' => array(
                  1 => 'Yes',
                  0 => 'No',
              ),
              'value' => 1,
          ));
        }
      }
      //END SITEPAGEINTREGRATION PLUGIN WORK//

      //START SITEPAGEMEMBER PLUGIN WORK
      $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if ($sitepageMemberEnabled) {
        $this->addElement('Radio', 'smecreate', array(
            'label' => 'Member in Pages',
            'description' => 'Do you want Member to be available to Pages join by members of this level? This setting will also apply to ability of users of this level to join Member in Pages.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => 1,
        ));
      }
      //End SITEPAGEMEMBER PLUGIN WORK

       // Element : profile
        $this->addElement('Radio', 'profile', array(
            'label' => 'Profile Creation',
            'description' => 'Do you want members of this level to create profiles for their pages? (Using this feature, members will be able to create a profile for their Page and fill the corresponding details which will be displayed on info pages. If set to no, "Profile Types" link will not be shown on the Page Dashboard.)',
            'multiOptions' => array(
                '1' => 'Allow profile creation with all custom Fields.',
                '2' => 'Allow profile creation with only below selected custom Fields.',
                '0' => 'Do not allow the custom profile creation.',
            ),
            'value' => 1,
            'onclick' => 'showprofileOption(this.value)',
        ));

        //Add Dummy element for using the tables
        $this->addElement('Dummy', 'profilefield', array(
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_profilefield.tpl',
                        'class' => 'form element'
                )))
        ));
       //START SITECROWDFUNDING PLUGIN WORK
      $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
      if ($sitecrowdfundingEnabled && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
        $this->addElement('Radio', 'sprcreate', array(
            'label' => 'Allow Creation of Projects',
            'description' => 'Do you want Crowdfunding projects to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Projects in Pages.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_sprcreate', array(
            'label' => 'Crowdfunding Project Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the projects in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
        ));

        //END SITECROWDFUNDING PLUGIN WORK
      }

             //START SITEMUSIC PLUGIN WORK
      $sitemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic');
      if ($sitemusicEnabled && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
        $this->addElement('Radio', 'samcreate', array(
            'label' => 'Allow Creation of Music',
            'description' => 'Do you want Music Album to be available to Pages created by members of this level? This setting will also apply to ability of users of this level to create Music in Pages.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => 1,
        ));

        $this->addElement('MultiCheckbox', 'auth_samcreate', array(
            'label' => 'Music Album Creation Options',
            'description' => 'Your users can choose from any of the options checked below when they decide who can create the music album in their page. If you do not check any options, everyone will be allowed to create.',
            'multiOptions' => $privacy_array
        ));

        //END SITEMUSIC PLUGIN WORK
      }
    }



    if (!$this->isPublic()) {
      // Element: style
      $this->addElement('Radio', 'style', array(
          'label' => 'Allow Custom CSS Styles?',
          'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their pages by altering their CSS styles.',
          'multiOptions' => array(
              1 => 'Yes, enable custom CSS styles.',
              0 => 'No, disable custom CSS styles.',
          ),
          'value' => 1,
      ));
    }
    // Element: claim
    $this->addElement('Radio', 'claim', array(
        'label' => 'Claim Pages',
        'description' => 'Do you want members of this level to be able to claim pages? (This will also depend on other settings for claiming like in global settings, manage claims, setting while creation of page, etc.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 1,
    ));
    if (!$this->isPublic()) {
      // Element: max
      $this->addElement('Text', 'max', array(
          'label' => 'Maximum Allowed Pages',
          'description' => 'Enter the maximum number of directory items / pages that members of this level can create. This field must contain an integer; use zero for unlimited.',
          'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
          ),
      ));
    }

  }

}

?>
