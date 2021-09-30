<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: videofeed-profile.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->viewPermission): ?>
  <?php
  $video = $this->video;


  $tags = $video->getVideoTags();
  $description = trim($video->getVideoDescription());
  ?>
  <div class="photo_lightbox_cont">
    <div class="photo_lightbox_left" id="siteviewvideo_lightbox_left" style="background-color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.bgcolor', '#0A0A0A') ?>">
      <table width="100%" height="100%">
        <tr>
          <td width="100%" height="100%" valign="middle">
            <div class="photo_lightbox_image" id='media_image_div_seaocore'>
              <div class="video_view video_view_container"> 


                <div class="video_embed">
                  <object width="750" height="440">
                    <param name="movie" value="https://www.youtube.com/v/<?php echo $this->video->getVideoId(); ?>&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
                    <param name="allowFullScreen" value="true"/>
                    <param name="allowScriptAccess" value="always"/>
                    <embed src="https://www.youtube.com/v/<?php echo $this->video->getVideoId(); ?>&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1&autoplay=1" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="750" height="440" wmode="transparent"/>
                    <param name="wmode" value="transparent" />
                  </object>
                </div> 
              </div>
              <div class="video_viewer_stats">
                <div id="sitevideovideo_rating" class="rating fleft"> 
                  <?php
                  $rating = $video->getVideoRatingInfo();
                  //Array ( [average] => 4.59322 [numRaters] => 59 ) 
                  ?>
                  <span class="videofeed_rating_star">
                    <span class="videofeed_rating_star_average" style="width: <?php echo $rating['average'] / 5 * 100 ?>%;"></span>
                  </span>
                </div>
                <div class="fright">		
                  <?php echo $this->translate('Updated %s', $this->timestamp(strtotime($video->getUpdated())));?> | <?php echo $this->translate(array('%s view','%s views',$video->getVideoViewCount()), $this->locale()->toNumber($video->getVideoViewCount()))?>
                </div>                
              </div>             
            </div>
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.gotovideo', 1)): ?>
                <div style="position:absolute;bottom:20px;right:10px;">
                  <a style="text-decoration:none;"  href="<?php echo $this->url(array('videofeed_id'=>$video->getVideoId()), 'videofeed_profile', true)?>">
                    <span class="lightbox_btm_bl_btn_video"> 
                      <i></i><?php echo $this->translate("Go to Video") ?>
                    </span>
                  </a>           
                </div>
            <?php endif; ?>   
          </td>
        </tr>     
      </table>
    </div>
    <div class="photo_lightbox_right" id="photo_lightbox_right_content"> 
      <div id="main_right_content_area"  style="height: 100%">
        <div id="main_right_content" class="scroll_content">        
          <div id="photo_right_content" class="photo_lightbox_right_content">
            <div class='photo_right_content_top'>
              <div class='photo_right_content_top_l'>               
              </div>
              <div class='photo_right_content_top_r'>   
                <?php echo $this->video->getVideoTitle(); ?>
              </div>

            </div>
            <?php if ($description): ?>
              <div class="photo_right_content_top_title photo_right_content_top_caption">
                <div id="link_svvideo_description">                
                  <span id="svvideo_description" class="lightbox_photo_description">		              
                    <?php echo $this->viewMore($description); ?>	             
                  </span>		           
                </div>

              </div>
            <?php endif; ?>
            <div class="photo_right_content_tags" style="margin-bottom:5px;">  
            </div>   
            <?php if (!empty($tags)): ?>
              <div class="photo_right_content_tags">
                <b> <?php echo $this->translate('Tags:') ?></b>	
                <?php foreach ($tags as $tag): ?> 
                  # <?php echo $this->htmlLink(array('route' => 'videofeed_general', 'action' => 'browse', 'keyword' => $tag), $tag) ?>
                <?php endforeach; ?>
              </div>  
            <?php endif; ?>



          </div>
        </div>
      </div>
      <div class="photo_right_content_add" id="ads">
      </div>
    </div>
  </div>
<div class="lightbox_btm_bl" style="">
  </div>
<?php else: ?>
  <div  class="photo_lightbox_cont" style="background-color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.bgcolor', '#0A0A0A') ?>">
    <div class="video_viewer_video_content" >
      <h4><?php echo $this->translate("Private Page") ?></h4>
      <div class="video_viewer_thumb_wrapper">
        <?php echo $this->htmlLink($this->subject()->getHref(), $this->itemPhoto($this->subject(), 'thumb.normal'), array('class' => 'thumb')) ?>
      </div>
      <div class="video_viewer_video_info">  
        <b><?php echo $this->htmlLink($this->subject()->getHref(), $this->subject()->getTitle()) ?></b>              
      </div>          
      <div class="video_viewer_privacy_msg clr">
        <img src="./application/modules/Seaocore/externals/images/notice.png" alt="" style="width: 20px;" />
        <span>
          <?php
          echo $this->translate($this->message);
          ?></span>
      </div>
    </div>
  </div>
  <div class="lightbox_btm_bl">    
  </div>
<?php endif; ?>
<?php if (empty($this->is_ajax_lightbox)): ?>
  <div id="ads_hidden_siteviewvideo" style="display: none;" >
    <?php echo $this->content()->renderWidget("seaocore.lightbox-ads", array('limit' => 1)) ?>
  </div>
<?php endif; ?>

<style type="text/css">
  .video_viewer_stats * {
    color: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.fontcolor', '#FFFFFF') ?>;
  }
  .photo_lightbox_cont{
    bottom: 0px;
  }
  .lightbox_btm_bl{
    height: 0px;
  }

.lightbox_btm_bl_btn_video {
	background:url(./application/modules/Seaocore/externals/images/plbbi.png) repeat-x bottom;
	text-decoration:none;
	color:#FFFFFF;
	border-radius: 3px 3px 3px 3px;
	cursor: pointer;
	float: left;
	font-size: 12px;
	font-weight: bold;
	height: 26px;
	line-height: 26px;
	padding: 0 10px;
	text-decoration: none;
}
.lightbox_btm_bl_btn_video{
	background:url(./application/modules/Seaocore/externals/images/plbbi.png) repeat-x bottom;
	text-decoration:none;
	color:#FFFFFF;
}
.lightbox_btm_bl_btn_video i {
	background-image: url(./application/modules/Sitevideo/externals/images/video_default.png);
	background-position:top;
	float: left;
	height: 10px;
	margin-right: 6px;
	margin-top: 8px;
	width: 18px;
}
.lightbox_btm_bl_btn_video:hover i {
	background-position:bottom;
}
</style>
