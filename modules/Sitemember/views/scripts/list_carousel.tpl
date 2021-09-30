<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list_carousel.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $sitemember = $this->sitemember; ?>
<?php $rel = 'user' . ' ' . $sitemember->user_id; ?>
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
