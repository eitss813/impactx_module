<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-all-photos.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<ul class="sea_val_photos_thumbs">	  
  <?php foreach ($this->paginator as $photo): ?>
  	<li id="lb-all-thumbs-photo-<?php echo $photo->photo_id ?>" onclick="onclickPhotoThumb($(this)); getSitealbumPhoto('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo,  array())) ?>',1,'<?php echo $photo->getPhotoUrl(); ?>');closeAllPhotoContener();" >     
      <a class="thumbs_photo"  onclick="onclickPhotoThumb($(this)); getSitealbumPhoto('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo,  array())) ?>',1,'<?php echo $photo->getPhotoUrl(); ?>');closeAllPhotoContener();" >
      	<span class="lightbox_thumb" style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);">
        <?php 
          echo $this->itemBackgroundPhoto($photo, 'thumb.normal', $photo->getTitle(), array());   ?>
        	<?php if(!empty($photo->comment_count)): ?>
		        <span class="sea_plcmc">
		        	<span class="sea_plcmc_b">
		        		<span class="sea_plcmc_bb"><?php echo $photo->comment_count; ?></span>
		        		<span class="sea_plcmc_bc"></span>
		        	</span>
		        	<span class="sea_plcmc_f">
		        		<span class="sea_plcmc_fb"><?php echo $photo->comment_count; ?></span>
		        		<span class="sea_plcmc_fc"></span>
		        	</span>
	        	</span>
	      	<?php endif ?>
	      	<?php if(!empty($photo->like_count)): ?>
        		<span class="sea_plcml">
		        	<span class="sea_plcml_f">
		        		<span class="sea_plcmc_fb"><?php echo $photo->like_count; ?></span>
		        	</span>
	        	</span>
	        <?php endif ?>
        </span>
      </a>       
    </li>     
  <?php endforeach; ?>	 
</ul>