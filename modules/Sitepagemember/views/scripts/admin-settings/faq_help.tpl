<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("The groups on my site also have photos. What will be required to migrate these photos to the advanced Groups created with this extension?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Ans: To migrate the photos of SE groups to the advanced Groups created with this extension, you will be required to install and enable the latest version of “<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums' target='_blank'>Directory / Pages - Photo Albums Extension</a>” on your site.");?>
			</div>
		</li>
	  <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("The groups on my site also have discussions. What will be required to migrate these discussions to the advanced Groups created with this extension?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Ans: To migrate the discussions of SE groups to the advanced Groups created with this extension, you will be required to install and enable the latest version of “<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-discussions' target='_blank'>Directory / Pages - Discussion Extension</a>” on your site.");?>
			</div>
		</li>
	  <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("The groups on my site also have events. What will be required to migrate these events to the advanced Groups created with this extension?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("Ans: To migrate the events of SE groups to the advanced Groups created with this extension, you will be required to install and enable the latest version of “<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-events' target='_blank'>Directory / Pages - Events Extension</a>” on your site. ");?>
			</div>
		</li>
	  <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("The groups on my site also have polls. What will be required to migrate these polls to the advanced Groups created with this extension?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate("Ans: To migrate the events of SE groups to the advanced Groups created with this extension, you will be required to install and enable the latest version of “<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-polls' target='_blank'>Directory / Pages - Polls Extension</a>” on your site.");?>
			</div>
		</li>
	  <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("The groups on my site also have documents. What will be required to migrate these documents to the advanced Groups created with this extension?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate("Ans: To migrate the documents of SE groups to the advanced Groups created with this extension, you will be required to install and enable the latest version of “<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-documents' target='_blank'>Directory / Pages - Documents Extension</a>” on your site.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I have created Member Roles from the “Manage Member Roles” section of this plugin. Now I want to display members based on these roles on Page Profile page. What should do?");?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo $this->translate("Ans: To display members based on their roles on Page Profile page, follow the steps below:<br />&nbsp;&nbsp;&nbsp;i). Place the widget “Page Profile Members” on Page Profile page.<br />&nbsp;&nbsp;&nbsp;ii). Choose the setting “Yes, display members based on their roles.”<br />&nbsp;&nbsp;&nbsp;iii). Select the member roles which you want to display in the block placed.<br />&nbsp;&nbsp;&nbsp;iv). Now you can place this widget multiple times with different Member Roles chosen for each placement.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I have changed the word: “Business” to some other word (say Directory, Group, etc), but I am seeing my changed word in place of “Business” at various places in the admin panel also. Why is this happening?");?></a>
			<div class='faq' style='display: none;' id='faq_8'>
			<?php echo $this->translate("Ans: SocialEngine uses same language variables for both the admin panel and user sides. Therefore, when you change the word: “Business” to some other word, then this change is visible in both user and administration sides.");?>
			</div>
		</li>
	</ul>
</div>