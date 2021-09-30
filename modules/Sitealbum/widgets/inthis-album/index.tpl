<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<h3><?php echo $this->translate("Members tagged in this Album: ") . $this->count ?></h3>
<ul class="listtagged_users_block seaocore_sidebar_list">
	<li class="seaocore_member_tooltip_wrapper">		  
		<?php
			$container = 1;
			foreach( $this->insideAlbum as $value ):   
	    $item=Engine_Api::_()->getItem('user', $value->user_id);
		?>		
			<div class="tagged_member_thumb">
				<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', '', array('align'=>'left')), array('class' => 'item_photo','title' => $item->getTitle(), 'target' => '_parent')); ?>
				<!--  Start Tool Tip Work  -->
				<div class="seaocore_member_tooltip_outer" style="display:none;">
					<div class="seaocore_member_tooltip">
						<div class="seaocore_member_tooltip_content">
							<div class="seaocore_member_tooltip_arrow">
								<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tooltip_arrow_top.png' alt="" />
							</div>
							<div class='item_member'>
								<div class='item_member_thumb'>
								<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', '', array('align'=>'left')), array('class' => 'item_photo','title' => $item->getTitle(), 'target' => '_parent')); ?>
								</div>
								<div class='item_member_detail'>
									<div class='item_member_title'>
										<?php echo $item->__toString()?>
									</div>
									<div class='item_member_stat'>
										<?php if( $this->viewer->getIdentity() ): ?>
										<?php  if(!$item->membership()->isMember($this->viewer, null) ): ?>
										<?php echo $this->userFriendship($item); ?>
										<?php endif; ?>
										<?php if(Engine_Api::_()->sitealbum()->canSendUserMessage($item)):?>
										<?php echo $this->htmlLink(array('route' =>'messages_general', 'action'=>'compose','to'=>$item->getIdentity()), $this->translate('Send Message'), array(
										'class' => 'buttonlink' ,
										'style' => "background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);"      
										)) ?>
										<?php endif; ?>
									<?php endif; ?>
									</div>
								</div>
							</div>		
						</div>
					</div>
				</div>
				<!-- End Tool Tip Work-->
			</div>
     <?php  if($this->limit == $container): ?>
     <?php break; ?>
     <?php endif;?>
 		<?php $container++ ; ?>
  	<?php endforeach; ?>
	</li>
	<li>
		<a class="smoothbox seaocore_sidebar_more_link" href="<?php echo $this->url(array('action' => 'tagged-user',  'album_id' => $this->subject()->getIdentity()), 'sitealbum_general' , true); ?>"><?php echo $this->translate("See All") ?></a> 
	</li>	 
</ul>

<script type="text/javascript">
/* moo style */
window.addEvent('domready',function() {
       //opacity / display fix
	$$('.seaocore_member_tooltip_outer').setStyles({
		opacity: 0,
		display: 'none'
	});
	//put the effect in place
	$$('.seaocore_member_tooltip_wrapper > div').each(function(el,i) {
		el.addEvents({
			'mouseenter': function() {
				el.getElement('div').style.display = 'block';
				el.getElement('div').fade('in');
			},
			'mouseleave': function() {
				el.getElement('div').style.display = 'none'; 
				el.getElement('div').fade('out');
			}
		});
	});
});
</script>