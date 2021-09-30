<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _showVideoListGrid.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php if(isset($this->optionsEnable) && in_array('pinboard',$this->optionsEnable) && !$this->is_ajax){ 
	 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/imagesloaded.pkgd.js');
	 $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/pinboard.css'); 
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/wookmark.min.js');
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/pinboardcomment.js');
  
 } ?>
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
      }
?>
<?php if($this->canUpload && !$this->is_ajax && $this->allow_create){ ?>
  <div class="sesbasic_profile_tabs_top sesbasic_clearfix">
    <a class="sesbasic_button fa fa-plus" href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'create', 'parent_id' => $this->parent_id, 'parent_type' => $this->parent_type), 'sesvideo_general', true); ?>">
      <?php echo $this->translate('Post New Video'); ?>
    </a>
  </div>
<?php } ?>
<?php if(!$this->is_ajax){ ?>
  <div class="sesbasic_view_type sesbasic_clearfix clear" style="display:<?php echo $this->bothViewEnable ? 'block' : 'none'; ?>;height:<?php echo $this->bothViewEnable ? '' : '0px'; ?>">
  	<div class="sesbasic_view_type_options sesbasic_view_type_options_<?php echo $randonNumber; ?>">
    <?php if(is_array($this->optionsEnable) && in_array('list',$this->optionsEnable)){ ?>
      <a href="javascript:;" rel="list" id="sesvideo_list_view_<?php echo $randonNumber; ?>" class="listicon selectView_<?php echo $randonNumber; ?> <?php if($this->view_type == 'list') { echo 'active'; } ?>" title="<?php echo $this->translate('List View') ; ?>"></a>
    <?php } ?>
    <?php if(is_array($this->optionsEnable) && in_array('grid',$this->optionsEnable)){ ?>
    	<a href="javascript:;" rel="grid" id="sesvideo_grid_view_<?php echo $randonNumber; ?>" class="gridicon selectView_<?php echo $randonNumber; ?> <?php if($this->view_type == 'grid') { echo 'active'; } ?>" title="<?php echo $this->translate('Grid View'); ?>"></a>
    <?php } ?>
    <?php if(is_array($this->optionsEnable) && in_array('pinboard',$this->optionsEnable)){ ?>
     	<a href="javascript:;" rel="pinboard" id="sesvideo_pinboard_view_<?php echo $randonNumber; ?>" class="boardicon selectView_<?php echo $randonNumber; ?> <?php if($this->view_type == 'pinboard') { echo 'active'; } ?>" title="<?php echo $this->translate('Pinboard View'); ?>"></a>
    <?php } ?>
    </div>
  </div>
<?php } ?>
<?php if((isset($this->optionsListGrid['tabbed']) || $this->loadJs || isset($this->optionsListGrid['profileTabbed'])) && !$this->is_ajax){ ?>
  <div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix sesbasic_bxs clear">
    <ul class="sesvideo_video_listing sesbasic_clearfix clear" id="tabbed-widget_<?php echo $randonNumber; ?>" style="min-height:50px;">
<?php } ?>
       <?php if(isset($this->chanelId)){ 
       		$chanelCustomUrl = array('type'=>'sesvideo_chanel','item_id'=>$this->chanelId);
       }else if(isset($this->playlistId)){ 
       		$chanelCustomUrl = array('type'=>'sesvideo_playlist','item_id'=>$this->playlistId);
       }else{
       		$chanelCustomUrl = array();
      	} 
        ?>        
      <?php foreach( $this->paginator as $item ):
        if(isset($this->getVideoItem) && $this->getVideoItem == 'getVideoItem'){
         $oldItem = $item;
        	if(isset($item->video_id))
           $item = Engine_Api::_()->getItem('video', $item->video_id);
          else if(isset($item->resource_id))
           $item = Engine_Api::_()->getItem('video', $item->resource_id);
          else
           $item = Engine_Api::_()->getItem('video', $item->file_id);
          if(isset($oldItem->watchlater_id)){
          	$watchlater_watch_id = $oldItem->watchlater_id;
            $watchlater_watch_id_exists = 'YES';
            }
        }else if(isset($this->getChanelItem) && $this->getChanelItem == 'getChanelItem'){
        	if(isset($item->chanel_id))
           $item = Engine_Api::_()->getItem('sesvideo_chanel', $item->chanel_id);
         else if(isset($item->resource_id))
           $item = Engine_Api::_()->getItem('sesvideo_chanel', $item->resource_id);
        }
        /*Rating code start*/
       if($item->getType() == 'video'){
          $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.rating',1);
          $allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.show',1);
          if($allowRating == 0){
            if($allowShowPreviousRating == 0)
              $ratingShow = false;
             else
              $ratingShow = true;
          }else
            $ratingShow = true;
       }else if($item->getType() == 'sesvideo_chanel'){
          $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.chanel.rating',1);
          $allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratechanel.show',1);
          if($allowRating == 0){
            if($allowShowPreviousRating == 0)
              $ratingShow = false;
             else
              $ratingShow = true;
          }else
            $ratingShow = true;
       }else
       	$ratingShow = true;
       /*Rating code End*/
      ?>
			<?php if($this->view_type == 'grid' && $this->viewTypeStyle == 'mouseover'){ ?>
        <li class="sesvideo_listing_in_grid2 <?php if((isset($this->my_videos) && $this->my_videos) || (isset($this->my_channel) && $this->my_channel)){ ?>isoptions<?php } ?>" style="width:<?php echo is_numeric($this->width_grid) ? $this->width_grid.'px' : $this->width_grid ?>;height:<?php echo is_numeric($this->height_grid) ? $this->height_grid.'px' : $this->height_grid ?>;">
          <div class="sesvideo_listing_in_grid2_thumb sesvideo_thumb sesvideo_play_btn_wrap">
            <?php
              $href = $item->getHref($chanelCustomUrl);
              $imageURL = $item->getPhotoUrl();
            ?>
            <a href="<?php echo $href; ?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesvideo_chanel' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>">
              <span style="background-image:url(<?php echo $imageURL; ?>);"></span>
            </a>
            <?php  if($item->getType() == 'video') { ?>
            <a href="<?php echo $item->getHref($chanelCustomUrl)?>" data-url = "<?php echo $item->getType() ?>" class="sesvideo_play_btn fa fa-play-circle sesvideo_thumb_img">
            	<span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);display:none;"></span>
            </a>  
            <?php } ?>
            <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
              <p class="sesvideo_labels">
              <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
                <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
              <?php } ?>
              <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
                <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
              <?php } ?>
              <?php if(isset($this->hotLabelActive) && $item->is_hot == 1){ ?>
                <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
              <?php } ?>
              </p>
             <?php } ?>
            <?php if(isset($this->durationActive) && isset($item->duration) && $item->duration ): ?>
              <span class="sesvideo_length">
                <?php
                  if( $item->duration >= 3600 ) {
                    $duration = gmdate("H:i:s", $item->duration);
                  } else {
                    $duration = gmdate("i:s", $item->duration);
                  }
                  echo $duration;
           ?>
              </span>
            <?php endif ?>
             <?php if(isset($this->watchLaterActive) && (isset($item->watchlater_id) || isset($watchlater_watch_id_exists)) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
            <?php $watchLaterId = isset($watchlater_watch_id_exists) ? $watchlater_watch_id : $item->watchlater_id; ?>
              <a href="javascript:;" class="sesvideo_watch_later_btn sesvideo_watch_later <?php echo !is_null($watchLaterId)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($watchLaterId)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $item->video_id ; ?>"></a>
            <?php } ?>
						<?php
           		if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive) || isset($this->playlistAddActive)){
          		$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
           		<div class="sesvideo_thumb_btns"> 
              	<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
              	
                  <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

                <?php } 
                if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ){
                       if(isset($item->chanel_id)){
                              $itemtype = 'sesvideo_chanel';
                              $getId = 'chanel_id';
                            }else{
                              $itemtype = 'sesvideo_video';
                              $getId = 'video_id';
                            }
                      $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                      if(isset($this->likeButtonActive) && $canComment){
                    ?>
                  <!--Like Button-->
                  <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->$getId,$item->getType()); ?>
                    <a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $itemtype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
                    <?php } ?>
                     <?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)){ ?>
                    <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$itemtype,'resource_id'=>$item->$getId)); ?>
                    <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $itemtype; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
                  <?php } ?>
                     <?php if(empty($item->chanel_id) && isset($this->playlistAddActive)){ ?>
                    <a href="javascript:;" onclick="opensmoothboxurl('<?php echo $this->url(array('action' => 'add','module'=>'sesvideo','controller'=>'playlist','video_id'=>$item->video_id),'default',true); ?>')" class="sesbasic_icon_btn sesvideo_add_playlist"  title="<?php echo  $this->translate('Add To Playlist'); ?>" data-url="<?php echo $item->video_id ; ?>"><i class="fa fa-plus"></i></a>
                  <?php } ?>
                <?php  } ?>
              </div>
            <?php } ?>      
          </div>
          <?php if(isset($this->titleActive) ){ ?>
            <div class="sesvideo_listing_in_grid2_title_show sesvideo_animation">
            	<?php if(strlen($item->getTitle()) > $this->title_truncation_grid){ 
              				$title = mb_substr($item->getTitle(),0,$this->title_truncation_grid).'...';
                       echo $this->htmlLink($item->getHref(),$title,array('title'=>$item->getTitle())  ) ?>
              <?php }else{ ?>
              <?php echo $this->htmlLink($item->getHref(),$item->getTitle(),array('title'=>$item->getTitle())  ) ?>
              <?php } ?>
            </div>
            <?php } ?>
          <div class="sesvideo_listing_in_grid2_info clear sesvideo_animation">
          	<?php if(isset($this->titleActive) ){ ?>
            <div class="sesvideo_listing_in_grid2_title">
            	<?php if(strlen($item->getTitle()) > $this->title_truncation_grid){ 
              				$title = mb_substr($item->getTitle(),0,$this->title_truncation_grid).'...';
                       echo $this->htmlLink($item->getHref(),$title,array('title'=>$item->getTitle())  ) ?>
              <?php }else{ ?>
              <?php echo $this->htmlLink($item->getHref(),$item->getTitle(),array('title'=>$item->getTitle())  ) ?>
              <?php } ?>
            </div>
            <?php } ?>
            <?php if(isset($this->byActive)){ ?>
              <div class="sesvideo_listing_in_grid2_date sesvideo_list_stats sesbasic_text_light">
                <?php $owner = $item->getOwner(); ?>
                <span>
                  <i class="fa fa-user"></i>
                  <?php echo $this->translate("by") ?> <?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?>
                </span>
              </div>
             <?php } ?>
            <?php if(isset($this->categoryActive)){ ?>
            <?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)){ 
               $categoryItem = Engine_Api::_()->getItem('sesvideo_category', $item->category_id);
            ?>
            <div class="sesvideo_listing_in_grid2_date sesvideo_list_stats sesbasic_text_light">
              <span>
                <i class="fa fa-folder-open" title="<?php echo $this->translate('Category'); ?>"></i> 
                  <a href="<?php echo $categoryItem->getHref(); ?>">
                  <?php echo $categoryItem->category_name; ?></a>
                  <?php $subcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subcat_id); ?>
                   <?php if($subcategory){ ?>
                      &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
                  <?php } ?>
                  <?php $subsubcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subsubcat_id); ?>
                   <?php if($subsubcategory){ ?>
                      &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
                  <?php } ?>
              </span>
            </div>
           <?php } ?>
             <?php } ?>
             <?php if(isset($this->locationActive) && isset($item->location) && $item->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
            	<div class="sesvideo_listing_in_grid2_date sesvideo_list_stats sesvideo_list_location sesbasic_text_light">
                <span>
                  <i class="fa fa-map-marker"></i>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?>
                    <a href="javascript:;" onclick="openURLinSmoothBox('<?php echo $this->url(array("module"=> "sesvideo", "controller" => "index", "action" => "location",  "video_id" => $item->getIdentity(),'type'=>'video_location'),'default',true); ?>');return false;"><?php echo $item->location; ?></a>
                  <?php } else { ?>
                    <?php echo $item->location; ?>
                  <?php } ?>
                </span>
              </div>
            <?php } ?>
            <div class="sesvideo_listing_in_grid2_date sesvideo_list_stats sesbasic_text_light">
              <?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
              <?php } ?>
              <?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span>
              <?php } ?>
               <?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
                    <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span>
                  <?php } ?>
                  
              <?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
              <?php } ?>
               <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
              <span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?></span>
            <?php endif; ?>
            </div>
            <?php if($this->isBlogAdmin):?>
							<?php if($this->can_edit || $item->status !=2 && $this->can_delete){ ?>
								<div class="sesvideo_listing_in_grid2_date sesbasic_text_light">
									<span class="sesvideo_list_option_toggle fa fa-ellipsis-v sesbasic_text_light"></span>
									<div class="sesvideo_listing_options_pulldown">
										<?php if($this->can_edit){ 
											echo $this->htmlLink(array('route' => 'sesvideo_general','module' => 'sesvideo','controller' => 'index','action' => 'edit','video_id' => $item->video_id), $this->translate('Edit Video')) ; 
										} ?>
										<?php if ($item->status !=2 && $this->can_delete){
											echo $this->htmlLink(array('route' => 'sesvideo_general', 'module' => 'sesvideo', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id), $this->translate('Delete Video'), array('onclick' =>'opensmoothboxurl(this.href);return false;'));
										} ?>
									</div>
								</div>
							<?php } ?>
							<div class="sesvideo_manage_status_tip">
								<?php if($item->status == 0):?>
										<div class="tip">
											<span>
												<?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
											</span>
										</div>
										<?php elseif($item->status == 2):?>
										<div class="tip">
											<span>
												<?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
											</span>
										</div>
										<?php elseif($item->status == 3):?>
										<div class="tip">
											<span>
											<?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
											</span>
										</div>
										<?php elseif($item->status == 4):?>
										<div class="tip">
											<span>
											<?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
											</span>
										</div>
										<?php elseif($item->status == 5):?>
										<div class="tip">
											<span>
											<?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
											</span>
										</div>
										<?php elseif($item->status == 7):?>
										<div class="tip">
											<span>
											<?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
											</span>
										</div>
								<?php elseif(!$item->approve):?>
										<div class="tip">
											<span>
											<?php echo $this->translate('Your video has been successfully submitted for approval to our adminitrators - you will be notified when it is ready to be viewed.'); ?>
											</span>
										</div>
								<?php endif;?>
						  <?php endif;?>
						</div>
          </div>
        </li>
        <?php }else if($this->view_type == 'grid' && $this->viewTypeStyle != 'mouseover'){ ?>
        <li class="sesvideo_listing_grid <?php if((isset($this->my_videos) && $this->my_videos) || (isset($this->my_channel) && $this->my_channel)){ ?>isoptions<?php } ?>" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
          <div class="sesvideo_grid_thumb sesvideo_thumb sesvideo_play_btn_wrap" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
            <?php
              $href = $item->getHref($chanelCustomUrl);
              $imageURL = $item->getPhotoUrl();
            ?>
            <a href="<?php echo $href; ?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesvideo_chanel' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>">
              <span style="background-image:url(<?php echo $imageURL; ?>);"></span>
            </a>
            <?php  if($item->getType() == 'video') { ?>
            <a href="<?php echo $item->getHref($chanelCustomUrl)?>" data-url = "<?php echo $item->getType() ?>" class="sesvideo_play_btn fa fa-play-circle sesvideo_thumb_img">
            	<span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);display:none;"></span>
            </a>  
            <?php } ?>
            <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
              <p class="sesvideo_labels">
              <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
                <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
              <?php } ?>
              <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
                <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
              <?php } ?>
              <?php if(isset($this->hotLabelActive) && $item->is_hot == 1){ ?>
                <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
              <?php } ?>
              </p>
             <?php } ?>
            <?php if(isset($this->durationActive) && isset($item->duration) && $item->duration ): ?>
              <span class="sesvideo_length">
                <?php
                  if( $item->duration >= 3600 ) {
                    $duration = gmdate("H:i:s", $item->duration);
                  } else {
                    $duration = gmdate("i:s", $item->duration);
                  }
                  echo $duration;
           ?>
              </span>
            <?php endif ?>
             <?php if(isset($this->watchLaterActive) && (isset($item->watchlater_id) || isset($watchlater_watch_id_exists)) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
            <?php $watchLaterId = isset($watchlater_watch_id_exists) ? $watchlater_watch_id : $item->watchlater_id; ?>
              <a href="javascript:;" class="sesvideo_watch_later_btn sesvideo_watch_later <?php echo !is_null($watchLaterId)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($watchLaterId)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $item->video_id ; ?>"></a>
            <?php } ?>
						<?php
           		if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive) || isset($this->playlistAddActive)){
          		$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
           		<div class="sesvideo_thumb_btns"> 
              	<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
                  <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
                  
                <?php } 
                if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ){
                       if(isset($item->chanel_id)){
                              $itemtype = 'sesvideo_chanel';
                              $getId = 'chanel_id';
                            }else{
                              $itemtype = 'sesvideo_video';
                              $getId = 'video_id';
                            }
                      $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                      if(isset($this->likeButtonActive) && $canComment){
                    ?>
                  <!--Like Button-->
                  <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->$getId,$item->getType()); ?>
                    <a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $itemtype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
                    <?php } ?>
                     <?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)){ ?>
                    <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$itemtype,'resource_id'=>$item->$getId)); ?>
                    <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $itemtype; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
                  <?php } ?>
                     <?php if(empty($item->chanel_id) && isset($this->playlistAddActive)){ ?>
                    <a href="javascript:;" onclick="opensmoothboxurl('<?php echo $this->url(array('action' => 'add','module'=>'sesvideo','controller'=>'playlist','video_id'=>$item->video_id),'default',true); ?>')" class="sesbasic_icon_btn sesvideo_add_playlist"  title="<?php echo  $this->translate('Add To Playlist'); ?>" data-url="<?php echo $item->video_id ; ?>"><i class="fa fa-plus"></i></a>
                  <?php } ?>
                <?php  } ?>
              </div>
            <?php } ?>      
          </div>
          <div class="sesvideo_grid_info clear">
          	<?php if(isset($this->titleActive) ){ ?>
            <div class="sesvideo_grid_title">
            	<?php if(strlen($item->getTitle()) > $this->title_truncation_grid){ 
              				$title = mb_substr($item->getTitle(),0,$this->title_truncation_grid).'...';
                       echo $this->htmlLink($item->getHref(),$title,array('title'=>$item->getTitle()) ) ?>
              <?php }else{ ?>
              <?php echo $this->htmlLink($item->getHref(),$item->getTitle(),array('title'=>$item->getTitle())  ) ?>
              <?php } ?>
            </div>
            <?php } ?>
            <?php if(isset($this->byActive)){ ?>
              <div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light">
              	<?php $owner = $item->getOwner(); ?>
                <span>
                  <i class="fa fa-user"></i>
                  <?php echo $this->translate("by") ?> <?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?>
                </span>
              </div>
             <?php } ?>
            <?php if(isset($this->categoryActive)){ ?>
            <?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)){ 
            	 $categoryItem = Engine_Api::_()->getItem('sesvideo_category', $item->category_id);
            ?>
            <div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light">
              	<span>
                	<i class="fa fa-folder-open" title="<?php echo $this->translate('Category'); ?>"></i> 
                  	<a href="<?php echo $categoryItem->getHref(); ?>">
                  	<?php echo $categoryItem->category_name; ?></a>
                		<?php $subcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subcat_id); ?>
                     <?php if($subcategory){ ?>
                        &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
                    <?php } ?>
                    <?php $subsubcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subsubcat_id); ?>
                     <?php if($subsubcategory){ ?>
                        &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
                    <?php } ?>
                </span>
            </div>
             <?php } ?>
             <?php } ?>
              <?php if(isset($this->locationActive) && isset($item->location) && $item->location  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
            	<div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light sesvideo_list_location">
                <span>
                  <i class="fa fa-map-marker"></i>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?>
                    <a href="javascript:;" onclick="openURLinSmoothBox('<?php echo $this->url(array("module"=> "sesvideo", "controller" => "index", "action" => "location",  "video_id" => $item->getIdentity(),'type'=>'video_location'),'default',true); ?>');return false;"><?php echo $item->location; ?></a>
                  <?php } else { ?>
                    <?php echo $item->location; ?>
                  <?php } ?>
                </span>
              </div>
            <?php } ?>
            <div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light">
              <?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
              <?php } ?>
              <?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span>
              <?php } ?>
               <?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
                  	<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span>
                  <?php } ?>
                  
              <?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
              <?php } ?>
               <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
              <span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?></span>
            <?php endif; ?>
            </div>
           <?php  if(isset($this->descriptiongridActive)){ ?>
                <div class="sesvideo_list_des">
                  <?php if(strlen($item->description) > $this->description_truncation_grid){ 
              				$description = mb_substr($item->description,0,$this->description_truncation_grid).'...';
                      echo $title = nl2br(strip_tags($description));
                  	 }else{ ?>
                  <?php  echo nl2br(strip_tags($item->description));?>
                  <?php } ?>
                </div>
                <?php } ?>
						<?php if($this->can_edit || $item->status !=2 && $this->can_delete){ ?>
							<div class="sesvideo_grid_date sesbasic_text_light">
								<span class="sesvideo_list_option_toggle fa fa-ellipsis-v sesbasic_text_light"></span>
								<div class="sesvideo_listing_options_pulldown">
									<?php if($this->can_edit){ 
										echo $this->htmlLink(array('route' => 'sesvideo_general','module' => 'sesvideo','controller' => 'index','action' => 'edit','video_id' => $item->video_id), $this->translate('Edit Video')) ; 
									} ?>
									<?php if ($item->status !=2 && $this->can_delete){
										echo $this->htmlLink(array('route' => 'sesvideo_general', 'module' => 'sesvideo', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id), $this->translate('Delete Video'), array('onclick' =>'opensmoothboxurl(this.href);return false;'));
									} ?>
								</div>
							</div>
						<?php } ?>
						<div class="sesvideo_manage_status_tip">
							<?php if($item->status == 0):?>
									<div class="tip">
										<span>
											<?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
										</span>
									</div>
									<?php elseif($item->status == 2):?>
									<div class="tip">
										<span>
											<?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
										</span>
									</div>
									<?php elseif($item->status == 3):?>
									<div class="tip">
										<span>
										<?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
										</span>
									</div>
									<?php elseif($item->status == 4):?>
									<div class="tip">
										<span>
										<?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
										</span>
									</div>
									<?php elseif($item->status == 5):?>
									<div class="tip">
										<span>
										<?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
										</span>
									</div>
									<?php elseif($item->status == 7):?>
									<div class="tip">
										<span>
										<?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
										</span>
									</div>
							<?php elseif(!$item->approve):?>
									<div class="tip">
										<span>
										<?php echo $this->translate('Your video has been successfully submitted for approval to our adminitrators - you will be notified when it is ready to be viewed.'); ?>
										</span>
									</div>
							<?php endif;?>
						</div>
          </div>
        </li>
        <?php }else if($this->view_type == 'list'){ ?>
        		<li class="sesvideo_listing_list sesbasic_clearfix clear">
              <div class="sesvideo_list_thumb sesvideo_thumb sesvideo_play_btn_wrap" style="height:<?php echo is_numeric($this->height_list) ? $this->height_list.'px' : $this->height_list ?>;width:<?php echo is_numeric($this->width_list) ? $this->width_list.'px' : $this->width_list ?>;">
             <?php
                $href = $item->getHref($chanelCustomUrl);
                $imageURL = $item->getPhotoUrl();
              ?>
              <a href="<?php echo $href; ?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesvideo_chanel' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>">
              	<span style="background-image:url(<?php echo $imageURL; ?>);"></span>
              </a>
              <?php if($item->getType() != 'sesvideo_chanel'){ ?>
              <a href="<?php echo $item->getHref($chanelCustomUrl)?>" data-url = "<?php echo $item->getType() ?>" class="sesvideo_play_btn fa fa-play-circle <?php echo $item->getType() != 'sesvideo_chanel' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>">
              	<span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);display:none;"></span>
              </a> 
              <?php } ?>
              <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
              <p class="sesvideo_labels">
              <?php if(isset($this->featuredLabelActive) && isset($item->is_featured) && $item->is_featured == 1){ ?>
                <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
              <?php } ?>
              <?php if(isset($this->sponsoredLabelActive) && isset($item->is_sponsored) && $item->is_sponsored == 1){ ?>
                <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
              <?php } ?>
               <?php if(isset($this->hotLabelActive) && $item->is_hot == 1){ ?>
                <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
              <?php } ?>
              </p>
             <?php } ?>
              <?php if(isset($this->durationActive) && isset($item->duration) && $item->duration ): ?>
                <span class="sesvideo_length">
                  <?php
                    if( $item->duration >= 3600 ) {
                      $duration = gmdate("H:i:s", $item->duration);
                    } else {
                      $duration = gmdate("i:s", $item->duration);
                    }
                    echo $duration;
                  ?>
                </span>
              <?php endif ?>
               <?php if(isset($this->watchLaterActive) && (isset($item->watchlater_id) || isset($watchlater_watch_id_exists)) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
            <?php $watchLaterId = isset($watchlater_watch_id_exists) ? $watchlater_watch_id : $item->watchlater_id; ?>
              <a href="javascript:;" class="sesvideo_watch_later_btn sesvideo_watch_later <?php echo !is_null($watchLaterId)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($watchLaterId)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $item->video_id ; ?>"></a>
            <?php } ?>    
            						<?php
           		if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive) || isset($this->playlistAddActive)){
          		$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
           		<div class="sesvideo_thumb_btns"> 
              	<?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
              	
                  <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>

                <?php } 
                if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ){
                       if(isset($item->chanel_id)){
                              $itemtype = 'sesvideo_chanel';
                              $getId = 'chanel_id';
                            }else{
                              $itemtype = 'sesvideo_video';
                              $getId = 'video_id';
                            }
                      $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                      if(isset($this->likeButtonActive) && $canComment){
                    ?>
                  <!--Like Button-->
                  <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->$getId,$item->getType()); ?>
                    <a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $itemtype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
                    <?php } ?>
                     <?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)){ ?>
                    <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$itemtype,'resource_id'=>$item->$getId)); ?>
                    <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $itemtype; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
                  <?php } ?>
                     <?php if(empty($item->chanel_id) && isset($this->playlistAddActive)){ ?>
                    <a href="javascript:;" onclick="opensmoothboxurl('<?php echo $this->url(array('action' => 'add','module'=>'sesvideo','controller'=>'playlist','video_id'=>$item->video_id),'default',true); ?>')" class="sesbasic_icon_btn sesvideo_add_playlist"  title="<?php echo  $this->translate('Add To Playlist'); ?>" data-url="<?php echo $item->video_id ; ?>"><i class="fa fa-plus"></i></a>
                  <?php } ?>
                <?php  } ?>
              </div>
            <?php } ?>    
          	</div>
            <div class="sesvideo_list_info">
            	<?php  if(isset($this->titleActive)){ ?>
                <div class="sesvideo_list_title">
                 <?php if(strlen($item->getTitle()) > $this->title_truncation_list){
              				$title = mb_substr($item->getTitle(),0,$this->title_truncation_list).'...';
                       echo $this->htmlLink($item->getHref(),$title,array('title'=>$item->getTitle())  );
                  	 }else{ ?>
                  <?php echo $this->htmlLink($item->getHref(),$item->getTitle(),array('title'=>$item->getTitle())  ) ?>
                  <?php } ?>
                </div>
              <?php } ?>
              <?php if(isset($this->byActive)){ ?>
                <div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light">
                  <?php $owner = $item->getOwner(); ?>
                  <span>
                    <i class="fa fa-user"></i>
                    <?php echo $this->translate("by") ?> <?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?>
                  </span>
                </div>
               <?php } ?>
               <?php if(isset($this->categoryActive)){ ?>
                <?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)){ 
                     $categoryItem = Engine_Api::_()->getItem('sesvideo_category', $item->category_id);
                  	if($categoryItem){
                  ?>
                  <div class="sesvideo_list_date sesvideo_list_stats sesbasic_text_light">
                      <span>
                      		<i class="fa fa-folder-open" title="<?php echo $this->translate('Category'); ?>"></i> 
                          	<a href="<?php echo $categoryItem->getHref(); ?>"><?php echo $categoryItem->category_name; ?></a>
                      			<?php $subcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subcat_id); ?>
                             <?php if($subcategory){ ?>
                                &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
                            <?php } ?>
                            <?php $subsubcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subsubcat_id); ?>
                             <?php if($subsubcategory){ ?>
                                &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
                            <?php } ?>
                      </span>
                  </div>
                   <?php 
                   	}
                   } ?>
                 <?php } ?>
                 <?php if(isset($this->locationActive) && isset($item->location) && $item->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
            	<div class="sesvideo_list_date sesvideo_list_stats sesbasic_text_light sesvideo_list_location">
                <span>
                  <i class="fa fa-map-marker"></i>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?>
                    <a href="javascript:;" onclick="openURLinSmoothBox('<?php echo $this->url(array("module"=> "sesvideo", "controller" => "index", "action" => "location",  "video_id" => $item->getIdentity(),'type'=>'video_location'),'default',true); ?>');return false;"><?php echo $item->location; ?></a>
                  <?php } else { ?>
                    <?php echo $item->location; ?>
                  <?php } ?>
                </span>
              </div>
            <?php } ?>
                <div class="sesvideo_list_date sesvideo_list_stats sesbasic_text_light">
                	<?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
                  	<span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
                  <?php } ?>
                  <?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
                  	<span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span>
                  <?php } ?>
                  
                  <?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
                  	<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span>
                  <?php } ?>
                  
                  <?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
                  	<span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
                  <?php } ?>
                  <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
                  <span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?></span>
                <?php endif; ?>
                </div>
                
                <?php if(isset($this->descriptionlistActive)){ ?>
                <div class="sesvideo_list_des">
                  <?php if(strlen($item->description) > $this->description_truncation_list){ 
              				$description = mb_substr($item->description,0,$this->description_truncation_list).'...';
                      echo $title = nl2br(strip_tags($description));
                  	 }else{ ?>
                  <?php  echo nl2br(strip_tags($item->description));?>
                  <?php } ?>
                </div>
                <?php } ?>
								
                <div class="sesvideo_options_buttons sesvideo_list_options sesbasic_clearfix"> 
                    <?php if($this->isBlogAdmin):?>
											<?php if($this->can_edit){ ?>
												<a href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'edit', 'video_id' => $item->video_id), 'sesvideo_general', true); ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate('Edit Video'); ?>">
													<i class="fa fa-edit"></i>
												</a>
											<?php } ?>
											<?php if ($item->status !=2 && $this->can_delete){ ?>
												<a href="<?php echo $this->url(array('module' => 'sesvideo', 'action' => 'delete', 'video_id' => $item->video_id), 'sesvideo_general', true); ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate('Delete Video'); ?>" onclick='opensmoothboxurl(this.href);return false;'>
													<i class="fa fa-trash"></i>
											</a>
											<?php } ?>
											<?php if($item->status == 0):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
													</span>
												</div>
											<?php elseif($item->status == 2):?>
												<div class="tip">
													<span>
														<?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
													</span>
												</div>
											<?php elseif($item->status == 3):?>
												<div class="tip">
													<span>
													<?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif($item->status == 4):?>
												<div class="tip">
													<span>
													<?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif($item->status == 5):?>
												<div class="tip">
													<span>
													<?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif($item->status == 7):?>
												<div class="tip">
													<span>
													<?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
													</span>
												</div>
											<?php elseif(!$item->approve):?>
										<div class="tip">
											<span>
												<?php echo $this->translate('Your video has been successfully submitted for approval to our adminitrators - you will be notified when it is ready to be viewed.'); ?>
											</span>
										</div>
									<?php endif;?>
                <?php endif;?>
                </div>
              </div>
            </li>
        <?php }else if(isset($this->view_type) && $this->view_type == 'pinboard'){ ?>
        		  <li class="sesbasic_bxs sesbasic_pinboard_list_item_wrap new_image_pinboard">
              	<div class="sesbasic_pinboard_list_item sesbm <?php if((isset($this->my_videos) && $this->my_videos) || (isset($this->my_channel) && $this->my_channel)){ ?>isoptions<?php } ?>">
                	<div class="sesbasic_pinboard_list_item_top sesvideo_play_btn_wrap">
                  	<div class="sesbasic_pinboard_list_item_thumb">
                  		<a href="<?php echo $item->getHref($chanelCustomUrl)?>" data-url = "<?php echo $item->getType() ?>" class="<?php echo $item->getType() != 'sesvideo_chanel' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>">
                      	<img src="<?php echo $item->getPhotoUrl(); ?>">
                        	<span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);display:none;"></span>
                      </a>
                    </div>
                    <?php if ($item->getType() == 'video'){ ?>
                    <a href="<?php echo $item->getHref($chanelCustomUrl)?>" data-url = "<?php echo $item->getType() ?>" class="sesvideo_play_btn fa fa-play-circle <?php echo $item->getType() != 'sesvideo_chanel' ? 'sesvideo_thumb_img' : 'sesvideo_thumb_nolightbox' ?>">
                    	<span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);display:none;"></span>
                    </a>           
                    <?php  } ?>         
                    <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive) || isset($this->hotLabelActive)){ ?>
                      <div class="sesbasic_pinboard_list_label">
                      <?php if(isset($this->featuredLabelActive) && $item->is_featured == 1){ ?>
                        <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
                      <?php } ?>
                      <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored == 1){ ?>
                        <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                      <?php } ?>
                      <?php if(isset($this->hotLabelActive) && $item->is_hot == 1){ ?>
                        <span class="sesvideo_label_hot"><?php echo $this->translate("HOT"); ?></span>
                      <?php } ?>
                      </div>
                     <?php } ?>                    
                  <?php if((isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)) || isset($this->likeButtonActive) || isset($this->favouriteButtonActive) || isset($this->playlistAddActive)){
                    $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
                     <div class="sesbasic_pinboard_list_btns"> 
                   <?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
                    
                    <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
                    

                    <?php } 
                    if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ){
                           if(isset($item->chanel_id)){
                                  $itemtype = 'sesvideo_chanel';
                                  $getId = 'chanel_id';
                                }else{
                                  $itemtype = 'sesvideo_video';
                                  $getId = 'video_id';
                                }
                          $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                          if(isset($this->likeButtonActive) && $canComment){
                        ?>
                      <!--Like Button-->
                      <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->$getId,$item->getType()); ?>
                        <a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $itemtype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
                        <?php } ?>
                         <?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)){ ?>
                        
                        <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$itemtype,'resource_id'=>$item->$getId)); ?>
                        <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $itemtype; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
                      <?php } ?>
                      
                         <?php if(empty($item->chanel_id) && isset($this->playlistAddActive)){ ?>
                        <a href="javascript:;" onclick="opensmoothboxurl('<?php echo $this->url(array('action' => 'add','module'=>'sesvideo','controller'=>'playlist','video_id'=>$item->video_id),'default',true); ?>')" class="sesbasic_icon_btn sesvideo_add_playlist"  title="<?php echo  $this->translate('Add To Playlist'); ?>" data-url="<?php echo $item->video_id ; ?>"><i class="fa fa-plus"></i></a>
                      <?php } ?>
                    <?php  } ?>
                  </div>
                  <?php } ?>
										<?php if(isset($this->durationActive) && isset($item->duration) && $item->duration ): ?>
                      <span class="sesvideo_length">
                        <?php
                          if( $item->duration >= 3600 ) {
                            $duration = gmdate("H:i:s", $item->duration);
                          } else {
                            $duration = gmdate("i:s", $item->duration);
                          }
                          echo $duration;
                        ?>
                      </span>
                    <?php endif ?>
                     <?php if(isset($this->watchLaterActive) && (isset($item->watchlater_id) || isset($watchlater_watch_id_exists)) && Engine_Api::_()->user()->getViewer()->getIdentity() != '0'){ ?>
                    <?php $watchLaterId = isset($watchlater_watch_id_exists) ? $watchlater_watch_id : $item->watchlater_id; ?>
                      <a href="javascript:;" class="sesvideo_watch_later_btn sesvideo_watch_later <?php echo !is_null($watchLaterId)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($watchLaterId)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $item->video_id ; ?>"></a>
                    <?php } ?>  
                  </div>
                  <div class="sesbasic_pinboard_list_item_cont sesbasic_clearfix">
              			<div class="sesbasic_pinboard_list_item_title">
                    <?php if(strlen($item->getTitle()) > $this->title_truncation_pinboard){ 
              				 $title = mb_substr($item->getTitle(),0,$this->title_truncation_pinboard).'...';
                       echo $this->htmlLink($item->getHref(),$title ) ?>
                    <?php }else{ ?>
                    <?php echo $this->htmlLink($item->getHref(),$item->getTitle() ) ?>
                    <?php } ?>   
                    </div>                   
                    <?php if(isset($this->categoryActive)){ ?>
                      <?php if($item->category_id != '' && intval($item->category_id) && !is_null($item->category_id)){ 
                         $categoryItem = Engine_Api::_()->getItem('sesvideo_category', $item->category_id);
                      ?>
                      <div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light">
                        <span>
                          <i class="fa fa-folder-open" title="<?php echo $this->translate('Category');?>"></i> 
                          <a href="<?php echo $categoryItem->getHref(); ?>">
                          	<?php echo $categoryItem->category_name; ?>
                          </a>
                          <?php $subcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subcat_id); ?>
                           <?php if($subcategory){ ?>
                              &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
                          <?php } ?>
                          <?php $subsubcategory = Engine_Api::_()->getItem('sesvideo_category',$item->subsubcat_id); ?>
                           <?php if($subsubcategory){ ?>
                              &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
                          <?php } ?>
                        </span>
                      </div>
                       <?php } ?>
                    <?php } ?>
										  <?php if(isset($this->locationActive) && isset($item->location) && $item->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){ ?>
            	<div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light sesvideo_list_location">
                <span>
                  <i class="fa fa-map-marker"></i>
                  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?>
                    <a href="javascript:;" onclick="openURLinSmoothBox('<?php echo $this->url(array("module"=> "sesvideo", "controller" => "index", "action" => "location",  "video_id" => $item->getIdentity(),'type'=>'video_location'),'default',true); ?>');return false;"><?php echo $item->location; ?></a>
                  <?php } else { ?>
                    <?php echo $item->location; ?>
                  <?php } ?>
                </span>
              </div>
            <?php } ?>
                    <?php if(isset($this->descriptionpinboardActive)){ ?>
                    	<div class="sesbasic_pinboard_list_item_des">
                      <?php if(strlen($item->description) > $this->description_truncation_pinboard){ 
                          $description = mb_substr($item->description,0,$this->description_truncation_pinboard).'...';
                          echo $title = nl2br(strip_tags($description));
                         }else{ ?>
                  <?php  echo nl2br(strip_tags($item->description));?>
                  </div>
                  <?php } ?>
                		<?php } ?>
                    <?php if($this->isBlogAdmin){ ?>
                    	<?php if($this->can_edit || $item->status !=2 && $this->can_delete){ ?>
                        <div class="sesvideo_grid_date sesbasic_text_light">
                          <span class="sesvideo_list_option_toggle fa fa-ellipsis-v sesbasic_text_light"></span>
                          <div class="sesvideo_listing_options_pulldown">
                            <?php if($this->can_edit){ 
                              echo $this->htmlLink(array('route' => 'sesvideo_general','module' => 'sesvideo','controller' => 'index','action' => 'edit','video_id' => $item->video_id), $this->translate('Edit Video')) ; 
                            } ?>
                            <?php if ($item->status !=2 && $this->can_delete){
                              echo $this->htmlLink(array('route' => 'sesvideo_general', 'module' => 'sesvideo', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id), $this->translate('Delete Video'), array('onclick' =>'opensmoothboxurl(this.href);return false;'));
                            } ?>
                          </div>
                      	</div>
                      <?php } ?>
                      <div class="sesvideo_manage_status_tip">
                        <?php if($item->status == 0):?>
                           <div class="tip">
                             <span>
                               <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
                             </span>
                           </div>
                           <?php elseif($item->status == 2):?>
                           <div class="tip">
                             <span>
                               <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
                             </span>
                           </div>
                           <?php elseif($item->status == 3):?>
                           <div class="tip">
                             <span>
                              <?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
                             </span>
                           </div>
                           <?php elseif($item->status == 4):?>
                           <div class="tip">
                             <span>
                              <?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
                             </span>
                           </div>
                           <?php elseif($item->status == 5):?>
                           <div class="tip">
                             <span>
                              <?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
                             </span>
                           </div>
                           <?php elseif($item->status == 7):?>
                           <div class="tip">
                             <span>
                              <?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="'.$this->url(array('action' => 'create','module'=>'sesvideo','controller'=>'index'),'default',true).'/type/3'.'">', '</a>'); ?>
                             </span>
                           </div>
                        <?php endif;?>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="sesbasic_pinboard_list_item_btm sesbm sesbasic_clearfix">
                  	<?php if(isset($this->byActive)){ ?>    
                      <div class="sesbasic_pinboard_list_item_poster sesbasic_text_light sesbasic_clearfix">
                        <?php $owner = $item->getOwner(); ?>
                        <div class="sesbasic_pinboard_list_item_poster_thumb">
                        	<?php echo $this->htmlLink($item->getOwner()->getParent(), $this->itemPhoto($item->getOwner()->getParent(), 'thumb.icon')); ?>
                        </div>
                        <div class="sesbasic_pinboard_list_item_poster_info">
                          <span class="sesbasic_pinboard_list_item_poster_info_title"><?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?></span>
                          <span class="sesbasic_pinboard_list_item_poster_info_stats sesbasic_text_light">
                            <?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
                              <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
                            <?php } ?>
                            <?php if(isset($this->commentActive) && isset($item->comment_count)) { ?>
                              <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span>
                            <?php } ?>
                             <?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
                                  <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span>
                                <?php } ?>                          
                            <?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
                              <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
                            <?php } ?>
                               <?php if(isset($this->ratingActive) && $ratingShow && isset($item->rating) && $item->rating > 0 ): ?>
                      <span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?></span>
                    <?php endif; ?>  
                          </span>
                        </div>
                      </div>
										<?php } ?>
                 <?php if(isset($this->enableCommentPinboardActive)){ ?>
                    <div class="sesbasic_pinboard_list_comments sesbasic_clearfix">
                    <?php $itemType = '';?>
                    <?php if(isset($item->video_id)){ 
                    	$item_id = $item->video_id; 
                      $itemType = 'video';
                     }else if (isset($item->chanel_id)){ 
                    	$item_id = $item->chanel_id; 
                      $itemType = 'sesvideo_chanel';
                     } ?>
                    <?php if(($itemType != '')){ ?>
                    	<?php echo (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment') ? $this->action('list', 'comment', 'sesadvancedcomment', array('type' => $itemType, 'id' => $item_id,'page'=>'')) : $this->action("list", "comment", "sesbasic", array("item_type" => $itemType, "item_id" => $item_id,"widget_identity"=>$randonNumber))); ?>
                      <?php } ?>
                    </div>
                 <?php } ?>
                  </div>
              	</div>
              </li>
        <?php
        }else if(isset($this->view_type) && $this->view_type == 'playlist'){ ?>
            <li class="sesvideo_listing_list sesbasic_clearfix clear">
            <div class="sesvideo_browse_playlist_thumb sesvideo_list_thumb sesvideo_thumb">
              <a href="<?php echo $item->getHref(); ?>" class="sesvideo_thumb_nolightbox">
                <span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span>
              </a>
              <?php if(isset($this->featuredLabelActive) || isset($this->sponsoredLabelActive)){ ?>
                <p class="sesvideo_labels">
                <?php if(isset($this->featuredLabelActive) && $item->is_featured ){ ?>
                  <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
                <?php } ?>
                <?php if(isset($this->sponsoredLabelActive) && $item->is_sponsored ){ ?>
                  <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                <?php } ?>
                </p>
               <?php } ?>
							<div class="sesvideo_thumb_btns">
             		<?php
          			$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
              <?php if(isset($this->socialSharingActive)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.sharing', 1)){ ?>
                
                    <?php  echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $item, 'socialshare_enable_plusicon' => $this->socialshare_enable_plusicon, 'socialshare_icon_limit' => $this->socialshare_icon_limit)); ?>
                
                    <?php } ?>
               <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0): ?>
              	 <?php  if(isset($this->likeButtonActive)){ ?>
              <!--Like Button-->
              <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->playlist_id,'sesvideo_playlist'); ?>
                <a href="javascript:;" data-url="<?php echo $item->playlist_id ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_sesvideo_playlist <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
                <?php } ?>
                 <?php if(isset($this->favouriteButtonActive) && isset($item->favourite_count)  && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)){ ?>
                <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>'sesvideo_playlist','resource_id'=>$item->playlist_id)); ?>
                <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_sesvideo_playlist <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->playlist_id ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
              <?php } ?>
                <a  href="javascript:void(0);" class="smoothbox sesbasic_icon_btn" title="<?php echo $this->translate("Share") ?>" onclick="openURLinSmoothBox('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesvideo_playlist', 'id' => $item->playlist_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;">
                	<i class="fa fa-share"></i>
                </a>
                 <?php if(isset($this->my_playlist)){ ?>
                  <a href="<?php echo $this->url(array('action'=>'edit', 'playlist_id'=>$item->getIdentity(),'slug'=>$item->getSlug()),'sesvideo_playlist_view',true) ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate("Edit Playlist") ?>">
                    <i class="fa fa-edit"></i>
                  </a>
                  <a href="<?php echo $this->url(array('action'=>'delete', 'playlist_id'=>$item->getIdentity(),'slug'=>$item->getSlug(),  'format' => 'smoothbox'),'sesvideo_playlist_view',true) ?>" class="sesbasic_icon_btn smoothbox" title="<?php echo $this->translate("Delete Playlist") ?>">
                    <i class="fa fa-trash"></i>
                    </a>
                 <?php } ?>
                 <?php endif; ?>
              </div>
            </div>
            <div class="sesvideo_list_info">
              <?php if(!empty($this->titleActive)): ?>
              <div class="sesvideo_list_title">
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle(),array('title'=>$item->getTitle()) ) ?>
              </div>
              <?php endif; ?>
              <?php if(!empty($this->byActive)): ?>
              <div class="sesvideo_list_date sesbasic_text_light">
                <?php echo $this->translate('Created By %s', $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle())) ?>
              </div>
              <?php endif; ?>
              <div class="sesvideo_grid_date sesvideo_list_stats sesbasic_text_light">
              <?php if(isset($this->likeActive) && isset($item->like_count)) { ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
              <?php } ?>
               <?php if(isset($this->favouriteActive) && isset($item->favourite_count) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
                  	<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span>
                  <?php } ?>
              <?php if(isset($this->viewActive) && isset($item->view_count)) { ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
              <?php } ?>
              </div>
              <?php if(!empty($this->descriptionActive)): ?>
                <div class="sesvideo_list_des">
                  <?php echo $this->viewMore(nl2br($item->description)); ?>
                </div>
              <?php endif; ?>
              
              <?php $playlist = $item;
              $videos = $item->getVideos();
                $playlistUrl = array('type'=>'sesvideo_playlist','item_id'=>$item->getIdentity());
             ?>
              <?php if(count($videos) > 0): ?>
              <div class="clear sesbasic_clearfix sesvideo_videos_minilist_container sesbm sesbasic_custom_scroll">
                <ul class="clear sesvideo_videos_minilist sesbasic_bxs">
                  <?php foreach( $videos as $videoItem ): ?>
                  <?php $video = Engine_Api::_()->getItem('video', $videoItem->file_id); ?>
                  <?php if( !empty($video) ): ?>
                  <li class="sesbasic_clearfix sesbm">
                    <div class="sesvideo_videos_minilist_photo">
                      <a class="sesvideo_thumb_img" data-url = "<?php echo $item->getType() ?>" href="<?php echo $video->getHref($playlistUrl); ?>">
                        <span style="background-image:url(<?php echo $video->getPhotoUrl() ?>);"></span>
                      </a>
                    </div>
                   
                     <?php if( isset($this->watchLaterActive) && isset($videoItem->watchlater_id)){ ?>
                      <div class="sesvideo_videos_minilist_buttons">
                        <a href="javascript:;" class="sesbasic_icon_btn sesvideo_watch_later <?php echo !is_null($videoItem->watchlater_id)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($videoItem->watchlater_id)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $video->video_id ; ?>"><i class="far fa-clock"></i></a>
                      </div>
                    <?php } ?>
                    <div class="sesvideo_videos_minilist_name" title="<?php echo $video->title ?>">
                    	<?php echo $this->htmlLink($video->getPhotoUrl(), $this->htmlLink($video->getHref(), $video->title), array()); ?>
                    </div>
                  </li>
                  <?php endif; ?>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php endif; ?>
               </div>
            </li>
      <?php 	} ?>
        <?php endforeach; ?>
        <?php  if(  $this->paginator->getTotalItemCount() == 0){  ?>
           <div class="sesbasic_tip clearfix">
    <img src="application/modules/Sesblog/externals/images/video_icon.png" alt="">
    <span class="sesbasic_text_light">
      <?php echo $this->translate('Nobody has created a video yet.');?>
                  <?php if ($this->can_create):?>
                    <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "sesvideo_general").'">', '</a>'); ?>
                  <?php endif; ?>
  </div>
              
    			<?php } ?>
     <?php
   if((isset($this->optionsListGrid['paggindData']) || $this->loadJs) && $this->loadOptionData == 'pagging' && !isset($this->show_limited_data)){ ?>
 		 <?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesvideo"),array('identityWidget'=>$randonNumber)); ?>
 <?php } ?>
 <?php if(!$this->is_ajax){ ?>
 <?php if(isset($this->optionsListGrid['tabbed']) || $this->loadJs){ ?>
  </ul>
 <?php } ?>
 <?php if((isset($this->optionsListGrid['paggindData']) || $this->loadJs) && $this->loadOptionData != 'pagging' && !isset($this->show_limited_data)){ ?>
  <div class="sesbasic_load_btn" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'sesbasic_link_btn')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" /> </div>
 <?php }
 	 if(isset($this->optionsListGrid['tabbed']) || $this->loadJs){ ?>
</div>
<?php } ?>
<?php if(isset($this->optionsListGrid['tabbed']) && !$this->is_ajax){ ?>
</div>
<?php } ?>
<?php if(!$this->is_ajax){ ?>
<script type="application/javascript">var requestTab_<?php echo $randonNumber; ?>;</script>
<?php } ?>
<?php if(isset($this->optionsListGrid['paggindData']) || isset($this->loadJs)){ ?>
<script type="text/javascript">
var valueTabData ;
// globally define available tab array
	var availableTabs_<?php echo $randonNumber; ?>;
	var requestTab_<?php echo $randonNumber; ?>;
<?php if(isset($defaultOptionArray)){ ?>
  availableTabs_<?php echo $randonNumber; ?> = <?php echo json_encode($defaultOptionArray); ?>;
<?php  } ?>
<?php if($this->loadOptionData == 'auto_load' && !isset($this->show_limited_data)){ ?>
		window.addEvent('load', function() {
		 sesJqueryObject(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
sesJqueryObject(document).on('click','.selectView_<?php echo $randonNumber; ?>',function(){
		if(sesJqueryObject(this).hasClass('active'))
			return;
		if($("view_more_<?php echo $randonNumber; ?>"))
			document.getElementById("view_more_<?php echo $randonNumber; ?>").style.display = 'none';
		document.getElementById("tabbed-widget_<?php echo $randonNumber; ?>").innerHTML = "<div class='clear sesbasic_loading_container'></div>";
		sesJqueryObject('#sesvideo_grid_view_<?php echo $randonNumber; ?>').removeClass('active');
		sesJqueryObject('#sesvideo_list_view_<?php echo $randonNumber; ?>').removeClass('active');
		sesJqueryObject('#sesvideo_pinboard_view_<?php echo $randonNumber; ?>').removeClass('active');
		sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display','none');
		sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').css('display','none');
		sesJqueryObject(this).addClass('active');
		if(sesJqueryObject('#filter_form').length)
			var	searchData<?php echo $randonNumber; ?> = sesJqueryObject('#filter_form').serialize();
		 if (typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestTab_<?php echo $randonNumber; ?>.cancel();
		 }
		 if (typeof(requestViewMore_<?php echo $randonNumber; ?>) != 'undefined') {
			 requestViewMore_<?php echo $randonNumber; ?>.cancel();
		 }
	  requestTab_<?php echo $randonNumber; ?> = (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/" + defaultOpenTab,
      'data': {
        format: 'html',
        page: 1,
				type:sesJqueryObject(this).attr('rel'),
				params : <?php echo json_encode($this->params); ?>, 
				is_ajax : 1,
				searchParams:searchData<?php echo $randonNumber; ?>,
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = responseHTML;
			if($("loading_image_<?php echo $randonNumber; ?>"))
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
			pinboardLayout_<?php echo $randonNumber ?>();
      }
    })).send();
});
</script>
<?php } ?>
<?php } ?>
<?php if(!$this->is_ajax){ ?>
<script type="application/javascript">
	var wookmark = undefined;
 //Code for Pinboard View
  var wookmark<?php echo $randonNumber ?>;
  function pinboardLayout_<?php echo $randonNumber ?>(force){
		if(sesJqueryObject('.sesbasic_view_type_options_<?php echo $randonNumber; ?>').find('.active').attr('rel') != 'pinboard'){
		 sesJqueryObject('#tabbed-widget_<?php echo $randonNumber; ?>').removeClass('sesbasic_pinboard_<?php echo $randonNumber; ?>');
		 sesJqueryObject('#tabbed-widget_<?php echo $randonNumber; ?>').css('height','');
	 		return;
	  }
		sesJqueryObject('.new_image_pinboard').css('display','none');
		var imgLoad = imagesLoaded('#tabbed-widget_<?php echo $randonNumber; ?>');
		imgLoad.on('progress',function(instance,image){
			sesJqueryObject(image.img.offsetParent).parent().parent().show();
			sesJqueryObject(image.img.offsetParent).parent().parent().removeClass('new_image_pinboard');
			imageLoadedAll<?php echo $randonNumber ?>();
		});
  }
  function imageLoadedAll<?php echo $randonNumber ?>(force){
	 sesJqueryObject('#tabbed-widget_<?php echo $randonNumber; ?>').addClass('sesbasic_pinboard_<?php echo $randonNumber; ?>');
	 if (typeof wookmark<?php echo $randonNumber ?> == 'undefined') {
			(function() {
				function getWindowWidth() {
					return Math.max(document.documentElement.clientWidth, window.innerWidth || 0)
				}				
				wookmark<?php echo $randonNumber ?> = new Wookmark('.sesbasic_pinboard_<?php echo $randonNumber; ?>', {
					itemWidth: <?php echo isset($this->width_pinboard) ? str_replace(array('px','%'),array(''),$this->width_pinboard) : '300'; ?>, // Optional min width of a grid item
					outerOffset: 0, // Optional the distance from grid to parent
					align:'left',
					flexibleWidth: function () {
						// Return a maximum width depending on the viewport
						return getWindowWidth() < 1024 ? '100%' : '40%';
					}
				});
			})();
    } else {
      wookmark<?php echo $randonNumber ?>.initItems();
      wookmark<?php echo $randonNumber ?>.layout(true);
    }
}
 sesJqueryObject(window).resize(function(e){
    pinboardLayout_<?php echo $randonNumber ?>('',true);
   });
 sesJqueryObject(window).resize(function(e){
  pinboardLayout_<?php echo $randonNumber ?>('',true);
 });
sesJqueryObject(document).ready(function(){
	pinboardLayout_<?php echo $randonNumber ?>();
})
</script>
<?php } ?>
<?php if(isset($this->optionsListGrid['paggindData']) || isset($this->loadJs)){ ?>
<script type="text/javascript">
var defaultOpenTab ;
<?php if(isset($this->optionsListGrid['paggindData'])) {?>
	function changeTabSes_<?php echo $randonNumber; ?>(valueTab){
			if(sesJqueryObject("#sesTabContainer_<?php echo $randonNumber ?>_"+valueTab).hasClass('active'))
				return;
			var id = '_<?php echo $randonNumber; ?>';
			var length = availableTabs_<?php echo $randonNumber; ?>.length;
			for (var i = 0; i < length; i++){
				if(availableTabs_<?php echo $randonNumber; ?>[i] == valueTab){
					document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).addClass('active');
					sesJqueryObject('#sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).addClass('sesbasic_tab_selected');
				}
				else{
						sesJqueryObject('#sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).removeClass('sesbasic_tab_selected');
					document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).removeClass('active');
				}
			}
		if(valueTab){
				if(valueTab.search('playlist') != -1)
					sesJqueryObject('.sesbasic_view_type').hide();
				else
					sesJqueryObject('.sesbasic_view_type').show();
				document.getElementById("tabbed-widget_<?php echo $randonNumber; ?>").innerHTML = "<div class='clear sesbasic_loading_container'></div>";
			if($("view_more_<?php echo $randonNumber; ?>"))
				document.getElementById("view_more_<?php echo $randonNumber; ?>").style.display = 'none';
			if($('ses_pagging'))
				document.getElementById("ses_pagging").style.display = 'none';
			 if (typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestTab_<?php echo $randonNumber; ?>.cancel();
 			 }
			  if (typeof(requestViewMore_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestViewMore_<?php echo $randonNumber; ?>.cancel();
 			 }
			 defaultOpenTab = valueTab;
			 console.log('safd');
			 requestTab_<?php echo $randonNumber; ?> = new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl+"widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/"+valueTab,
				'data': {
					format: 'html',
					page:  1,    
					params :<?php echo json_encode($this->params); ?>, 
					is_ajax : 1,
					identity : '<?php echo $randonNumber; ?>',
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = '';
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
					pinboardLayout_<?php echo $randonNumber ?>();
					if($('ses_pagging'))
						document.getElementById("ses_pagging").style.display = 'block';
					if(!sesJqueryObject('#tabbed-widget_<?php echo $randonNumber; ?> li').length)
						sesJqueryObject('.sesbasic_view_type_options_<?php echo $randonNumber; ?>').hide();
					else
						sesJqueryObject('.sesbasic_view_type_options_<?php echo $randonNumber; ?>').show();
				}
    	});
		requestTab_<?php echo $randonNumber; ?>.send();
    return false;			
		}
	}
<?php } ?>
var requestViewMore_<?php echo $randonNumber; ?>;
var params<?php echo $randonNumber; ?> = <?php echo json_encode($this->params); ?>;
var identity<?php echo $randonNumber; ?>  = '<?php echo $randonNumber; ?>';
 var page<?php echo $randonNumber; ?> = '<?php echo $this->page + 1; ?>';
 var searchParams<?php echo $randonNumber; ?> ;
	<?php if($this->loadOptionData != 'pagging'){ ?>
   viewMoreHide_<?php echo $randonNumber; ?>();	
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    requestViewMore_<?php echo $randonNumber; ?> = new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: page<?php echo $randonNumber; ?>,    
				params :	params<?php echo $randonNumber; ?>, 
				is_ajax : 1,
				searchParams:searchParams<?php echo $randonNumber; ?> ,
				identity : identity<?php echo $randonNumber; ?>,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if($('loading_images_browse_<?php echo $randonNumber; ?>'))
					sesJqueryObject('#loading_images_browse_<?php echo $randonNumber; ?>').remove();
				if($('loadingimgsesvideo-wrapper'))
						sesJqueryObject('#loadingimgsesvideo-wrapper').hide();
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				pinboardLayout_<?php echo $randonNumber ?>();
				
      }
    });
		requestViewMore_<?php echo $randonNumber; ?>.send();
    return false;
  }
	<?php }else{ ?>
		function paggingNumber<?php echo $randonNumber; ?>(pageNum){
			 sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','block');
			 var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
				requestViewMore_<?php echo $randonNumber; ?> = (new Request.HTML({
					method: 'post',
					'url': en4.core.baseUrl + "widget/index/mod/sesblog/name/<?php echo $this->widgetName; ?>/openTab/" + openTab_<?php echo $randonNumber; ?>,
					'data': {
						format: 'html',
						page: pageNum,    
						params :params<?php echo $randonNumber; ?> , 
						is_ajax : 1,
						searchParams:searchParams<?php echo $randonNumber; ?>  ,
						identity : identity<?php echo $randonNumber; ?>,
						type:'<?php echo $this->view_type; ?>'
					},
					onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					if($('loading_images_browse_<?php echo $randonNumber; ?>'))
					sesJqueryObject('#loading_images_browse_<?php echo $randonNumber; ?>').remove();
					if($('loadingimgsesvideo-wrapper'))
						sesJqueryObject('#loadingimgsesvideo-wrapper').hide();
						sesJqueryObject('.sesbasic_loading_cont_overlay').css('display','none');
						document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
						pinboardLayout_<?php echo $randonNumber ?>();
					}
				}));
				requestViewMore_<?php echo $randonNumber; ?>.send();
				return false;
		}
	<?php } ?>
</script>
<?php } ?>
<?php if(!$this->is_ajax){ ?>
<script type="application/javascript">
var tabId_<?php echo $this->identity; ?> = <?php echo $this->identity; ?>;
window.addEvent('domready', function() {
	tabContainerHrefSesbasic(tabId_<?php echo $this->identity; ?>);	
});
sesJqueryObject(document).on('click',function(){
	sesJqueryObject('.sesvideo_list_option_toggle').removeClass('open');
});
</script>
<?php } ?>
