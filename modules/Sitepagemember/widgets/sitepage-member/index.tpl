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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>

<?php if (!empty($this->friend)) : ?>
<?php if($this->friendpaginator->getTotalItemCount()): ?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagemember_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
	
	<h3 class="sitepage_mypage_head sitepage_member_browse_head"><span class=""><?php echo $this->translate('Friends');?></span></h3>
	<ul class="seaocore_browse_list">
		<?php foreach ($this->friendpaginator as $sitepagemember): ?>
			<li id="sitepagemember-item-<?php echo $sitepagemember->member_id ?>">
				<div class="seaocore_browse_list_photo_small">
							<?php $user_object = Engine_Api::_()->getItem('user', $sitepagemember->user_id);
							echo $this->htmlLink($user_object->getHref(), $this->itemPhoto($user_object->getOwner(), 'thumb.icon'));  ?>
				</div>
				<div class="seaocore_browse_list_options">
						<?php //FOR MESSAGE LINK
						$item = Engine_Api::_()->getItem('user', $sitepagemember->user_id);
						if ((Engine_Api::_()->seaocore()->canSendUserMessage($item)) && (!empty($this->viewer_id))) : ?>
							<a href="<?php echo $this->base_url ?>/messages/compose/to/<?php echo $sitepagemember->user_id ?>" target="_parent" class="buttonlink" style=" background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);"><?php echo $this->translate('Message'); ?></a>
						<?php endif; ?>
						<?php //Add friend link.
						$uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitepagemember->user_id)); ?>
						<?php if (!empty($uaseFRIENFLINK)) : ?>
							<?php echo $uaseFRIENFLINK; ?>
						<?php endif; ?>
				</div>
				<div class='seaocore_browse_list_info'>
					<div class='seaocore_browse_list_info_title'>
						<h3><?php echo $this->htmlLink($this->item('user', $sitepagemember->user_id)->getHref(), $this->user($sitepagemember->user_id)->displayname, array('title' => $sitepagemember->displayname, 'target' => '_parent')); ?></h4>
					</div>
					<div class="seaocore_browse_list_info_date">
						<?php //$count = Engine_Api::_()->getDbtable('membership', 'sitepage')->countPages($sitepagemember->JOINP_COUNT);
						echo $this->htmlLink(array('route' => 'sitepagemember_approve', 'action' => 'page-join', 'user_id' => $sitepagemember->user_id), $this->translate(array('%s Page Joined', '%s Pages Joined', $sitepagemember->JOINP_COUNT), $this->locale()->toNumber($sitepagemember->JOINP_COUNT)), array('onclick' => 'owner(this);return false')); ?>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php //echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagemember"), array("orderby" => $this->orderby)); ?>
<?php endif;?>
<?php endif;?>

<?php if($this->paginator->getTotalItemCount()):?>
<br /> 
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagemember_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
  <?php if(!empty($this->friend)) : ?>
		<h3 class="sitepage_mypage_head sitepage_member_browse_head"><span><?php echo $this->translate('Other Members');?></span></h3>
	<?php endif; ?>
	<ul class="seaocore_browse_list">
		<?php foreach ($this->paginator as $sitepagemember): ?>
			<li id="sitepagemember-item-<?php echo $sitepagemember->member_id ?>">
				<div class="seaocore_browse_list_photo_small"> 
							<?php $user_object = Engine_Api::_()->getItem('user', $sitepagemember->user_id);
							echo $this->htmlLink($user_object->getHref(), $this->itemPhoto($user_object->getOwner(), 'thumb.icon'));  ?>
				</div>
				<div class="seaocore_browse_list_options seaocore_icon_done">
						<?php //FOR MESSAGE LINK
						$item = Engine_Api::_()->getItem('user', $sitepagemember->user_id);
						if ((Engine_Api::_()->seaocore()->canSendUserMessage($item)) && (!empty($this->viewer_id))) : ?>
							<a href="<?php echo $this->base_url ?>/messages/compose/to/<?php echo $sitepagemember->user_id ?>" target="_parent" class="buttonlink" style=" background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);"><?php echo $this->translate('Message'); ?></a>
						<?php endif; ?>
						<?php //Add friend link.
						$uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitepagemember->user_id)); ?>
						<?php if (!empty($uaseFRIENFLINK)) : ?>
							<?php echo $uaseFRIENFLINK; ?>
						<?php endif; ?>
				</div>
				<div class='seaocore_browse_list_info'>
					<div class='seaocore_browse_list_info_title'>
						<h3><?php 
						echo $this->htmlLink($this->item('user', $sitepagemember->user_id)->getHref(), $this->user($sitepagemember->user_id)->displayname, array('title' => $sitepagemember->displayname, 'target' => '_parent')); ?> </h3>
					</div>
					<div class="seaocore_browse_list_info_date">
						<?php //$count = Engine_Api::_()->getDbtable('membership', 'sitepage')->countPages($sitepagemember->user_id);
						echo $this->htmlLink(array('route' => 'sitepagemember_approve', 'action' => 'page-join', 'user_id' => $sitepagemember->user_id), $this->translate(array('%s Page Joined', '%s Pages Joined', $sitepagemember->JOINP_COUNT), $this->locale()->toNumber($sitepagemember->JOINP_COUNT)), array('onclick' => 'owner(this);return false')); ?>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagemember"), array("orderby" => $this->orderby)); ?>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>

<script type="text/javascript">
  var pageAction = function(page){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
			}
    form.elements['page'].value = page;
		form.submit();
  }
</script>