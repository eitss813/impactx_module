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
<?php
$baseUrl = $this->layout()->staticBaseUrl;

$this->headScript()->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/lbwithoutfixwindow.js');

$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_photolightbox.css');
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
?>
<style type="text/css">
  .sitealbum_lightbox_image_content {background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .sitealbum_lightbox_user_options{background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .sitealbum_lightbox_user_right_options{background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .sitealbum_lightbox_photo_detail{background:<?php echo $coreSettings->getSetting('seaocore.photolightbox.bgcolor', '#000000'); ?>;}
  .sitealbum_lightbox_user_options a,
  .sitealbum_lightbox_photo_detail,
  .sitealbum_lightbox_photo_detail a{color:<?php echo $coreSettings->getSetting('seaocore.photolightbox.fontcolor', '#FFFFFF') ?>;}
</style>
<div class="photo_lightbox_white_content_wrapper" onclick = "closeLightBoxAlbum()">
  <div class="photo_lightbox_white_content"  id="white_content_default_album"  >
    <div id="image_div_album">       
      <div class="photo_lightbox_image_content album_viewmedia_container sitealbum_lightbox_image_content" id="media_image_div_sitealbum"></div>
      <div id="photo_lightbox_user_options"></div>
      <div class="" id="photo_lightbox_user_right_options"></div>
      <div class="photo_lightbox_text_content" id="photo_lightbox_text">
      </div>
    </div>
  </div>
</div>