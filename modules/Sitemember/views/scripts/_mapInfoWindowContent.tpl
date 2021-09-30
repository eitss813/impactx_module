<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mapInfoWindowContent.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
<div id="content">
  <div id="siteNotice">
  </div>
  <div class="sitemember_map_info_tip o_hidden">
    <div class="sitemember_map_info_tip_top o_hidden">
      <div class="fright">
        <span >
          <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $this->sitemember->featured): ?>
            <i class="seao_icon seaocore_icon_featured" title="<?php echo $this->translate('Featured'); ?>"></i>
          <?php endif; ?>
        </span>
        <span>
          <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($this->sitemember->sponsored)): ?>
            <i class="seao_icon seaocore_icon_sponsored" title="<?php echo $this->translate('Sponsored'); ?>"></i>
          <?php endif; ?>
        </span>
      </div>
      <div class="sitemember_map_info_tip_title fleft">
        <?php echo $this->htmlLink($this->sitemember->getHref(), $this->sitemember->getTitle()) ?>

        <?php
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($this->sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($this->sitemember->user_id);
          $user = Engine_Api::_()->getItem('user', $this->sitemember->user_id);
          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
          ?>
          <?php if ($verify_count >= $verify_limit): ?>
            <span class="siteverify_tip_wrapper">
                <i class="sitemember_list_verify_label mleft5"></i>
                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
            </span>
            <?php
          endif;
        endif;
        ?>         
      </div>

      <?php
      if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
        $online_status = Engine_Api::_()->sitemember()->isOnline($this->sitemember->user_id);
        ?>
        <span class="fright seaocore_txt_light">
          <?php if (!empty($online_status)) : ?>
            <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
            <?php echo $this->translate("Online"); ?>
          <?php //else: ?>
<!--            <img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />-->
            <?php //echo $this->translate("Offline"); ?>
          <?php endif; ?>
        </span>
      <?php endif; ?>
    </div>
    <div class="sitemember_map_info_tip_photo prelative" >
      <?php echo $this->htmlLink($this->sitemember->getHref(array('profile_link' => 1)), $this->itemPhoto($this->sitemember, 'thumb.icon')) ?>
    </div>
    <div class="sitemember_map_info_tip_info">
      <?php if (!empty($this->statistics)) : ?>
        <?php echo $this->memberInfo($this->sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
      <?php endif; ?>
    </div>
  </div>
</div>