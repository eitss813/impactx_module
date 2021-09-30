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

<?php if(!empty($this->friend) && $this->friendpaginator->getTotalItemCount()): ?> 
	<div class="sm-content-list" id="friend_members">
    <h3 class="t_l"><?php echo $this->translate('Friends');?></h3>
		<ul data-role="listview" data-inset="false" data-icon="arrow-r">
			<?php foreach ($this->friendpaginator as $sitepagemember): ?>
				<li data-icon="envelope" data-inset="true">

					<?php if ($this->SmUserFriendshipAjax($this->user($sitepagemember->user_id))): ?>
						<div class="ui-item-member-action">
							<?php echo $this->SmUserFriendshipAjax($this->user($sitepagemember->user_id)) ?>
						</div>
					<?php endif; ?>
					<a href="<?php echo $this->url(array( 'action' => 'page-join', 'user_id' => $sitepagemember->user_id), 'sitepagemember_approve', 'true'); ?>">	
						<?php echo $this->itemPhoto(Engine_Api::_()->getItem('user', $sitepagemember->user_id)->getOwner(), 'thumb.icon');?>   
						<h3><?php echo $this->user($sitepagemember->user_id)->displayname;?></h3>
            <p><?php echo $this->translate(array('%s Page Joined', '%s Pages Joined', $sitepagemember->JOINP_COUNT), $this->locale()->toNumber($sitepagemember->JOINP_COUNT));?></p>
					</a>
						<?php //FOR MESSAGE LINK
						$item = Engine_Api::_()->getItem('user', $sitepagemember->user_id);
						if ((Engine_Api::_()->seaocore()->canSendUserMessage($item)) && (!empty($this->viewer_id))) : ?>
              <a href="<?php echo $this->url(array('controller' => 'messages','action' => 'compose', 'to' => $sitepagemember->user_id), 'default', 'true'); ?>" class="buttonlink">
              </a>
						<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($this->friendpaginator->count() > 1): ?>
			<?php
				echo $this->paginationAjaxControl(
							$this->friendpaginator, $this->identity, 'friend_members');
			?>
		<?php endif; ?>
	</div><br />
<?php endif; ?>

<?php if($this->paginator->getTotalItemCount()):?>
	<div class="sm-content-list" id="other_members">
      <?php if(!empty($this->friend)) : ?>
      <h3 class="t_l"><?php echo $this->translate('Other Members');?></h3>
      <?php endif;?>
		<ul data-role="listview" data-inset="false" data-icon="arrow-r">
			<?php foreach ($this->paginator as $sitepagemember): ?>
				<li data-icon="false" class="sm-ui-browse-items">
					<?php //FOR MESSAGE LINK
					$item = Engine_Api::_()->getItem('user', $sitepagemember->user_id);
					if ((Engine_Api::_()->seaocore()->canSendUserMessage($item)) && (!empty($this->viewer_id))) : ?>
						<div class="ui-item-member-action">
							<a href="<?php echo $this->url(array('controller' => 'messages','action' => 'compose', 'to' => $sitepagemember->user_id), 'default', 'true'); ?>" class="buttonlink">
                <?php echo $this->translate('Message'); ?>
              </a>
						</div>
					<?php endif; ?>
					<?php if ($this->SmUserFriendshipAjax($this->user($sitepagemember->user_id))): ?>
						<div class="ui-item-member-action">
							<?php echo $this->SmUserFriendshipAjax($this->user($sitepagemember->user_id)) ?>
						</div>
					<?php endif; ?>
					<a href="<?php echo $this->url(array( 'action' => 'page-join', 'user_id' => $sitepagemember->user_id), 'sitepagemember_approve', 'true'); ?>">	
						<?php echo $this->itemPhoto(Engine_Api::_()->getItem('user', $sitepagemember->user_id)->getOwner(), 'thumb.icon');?>   
						<h3><?php echo $this->user($sitepagemember->user_id)->displayname;?></h3>
            <p><?php echo $this->translate(array('%s Page Joined', '%s Pages Joined', $sitepagemember->JOINP_COUNT), $this->locale()->toNumber($sitepagemember->JOINP_COUNT));?></p>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
				echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'other_members');
			?>
		<?php endif; ?>
	</div>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>
