<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	var assigned_previous_id = '<?php echo $this->assigned_previous_id; ?>';
	function startImport() 
	{	
    var import_confirmation =  confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to start importing Pages ?")) ?>');

		if(import_confirmation) {

			Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing Pages...")) ?>' + "</b><br /><img src='"+en4.core.staticBaseUrl+"application/modules/Sitepage/externals/images/loader.gif' alt='' /></center></div>");

			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl+'admin/sitepage/importlisting',
				method: 'get',
				data : {
					'start_import' : 1,
					'assigned_previous_id' : assigned_previous_id,
					'format' : 'json',
				},
				onSuccess : function(responseJSON) {
					
					$('import_button').style.display = 'none';
					$('importlisting_elements').style.display = 'none';
					
					if (responseJSON.assigned_previous_id < responseJSON.last_listing_id) {
						$('import_again_button').style.display = 'block';
						assigned_previous_id = responseJSON.assigned_previous_id;
						
						$('unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/cross.png);'>"+'<?php echo $this->string()->escapeJavascript($this->translate("Sorry for this inconvenience !!")) ?>' + "<br />"+'<?php echo $this->string()->escapeJavascript($this->translate("Importing is interrupted due to some reason. Please click on 'Import Again' button to start the importing from the same point again.")) ?>'+"</span><br />";
					}
					else {
						$('import_again_button').style.display = 'none';
						$('unsuccess_message').style.display = 'none';
						$('success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/notice.png);'>"+'<?php echo $this->string()->escapeJavascript($this->translate("Importing is done succesfully.")) ?>'+"</span><br />";
					}
					Smoothbox.close();
				}
			}))
		}
	}
</script>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'log', 'action' => 'index'), $this->translate('Import History'), array('class'=> 'buttonlink icon_sitepages_log')) ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'importlisting', 'action' => 'manage'), $this->translate('Manage CSV Import Files'), array('class'=> 'buttonlink icon_sitepage_admin_import_manage')) ?><br/><br/>

<?php if($this->first_listing_id): ?>
	<div class="importlisting_form">
		<div>
			<h3><?php echo $this->translate('Import Listings into Pages');?></h3>
			<p>
				<?php echo $this->translate("This Importing tool is designed to migrate content directly from a Listing to a Page. Using this, you can convert all the listings on your site into Pages. Please note that we try to import all the data corresponding to a Listing but there is a possibility of some data losses too.<br />Below are the conditions which are required to be true for this import. Please check the points carefully and if some condition is yet to be fulfilled then do that first and then start importing your Listings. Once the import gets started, it is recommended not to close the lightbox, otherwise it will not be completed successfully and some data losses may occur.");?>
			</p>
	
			<br />
			<div id="success_message" class='success-message'></div>
			<div id="unsuccess_message" class="error-message"></div>

			<?php if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.isActivate') || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')): ?>
				<div class="tip">
					<span>
						<?php  echo $this->translate("Directory/Pages - Videos Extension or Videos Plugin is either not installed or disabled at your site. So, this import tool will not be able to import the data corresponding to Listing Videos.") ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagereview.isActivate')): ?>
				<div class="tip">
					<span>
						<?php  echo $this->translate("Directory/Pages - Reviews and Rating Extension Plugin is not installed or disabled at your site, so listing import functionality will not import the listing review datas.") ?>
					</span>
				</div>
			<?php endif; ?>

			<div class="importlisting_elements" id="importlisting_elements" >
				<?php 
					$is_error = 0;
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('list')) {
						$error_msg1 = $this->translate("Listings / Catalog Showcase Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else{
						$is_error = 1;
						$error_msg1 = $this->translate("Listings / Catalog Showcase Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.isActivate')) {
						$error_msg1 = $this->translate("Directory/Pages Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else{
						$is_error = 1;
						$error_msg1 = $this->translate("Directory/Pages Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate')) {
						$error_msg1 = $this->translate("Directory/Pages - Photo Albums Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else{
						$is_error = 1;
						$error_msg1 = $this->translate("Directory/Pages - Photo Albums Extension Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagediscussion.isActivate')) {
						$error_msg1 = $this->translate("Directory/Pages - Discussion Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
					else{
						$is_error = 1;
						$error_msg1 = $this->translate("Directory/Pages - Discussion Extension Plugin is not installed or disabled!");
						echo "<span class='red'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagereview.isActivate')) {
						$error_msg1 = $this->translate("Directory/Pages - Reviews and Ratings Extension Plugin is installed and enabled!");
						echo "<span class='green'><img src='".  $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
			
					if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.isActivate') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {
						$error_msg1 = $this->translate("Directory/Pages - Videos Extension and Videos Plugin is installed and enabled!");
						echo "<span class='green'><img src='". $this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/><b>$error_msg1</b></span>";
					}
			
				?>
			</div>
	
			<div id="import_button" class="import_button">
				<?php if($is_error == 0): ?>
					<button type="button" id="continue" name="continue" onclick='startImport();'>
						<?php echo $this->translate('Start Import');?>
					</button>
				<?php endif;?>
			</div>
	
			<div id="import_again_button" style="display:none;" class="import_button">
				<?php if($is_error == 0): ?>
					<button type="button" id="continue" name="continue" onclick='startImport();'>
						<?php echo $this->translate('Import Again');?>
					</button>
				<?php endif;?>
			</div>
		</div>
	</div>
<?php endif;?>

<div class="importlisting_form">
	<div>
		<h3><?php echo $this->translate('Import Pages from a CSV file');?></h3>

		<p>
		 <?php echo "This tool allows you to import Pages corresponding to the entries from a .csv file. Here, you can also generate your own .csv template file by selecting the Profile Fields to be included in it by using the “Generate new CSV template file” link below. Before starting to use this tool, please read the following points carefully. ";?>
		</p>

		<ul class="importlisting_form_list">

			<li>
				<?php echo $this->translate("Don't add any new column in the csv file from which importing has to be done.");?>
			</li>

			<li>
				<?php echo $this->translate("The data in the files should be pipe('|') separated and in a particular format or ordering. So, there should be no pipe('|') in any individual column of the CSV file . If you want to add comma(',') separated data in the CSV file, then you can select the comma(',') option during the CSV file upload process. Note: There is one drawback of using the comma(',') separated data that you will not be able to use comma in fields like description, address, price, overview etc. for the entries in the CSV file.");?>
			</li>

			<li>
				<?php echo $this->translate("Page title and category name are the required fields for all the entries in the file.");?>
			</li>

			<li>
				<?php echo $this->translate("For making pages claimable, admin should be added as 'Claimable Page Creators' from");?>
				<a href="<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'claim', 'action' => 'index'), 'admin_default', true) ?>"><?php echo $this->translate(" Manage Claims ") ?></a>
				<?php echo $this->translate("tab. Claim functionality also depends on Member level settings and Global settings. 'Claim a Page' value should be 1 or 0 in your csv file. Value 1 indicates the page is claimable and 0 indicates the page is not claimable.");?>
			</li>

			<li>
				<?php echo $this->translate("Category and sub-category name should exactly match with the existing categories and sub-categories, otherwise category or sub-category will be considered as null for that Page.");?>
			</li>
    <li>
      <?php echo $this->translate('Now, you can also import profile pictures for pages via the CSV file by following method:<br />
  a) Make a zipped folder containing all the photos to be uploaded and mention the name of the pictures in the csv file with associated page to be imported. You can later upload the folder from the “Manage CSV Import Files” section. [Note: To allow photos folder to be uploaded on your site successfully, please make sure that the php_zip extension is installed on your server.]');?>
    </li>
			<li>
				<?php echo $this->translate("In case you want to insert more than one tag for an entry, then the tags string should be seperated by hash('#'). For example, if you want to insert 2 tags for an entry - 'tag1' and 'tag2', then tag string for that will be 'tag1#tag2'.");?>
			</li>

			<li>
				<?php echo $this->translate("Before starting the importing process, it is recommended that you should create category, Profile Fileds and do Category-Profile mappings from the 'Category-Page Profile Mapping' section.");?>
			</li>

			<li>
				<?php echo $this->translate("You can import the maximum of 2000 pages at a time and if you want to import more, you would have to then repeat the whole process. For example, you have to import 3500 pages. Then, you would have to create 2 CSV files - one having 2000 entries and another having 1500 entries corresponding to the Pages. After that, just upload both the files and import pages from them one by one from 'Manage CSV Import Files' section.");?>
			</li>

			<li>
				<?php echo $this->translate("You can also 'Stop' and 'Rollback' the import process. 'Stop' will just stop the import process going on at that time from that file and 'Rollback' will undo or delete all the Pages created from that CSV import file till that time.");?>
			</li>

			<li>
				<?php echo $this->translate("Files must be in the CSV format to be imported. You can also download the demo template below for your reference.");?>
			</li>

		</ul>
		
		<br />

		<a href=<?php echo $this->url(array('action' => 'download-sample')) ?><?php echo '?path=' . urlencode('example_page_import.csv');?> target='downloadframe' class="buttonlink icon_sitepages_download_csv"><?php echo $this->translate('Download the CSV template')?></a>
  
  <a href="<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'fields', 'action' => 'show-customfields')) ?>" class="buttonlink icon_sitepage_generate_csv"><?php echo $this->translate('Generate new CSV template file')?></a> 
		
		<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepage', 'controller' => 'admin-importlisting', 'action' => 'import'), $this->translate('Import a file'), array('class' => 'smoothbox buttonlink icon_sitepages_import')) ?>
		
		<br />
		<br />
		
	</div>
</div>		
