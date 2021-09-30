<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
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

<div class='tabs'>
  <ul class="navigation">
    <li class="<?php
    if ($this->faq_type == 'general') {
      echo "active";
    }
    ?>">
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'settings', 'action' => $this->action, 'faq_type' => 'general'), $this->translate('General'), array())
          ?>
    </li>
    <li class="<?php
    if ($this->faq_type == 'customize') {
      echo "active";
    }
    ?>">
          <?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'settings', 'action' => $this->action, 'faq_type' => 'customize'), $this->translate('Customize'), array())
          ?>
    </li>
  </ul>
</div>
<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">	
    <?php if ($this->faq_type == 'general'): ?>
    
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How should I start with creating albums on my site?"); ?></a>
        <div class='faq' style='display: none;' id='faq_1'>
          <?php echo $this->translate("Ans: After plugin installation, follow the steps below:<br />
- Start by configuring the Global Settings for your plugin.<br />
- Then, configure Member Level Settings for albums.<br />
- Then create the categories and sub-categories.<br />
- Then go to the Profile Fields section to create custom fields if required for any Albums on your site and configure mapping between album categories and profile types such that custom profile fields can be based on categories and sub-categories for your albums.<br>Customize various widgetized pages from the Layout Editor section."); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("How can I make photos featured? Where are featured photos displayed?"); ?></a>
        <div class='faq' style='display: none;' id='faq_3'>
          <?php echo $this->translate('Ans: You can make photos featured either from the "Featured Photos" section in the Admin Panel of the Advanced Photo Albums Plugin, or using the "Make Featured" link on the main photo page / lightbox. Featured Photo can be displayed in various widgets and blocks enabling the featured option like ‘Featured Photos Slideshow’, ‘Recent / Random / Popular Photos’, etc.'); ?>
        </div>
      </li>

<li>
        <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I am getting blurry images on my site? What should I do?"); ?></a>
        <div class='faq' style='display: none;' id='faq_4'>
          <?php echo $this->translate('Ans: Please follow the below steps to resolve your problem: <br />
1. Go to the Global Settings section available in the admin panel of this plugin.<br />
2. Configure the “Normal Photo Height”, “Normal Photo Width”, “Normal Large Photo Height”, “Normal Large Photo Width”, “Main Photo Height”, “Main Photo Width”.<br />
3. Now configure the settings of the widget in which you are getting blurry images, set height and width of the album / album photo accordingly. <br />
[Now, if your photo’s height and width will lie in between the Normal Photo’s height and width mentioned in the above settings, it will show Normal Photo else it will show the Main Photo of the mentioned size.]'); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want different views for my Album’s Profile Page. In how many ways I can do so?"); ?></a>
        <div class='faq' style='display: none;' id='faq_5'>
          <?php echo $this->translate("Ans: There are 2 ways by which you can showcase your Album’s Profile Page in an attractive manner. For this you can visit our below demo URL’s for different views of Album Profile Page: <br />
          - <a href='http://demo.socialengineaddons.com/albums/view/293'>http://demo.socialengineaddons.com/albums/view/293</a><br />
          - <a href='http://demo.socialengineaddons.com/albums/view/293?otherView=tmp22'>http://demo.socialengineaddons.com/albums/view/293?otherView=tmp22</a><br />

          "); ?>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate('How can I mark an album as "Album of the Day"?'); ?></a>
        <div class='faq' style='display: none;' id='faq_6'>
          <?php echo $this->translate('Ans: You can mark an album as "Album of the Day" either from the "Widget Settings" > "Album of the Day" section in the Admin Panel of the Advanced Photo Albums Plugin, or using the "Make Album of the Day" link on the main album page. Album of the Day is displayed in the "Album of the Day" widget.'); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I am not able to see the Cover Photo for my albums. Why is it so?"); ?></a>
        <div class='faq' style='display: none;' id='faq_7'>
          <?php echo $this->translate("Ans: It is happening because you need our <a href='http://www.socialengineaddons.com/socialengine-content-profiles-cover-photo-banner-site-branding-plugin' target='_blank'>Content Profiles - Cover Photo, Banner & Site Branding Plugin</a> to enable attractive and beautiful cover photos for your albums. To purchase our plugin you can <a href='http://www.socialengineaddons.com/contact' target='_blank'>contact us</a>."); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want fewer fields to be visible in the Top content widget available on the Album View Page. How can I do so?"); ?></a>
        <div class='faq' style='display: none;' id='faq_8'>
          <?php echo $this->translate('Ans: To do so, please go to ‘Layout Editor’ > ‘Album View Page’. Now enable / disable the options from the edit pop up of ‘Album Profile: Top Content of Album View Page’ widget as per your requirements.'); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("How can I see Albums according to my location in various widgets on Albums Home, Browse Albums, etc pages?"); ?></a>
        <div class='faq' style='display: none;' id='faq_9'>
          <?php echo $this->translate('Ans: If you want to see albums based on your location, then simply choose ‘Yes’ option for “Do you want to display albums / photos based on user’s current location?” setting. You can also choose the miles within which albums will be displayed.'); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Photos are getting stretch in the widgets though I have uploaded all high quality photos on my site. What might be the reason?"); ?></a>
        <div class='faq' style='display: none;' id='faq_10'>
          <?php echo $this->translate('Ans: Go to the Global Settings section available in the admin panel of this plugin and increase the ‘Height’ and ‘Width’ values for:<br />
          - Main Photo Height (in pixels)<br />
					- Main Photo Width (in pixels)<br />
					- Normal Photo Height (in pixels)<br />
					- Normal Photo Width (in pixels)<br />
                    - Normal Large Photo Height (in pixels)<br />
                    - Normal Large Photo Width (in pixels)<br />
					By increasing the values for the above settings, Your photos will now appear very attractive and beautiful.<br />
					[Note: The updated settings will only be effected on the newly uploaded photos so, you need to upload the old photos again to see them with the updated settings.]
          '); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("I want Like and Comment hover strip in the widgets. How can I do this?"); ?></a>
        <div class='faq' style='display: none;' id='faq_12'>
          <?php echo $this->translate("Ans: To to this, please go to the ‘Layout Editor’ > ‘Widgetize Page’ and enable the ‘Like / Comment strip’ from the edit pop up of those widgets like: Album Photos, Recent / Random / Popular Photos, Photo Of the Day, etc. By this you will be able to see the likes and comments count while hovering on the photos. <br /> [Note: you will be able to do this only for the widgets showcasing photos in them.]"); ?>
        </div>
      </li>    
      
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("Contents in the various widgets are not coming fine on my site. What might be the reason?"); ?></a>
        <div class='faq' style='display: none;' id='faq_13'>
          <?php echo $this->translate("Ans: It may be because of the lesser margin between the consecutive elements in the widget. Please go to the Layout Editor and configure the ‘Vertical and Horizontal Margins Between Elements’ setting from the edit pop up of those widget for which the content is not coming fine."); ?>
        </div>
      </li>    
      
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("I have placed Sponsored Categories widget but, I am unable to see any of the categories in this widget. Why is it happening so?"); ?></a>
        <div class='faq' style='display: none;' id='faq_14'>
          <?php echo $this->translate("Ans: It is happening because you have not selected any of the categories as Sponsored. This widget showcase only those categories which are sponsored. Follow the below steps to do so:<br />
          1. Please go to the Categories section available in the admin panel of this plugin. <br />
					2. Edit categories you want as sponsored by clicking on them<br />
					3. Enable ‘Mark As Sponsored’ setting for each of those categories. <br />
					You will now be able to see your marked sponsored categories in the Sponsored Categories widget."); ?>
        </div>
      </li>    
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate(" Is there any widget in which I can show all the informations related to my album on its Albums Main Page?"); ?></a>
        <div class='faq' style='display: none;' id='faq_15'>
          <?php echo $this->translate("Ans: Yes, you can use our ‘Album Profile: Album Information’ widget for showing all the information related to your album."); ?>
        </div>
      </li>
      
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate(" The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
        <div class='faq' style='display: none;' id='faq_18'>
          <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
        </div>
      </li>
      
      
      
      
      
      
    <?php elseif ($this->faq_type == 'customize'): ?>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_199');"><?php echo $this->translate("Photos from my site’s activity feeds are not shown in the Photo Lightbox Viewer and when I click on them, I am being redirected to their view pages. What might be the reason?"); ?></a>
        <div class='faq' style='display: none;' id='faq_199'>
          ~ You can also see all the photos from your site’s activity feeds in the Photo Lightbox Viewer by using our <a href="http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin" target="_blank">"Advanced Activity Feeds / Wall Plugin"</a>. (To see details, please <a href="http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin" target="_blank">visit here</a>.) or by doing minor modifications mentioned below to make Photo Lightbox Viewer work with activity feeds:<br>
          Step 1:<br>
          a) Open the file after replacing the MODULE_NAME: ‘application/modules/MODULE_NAME/views/scripts/_activityText.tpl’<br>
          (Note: The MODULE_NAME will be the name of the Activity Feed which is installed on your site. For example: If you have official SocialEngine Core Activity module, then replace MODULE_NAME with ‘Activity’.)<br>

          b) Now, add below mentioned code in the last of this file: <br>

          <div class="code">
            &lt;script type="text/javascript"&gt;<br>
            <br>
            en4.core.runonce.add(function() { <br>
            //SHOW IMAGES IN THE LIGHTBOX FOR THE ACTIVITY FEED <br>
            if( activityfeed_lightbox != 0 ) {<br>
            //DISPLAY ACTIVITY FEED IMAGES IN THE LIGHTBOX FOR THE GROUP <br>
            addSEAOPhotoOpenEvent(Array('feed_attachment_group_photo','feed_attachment_event_photo','feed_attachment_sitepage_photo','feed_attachment_list_photo','feed_attachment_recipe_photo','feed_attachment_sitepagenote_photo','feed_attachment_album_photo','feed_attachment_sitebusiness_photo','feed_attachment_sitebusinessnote_photo'));<br>
            }<br>
            });<br>
            &lt;/script&gt;
          </div>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I do not want to show default albums on Member Profile Page. How can it be possible?"); ?></a>
        <div class='faq' style='display: none;' id='faq_10'>
          <?php echo $this->translate("Ans: Yes, you can choose to not display the default albums on Member Profile Page."); ?><br />
          <?php echo $this->translate("To do so, please follow the steps below:") ?><br /><br />
          <?php if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()): ?>
            <?php echo $this->translate('1) Open the file with path: "application/modules/Album/Model/DbTable/Albums.php".') ?><br /><br />
            <?php echo $this->translate("2) Search for the following code near about line number 79.") ?><br /><br />
          <?php else: ?>
            <?php echo $this->translate('1) Open the file having the path: "application/modules/Album/Api/Core.php".') ?><br /><br />
            <?php echo $this->translate("2) Search for the following code near about line number 79.") ?><br /><br />
          <?php endif; ?>

          <div class="code">
            <?php echo 'if( !empty($options[\'search\']) && is_numeric($options[\'search\']) ) {<br />&nbsp;
                            $select->where(\'search = ?\', $options[\'search\']);<br />&nbsp;
                    }' ?><br />							
          </div><br />
          <?php echo $this->translate("3)  Now paste the code mentioned in this step just below the code mentioned in step 2.") ?><br /><br />
          <div class="code">
            <?php echo '$select->where(\'type IS NULL\');' ?><br />					
          </div><br />
          <div class='tip' style='position:relative;'><span><b>
                <?php echo $this->translate('NOTE: Whenever you will upgrade Core Albums plugin on your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?></b></span>
          </div>
        </div>
      </li>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("I do not want to show default albums to members on Browse Albums Page. How can it be possible?"); ?></a>
        <div class='faq' style='display: none;' id='faq_11'>
          <?php echo $this->translate("Ans: Yes, you can choose to not display the default albums on Browse Albums Page."); ?><br />
          <?php echo $this->translate("To do so, please follow the steps below:") ?><br /><br />
          <?php echo $this->translate('1) Open the file with path: "application/modules/Album/controllers/IndexController.php".') ?><br /><br />
          <?php echo $this->translate("2) Search for the folowing line of code near about line number 67.") ?><br /><br />
          <div class="code">
            <?php echo '$select = $table->select()<br />&nbsp;
                    ->where("search = 1")<br />&nbsp;
                  ->order($order . \' DESC\');' ?><br />     
          </div><br />
          <?php echo $this->translate("3) Now paste the code mentioned in this step just below the code mentioned in step 2.") ?><br /><br />
          <div class="code">
            <?php echo '$select->where(\'type IS NULL\');' ?><br />         
          </div><br />
          <div class='tip' style='position:relative;'><span><b>
                <?php echo $this->translate('NOTE: Whenever you will upgrade Core Albums plugin on your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?></b></span>
          </div>
        </div>
      </li>

      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("I am having the 3rd party Rates Plugin on my site and Advanced Photo Albums plugin is not working with that plugin. What can be the solution ?"); ?></a>
        <div class='faq' style='display: none;' id='faq_12'>
          <?php echo $this->translate("Ans: For the compatibility of Advanced Photo Albums plugin with 3rd party Rates Plugin, please follow the below steps:"); ?><br /><br />
          <?php echo $this->translate("1) At the admin panel of your site go to the below link :") ?><br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate("SITE_URL/admin/rate/faq") ?><br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate('And then click on "Album Photos Page." there and you will see some instructions like given below for that plugin :') ?><br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate('File - application\modules\Album\views\scripts\album\view.tpl') ?><br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate('Line - 82 (or search red-highlighted text via editor)') ?><br /><br />
          <?php echo $this->translate('2) Now, to make the rates plugin compatible with our Advanced photo Albums plugin, you just have to use the below file path:<br />&nbsp;&nbsp;&nbsp;
"application/modules/Sitealbum/widgets/album-view/index.tpl"') ?><br /><br />

          <?php echo $this->translate('3) Now, they have instructed to search for below code in the FAQ for Albums plugin:') ?><br /><br />
          <div class="code">
            <?php echo '&lt;a class="thumbs_photo" href="&lt;?php echo $photo->getHref(); ?&gt;" &gt; <br />
                      &lt;span style="background-image: url(&lt;?php echo $photo->getPhotoUrl(\'thumb.normal\'); ?&gt;);" &gt; &lt;/span &gt;<br />
                    &lt;/a &gt;';
            ?><br />
          </div>
          <br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate('And you have to search our code given below in place of the above code to do the same thing for Advanced Albums Plugin:') ?><br /><br />
          <div class="code">
            <?php echo ' &lt;a class="thumbs_photo" href="&lt;?php echo $photo->getHref(); ?&gt;" &lt;?php if($this->showLightBox): ?&gt; onclick=\'openLightBoxAlbum("&lt;?php echo $photo->getPhotoUrl()?&gt;","&lt;?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo); ?&gt;");return false;\' &lt;?php endif; ?&gt;&gt;<br />
                  &lt;span style="background-image: url(&lt;?php echo $photo->getPhotoUrl(\'thumb.normal\'); ?&gt;);"&gt;&lt;/span&gt;<br />
                  &lt;/a&gt;';
            ?><br />
          </div>
          <br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate('And then replace the above code in the same way by the green block of code as told by them in the above FAQ link of that plugin.') ?><br /><br />
          &nbsp;&nbsp;&nbsp;<?php echo $this->translate('In this way, you just had to change the file name and the searchable block to make this Rates plugin work for Advanced Photo Albums plugin.') ?><br /><br />

          <div class='tip' style='position:relative;'><span><b>
                <?php echo $this->translate('NOTE: Whenever you will upgrade Advanced Photo Albums plugin on your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?></b></span>
          </div>
        </div>
      </li>
      <li style='display: none;'>
        <a href="javascript:void(0);" onClick="faq_show('faq_12');" style='display: none;'><?php echo $this->translate("I am having the 3rd party Advanced Wall Plugin on my site but photos in the Activity Feed are not being displayed in lightbox. What can be the solution ?"); ?></a>
        <div class='faq' style='display: none;' id='faq_12'>
          <?php echo $this->translate("We have transfer the lightbox settings to the SocialEngineAddOns Plugin. if you want to change the lighbox setting then please click <a href='" . $this->baseUrl() . "/admin/seaocore/settings/lightbox' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('here') . "</a>"); ?>
        </div>
      </li>
    <?php endif; ?>
  </ul>
</div>
