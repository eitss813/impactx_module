<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _composeSitepageVideo.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php  if (Engine_Api::_()->core()->hasSubject() && in_array($this->subject()->getType(),array('sitepage_page','sitepageevent_event', 'siteevent_event'))):?>
<?php $subject = $this->subject();
 if(in_array($subject->getType(),array('siteevent_event'))):
    $subject = $this->subject()->getParent();
    if($subject->getType() != 'sitepage_page')
			return;
 endif;
?>
<style type="text/css">
  /*
ACTIVITY FEED COMPOSER  VIDEO
These styles are used for the attachment composer above the
main feed.
*/
#compose-video-activator,
#compose-video-menu span
{
 display: none !important;
}
</style>
 <?php
 if(in_array($subject->getType(),array('sitepageevent_event'))):
    $subject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
 endif;
  //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagevideo")) {
        return;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        return;
      }
    }
 if (!Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit') &&!Engine_Api::_()->sitepage()->isManageAdmin($subject,'svcreate') ):
    return;
 endif; ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitepagevideo/externals/scripts/composer_video.js') ?>
<?php
    $allowed = 0;
    $user = Engine_Api::_()->user()->getViewer();
    $youtubeEnabled = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    $allowed_upload = 1;
    $ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->sitepagevideo_ffmpeg_path;
    if($allowed_upload && $ffmpeg_path) $allowed = 1;
$allowed_upload = 0;
    ?>

    <?php $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.manifestUrl', "page-videos");?>

<script type="text/javascript">
  var videoRouteURL = '<?php echo $routeStart; ?>';
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.SitepageVideo({
      title : '<?php echo $this->translate('Add Video') ?>',
      lang : {
        'Add Video' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
        'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
        'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube')) ?>',
        'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo')) ?>',
        'To upload a video from your computer, please use our full uploader.': '<?php echo addslashes($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create','page_id'=>$subject->getIdentity(), 'type'=>3), 'sitepagevideo_general'))) ?>'
      },
      allowed : <?php echo $allowed;?>,
      youtubeEnabled: <?php echo (int) $youtubeEnabled?>,
      type : type,
      requestOptions : {
        'url' : en4.core.baseUrl + 'sitepagevideo/index/compose-upload/format/json/c_type/'+type
      }
    }));
  });
</script>
<?php endif; ?>
