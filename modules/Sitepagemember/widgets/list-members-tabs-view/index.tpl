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
<?php if(empty($this->is_ajax)): ?>
	<div class="layout_core_container_tabs">
		<div class="tabs_alt tabs_parent">
			<ul id="main_tabs">
				<?php foreach ($this->tabs as $tab): ?>
				<?php $class = $tab->name == $this->activTab->name ? 'active' : '' ?>
					<li class = '<?php echo $class ?>'  id = '<?php echo 'sitepagemember_' . $tab->name.'_tab' ?>'>
						<a href='javascript:void(0);'  onclick="tabSwitchSitepagemember('<?php echo $tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div id="hideResponse_div" style="display: none;"></div>
		<div id="sitepagelbum_members_tabs">
<?php endif; ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <?php if($this->is_ajax !=2):  ?>
      <ul class="seaocore_browse_list" id="sitepagemember_list_tab_member_content">
    <?php endif; ?>
    <?php foreach( $this->paginator as $member ):  ?>
			<li>
			<div class="seaocore_browse_list_photo_small">
				<?php	$user_object = Engine_Api::_()->getItem('user', $member->user_id);
					echo $this->htmlLink($user_object->getHref(), $this->itemPhoto($user_object->getOwner(), 'thumb.icon')); ?>
			</div>
      <div class="seaocore_browse_list_options seaocore_icon_done">
					<?php //FOR MESSAGE LINK
					$item = Engine_Api::_()->getItem('user', $member->user_id);
					if ((Engine_Api::_()->seaocore()->canSendUserMessage($item)) && (!empty($this->viewer_id))) : ?>
						<a href="<?php echo $this->base_url ?>/messages/compose/to/<?php echo $member->user_id ?>" target="_parent" class="buttonlink smoothbox icon_message"><?php echo $this->translate('Message'); ?></a>
					<?php endif; ?>
					<?php //Add friend link.
					$uaseFRIENFLINK = $this->userFriendshipAjax($this->user($member->user_id)); ?>
					<?php if (!empty($uaseFRIENFLINK)) : ?>
						<?php echo $uaseFRIENFLINK; ?>
					<?php endif; ?>
				</div>
			<div class="seaocore_browse_list_info">
				<div class="seaocore_browse_list_info_title">
					<div class="seaocore_title">
						<?php  echo $this->htmlLink($this->item('user', $member->user_id)->getHref(), $this->user($member->user_id)->displayname, array('title' => $member->displayname, 'target' => '_parent')); ?>
					</div>
				</div>
				<div class="seaocore_browse_list_info_date">
				<?php echo $this->htmlLink(array('route' => 'sitepagemember_approve', 'action' => 'page-join', 'user_id' => $member->user_id), $this->translate(array('%s Page Joined', '%s Pages Joined', $member->JOINP_COUNT), $this->locale()->toNumber($member->JOINP_COUNT)), array('onclick' => 'owner(this);return false')); ?>
				</div>
			</div>
		</li>
    <?php endforeach;?>
    <?php if($this->is_ajax !=2): ?>  
    </ul>  
  <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No members have been joined yet.');?>
      </span>
    </div>
  <?php endif; ?>
<?php if(empty($this->is_ajax)): ?>
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitepagemember_members_tabs_view_more" onclick="viewMoreTabMember()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitepagemember_members_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitepagemember = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitepagemember_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitepagemember_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitepagemember_'+tabName+'_tab'))
        $('sitepagemember_'+tabName+'_tab').set('class', 'active');
   if($('sitepagelbum_members_tabs')) {
      $('sitepagelbum_members_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="sitepage_tabs_loader_img" /></center>';
    }   
    if($('sitepagemember_members_tabs_view_more'))
    $('sitepagemember_members_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepagemember/name/list-members-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        //category_id : '<?php //echo $this->category_id?>',
        tabName: tabName,
        //margin_photo : '<?php //echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitepagelbum_members_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitepageMember();
             <?php endif; ?> 
      }
    });

    request.send();
  }
</script>
<?php endif; ?>
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<?php if(!empty ($this->showViewMore)): ?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLinkSitepageMember();  
    });
    function getNextPageSitepageMember(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitepageMember(){
      if($('sitepagemember_members_tabs_view_more'))
        $('sitepagemember_members_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabMember() {
    $('sitepagemember_members_tabs_view_more').style.display ='none';
    $('sitepagemember_members_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepagemember/name/list-members-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        //category_id : '<?php //echo $this->category_id?>',
        tabName : '<?php echo $this->activTab->name ?>',
        //margin_photo : '<?php //echo $this->marginPhoto ?>',
        page: getNextPageSitepageMember()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitepagemember_list_members_tabs_view').innerHTML;
        $('sitepagemember_list_tab_member_content').innerHTML = $('sitepagemember_list_tab_member_content').innerHTML + photocontainer;
        $('sitepagemember_members_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";
      }
    }));

    return false;
  }
</script>
<?php endif; ?>