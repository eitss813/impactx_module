<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-member.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	function userWidgetRequestSend (member_id, page_id) 
	{
		var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'approve'), 'sitepagemember_approve', true) ?>';
		$.ajax({
			url : friendUrl,
			type: "POST",
			dataType: "html",
			data : {
				format: 'html',
				member_id: member_id,
				page_id: page_id,

			},
			'success' : function(responseTree, responseElements, responseHTML, responseJavaScript)
			{
        parent.window.location.reload();
				//document.getElementById('more_results_shows_'+member_id).innerHTML = "You have successfully approve this member.";
				//setTimeout("hideField(" + member_id + ")", 1000);
			}
		});
	}

	function rejectmemberRequestSend (member_id, page_id, user_id) {
		var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'sitepagemember_approve', true) ?>';
		$.ajax({
			url : friendUrl,
			type: "POST",
			dataType: "html",
			data : {
				format: 'html',
				member_id: member_id,
				page_id:page_id,
				user_id: user_id
			},
			'success' : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
				//document.getElementById('more_results_shows_'+member_id).innerHTML = "You have ignored the invite to the page.";
				//setTimeout("hideField(" + member_id + ")", 1000);
				//alert(document.getElementById('members_results_friend').getChildren().length);
        parent.window.location.reload();
			}
		});
	}
</script>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
	<div class="top">
		<div class="heading"><?php echo $this->translate('Requested Members')?></div>
	</div>
</div><br />

<?php if (count($this->paginator) > 0) : ?>
  <div class="sm-content-list">
   <ul data-role="listview" data-inset="false">
		<?php foreach( $this->paginator as $value ): ?>
			<li data-icon="cog" data-inset="true">
       <a>
        <?php echo $this->itemPhoto($value->getOwner(), 'thumb.icon');?>
        <h3><?php echo $value->getTitle();?></h3>
       </a>
       <a href="#sitepagemember_<?php echo $value->user_id;?>" data-rel="popup"></a>
				<div data-role="popup" id="sitepagemember_<?php echo $value->user_id ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
					<div data-inset="true" style="min-width:150px;" class="sm-options-popup">
						<a href="javascript:void(0);" onclick="userWidgetRequestSend('<?php echo $value->member_id ?>', '<?php echo $value->page_id ?>');" class="ui-btn-default"><?php echo $this->translate('Approve Member')?></a>
						<a href="javascript:void(0);" onclick="rejectmemberRequestSend('<?php echo $value->member_id ?>', '<?php echo $value->page_id ?>', '<?php echo $value->user_id ?>');" class="ui-btn-default"><?php echo $this->translate('Reject Request')?></a>
						<a href="#" data-rel="back" class="ui-btn-default"><?php echo $this->translate('Cancel'); ?></a>
          </div>
				</div>
      </li>
		<?php endforeach;?>
   </ul>
  </div>
<?php else:?>
  <div class="tip">
   <span>
     <?php echo $this->translate('No members request.');?>
   </span>
  </div>
<?php endif;?>