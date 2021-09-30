<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq_help.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="admin_siteotpverifier_files_wrapper">
  <ul class="admin_siteotpverifier_files siteotpverifier_faq">	    
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');">What are the steps to configure Amazon service for OTP verification code?</a>
      <div class='faq' style='display: none;' id='faq_1'>
        <b>Service Integration Steps of Amazon:</b><br/>
        <ul class="faq_stepts">
          <li> Sign in into your <a href="https://aws.amazon.com/" target="_blank" >Amazon</a> account. [If you don't have an account on Amazon then please <a href="https://portal.aws.amazon.com/billing/signup?nc2=h_ct&redirect_url=https%3A%2F%2Faws.amazon.com%2Fregistration-confirmation#/start">signup</a>.]</li>
          <li>
            Go to: 'My Security Credentials' from the dropdown available in the header section under the create account.
          </li>
          <li>Open: 'Access keys (access key ID and secret access key)'.</li>
          <li> Click on 'Create New Access Key'. A pop-up will appear, click on 'Download Key File'.</li>
          <li> Open the downloaded file. </li>
          <li>Copy the required details and save it in the 'Service Integration' → 'Amazon' → 'Edit'.</li>
        </ul>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');">What are the steps to configure Twilio service for OTP verification code?</a>
      <div class='faq' style='display: none;' id='faq_2'>
        <b>Service Integration Steps of Twilio:</b><br/>
        <ul class="faq_stepts">
          <li> Sign in into your <a href="https://www.twilio.com/login" target="_blank" >Twilio</a> account. [If you don't have an account on Amazon then please <a href="https://www.twilio.com/try-twilio">signup</a>.]</li>
          <li>
            Go to 'Dashboard' → 'Account Summary'. From here, you will get 'Account SID' and 'Authorization Token'. 
          </li>
          <li>Go to: <a href="https://www.twilio.com/console/phone-numbers/searchable" target="_blank" >https://www.twilio.com/console/phone-numbers/searchable</a>
            <ul>
              <li>Fill the details in the search fields like: Country, Number, Location, Capabilities. </li>
              <li>Now, click on 'Search' button.</li>
              <li>List of phone numbers will appear as per the filled criteria. You can check the monthly charges for the number. Buy any number as per your preference.</li>
              <li>Click on 'Setup Number'.</li>
              <li>You will get the 'Phone Number' from here.</li>
            </ul>
          </li>
          <li> Save all the details in 'Service Integration' → 'Twilio' → 'Edit'.</li>
        </ul>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');">Why my OTP service has stopped?</a><br/>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');">Why users on my site are not receiving OTP code for signin / signup? </a>
      <div class='faq' style='display: none;' id='faq_3'>
        If your site users are not able to receive OTP verification code then please check whether you have sufficient balance in your integrated service account (Twilio or Amazon).
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');">Is it possible to test the functionality of this plugin without configuring Amazon or Twilio service?</a><br/>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');">What is Virtual SMS Client? How it works?</a>
      <div class='faq' style='display: none;' id='faq_4'>
        <p>You can test the functionality of this plugin through 'Virtual SMS Client', which does not require configuration of Amazon or Twilio service.</p><br />
        You can use 'Virtual SMS Client' by following below steps: <br />
        <ul class="faq_stepts">
          <li>Go to 'Service Integration' section in the admin panel of this plugin.</li>
          <li>Enable 'Virtual SMS Client'.</li>
          <li>Click on 'View SMS' under 'Options'. You will see a mobile opened in a new browser's tab.
          </li>
          <li>Open this URL in the browser where you are checking the secure signin / signup process using OTP verification code.</li>
        </ul>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');">How do I enable on Two Factor Verification on my website?</a>
      <div class='faq' style='display: none;' id='faq_5'>
        It is really easy! Just follow the below steps:<br>
        <ul class="faq_stepts">
          <li>Go to 'Global Settings' section in the admin panel of this plugin.</li>
          <li>Find 'Login Options' setting here.</li>
          <li>Enable 'Two Factor Verification'.</li>
          <li>Now, go to 'Member Level Settings' to enable 'Two Factor Verification' on a member level basis.</li>
          <li>'Two Factor Verification' is enabled on your website. User can decide whether he wants to enable it or not.</li>
          <li>User can go to 'Settings' → 'Phone Number Details' to enable / disable 'Two Factor Verification'.</li>
        </ul>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');">If a user bought a new phone number and want to associate that with his account, then how he can do it?</a>
      <div class='faq' style='display: none;' id='faq_6'>
        User can follow below steps to edit / change the associated phone number:<br/>
        <ul class="faq_stepts">
          <li> Go to 'Settings' → 'Phone Number Details' at user side.</li>
          <li>Click on 'Edit' showing near the current phone number.</li>
          <li>Edit / change the phone number and click on 'Save' button.</li>
        </ul>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');">If a user has signed up using Phone Number then where he will receive all the mail notifications?</a><br/>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');">Can a user associate an email address after doing signup using Phone Number?</a>
      <div class='faq' style='display: none;' id='faq_7'>
        When a user signups using Phone Number then an email address with below format is assigned to him:<br />
        se[PHONE_NO]@semail.com<br /><br />
        As this is a dummy email, user will not be able to receive any mail notifications sent from your site. User can edit this email address from 'General Settings' at user side.
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');">I have enabled Email Verification in sign up process. But, user can sign up using either email address or phone number. So, how will their email address will verify?</a><br/>

      <div class='faq' style='display: none;' id='faq_8'>
        If a user signups using phone number then a dummy email address is assigned to him. This dummy email address gets automatically verified once the user successfully signups using phone number.
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');">How can we ban a user who has exhausted the limit of attempts to resend the verification code? For how long this user will be banned?</a><br/>

      <div class='faq' style='display: none;' id='faq_9'>
        Follow below steps to ban a user who has exhausted the limit of attempts to resend the verification code:<br/>
        <ul>
          <li>Go to 'Member Level Settings' section in the admin panel of this plugin.</li>
          <li>Set count of 'User Blocking Duration' for the time duration you want to block a user after continuous failed attempts.</li>
        </ul>
      </div>
    </li>
  </ul>
</div>


<script type="text/javascript">
  function faq_show(id) {
    if ($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
<?php if( $this->faq ): ?>
    faq_show('<?php echo $this->faq ?>');
<?php endif; ?>
</script>