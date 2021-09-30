<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagevideo/externals/styles/style_sitepagevideo.css');
?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div>
			<div class="photo">
					<?php echo $this->htmlLink($this->memberOfDay->getHref(), $this->itemPhoto($this->memberOfDay->getOwner(), 'thumb.profile')); ?>
			</div>
		  </a>
		</div>
		<div class="info clr">
			<div class="title">
        <?php echo $this->htmlLink($this->item('user', $this->memberOfDay->user_id)->getHref(), $this->user($this->memberOfDay->user_id)->displayname, array('title' => $this->memberOfDay->displayname, 'target' => '_parent')); ?>
			</div>
		</div>
		<div class="seaocore_browse_list_info_date">
			<?php echo $this->htmlLink(array('route' => 'sitepagemember_approve', 'action' => 'page-join', 'user_id' => $this->memberOfDay->user_id), $this->translate(array('%s Page Joined', '%s Pages Joined', count($this->result)), $this->locale()->toNumber(count($this->result))), array('class' => 'smoothbox')); ?>
		</div>
	</li>
</ul>

<script type="text/javascript">
function showSmoothBox(url) {
  Smoothbox.open(url);
}
</script>