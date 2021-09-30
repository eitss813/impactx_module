<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesmultipleform/externals/styles/styles.css'); ?>
<?php if($this->blockposition == 1): ?>
  <ul class="sesmultipleform_keycontact_list sesbasic_sidebar_block sesbasic_clearfix sesbasic_bxs">
    <?php foreach( $this->paginator as $user ):
    $user_item = $this->item('user', $user->user_id); ?>
    <li>
      <?php echo $this->htmlLink($user_item->getHref(), $this->itemPhoto($user_item, 'thumb.icon', $user_item->getTitle()), array('class' => 'keymembers_thumb')) ?>
      <div class='sesmultipleform_keymembers_info'>
        <div class='sesmultipleform_keymembers_name'>
          <?php echo $this->htmlLink($user_item->getHref(), $user_item->getTitle()) ?>
        </div>
        <?php if($user->designation): ?>
        <div class='sesmultipleform_keymembers_stat'>
          <i><?php echo $this->translate($user->designation); ?></i>
        </div>
        <?php endif; ?>
        <?php if($this->emailshow): ?>
        <div class='sesmultipleform_keymembers_stat'>
          <a href='mailto:<?php echo $user_item->email ?>' target="_blank"><b><?php echo $this->translate("Send Email") ?></b></a>
        </div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <ul class="sesmultipleform_keycontact_block_list sesbasic_clearfix sesbasic_bxs">
    <?php foreach( $this->paginator as $user ):
    $user_item = $this->item('user', $user->user_id); ?>
    <li style="height: <?php echo $this->height ?>px;width: <?php echo $this->width ?>px;">
      <div class="keymembers_thumb">
        <?php echo $this->htmlLink($user_item->getHref(), $this->itemPhoto($user_item, 'thumb.profile', $user_item->getTitle())) ?>
      </div>
      <div class='sesmultipleform_keymembers_info'>
        <div class='sesmultipleform_keymembers_name'>
          <?php echo $this->htmlLink($user_item->getHref(), $user_item->getTitle()) ?>
        </div>
        <?php if($user->designation): ?>
        <div class='sesmultipleform_keymembers_stat'>
          <i><?php echo $this->translate($user->designation); ?></i>
        </div>
        <?php endif; ?>
        <?php if($this->emailshow): ?>
        <div class='sesmultipleform_keymembers_stat'>
					<a href='mailto:<?php echo $user_item->email ?>' target="_blank"><b><?php echo $this->translate("Send Email") ?></b></a>
        </div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>