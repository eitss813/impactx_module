<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-11-15 9:40:21Z SocialEngineAddOns $
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
<div class="admin_sitepage_files_wrapper">
	<ul class="admin_sitepage_files sitepage_faq">
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Why emails are queued for sending ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: Mail queueing permits the emails to be sent out over time, preventing your mail server from being overloaded by outgoing emails. We utilize mail queueing for large email blasts to help prevent negative performance impacts on your site.")?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I am not receiving messages sent from 'Message Page Admins' section of this plugin even when I have created some pages. What is the reason ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Ans: You are not receiving messages sent from 'Message Page Admins' section of this plugin because messages can be sent to other users only, you can not send messages to yourself.");?>
			</div>
		</li>
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want the color settings of the email template sent using this plugin to be different from the email template sent from 'Insights'. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php echo $this->translate("Ans: You can change the color settings of email template from 'Insights Email Settings' section of Directory / Pages Plugin for sending emails using this plugin. After sending email you can again change the color settings.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("From where should I edit the header and footer of the email body ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Ans: To edit the header and footer of the email body, go to Admin panel -> Main Navigation Menu bar -> Settings -> Mail Templates -> Choose message(Header ( Members ) and Footer ( Members )) -> Message Body.");?>
			</div>
		</li>		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>	
	</ul>
</div>