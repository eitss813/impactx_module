<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel_compliment.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $sitemember = $this->sitemember; ?>
<?php $rel = 'user' . ' ' . $sitemember->user_id; ?>
<?php if($this->itemViewType && $this->vertical): ?>
 <li class="compliment_list_crousal sitemember_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
                            <?php echo $this->htmlLink($sitemember->getHref(array()), $this->itemPhoto($sitemember, "thumb.icon", array('title' => $sitemember->getTitle()))) ?>
                    <div class='seaocore_sidebar_list_info'>
                        <div class='seaocore_sidebar_list_title'>
                            
                                <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->truncation), array('title' => $sitemember->getTitle(),'class' => 'sea_add_tooltip_link')); ?>
                         </div>
                        <?php if(!empty($this->compliment) && !empty($this->complimentItem)): ?>
                            <span class="seao_listings_stats">
                                        <i class="fa-gift seao_icon_strip seao_icon" aria-hidden="true"></i>
                                        <span class="o_hidden"> 
                                            <?php echo $this->compliment->getComplimentCount(array('complimentcategory_id' => $this->complimentItem->getIdentity(),'resource_id' => $sitemember->getIdentity(),'resource_type' =>'user')); ?>
                                        </span>
                            </span>
                            <?php endif; ?>
                     <?php if (!empty($this->showOptions)) : ?>
                        <?php echo $this->memberInfo($sitemember, $this->showOptions, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                     <?php endif; ?>
                            <div class="clr sitemember_action_link_options sitemember_action_links">
                         <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                            <?php if (!empty($uaseFRIENFLINK)) : ?>
                    <?php //echo $uaseFRIENFLINK; ?>
                    <?php endif; ?></div>
                    </div>
                          
                </li> 
<?php else: ?> 
 <li class="sitemember_grid_view sitemember_carousel_content_item" style="height: <?php echo ($this->blockHeight) ?>px;width : <?php echo ($this->blockWidth) ?>px;">
  <div class="sitemember_grid_thumb">
    <a href="<?php echo $sitemember->getHref(array()) ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" title="<?php echo $sitemember->getTitle() ?>">
      <?php
      $isLarge = ($this->blockWidth > 170);
      $url = $sitemember->getPhotoUrl('thumb.profile');
      if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
      endif;
      ?>
      <span style="background-image: url(<?php echo $url; ?>); <?php if($this->circularImage):?>height:<?php echo $this->circularImageHeight; ?>px;<?php endif;?>"></span>
    </a>
    <?php if (!empty($this->showOptions) && in_array('featuredLabel', $this->showOptions) && $sitemember->featured): ?>
      <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></i>
    <?php endif; ?>

    <?php if (!empty($this->titlePosition)) : ?>
      <div class="sitemember_grid_title">
        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel'=> "$rel")); ?> 

        <?php
        //GET VERIFY COUNT AND VERIFY LIMIT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->showOptions) && in_array('verifyLabel', $this->showOptions)) :
          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
          $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
          ?>
          <?php if ($verify_count >= $verify_limit): ?>                 
            <span class="siteverify_tip_wrapper">
                <i class="sitemember_list_verify_label mleft5"></i>
                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
            </span>
          <?php endif; ?>
        <?php endif; ?>

        <?php
        if (!empty($this->showOptions) && in_array('memberStatus', $this->showOptions)) :
          $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
          ?>
          <span class="fright seaocore_txt_light">
    <?php if (!empty($online_status)) : ?>
              <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
              <!--<?php echo $this->translate("Online"); ?>-->
    <?php //else: ?>
<!--              <img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />-->
              <!--<?php //echo $this->translate("Offline"); ?>-->
          <?php endif; ?>
          </span>
      <?php endif; ?>
      </div>  
<?php endif; ?>

  </div>
    <?php if (!empty($this->showOptions) && in_array('sponsoredLabel', $this->showOptions) && !empty($sitemember->sponsored)): ?>
    <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;'>
    <?php echo $this->translate('Sponsored'); ?>
    </div>
    <?php endif; ?>
  <div class="sitemember_grid_info">
      <?php if(!empty($this->compliment) && !empty($this->complimentItem)): ?>
        <span class="sitemember_compliment_info seao_listings_stats">
         <!--  <?php  echo $this->itemPhoto($this->complimentItem, 'thumb.icon', '', array(
                'title' => $this->complimentItem->getTitle())); ?>-->
                <i class="fa-gift seao_icon_strip seao_icon" aria-hidden="true"></i>
                <span> 
                <?php echo $this->compliment->getComplimentCount(array('complimentcategory_id' => $this->complimentItem->getIdentity(),'resource_id' => $sitemember->getIdentity(),'resource_type' =>'user')); ?> Compliment
                </span>
        </span>
<?php endif; ?>
      <?php if (empty($this->titlePosition)) : ?>
      <div class="sitemember_grid_title">
        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel'=> "$rel")); ?>

        <?php
        //GET VERIFY COUNT AND VERIFY LIMIT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->showOptions) && in_array('verifyLabel', $this->showOptions)) :
          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
          $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
          ?>
          <?php if ($verify_count >= $verify_limit): ?>
            <span class="siteverify_tip_wrapper">
                <i class="sitemember_list_verify_label mleft5"></i>
                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
            </span>
          <?php endif; ?>
        <?php endif; ?>

        <?php
        if (!empty($this->showOptions) && in_array('memberStatus', $this->showOptions)) :
          $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
          ?>
          <span class="fright seaocore_txt_light">
            <?php if (!empty($online_status)) : ?>
              <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
              <!--<?php //echo $this->translate("Online"); ?>-->
            <?php //else: ?>
<!--              <img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />-->
              <!--<?php //echo $this->translate("Offline"); ?>-->
          <?php endif; ?>
          </span>
      <?php endif; ?>
      <?php if(!empty($this->compliment) && !empty($this->complimentItem)): ?>
<span class="sitemember_compliment_info seao_listings_stats">
  <!--<?php  echo $this->itemPhoto($this->complimentItem, 'thumb.small-icon', '', array(
            'title' => $this->complimentItem->getTitle())); ?>-->
            <i class="fa-gift seao_icon_strip seao_icon" aria-hidden="true"></i>
            <span> 
            <?php echo $this->compliment->getComplimentCount(array('complimentcategory_id' => $this->complimentItem->getIdentity(),'resource_id' => $sitemember->getIdentity(),'resource_type' =>'user')); ?> Compliment

            </span>
</span>
<?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($this->showOptions)) : ?>
      <?php echo $this->memberInfo($sitemember, $this->showOptions, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
    <?php endif; ?>
<?php if(!empty($this->links)):?>
      <div class="clr sitemember_action_link_options sitemember_action_links">
    <?php
    $items = Engine_Api::_()->getItem('user', $sitemember->user_id);
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    ?>
    <?php if (Engine_Api::_()->seaocore()->canSendUserMessage($items) && !empty($viewer_id)  && in_array('messege', $this->links)): ?>
      <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
    <?php endif; ?>

    <?php if (!empty($this->links) && in_array('addfriend', $this->links)): ?>
      <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
      <?php if (!empty($uaseFRIENFLINK)) : ?>
    <?php echo $uaseFRIENFLINK; ?>
  <?php endif; ?>
<?php endif; ?>
  </div>
<?php endif; ?>
  </div>
</li>
 <?php endif; ?>
<script type="text/javascript">
    en4.core.runonce.add(function() { 
        setGridHoverEffect('<?php echo $this->circularImage;?>');
    });
</script>

<style type="text/css" >

.sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::before, .sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::after {
    background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;
	}
	
</style>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
?>

<?php 
$this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_icon_toolbar.css');
?>
<script type="text/javascript">
  
  en4.core.runonce.add(function() {
       // Add hover event to get tool-tip
   var show_tool_tip=false;
   var counter_req_pendding=0;
    $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {  
      var el = $(event.target); 
      ItemTooltips.options.offset.y=el.offsetHeight;
      ItemTooltips.options.showDelay=100;
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
  '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>'+
'</div></div></div></div>'  
);
        el.store('tip:text', '');       
        // Load the likes
        var url = en4.core.baseUrl+'/seaocore/feed/show-tooltip-info';
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
      hideDelay :0,
      offset : {'x' : 0,'y' : 0},
      windowPadding: {'x':370, 'y':(window_size.y/2)}
    }
    );  
  });  
</script>