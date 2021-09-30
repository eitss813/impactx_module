<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 

?>

<?php if($this->paginator->getTotalItemCount() > 0) :?>
  <?php if ($this->can_edit && !empty($this->allowed_upload_photo)): ?>
    <div class="profile-content-top-button" data-role="controlgroup" data-type="horizontal">
      <a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => 0, 'tab' => $this->identity), 'sitepage_photoalbumupload', true) ?>' ><?php echo $this->translate('Create an Album'); ?></a>
    </div>
  <?php elseif (!empty($this->allowed_upload_photo) && ($this->sitepage->owner_id != $this->viewer_id)): ?>
    <div class="profile-content-top-button" data-role="controlgroup" data-type="horizontal">
      <a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => $this->default_album_id, 'tab' => $this->identity), 'sitepage_photoalbumupload', true) ?>'  class='buttonlink icon_sitepage_photo_new '><?php echo $this->translate('Add Photos'); ?></a>
    </div>
  <?php endif; ?>
  <div class="album-listing" id='profile_sitepagealbums'>
    <ul>
      <?php foreach ($this->paginator as $album): ?>
        <li>
          <a href="<?php echo $album->getHref(); ?>" class="listing-btn">
            <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png'; $temp_url=$album->getPhotoUrl('thumb.main'); if(!empty($temp_url)): $url=$album->getPhotoUrl('thumb.main'); endif;?>
            <span class="listing-thumb" style="background-image: url(<?php echo $url; ?>);"> </span>
            <h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
            <p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count());?></p>
          </a> 
          <p class="list-owner">
            <?php echo $this->translate('By'); ?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle()) ?>
          </p>
          <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
            <?php if($album->likes()->getLikeCount() > 0 || $album->comment_count > 0) : ?>
              <a class="listing-stats ui-link-inherit" onclick='sm4.core.photocomments.album_comments_likes(<?php echo $album->getIdentity();?>, "<?php echo $album->getType();?>")'>
              <?php if($album->likes()->getLikeCount() > 0) : ?> 
                  <span class="f_small"><?php echo $this->locale()->toNumber($album->likes()->getLikeCount()); ?></span>
                <i class="ui-icon-thumbs-up-alt"></i>
              <?php endif;?>
              <?php if($album->comment_count > 0) : ?>
                  <span class="f_small"><?php echo $this->locale()->toNumber($album->comment_count) ?></span>
                  <i class="ui-icon-comment"></i>
              <?php endif;?>
              </a>
            <?php endif;?>
          <?php endif;?>
        </li>
      <?php endforeach; ?>   
    </ul>
  </div>
  <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationAjaxControl(
            $this->paginator, $this->identity, 'profile_sitepagealbums');
    ?>
  <?php endif; ?>
<?php endif;?>

<?php if($this->paginators->getTotalItemCount() > 0):?>
	<div class="ui-page-content" id="profile_sitepagephotos">
		<br /><b><?php echo $this->translate('Photos by Others'); ?></b> &#8226;
    <?php echo $this->translate(array('%s photo', '%s photos', $this->paginators->getTotalItemCount()), $this->locale()->toNumber($this->paginators->getTotalItemCount())) ?><br /><br />
		<ul class="thumbs thumbs_nocaptions">
			<?php foreach ($this->paginators as $photo): ?>
				<li>
					<a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
						<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginators->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginators, $this->identity, 'profile_sitepagephotos');
			?>
		<?php endif; ?>

	</div>
<?php endif; ?>