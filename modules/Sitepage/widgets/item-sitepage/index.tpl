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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_browse_sitepage_day">
	<li>
		<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->dayitem->page_id, $this->dayitem->owner_id, $this->dayitem->getSlug()), $this->itemPhoto($this->dayitem, 'thumb.profile')) ?>
		<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->dayitem->page_id, $this->dayitem->owner_id, $this->dayitem->getSlug()), $this->dayitem->getTitle(), array('title' => $this->dayitem->getTitle())) ?>
	</li>
	<?php if($this->show_likes) :?>
		<li>
			<?php echo $this->translate(array('%s like', '%s likes', $this->sitepage->like_count), $this->locale()->toNumber($this->sitepage->like_count)) ?>
		</li>
	<?php endif?>
	<?php if($this->show_comments) :?>
		<li>
			<?php echo $this->translate(array('%s comment', '%s comments', $this->sitepage->comment_count), $this->locale()->toNumber($this->sitepage->comment_count)) ?>
		</li>
	<?php endif?>
	<?php if($this->show_views) :?>
		<li>
			<?php echo $this->translate(array('%s view', '%s views', $this->sitepage->view_count), $this->locale()->toNumber($this->sitepage->view_count)) ?>
		</li>
	<?php endif?>
	<?php if($this->show_followers) :?>
		<li>
			<?php echo $this->translate(array('%s follower', '%s followers', $this->sitepage->follow_count), $this->locale()->toNumber($this->sitepage->follow_count)) ?>
		</li>
	<?php endif?>
</ul>