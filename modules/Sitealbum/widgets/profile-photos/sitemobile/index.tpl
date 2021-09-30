<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="album-listing" id='sitealbum_profile_photos'>
  <ul>
    <?php foreach ($this->paginator as $album): ?>
      <li class="sm-ui-browse-items">
        <a href="<?php echo $album->getHref(); ?>">
          <p class="ui-li-aside-show ui-li-aside" style="display:none;"><?php echo $this->locale()->toNumber($album->count())?></p>
          <?php //echo $this->itemPhoto($album, 'thumb.icon'); ?>
            <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png'; $temp_url=$album->getPhotoUrl('thumb.main'); if(!empty($temp_url)): $url=$album->getPhotoUrl('thumb.main'); endif;?>
            <span class="listing-thumb" style="background-image: url(<?php echo $url; ?>);"> </span> 
            <div class="ui-list-content">
            <h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
          </div>
          <p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count())?></p>
        </a>
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
      </li>
    <?php endforeach; ?>  
  </ul>
</div>
<?php if ($this->paginator->count() > 1): ?>
	<?php
		echo $this->paginationAjaxControl(
					$this->paginator, $this->identity, 'sitealbum_profile_photos');
	?>
<?php endif; ?>