<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Videos to be available to only certain directory items / pages on my site. How can this be done?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: You can enable packages for pages on your site, and make Videos available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Videos to be available for pages of only certain member levels.");?></a>
			</div>
		</li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I had not selected to 'Enable Videos Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Directory/Pages Plugin' by editing the Default Package and selecting the 'Videos' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I want to use this plugin for directory of car videos. How can I change the word: 'page videos' to 'car videos' in this plugin?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'page videos' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
			</div>
		</li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to change the text 'pagevideos' to 'carvideos' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the field : Page Videos URL alternate text for "pagevideos"<br />Then, enter the text you want to display in place of \'pagevideos\' in the text box there.'); ?>
      </div>
    </li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?></a>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Page Videos. What can be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?></a>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want to enhance the Pages on my site and provide more features to my users. How can I do it?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate('Ans: There are various apps / extensions available for the "Directory / Pages Plugin" which can enhance the Pages on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-pages-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-pages-extensions</a>');?></a>
			</div>
		</li>
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate('How can I enable the "Upload from My Computer" feature in Directory/Pages Videos extension?');?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate('Ans: Yes, you can enable the "Upload from My Computer" feature in Directory/Pages Videos extension and for that you will have to get FFMPEG installed at your site. You can contact your server hosting company for this and they will install it at your site.');?></a>
			</div>
		</li>
	</ul>
</div>