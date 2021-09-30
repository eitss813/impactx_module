<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    faq.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<h2>
    <?php echo $this->translate("Social Login and Sign-up Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
    <?php endif; ?>
    <?php if( count($this->navigationSubMenu) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigationSubMenu)->render()
        ?>
      </div>

<?php endif; ?>
<script type="text/javascript">
    function faq_show(id) {
        if ($(id).style.display == 'block') {
            $(id).style.display = 'none';
        } else {
            $(id).style.display = 'block';
        }
    }
</script>
<div class="admin_sitelogin_files_wrapper">
    <ul class="admin_sitelogin_files sitelogin_faq">	    
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How do I generate my Google API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_1'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://console.developers.google.com/apis/library' target='_blank'>https://console.developers.google.com/apis/library</a> and signup with your google account.<br />
                - Click on ‘Select project’ and add a new project.<br />
                - Name the project and you can also edit the project id if in case you don’t want to keep the suggested one.<br />
                - Again click on ‘Select a project’ and choose the new project created by you.<br />
                - Go to the ‘Credentials’ on the left panel of your screen.<br />
                - To create an OAuth client ID, we must first set a product name on the consent screen. To do so, click on ‘OAuth Consent Screen’ and fill the required details.<br />
                - Now, click on ‘Create Credentials’ and select ‘OAuth client ID’.<br />
                - Select ‘Web Application’ in Application Type. Fill the required details like name of the web application and Authorized redirect URL.<br />
                - Format for Authorized redirect URLs <br />
\"https://www.example.com/sitelogin/auth/google?google_connected=1\" <br />
                - Format for Authorized JavaScript Origins<br />
\"https://www.example.com\"<br />
                - Open the Web Application you have created to get the ‘Client ID’ and ‘Client Secret Key’.<br />
               
                [You can refer this <a href='https://youtu.be/o5z71Ch-Qow' target='_blank'>video</a> for more details.]"); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("How do i generate my LinkedIn API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_2'>
                <?php echo $this->translate('Ans: Follow the below steps:<br/>
                - Go to <a href="https://www.linkedin.com/secure/developer?newapp=" target="_blank">https://www.linkedin.com/secure/developer?newapp=</a> and signin with your linkedIn account.<br />
                - Click on Create Application.<br />
                - Fill in the required details and submit.<br />
                - Once submitted you will be redirected to a page having Client ID and Client Secret key. Please copy these two credentials.<br />
                - Select the options : "r_basicprofile", "r_emailaddress".<br />
                - Please fill the Authorized redirect URL in OAuth 2.0 column.<br />
                  Format for <b>Authorized Redirect URL<br/>  "https://www.example.com/sitelogin/auth/linkedin"</b> and Update.<br />
                - Put the Client ID and API key in the admin panel of this plugin.<br />
                [You can refer this <a href="https://youtu.be/bMwPYUxXzcE" target="_blank">video</a> for more details.]'); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("How do I generate my Facebook API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_3'>
                <?php echo $this->translate('Ans:Follow the below steps:<br />
                - Go to <a href="https://developers.facebook.com/" target="_blank">https://developers.facebook.com/</a> and enter your account credentials.<br />
                - If you will be registered as a developer then you will see a Create App option at the right top corner otherwise a Register option will be there. For registration you need to fill a confirmation code send on the contact number provided by you.<br />
                - Enter the name for the app and email id.<br />
                - You will land on the add product page.<br />
                - Go to the app review tab and make your app public by selecting a category for your app.<br />
                - Go to the dashboard tab.<br />
                - Copy and save your App Id and App Secret Key.<br />
                - Go to the settings tab and add your domain name and the website url in the add platform section and save the changes.<br />
                - Format for the domain name: domainname.com<br/>
                [You can refer this <a href="https://youtu.be/z5wXyZ1XUjI" target="_blank">video</a> for more details.]'); ?>
                </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("How do I generate my Twitter API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_4'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://dev.twitter.com/' target='_blank'>https://dev.twitter.com/</a><br />
                - Click on ‘My apps’ and sign in with your account credentials.<br />
                - Click on ‘Create new app’.<br />
                - Fill in the details like application name, description, website and callback url.<br />
                - Format for callback url: https:/www.example.com/sitelogin/auth/twitter <br />
                - You can set the permissions for the app according to your choice from the permissions tab.<br />
                - Go to the Key and Access Tokens and copy your API Consumer ID and API Consumer secret key.<br />
                [You can refer this <a href='https://youtu.be/jKagZFellG8' target='_blank'>video</a> for more details.]"); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("How do I generate my Instagram API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_5'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://www.instagram.com/developer/' target='_blank'>https://www.instagram.com/developer/</a> and sign in with your account details.<br />
                - Go to the ‘Manage clients’ tab at the main menu and register a new client.<br />
                - Fill all the required details.<br />
                - Format for redirect url is: https://www.example.com/sitelogin/auth/instagram .<br />
                - Your application will be created. Now go to the manage tab and copy the Client ID and Client Secret Key.<br/>
                - Right now your App will be in Sandbox Mode, you can add other users to use this App from the Sandbox tab.<br/>
                - For making your App live you need to submit it for review. If accepted, it will become live automatically.<br/>
            [You can refer this <a href='https://youtu.be/_mifnPqhyYQ' target='_blank'>video</a> for more details.]"); ?>

            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("How do I generate my Yahoo API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_6'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://developer.yahoo.com' target='_blank'>https://developer.yahoo.com</a> and login with your account credentials.<br />
                - Click on ‘My apps’ in the footer of the page.<br />
                - Click on ‘Create an app’.<br />
                - Put the details required and copy the Client ID and Client Secret Key.<br />
                - Format for Yahoo Callback Domain: https://www.example.com<br/>
                [You can refer this <a href='https://youtu.be/cL3GrtRRAdY' target='_blank'>video</a> for more details.]"); ?>

            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("How do I generate my Pinterest API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_7'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://developers.pinterest.com/' target='_blank'>https://developers.pinterest.com/</a> and sign in with your account.<br />
                - Go to the ‘Apps’ in the main menu.<br />
                - Agree to the developers terms and Api policy and create app.<br />
                - Put the details and add the url, redirect url.<br />
                - Format for redirect url: https://www.example.com/sitelogin/auth/pinterest .<br />
                [Note: Site URL must use the HTTPS protocols.]<br />
                - Copy the App ID and App Secret Key"); ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("How do I generate my Flickr API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_8'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://www.flickr.com/services/developer/api/' target='_blank'>https://www.flickr.com/services/developer/api/</a> and login with your account details.<br />
                - Click on ‘API’ in the main menu and then ‘Request an api key’.<br />
                - Read the instructions and again click on ‘Request an api key’.<br />
                - Choose whether you are going to create a commercial or non-commercial app.<br />
                - Fill the required details and save.<br />
                - Copy your App Key and App Secret Key.<br />
                - Go to the ‘App Garden’.<br />
                - On your App page, add website URL, screenshot, tags.<br />
                - Make your App public by editing ‘Who can see this app?’.<br />
                - Edit the authentication flow and add description and redirect URL.<br />
                - Format for the Redirect URL: https://www.example.com/sitelogin/auth/flickr
                   "); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("How do I generate my Vkontakte API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_9'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://vk.com/dev' target='_blank'>https://vk.com/dev</a> and login with your account details.<br />
                - Then click on ‘My apps’ and create an application.<br />
                - Name your app and fill the site url and base domain.<br />
                - Confirmation code for the app activation will be send to your registered number.<br />
                - Fill that code and activate your application.<br />
                - Fill the description for the app in the information section.<br />
                - Go to the settings tab and copy Application ID and Secure Key.<br />
                - Also add the redirect url and save the changes.<br />
                - Format for redirect url is: https://www.example.com/sitelogin/auth/vk"); ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("How do I generate my Outlook API Key?"); ?></a>
            <div class='faq' style='display: none;' id='faq_10'>
                <?php echo $this->translate("Ans:Follow the below steps:<br />
                - Go to <a href='https://apps.dev.microsoft.com/#/appList' target='_blank'>https://apps.dev.microsoft.com/#/appList</a> and login with your account credentials.<br />
                - Click on ‘Add an app’ and give your application a name and create.<br />
                - Copy your Application ID and click on generate new password and then copy the Application Secret Key, it will be shown in a popup only one time. So save it securely. If you want to view it for the next time you need to ‘Generate New Password’ for the same application.<br />
                - Add the platform and respective urls.<br />
                - Format for the redirect url: https://www.example.com/sitelogin/auth/outlook.<br />
                [Note: Site URL must use the HTTPS protocols.]<br />
                - Add the profile urls also and save the changes"); ?>
            </div>
        </li>
    </ul>
</div>
