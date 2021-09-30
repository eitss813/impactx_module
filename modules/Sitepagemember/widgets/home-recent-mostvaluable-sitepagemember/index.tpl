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
<ul class="sitepage_sidebar_list">
  <?php foreach ($this->paginator as $sitepagemember): ?>
    <li>
			<?php echo $this->htmlLink($sitepagemember->getHref(), $this->itemPhoto($sitepagemember->getOwner(), 'thumb.icon')); ?>
			<div class='sitepage_sidebar_list_info'>
				<div class='sitepage_sidebar_list_title'>
					<?php echo $this->htmlLink($this->item('user', $sitepagemember->user_id)->getHref(), $this->user($sitepagemember->user_id)->displayname, array('title' => $sitepagemember->displayname, 'target' => '_parent')); ?> 	
				</div>
			  <div class='sitepage_sidebar_list_details'>
          <?php echo $this->htmlLink(array('route' => 'sitepagemember_approve', 'action' => 'page-join', 'user_id' => $sitepagemember->user_id), $this->translate(array('%s Page Joined', '%s Pages Joined', $sitepagemember->JOINP_COUNT), $this->locale()->toNumber($sitepagemember->JOINP_COUNT)), array('class' => 'smoothbox')); ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>