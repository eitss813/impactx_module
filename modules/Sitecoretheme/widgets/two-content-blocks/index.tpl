<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div class="sitecoretheme_two-content-blocks_wrapper sitecoretheme_two-content-blocks_<?php echo $this->identity; ?>">

	<?php foreach ($this->data as $itemData): ?>
		<div class="_main _main_style_<?php echo $itemData['viewType'] ?> content-blocks_<?php echo $itemData['itemType'] ?> wow fadeIn animated">
			<ul class="">
				<?php foreach ($itemData['results'] as $row) : ?>
					<li>
						<div class="_item">
							<div class="_item_img" style="position: relative;">
								<?php echo $this->htmlLink($row->getHref(), $this->itemBackgroundPhoto($row, 'thumb.main'), array('class' => 'thumb')) ?>
								<i class="two_content_block_module_icon item_icon_<?php echo $itemData['itemType'] ?>"></i>
							</div>
							<div class="_item_info">
								<div class="_item_title">
									<?php echo $this->htmlLink($row->getHref(), $row->getTitle()) ?>
								</div>
									
									<?php if ($itemData['categoryTable'] && !empty($row->category_id)): ?>
										<div class="_item_category">
											<?php
											echo $this->partial('_contentCategory.tpl', 'sitecoretheme', array(
												'table' => $itemData['categoryTable'],
												'item' => $row
											));
											?>
										</div>
									<?php endif; ?>
								<div class="_item_date"><?php echo date("F j, Y", strtotime($row->creation_date)); ?></div>
								<div class="_item_body">
									<?php echo $row->getDescription(); ?>
								</div>
							</div>
						</div>
						<?php if (!empty($itemData['readMoreText'])): ?>
							<div class="_readmore">
								<?php echo $this->htmlLink($row->getHref(), $this->translate($itemData['readMoreText'])) ?>
							</div>
						<?php endif; ?>						
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
<script type="text/javascript">
  $$('.sitecoretheme_two-content-blocks_<?php echo $this->identity; ?>')
          .getParent('.layout_sitecoretheme_two_content_blocks')
          .addClass('layout_sitecoretheme_two_content_blocks_<?php echo $this->identity; ?>');
</script>
<style type="text/css">
<?php if ($this->backgroundImage): ?>	
		.layout_sitecoretheme_two_content_blocks_<?php echo $this->identity; ?> {
			background-image:url(<?php echo $this->layout()->staticBaseUrl . $this->backgroundImage ?>);
		}
<?php endif; ?>
<?php if ($this->headingColor): ?>	
		.layout_sitecoretheme_two_content_blocks_<?php echo $this->identity; ?> h3,
		.layout_sitecoretheme_two_content_blocks_<?php echo $this->identity; ?> ._header h3,
		.layout_sitecoretheme_two_content_blocks_<?php echo $this->identity; ?> ._header .widgets_title_description {
			color: <?php echo $this->headingColor; ?>
		}
<?php endif; ?>
<?php if ($this->backgroundOverlayColor): ?>	
		.layout_sitecoretheme_two_content_blocks_<?php echo $this->identity; ?>::before {
			background-color: <?php echo $this->backgroundOverlayColor ?>;
			opacity: <?php echo $this->backgroundOverlayOpacity / 100 ?>;
		}
<?php endif; ?>
</style>