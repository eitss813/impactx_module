<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl';
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>
<ul class="seaocore_sidebar_list o_hidden <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
  <li class="prelative">
    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $this->featured): ?>
      <i class="seaocore_list_featured_label"><?php echo $this->translate('Featured'); ?></i>
    <?php endif; ?>
    <?php $rel = 'user' . ' ' . $this->sitemember->user_id; ?>
                        <?php if($this->circularImage):?>
                        <?php
                        $url = $this->sitemember->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        

                        <a href="<?php echo $this->sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                        </a>
                    <?php else:?>

                      <?php echo $this->htmlLink($this->sitemember->getHref(), $this->itemPhoto($this->sitemember, 'thumb.profile', '', array('align' => 'center', 'class' => 'sea_add_tooltip_link', 'rel' => "$rel"))); ?>
                    <?php endif;?>
    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($this->sponsored)): ?>
      <div class="seaocore_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
        <?php echo $this->translate('Sponsored'); ?>
      </div>
    <?php endif; ?>
    <div class="p5 o_hidden">
      <div class="fleft widthfull">
        <span><?php echo $this->htmlLink($this->sitemember->getHref(), $this->sitemember->getTitle(), array('title' => $this->sitemember->getTitle(), 'class' => 'bold fleft sea_add_tooltip_link', 'rel' => "$rel")); ?></span>
        <?php
//GET VERIFY COUNT AND VERIFY LIMIT
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($this->sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($this->sitemember->user_id);
          $verify_limit = Engine_Api::_()->authorization()->getPermission($this->sitemember->level_id, 'siteverify', 'verify_limit');
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

        <?php
        if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
          $online_status = Engine_Api::_()->sitemember()->isOnline($this->sitemember->user_id);
          ?>
          <span class="fright seaocore_txt_light">
            <?php if (!empty($online_status)) : ?>
              <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
              <!--<?php echo $this->translate("Online"); ?>-->
            <?php endif; ?>
          </span>
        <?php endif; ?>

      </div>
      <div class='seaocore_browse_list_info_date'>
        <?php if (!empty($this->statistics)) : ?>
          <?php echo $this->memberInfo($this->sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
        <?php endif; ?>
      </div>
    </div>
  </li>
</ul>

<style type="text/css" >

.sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::before, .sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::after {
    background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;
	}
</style>