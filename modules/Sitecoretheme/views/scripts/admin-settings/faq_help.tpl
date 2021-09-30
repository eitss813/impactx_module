<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<script type="text/javascript">
    function faq_show(id) {
        if ($(id)) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    }
<?php if ($this->faq_id): ?>
        window.addEvent('domready', function () {
            faq_show('<?php echo $this->faq_id; ?>');
        });
<?php endif; ?>
</script>
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files sitecoretheme_faq">

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo "How can I set up different sections of landing page as per my requirement?"; ?></a>
            <div class='faq' style='display: none;' id='faq_1'>
                <?php
                $landingUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'slider'), 'admin_default', true);
                $highlightUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'highlights'), 'admin_default', true);
                $achievementUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'stats'), 'admin_default', true);
                $bannerUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'text-banner'), 'admin_default', true);
                $servicesUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'services'), 'admin_default', true);
                $actionUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'cta-buttons'), 'admin_default', true);
                $videoUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'video-banner'), 'admin_default', true);
                $bannerTagUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'text-banner'), 'admin_default', true);
                $bannerServiceUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'services'), 'admin_default', true);
                $informativeUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'blocks', 'action' => 'index'), 'admin_default', true);
                $markersUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'markers'), 'admin_default', true);
                $promotionUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'app-banner'), 'admin_default', true);
                ?>
                <?php
                echo "To set up different sections of landing page, please go to the ‘Landing Page’ section from the Admin panel of this theme. You will see below sub-sections there and you can configure these sub-sections as per your need:<br />
                  1. <a href='$landingUrl'>Landing Page Slider</a><br />
                  2. <a href='$highlightUrl'>Highlights Block </a><br />
                  3. <a href='$achievementUrl'>Achievement Block </a><br />
                  4. <a href='$videoUrl'>Video Banner </a><br />
                  5. <a href='$bannerTagUrl'>Banner Tagline </a><br />
                  6. <a href='$bannerServiceUrl'>Services Block </a><br />
                  7. <a href='$informativeUrl'>Informative Block </a><br />
                  8. <a href='$markersUrl'>Markers section </a><br />
                  9. <a href='$actionUrl'>Action Buttons </a><br />
                  10. <a href='$promotionUrl'>Promotional Banner</a>";
                ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo "Theme is not installed properly on my website, everything is scattered. What might be the problem?"; ?></a>
            <div class='faq' style='display: none;' id='faq_2'>
                <?php
                $url = $this->url(array('module' => 'sitecoretheme', 'controller' => 'settings', 'action' => 'place-htaccess-file'), 'admin_default', true);
                $genralSettingUrl = $this->url(array('module' => 'core', 'controller' => 'settings', 'action' => 'general'), 'admin_default', true);
                ?>
                <?php
                echo "It could be possible that Versatile Theme directory has missed the creation of 'customization.css' file. For resolving this, you need to create a customization.css file in '/application/themes/allure/', or, you can click here to create 'customization.css' file.";
                ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo "Can I add my custom CSS in this theme? If yes, then how I can add it so that my changes do not get lost in case of theme up-gradations?"; ?></a>
            <div class='faq' style='display: none;' id='faq_3'>
                <?php echo "Yes, you can add your custom CSS in this theme. We have created a new file 'customization.css' for you in this theme, which enables you to add your customized changes for your website. You can write your CSS code over here and make your site look just the way you want it to. Also, It will not lost in case of theme upgradation.You can find this file by following the below steps :<br /><br />
                  1. Go to the 'Appearance' >> 'Theme Editor' section from the admin panel of this theme.<br />
                  2. Now choose 'customization.css' from the 'editing file' dropdown. You may add the changes here which you want to do for your website.<br />
                  [Note: If you are unable to find this file in the 'editing file' drop down then please read the above FAQ.]"; ?>
            </div>
        </li>


        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo "Fonts are not appearing fine on my site. What could be the problem? How can I resolve this?"; ?></a>
            <div class='faq' style='display: none;' id='faq_4'>
                <?php
                $url = $this->url(array('module' => 'sitecoretheme', 'controller' => 'settings', 'action' => 'place-htaccess-file'), 'admin_default', true);
                $genralSettingUrl = $this->url(array('module' => 'core', 'controller' => 'settings', 'action' => 'general'), 'admin_default', true);
                ?>
                <?php
                echo "It is happening because you are using the 'Static File Base URL' setting in '<a href='$genralSettingUrl'>General Settings</a>' section of admin panel. To resolve this, you need to create .htaccess file over here: '/application/themes/sitecoretheme/', or, you can <a href='javascript:void(0)' onclick='Smoothbox.open(\"$url\");'>click here</a> to create .htaccess file.";
                ?>
            </div>
        </li> 

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo "Can I change the images of slider in the background on the landing page?"; ?></a>
            <div class='faq' style='display: none;' id='faq_6'>
                <?php echo "Yes, you can do so by following the below steps:<br />
                  1. Go to the 'Slider Images' → 'Landing Page Slider Images' section in the admin panel of this theme.<br />
                  2. Upload the images that you want to display on your landing page.<br />
                  3. You can also select the preferred images to show in landing page slider. To do so, go to 'Landing Page' → 'Landing Page Slider'. Select option \"Select the images\" under 'Slider Images' setting. Choose the images which you want to show in the slider.<br /><br />

                  [Note: You can upload multiple images to display them one after another as slideshow.]"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo "How can I change the below text displaying on image rotator?"; ?></a>
            <div class='faq' style='display: none;' id='faq_7'>
                <?php echo "\"Explore the world with us\" and \"A true social community is when you feel connected and responsible for what happens around.\"<br /> 
                Please follow below steps to change the text displaying on image rotator:<br />
                1.  Go to 'Landing Page' → 'Landing Page Slider' section available in the admin panel of this theme.<br />
                2. On scrolling down, you will get settings by names Slider Text and Slider Moving Text. Change the text corresponding to it.<br />
                3. Click on save changes."; ?>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo "How can I display logo on Landing Page?"; ?></a>
            <div class='faq' style='display: none;' id='faq_8'>
                <?php echo "To do so, please follow the below steps:<br />
                  1. Go to 'Appearance' → 'File & Media Manager' and upload the desired logo.<br />
                  2. Now, go to 'Plugins' → '".SITECORETHEME_PLUGIN_NAME."' → 'Landing Page' → 'Manage Header' → 'Landing Page header Widget Settings'. Here you will get setting \"Display Logo\". Select ‘Yes’ if you want to display the logo on the Landing Page.<br />
                  3. The setting \"Select Logo\" on the same page will allow you to select the uploaded logo from the drop down corresponding to it.<br />
                  4. Click on save changes."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo "I want to display an image rotator on inner pages of my website. Is it possible with this theme?"; ?></a>
            <div class='faq' style='display: none;' id='faq_9'>
                <?php echo "Yes, you can easily do it by using our ‘".SITECORETHEME_PLUGIN_NAME." - Inner Page Slider’ widget. If you want to to display banner image rotator on any inner page of your site, then please follow below steps:<br />
                  1. To upload your banner images go to 'Slider Images' → 'Inner Slider Page Images' and set the sequence of banner images by dragging-and-dropping them vertically. Multiple banner images can be added to display them in a circular manner, i.e one after another.<br />
                  2. Place this widget: '".SITECORETHEME_PLUGIN_NAME." - Inner Page Slider' on the widgetized page of your site and edit this widget settings to configure various options related to how to show banner images on that page. 
                  3. Click on save changes."; ?>
            </div>
        </li>


        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo "I want to set a background image in the body of my site. Is it possible with this theme?"; ?></a>
            <div class='faq' style='display: none;' id='faq_10'>
                <?php echo "Yes, you can set a background image in the body of your website to make your website more attractive and appealing. To do so, please go to the 'Global Settings' tab in the admin panel of this theme and select the image under setting 'Website's Body Background Image'.<br /><br />
                  
                  [Note: You can upload a new image from \"Appearance\" → \"File & Media Manager\"]"; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo "I want to change this footer text: 'Explore & Watch videos that…...'. How can I do so?"; ?></a>
            <div class='faq' style='display: none;' id='faq_11'>
                <?php echo "To do so, please follow the below steps:<br />
                  1. Go to the 'Manage Footer' → 'Footer Settings' tab available in the admin panel of this theme.<br />
                  2. Now, go to the 'Footer Title' setting and edit this text.<br />
                  3. Click on save changes."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo "Few pages of my website are not appearing fine because theme.css is not loading on my site. What should I do?"; ?></a>
            <div class='faq' style='display: none;' id='faq_12'>
                <?php echo "Please enable 'Development Mode' for your website from the 'Admin Panel' home page and check the pages which were not coming fine. It would be showing fine now and if everything seems fine change to 'Production Mode' again."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo 'I want profile pictures of members should be in circular shape. Is it possible? If yes, then how? Where else will it affect?'; ?></a>
            <div class='faq' style='display: none;' id='faq_13'>
                <?php echo "Yes, it is possible. To do so, please follow below steps:<br />
                  1. Go to 'Global Settings' section in the admin panel of this theme.<br />
                  2. Here, you will get setting by the name 'Member's Thumbnail Images in Circular Shape'. Click on \"Yes\".<br /> 
                  3. Click on save changes.<br /><br />
                  By doing this, the profile picture of the members will be in circular shape. This setting will not affect any other image / thumbnail of the website."; ?>
            </div>
        </li>


        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo "Currently, there is a section Promoting App Stores. I want to promote another page of mine. Is it possible?"; ?></a>
            <div class='faq' style='display: none;' id='faq_14'>
                <?php echo "Yes, it is possible to promote any other web page through this section. To do so, follow below steps:<br />
                  1. Go to 'Landing Page' → 'Promotion Banner' section in the admin panel of this theme.<br />
                  2. Here, you will get a setting namely \"Type of Promotion\".<br />
                  3. Select \"Other Promotion via Link\".<br />
                  4. Fill related information in the given input boxes like: \"Other Promotional URL\" and \"Other Promotional Text\".<br />
                  5. Click on save changes."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo "How can I create a new customized color scheme for my website?"; ?></a>
            <div class='faq' style='display: none;' id='faq_15'>
                <?php echo "To do so, proceed with the mentioned steps:<br />
                  1. Go to 'Theme Customization' → 'Color Editor'.<br />
                  2. Click on \"New Custom Scheme\".<br />
                  3. Enter theme title and description.<br />
                  4. Select the color scheme which you want to use as Base Template.<br />
                  5. Click on 'Clone' button.<br />
                  6. Activate the newly created theme.<br />
                  7. Click on \"Update Colors\" button.<br />
                  8. You have two methods to update the colors of your active color scheme.<br />
                    a. Update the colors for specific elements solely<br />
                    b. Update the colors wherever they are being used<br />
                  9. Select the method by which you want to change the colors. Update the colors as per your choice.<br />
                  10. Click on save changes."; ?>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo "How can I disable any particular section from Landing page of my website?"; ?></a>
            <div class='faq' style='display: none;' id='faq_16'>
                <?php echo "To disable any section from landing page of your website you will have to remove the related widget from the widgetized page of landing page from layout editor."; ?>
            </div>
        </li>
         <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo "I want to change the icons under the heading. How may I do that?"; ?></a>
            <div class='faq' style='display: none;' id='faq_17'>
                <?php echo "To do so, proceed with the mentioned steps:<br />
                  1. Go to 'Theme Customization' → 'Layout Settings'.<br />
                  2. Scroll down and reach to the setting by the name 'Heading Style'.<br />
                  3. You will get plenty of options corresponding to this setting in the form of drpdown. You can set the required one to display.<br />
                  4. Click on save changes."; ?>
            </div>
        </li>
<li>
            <a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo "How can I add menus in 'Scroll Content Menu on Landing Page? "; ?></a>
            <div class='faq' style='display: none;' id='faq_18'>
                <?php echo "You have to add headings of each block to get them displayed in Scroll Content Menu. Every heading will get automatically added as a menu item in Scroll Content Menu."; ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo "Can I add more informative blocks on my landing page?"; ?></a>
            <div class='faq' style='display: none;' id='faq_19'>
                <?php echo "Yes, you can add any number of Informative Blocks on your Landing Page. To do so, proceed with the mentioned steps:<br />
                  1. Go to 'Informative Blocks' tab present in the admin panel settings.<br />
                  2. Click on 'Create New Informative Block'.<br />
                  3. Add the asked information in the fields.<br />
                  4. Click on Create Block. <br />
                  You will have to add new widget to display the newly added informative block. Do other settings related to this block using that widget."; ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo "How can I use the Marker Block or what can I display with the use of Markers Block?"; ?></a>
            <div class='faq' style='display: none;' id='faq_20'>
                <?php echo "Markers Block is a widget which allows you to display information in the form of mapping mark symbols. You can use it to display some important stats of your website like no. of projects, no. of clients, and so on. You can show your creativity to display some other information too on the Landing page using this block."; ?>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo "The Landing Page of my website has become so long and reaching to spaecific areas of the page needs much scrolling, Is there some solution?"; ?></a>
            <div class='faq' style='display: none;' id='faq_21'>
                <?php echo "Yes, there is a widget named \“Versatile - Responsive Multi-Purpose Theme - Scroll Content Menu\”, just place this widget on the Landing page from Layout Editor. Every widget having heading will then show up a menu item inside this widget which you can click and you will be redirected to that particular section of the Landing page."; ?>
            </div>
        </li>

    </ul>
</div>