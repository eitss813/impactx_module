<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _feedEveryone.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$path=APPLICATION_PATH. '/application/modules/Activity/Model/DbTable/Actions.php';
$string='$ids = Engine_Api::_()->getApi(\'subCore\', \'sitepage\')->getEveryonePageProfileFeeds($about, $this->_getInfo($params));'; ?>
<div id="sitepagefeed_everyone_dummy-wrapper" class="form-wrapper">
  <div id="sitepagefeed_everyone_dummy-label" class="tip">
  	<span>
  		<?php echo $this->translate('To show all Activity Feeds on Page Profile irrespective of your setting in Activity Feed Settings, follow ANY ONE of the below 3 options.');?>
  	</span>	
  </div>
	<div class="form-label">&nbsp;</div>
	<div id="sitepagefeed_everyone_dummy-element" class="form-element">
    <?php $dummyValue=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagefeed.everyone.dummy', 'c');?>
    <p class="description"><input type="radio" onclick="showTips('tip_everyone_widget','everyone')" value="a" id="sitepagefeed_everyone_dummy-a" name="sitepagefeed_everyone_dummy" <?php if($dummyValue=='a'):?> checked ='checked'<?php endif; ?> ><?php echo $this->translate('Use "SocialEngineAddOns Activity Feed" widget instead of core "Activity Feed" widget on Page Profile. (For this, go to "Layout" > "Layout Editor" > "Page Profile" and replace the already placed "Activity Feed" widget with "SocialEngineAddOns Activity Feed" widget. If you have already done this replacement for the above field, then ignore this now.)') ?></p>
    <p class="description"><input type="radio" onclick="showTips('tip_everyone_faq','everyone')" value="b" id="sitepagefeed_everyone_dummy-b" name="sitepagefeed_everyone_dummy" <?php if($dummyValue=='b'):?> checked="'checked" <?php endif; ?> >
    <a href="javascript:void(0);" onclick='javascript:$("sitepagefeed_everyone_dummy-b").checked="checked";showTips("tip_everyone_faq","everyone"); openSmoothboxFeed("<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'settings', 'action' => 'overwrite', 'type' => 'activity_feed_everyone'),'admin_default') ?>");' > <?php echo $this->translate('Click here') ?> </a>
    <?php echo $this->translate(' to automatically overwrite the file: "/application/modules/Activity/Model/DbTable/Actions.php" with the required minor modification. (If you do not want automatic overwriting of file, you can choose the 3rd option for manual changes which also shows the minor modification required.') ?>
    </p>
    <p class="description"><input type="radio" onclick="showTips('tip_everyone_faq','everyone'); faq_show('faq_59');" value="c" id="sitepagefeed_everyone_dummy-b" name="sitepagefeed_everyone_dummy"  <?php if($dummyValue=='c'):?> checked="'checked" <?php endif; ?> > <?php echo $this->translate('If you do not want to follow any of the above two options, then you can manually do the minor code modification. (Please see below the steps that you need to follow.)') ?></p>
    <div id="tip_everyone_widget" style="display: none;" >
      <div class="tip">
        <span class="sitepage_activity_tip">
        <?php if(!(bool)Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0)):?>
        <?php if (Engine_Api::_()->getApi('subCore', 'sitepage')->isCoreActivtyFeedWidget('sitepage_index_view', 'activity.feed')): ?>
        <?php echo $this->translate('Core Activity Feed widget is placed on the Page Profile page. So, either replace it with SocialEngineAddOns Activity Feed widget there or follow one of the remaining 2 options above.') ?>
        <?php else: ?>
        <?php echo $this->translate('You have not placed any of the activity feed (neither core nor SocialEngineAddOns) widget on Page Profile page.') ?>
        <?php endif; ?>
        <?php  else:?>
        <?php echo $this->translate('You have enabled the "Edit Page Layout" field from Global Settings, allowing Page Admins to control page layout. %s to replace core "Activity Feed" widget with "SocialEngineAddOns Activity Feed" widget for existing Pages on your website.',
                 " <a href=\"javascript:void(0);\" onclick='openSmoothboxFeed(\"".$this->url(array('module' => 'sitepage', 'controller' => 'settings', 'action' => 'overwrite-activity-widgets'),'admin_default')."\");' > ".$this->translate('Click here')." </a>"
                ) ?>
        <?php endif; ?>
        </span>
      </div>
    </div>
    <div id="tip_everyone_faq" style="display: none;" >
      <div class="tip">
        <span class="sitepage_activity_tip">
          <?php if (!Engine_Api::_()->getApi('subCore', 'sitepage')->isContentInFile($path, $string)): ?>
        <?php echo $this->translate("No changes have been done in the activity feed file for the above customization."); ?>
          <?php else:?>
          <?php echo $this->translate("The required changes have been successfully done for displaying all activity feeds on page profiles."); ?>
           <?php endif; ?>
        </span>
      </div>
    </div>    
    <div class='faq' style='display: none;' id='faq_59'>
      <b><?php echo $this->translate('If you do not want to set the value of "Feed Content" setting to "All Members" for User side Activity Feeds. In that also, you can make the display of activity feed on Pages independent of that setting so that page feeds can be seen by all the users. For this, you would have to follow the below steps:');?></b><br /><br />
      <?php echo $this->translate('1) Open the file with path "/application/modules/Activity/Model/DbTable/Actions.php"') ?><br /><br />
      <?php echo $this->translate('2) Now, search in this file the below given code at line no 330 (approx) :') ?><br /><br />
      <div class="code">
        <?php echo '// No visible actions <br />
                      if (empty($actions)) {<br />
                        return null;<br />
                      }<br /><br />
                    // Process ids
                    $ids = array();' ?><br />
        <img src="https://lh4.googleusercontent.com/-H76i6N9Ig2w/TjzdebHKWFI/AAAAAAAAAFI/aSAXNQzLklk/s912/feed_6_1.png" alt="" />
      </div><br />
      <?php echo $this->translate("3) Now, replace the above code with the new block of code given below:") ?><br /><br />
      <div class="code">
        <?php echo '// Process ids <br />
                    $ids = array();<br />
                    if ($about->getType() == \'sitepage_page\'  || $about->getType() == \'sitepageevent_event\') {<br />
                      $ids = Engine_Api::_()->getApi(\'subCore\', \'sitepage\')->getEveryonePageProfileFeeds($about, $this->_getInfo($params));<br />
                    }<br />
                    // No visible actions and ids <br />
                    if (empty($actions) && empty($ids)) {<br />
                      return null;<br />
                    }' ?><br />
        <img src="https://lh3.googleusercontent.com/-VZEwU7K7UMk/ToQAl1XjuBI/AAAAAAAAAFo/uwO4R-JT55c/actions_activity.png" alt="" />
      </div>
      <div class='tip'><span><b>
        <?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?></b></span>
      </div>
    </div>
  </div>
</div>
