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
<?php if($this->paginator->getTotalItemCount() > 0) :?>
  <?php if ($this->can_create): ?>
		<div class="profile-content-top-button" data-role="controlgroup" data-type="horizontal">
			<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'tab' => $this->identity), 'sitepagevideo_create', true) ?>' class='buttonlink icon_type_sitepagevideo_new'><?php echo $this->translate('Add a Video'); ?>   </a>
		</div>
	<?php endif; ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
    <div class="videos-listing"  id="profile_sitepagevideos">
      <ul data-role="none">
        <?php foreach( $this->paginator as $item ): ?>
          <li>  
            <div class="videos-listing-top">
              <a href="<?php echo $item->getHref(); ?>">
              <?php
                if( $item->photo_id ) {
                  echo $this->itemPhoto($item, 'thumb.profile');
                } else {
                  echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Sitepagevideo/externals/images/video.png">';
                }
              ?>
                 <i class="ui-icon ui-icon-play"></i>
              </a>
              <?php if( $item->duration ): ?>
               <span class="video-duration">
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
            </div>
            <div class="videos-listing-bottom">
              <div class="videos-listing-left">
                <p class="video-title"><?php echo $item->getTitle() ?></p>
                <p class="video-stats f_small t_light">
                  <?php echo $this->translate('By'); ?>
                  <?php echo $item->getOwner()->getTitle(); ?>
                </p>
              </div>
              <div class="videos-listing-right">
                <p> 
                  <?php if( $item->rating > 0 ): ?>
                  <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                    <span class="rating_star_generic rating_star"></span>
                  <?php endfor; ?>
                  <?php if( (round($item->rating) - $item->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half"></span>
                  <?php endif; ?>
                  <?php endif; ?>
                </p>
                <p class="listing-counts">
                  <span class="f_small"><?php echo $item->likes()->getLikeCount(); ?></span>
                  <i class="ui-icon-thumbs-up-alt"></i>
                  <span class="f_small"><?php echo $this->locale()->toNumber($item->comment_count) ?></span>
                  <i class="ui-icon-comment"></i>
                  <span class="f_small"><?php echo $this->locale()->toNumber($item->view_count) ?></span>
                  <i class="ui-icon-eye-open"></i>
                </p>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php else :?>
 	<div class="sm-content-list ui-listgrid-view"  id="profile_sitepagevideos">
		<ul data-role="listview" data-inset="false" data-icon="arrow-r">
		  <?php foreach( $this->paginator as $item ): ?>
				<li>  
					<a href="<?php echo $item->getHref(); ?>">
					<?php
						if( $item->photo_id ) {
							echo $this->itemPhoto($item, 'thumb.profile');
						} else {
							echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Sitepagevideo/externals/images/video.png">';
						}
					?>
					<div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play"></i></div>
					<h3><?php echo $item->getTitle() ?></h3>
					<?php if( $item->duration ): ?>
						<p class="ui-li-aside">
							<?php
								if( $item->duration >= 3600 ) {
									$duration = gmdate("H:i:s", $item->duration);
								} else {
									$duration = gmdate("i:s", $item->duration);
								}
								echo $duration;
							?>
						</p>
					<?php endif ?>
					<p><?php echo $this->translate('By'); ?>
						<strong><?php echo $item->getOwner()->getTitle(); ?></strong>
					</p>
					<p class="ui-li-aside-rating"> 
						<?php if( $item->rating > 0 ): ?>
							<?php for( $x=1; $x<=$item->rating; $x++ ): ?>
								<span class="rating_star_generic rating_star"></span>
							<?php endfor; ?>
							<?php if( (round($item->rating) - $item->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half"></span>
							<?php endif; ?>
						<?php endif; ?>
					</p>
					</a> 
				</li>
		  <?php endforeach; ?>
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitepagevideos');
			?>
		<?php endif; ?>

	</div>
	<?php endif; ?>
<?php else :?>
	<div class="tip" id='sitepagevideo_search'>
		<span>
			<?php echo $this->translate('No videos have been added in this Page yet.'); ?>
			<?php if ($this->can_create): ?>
				<?php echo $this->translate('Be the first to %1$sadd%2$s one!', '<a href="' . $this->url(array('page_id' => $this->sitepage->page_id, 'tab' => $this->identity), 'sitepagevideo_create') . '">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>