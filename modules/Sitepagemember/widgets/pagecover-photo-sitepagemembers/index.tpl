<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	seaocore_content_type = 'sitepage_page';
</script>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>   
<?php $minHeight=120; ?>
<div class="sitepage_cover_information_wrapper">
  <div class='sitepage_cover_wrapper' id="sitepage_cover_photo" style='min-height:<?php echo $minHeight;?>px; height:<?php echo (!empty($this->sitepage->page_cover)) ? $this->columnHeight:$minHeight; ?>px;'  >
  </div>
  <?php if (!empty($this->showContent) || !empty($this->statistics)) : ?>
  <div class="sitepage_cover_information b_medium">
    <?php if($this->showContent):?>
			<div class="sp_coverinfo_buttons">
				<?php if (in_array('likeButton', $this->showContent)): ?>
					<div>
						<?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
					</div>	
				<?php endif; ?>
				<?php if (in_array('followButton', $this->showContent)): ?>
					<div>
						<?php echo $this->content()->renderWidget("seaocore.seaocore-follow"); ?>
					</div>	
				<?php endif; ?>
				<?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id);
				if (empty($joinMembers) && in_array('joinButton', $this->showContent) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
				<div>
					<?php if (!empty($this->viewer_id)) : ?>
						<?php if (!empty($this->sitepage->member_approval)): ?>
							<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'join', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><span><?php echo $this->translate("Join Page"); ?></span></a>
						<?php else: ?>
							<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'request', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><span><?php echo $this->translate("Join Page"); ?></span></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				
				<?php if (in_array('addButton', $this->showContent)): ?>
					<?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = 'Invite'); ?>
					<?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
					<div>
						<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>	
					</div>
					<?php elseif (!empty($hasMembers) && empty($this->sitepage->member_invite)): ?>
					<div>
						<a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
    <?php endif; ?>
    <?php if($this->statistics):?>
			<div class="sp_coverinfo_status">
				<?php if (in_array('title', $this->showContent)): ?>
				<h2><?php echo $this->sitepage->getTitle() ?></h2>
				<?php endif; ?>
				<div class="sp_coverinfo_stats seaocore_txt_light" >
				
					<?php if (in_array('likeCount', $this->statistics) && isset($this->sitepage->like_count)): ?>
						<a  id= "sitepage_page_num_of_like_<?php echo $this->sitepage->page_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'sitepage_page', 'resource_id' => $this->sitepage->page_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->sitepage->like_count),$this->locale()->toNumber($this->sitepage->like_count)); ?></a>
					<?php endif; ?>
					
					<?php if (in_array('followCount', $this->statistics) && isset($this->sitepage->follow_count)): ?>
						<?php if (in_array('likeCount', $this->statistics) && isset($this->sitepage->like_count)): ?>
							&middot; 
						<?php endif; ?>
							<a id= "sitepage_page_num_of_follows_<?php echo $this->sitepage->page_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'sitepage_page', 'resource_id' => $this->sitepage->page_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->sitepage->follow_count),$this->locale()->toNumber($this->sitepage->follow_count)); ?></a>
					<?php endif; ?>
					
					<?php if (in_array('memberCount', $this->statistics) && isset($this->sitepage->member_count)): ?>
						<?php //if (in_array('likeCount', $this->statistics) && isset($this->sitepage->like_count)): ?>
							&middot; 
						<?php //endif; ?>
						<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
						if ($this->sitepage->member_title && $memberTitle) {
							if ($this->sitepage->member_count == 1) : ?>
							<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitepage->member_count . ' member'; ?></a>
						<?php	else: ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitepage->member_count . ' ' .  $this->sitepage->member_title;?></a>
						<?php 	endif; ?>
						<?php } else { ?>
						<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s member', '%s members', $this->sitepage->member_count),$this->locale()->toNumber($this->sitepage->member_count)); ?></a>
					  <?php 	} ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
  </div>
  <?php endif; ?>
<div class="clr"></div>
</div>
<script type="text/javascript">
    document.seaoCoverPhoto= new SitepageCoverPhoto({
      block :$('sitepage_cover_photo'),
      photoUrl:'<?php echo $this->url(array('action' => 'get-cover-photo', 'page_id' => $this->sitepage->page_id,'show_member'=>1, 'memberCount'=>$this->memberCount, 'onlyMemberWithPhoto' => $this->onlyMemberWithPhoto), 'sitepage_profilepage', true); ?>',
      buttons:'seao_cover_options',
      columnHeight:<?php echo $this->columnHeight ?>,
      positionUrl :'<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true); ?>',
      position :<?php  echo $this->cover_params ? json_encode($this->cover_params): json_encode(array('top' => 0, 'left' => 0)); ?>
    });
</script>
<script type="text/javascript">
	function showSmoothBox(url) {
		Smoothbox.open(url);
		parent.Smoothbox.close;
	}
</script>