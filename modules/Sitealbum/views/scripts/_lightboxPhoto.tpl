<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _lightboxPhoto.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<div class="photo_lightbox" id="album_light" style="display: none;">
  <?php
  $this->headScript()
          ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
          ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
          ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
          ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js')
          ->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/tagger/tagger.js')
          ->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/core.js')
          ->appendFile($baseUrl . 'application/modules/Album/externals/scripts/core.js');
  $this->headTranslate(array('Save', 'Cancel', 'delete', 'remove tag'));

  $coreSettings = Engine_Api::_()->getApi('settings', 'core');
  ?>
  <style type="text/css">
    .photo_lightbox_left, 
    .sitealbum_lightbox_image_content {background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
    .sitealbum_lightbox_user_options{background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
    .sitealbum_lightbox_user_right_options{background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
    .sitealbum_lightbox_photo_detail{background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
    .sitealbum_lightbox_user_options a,
    .sitealbum_lightbox_photo_detail,
    .sitealbum_lightbox_photo_detail a{color:<?php echo $coreSettings->getSetting('seaocore.photolightbox.fontcolor', '#FFFFFF') ?>;}
  </style>
  <?php
  if ($coreSettings->getSetting('sea.lightbox.fixedwindow', 1)):
    $this->headScript()
            ->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js')
            ->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/lbfixwindow.js');
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_advanced_photolightbox.css')
            ->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
  else:
    ?>
    <input type="hidden" id="canReloadSitealbum" value="0" />
    <div class="photo_lightbox_overlay"></div>
    <?php
    echo $this->partial('_lightboxPhotoWithoutFixWindow.tpl', 'sitealbum');
  endif;
  ?>
</div>
<script type = "text/javascript">
  siteablum_loading_image = '<?php echo $coreSettings->getSetting('sitealbum.lightbox.onloadshowthumb', 1) ?>';
  en4.core.staticBaseUrl = '<?php echo $this->escape($this->layout()->staticBaseUrl) ?>';
  window.addEvent('domready', function() {
    en4.core.language.addData({
      "Add a caption": "<?php echo $this->string()->escapeJavascript($this->translate("Add a caption")); ?>",
      "Close": "<?php echo $this->string()->escapeJavascript($this->translate("Close")); ?>",
      "Press Esc to Close": "<?php echo $this->string()->escapeJavascript($this->translate("Press Esc to Close")); ?>",
      "Press Esc to exit Full-screen": "<?php echo $this->string()->escapeJavascript($this->translate("Press Esc to exit Full-screen")); ?>",
      "Add a title": "<?php echo $this->string()->escapeJavascript($this->translate("Add a title")); ?>"
    });
  });
</script>