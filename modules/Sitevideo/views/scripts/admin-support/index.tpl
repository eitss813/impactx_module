<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>

    <div class='seaocore_admin_tabs clr'>

        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<p>To make smooth installation of this plugin at your site so that you can enable / use this plugin's features at maximum extent, below are some helpful videos along with other content that can help you to set up this plugin more efficiently. Go through this section to get information about the setup of this plugin’s extension, integration of this plugin with your site’s different plugins and get to know about a few more features of this plugin in much more detailed manner.</p>
<ul class="admin_seaocore_files seaocore_faq mtop15">	
	<li>
  	<p class="bold">How can I set up my site after installing Advanced Videos / Channels / Playlists Plugin?</p>
  	<div class="faq"><iframe width="700" height="400" src="https://www.youtube.com/embed/2lThfddZjPE" frameborder="0" allowfullscreen></iframe>
</div>
  </li>
  <li>
  	<p class="bold">How to delete default content (videos, channels, video categories, channel categories, etc.) created after installation of Advanced Videos / Channels / Playlists Plugin?</p>
		<div class="faq">Go to “<a href="/admin/sitevideo/manage-video" target="_blank">Manage Videos</a>” / “<a href="/admin/sitevideo/manage" target="_blank">Manage Channels</a>” section available in the admin panel of this plugin, select the videos / channels from here to delete them.
      <div class="center bold">	or </div>
      You can also delete the default created videos / channels from the “My Videos” menu
      available at user panel of your site. 
      <div> If you also want to delete / edit default created categories for videos / channels then, go to 
      “<a href="/admin/sitevideo/settings/video-categories" target="_blank">Categories</a>” tab available in the admin panel of this plugin, select Videos / Channels tab
      to delete / edit the respective categories.</div>
    </div>
  </li>
  <li>
  	<p class="bold">How can I set up and integrate different plugins like: Advanced Events, Store / Marketplace, etc. with Advanced Videos / Channels / Playlists plugin to enhance the features of these plugins ? Also, what are the suggested plugins that I can purchase to make my site as magnificent as your demo site?</p>
  	<div class="faq"><iframe width="700" height="400" src="https://www.youtube.com/embed/CGyMSYiRhAg" frameborder="0" allowfullscreen></iframe>
</div></li>
  <li>
  	<p class="bold">What are the extended features for video uploading via Advanced Activity Feeds / Wall plugin after installing Advanced Videos / Channels / Playlists plugin?</p>
    <div class="faq"><iframe width="700" height="400" src="https://www.youtube.com/embed/ksWtzGSQEMM" frameborder="0" allowfullscreen></iframe>
</div></li>
  <li><p class="bold">How a default channel creation done on a new signup?</p><div class="faq"><iframe width="700" height="400" src="https://www.youtube.com/embed/YDgttcSJW30" frameborder="0" allowfullscreen></iframe>
</div></li>
  <li><p class="bold">From where I can get answers to some other questions that I have related to this plugin?</p><div class="faq">Go to “<a href="admin/sitevideo/settings/faq" target="_blank">FAQ</a>” section available in the admin panel of this plugin to get all the information related to this plugin.</div></li>
  <li><p class="bold">I want detailed information about different features and functionality of Advanced Videos / Channels / Playlists Plugin and it’s extension, from where I can get the same?</p><div class="faq">To get detailed information about Advanced Videos / Channels / Playlists Plugin, go to :
<a href="https://www.socialengineaddons.com/socialengine-advanced-videos-channels-playlists-plugin" target="_blank">https://www.socialengineaddons.com/socialengine-advanced-videos-channels-playlists-plugin</a>
And for extension, go to :
<a href="https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension" target="_blank">https://www.socialengineaddons.com/videoextensions/socialengine-advanced-videos-pages-businesses-groups-listings-events-stores-extension</a>
</div></li>
  <li><p class="bold">My queries has not been resolved by above questions, what should I do?</p><div class="faq">If you still have any other queries left, please file a support ticket from the "Support" section of your Client Area on SocialEngineAddOns (<a href="http://www.socialengineaddons.com/user/login" target="_blank">http://www.socialengineaddons.com/user/login</a>) so that our support team could look into this. Purchase of this Software, entitles the Licensee of 60 days technical support from SocialEngineAddOns. If your support duration has expired, then please subscribe to our "<a href="http://www.socialengineaddons.com/subscriptions" target="_blank">Basic</a>" or "<a href="http://www.socialengineaddons.com/subscriptions" target="_blank">Plus</a>" Subscription Plans.
</div></li>
</ul>
