<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagevideo_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
    <input type="hidden" id="itemCount" name="itemCount"  value="<?php echo $this->itemCount;?>"/>
  </form>
		
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitepage): ?>
				<li id="sitepagevideo-item-<?php echo $sitepage->video_id ?>">
				<div class="seaocore_browse_list_photo"> 
				<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $sitepage->page_id);?>
				<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
								$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $sitepage->page_id, $layout);?>
					<a href="<?php echo $this->url(array('user_id' => $sitepage->owner_id, 'video_id' =>  $sitepage->video_id,'tab' => $tab_id,'slug' => $sitepage->getSlug()),'sitepagevideo_view', true)?>">
		
					<div class="sitepagevideo_thumb_wrapper">
						<?php if ($sitepage->duration): ?>
							<span class="sitepagevideo_length">
								<?php
									if ($sitepage->duration > 360)
										$duration = gmdate("H:i:s", $sitepage->duration); else
										$duration = gmdate("i:s", $sitepage->duration);
									if ($duration[0] == '0')
										$duration = substr($duration, 1); echo $duration;
								?>
							</span>
						<?php endif; ?>
						<?php  if ($sitepage->photo_id): ?>
							<?php echo   $this->itemPhoto($sitepage, 'thumb.normal'); ?>
						<?php else: ?>
							<img src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagevideo/externals/images/video.png" class="thumb_normal item_photo_video  thumb_normal" />
						<?php endif;?>
					</div>
				</a>
						</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
             <span>
              <?php if (($sitepage->price>0)): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
						<?php endif; ?>
						<?php if ($sitepage->featured == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						<?php endif; ?>
              </span>
              
							<span class="list_rating_star">
								<span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star_half"></span>
								<?php if($sitepage->rating>0):?>
									<?php
									$currentRatingValue = $sitepage->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
									$finalRatingValue = (int)$currentRatingValue;
									}
									else {
									$finalRatingValue = (int)$currentRatingValue + .5;
									}
									?>
									<?php for($x=1; $x<=$sitepage->rating; $x++): ?>
										<span class="rating_star_generic rating_star" title= "<?php echo $finalRatingValue.$this->translate(' rating');?>" ></span>
									<?php endfor; ?>
									<?php if((round($sitepage->rating)-$sitepage->rating)>0):?>
										<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue ?> rating"></span>
									<?php endif; ?>
								<?php endif; ?>
							</span>
							<h3><?php echo $this->htmlLink($sitepage->getHref(), $sitepage->getTitle(), array('title' => $sitepage->getTitle())); ?> </h3>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()),  $sitepage->sitepage_title) ?>
						</div>
						<div class="seaocore_browse_list_info_date">
	            <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>,
							<?php echo $this->translate(array('%s comment', '%s comments', $sitepage->comments()->getCommentCount()),$this->locale()->toNumber($sitepage->comments()->getCommentCount())) ?>, <?php echo $this->translate(array('%s like', '%s likes', $sitepage->likes()->getLikeCount()),$this->locale()->toNumber($sitepage->likes()->getLikeCount())) ?>, <?php echo $this->translate(array('%s view', '%s views', $sitepage->view_count),$this->locale()->toNumber($sitepage->view_count)) ?>


						</div>	

						<div class='seaocore_browse_list_info_blurb'>
							<?php echo $this->viewMore($sitepage->description); ?><br />
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagevideo"), array("orderby" => $this->orderby,"itemCount" => $this->itemCount)); ?>


<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>


<script type="text/javascript">
  var pageAction = function(page){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
			}
    form.elements['page'].value = page;
    $('filter_form_page').elements['itemCount'].value = '<?php echo $this->itemCount;?>';
		form.submit();
  } 
</script>