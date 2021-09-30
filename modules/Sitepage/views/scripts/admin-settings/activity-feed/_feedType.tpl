<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _feedType.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$path=APPLICATION_PATH. '/application/modules/Activity/controllers/IndexController.php';
$string='if ($subject->getType() == "sitepage_page") {'; ?>
	<div id="sitepagefeed_type_dummy-wrapper" class="form-wrapper">
	<div id="sitepagefeed_type_dummy-label" class="tip">
		<span>
   		<?php echo $this->translate("If you also want to show Page's photo and title in Page Profile activity feeds for posts from Page's status sharing box in Updates tab, then follow ANY ONE of the below 3 options. (Even if you do not follow any of the below options, page activity feeds other than status posts will be displayed with Page's photo and title.)") ?>
   	</span>	
	</div>
		<div class="form-label"><?php echo $this->translate("Activity Feeds for Page Status posts"); ?></div>
    <div id="sitepagefeed_type_dummy-element" class="form-element">
      <?php $dummyValue=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagefeed.type.dummy', 'c');?>
      <p class="description"><input type="radio" onclick="showTips('tip_feed_widget','feed')" value="a" id="sitepagefeed_type_dummy-a" name="sitepagefeed_type_dummy" <?php if($dummyValue=='a'):?> checked ='checked'<?php endif; ?> ><?php echo $this->translate('Use "SocialEngineAddOns Activity Feed" widget instead of core "Activity Feed" widget. (For this, go to "Layout" > "Layout Editor" > "Page Profile" and replace the already placed "Activity Feed" widget with "SocialEngineAddOns Activity Feed" widget.)') ?> </p>
			
      <p class="description"><input type="radio" onclick="showTips('tip_feed_faq','feed')" value="b" id="sitepagefeed_type_dummy-b" name="sitepagefeed_type_dummy" <?php if($dummyValue=='b'):?> checked="checked" <?php endif; ?> >
      <a href="javascript:void(0);" onclick='javascript:$("sitepagefeed_type_dummy-b").checked="checked";showTips("tip_feed_faq","feed"); openSmoothboxFeed("<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'settings', 'action' => 'overwrite', 'type' => 'activity_feed_type'),'admin_default') ?>");' > <?php echo $this->translate('Click here') ?> </a>
      <?php echo $this->translate(' to automatically overwrite the file: "/application/modules/Activity/controllers/IndexController.php" with the required minor modification. (If you do not want automatic overwriting of file, you can choose the 3rd option for manual changes which also shows the minor modification required.') ?>    
      </p>
      <p class="description"><input type="radio" onclick="showTips('tip_feed_faq','feed'); faq_show('faq_38');" value="c" id="sitepagefeed_type_dummy-b" name="sitepagefeed_type_dummy"  <?php if($dummyValue=='c'):?> checked="checked" <?php endif; ?> ><?php echo $this->translate('If you do not want to follow any of the above two options, then you can manually do the minor code modification. (Please see below the steps that you need to follow.)') ?></p>
      <div id="tip_feed_widget" style="display: none;" >
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
      <div id="tip_feed_faq" style="display: none;" >
      	<div class="tip">
          <span class="sitepage_activity_tip">
           <?php if (!Engine_Api::_()->getApi('subCore', 'sitepage')->isContentInFile($path, $string)): ?>
            <?php echo $this->translate("No changes have been done in the activity feed file for the above customization."); ?>
          <?php else:?>
            <?php echo $this->translate("The required changes have been successfully done for showing Page photo and title in activity feeds for Page status posts."); ?>
           <?php endif; ?>
          </span>
        </div>
    	</div>
      <div class='faq' style='display: none;' id='faq_38'>
        <b><?php echo $this->translate("Please follow the steps below to manually do these changes in the file:") ?></b><br /><br />
				<?php echo $this->translate('1) Open the file with path: "/application/modules/Activity/controllers/IndexController.php".') ?><br /><br />
				<?php echo $this->translate('2) Search for the block of code given below in this file at line no. 147 (approx) :') ?><br /><br />
				<div class="code">
					<?php echo '$action = Engine_Api::_()->getDbtable(\'actions\', \'activity\')->addActivity($viewer, $subject, $type, $body); <br /><br />
					// Try to attach if necessary <br />
					if( $action && $attachment ) {' ?>
					<img src="https://lh3.googleusercontent.com/-WZK_GdQpMyw/Tife1Yhu84I/AAAAAAAAAE0/v_bt2ihO04c/before-1.jpg" alt="" />
				</div><br />
				<?php echo $this->translate("3) Now, place the below block of code just above the code mentioned above:") ?><br /><br />
				<div class="code">
					<?php echo 'if($subject->getType()=="sitepage_page"){   <br />&nbsp;
											if (Engine_Api::_()->sitepage()->isPageOwner($subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) <br />&nbsp;&nbsp;
												$action = Engine_Api::_()->getDbtable(\'actions\', \'seaocore\')->addActivity( $subject,$viewer, \'sitepage_post_self\', $body);	 <br />&nbsp;
												elseif ($subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($subject)) <br />&nbsp;&nbsp;
													$action = Engine_Api::_()->getDbtable(\'actions\', \'activity\')->addActivity($viewer, $subject, \'sitepage_post\', $body); <br />
											} else' ?>
					<img src="https://lh4.googleusercontent.com/-OHekOzbJOig/Tp_d9YDTaBI/AAAAAAAAAGA/F8_y2eNTOO8/s912/ActivityFeedIndexController.png" alt="" />
				</div><br /><br />
				<div class='tip'><span><b>
					<?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to do them again in the respective files as mentioned above.'); ?></b></span>
				</div>
			</div>
		</div>
	</div>

