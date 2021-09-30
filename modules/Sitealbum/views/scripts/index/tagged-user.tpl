<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tagged-user.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php  $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1); ?>
<div class="seaocore_members_popup seaocore_members_popup_notbs">
	<div class="top">
		<div class="heading"><?php echo $this->translate('Members')?></div>
  </div>
	<div class="seaocore_members_popup_content">
		<?php foreach( $this->insideAlbum as $value ):
    	$item=Engine_Api::_()->getItem('user', $value->user_id); ?>
	    <div class="item_member_list">
		    <div class="item_member_thumb">
		    	<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'),array('target'=>'blank')) ?>
		    </div>
		    <div class="item_member_option">
       		<?php if( $this->viewer->getIdentity() ): ?>
	        <?php  if(!$item->membership()->isMember($this->viewer, null) ): ?>
	         <?php echo $this->userFriendship($item); ?>
	        <?php endif; ?>
	          <?php if(Engine_Api::_()->sitealbum()->canSendUserMessage($item)):?>
	             <?php echo $this->htmlLink(array('route' =>'messages_general', 'action'=>'compose','to'=>$item->getIdentity()), $this->translate('Send Message'), array(
	            'class' => 'buttonlink' ,
	            'style' => "background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);",
	            'onclick'=>'javascript:parent.window.location.href=this.href; parent.Smoothbox.close(); return false;',
	          )) ?>
	          <?php endif;   ?>
		      <?php endif; ?>
      	</div>
		    <div class="item_member_details">
		    	<div class="item_member_name">
		    		<?php echo $this->htmlLink($item->getHref(), $item->getTitle(),array('target'=>'blank'));?>
		    	</div>
		    </div>
	    </div>
	  <?php endforeach;?>
	</div>
</div>
<div class="seaocore_members_popup_bottom">
	<button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
</div>
