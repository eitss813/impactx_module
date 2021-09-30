<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
  <?php foreach ($this->paginator as $video):?>
        <li>
					<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $video['page_id']);?>
					<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
					$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $video['page_id'], $layout);?>
					<a href="<?php echo Engine_Api::_()->sitepage()->getHref($sitepage_object->page_id, $sitepage_object->owner_id, $sitepage_object->getSlug()); ?>">
						<?php echo $this->itemPhoto($sitepage_object, 'thumb.icon', $sitepage_object->getTitle()) ?>
					</a>
					<div class="sitepage_sidebar_list_info">
						<div class="sitepage_sidebar_list_title">
            <?php echo $this->htmlLink($sitepage_object->getHref(), Engine_Api::_()->sitepage()->truncation($sitepage_object->getTitle()), array('title' => $sitepage_object->getTitle())); ?> 

						</div>
						<?php $category = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage_object->category_id);?>
						<?php $subCategory = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage_object->subcategory_id);?>
						<?php if ($category): ?>
							<div class="sitepage_sidebar_list_details">
								<?php echo $category->category_name;?>
								<?php if ($subCategory): ?> &raquo;
								<?php echo $subCategory->category_name; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
            </div>
						<div class="sitepage_sidebar_list_details">
						 <?php echo $this->htmlLink($sitepage_object->getHref(array('tab'=> $tab_id)),$video['item_count'].' videos'); ?>
				    </div>	
      </li>
	<?php endforeach; ?>
</ul>