<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _feedLikepage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$path=APPLICATION_PATH. '/application/modules/Activity/views/scripts/_activityText.tpl';
$string='$this->itemPhoto($item, \'thumb.icon\', $item->getTitle()), array()'; ?>
<?php $dummyValue=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagefeed.likepage.dummy', 'c');?>
<div id="sitepagefeed_likepage_dummy-wrapper" class="form-wrapper">    
  <div id="sitepagefeed_likepage_dummy-label" class="tip">
  	<span>
  		<?php echo $this->translate("To enable users to receive on their homepage the activity feeds of Pages that they have liked, follow ANY ONE of the below 3 options.");?>
  	</span>	
  </div>
  <div class="form-label">&nbsp;</div>
  <div id="sitepagefeed_likepage_dummy-element" class="form-element">
    <p class="description"><input type="radio" onclick="showTips('tip_like_widget','like')" value="a" id="sitepagefeed_likepage_dummy-a" name="sitepagefeed_likepage_dummy" <?php if($dummyValue=='a'):?> checked ='checked'<?php endif; ?> > <?php echo $this->translate('Use "SocialEngineAddOns Activity Feed" widget instead of core "Activity Feed" widget on Member Home Page. (For this, go to "Layout" > "Layout Editor" > "Member Home Page" and replace the already placed "Activity Feed" widget with "SocialEngineAddOns Activity Feed" widget.)') ?></p>
    <p class="description"><input type="radio" onclick="showTips('tip_like_faq','like')" value="b" id="sitepagefeed_likepage_dummy-b" name="sitepagefeed_likepage_dummy" <?php if($dummyValue=='b'):?> checked ='checked'<?php endif; ?> >
    <a href="javascript:void(0);" onclick='javascript:$("sitepagefeed_likepage_dummy-b").checked="checked";showTips("tip_like_faq","like"); openSmoothboxFeed("<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'settings', 'action' => 'overwrite', 'type' => 'activity_feed_likepage'),'admin_default') ?>");' > <?php echo $this->translate('Click here') ?> </a>
    <?php echo $this->translate(' to automatically overwrite the file: "/application/modules/Activity/views/scripts/_activityText.tpl" with the required minor modification. (If you do not want automatic overwriting of file, you can choose the 3rd option for manual changes which also shows the minor modification required.'); ?>
    <p class="description"><input type="radio" onclick="showTips('tip_like_faq','like'); faq_show('faq_37');" value="c" id="sitepagefeed_likepage_dummy-c" name="sitepagefeed_likepage_dummy" <?php if($dummyValue=='c'):?> checked ='checked'<?php endif; ?> ><?php echo $this->translate('If you do not want to follow any of the above two options, then you can manually do the minor code modification. (Please see below the steps that you need to follow.)') ?></p>
    <div id="tip_like_widget" style="display: none;" >
      <div class="tip">
    <span class="sitepage_activity_tip">
      <?php if (Engine_Api::_()->getApi('subCore', 'sitepage')->isCoreActivtyFeedWidget('user_index_home', 'activity.feed')): ?>
      <?php echo $this->translate('The core "Activity Feed" widget is placed on Member Home Page. So, either replace it with "SocialEngineAddOns Activity Feed" widget, or follow one of the other 2 options above.') ?>
      <?php else: ?>
      <?php echo $this->translate('You have not placed any of the activity feed (neither core nor SocialEngineAddOns) widget on the Member Home Page.') ?>
      <?php endif; ?>
    </span>
    </div>
    </div>
    <div id="tip_like_faq" style="display: none;" >
      <div class="tip">
       <span class="sitepage_activity_tip">
        <?php if (!Engine_Api::_()->getApi('subCore', 'sitepage')->isContentInFile($path, $string)): ?>
            <?php echo $this->translate("No changes have been done in the activity feed file for the above customization."); ?>
          <?php else:?>
            <?php echo $this->translate("The required changes have been successfully done for showing Page Activity Feeds to users on Home Page for Pages liked by them."); ?>
           <?php endif; ?>
        </span>
      </div> 
    </div>
    <div class='faq' style='display: none;' id='faq_37'>
      <b>	<?php echo $this->translate("Please follow the steps below to manually do these changes in the file:") ?></b><br /><br />
      <?php echo $this->translate('1) First of all, make sure that you have selected "Page\'s Photo and Title" option for the field "Directory Items / Pages Activity Feed Type" above.') ?><br /><br />
      <?php echo $this->translate('2) Then, open the file: "/application/modules/Activity/views/scripts/_activityText.tpl".') ?><br /><br />
      <?php echo $this->translate('3) Search for the block of code given below in this file at line no. 95 (approx) :') ?><br /><br />
      <div class="code">
        <?php echo '&lt;?php // User\'s profile photo ?&gt;<br />
    &lt;div class=\'feed_item_photo\'&gt;&lt;?php echo $this-&gt;htmlLink($action-&gt;getSubject()-&gt;getHref(),<br />
      $this-&gt;itemPhoto($action-&gt;getSubject(), \'thumb.icon\', $action-&gt;getSubject()-&gt;getTitle())<br />
    ) ?&gt;&lt;/div&gt;' ?><br />
        <img src="https://lh3.googleusercontent.com/-sM5eAxV5SUQ/Tx6W2xiRcOI/AAAAAAAAAG0/sagtL_6dTBw/s883/_activityText.jpg" alt="" />
      </div><br />
      <?php echo $this->translate("4) Now, replace the above code with the new block of code given below:") ?><br /><br />
      <div class="code">
        <?php echo '&lt;?php $item = (isset($action-&gt;getTypeInfo()-&gt;is_object_thumb) && !empty($action-&gt;getTypeInfo()-&gt;is_object_thumb)) ? $action-&gt;getObject() : $action-&gt;getSubject(); ?&gt;<br />
        &lt;?php // User\'s profile photo ?&gt;<br />
        &lt;div class=\'feed_item_photo\'&gt;&lt;?php echo $this-&gt;htmlLink($item-&gt;getHref(), <br />
        $this-&gt;itemPhoto($item, \'thumb.icon\', $item-&gt;getTitle()), array()<br />
        ) ?&gt;&lt;/div&gt;' ?><br />
        <img src="https://lh4.googleusercontent.com/-oJy0S7g5NbY/Tx6XMRGkNRI/AAAAAAAAAG8/fgMxbIWssak/s912/_modifiedactivitytext.jpg" alt="" />
      </div><br /><br />
      <div class='tip'><span><b>
        <?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?></b></span>
      </div>
    </div>
  </div>
</div>