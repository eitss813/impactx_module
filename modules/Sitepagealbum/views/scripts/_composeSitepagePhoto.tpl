<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id:_composeSitepagePhoto.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
ACTIVITY FEED COMPOSER  ALBUM PHOTO
These styles are used for the attachment composer above the
main feed.
*/
#compose-photo-activator,
#compose-photo-menu span
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
      if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagealbum")) {
        return;
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'spcreate');
      if (empty($isPageOwnerAllow)) {
        return;
      }
    }
 if (!Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit') &&!Engine_Api::_()->sitepage()->isManageAdmin($subject,'spcreate') ):
    return;
  endif;
?>
<?php
  $this->headScript()
     ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepagealbum/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepagealbum/externals/scripts/composer_photo.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/Request.Blob.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/Uploader.HTML5.js');
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/seao-fancy-uploader/uploader.css');
  $this->headTranslate(array(
    'Remove', 'Click to remove this entry.',
    'Upload failed', 'Upload Progress ({size})',
    '{name} already added.', 'An error occurred.',
    'FAILED ( {name} ) : {error}', 'Reached Maximum File Uploads',
    'Minimum Files Size Deceeded - {filename} ( {filesize} )',
    'Maximum Files Size Exceeded - {filename} ( {filesize} )',
    'Invalid File Type - %s (%s)',
    'Add Photos'
  ));
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.SitepagePhoto({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
      lang : {
        'Add Photo' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl + 'sitepage/album/compose-upload/type/'+type+'/page_id/'+<?php echo $subject->getIdentity() ?>
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl + 'sitepage/album/compose-upload/format/json/type/'+type+'/page_id/'+<?php echo $subject->getIdentity() ?>,
      }
    }));
  });
</script>
<?php endif; ?>
