<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $addThisCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.addthis',0); ?>

<?php if($addThisCode && in_array('addThis',$this->allowAdvShareOptions)) { ?>
  <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $addThisCode; ?>" async></script>
<?php } ?>

<div class="sesblog_social_share_button">
 <button class="sesbasic_popup_slide_open sesbasic_bxs sesbasic_share_btn"><i class="fa fa-share"></i> <?php echo $this->translate("Share")?></button>
</div>

<!-- Slide in -->
<div id="sesbasic_popup_slide" class="well" style="display:none">
  <div class="sesbasic_popup sesbasic_bxs">
    <div class="sesbasic_popup_title">
       <?php echo $this->translate("Share This %s",ucfirst(str_replace(array('sesblog_',''),'',$this->subject()->getType()))); ?>
      <span class="sesbasic_popup_slide_close sesbasic_text_light">
        <i class="fa fa-times"></i>
      </span>
    </div>
    <div class="sesbasic_popup_content">
      <div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
      	<div class="sesbasic_share_popup_buttons clear">
          <?php if(in_array('privateMessage',$this->allowAdvShareOptions)) { ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="opensmoothboxurl('<?php echo $this->url(array('module'=> 'sesbasic', 'controller' => 'index', 'action' => 'message','item_id' => $this->subject()->getIdentity(), 'type'=>$this->subject()->getType()),'default',true); ?>')"> <?php echo $this->translate("Private Message"); ?></a>
          <?php } ?>
          <?php if(in_array('siteShare',$this->allowAdvShareOptions)) { ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="opensmoothboxurl('<?php echo $this->url(array('module'=> 'sesblog', 'controller' =>'index','action' => 'share','type' => $this->subject()->getType(),'id' => $this->subject()->getIdentity(),'format' => 'smoothbox'),'default',true); ?>')"> <?php echo $this->translate("Share on Site"); ?></a>
          <?php } ?>
          <?php if(in_array('quickShare',$this->allowAdvShareOptions)){ ?>
            <a href="javascript:void(0)" class="sesbasic_button" onClick="sesblogsendQuickShare('<?php echo $this->url(array('module'=> 'sesblog', 'controller' =>'index','action' => 'share','type' => $this->subject()->getType(),'id' => $this->subject()->getIdentity()),'default',true); ?>');return false;"> <?php echo $this->translate("Quick Share on Site"); ?></a>
          <?php } ?>
        </div>
      </div>
      <?php if($addThisCode && in_array('addThis',$this->allowAdvShareOptions)){ ?>
        <div class="sesbasic_share_popup_content_row clear sesbasic_clearfix">
          <div class="sesbasic_share_popup_content_field clear">
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
          </div>
        </div>
      <?php } ?>
      <div class="sesbasic_share_popup_content_row">
        <div class="sesbasic_share_itme_preview sesbasic_clearfix">
          <div class="sesbasic_share_itme_preview_img">
            <img src="<?php echo $this->subject()->getPhotoUrl();?>" />
          </div>
          <div class="sesbasic_share_itme_preview_info">
            <div class="sesbasic_share_itme_preview_title">
              <a href="<?php echo $this->subject()->getHref();?>"><?php echo $this->subject()->title;?></a>
            </div>
            <div class="sesbasic_share_itme_preview_des">
              <?php if(strlen($this->subject()->body) > 200) {
                $description = mb_substr($this->subject()->body,0,200).'...';
                echo nl2br(strip_tags($description));
              } else { ?>
                <?php  echo nl2br(strip_tags($this->subject()->body));?>
              <?php } ?>
            </div>	
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery-1.8.2.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.popupoverlay.js'); ?>

<script type="text/javascript">
  jquery1_8_2SesObject(document).ready(function () {
    jquery1_8_2SesObject('#sesbasic_popup_slide').popup({
      focusdelay: 400,
      outline: true,
      vertical: 'top'
    });
  });

  <?php if(isset($_SESSION['newPage']) && $_SESSION['newPage']){ ?>
    var autoOpen = true;
  <?php }else{ ?>
    var autoOpen = false;
  <?php } ?>
  
  jquery1_8_2SesObject(document).ready(function () {
    jquery1_8_2SesObject('#sesbasic_popup_slide').popup({
        'autoopen':autoOpen,
    });
    <?php unset($_SESSION['newPage']); ?>
  });
</script>
