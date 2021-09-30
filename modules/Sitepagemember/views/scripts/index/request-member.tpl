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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>
<?php 
   $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
$settings = Engine_Api::_()->getApi('settings', 'core');
$advancedactivity_composer_type = $settings->getSetting('advancedactivity.composer.type', 0);
$infotooltip = $settings->getSetting('advancedactivity.info.tooltips', 1); ?>
<script type="text/javascript">

  var CommentLikesTooltips;
  en4.core.runonce.add(function() {
 // Add hover event to get tool-tip
var feedToolTipAAFEnable="<?php if( !empty($advancedactivity_composer_type) ){ echo $infotooltip ? true:false; }else { echo ''; } ?>";
   if(feedToolTipAAFEnable) {
   var show_tool_tip=false;
   var counter_req_pendding=0;
    $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {  
      var el = $(event.target); 
      ItemTooltips.options.offset.y = el.offsetHeight;
      ItemTooltips.options.showDelay = 0;
        if(!el.hasAttribute("rel")){
                  el=el.parentNode;      
           } 
       show_tool_tip=true;
      if( !el.retrieve('tip-loaded', false) ) {
       counter_req_pendding++;
       var resource='';
      if(el.hasAttribute("rel"))
         resource=el.rel;
       if(resource =='')
         return;
      
        el.store('tip-loaded', true);
        el.store('tip:title', '<div class="" style="">'+
 ' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">'+
    '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">'+
  '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>'+
'</div></div></div></div>'  
);
        el.store('tip:text', '');       
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'show-tooltip-info'), 'default', true) ?>';
        el.addEvent('mouseleave',function(){
         show_tool_tip=false;  
        });       
     
        var req = new Request.HTML({
          url : url,
          data : {
          format : 'html',
          'resource':resource
        },
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {          
            el.store('tip:title', '');
            el.store('tip:text', responseHTML);
            ItemTooltips.options.showDelay=0;
            ItemTooltips.elementEnter(event, el); // Force it to update the text 
             counter_req_pendding--;
              if(!show_tool_tip || counter_req_pendding>0){               
              //ItemTooltips.hide(el);
              ItemTooltips.elementLeave(event,el);
             }           
            var tipEl=ItemTooltips.toElement();
            tipEl.addEvents({
              'mouseenter': function() {
               ItemTooltips.options.canHide = false;
               ItemTooltips.show(el);
              },
              'mouseleave': function() {                
              ItemTooltips.options.canHide = true;
              ItemTooltips.hide(el);                    
              }
            });
            Smoothbox.bind($$(".sea_add_tooltip_link_tips"));
          }
        });
        req.send();
      }
    });
    // Add tooltips
   var window_size = window.getSize()
   var ItemTooltips = new SEATips($$('.sea_add_tooltip_link'), {
      fixed : true,
      title:'',
      className : 'sea_add_tooltip_link_tips',
      hideDelay :200,
      offset : {'x' : 0,'y' : 0},
      windowPadding: {'x':370, 'y':(window_size.y/2)}
    }); 
  }
  });
var sitetagcheckin_id = '<?php echo $this->sitetagcheckin_id;?>';
</script>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
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
			var friend_id = '<?php echo $this->friend_id; ?>';
			document.getElementById('request_member_pops_view_more').style.display ='none';
			document.getElementById('request_member_pops_loding_image').style.display ='';
			en4.core.request.send(new Request.HTML({
				method : 'post',
				'url' : en4.core.baseUrl + 'seaocore/feed/more-mutual-friend/id/' + friend_id,
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

		function userWidgetRequestSend (member_id, page_id) 
		{
			var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'approve'), 'sitepagemember_approve', true) ?>';
			en4.core.request.send(new Request.HTML({
				url : friendUrl,
				data : {
					format: 'html',
					member_id: member_id,
					page_id: page_id
				},
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
				{
					document.getElementById('more_results_shows_'+member_id).innerHTML = "You have successfully approve this member.";
					setTimeout("hideField(" + member_id + ")", 1000);
				}
			}));
		}

		function rejectmemberRequestSend (member_id, page_id, user_id) {
			var friendUrl = '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'sitepagemember_approve', true) ?>';
			en4.core.request.send(new Request.HTML({
				url : friendUrl,
				data : {
					format: 'html',
					member_id: member_id,
					page_id:page_id,
					user_id: user_id
				},
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
					document.getElementById('more_results_shows_'+member_id).innerHTML = "You have ignored the invite to the page.";
					setTimeout("hideField(" + member_id + ")", 1000);
					//alert(document.getElementById('members_results_friend').getChildren().length);
				}
			}));
		}

		function hideField(user_id) {
			document.getElementById('more_results_shows_'+user_id).destroy();
			if (document.getElementById('members_results_friend').getChildren().length == '2') {
			   document.getElementById('members_results_friend').innerHTML = 
			   "<div class='tip' id=''><span><?php echo $this->translate('There are no more requests.');?> </span></div>";
			}
		}
</script>

<?php //endif; ?>

<?php if (empty($this->showViewMore)): ?>
  <div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="top">
      <div class="heading"><?php echo $this->translate('Requested Members')?></div>
    </div>
    <div class="seaocore_members_popup_content" id="members_results_friend">
<?php endif; ?>

<?php if (count($this->paginator) > 0) : ?>
<?php foreach( $this->paginator as $value ): ?>

  <div class="item_member_list" id="more_results_shows_<?php echo $value->member_id; ?>">
    <div class="item_member_thumb">
      <?php echo $this->htmlLink($value->getHref(), $this->itemPhoto($value->getOwner(), 'thumb.icon'), array('class'=>'sea_add_tooltip_link', 'rel'=>'user'.' '.$value->user_id)); ?>
    </div>
    <div class="item_member_option">
			<?php if($value->active == 0 &&  $value->user_approved == 0 ) : ?>
			
				<a href="javascript:void(0);" onclick="userWidgetRequestSend('<?php echo $value->member_id ?>', '<?php echo $value->page_id ?>');" class="icon_sitepage_accept buttonlink"><?php echo $this->translate('Approve Member')?></a>
				
				<a href="javascript:void(0);" onclick="rejectmemberRequestSend('<?php echo $value->member_id ?>', '<?php echo $value->page_id ?>', '<?php echo $value->user_id ?>');" class="icon_sitepage_cancel buttonlink"><?php echo $this->translate('Reject Request')?></a>
			<?php endif;?>
		</div>
    <div class="item_member_details">
      <div class="item_member_name">
        <?php echo $this->htmlLink($value->getHref(), $value->getTitle(), array('title' => $value->getTitle(), 'target' => '_parent', 'class'=>'sea_add_tooltip_link', 'rel'=>'user'.' '.$value->user_id)); ?>
      </div>
    </div>
  </div>
<?php endforeach;?>
<?php else : ?>
<div class="tip" id='sitepagemember_search'>
		  <span>
			  <?php echo $this->translate('No members request.');?>
		  </span>
	</div>
<?php endif; ?>
<?php if (empty($this->showViewMore)): ?>
<div class="seaocore_item_list_popup_more" id="request_member_pops_view_more" onclick="viewMoreTabMutualFriend()">
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => 'feed_viewmore_link',
            'class' => 'buttonlink icon_viewmore'
    ))
    ?>
</div>
<div class="seaocore_item_list_popup_more" id="request_member_pops_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading ...") ?>
</div>

<?php //if (empty($this->showViewMore)): ?>
    </div>
  </div>

  <div class="seaocore_members_popup_bottom">
      <button onclick='smoothboxclose()' ><?php echo $this->translate('Close') ?></button>
  </div>
<?php endif; ?>
<script type="text/javascript">
 function smoothboxclose () {
  parent.window.location.href = "<?php echo $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($value->page_id), 'tab' => $this->tab_selected_id), 'sitepage_entry_view', true) ?>";
  parent.Smoothbox.close ();
 }
</script>