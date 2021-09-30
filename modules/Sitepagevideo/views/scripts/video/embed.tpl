<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: embed.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
  <?php if ($this->error == 1): ?>
    <?php echo $this->translate('Embedding of videos has been disabled.') ?>
    <?php return ?>
  <?php elseif ($this->error == 2): ?>
    <?php echo $this->translate('Embedding of videos has been disabled for this video.') ?>
    <?php return ?>
  <?php elseif (!$this->video || $this->video->status != 1): ?>
    <?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.') ?>
    <?php return ?>
  <?php endif; ?>

  <textarea cols="50" rows="4"><?php
  echo $this->embedCode;
  ?></textarea>

  <br />
  <br />

  <div>
    <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
  </div>
</div>