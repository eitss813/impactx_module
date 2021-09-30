<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>
<?php
if($this->showLightBox):
include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>

<h3><?php echo $this->translate("You and ") .$this->owner->getTitle() ?></h3>
<ul class="seaocore_sidebar_list sitealbum_sidebar">
	<li>
	  <?php
			foreach( $this->youAndOwner as $value ):
	    $item=Engine_Api::_()->getItem('album_photo', $value->resource_id);
		?>
		<a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $item->getPhotoUrl()?>","<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item); ?>");return false;' <?php endif; ?> title="<?php echo $item->getTitle(); ?>" >
		<?php 
      echo $this->itemBackgroundPhoto($item, 'thumb.normal', $item->getTitle(), array('class' => ""));
    ?>
    </a> 
  	<?php endforeach; ?>
		<div class="sitealbum_sidebar_des">
			<a href="<?php echo $this->url(array('action' => 'you-and-owner-photos',  'owner_id' => $this->owner->getIdentity()), 'sitealbum_general' , true); ?>">  <?php echo $this->translate(array('%1$s photo of you and %2$s', '%1$s photos of you and %2$s', $this->count), $this->locale()->toNumber($this->count),$this->owner->getTitle()) ?> </a>
		</div>
	</li>
</ul>