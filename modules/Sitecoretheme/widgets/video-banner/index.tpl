<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="sitecoretheme_video_banner_wrapper" style=" background-color:">
  <?php $src = $this->settings('sitecoretheme.landing.videobanner.image') ?: 'application/modules/Sitecoretheme/externals/images/video-baanner.jpg' ?>
  <img class="_banner" src="<?php echo $src ?>" alt="" >
  <div class="sitecoretheme_video_banner_inner _info-section-inner">
    <div class="_wrap-inner">
      <div class="_icon_box">
        <a class="_video_action_btn" href="<?php
        if( $this->settings('sitecoretheme.landing.videobanner.videoType') == 1 ): echo $this->settings('sitecoretheme.landing.videobanner.videoUrl');
        else:
          ?> javascript:void();<?php endif; ?>" data-type="iframe">
          <div class="image_wrapper">
            <div class="_image_layer"></div>
            <div class="_image_layer"></div>
            <div class="_image_layer">
              <div>
                <i class="fa fa-play"></i>
              </div>
            </div>
          </div>
          <div class="_heading_wrapper">
            <div class="_heading"><h2><?php echo $this->translate($this->settings('sitecoretheme.landing.videobanner.heading', "Watch the short video and see how we work")) ?></h2></div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
  .sitecoretheme_video_banner_inner ._wrap-inner ._heading_wrapper ._heading h2 {
    color: <?php echo $this->settings('sitecoretheme.landing.videobanner.color', '#FFFFFF') ?>;
  } 
</style>

<script type="text/javascript">
  $$('.sitecoretheme_video_banner_wrapper ._video_action_btn').addEvent('click', function (e) {
    e.stop();
<?php if( $this->settings('sitecoretheme.landing.videobanner.videoType') == 1 ) : ?>
  <?php $url = ( $this->settings('sitecoretheme.landing.videobanner.videoType') == 1 && stripos($this->settings('sitecoretheme.landing.videobanner.videoUrl'), '.mp4') !== false) && 0 ? $this->url(array('module' => 'sitecoretheme', 'controller' => 'general', 'action' => 'video-url'), 'default', true) . '?url=' . urlencode($this->settings('sitecoretheme.landing.videobanner.videoUrl')) : $this->settings('sitecoretheme.landing.videobanner.videoUrl') ?>
      SmoothboxSEAO.open({
        iframe: {
          src: '<?php echo $url ?>'
        }
      });
<?php else: ?>
      SmoothboxSEAO.open({
        embed: {
          code: '<?php echo $this->string()->escapeJavascript($this->settings('sitecoretheme.landing.videobanner.videoEmbed', ''), false); ?>'
        }
      });
<?php endif; ?>
  });

</script>