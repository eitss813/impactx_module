<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function faq_show(id) {
    if ($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I do not want my member to update reviews once they have given review to any member. How can I do so?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: To do so, please go to the ‘Reviews & Ratings’ >> ‘Member Level Settings’ section available in the admin panel of this plugin. Now select ‘No, do not allow members to update their reviews’ for ‘Allow Updating of Reviews?’ setting. You can configure this setting for various member level by choosing the appropriate member type from the drop-down."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I want site members to be visible according to the viewers network. Is it possible with this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate('Ans: Yes, you can do so by enabling ‘Browse By Network’ settings from the Global Settings section available in the admin panel of this plugin.'); ?>
      </div>
    </li>
    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I want to display site members images in circular shape throughout my website. Is it possible with this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans: Yes, you can display the members images in circular shape throughout your website by just adding few lines of customization code provided below. Follow the below steps to do this: <br /> 
        1) Open the theme editor <br /> 
2) Open the customization.css. Add the below code in this file: <br /> 
/* Global member photos and icons */ <br /> 
img.thumb_icon,.suggestion_list .item_photo img <br /> 
{ <br /> 
  width: 48px; <br /> 
  height: 48px; <br /> 
  border-radius:50% !important; <br /> 
} <br /> 
/* For Info tooltip */ <br /> 
.info_tip_has_cover .tip_main_photo, .info_tip_has_cover .tip_main_photo img{<br />border-radius:50% !important;<br />}<br />
/* Member profile photo */ <br /> 
 .layout_siteusercoverphoto_user_cover_photo .seaocore_profile_main_photo {<br />
    border-radius: 50%;<br />
}"); ?><br />
3) Save the changes.
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate('I want to delete some members from my site, but I am unable to find a way for that option. How will I be able to do so?'); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate("Ans: You need to go to the ‘Manage’ >> ‘Members’ section available in the main navigation bar of the admin panel. You will be able to manage your site members of your site from here. Also, you will see a ‘click here’ link on the ‘View Members’ section available in the admin panel of this plugin. You can click here to go to the members section from here immediately."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("How can I rearrange the Fields shown to users while the sign up process?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate('Ans: Please go to the ‘Settings’ >> ‘Profile Questions’ available in the main navigation bar of the admin panel. There, you can drag-and-drop the profile Questions vertically and save the sequence as per your requirement.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I have added location while sign up but when I click on edit location, it shown me ‘No location has been added’. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: It is happening because you have not synchronized the member location (Profile Question created by you) entered while sign up process with Profile Type. To do so please follow the below steps:<br />1. Go to ‘Admin Panel’ >> ‘Settings’ >> ‘Profile Questions’. Now create Profile Type ‘Location / City / Country’ using ‘Add a Question’ link.<br />2. Now go to Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin >> ‘Global Settings’ >> ‘Profile Type - Location Field Mapping(setting)’, now use click here link to map the created Profile Type.<br />3. Add Profile Question using add link for the various Profile Types created by you."); ?>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("How can I see Members according to my location in various widgets on Members Home, Browse Members, etc pages?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: If you want to see members based on your location, then simply choose ‘Yes’ option for “Do you want to display member based on user’s current location?” setting."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want only some specific locations to be visible on my site and in various widgets for browsing and searching. Is it possible with this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate("Ans: Yes, please go to the ‘SocialEngineAddons Core Plugin’ >> ‘Location & Maps’ section available in the admin panel. Now enable ‘Enable Specific Locations’ setting and go to the ‘Manage Locations’ section to enter locations you want to enable for browsing and searching on your site and in various widgets. <br />
After enabling this setting you will be able to see those locations in the various widgets under the drop-down."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I want more fields to be visible in the info tooltip shown for the member. How can I do so?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate("Ans: Please go to ‘Admin Panel’ >> ‘SocialEngineAddons Core Plugin’ >> ‘Info Tooltip Settings [
Dependent on our <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>Advanced Activity Feeds / Wall Plugin</a>]’. Now configure the given settings according to you and click on ‘save changes’."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I do not want to show some fields in the search box to the users. How can I do this?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate("Ans: Go to the 'Search Form Settings' section in the admin panel of this plugin and click on the Hide / Display icon for such fields. Fields which have been made hidden here would not be shown in the search box placed at various pages. You can also display them later if you want."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("I want users to see particular location in the location field in various widget placed on Browse Member page. Does this plugin support this functionality?"); ?></a>
      <div class='faq' style='display: none;' id='faq_11'>
        <?php echo $this->translate("Ans: Yes, you can do this by saving the location (for example: Canada) for the “Default Location for content searching” field available in the ‘SocialEngineAddons Core Plugin’ >> ‘Location & Maps’ section [Please ensure that ‘Enable Specific Locations’ is set to NO]."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("I want to display suggestion to users regarding the people they may know, so that they can add them as their friend from “Add Friend” link immediately from there."); ?></a>
      <div class='faq' style='display: none;' id='faq_12'>
        <?php echo $this->translate("Ans: You can do this by placing our ‘People You May Know’ widget [Dependent on our “<a href='http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin' target='_blank'>Suggestions / Recommendations / People you may know & Inviter</a>” or “<a href='http://www.socialengineaddons.com/socialengine-people-you-may-know-friend-suggestions-inviter' traget='_blank'>People you may know / Friend Suggestions & Inviter</a>” plugins]. Members will be able to see suggestions regarding the people they may know and will be able to add them from this widget itself.<br /> You can also use our “Profile Mutual Friends” widget for showcasing common friends of the viewer and the member being currently viewed on member profile page for enhancing the member browsing experience."); ?>
      </div>
    </li>
        <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("After installing this plugin, users on my site can now enter locations from their 'Edit My Profile' page and 'Edit My Location' page. Which location will be associated with users’ Profiles to be searched in the 'Members Location & Proximity Based Search'?"); ?></a>
      <div class='faq' style='display: none;' id='faq_13'>
        <?php echo $this->translate("The location entered from the 'Edit My Location' page will always be associated with the profiles of members on your site whereas the location entered from the 'Edit My Profile' page will be associated, if they have entered their location in the “Location” type field mapped by you, from the 'Profile Type - Location Field Mapping' field in the 'Global Settings' section of this plugin. Both these locations will be synced with each other.<br />If you change a mapping, then the new location entered in the newly mapped “Location” type field will be synced when a user edits their location from any of the above mentioned 2 pages."); ?>
      </div>
    </li>
     
    
  </ul>
</div>
