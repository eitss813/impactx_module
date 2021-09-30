<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: page-join.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php //if(!empty ($this->showViewMore)): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLink();
    });
    
    function getNextPageViewMoreResults(){
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    
    function hideViewMoreLink(){
        if($('request_member_pops_view_more'))
            $('request_member_pops_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }

    function viewMoreTabMutualFriend()
    {
			var user_id = '<?php echo $this->user_id; ?>';
			document.getElementById('request_member_pops_view_more').style.display ='none';
			document.getElementById('request_member_pops_loding_image').style.display ='';
			en4.core.request.send(new Request.HTML({
				method : 'post',
				'url' : en4.core.baseUrl + 'sitepagemember/admin-manage/page-join/user_id/' + user_id,
				'data' : {
						format : 'html',
						showViewMore : 1,
						page: getNextPageViewMoreResults()
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('members_results_friend').innerHTML = document.getElementById('members_results_friend').innerHTML + responseHTML;
					document.getElementById('request_member_pops_view_more').destroy();
					document.getElementById('request_member_pops_loding_image').style.display ='none';
				}
			}));
			return false;
    }

    function leavePage (user_id, page_id) {
			var friendUrl = '<?php echo $this->url(array('module' => 'sitepagemember', 'controller' => 'admin-manage', 'action' => 'delete'), 'default', true) ?>';
			en4.core.request.send(new Request.HTML({
				url : friendUrl,
				data : {
					format: 'html',
					user_id: user_id,
					page_id: page_id,
				},
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
					document.getElementById('more_results_shows_'+page_id).innerHTML = "You have leave the page.";
					setTimeout("hideField(" + page_id + ")", 1000);
				}
			}));
		}
		
	  function hideField(page_id) {
			document.getElementById('more_results_shows_'+page_id).destroy();
			if (document.getElementById('members_results_friend').getChildren().length == '2') {
			   document.getElementById('members_results_friend').innerHTML = 
			   "<div class='tip' id=''><span><?php echo $this->translate('There are no more pages joined by this member.');?> </span></div>";
			}
		}
</script>

<?php //endif; ?>

<?php if (empty($this->showViewMore)): ?>
  <div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="top">
    <?php $user = Engine_Api::_()->user()->getUser($this->user_id); ?>
      <div class="heading"><?php echo $this->translate('Pages joined by ')?><?php echo $user->displayname ?></div>
    </div>
    <div class="seaocore_members_popup_content" id="members_results_friend">
<?php endif; ?>

<?php if (count($this->paginator) > 0) : ?>
	<?php foreach( $this->paginator as $value ): ?>
		<?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $value->page_id); ?>
		<div class="item_member_list" id="more_results_shows_<?php echo $sitepage->page_id; ?>">
			<div class="item_member_thumb">
				<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon'), array('title' => $sitepage->getTitle())); ?>
			</div>
			<div class="item_member_option">
			<?php if ($sitepage->owner_id != $this->user_id) : ?>
				<a href="javascript:void(0);" onclick="leavePage('<?php echo $this->user_id ?>', '<?php echo $sitepage->page_id ?>');" class="icon_sitepagemember_leave buttonlink"><?php echo $this->translate('Remove Member')?></a>
				<?php endif; ?>
			</div>
			<div class="item_member_details">
				<div class="item_member_name">
					<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $sitepage->getTitle(), array('title' => $sitepage->getTitle())) ?>
				</div>
				<div class="item_member_stat">
					<?php //echo $this->translate(array('%s Member', '%s Members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
				</div>
			</div>
		</div>
	<?php endforeach;?>
<?php else : ?>
<div class="tip" id='sitepagemember_search'>
		  <span>
			  <?php echo $this->translate('No members found.');?>
		  </span>
	</div>
<?php endif; ?>

<?php if (empty($this->showViewMore)):  ?>
	<div class="seaocore_item_list_popup_more" id="request_member_pops_view_more" onclick="viewMoreTabMutualFriend()" >
	<?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore' )); ?>
	</div>
	<div class="seaocore_item_list_popup_more" id="request_member_pops_loding_image" style="display: none;">
		<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
			<?php echo $this->translate("Loading ...") ?>
	</div>
<?php //if (empty($this->showViewMore)): ?>
    </div>
  </div>
  <div class="seaocore_members_popup_bottom">
      <button  onclick='smoothboxclose()' ><?php echo $this->translate('Close') ?></button>
  </div>
<?php endif; ?>

<script type="text/javascript">
 function smoothboxclose () {
  parent.Smoothbox.close () ;
 }
</script>