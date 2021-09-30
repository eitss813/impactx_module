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
<ul class="sitepage_sidebar_list">
	<?php  foreach ($this->userListings as $sitepage): ?>
		<li>
			<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id,$sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon'),array('title' => $sitepage->getTitle())) ?>
			<div class='sitepage_sidebar_list_info'>
				<div class='sitepage_sidebar_list_title'>
					<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id,$sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle()), array('title' => $sitepage->getTitle())) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>