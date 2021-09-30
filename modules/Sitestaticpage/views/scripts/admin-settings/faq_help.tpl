<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
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
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo "What is an HTML BLOCK?";?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo "An HTML Block enables you to display rich content created using WYSIWYG Editor in Static HTML Block widget from the Layout Editor."; ?>
			</div>
		</li>   
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo "How can I create an HTML Block?";?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo "To create an HTML Block, please follow the steps below:"?>
				<br />
				<?php echo 'Step 1: Go to the Manage Static Pages section of this plugin and click on the "Create New Static Page / HTML Block" link.
'; ?>	
        <br />
				<?php echo 'Step 2: From the field "Static Page or HTML Block", choose "HTML Block".'; ?>	
        <br />
				<?php echo 'Step 3: Fill in the details for your HTML Block.'; ?>	
        <br />
				<?php echo 'Step 4: Now, go to the Layout Editor and place the Static HTML Block widget at desired position on a widgetized page of your choice.'; ?>	
        <br />
				<?php echo 'Step 5: From the edit settings of this widget, choose the HTML Block widget you want to display in that widget.'; ?>	
			</div>
		</li>     
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo "How can I enable the Multi Languages in this plugin?";?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo 'You can enable Multi Languages from Global Settings section of this plugin. To do so, select "Yes" for the setting "Multiple Languages for Static Pages".';?>
			</div>
		</li>        
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo "Who receives the response for the forms filled by any users on a Static Page / HTML Block?";?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo "When you create a ‘Form’ from the ‘Manage Forms’ section of this plugin, then enter email addresses on which you want to receive the responses in the “EMAIL ADDRESSES” field. ";?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo "I have placed a 'Static HTML Block' widget on a widgetized page from the 'Layout Editor' section, but it is not getting displayed on that page. What would be the reason?";?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo "The 'Static HTML BLOCK' widget has 'Start Time' and 'End Time' setting. Please make sure that you are trying to view this widget during the chosen time period for this widget.";?>
			</div>
		</li>              
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo "I want my Static Pages to be displayed to users of only certain Member Level and / or Network. How can this be done?";?></a>
			<div class='faq' style='display: none;' id='faq_7'>
				<?php echo "This can be easily done. When you are creating a Static Page, there are two settings for selecting the Member Levels and Networks, to whom this page should be displayed. From there you can select the Member levels / Networks to whom this Static page should be visible.";?>
				<br /> <br />
			</div>
		</li>   
    <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo "I have installed this plugin and found 3 default HTML Blocks - Contact Us, Privacy Policy and Terms of Services. How can I use these blocks on the help pages of my site?";?></a>
			<div class='faq' style='display: none;' id='faq_8'>
				<?php echo "To use these HTML Blocks on the help pages of your site, please follow the steps below:"; ?>
        
        <br />
				<?php echo 'Step 1: Open the desired widgetized help page from the Admin >> Layout Editor.'; ?>	
         <br />
				<?php echo 'Step 2: Now, place the widget "Static HTML Block" at desired place.'; ?>
         <br />
				<?php echo 'Step 3: Choose appropriate HTML Block in the "Choose Content" field.'; ?>
          <br />
				<?php echo 'Step 4: Configure other settings of the widget and save your changes.'; ?>
			</div>
		</li>
    
      <li>
			<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo "How can I place an 'HTML Block' on a Non-widgetized page?";?></a>
			<div class='faq' style='display: none;' id='faq_9'>
				<?php echo "To do so, please follow the steps mentioned below:"; ?>
        
        <br />
				<?php echo 'Step 1: Open the desired file.'; ?>	
         <br />
				<?php echo 'Step 2: Copy and paste the below code at desired position in the file:';?>
         <br /><br />
				<?php echo htmlspecialchars('<?php $starttime = date("Y-m-d H:i:s");'); ?>
         <?php echo '<b> //Enter the appropriate values for the start date in date(). (where, Y- Year, m- Month, d- Date, H- Hour, i- Minute, s- Seconds)</b>'?>
         <br/>
         <?php echo '$htmlblock_id = X;'?>   
         <?php echo '<b> // Enter the desired HTML Block’s ID. (where X- ID of HTML Block)</b>';?>
          <br />
				<?php echo 'echo $this->content()->renderWidget("sitestaticpage.html-blocks", array("static_pages"=>X, "starttime"=>$starttime)); ?>'; ?>
        <?php echo '<b> // Enter the desired HTML Block’s ID. (where X- ID of HTML Block)</b>';?>
			</div>
		</li>
		
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo "The CSS of this plugin is not coming on my site. What should I do ?";?></a>
			<div class='faq' style='display: none;' id='faq_10'>
				<?php echo "Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.";?>
			</div>
		</li>
  </ul>
</div>