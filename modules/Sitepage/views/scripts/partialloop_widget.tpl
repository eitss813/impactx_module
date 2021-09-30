<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialloop_widget.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<li>
	<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()), $this->itemPhoto($this->sitepage, 'thumb.icon'), array('title' => $this->sitepage->getTitle())) ?>
	<div class='sitepage_sidebar_list_info'>
		<div class='sitepage_sidebar_list_title'>
			<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($this->sitepage->getTitle()), array('title' => $this->sitepage->getTitle())) ?>
		</div>
		<div class='sitepage_sidebar_list_details'>
			<?php echo $this->translate(array('%s view', '%s views', $this->sitepage->view_count), $this->locale()->toNumber($this->sitepage->view_count)) ?>,
			<?php echo $this->translate(array('%s like', '%s likes', $this->sitepage->like_count), $this->locale()->toNumber($this->sitepage->like_count)) ?>
		</div>
	</div>
</li>