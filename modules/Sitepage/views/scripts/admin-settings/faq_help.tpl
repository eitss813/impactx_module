<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
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

<?php if(!empty($this->faq)) : ?>
	<p><?php echo $this->translate("Browse the different FAQ sections of this plugin by clicking on the corresponding tabs below.") ?><p>
	<br />
	<?php $action = 'faq' ?>
<?php else : ?>
	<?php $action = 'readme' ?>
<?php endif; ?>

<div class='tabs seaocore_faq_tabs'>
		<ul class="navigation">
		  <li class="<?php if($this->faq_type == 'general') { echo "active"; } ?>">
		 	<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'general'), $this->translate('General'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'package') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'package'), $this->translate('Packages'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'layout') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'layout'), $this->translate('Page Layout'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'claim') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'claim'), $this->translate('Claims'), array())
		  ?>
			<li class="<?php if($this->faq_type == 'category') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'category'), $this->translate('Categories'), array())
		  ?>
			<li class="<?php if($this->faq_type == 'profile') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'profile'), $this->translate('Profile Fields'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'insights') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'insights'), $this->translate('Insights'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'import') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'import'), $this->translate('Import'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'language') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'language'), $this->translate('Language'), array())
		  ?>
			</li>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.isActivate',0)): ?>
			<li class="<?php if($this->faq_type == 'activity') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => 'activity-feed'), $this->translate('Activity Feed'), array())
		  ?>
			</li>
      <?php endif; ?>
			<li class="<?php if($this->faq_type == 'customize') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'customize'), $this->translate('Customize'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'integration') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'sitepage','controller' => 'settings','action' => $action, 'faq_type' => 'integration'), $this->translate('Plugin Integration'), array())
		  ?>
			</li>
		</ul>
	</div>

<?php switch($this->faq_type) : ?>
<?php case 'general': ?>
	<div class="admin_seaocore_files_wrapper">
		<ul class="admin_seaocore_files seaocore_faq">
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How do I go about setting up this plugin on my site? What are the steps?");?></a>
				<div class='faq' style='display: none;' id='faq_1'>
					<?php echo $this->translate("Ans: Below are the steps you should follow to configure the plugin on your site according to your requirements:");?>
					<ul>
						<li>
							<?php echo $this->translate("Start by configuring the Global Settings for this plugin on your site. Some important settings here are:");?>
							<ul>
								<li>
									<?php echo $this->translate("Should Packages be enabled for directory items / pages?");?>
								</li>
								<li>
									<?php echo $this->translate("Should pages be able to have multiple admins / owners?");?>
								</li>
								<li>
									<?php echo $this->translate("Should page admins / owners be able to alter the block positions(layout) / add new available blocks on the directory item / page profile?");?>
								</li>
								<li>
									<?php echo $this->translate("Should Price and Location fields be enabled for directory items / pages?");?>
								</li>
								<li>
									<?php echo $this->translate("Should maps integration be enabled?");?>
								</li>
							</ul>
							<?php echo $this->translate("and many more.");?>
						</li>
						<li>
							<?php echo $this->translate('If you have enabled packages for directory items / pages on your site, then create packages from the "Manage Packages" section. Users will have to select a package before creating a page. You can choose settings for packages like free/paid, duration, auto-approve, featured, sponsored, features available, apps accessible, etc.');?>
						</li>
						<li>
							<?php echo $this->translate('If you have disabled packages, then settings for pages like auto-approve, featured, sponsored, features available, apps accessible, etc will depend on Member Level Settings. You may configure them from the "Member Level Settings" section.');?>
						</li>
						<li>
							<?php echo $this->translate('If you have configured paid page packages on your site, then configure payment related settings on your site from the "Billing" > "Settings" and "Billing" > "Gateways" sections.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure the categories and sub-categories for pages on your site from the "Categories" section.');?>
						</li>
						<li>
							<?php echo $this->translate('Create and configure profile types and profile questions (custom fields) for pages on your site from the Profile Fields section.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure the Categories to Page Profile mapping from the "Category - Page Profile Mapping" section. This feature enables you to have different profile information questions for pages based on their categories.');?>
						</li>
						<li>
							<?php echo $this->translate('Choose the type of layout for Pages on your site from the "Page Layout" section. This plugin provides 2 types of layouts: tabbed and non-tabbed.');?>
						</li>
						<li>
							<?php echo $this->translate('Configure other settings for directory items / pages on your site like: Member Level Settings, Widget Settings, Insights Settings, Ad Settings, etc.');?>
						</li>
						<li>
							<?php echo $this->translate('Place the various widgets available with this plugin at the desired locations from the "Layout" > "Layout Editor" section.');?>
						</li>
						<li>
							<?php echo $this->translate('Install and set up the desired extensions / apps for this plugin. There are various extensions available for this plugin which extend and enhance its functionalities. To see the available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-pages-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-pages-extensions</a>');?>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Can a directory item / page have multiple administrators? How will this work?");?></a>
				<div class='faq' style='display: none;' id='faq_5'>
					<?php echo $this->translate('Ans: Yes, directory items / pages on your site can have multiple administrators having equal authority on the page. Multiple admins for pages can be enabled from Global Settings using the field: "Page Admins". If enabled, then every Page will be able to have multiple administrators who will be able to manage that Page. Page Admins will have the authority to add other users as administrators of their Page from the Page Dashboard.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Can the owners of directory items / pages select somes specific administrators of their pages and highlight them in some way to users?");?></a>
				<div class='faq' style='display: none;' id='faq_6'>
					<?php echo $this->translate('Ans. Yes, Page owners can do so by making some admins as "Featured" from the Page Dashboard. These Featured Page admins will then be displayed in a block at Page Profile.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("What is Proximity Search?");?></a>
				<div class='faq' style='display: none;' id='faq_7'>
					<?php echo $this->translate("Ans: Proximity Search enables users to find directory items / pages within a certain distance from a location.");?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_34');"><?php echo $this->translate('How can I disbale the Price Field from the "Create New Page" section?');?></a>
				<div class='faq' style='display: none;' id='faq_34'>
					<?php echo $this->translate('Ans: Go to the "Global Settings" section of this plugin and set the "Price Field" field to "No" and save the settings. This will disable the Price field at the "Cretae New Page" form.');?>
				</div>
			</li>

			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_60');"><?php echo $this->translate('What is the difference between sponsored and featured pages?');?></a>
				<div class='faq' style='display: none;' id='faq_60'>
					<?php echo $this->translate('Ans: "Sponsored" and "Featured" are just some kind of special or can say preferred categories which are in some way highlighted through dedicated widgets on the site.<br />Now, if we talk about the difference between the two, then there is no difference as such on the level of functionality between the two. They just work like labels for a particular page so that user can view the Page with that label.<br /><br />For ex- there is a "Featured Pages Slideshow widget" and a "Sponsored Pages Carousel widget" in this plugin.<br />So, one difference in terms of display of Featured and Sponsored Pages is that Featured Pages are displayed in Slideshow and Sponsored Pages are displayed in Carousel in the respective widgets.<br /><br />But there is no such criteria only on which a page can be made sponsored or featured. Site admin can make any page sponsored or featured considering the value of that Page and also users can select packages accordingly while creating page to make their 
page sponsored or featured.');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("How can pages be made sponsored? Where are sponsored pages displayed? Can I have users to pay for Sponsored Pages?");?></a>
				<div class='faq' style='display: none;' id='faq_8'>
					<?php echo $this->translate("Ans: There are 3 ways in which pages can be made sponsored:");?>
							<br />
							<?php echo $this->translate('1) If you have packages enabled on your site, then you can choose for pages of a package to be automatically made sponsored.');?>
							<br />
							<?php echo $this->translate('2) If packages are disabled, then you can choose from Member Level Settings that pages created by certain member levels be automatically made sponsored.');?>
							<br />
							<?php echo $this->translate('3) From the Manage Pages section, you can make / remove pages as sponsored.');?>
							<br />
					<?php echo $this->translate('Sponsored Pages are shown in the "Sponsored Pages Carousel" widget. Also, at various places like in listing of pages on Pages Home and in Browse Pages, sponsored pages are specially marked. The setting: "Default ordering on Browse Pages" in Global Settings also enable you to show sponsored pages at the top in the listing on browse pages. Also, on the main profile of sponsored pages, you can configure a label to be shown above the profile picture. Users can be made to pay to get their pages sponsored by making auto-sponsored packages as paid packages.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("How can pages be made featured? Where are featured pages displayed? Can I have users to pay for Featured Pages?");?></a>
				<div class='faq' style='display: none;' id='faq_9'>
					<?php echo $this->translate("Ans: There are 3 ways in which pages can be made featured:");?>
							<br />
							<?php echo $this->translate('1) If you have packages enabled on your site, then you can choose for pages of a package to be automatically made featured.');?>
							<br />
							<?php echo $this->translate('2) If packages are disabled, then you can choose from Member Level Settings that pages created by certain member levels be automatically made featured.');?>
							<br />
							<?php echo $this->translate('3) From the Manage Pages section, you can make / remove pages as featured.');?>
							<br />
					<?php echo $this->translate('Featured Pages are shown in the "Featured Pages Slideshow" widget. Also, at various places like in listing of pages on Pages Home and in Browse Pages, featured pages are specially marked. The setting: "Default ordering on Browse Pages" in Global Settings also enable you to show featured pages at the top in the listing on browse pages. Also, on the main profile of featured pages, you can configure a label to be shown below the profile picture. Users can be made to pay to get their pages featured by making auto-featured packages as paid packages.'); ?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I have multiple networks on my site and I want users to be shown only those pages belonging to their network. How can this be done?");?></a>
				<div class='faq' style='display: none;' id='faq_10'>
					<?php echo $this->translate('Ans: You can do this by saving "Yes" for the "Browse by Networks" field in Global Settings.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_47');"><?php echo $this->translate("I have closed a Page. But still I am able to view this. How can I disable the display of closed pages to users ?");?></a>
				<div class='faq' style='display: none;' id='faq_47'>
					<?php echo $this->translate("Ans: To do so, please go to the 'Global Settings' section of this plugin and find the field 'Open / Closed status in Search'. Setting this field to 'No' will disable the display of Closed Pages to users on the Browse Pages and in the search options and results.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("The widths of the webpage columns are not coming fine on Pages Home. What might be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_22'>
					<?php echo $this->translate("Ans: This is happening because none or very few directory items / pages have been created and viewed on your site, and thus the widgets on Pages Home are currently empty. Once pages are rated and liked on your site, and more activity happens, these widgets will get populated and Pages Home will look good.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_35');"><?php echo $this->translate('Is there any need of Google Maps JavaScript API keys?');?></a>
				<div class='faq' style='display: none;' id='faq_35'>
					<?php echo $this->translate('Ans: No, there is no need of Google Maps JavaScript API keys. This is because we are using Google Maps Javascript API V3 in this plugin and this version no longer needs API keys. For more details, you can visit this link: %s', '<a href="http://code.google.com/apis/maps/documentation/javascript/basics.html" target="_blank">http://code.google.com/apis/maps/documentation/javascript/basics.html</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_58');"><?php echo $this->translate('Almost all the Pages of my site belongs to the location which come under a particular area or city. Is there any way so that users can clearly view the nearby areas and locations in the map without manually zooming it?');?></a>
				<div class='faq' style='display: none;' id='faq_58'>
					<?php echo $this->translate("Ans: Yes, you can set the center location for the Pages in the field 'Center Location for Map at Pages Home and Browse Pages' under the Global Settings section of this plugin. Below this option, you can enter the default zoom level for the map as per your requirement so that users do not have to manually do that.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_48');"><?php echo $this->translate("How can I rearrange the items in the search box shown on the ‘Pages Home’ and ‘Browse Pages’ pages?");?></a>
				<div class='faq' style='display: none;' id='faq_48'>
					<?php echo $this->translate("Ans: Yes, you can rearrange the items in the search box from the 'Search Form Settings' section in the admin panel of this plugin. There, you can drag-and-drop the search fields vertically and save the sequence as per your requirement.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_49');"><?php echo $this->translate("I do not want to show some fields in the search box to the users. How can I do this?");?></a>
				<div class='faq' style='display: none;' id='faq_49'>
					<?php echo $this->translate("Ans: Go to the 'Search Form Settings' section in the admin panel of this plugin and click on the Hide / Display icon for such fields. Fields which have been made hidden here would not be shown in the search box shown on the ‘Pages Home’ and ‘Browse Pages’ pages. You can also display them later if you want.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_57');"><?php echo $this->translate("I want my users to be able to link their Pages to other related Pages. Can it be done ?");?></a>
				<div class='faq' style='display: none;' id='faq_57'>
					<?php echo $this->translate("Ans: Yes, you can link your Page to the other related pages with this plugin. To do so, please go the 'Global Settings' section of this plugin and select the option 'Yes' for the field 'Linking Pages' there. Doing so will make a 'Link to your Page' link appear on Pages and then users can select any of their Pages which they want to be linked to that Page.<br /><br />Once a Page is linked to their Page, that Page will appear in a widget titled 'Linked pages' on their Page's Profile.<br /> Please make sure that 'Page Profile Linked Pages' widget is placed on the Profile Page Layout.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_28');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_28'>
					<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
				</div>
			</li>
		    <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_100');"><?php echo $this->translate("I have changed the word: “Page” to some other word (say Directory, Group, etc), but I am seeing my changed word in place of “Page” at various places in the admin panel also. Why is this happening?");?></a>
				<div class='faq' style='display: none;' id='faq_100'>
					<?php echo $this->translate("Ans: SocialEngine uses same language variables for both the admin panel and user sides. Therefore, when you change the word: “Page” to some other word, then this change is visible in both user and administration sides.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_101');"><?php echo $this->translate("I have enabled Page Verification setting on my site, but no such option is displaying on the page profiles. Is there any other setting related to this functionality?");?></a>
				<div class='faq' style='display: none;' id='faq_101'>
					<?php echo $this->translate('Ans: Yes, you have to place the required widgets as explained below: <br><br> Firstly, place the “Verify Page Button & Stats” on the page profile of layout editor. This will showcase the verify button and verification stats on all the page profiles of your site. <br> Secondly, you can place “Most Verified Pages” widget on any of the widgetized page like pages home or browse pages to list down all the verified pages. <br> Also, you need to enable page verification for different member levels from the “Member Level Settings” tab.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("Where can I enable / disable the Tips Section?");?></a>
				<div class='faq' style='display: none;' id='faq_20'>
					<?php echo $this->translate('Ans: You can enable / disable the Tips section from the Global Settings section of this plugin. Make sure to save your text in the tips section if you enable tips. ');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("The changes I make in the Global Settings is not reflecting. What could be the issue?");?></a>
				<div class='faq' style='display: none;' id='faq_21'>
					<?php echo $this->translate('Ans: Please make sure that tips is not left blank if tips have been enabled for your site. If the tips is enabled and the tips editor is left blank, other changes that you make in the settings will also not be saved.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_42');"><?php echo $this->translate("Where can I save the Operating Hours for my pages?");?></a>
				<div class='faq' style='display: none;' id='faq_42'>
					<?php echo $this->translate('Ans: You can save the operating hours for your pages from your page dashboard in terms of Hours and Days.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("Where can I disable joining of my pages by other members on the site?");?></a>
				<div class='faq' style='display: none;' id='faq_23'>
					<?php echo $this->translate('Ans: You can enable / disable joining of your pages by other members on the site from your Page Dashboard. However, this is dependent on our Directory / Pages - Page Members Extension, i.e. the setting to enable / disable joining of pages will be displayed only if you have this extension installed on your site.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("Where can I disable joining of my pages by other members on the site?");?></a>
				<div class='faq' style='display: none;' id='faq_24'>
					<?php echo $this->translate('Ans: You can enable / disable joining of your pages by other members on the site from your Page Dashboard. However, this is dependent on our Directory / Pages - Page Members Extension, i.e. the setting to enable / disable joining of pages will be displayed only if you have this extension installed on your site.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo $this->translate("Why can’t I find the option to add Page Services to my Pages?");?></a>
				<div class='faq' style='display: none;' id='faq_25'>
					<?php echo $this->translate('Ans: If packages are enabled on your site, all member levels will be able to add services to their pages from the Page Dashboards. However, if packages have been disabled please go to the Member Level settings and enable creation of services for the required member levels.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo $this->translate(" How can I add a Call-to-Action Button to the pages on my site?");?></a>
				<div class='faq' style='display: none;' id='faq_26'>
					<?php echo $this->translate('Ans: Please follow the below steps to add call-to-action buttons to your pages.');?>
					<ul>
						<li>
							<?php echo $this->translate('Place the “Add Button” widget to your Page Profile from the Layout Editor.');?>
						</li>
						<li>
							<?php echo $this->translate('Once the widget has been placed on the layouts of your pages, page admins can see the Add a Button Widget on their Page Profiles which they can configure according to their requirements.');?>
						</li>
					</ul>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate(" What is Quick Page Creation?");?></a>
				<div class='faq' style='display: none;' id='faq_27'>
					<?php echo $this->translate('Ans: ');?>
					<ul>
						<li>
							<?php echo $this->translate('You can enable / disable the Quick Page Creation option from the Global settings of this plugin.');?>
						</li>
						<li>
							<?php echo $this->translate(' When Quick Page Creation has been enabled only the mandatory questions, i.e. Page title, URL, profile photo, etc. will be visible in the Page Creation Form.');?>
						</li>
						<li>
							<?php echo $this->translate(' The non-mandatory fields will be available in the page dashboard that Page Owners can fill in at their own convenience.');?>
						</li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
	<?php break; ?>

	<?php case 'package': ?>
	<div class="admin_seaocore_files_wrapper">
		<ul class="admin_seaocore_files seaocore_faq">
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("What are Page Packages? How are Packages and Member Level Settings related?");?></a>
				<div class='faq' style='display: none;' id='faq_2'>
					<?php echo $this->translate('Ans: Both packages and member level settings enable you to configure settings for pages belonging to them / created by members belonging to them, like auto-approve, featured, sponsored, features available, apps accessible, etc. Packages can be enabled from "Global Settings" and created from "Manage Packages" section. If packages are disabled, then settings configured in Member Level Settings apply. If packages are enabled, then before creating a page on your site, users will have to choose a package for it. Packages in this system are very flexible and create many settings as mentioned below, to suit your needs:');?>
					<ul>
						<li>
							<?php echo $this->translate('Paid / Free package. Paid packages enable you to monetize your site!');?>
						</li>
						<li>
							<?php echo $this->translate('Package cost');?>
						</li>
						<li>
							<?php echo $this->translate('Lifetime Duration for pages of this package (forever, or fixed duration)');?>
						</li>
						<li>
							<?php echo $this->translate('Auto-Approve pages of this package');?>
						</li>
						<li>
							<?php echo $this->translate('Make pages of package sponsored');?>
						</li>
						<li>
							<?php echo $this->translate('Make pages of package featured');?>
						</li>
						<li>
							<?php echo $this->translate('Which of the following features should be available to pages of this package:');?>
							<ul>
								<li>
									<?php echo $this->translate('Tell a friend');?>
								</li>
								<li>
									<?php echo $this->translate('Print');?>
								</li>
								<li>
									<?php echo $this->translate('Rich Overview');?>
								</li>
								<li>
									<?php echo $this->translate('Location Map');?>
								</li>
								<li>
									<?php echo $this->translate('Insights');?>
								</li>
								<li>
									<?php echo $this->translate('Contact Details');?>
								</li>
<!--								<li>
									<?php echo $this->translate('Save to Foursquare Button');?>
								</li>-->
                <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) :?>
                  <li>
                    <?php echo $this->translate('Display Twitter Updates');?>
                  </li>
                <?php endif;?>
								<li>
									<?php echo $this->translate('Send an Update');?>
								</li>
							</ul>
							<?php echo $this->translate('and more'); ?>
						</li>
						<li>
							<?php echo $this->translate('Modules / App Extensions that should be available to pages of this package.'); ?>
						</li>
						<li>
							<?php echo $this->translate('Show all, none or restricted profile information for pages of this package. Restricted profile information will enable you to choose the profile fields that should be available to pages of this package.'); ?>
						</li>
					</ul>
				</div>
			</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I have created a new package but no Apps are available for the pages of this package. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_3'>
						<?php echo $this->translate("Ans. To enable the various Apps for the pages of a package, you would have to edit this package from 'Manage Packages' section and select the modules in the 'Modules / Apps' field which you want to be available for the pages of that package.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Can the Page owner change the package of a Page after its creation?");?></a>
					<div class='faq' style='display: none;' id='faq_4'>
						<?php echo $this->translate("Ans: Yes, Page owners can change the package of their Pages from the 'Packages' section at Page Dashboard. All the available packages with their features are listed there. Once the package for a page is changed, all the settings of the page will be applied according to the new package, including apps available, features available, price, etc.<br />Please note that if you(admin) have not selected the 'Auto-Approve' field for the new package in 'Manage Packages' section, then the Page will get dis-approved. Also, if the new package is a paid package then the Page will get dis-approved even if it has been made auto-approved by you(admin) and page owner will have to first make the payment to make it approved again.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_59');"><?php echo $this->translate("Why am I not able to delete a Page Package?");?></a>
					<div class='faq' style='display: none;' id='faq_59'>
						<?php echo $this->translate("Ans: You can not delete a Page Package once you have created it. This is because the consistency of the already created pages in that package would get affected in that case. But if you wish, you can disable a page package from Manage Packages section and hence it would not be displayed in the list of packages during the initial step of the page creation and also while upgrading the package of a page at Page's Dashboard.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_60');"><?php echo $this->translate("Is it possible to let users create free trial pages on my site valid only for a limited period of time?");?></a>
					<div class='faq' style='display: none;' id='faq_60'>
						<?php echo $this->translate('Ans: Yes, it is possible to do so by doing the following things:<br />1) You can disable the existing free package from the "Manage Packages" section at the admin panel of this plugin.<br />2) Now, create a new free package from there and you can set the specifications including the "Duration" and the features you want to give users in that package and then do not select the checkbox for field \'Show in "Other available Packages" List\' so that users will not be able to upgrade their page\'s package and use this package for more than the no. of days you have set there.<br />3) Also, in future when you do not want users to create free Pages anymore at your site, you can disable all the FREE packages from "Manage Packages" section.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'layout': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Can I enable the owners of directory items / pages on my site to customize the position of blocks / layout of their pages, and add new blocks to it?");?></a>
					<div class='faq' style='display: none;' id='faq_11'>
						<?php echo $this->translate('Ans: Yes, you can do so by enabling the "Edit Page Layout" field in Global Settings. If enabled, page admins will be able to alter the block positions / add new available blocks on the directory item / page profile. Page admins will also be able to add HTML blocks on their directory item / page profile.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("Can I change the layout types for profile of directory items / pages on my site? What options do I have?");?></a>
					<div class='faq' style='display: none;' id='faq_12'>
						<?php echo $this->translate('Ans: Yes, you can choose from amongst 2 attractive AJAX based layouts for Pages on your site, from the "Page Layout" section. The 2 layouts are: Tabbed Layout and Non-tabbed Layout. The 2 layouts differ primarily in the way the AJAX based widgets like Info, Updates, Overview, and modules based widgets like Photos, Videos, Reviews, etc are displayed. The tabbed layout has main widgets as ajax based tabs in a horizontal row and in the middle column of the page. The non-tabbed layout has main widgets as ajax based links in a vertical order and in the left column of the page, below the page profile picture.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_45');"><?php echo $this->translate("I do not want some tabs to be hidden in the 'More' at the Page Profile. I want users to view them directly on the Page profile. What should I do ?");?></a>
					<div class='faq' style='display: none;' id='faq_45'>
						<?php echo $this->translate("Ans: If you want some specific tabs to be shown directly and not in the 'More' tab, then you can do the following things:<br /><br />1) Go to the Global Settings section of this plugin, and set the value of 'Tabs / Links' field like that so as to occupy the whole space in the navigation bar for tabs at Page profile. This will display that much of tabs out of the 'More' tab on the Page Profile.<br /><br />2) Now, if user 'Edit Page Layout' is disabled from Global Settings section then, you can go to the 'Layout' > 'Layout Editor' section at the admin side and edit the Page profile layout and place those widgets a bit above vertically in the tab container there.<br /> Otherwise individual Page Admins will be able to set the ordering of tabs on the Page Profile from their Page Dashboard's 'Edit Layout' section.<br /><br />It will let those tabs appear in priority and out of the 'More' tab if there are too many tabs.");?>
					</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_43');"><?php echo $this->translate("The left column menu is not shown in the tabs of Pages Plugin Extensions on Page profile. But this is not the case with 'Info' and 'Updates' tabs. Why is it so ?");?></a>
				<div class='faq' style='display: none;' id='faq_43'>
					<?php echo $this->translate("Ans: In that case Community Ads would have been installed at your site and is being integrated with Pages Plugin.<br /><br />When Ads are shown in the tabs of pages extensions at Page profile, we have to hide the left side menu column. This is because of the lack of width in that case. So, to display the Ads in such a way that there is no UI issue, we are hiding the left side menu in case of extension tabs.<br /><br /> While at Info and Update tabs, there is sufficient width to display Ads at the same Page and we do not need to hide left side menu in that case.");?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_44');"><?php echo $this->translate("I have messed up with the layout of Page Profile and want to set it to the default layout again. What should I do ?");?></a>
				<div class='faq' style='display: none;' id='faq_44'>
					<?php echo $this->translate("Ans: Yes, you can reset the layout of your Pages to default layout from the 'Page Layout' > 'Page Profile Layout Type' section at the admin side of this plugin. You can just choose one out of the two layouts there and save it. It will set the layout of all the Pages at your site to the default layout.");?>
				</div>
			</li>
      <li>
				<a href="javascript:void(0);" onClick="faq_show('faq_80');"><?php echo $this->translate('How can I configure Pages Main Navigation Menu bar? Suppose my site is an Album based site and I want users to view "Albums" tab above other tabs in the Pages Main Navigation Menu. How can I do this?');?></a>
				<div class='faq' style='display: none;' id='faq_80'>
					<?php echo $this->translate('Ans: You can do so by following the steps mentioned below:<br /><br />
						1) Go to the Global Settings section of this plugin, and set the value of "Tabs in Pages navigation bar" as the number of tabs you want to be displayed before the "More" drop-down link including more link.<br /><br />
						2)Now, to configure the tabs, go to the "Layout" > "Menu Editor" section at the admin side. There on selecting "Page Main Navigation Menu" from "Editing" drop-down you can add item or edit the items in the navigation bar. To re-order the tabs drag and drop the tabs to their desired position.');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_81');"><?php echo $this->translate('What will happen to my existing Page Layouts if I have upgraded my Plugin to the new version?');?></a>
				<div class='faq' style='display: none;' id='faq_81'>
					<?php echo $this->translate('Ans: Consider following the points mentioned below:<br /><br />
						1) If you have upgraded your plugin to the new version, please check the “Edit Page Layout” in the Global Settings of this plugin.<br /><br />
						2)If this has been set to Yes, the new Layout Mapping Feature will be set to “No Mapping” and will not affect the layouts of your site.<br /><br />
						3)However, if this setting, i.e. the Edit Page Layout is not visible to you it means that it was set to No previously (before the Upgrade).<br /><br />
						4)Importantly, if you set the Edit Page Setting from Yes to No after you upgrade the plugin, you will not be able to see the previous settings anymore and only the Layout Mapping setting will be available. The previous Page Layout tabs will still be available, however, you will not be able to make any changes there.');?>
				</div>
			</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'claim': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("Can users claim directory items / pages on my site, if they are the rightful owner? How does this work?");?></a>
					<div class='faq' style='display: none;' id='faq_13'>
						<?php echo $this->translate('Ans: The claiming of pages feature can be enabled from the "Claim a Page Listing" field in Global Settings. If enabled, users will be able to file claims for directory items / pages. You can customize the position of the "Claim a Page" link from Global Settings. Claims filed by users can be managed from the "Manage Claims" section. From "Member Level Settings", you can choose if users of a member level should be able to claim pages. Whenever someone makes a claim for a page, that claim comes to you(admin) for review and approval.<br />You(admin) can also assign certain users as "Claimable Page Creators" from the "Manage Claims" section. Though using the "Claim a Page" link a user can make a claim for any page, the pages created by "Claimable Page Creators" get the "Claim this Page" link on the page profile itself. This would be useful in cases like if you have certain members whose job is to create only those pages on your site which could later be easily claimed by their 
rightful owners.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("How can I modify the Terms of Service for claiming a page?");?></a>
					<div class='faq' style='display: none;' id='faq_14'>
						<?php echo $this->translate('Ans: You can modify them from the Language variables of the plugin using the Language Manager. Go to the Layout > Language Manager and search for the two variables "PAGE_TERMS_CLAIM_1" and "PAGE_TERMS_CLAIM_2" and edit them according to your specifications.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("If I approve a claim request for a Page, what changes take place?");?></a>
					<div class='faq' style='display: none;' id='faq_15'>
						<?php echo $this->translate("Ans: If you approve a claim request by a user for a Page, it means that you are assigning that Page to the claimer and the claimer will now be the new owner of that page. All ownership rights for this page will then be transferred to the new owner. In this case, both the new and old owners will be recieving an email concerning the change in the ownership of the Page.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Can I change the owner of a Page and assign it to someone else even if I have not recieved a claim request for that Page?");?></a>
					<div class='faq' style='display: none;' id='faq_16'>
						<?php echo $this->translate("Ans. Yes, you can change the owner of a page from the 'change owner' option in the 'Mange Pages' section. Both the new and old owner of the page will be recieving an email concerning the change in the ownership of the page.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'category': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("The Categories and Sub-categories widget is not showing all the categories and sub-categories. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_23'>
						<?php echo $this->translate("Ans: This widget only shows those categories and sub-categories which have atleast one page in them. However, the Categories and Sub-categories sidebar widget shows all the categories and sub-categories irrespective of the number of pages in them.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("There is no option to delete a category or sub-category. If I want to delete them, how can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_24'>
						<?php echo $this->translate("Ans. Go to the 'Categories' section and click on the category or sub-category name which want to delete. The category name would then appear in a text box for editing. Now, you can either edit category name or delete the whole text to delete the category.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_40');"><?php echo $this->translate("How can I make my users to edit categories of their Pages ?");?></a>
					<div class='faq' style='display: none;' id='faq_40'>
						<?php echo $this->translate("Ans: Please go to the 'Global Settings' section of this plugin and select the value 'Yes' for the 'Edit Page Category' field there. This way page admins will be able to edit the category of their Pages even after the page creation in the 'Edit Info' section of the Page Dashboard.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_46');"><?php echo $this->translate("I am not able to see sub-categories below some categories in the widget on the 'Pages Home' page. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_46'>
						<?php echo $this->translate("Ans: This is because you would have not selected any subcategory for those Pages created in those categories. And as we have told you in the 1st FAQ of this section also that this widget only shows those categories and sub-categories which have atleast one page in them.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'profile': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("Can I add custom profile fields for directory items / pages? Can these fields be related to the categories of the pages?");?></a>
					<div class='faq' style='display: none;' id='faq_17'>
						<?php echo $this->translate('Ans: Yes, you can create custom profile fields for pages from the "Profile Fields" section. Profile Fields can be created by first creating profile types and then creating profile questions for them. You can also associate mappings between categories and page profile types from the "Category-Page Profile Mapping" section, such that profile fields are available to pages based on their categories. If you have not created such mappings between categories and profile types, then page owners will have to select a profile type before filling profile information.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_39');"><?php echo $this->translate("I have added some Profile fields for Pages, but these are not shown on the Page creation form. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_39'>
						<?php echo $this->translate('Ans: The profile questions which are created by the site admin in different profile types for the Pages on the site does not appear at the time of the creation of the Page in the create form.<br /><br />These profile information related questions appear after the creation of the Page at the Page Dashboard in the "Profile Info" tab.<br /><br />The user can select the profile type there and then fill the profile info in various fields. In case the category of that page had already mapped with a profile type by you (site admin), then the user can directly fill the information in the corresponding fields of the profile type.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("I have created a new Profile Type for Pages and associated it with a category. But while editing the Profile Info of a Page in that category from the Page Dashboard, all the fields of that profile type did not appear. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_18'>
						<?php echo $this->translate("Ans. Availability of Profile fields to pages also depends on their package; if packages are disabled, then it depends on the member level settings for the page owner. Now, you would have unchecked some profile fields in the 'Profile Information' field of the package your page belongs to. And if packages are disabled, then you would have unchecked some profile fields in the 'Profile Creation' field of 'Member Level Settings' for the member level of the owner of this Page. So, Please check these settings.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("How can I re-arrange the sequence of the custom profile fields for pages?");?></a>
					<div class='faq' style='display: none;' id='faq_19'>
						<?php echo $this->translate('Ans: You may drag-and-drop the fields in the "Profile Fields" section to their desired position in sequence.');?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'insights': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("Can directory item / page owners see the performance of their page listing?");?></a>
					<div class='faq' style='display: none;' id='faq_20'>
						<?php echo $this->translate('Ans: Yes, page owners can see both graphical as well as statistical reports of their pages. Graphical statistics are shown in the "Insights" section of the Page Dashboard and tabular statistics are shown in the "Reports" section. Insights can also be made available to pages based on their package. Insights for pages show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. Using this, page admins will also be getting periodic, auto-generated emails containing Page insights. Settings for these can be configured from the "Insights Email Settings" and "Insights Graph Settings" sections in the admin panel.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo $this->translate("For the Graphical Insights of the Pages on my site, the dates shown do not match with the actual dates of the activities. For example, it shows up yesterday's or tomorrow's date in place of today's date. How should this be corrected ?");?></a>
					<div class='faq' style='display: none;' id='faq_25'>
						<?php echo $this->translate('Ans. This is happening because of the Locale Settings of your site. Please go to the "Settings" > "Locale Settings" section in the Admin Panel and set the \'Default Timezone\' according to your location.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo $this->translate("I am not able to view comments for the Pages in the insights displayed at Page Profile, graph, reports etc. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_26'>
						<?php echo $this->translate("Ans: Comments are shown at all these places only if Info widget is rendered on Page Profile as comments can be posted from there. You might have removed the Info widget from Page Profile. Please check it from the 'Layout' > 'Layout Editor' section.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
	<?php case 'import': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_50');"><?php echo $this->translate("I want to import many Pages altogether from a CSV file. Is that possible with this plugin ?");?></a>
					<div class='faq' style='display: none;' id='faq_50'>
						<?php echo $this->translate("Ans: Yes, you can import many pages at the same time from a CSV file. To do so, please go to the 'Import' section at the admin side of this plugin and read all the instructions given there under the section 'Import Pages from a CSV file' and then click on 'Import a file' link.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_51');"><?php echo $this->translate("I want to convert all the Listings of my site into Pages. How can it be done ?");?></a>
					<div class='faq' style='display: none;' id='faq_51'>
						<?php echo $this->translate("Ans: Yes, you can convert your Listings into Pages but there are some required conditions for that. Please go to the 'Import' section at the admin side of this plugin and fulfil all the required conditions given under the section 'Import Listings into Pages'. Then, you can import your Listings into Pages there.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_52');"><?php echo $this->translate("In the import section at admin side, I can not see any button or link to import Listings at my site into Pages. Why is it so ?");?></a>
					<div class='faq' style='display: none;' id='faq_52'>
						<?php echo $this->translate("Ans: There are some required conditions which are required to be fulfilled before starting to import Listings into Pages. when the condition becomes true or is fulfilled, it appears in green otherwise red. So, once all the conditions are green or fulfilled, the button for 'Import listings' will automatically appear there.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'language': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("I want to use this plugin for directory of cars. How can I change the word: 'pages' to 'cars' in this plugin?");?></a>
					<div class='faq' style='display: none;' id='faq_21'>
						<?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'pages' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_42');"><?php echo $this->translate("I want to change the text 'pageitems' to 'caritems' in the URLs of this plugin. How can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_42'>
						<?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the fields:<br />1) "Pages URL alternate text for \'pageitems\'" <br /> 2) "Pages URL alternate text for \'pageitem\'" <br /><br />Now, enter the text you want to display in place of \'pageitems\' and \'pageitem\' in the respective text boxes there.');?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_29');"><?php echo $this->translate("There are multiple languages on my site. How should this plugin be used for non-English languages?");?></a>
					<div class='faq' style='display: none;' id='faq_29'>
						<?php echo $this->translate("Ans : This plugin only comes with English language by default. For other languages, you need to copy the 'sitepage.csv' language file from the directory: '/application/languages/en/' of your site, to the directory '/application/languages/LANGUAGE_PACK_DIRECTORY/'. Then, go to the section 'Layout' > 'Language Manager' in the Admin Panel and edit phrases for the desired language.");?>
						</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
	<?php case 'customize': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_56');"><?php echo $this->translate("I want to remove some information from the 'Basic Information' section in the Info tab at the Page profile. How can I do so?");?></a>
					<div class='faq' style='display: none;' id='faq_56'>
						<?php echo $this->translate("Ans: You can do so by commenting the code corresponding to the sections you do not want to display in the following template file:<br />'application/modules/Sitepage/widgets/info-sitepage/index.tpl'");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_57');"><?php echo $this->translate("I want to customize the main menu for Directory Items/Pages. From where can I do this ?");?></a>
					<div class='faq' style='display: none;' id='faq_57'>
						<?php echo $this->translate("Ans: Yes, you can do so from the admin panel at: 'Layout' > 'Menu Editor' section. Open the 'Page Main Navigation Menu' to edit and then you can edit all the items of this menu there.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_41');"><?php echo $this->translate("I want to change the 'Pages' text in the mini-navigation bar for Directory Items/Pages. How can I do so ?");?></a>
					<div class='faq' style='display: none;' id='faq_41'>
						<?php echo $this->translate('Ans: To do this, follow the steps below:<br /><br />1) Please open the file with path: <br />"application/modules/Sitepage/views/scripts/payment_navigation_views.tpl"<br /><br />2) Now, change the word "Pages" to anything you want at line no. 15 (approx) in this file.<br /><br />It will change the text "Pages" to as desired in the mini navigation bar for Directory/Pages.');?>
						</div>
				</li>
				<li>
				  <a href="javascript:void(0);" onClick="faq_show('faq_33');"><?php echo $this->translate("How can I improve search engine optimization (SEO) for the main directory item / page profile?");?></a>
				  <div class='faq' style='display: none;' id='faq_33'>
					  <?php echo $this->translate("Ans: A quick and easy way to improve SEO for page profile is to manually do the template level changes as detailed below (Please note that these changes will need to be re-done when you upgrade SocialEngine on your site.):");?>
					  <br /><br />
					  <?php echo $this->translate('1) 2 easy changes in the template file : "/application/modules/Core/widgets/container-tabs/index.tpl":'); ?>
					  <br /><br />
					  <?php echo $this->translate('i) Open the template file and find around line 50:'); ?>
					  <br /><br />
					  <div class="code">
						  <?php echo '&lt;li class="&lt;?php echo $class ?&gt;"&gt;&lt;a href="javascript:void(0);" onclick="tabContainerSwitch($(this), \'&lt;?php echo $tab[\'containerClass\'] ?&gt;\');"&gt;&lt;?php echo $this->translate($tab[\'title\']) ?&gt;&lt;?php if( !empty($tab[\'childCount\']) ): ?&gt;&lt;span&gt;(&lt;?php echo $tab[\'childCount\'] ?&gt;)&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/a&gt;&lt;/li&gt;'; ?>
					  </div>
					  <br />
					  <?php echo $this->translate('Replace the above code with the below code:') ?>
					  <br /><br />
					  <div class="code">
						  <?php echo '&lt;li class="&lt;?php echo $class ?&gt;"&gt;&lt;a href="&lt;?php echo  Zend_Controller_Front::getInstance()->getRequest()->getRequestUri()."/tab/".$tab[\'id\']?&gt;" onclick="tabContainerSwitch($(this), \'&lt;?php echo $tab[\'containerClass\'] ?&gt;\'); return false;"&gt;&lt;?php echo $this->translate($tab[\'title\']) ?&gt;&lt;?php if( !empty($tab[\'childCount\']) ): ?&gt;&lt;span&gt;(&lt;?php echo $tab[\'childCount\'] ?&gt;)&lt;/span&gt;&lt;?php endif; ?&gt;&lt;/a&gt;&lt;/li&gt;'; ?>
					  </div>
					  <br />
					  <?php echo $this->translate('2) 1 easy addition in the CSS file : "application/modules/Core/externals/style/main.css":'); ?>
					  <br /><br />
					  <?php echo $this->translate('Open this CSS file and add the below code at the end of this file:'); ?>
					  <div class="code">
					  <?php echo $this->translate('.tab_pulldown_contents > ul > li >a, .tab_pulldown_contents > ul > li >a:hover{<br />
		  color: $theme_tabs_font_color;<br />
		  text-decoration:none;<br />
	  }'); ?>
					  </div>
					  <br /><br />
					  <?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?>
				  </div>
				</li>
				<li>

				  <a href="javascript:void(0);" onClick="faq_show('faq_70');"><?php echo $this->translate("Can I change the Featured label for the Pages created on my site? If yes then how?");?></a>
				  <div class='faq' style='display: none;' id='faq_70'>
				    <?php echo $this->translate('Ans: Yes, you can change the look of the Featured label for the Pages on your site. You can customize Featured label for your site by replacing the image in the file at the path "application/modules/Sitepage/externals/images/featured-label.png".');?>
				  </div>
				</li>
				<li>
				  <a href="javascript:void(0);" onClick="faq_show('faq_71');"><?php echo $this->translate("Can I customize the icon for Featured Pages on my site? If yes then how?");?></a>
				  <div class='faq' style='display: none;' id='faq_71'>
				    <?php echo $this->translate('Ans: Yes, you can customize the icons for Featured Pages on your site by replacing the image in the file at the path "application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif".');?>
				  </div>
				</li>


			</ul>
		</div>
	<?php break; ?>

	<?php case 'integration': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_53');"><?php echo $this->translate("I have installed the Pages Discussions extension plugin on my site. But I can not find it in the 'Plugins' dropdown at the admin panel. Why should I do ?");?></a>
					<div class='faq' style='display: none;' id='faq_53'>
						<?php echo $this->translate("Ans: Discussion for Directory/Pages is a FREE extension plugin. Hence, it does not have admin section of its own and gets automatically enabled once you install it.<br />Its settings are controlled from the admin section of Directory/Pages Plugin only.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_54');"><?php echo $this->translate("I do not find disscussion section in my pages. Where can I enable it ?");?></a>
					<div class='faq' style='display: none;' id='faq_54'>
						<?php echo $this->translate("Ans: To enable disscussions for Pages, please go to the 'Manage Packages' section of this plugin (if packages are enabled from Global Settings) and edit the packages one by one for which you want to enable Discussions for Pages and select the checkbox corresponding to the 'Discussions' in the 'Modules / Apps' field.<br /><br />If Packages are disabled from Global Settings section of this plugin, then 'Discussions' depend on the Comments privacy. If comments are enabled for that member level then discussions are also otherwise not.");?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate("I have installed the Community Ads plugin and set the 'Advertise your Page Widget' and 'Sample Ad Widget' options as 'Yes' in the 'Ad Settings' section. But I am not able to view these widgets on Page Profile. What can be the reason?");?></a>
					<div class='faq' style='display: none;' id='faq_27'>
						<?php echo $this->translate('Ans: These widgets appear in the Info tab on Page Profile and will only be visible to Page admins. This widget is not shown to the Page admins if an Ad corresponding to that Page has been created. So, Please check this.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_55');"><?php echo $this->translate("I have installed the Community Ads plugin and then trying to create an Ad corresponding to a Page from the Page profile, then I am redirected to a Page saying that there are no Ad packages available although I am having packages in my Ads Plugin. What can be the reason ?");?></a>
					<div class='faq' style='display: none;' id='faq_55'>
						<?php echo $this->translate("Ans: This happens when you have not enabled 'Page' for any of the Ad Packages available on your site. You can do so by going to the 'Manage Packages' section of Community Ads Plugin and then editing the Package which you want to be available while creating the Ads corresponding to the Pages. Now, for the field 'Content Advertised in this Package', select the 'Page' option too in the multi-select box there.<br />In this way, that package would then be available when you will try to create an Ad corresponding to any Page from the Page Profile.");?>
					</div>
				</li>
				<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_30');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature and the Pages Recommendations widget in this plugin. What can be the reason?");?></a>
				<div class='faq' style='display: none;' id='faq_30'>
					<?php echo $this->translate('These features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_31');"><?php echo $this->translate("I want to improve the Likes functionality of this plugin. How can that be done?");?></a>
				<div class='faq' style='display: none;' id='faq_31'>
					<?php echo $this->translate('Ans: You may install the %1$sLikes Plugin and Widgets%2$s on your site to incorporate its integration with this plugin.', '<a href="http://www.socialengineaddons.com/socialengine-likes-plugin-and-widgets" target="_blank">', '</a>');?>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" onClick="faq_show('faq_32');"><?php echo $this->translate("I want to enhance the Pages on my site and provide more features to my users. How can I do it?");?></a>
				<div class='faq' style='display: none;' id='faq_32'>
					<?php echo $this->translate('Ans: There are various apps / extensions available for the "Directory / Pages Plugin" which can enhance the Pages on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-pages-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-pages-extensions</a>');?>
				</div>
			</li>
			</ul>
		</div>
	<?php break; ?>
<?php endswitch; ?>
