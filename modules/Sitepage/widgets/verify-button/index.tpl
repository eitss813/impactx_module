<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepageverify.css') ?>
          
<?php
if(empty($this->viewer_id)){
    $verify_show = "display:none;";
    $successMessege_show = "display:block;";
    $sitepage_ownerprofile = "display:none;";
    $adminMessege_show = "display:none;";
}
elseif ($this->viewer_id != $this->resource_owner_id ) {
  if (empty($this->hasVerified)) {
    $verify_show = "display:block;";
    $successMessege_show = "display:block;";
    $sitepage_ownerprofile = "display:none;";
    $adminMessege_show = "display:none;";
  } else {
    if (!empty($this->admin_approve)) {
      $verify_show = "display:none;";
      $successMessege_show = "display:block;";
      $sitepage_ownerprofile = "display:block;";
      $adminMessege_show = "display:none;";
    } else {
      $verify_show = "display:none;";
      $successMessege_show = "display:block;";
      $sitepage_ownerprofile = "display:none;";
      $adminMessege_show = "display:block;";
    }
  }
} else {
  $verify_show = "display:none;";
  $successMessege_show = "display:block;";
  $sitepage_ownerprofile = "display:none;";
  $adminMessege_show = "display:none;";
}
?>

<div id="sitepage" class="clr">
  <div class="sitepage_button_box mbot10" id="<?php echo 'page'; ?>_verify_<?php echo $this->resource_id; ?>" style ='<?php echo $verify_show; ?>' >
	  <div>
    	<a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'verify', 'action' => 'index', 'resource_id' => $this->resource_id,), 'default', true) ?>');" class="sitepage_buttonlink"><?php echo $this->translate("Verify %s", ucfirst($this->resource_title)); ?></a>
    </div>
  </div>
  
  <?php if ($this->verify_count > 0): ?>
      <div class="sitepage_verify_msg_box">
        <div id="<?php echo 'page'; ?>_successMessege_<?php echo $this->resource_id; ?>"  style ='<?php echo $successMessege_show; ?>'>
          <div class="seaocore_txt_light o_hidden">
            <?php if ($this->verify_count >= $this->verify_limit): ?> 
                <div class="fleft sitepage_tick_image">
                    <span class="sitepage_tip"><?php echo $this->translate('Verified Page'); ?><i></i></span>
                </div>
            <?php endif; ?>
            <span class="o_hidden sitepage_verify_label"><?php echo $this->translate("%s has been verified by", ucfirst($this->resource_title)) . ' ' . $this->translate(array('%s member', '%s members', $this->verify_count), $this->locale()->toNumber($this->verify_count)) . '.'; ?>
            </span>  
          </div>
          <a class="sitepage_links mbot10 f_small" href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'verify', 'action' => 'content-verify-member-list', 'resource_id' => $this->resource_id), 'default', true) ?>');"><?php echo $this->translate("View Details") . " &raquo;"; ?></a>
          <div id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>" class="clr o_hidden"  style ="<?php echo $sitepage_ownerprofile; ?>" >
            <div><?php echo $this->translate('You have verified this page.'); ?></div>
            <div class="siteverify_links f_small">
              <?php if (!empty($this->is_comment)) :
                ?>
                <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'verify', 'action' => 'edit', 'id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Edit"); ?></a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
  <?php endif; ?>

  <div class="sitepage_verify_msg_box" style ='<?php echo $adminMessege_show; ?>'>
      <div id="<?php echo 'user'; ?>_adminMessege_<?php echo $this->resource_id; ?>" class="o_hidden clr" style ='<?php echo $adminMessege_show; ?>'>
        <div class="seaocore_txt_light">
          <?php echo $this->translate("Your verification to %s will be approved by administrator.", ucfirst($this->resource_title)); ?>
        </div>
        <div class="sitepage_links">
          <?php if (!empty($this->is_comment)) : ?>
            <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'verify', 'action' => 'edit', 'id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Edit Request"); ?></a><?php endif; ?>
          <?php
          if (!empty($this->is_comment))
            echo $this->translate("|");
          ?>
          <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'verify', 'action' => 'delete', 'id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Cancel Request"); ?></a>
        </div>
      </div>
  </div>
</div>

<script type="text/javascript">
  function showSmoothBox(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }
</script>