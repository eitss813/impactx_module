<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: post.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>

<?php
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/Adintegration.tpl';
?>

<div class="layout_middle">
  <div class="generic_layout_container">
  <div class="sitevideo_view_top">
      <?php echo $this->htmlLink($this->sitevideo->getHref(), $this->itemPhoto($this->sitevideo, 'thumb.icon', '', array('align' => 'left'))) ?>
      <p>	
          <?php echo $this->sitevideo->__toString() ?>	
          <?php echo $this->translate('&raquo; '); ?>
          <?php echo $this->htmlLink($this->sitevideo->getHref(array('tab' => $this->tab_selected_id)), $this->translate('Discussions')) ?>
          <?php echo $this->translate('&raquo; '); ?>
          <?php echo $this->topic->__toString() ?>
      </p>
  </div>  
  <div class="clr o_hidden"> 
      <?php if ($this->message) echo $this->message ?>
      <?php if ($this->form) echo $this->form->render($this) ?>
  </div>
</div>
</div>