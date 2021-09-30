<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
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

<style type="text/css">
    .admin_seaocore_files li li{
        list-style-type: none;
    }
</style>

<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">  
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How can I start creating Projects after the setup of this plugin?"); ?></a>
            <div class='faq' style='display: none;' id='faq_1'>
                <?php echo $this->translate("After installing the plugin, follow below steps: <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("1. Configure “Global Settings”, then various “Member Level Settings”."); ?></li> 
                <li><?php echo $this->translate("2. Enable and configure payment gateways:<br>  - PayPal: Go to ‘Billing’ → ‘Gateways’ to enable and configure this payment gateway. [Note: This is the default payment gateway and is not dependent on any other plugin]
                    <br>  - Stripe and Mangopay: Go to “Advanced Payment Gateways / Stripe Connect Plugin” to enable and configure these payment gateways. [Note: Dependent on Advanced Payment Gateways / Stripe Connect Plugin.]"); ?></li>
                <li><?php echo $this->translate("3. Now, come back to “Crowdfunding / Fundraising / Donations Plugin” and configure settings related to the enabled payment gateways from the “Payment Settings” section available in the admin panel of this plugin."); ?></li>
                 <li><?php echo $this->translate("4. Add various shipping locations to be available for sending rewards to backers from “Shipping Locations” section available in the admin panel."); ?></li>
                 <li><?php echo $this->translate("5. Create packages with different features to be available to site users while creating a project. (Optional)"); ?></li>
                 <li><?php echo $this->translate("6. Now, click on “Create a Project” link from the user section to start creating projects."); ?></li>
                </ul>

            </div>
            </li>

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("What are the various restrictions I can set on the Projects being created on my website?"); ?></a>
                <div class='faq' style='display: none;' id='faq_2'>
                <?php echo $this->translate("You can set below restrictions for the Projects being created on your website: <br/>"); ?><br>
                <ul>
                <?php echo $this->translate("Case I: On the Basis of Packages"); ?><br>
                <li><?php echo $this->translate("1. Go to “Package” → “Manage Packages” section available in the admin panel."); ?></li> 
                <li><?php echo $this->translate("2. Disable ‘Auto-Approve’ from the packages available on your website. You can also disable this setting while creating a new package."); ?></li>
                <li><?php echo $this->translate("3. Now, only site admin can approve / disapprove a project after going through it thoroughly."); ?></li>
                </ul>
                <br>
                <ul>
                <?php echo $this->translate("Case II: On the Basis of Member Level Settings <br/>"); ?>
                 <li><?php echo $this->translate("1. Go to “Member Level Settings” section available in the admin panel."); ?></li>
                 <li><?php echo $this->translate("2. Select the member level to enable / disable various project’s feature for that member level."); ?></li>
                 <li><?php echo $this->translate("3. Now, enable / disable various settings related to project like: creation, deletion, commenting, discussion topics, count of projects etc."); ?></li>
                 <?php echo $this->translate("[Note: Enabling various restrictions on projects will allow only genuine projects to be created on your website. There by benefitting the interested backers and your site’s reputation.] <br/>"); ?> 
                </ul>

            </div>
            </li>

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("What are the steps for a Project Owner to make his project live?"); ?></a>
            <div class='faq' style='display: none;' id='faq_3'>
                <?php echo $this->translate("Below are the steps for a Project Owner to make his project live: <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("[Note: Project Owners can also go through the ‘FAQs for Project Owners’ page before starting a new project.]"); ?></li> 
                <li><?php echo $this->translate("1. Go to project creation page by clicking on “Create a Project”."); ?></li>
                <li><?php echo $this->translate("2. Select a package as per the features you want to associate with a project."); ?></li>
                <li><?php echo $this->translate("3. Fill all the details accurately like: title, category, location, description, project duration, funding goal, project status etc."); ?></li>
                 <li><?php echo $this->translate("4. Now, make payment for the package you have selected to make your project live."); ?></li>
                 <?php echo $this->translate("[Note: Project will not be visible to anyone and anywhere until payment for the package has been done.] <br/>"); ?>
                 <li><?php echo $this->translate("5. Configure payment gateways, which you want to be available for the backers, from the dashboard of the project."); ?></li>
                 <li><?php echo $this->translate("6. Create various rewards which can be selected by backers while funding your project."); ?></li>
                  <?php echo $this->translate("[Note: Reward creation is optional.] <br/>"); ?> 
                </ul>

            </div>
            </li>


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I am unable to edit my Project. What might be the reason behind it?"); ?></a>
            <div class='faq' style='display: none;' id='faq_4'>
                <?php echo $this->translate("You are unable to edit your project because of possible below reasons: <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("1. Published Project: When a project is in draft mode, it is not finalized so all the details related to that project are editable. But, once a project is published and is backed by even a single backer then few fields are non editable like: Project Duration and Funding Amount."); ?></li> <br>
                <li><?php echo $this->translate("2. Member Level Settings: ‘Allow Editing of Projects?’ is disabled for the member belonging to the particular member level."); ?></li>
                <li><?php echo $this->translate("If Project Owner still wants to edit the published project then he can contact to the site admin. Site admin can take proper action in such scenario and do the needful changes."); ?></li> 
                </ul> 
            </div>
            </li>

             <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I am unable to delete my Project. What might be the reason behind it?"); ?></a>
            <div class='faq' style='display: none;' id='faq_5'>
                <?php echo $this->translate("You are unable to delete your project because of possible below reasons: <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("1. Project Backer’s: When the project is funded even by a single backer."); ?></li> 
                <li><?php echo $this->translate("2. Member Level Settings: ‘Allow Deletion of Projects?’ is disabled for the member belonging to the particular member level."); ?></li>
                <li><?php echo $this->translate("If Project Owner still wants to delete the project then he can contact to the site admin. Site admin can take proper action in such scenario like: to refund the backed amount to the respective backer and delete the project."); ?></li> 
                 <li><?php echo $this->translate("[Note: In case, no one has backed the project or the project is in draft mode, then Project Owner can delete that project.]"); ?></li> 
                </ul> 
            </div>
            </li>

             <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("How can I change the background image of Categories Home Page?"); ?></a>
            <div class='faq' style='display: none;' id='faq_6'>
                
                <ul>
                <li><?php echo $this->translate("1. Go to “Layout” → “File & Media Manager” section available in the admin panel."); ?></li> 
                <li><?php echo $this->translate("2. Here you can upload images."); ?></li>
                <li><?php echo $this->translate("3. You can set any uploaded image as background image of Categories Home Page by configuring the setting of “Project Categories: Displays Category with Background Image Slideshow” widget."); ?></li> 
                </ul> 
            </div>
            </li> 

             <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("What are the various plugins which I can integrate with this plugin? And how can I integrate these plugins with Crowdfunding / Fundraising / Donations  plugin?"); ?></a>
            <div class='faq' style='display: none;' id='faq_7'>
                <?php echo $this->translate("You can integrate this plugin with below plugins: <br/>"); ?>
                <ul>
                <li><?php echo"1."; ?><a href="https://www.socialengineaddons.com/socialengine-advanced-events-plugin" target="_blank"><?php echo $this->translate("Advanced Events Plugin"); ?></a></li>
                <li><?php echo"2."; ?><a href="https://www.socialengineaddons.com/socialengine-groups-communities-plugin" target="_blank"><?php echo $this->translate("Groups / Communities Plugin"); ?></a></li>
                <li><?php echo"3."; ?><a href="https://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank"><?php echo $this->translate("Directory / Pages Plugin"); ?></a></li>
                <li><?php echo"4."; ?><a href="https://www.socialengineaddons.com/socialengine-directory-businesses-plugin" target="_blank"><?php echo $this->translate("Directory / Businesses Plugin"); ?></a></li>
                <li><?php echo"5."; ?><a href="https://www.socialengineaddons.com/socialengine-multiple-listing-types-plugin-listings-blogs-products-classifieds-reviews-ratings-pinboard-wishlists" target="_blank"><?php echo $this->translate("Multiple Listing Types Plugin - Listings, Blogs, Products, Classifieds, Reviews & Ratings, Pinboard, Wishlists, etc All In One"); ?></a></li>
                <br>   
                <li><?php echo $this->translate("To integrate various plugins with this plugin, follow below steps:"); ?></li>
                 <li><?php echo $this->translate("1. Install the plugins which you want to integrate with this plugin."); ?></li> 
                <li><?php echo $this->translate("2. Go to “Manage Modules” section available in the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("3. Enable / disable various modules from here."); ?></li>
                 <li><?php echo $this->translate("4. Go to the “Member Level Settings” of the enabled an integrated module to set project related settings on a per member level basis for that module."); ?></li> 
                </ul> 
            </div>
            </li> 

             <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I want to change the color of ‘Backed Amount Progress Circle’ visible on cover photo of Project Profile page. How can I do so?"); ?></a>
            <div class='faq' style='display: none;' id='faq_9'> 
                <ul>
                <li><?php echo $this->translate("1. Go to the “Global Settings” > “General Settings” section available in the admin panel of this plugin."); ?></li> 
                <li><?php echo $this->translate("2. Choose desired color from the rainbow in front of ‘Backed Amount Progress Circle’."); ?></li>
                <li><?php echo $this->translate("[Note: This setting will work only when ‘Content Cover Photo and Information’ widget is placed on Project Profile page.]"); ?></li> 
                </ul> 
            </div>
            </li>  


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to limit the fields for Project Owners while creating a new Project. From where I can do the same?"); ?></a>
            <div class='faq' style='display: none;' id='faq_10'> 
            <?php echo $this->translate("Follow below steps to limit the fields for Project Owners while creating a new project: <br/>"); ?><br>
                <ul>
                <li><?php echo $this->translate("1. Go to ‘Global Settings’ → ‘Miscellaneous Settings’ section in the admin panel of this plugin."); ?></li> 
                <li><?php echo $this->translate("2. Enable / disable various project creation fields."); ?></li>
                </ul> 
            </div>
            </li> 


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("What are various way to set commission per Project on Project creation?"); ?></a>
            <div class='faq' style='display: none;' id='faq_11'>
                <?php echo $this->translate("There are two cases in which you can set commission per Project on Project Creation: <br/>"); ?><br>
                <ul>
                <li><?php echo $this->translate("Case I: Packages Enabled"); ?></li> 
                <li><?php echo $this->translate("- Go to “Packages” → “Manage Packages” section available in the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("- You can set commission type and its value depending on various packages available on your website."); ?></li>
                <li><?php echo $this->translate("- Set commission type while creating a new package or edit the existing package."); ?></li> <br>
                <li><?php echo $this->translate("Case II: Packages Disabled"); ?></li> 
                <li><?php echo $this->translate("- Go to “Member Level Settings” section available in the admin panel of this plugin."); ?></li> 
                <li><?php echo $this->translate("- You can set commission type and its value depending on various member levels available on your website."); ?></li> 
                </ul> 
            </div>
            </li>

             <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("How can I configure various locations to be available as ‘Shipping Locations’ for rewards of different Projects?"); ?></a>
            <div class='faq' style='display: none;' id='faq_12'>
                <?php echo $this->translate("There are two ways to configure various locations to be available as ‘Shipping Locations’ for rewards of different Projects: <br/>"); ?>
                <ul><br>
                <li><?php echo $this->translate("Case I: Add Single Location"); ?></li> 
                <li><?php echo $this->translate("- Go to “Shipping Locations” section available in the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("- Click on ‘Add Location’ to add a location from the available list of locations."); ?></li><br>
                <li><?php echo $this->translate("Case II: Add Location in Bulk"); ?></li> 
                <li><?php echo $this->translate("- Go to “Shipping Locations” section available in the admin panel of this plugin."); ?></li> 
                <li><?php echo $this->translate("- Click on ‘Import Locations’. From here, you can download the CSV template file, fill the location names and their code. Save the file and import the locations via ‘Import Locations’."); ?></li>  
                </ul> 
            </div>
            </li>

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("What are the various payment methods available that can be implemented for transactions related to Projects?"); ?></a>
            <div class='faq' style='display: none;' id='faq_13'>
                <?php echo $this->translate("There are three payment methods which can be implemented for transactions related to Projects: <br/><br/>"); ?>
                <ul>
                <li><?php echo $this->translate("1. Normal: The amount funded by a backer in a project will get added wither in the Project Owner or the Site Owner’s account. Other person can request for their share accordingly. Like, site owner can set a threshold value after which project owner has to pay commission to him, or, project owner can request site owner to give him funded amount after deducting his commission."); ?></li> <br/>
                <li><?php echo $this->translate("2. Split Immediately: The amount funded by a backer in a project will get immediately distributed between the Project Owner and the Site Owner. This payment method is useful if you don’t want any restriction on Project Owners, as they will get complete funded amount irrespective of the success / failure of their projects."); ?></li> <br/>
                <li><?php echo $this->translate("3. Escrow: The amount funded by a backer in a project will be kept on hold for a certain period of time or until the Project gets completed. This payment method is useful if you want restriction on Project Owners, like they will get complete funded amount only when their projects have successfully reached their goal in a defined period of time."); ?></li> 
                </ul> 
            </div>
            </li>


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("What are the various payment options in ‘Escrow’ payment method? How payout and refund of backed amount carried out?"); ?></a>
            <div class='faq' style='display: none;' id='faq_14'>
                <?php echo $this->translate("There are two types of payment options in ‘Escrow’ payment method as follows: <br/>"); ?>
                <ul><br>
                <li><?php echo $this->translate("1. Automatic: Complete backed amount (after deducting the package and other commissions) of the successful project will be transferred to the Project Owner’s account automatically."); ?></li> <br>
                <li><?php echo $this->translate("2. Manual: Site admin will transfer complete backed amount (after deducting the package and other commissions) of the successful project manually from “Manage Projects” → “Payout” section available in the admin panel."); ?></li><br/>
                <li><?php echo $this->translate("Above payment options are for projects which have reached their goal in the defined set of time. In case, a few projects gets failed i.e. they have not reached their goal in the defined set of time, then you can opt for below action to be taken for the backed amount:"); ?></li><br>
                <li><?php echo $this->translate("1. Payout: To transfer the backed amount to the Project owner even though his project didn't reach its goal. "); ?></li> <br>
                <li><?php echo $this->translate("2. Refund: To return the backed amount to the backers of that project."); ?></li> <br>
                <li><?php echo $this->translate("For “Automatic” payment option, Payout is done automatically. But, in case of “Manual” payment option, go to “Manage Project” section in the admin panel and perform any action Payout or Refund for a particular project."); ?></li>  
                </ul> 
            </div>
            </li>
            
             <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("Is it possible for a project to be funded more than the set goal amount?"); ?></a>
            <div class='faq' style='display: none;' id='faq_15'> 
                <ul>
                <li><?php echo $this->translate("Yes, it is possible for a project to be funded more than the set goal amount or more than 100%."); ?></li>  
                </ul> 
            </div>
            </li>


              <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Is it possible to run a Project without creating any rewards in it? I am unable to find the link to create rewards in my Project, from where I can do the same?"); ?></a>
            <div class='faq' style='display: none;' id='faq_16'>
                <?php echo $this->translate("Yes, it is possible to run a Project without creating any rewards in it. There is always one option to back the project with any desired amount and that is without selecting any rewards. <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("To create rewards in a project, follow below steps:"); ?></li> 
                <li><?php echo $this->translate("1. Open the profile page of the project."); ?></li>
                <li><?php echo $this->translate("2. Now, go to the dashboard of this project."); ?></li>
                <li><?php echo $this->translate("3. Click on “Rewards” from the options displaying on left side of the dashboard page."); ?></li> 
                <li><?php echo $this->translate("4. Create / edit / delete various rewards from here."); ?></li>  
                </ul> 
            </div>
            </li>

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("I am unable to edit / delete my reward of my Project. What might be the reason behind it?"); ?></a>
            <div class='faq' style='display: none;' id='faq_17'><br>
                <?php echo $this->translate("You are unable to edit / delete reward of your project because of possible below reasons: <br/>"); ?><br>
                <ul>
                <li><?php echo $this->translate("1. Reward Selected: Once a reward is selected by even a single backer then few fields become non editable like: Backed Amount, Estimated Delivery, Shipping Details and Reward Quantity."); ?></li> <br>
                <li><?php echo $this->translate("2. Project Completed: Once a project has reached its goal in defined set of time, rewards of these projects cannot be edited or deleted whether it has been selected any backer or not."); ?></li>
                <li><?php echo $this->translate("[Note: If Project Owner still wants to edit / delete the selected reward then he can contact to the site admin. Site admin can do the needful changes.]"); ?></li> 
                </ul> 
            </div>
            </li>

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("How can I use this plugin to create donation based projects?"); ?></a>
            <div class='faq' style='display: none;' id='faq_18'>
                <?php echo $this->translate("To use this plugin for creating donation based projects, you need to set: <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("1. Time Duration: You can set project duration to life time (i.e. at max 5years). This project duration can be set from ‘Member Level Settings’ or ‘Package’, to do so follow below steps:"); ?></li> <br>
                <li><?php echo $this->translate("Case 1: When packages are enabled"); ?></li>
                <li><?php echo $this->translate("a) Go to ‘Packages’ → ‘Manage Packages’ section from the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("b) Set the project duration by editing the existing packages or while creating a new package."); ?></li><br>
                <li><?php echo $this->translate("Case 2: When packages are disabled"); ?></li>
                <li><?php echo $this->translate("a) Go to ‘Member Level Settings’ section from the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("b) Select the desired member level."); ?></li>
                <li><?php echo $this->translate("c) Set the project duration to life time for the selected member level."); ?></li><br>
                <li><?php echo $this->translate("2. Payment Method: You can set either ‘Normal’ or ‘Split-Immediately’ payment method for donation based projects. To do so, follow below steps:"); ?></li> 

                <li><?php echo $this->translate("a) Go to ‘Payment Settings’ section from the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("b) Select ‘Normal’ or ‘Split-Immediately’ payment method."); ?></li>
                <li><?php echo $this->translate("c) Configure further settings accordingly."); ?></li>
                <li><?php echo $this->translate("[Note: ‘Escrow’ payment method is not used for recommended here as for this case project duration cannot exceed 90 days. ]"); ?></li>  
                </ul> 
            </div>
            </li>

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("How can I set project duration as life time for the projects being created on my website?"); ?></a>
            <div class='faq' style='display: none;' id='faq_19'>
                <?php echo $this->translate("Follow below steps to set project duration as life time: <br/>"); ?>
                <ul><br>
                <li><?php echo $this->translate("Case 1: When packages are enabled"); ?></li> 
                <li><?php echo $this->translate("a) Go to ‘Packages’ → ‘Manage Packages’ section from the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("b) Create / edit a package to set the project duration to life time."); ?></li><br>
                <li><?php echo $this->translate("Case 2: When packages are disabled"); ?></li>
                <li><?php echo $this->translate("a) Go to ‘Member Level Settings’ section from the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("b) Select the desired member level."); ?></li>

                <li><?php echo $this->translate("c) Set the project duration to life time for the selected member level."); ?></li>
                <li><?php echo $this->translate("[Note: Escrow payment method will not work for projects with life time project duration.]"); ?></li> 
                </ul> 
            </div>
            </li> 


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("What are the various payment status available in ‘Manage Backers’ section and what are they implying to?"); ?></a>
            <div class='faq' style='display: none;' id='faq_20'>
                <?php echo $this->translate("Various payment status available in ‘Manage Backers’ section in the admin panel are: <br/>"); ?><br>
                <ul>
                <li><?php echo $this->translate("1. Okay:  Payment process is complete. Amount has been deducted from project backer’s account and is received by the project owner."); ?></li> <br>
                <li><?php echo $this->translate("2. Preapproved: Only pre-approval of payment will be taken from project’s backer. After completion of project, payout or refund will take place from the backer’s account."); ?></li><br>
                <li><?php echo $this->translate("3. Pending: Payment done by project backer is in pending state. The reason behind it can be  delay in payment gateway response."); ?></li><br>
                <li><?php echo $this->translate("4. Failed: Payment process has failed. The reason behind it can be: failing of payment process in middle, backer has changed his mind in between the payment process, backer does not have sufficient balance in his account or any other possible reason."); ?></li> 
                </ul> 
            </div>
            </li>  


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("How can I set up landing page of my site similar to the Crowdfunding Demo site?"); ?></a>
            <div class='faq' style='display: none;' id='faq_21'>
                <?php echo $this->translate("To set up the landing page of your site similar to the ");?>
                <a href="http://demo.crowdfunding.socialengineaddons.com" target="_blank">
                    <?php echo $this->translate("Crowdfunding Demo site");?>
                </a>
                <?php echo $this->translate(", follow below steps: <br/>"); ?>
                <ul>
                <li><?php echo $this->translate("1. Go to ‘Landing Page Setup’ section in the admin panel of this plugin."); ?></li>
                <li><?php echo $this->translate("2. Click on ‘Yes’ and save the setting to set the layout of your site similar to the"); ?>
                <a href="http://demo.crowdfunding.socialengineaddons.com" target="_blank">
                    <?php echo $this->translate("Crowdfunding Demo site");?>
                </a>
                <?php echo $this->translate("site."); ?>
                </li><br>  
                
                <li><?php echo $this->translate("Below are the list of widgets which are required to set up this landing page:"); ?></li> <br>

                <li><?php echo $this->translate("1. Home Page Background Videos & Photos - Landing Page Videos & Photos [Dependent on"); ?>
                <a href="https://www.socialengineaddons.com/socialengine-home-page-background-videos-photos-plugin" target="_blank">
                <?php echo $this->translate("Home Page Background Videos & Photos");?>
                </a>
                <?php echo $this->translate("Plugin]"); ?>
                </li> 
                <li><?php echo $this->translate("2. Landing Page: Featured Projects"); ?></li>
                <li><?php echo $this->translate("3. Landing Page: Sponsored Categories With Image"); ?></li>
                <li><?php echo $this->translate("4. Landing Page: Best Projects Carousel"); ?></li>
                <li><?php echo $this->translate("5. Ajax Based Main Projects Home Widget"); ?></li><br> 
                </ul> 
            </div>
            </li> 

            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("Is it possible to start donation on my event / group / page / business / listing without redirecting members to the associated Project?"); ?></a>
            <div class='faq' style='display: none;' id='faq_22'>
                <?php echo $this->translate("Yes, it is possible to start donation on your event / group / page / business / listing without redirecting members to the associated Project. This feature can be used in two ways, which are as User Driven and Admin Driven. Follow below steps to do so:");?> 
                <ul><br>
                <li><?php echo $this->translate("User Driven"); ?></li><br>
                <li><?php echo $this->translate("- Go to the dashboard of your event / group / page / business / listing."); ?> 
                </li><br>  
                
                <li><?php echo $this->translate("- Now, click on ‘Projects’ from the dashboard’s menu."); ?></li> <br>

                <li><?php echo $this->translate("- From the auto-suggest, user can select one project from the list of his projects."); ?>
                </li> <br>
                <li><?php echo $this->translate("- User can also set text for the ‘Back Project’ button to make it more suitable with the associated plugin. [Example: For a page belonging to an NGO, ‘Donate Now!’ text is more suitable.]"); ?></li><br><br>
                <li><?php echo $this->translate("Admin Driven"); ?></li><br>
                <li><?php echo $this->translate("- Go to the widgetized profile page of any integrated plugin."); ?></li><br>
                <li><?php echo $this->translate("- Place ‘Back Project’ button on the respective page."); ?></li><br> 
                <li><?php echo $this->translate("- Edit the widget and select a Project with the help of auto-suggest from the list of all the live Projects on your website. Save the settings."); ?></li><br>
                <li><?php echo $this->translate("- The selected project will be linked to the ‘Back Project’ button."); ?></li><br>
                <li><?php echo $this->translate("[Note: If admin has associated any project from the widget then ‘Projects’ tab will no longer be available for user. Only one can be authorized to associate a project with any integrated plugin’s profile page.]"); ?></li> 
                </ul> 
            </div>
            </li> 


            <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("Can I associate a Project with other non-integrated plugins? If so then how?"); ?></a>
            <div class='faq' style='display: none;' id='faq_23'>
                <?php echo $this->translate("Yes, you can associate a Project with other non-integrated plugins. To do so, please follow below steps:");?>  
                <ul><br>
                <li><?php echo $this->translate("- Go the widgetized page where you want to associate a project for backing."); ?></li><br>
                <li><?php echo $this->translate("- Place the ‘Back Project’ button in right / left side column of the widgetized page."); ?> 
                </li><br>  
                
                <li><?php echo $this->translate("- Edit the widget and select a Project with the help of auto-suggest from the list of all the live Projects on your website. Save the settings."); ?></li> <br>

                <li><?php echo $this->translate("- The selected project will be linked to the ‘Back Project’ button."); ?>
                </li>   
                </ul> 
            </div>
            </li> 
 
    </ul>
</div>



 