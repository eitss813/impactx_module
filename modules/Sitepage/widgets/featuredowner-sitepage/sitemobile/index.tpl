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

<div class="sm-content-list" id="profile_featuredowners">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->featuredowners as $item): ?>
			<li>
				<a href="<?php echo $item->getOwner()->getHref(); ?>">
					<?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon'); ?>
					<h3><?php echo $item->getOwner()->getTitle() ?></h3>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->featuredowners->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->featuredowners, $this->identity, "profile_featuredowners");
		?>
	<?php endif; ?>
</div>