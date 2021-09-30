<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9785 2012-09-25 08:34:18Z pamela $
 * @author     John Boehr <john@socialengine.com>
 */
?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
<?php if(!$this->autoContentLoad) : ?>
    <div class="videos-listing">
      <ul data-role="none" id="browsepagevideos_ul">
<?php endif;?>
        <?php foreach( $this->paginator as $item ): ?>
          <li>  
            <div class="videos-listing-top">
              <a href="<?php echo $item->getHref(); ?>">
            <?php
              if( $item->photo_id ) {
                echo $this->itemPhoto($item, 'thumb.normal');
              } else {
                echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
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
<?php if(!$this->autoContentLoad) : ?>
        </ul>
      </div>
<?php endif;?>
  <?php else :?>
	<div class="sm-content-list ui-listgrid-view">
		<ul data-role="listview" data-inset="false" data-icon="arrow-r">
		  <?php foreach( $this->paginator as $item ): ?>
				<li>  
					<a href="<?php echo $item->getHref(); ?>">
					<?php
						if( $item->photo_id ) {
							echo $this->itemPhoto($item, 'thumb.profile');
						} else {
							echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
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
          <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $item->page_id); ?>
					<p><?php echo $this->translate('in'); ?>
						<strong><?php echo $sitepage_object->title;  ?></strong>
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
	</div>
 <?php endif; ?>
 <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
	<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
			'pageAsQuery' => true,
		)); ?>
 <?php endif; ?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.');?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
   var browsePageWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitepagevideo/name/sitepage-video';  
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : browsePageWidgetUrl, 'activeRequest' : false, 'container' : 'browsepagevideos_ul' };  
          });
         
   <?php endif; ?>    
</script>