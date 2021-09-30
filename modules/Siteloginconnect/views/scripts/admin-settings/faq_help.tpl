<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecredit
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>

<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_99');"><?php echo $this->translate("Is this a stand-alone plugin?"); ?></a>
            <div class='faq' style='display: none;' id='faq_99'>
                <?php echo $this->translate('Ans: No, <a href="https://www.socialengineaddons.com/socialengine-social-login-signup-plugin" target="_blank">Social Login & Sign-up plugin</a> is required to run this plugin.<br />
                  '); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_98');"><?php echo $this->translate("Which Social sites are supported by this plugin?"); ?></a>
            <div class='faq' style='display: none;' id='faq_98'>
                <?php echo $this->translate("Ans: The following social sites can be used for the login feature i.e. users would be able to use their existing accounts of the following sites to login to your site: Twitter, Facebook, Google, Instagram, LinkedIn, Pinterest, Yahoo, Flickr, Outlook and Vk.<br/>
                    And the following sites can be used for synchronization of profile information i.e. the users of your sites can sync their profiles with their existing profiles on the following social sites: Facebook, Twitter, Instagram and LinkedIn.
                    <br/>
                  "); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_97');"><?php echo $this->translate("How can I synchronize the profile fields of different sites?"); ?></a>
            <div class='faq' style='display: none;' id='faq_97'>
                <?php echo $this->translate("Ans: You can map the profile fields of your site with the required parameters returning from other social sites that you’ve selected for Profile Synchronization feature from the Profile Fields Mapping section. So, then the users of your site would be able to choose any social site out of the social sites selected by you for synchronization and can fetch their profile information from any other social sites and if they want they can submit the information fetched, so that their profiles on your site can be synchronized with that fetched data (i.e. with their other existing profiles on other social sites).<br/>
                  "); ?>
            </div>
        </li>   
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("What will happen when user will update the profile fields on my site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_1'>
                <?php echo $this->translate("Ans: When a user will update his profile information on your SocialEngine site from his Profile info section, then the information updated by them would be saved and any previous information that was synced with his profile on any other social site would be lost.<br/>"); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("How the regular updates / syncs will be done?"); ?></a>
            <div class='faq' style='display: none;' id='faq_2'>
                <?php echo $this->translate('Ans: The profile synchronization is not done automatically at regular intervals, a user needs to sync it from “Synchronize Profile Data” section from his Member Settings page.<br/>
                '); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("What happens if a users updated his profile information on any other social site with which he has synced his profile on your site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_3'>
                <?php echo $this->translate('Ans: No changes would be done in his profile on your site unless the user syncs his profile with that social site again and submit the new updated profile data. That is synchronization needs to be done manually, profile info is not auto-synced without user’s permission.<br/>'); ?>
                </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("What if a user updates his profile photo on other social site with which he has synced his profile on your site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_4'>
                <?php echo $this->translate("Ans: Profile photo of the user will not be updated on your site by itself, it will be updated only when the user sync his profile again and submit the new profile pic fetched."); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Can a user sync his profile with more than one social site at a time?"); ?></a>
            <div class='faq' style='display: none;' id='faq_5'>
                <?php echo $this->translate("Ans: Yes, user can sync different profile fields of your SocialEngine site with different fields returning from the other social sites. So, for example, it may be that he syncs one profile question (say first name) with a value returning from Facebook and the other question (say last name) with a value returning from LinkedIn."); ?>

            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_55');"><?php echo $this->translate("Can nested Profile fields also be mapped with information fetched from other social sites?"); ?></a>
            <div class='faq' style='display: none;' id='faq_55'>
                <?php echo $this->translate("Ans: Yes, you can map nested profile fields too with values returning from other social sites. The nested profile field will be shown with all the sub-options in the Profile Fields Mapping section, you can select the required parameters from the respective drop-downs to map them with the sub-options."); ?>

            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Do I need to make new developer apps for the working of this plugin?"); ?></a>
            <div class='faq' style='display: none;' id='faq_6'>
                <?php echo $this->translate('Ans: No, you do not need to make any new apps, you just need to edit (add some new redirect URLs) the existing apps that you would have created while integrating the <a href="https://www.socialengineaddons.com/socialengine-social-login-signup-plugin" target="_blank">Social Login and Sign-up plugin</a> on your site. And if you have not yet made any third party apps for your site, then please refer to the Admin panel <a href="admin/sitelogin/settings/appfaq" target="_blank">FAQ</a> section of Social Login and Sign-up plugin to know how to create the apps. But if you have already configured apps for your site, then you just need to do below changes in your developer apps: <br/>
                    <br/>
                    What changes I need to do in my Google app?
                    <br/>
                    Follow the below steps:<br/>
- Go to <a href="https://console.developers.google.com/apis/library" target="_blank">https://console.developers.google.com/apis/library</a> and sign in with your Google account.<br/>
- Open your specific project and go to section “APIs & Services” > “Credentials” and then edit the respective webclient for your site - Add a new redirect url. Format for Authorized redirect URLs <br/>
"https://www.example.com/siteloginconnect/link/google?google_connected=1"
<br/><br/>
What changes I need to do in my LinkedIn app?<br/>
Follow the below steps:<br/>
- Go to <a href="https://www.linkedin.com/secure/developer?newapp=" target="_blank">https://www.linkedin.com/secure/developer?newapp=</a> and sign in with your LinkedIn account.<br/>
- Go to Your application settings. - Please add a new url in the Authorized redirect URL in OAuth 2.0 column.<br/>
-Format for Authorized Redirect URL<br/>
"https://www.example.com/siteloginconnect/link/linkedin" and "https://www.example.com/siteloginconnect/sync/linkedin" and then Update.

<br/><br/>
What changes I need to do in my Facebook app?<br/>
Follow the below steps:<br/>
-A redirect URL should be added to your Facebook login settings having below format 
             “https://www.example.com/user/auth/facebook”
<br/><br/>
What changes I need to do in my Twitter App?<br/>
Follow the below steps:<br/>
- Go to <a href="https://dev.twitter.com/" target="_blank">https://dev.twitter.com/</a><br/>
- Click on ‘My apps’ and sign in with your account credentials.<br/>
- Go to your respective application settings.<br/>
- Add new callback url and save. <br/>
- Format for callback url: "https:/www.example.com/siteloginconnect/link/twitter" and 
"https:/www.example.com/siteloginconnect/sync/twitter"
<br/><br/>
What changes I need to do in my Instagram App?<br/>
Follow the below steps:<br/>
- Go to <a href="https://www.instagram.com/developer/" target="_blank">https://www.instagram.com/developer/</a> and sign in with your account details.<br/>
- Add new redirect Urls. Format for redirect url is: "https://www.example.com/siteloginconnect/link/instagram" and "https://www.example.com/siteloginconnect/sync/instagram".<br/>
- Save changes.
<br/><br/>
What changes I need to do in my Yahoo App?<br/>
No changes needed for Yahoo App.
<br/><br/>
What changes I need to do in my Pinterest App?<br/>
Follow the below steps:<br/>
- Go to <a href="https://developers.pinterest.com/" target="_blank">https://developers.pinterest.com/</a> and sign in with your account.<br/>
- Go to the ‘Apps’ in the main menu.<br/>
- Add the new redirect url and Save.<br/>
- Format for redirect url: "https://www.example.com/siteloginconnect/link/pinterest" .<br/>
[Note: Site URL must use the HTTPS protocols.]
<br/><br/>
What changes I need to do in my Flickr App?<br/>
Follow the below steps:<br/>
- Go to <a href="https://www.flickr.com/services/developer/api/" target="_blank">https://www.flickr.com/services/developer/api/</a> and login with your account details.<br/>
- Go to the ‘App Garden’.<br/>
- Add new redirect URL and Save.<br/>
- Format for the Redirect URL: "https://www.example.com/siteloginconnect/link/flickr"

<br/><br/>
What changes I need to do in my Vkontakte App?<br/>
 Follow the below steps:<br/>
- Go to <a href="https://vk.com/dev" target="_blank">https://vk.com/dev</a> and login with your account details.<br/>
- Then click on ‘My apps’ and choose your respective app.<br/>
- Add new redirect url with existing ones and save the changes.<br/>
- Format for redirect url is: "https://www.example.com/siteloginconnect/link/vk"
<br/><br/>
What changes I need to do in my Outlook App?<br/>
Follow the below steps:<br/>
- Go to <a href="https://apps.dev.microsoft.com/#/appList" target="_blank">https://apps.dev.microsoft.com/#/appList</a> and login with your account credentials.<br/>
- Go to respective App settings.<br/>
- Add redirect url.Format for the redirect url: <br/>"https://www.example.com/siteloginconnect/link/outlook".<br/>
[Note: Site URL must use the HTTPS protocols.]<br/>
- Add the profile urls also and save the changes<br/>
<br/>
'
                ); ?>

            </div>
        </li>
    </ul>
</div>

        