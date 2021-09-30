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

<div class="sm-content-list" id="favourite_pages">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach( $this->userListings as $sitepage ): ?>
			<li>
				<a href="<?php echo Engine_Api::_()->sitepage()->getHref($sitepage->page_id_for, $sitepage->owner_id,$sitepage->getSlug()); ?>">
					<?php echo $this->itemPhoto($sitepage, 'thumb.icon'); ?>
					<h3><?php echo $sitepage->getTitle() ?></h3>
        </a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->userListings->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->userListings, $this->identity, "favourite_pages", array("category_id" => $this->category_id, "featured" => $this->featured, "sponsored" => $this->sponsored));
		?>
  <?php endif;?>
</div>