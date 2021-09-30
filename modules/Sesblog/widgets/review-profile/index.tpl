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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?> 
<div class="sesblog_review_view sesblog_profile_review sesbasic_bxs sesbasic_clearfix">
	<div class="sesblog_review_view_top sesbasic_clearfix">
    <?php if(in_array('title', $this->stats)): ?>
      <div class="sesblog_review_view_title"><?php echo $this->review->getTitle() ?></div>
    <?php endif; ?>
    <div class="sesblog_review_view_item_info sesbasic_clearfix">
      <?php if(in_array('postedin', $this->stats)): ?>
      	<div class="sesblog_review_view_info_img">
        	<?php echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.icon')); ?>
        </div>
      <?php endif; ?> 
      <div class="sesblog_review_view_info_cont sesbasic_clearfix">
        <?php if(in_array('postedin', $this->stats) && in_array('creationDate', $this->stats)) : ?>
          <p class='sesblog_review_view_stats sesbasic_text_light sesbasic_clearfix'>
            <?php if(in_array('postedin', $this->stats)): ?>
              <?php echo $this->translate('For');?> <?php echo $this->htmlLink($this->item, $this->item) ?>
            <?php endif; ?>
            <?php if(in_array('postedin', $this->stats) && in_array('creationDate', $this->stats)) : ?> | <?php endif; ?>
            <?php if(in_array('creationDate', $this->stats)): ?>
              <?php echo $this->translate('about').' '.$this->timestamp($this->review->creation_date) ?>
            <?php endif; ?> 
          </p>
        <?php endif; ?> 
        <p class="sesblog_review_view_stats sesbasic_text_light">
          <?php if(in_array('likeCount', $this->stats)): ?>
          <span><i class="fa fa-thumbs-up sesbasic_text_light"></i><?php echo $this->translate(array('%s like', '%s likes', $this->review->like_count), $this->locale()->toNumber($this->review->like_count)); ?></span>
          <?php endif; ?>
          <?php if(in_array('commentCount', $this->stats)): ?>
          <span><i class="fa fa-comment sesbasic_text_light"></i><?php echo $this->translate(array('%s comment', '%s comments', $this->review->comment_count), $this->locale()->toNumber($this->review->comment_count))?></span>
          <?php endif; ?>
          <?php if(in_array('viewCount', $this->stats)): ?>
          <span><i class="fa fa-eye sesbasic_text_light"></i><?php echo $this->translate(array('%s view', '%s views', $this->review->view_count), $this->locale()->toNumber($this->review->view_count))?></span>
          <?php endif; ?>
        </p>
      </div>
  	</div>
    <div class="sesblog_review_show_rating sesblog_review_listing_star">
      <?php if(in_array('rating', $this->stats)){ ?>
      <div class="sesbasic_rating_star">
        <?php $ratingCount = $this->review->rating;?>
        <?php for($i=0; $i<5; $i++){?>
          <?php if($i < $ratingCount):?>
            <span id="" class="sesblog_rating_star"></span>
          <?php else:?>
            <span id="" class="sesblog_rating_star sesblog_rating_star_disable"></span>
          <?php endif;?>
        <?php }?>
      </div>
      <?php } ?>
      <?php if(in_array('parameter', $this->stats)){ ?>
      <?php $reviewParameters = Engine_Api::_()->getDbtable('parametervalues', 'sesblog')->getParameters(array('content_id'=>$this->review->getIdentity(),'user_id'=>$this->review->owner_id)); ?>
      <?php if(count($reviewParameters)>0){ ?>
        <div class="sesblog_review_show_rating_box sesbasic_clearfix">
          <?php foreach($reviewParameters as $reviewP){ ?>
            <div class="sesbasic_clearfix">
              <div class="sesblog_review_show_rating_label"><?php echo $reviewP['title']; ?></div>
              <div class="sesblog_review_show_rating_parameters sesbasic_rating_parameter sesbasic_rating_parameter_small">
                <?php $ratingCount = $reviewP['rating'];?>
                <?php for($i=0; $i<5; $i++){?>
                  <?php if($i < $ratingCount):?>
                    <span id="" class="sesbasic-rating-parameter-unit"></span>
                  <?php else:?>
                    <span id="" class="sesbasic-rating-parameter-unit sesbasic-rating-parameter-unit-disable"></span>
                  <?php endif;?>
                <?php }?>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } 
      }
      ?>
  	</div>
  </div>
  <div class="sesblog_review_contant_disc">
  <?php if(in_array('pros', $this->stats) && $this->review->pros): ?>
    <div class="sesblog_review_view_cont_row"><b class="label"><?php echo $this->translate("Pros"); ?></b><?php echo $this->review->pros; ?></div>
  <?php endif; ?>
  <?php if(in_array('cons', $this->stats) && $this->review->cons): ?>
    <div class="sesblog_review_view_cont_row"><b class="label"><?php echo $this->translate("Cons"); ?></b><?php echo $this->review->cons; ?></div>
  <?php endif; ?>
  <?php if(in_array('customfields', $this->stats)): ?>
  	<?php $customFieldsData = Engine_Api::_()->sesblog()->getCustomFieldMapData($this->review); 
    	if(count($customFieldsData) > 0){ 
         foreach($customFieldsData as $valueMeta){
         if(!$valueMeta['value'])	
         	continue;
          echo '<p class="sesblog_review_view_cont_row"><b class="label">'. $valueMeta['label']. ': </b>'.
                $valueMeta['value'].'</p>';
         }     
 			 } ?>
  <?php endif; ?>
  <?php if(in_array('description', $this->stats)): ?>
    <div class='sesblog_review_view_cont_row'>
    	<b class="label"><?php echo $this->translate("Description"); ?></b>
      <div class="sesbasic_html_block"><?php echo $this->review->description; ?></div>
    </div>
  <?php endif; ?>
  <?php if(in_array('recommended', $this->stats)): ?>
  	<p class="sesblog_review_view_cont_row sesblog_review_view_recommended">
      <b><?php echo $this->translate("Recommended"); ?>
      <?php if($this->review->recommended): ?>
      	<i class="fa fa-check"></i></b>
      <?php else: ?>
        <i class="fa fa-times"></i></b>
      <?php endif; ?>
    </p>
  <?php endif; ?>
  </p>
</div>
<div class="sesblog_layout_contant" style="padding:0px;">
	<div class="sesblog_footer_two_blog ">
		<div class="sesblog_list_share_btns sesbasic_bxs">
		  <?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->review->getHref()); ?>
		  <?php if(in_array('socialSharing', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)):?>
		  
        <?php echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $this->review, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

			<?php endif;?>
			<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.allow.share', 1) && $this->viewer()->getIdentity() && in_array('share', $this->stats) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)): ?>
				<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $this->review->getType(), 'id' => $this->review->getIdentity()), 'default', true);?>" class="sesbasic_icon_btn share_icon smoothbox"><i class="fa fa-share "></i></a>
			<?php endif; ?>
			<?php $canComment =  $this->review->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');?>
			<?php if(in_array('likeButton', $this->stats) && $canComment):?>
				<?php $LikeStatus = Engine_Api::_()->sesblog()->getLikeStatus($this->review->review_id,$this->review->getType()); ?>
					<a href="javascript:;" data-url="<?php echo $this->review->review_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_like_btn  sesblog_like_sesblog_review_<?php echo $this->review->review_id ?> sesblog_like_sesblog_review_view <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"><i class="<?php if($LikeStatus):?>fa fa-thumbs-down<?php else:?>fa fa-thumbs-up<?php endif;?>"></i><span><?php if($LikeStatus):?><?php echo $this->translate('Unlike');?><?php else:?><?php echo $this->translate('Like');?><?php endif;?></span></a>
			<?php endif;?>
		</div>
	</div>
</div>
