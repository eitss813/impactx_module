<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
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

<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">	
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How should I start creating channels and uploading videos on my site?"); ?></a>
                <div class='faq' style='display: none;' id='faq_1'>
                    <?php echo $this->translate("After plugin installation, follow the below steps:<br />
1. Start by configuring the Global Settings for your plugin.<br />
2. Then, configure Member Level Settings for Videos / Channels.<br />
3. Go to “Videos” > “My Videos” section in the user panel to “Post New Video” / “Create New Channel”.<br />
4. You can also post a new video from “Videos” > “Post New Video” and “Videos” > “Videos Home” > “Post New Video”.<br/>
5. You can also create new channel from “Videos” > “Channels Home” > “Create New Channel”. <br/>"); ?>
                </div>
            </li>

            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Can I post a video without having a channel created?"); ?></a>
                <div class='faq' style='display: none;' id='faq_3'>
                    <?php echo $this->translate(' Yes, you can post a video without having a channel created. It will show on "Browse Videos" and "My Videos" page.
'); ?>
                </div>
            </li>



            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I am not seeing any option to upload video from My Computer while posting a new video. Why so?"); ?></a>
                <div class='faq' style='display: none;' id='faq_4'>
                    <?php echo $this->translate('It is because of any of the following reasons:<br />
1. Go to the “Global Settings” > “Video Settings” section available in the admin panel of this plugin. Enable "My Computer" from “Allowed Video Sources”.<br />
2. Check whether FFMPEG is installed on your server or not.'); ?>
                </div>
            </li>



      <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate('Can I disable Channels / Playlists / Watch Later feature?'); ?></a>
                <div class='faq' style='display: none;' id='faq_6'>
                    <?php echo $this->translate('Yes, to do so follow below steps:<br />
1. Go to the “Global Settings” > “General Settings” section available in the admin panel of this plugin.<br />

2. Channels / Playlists / Watch Later features can be enabled / disabled from here.
'); ?>
                </div>
            </li>



      
            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("Is it possible to set a value for the number of videos or channels a user can upload or create respectively ?"); ?></a>
                <div class='faq' style='display: none;' id='faq_7'>
                    <?php echo $this->translate("Yes, it is possible to set a value for the number of videos or channels a user can upload or create respectively.<br />
Below are the steps to set the value:
<br />
1. For videos:<br />
(i). Go to the “Member Level Settings” > “Videos” section available in the admin panel of this plugin.<br />
(ii). In “Maximum Allowed Videos” textbox you can enter an integer value for number of videos you want a user to upload.<br />
2. For channels:<br />
(i). Go to the “Member Level Settings” > “Channels” section available in the admin panel of this plugin.<br />
(ii). In “Maximum Allowed Channels” textbox you can enter an integer value for number of channels you want a user to create.<br />

"); ?>
                </div>
            </li>


            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("Can I edit details of my videos and videos thumbnail image once they are uploaded on my website?
"); ?></a>
                <div class='faq' style='display: none;' id='faq_8'>
                    <?php echo $this->translate('Yes, you can edit details of your videos but not videos thumbnail image once they are uploaded to your website. Go to “Videos” > “My Videos” > “Videos”, here you can see a list of all your videos. Click on “...” below the video to edit it\'s details.'); ?>
                </div>
            </li>


 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Where can I see all of my Liked / Favourited / Subscribed / Rated Channels along with the Channels I have created? And my  Playlists / Watch Later / Liked / Favourited / Rated Videos along with the Videos I have uploaded?"); ?></a>
                <div class='faq' style='display: none;' id='faq_16'>
                    <?php echo $this->translate("1. Go to “Videos” > “My Videos” > “Channels” on your site. You can see here all the channels you have created along with the channels that you have Liked / Favourited / Subscribed / Rated.<br />
2. Similarly go to “Videos” > “My Videos” > “Videos”. You can see here all the videos you have uploaded along with the videos that you have added in Playlists / Watch Later / Liked / Favourited / Rated.<br />
<br />

"); ?>
                </div>
            </li>

<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo $this->translate("How does videos and channels privacy works?"); ?></a>

                <div class='faq' style='display: none;' id='faq_25'>
                    <?php echo $this->translate("Below are the different privacy settings for videos and channels: <br/>
1. If Video is Public and Channel is Private, then only YOU can view both video and channel.<br />
2. If Video is Public and Channel is Public, then ANYONE can view both video and channel.<br />
3. If Video is Private and Channel is Public, then ANYONE can view channel and it’s videos except the private ones.<br />
4. If Video is Private and Channel is Private, then only YOU can view both video and channel.<br />

[Note: Private implies to “Just Me” whereas Public implies to “Everyone” / “All Registered Members” / “Friends and Networks” / “Friends of Friends” / “Friends Only”.]

"); ?>
                </div>

            </li>



        <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Can I add one video into multiple channels ?"); ?></a>
                <div class='faq' style='display: none;' id='faq_5'>
                    <?php echo $this->translate("Yes, you can add one video into multiple channels by following these steps: <br />
1. Go to the channel’s dashboard from “Channels Profile Page” or from “Videos” > “My Videos” > “Channels” > “Edit Channel” . <br />
         2. Click “Edit Channel” of the Channel in which you want to add the already uploaded videos.<br />
         3. Select “Manage Videos” from the dashboard.<br />
4. Click on “Add More Videos”.<br />
5. There are multiple options available like My Uploads / My Favourites / My Likes / My Rated to select videos to add them into a channel.
<br /> 
6. Now, select the videos you wish to add to this channel by clicking on the thumbnails.<br />

          "); ?>
                </div>
            </li>


            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("If I delete a channel. What happen to the videos of that channel?"); ?></a>
                <div class='faq' style='display: none;' id='faq_9'>
                    <?php echo $this->translate('If you delete a channel then the videos belonging to that channel will not be deleted and those videos will be visible to users on your site without any channel. <br />
[Note: If you also want to delete that channel\'s videos then you can delete them from “My Videos” section available on your site or “Manage Videos” section available in the admin panel.]'); ?>
                </div>
            </li>


            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I have added a video in my playlist / favourites / watch later / liked / rated. If the owner of the video deletes it, will it be automatically deleted from here also?"); ?></a>
                <div class='faq' style='display: none;' id='faq_10'>
                    <?php echo $this->translate('Yes, if the owner of the video has deleted the video then it will be automatically deleted from your playlist / favourites / watch later / liked / rated.          '); ?>
                </div>
            </li>

<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("How can I add a video as Featured Video and a channel as Featured Channel? How can I mark the labels as Featured and Sponsored for the Videos / Channels?"); ?></a>
                <div class='faq' style='display: none;' id='faq_14'>
                    <?php echo $this->translate("Featured Video can be displayed in various widgets and blocks enabling the featured option like ‘Featured Videos Slideshow’, ‘Featured Channels Slideshow’. <br />
There are two ways to make a video as Featured Video and a channel as Featured Channel:
<br />
          1. Go to the “Featured Videos / Channels” > “Videos” / “Channels” section available in the admin panel of this plugin. <br />
	(i). Select “Add a Video as Featured” / “Add a Channel as Featured”.<br />
	(ii). Fill the required details. <br />
	(iii). You can also add tagline and tagline description to make your Featured Video / Channel unique and appealing. <br/>
	(iv). Click on “Mark Featured” button to make that Video / Channel as Featured. <br />
2. Go to “Manage Videos” / “Manage Channels” section available in the admin panel of this plugin. <br />
	(i). Click on Featured / Sponsored label to make a video / channel as Featured Video / Featured Channel.
	"); ?>
                </div>
            </li>   


<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate("How can I change the tagline text displaying with Featured Videos & Channels?"); ?></a>
                <div class='faq' style='display: none;' id='faq_15'>
                    <?php echo $this->translate("1. Go to the “Featured Videos / Channels” section available in the admin panel of this plugin.<br />
2. Select the one whose tagline you want to change - Videos or Channels tab.<br />
3. Here you can see a list of featured videos / channels.<br />
4. Under “Options” select “edit details” to edit the tagline text and other details of videos / channels.<br />"); ?>
                </div>
            </li>

           
 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("I have placed Sponsored Categories widget but, I am unable to see any of the Sponsored Categories on my site. Why is it happening so?"); ?></a>
                <div class='faq' style='display: none;' id='faq_20'>
                    <?php echo $this->translate("1. It is happening because you have not made any category as Sponsored while creation.<br />

2. Go to the “Categories” > “Videos” / “Channels” section available in the admin panel of this plugin.<br />
(i). Click on “Add Category” and fill the required details or you can edit the setting of already created category.<br />
(ii). To make this category as sponsored check “Mark as Sponsored”.<br />
(iii). Click on “Add” button to save the settings of the category.<br />

3. Now, you will be able to see Sponsored Categories on the page where you have placed “Sponsored Categories” widget.
<br />

"); ?>
                </div>

            </li>


<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("My Videos Categories / Channels Categories home page’s UI is not coming fine. What might be the reason?"); ?></a>
                <div class='faq' style='display: none;' id='faq_23'>
                    <?php echo $this->translate("If the UI of your Videos Categories / Channels Categories home page is not coming fine, then please check that  you have placed the “Channel Categories: Display Category with Background Image Slideshow” /  “Video Categories: Display Category with Background Image Slideshow” widget in top container of your widgetized page or not. If not, then please place these widgets in top container of your widgetized page to fix the UI issue coming on your site.
"); ?>
                </div>

            </li> 


 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("How can I show multiple categories in slideshow on Categories Home Page?"); ?></a>
                <div class='faq' style='display: none;' id='faq_21'>
                    <?php echo $this->translate("To show multiple categories in a slideshow follow the below steps:
<br />

1. Place “Video Categories: Display Category with Background Image Slideshow” widget on Video Categories Home page and “Channel Categories: Display category with background image slideshow” widget on Channel Categories Home page.<br />

2. Now, select multiple categories that you want to display in the slideshow by editing the setting of above widgets.<br />

"); ?>
                </div>

            </li>  


 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("How can I change the background image of Categories Home Page?"); ?></a>
                <div class='faq' style='display: none;' id='faq_22'>
                    <?php echo $this->translate("1. Go to “Layout” > “File & Media Manager” section available in the admin panel.<br />
2. Here you can upload images.<br />
3. You can set any uploaded image as background image of Categories Home Page by configuring the setting of “Channel Categories: Displays Category with Background Image Slideshow” /  “Video Categories: Displays Category with Background Image Slideshow” widget. 


"); ?>
                </div>

            </li> 



            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("Can I create a private playlist that would not be visible to other members?"); ?></a>
                <div class='faq' style='display: none;' id='faq_12'>
                    <?php echo $this->translate("Yes, you can create a private playlist which would not be visible to other members. You can set the privacy of the playlist as Private while creating it.
<br/>
[Note: You can also edit the privacy to Private of the already created playlist from “Videos” > “My Videos” > “Videos” > “Playlists” . Now, select the playlist whose privacy you want to change. Click on “Edit Playlist” to edit the privacy of the particular playlist.]"); ?>
                </div>
            </li>    


           <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_31');"><?php echo $this->translate("How can I integrate and enable Directory / Pages Plugin, Advanced Events Plugin and others plugin to post videos directly from these plugins ?"); ?></a>
                <div class='faq' style='display: none;' id='faq_31'>
                    <?php echo $this->translate("Please follow the below steps to integrate and enable these plugins with Advanced Videos / Channels / Playlists plugin: <br />
1. Go to “Manage Modules” tab available in the admin panel of this plugin. <br />
2. Enable the plugins which you want to integrate from here by just one click. <br />


[Note: This feature is only available if your website has <a href='https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension' target='_blank'> Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension </a> installed.]


"); ?>
                </div>

            </li> 
             

<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_30');"><?php echo $this->translate("How can I import my videos from Directory / Pages Plugin and Advanced Events Plugin to Advanced Videos / Channels / Playlists plugin."); ?></a>
                <div class='faq' style='display: none;' id='faq_30'>
                    <?php echo $this->translate("You can import your videos from different plugins by following below steps: <br />
1. Go to 'Import' tab available in the admin panel of this plugin. <br />
2. Click on “Start Import” to import all your videos. <br />


[Note: This feature is only available if your website has <a href='https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension' target='_blank'> Advanced Videos - Pages, Businesses, Groups, Multiple Listing Types, Events, Stores, etc Extension </a> installed.]

"); ?>
                </div>

            </li>  


            <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_29');"><?php echo $this->translate("I am not seeing 'Justified' view related settings for Channel's photos, what might be the reason?"); ?></a>
                <div class='faq' style='display: none;' id='faq_29'>
                    <?php echo $this->translate("It might be that you have not installed <a href='https://www.socialengineaddons.com/socialengine-advanced-photo-albums-plugin' target='_blank'> 'Advanced Photo Albums Plugin' </a> on your site. To enable 'Justified' view and beautify the photos of your channels, please install this plugin. If you want to purchase this plugin, please <a href='https://www.socialengineaddons.com/socialengine-advanced-photo-albums-plugin' target='_blank'> click here </a>.


"); ?>
                </div>

            </li>  



<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("Social Sharing links like Facebook, LinkedIn, Twitter etc. and other actions links like Watch Later, Favourite etc. are not visible on my videos. What might be the reason?"); ?></a>
                <div class='faq' style='display: none;' id='faq_18'>
                    <?php echo $this->translate("1. Go to the “Layout Editor” and select the widgetized page to edit the settings of the widgets that you have placed on that page of your site.
<br />
2. Enable / disable social sharing links like Facebook, LinkedIn, Twitter etc. as well as other action links like Watch Later, Favourite etc. from there.

"); ?>
                </div>

            </li>  




<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate("What is FFMPEG Path in this plugin? From where will I get it?"); ?></a>
                <div class='faq' style='display: none;' id='faq_27'>
                    <?php echo $this->translate("Follow the steps below to install FFMPEG and to define path for it in Global Settings:<br />
1. Download FFMPEG static files from <a href='http://ffmpeg.gusari.org/static/64bit' target='_blank'> ffmpeg.gusari.org/static/64bit </a> <br />
2. Place FFMPEG files in Document Root. For e.g. /var/www/html or /home/abc/public_html . <br />
3. Go to “Global Settings” > “Video Settings” section available in the admin panel of this plugin.<br />

4. Define FFMPEG path in “Path to FFMPEG” field.<br />
5. Click on “Save Changes” button to save the new changes in the settings.<br />
6. Give 777 Recursive permission to FFMPEG folder.<br />

7. You can check the version of FFMPEG that you have installed along with the supported video formats in “Global Settings” > “Video Utilities” section available in the admin panel of this plugin.




"); ?>
                </div>
            </li> 

 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_28');"><?php echo $this->translate("Do I need to upload videos in any specific format or it supports all formats?"); ?></a>
                <div class='faq' style='display: none;' id='faq_28'>
                    <?php echo $this->translate("No, FFMPEG converts all the video formats dynamically for your website. So, you do not need to bother about video formats and resizing.
"); ?>
                </div>
</li>

<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_32');"><?php echo $this->translate("I am getting an error while uploading large video files on my system, it says the maximum limit has been exceeded. Is it possible to increase the Max limit for uploading videos?
"); ?></a>
                <div class='faq' style='display: none;' id='faq_32'>
                    <?php echo $this->translate("Yes, please follow the below steps to do so:<br />
1. Go to the: /admin/system, now search for the below variables. They should be large in size. You can ask from your hosting company to change them for you.<br />
 2. upload_max_filesize = 8MB [Can be exceeded up to 64MB]<br />
3. post_max_size = 8MB [Can be exceeded up to 64MB]<br />
4. memory_limit = 512MB<br />
5.You will now be able to upload large video files based on your above settings.

"); ?>
                </div>
</li>

<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("I have multiple networks on my site and I want users to be shown only those Channels / Videos belonging to their network only. How can this be done?
"); ?></a>
                <div class='faq' style='display: none;' id='faq_24'>
                    <?php echo $this->translate("1. Go to the “Global Settings” > “General Settings” section available in the admin panel of this plugin.<br />
2. Select yes in “Browse by Networks” to show Channels / Videos according to viewer's network if he has selected any.<br/> 
[Note: This is applicable only when you have joined more than one network. Go to “Settings” > “Networks” section of your site. You can join different networks from there.] 

"); ?>
                </div>

            </li> 




 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo $this->translate("After installation of this plugin, thumbnails of my already uploaded YouTube's videos and Vimeo's videos are not coming fine. What might be the reason and how can I fix it ?"); ?></a>
                <div class='faq' style='display: none;' id='faq_26'>
                    <?php echo $this->translate(" It is not coming fine because the thumbnail images are small in size as compare to required sizes. To fix this issue, you need to create thumbnail images of your YouTube's videos and Vimeo's videos again in required sizes. To do so, please follow the below steps: <br />
1. Go to 'Synchronization' tab available in the admin panel of this plugin. <br />
2. You can follow further steps from here to fix the size of the thumbnail images.
"); ?>
                </div>

            </li> 

<li>
                <a href="javascript:void(0);" onClick="faq_show('faq_33');"><?php echo $this->translate("Some videos from my site’s activity feeds are being shown in the Video Lightbox Viewer when I click on their titles, but for some other videos I am being redirected to their view pages. What might be the reason?"); ?></a>
                <div class='faq' style='display: none;' id='faq_33'>
                    <?php echo $this->translate("You can also see all the videos from your site’s activity feeds in the Video Lightbox Viewer by using our <a href='https://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'> Advanced Activity Feeds / Wall Plugin </a> . (To see details, please <a href='https://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'> visit here </a>.) Or you can do minor modifications mentioned below to make Video Lightbox Viewer work with activity feeds: <br />
Step 1: <br />
	(i). Open the file: 'application/modules/Activity/Model/Helper/Item.php'. <br />
	(ii).Go to the line around line number 59 having: <br />
	class='feed_item_username' <br />
	(iii).Replace the above code with the new block of code below after the dot(.): <br />
	class=“feed_item_username feed_'.$item->getType().'_title” <br />
Step 2: <br />
	(i). Open the file: 'application/modules/Activity/views/scripts/_activityText.tpl'. <br />
	(ii). Go to the line around line number 65 having: <br />
	$$('.comments_body').enableLinks(); <br />
	(iii). Now below this line, add the new block of code mentioned below: <br />
	if(en4.sitevideoview){ <br />
en4.sitevideoview.attachClickEvent(Array('feed','feed_video_title','feed_sitepagevideo_title',<br /> 'feed_sitebusinessvideo_title','feed_ynvideo_title','feed_sitegroupvideo_title','feed_sitestorevideo_title'));
} <br />


"); ?>
                </div>

            </li> 





 <li>
                <a href="javascript:void(0);" onClick="faq_show('faq_34');"><?php echo $this->translate("What might be the reason, if my videos are not playing in Safari browsers?"); ?></a>
                <div class='faq' style='display: none;' id='faq_34'>
                    <?php echo $this->translate("May be “Apple's QuickTime” plugin is not installed and enabled on your system browser. Please install it and, then play the videos.
"); ?>
                </div>

            </li> 


      
    </ul>
</div>




