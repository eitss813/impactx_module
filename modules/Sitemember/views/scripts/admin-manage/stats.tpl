<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: stats.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div class="global_form_popup admin_member_stats">
  <h3><?php echo "Member Statistics"; ?></h3>
  <ul>
    <li>
      <?php echo $this->itemPhoto($this->user, 'thumb.icon', $this->user->getTitle()) ?>
    </li>
    <?php if (!empty($this->user->username)): ?>
      <li>
        <?php echo $this->translate('User Name:') ?>
        <span><?php echo $this->user->username; ?></span>
      </li>
    <?php endif; ?>
    <?php if (!empty($this->memberType)): ?>
      <li>
        <?php echo $this->translate('Member Type:') ?>
        <span><?php echo $this->translate($this->memberType) ?></span>
      </li>
    <?php endif; ?>

    <?php if (!empty($this->user->creation_date)): ?>
      <li>
        <?php echo $this->translate('Signup Date:') ?>
        <span><?php echo $this->user->creation_date; ?></span>
      </li>
    <?php endif; ?>

    <li>
      <?php echo $this->translate('Featured:'); ?>
      <span>
        <?php if (!empty($this->featured)) : ?>
          <?php echo "Yes"; ?>
        <?php else : ?>
          <?php echo "No"; ?>
        <?php endif; ?>
      </span>
    </li>

    <li>
      <?php echo $this->translate('Sponsored:') ?>
      <span>
        <?php if (!empty($this->sponsored)) : ?>
          <?php echo "Yes"; ?>
        <?php else : ?>
          <?php echo "No"; ?>
        <?php endif; ?>
      </span>
    </li>

    <li>
      <?php echo $this->translate('Profile Views:') ?>
      <span><?php echo $this->translate(array('%s view', '%s views', $this->user->view_count), $this->locale()->toNumber($this->user->view_count)) ?></span>
    </li>

    <li>
      <?php echo $this->translate('Likes:') ?>
      <span><?php echo $this->translate(array('%s like', '%s likes', $this->likeCount), $this->locale()->toNumber($this->likeCount)) ?></span>
    </li>

    <li>
      <?php echo $this->translate('Friends:') ?>
      <span><?php echo $this->translate(array('%s friend', '%s friends', $this->user->member_count), $this->locale()->toNumber($this->user->member_count)) ?></span>
    </li>

    <?php if (!empty($this->networks) && count($this->networks) > 0): ?>
      <li>
        <?php echo $this->translate('Networks:') ?>
        <span><?php echo $this->fluentList($this->networks) ?></span>
      </li>
    <?php endif; ?>
  </ul>
  <br/>
  <button type="submit" onclick="parent.Smoothbox.close();return false;" name="close_button" value="Close">Close</button>
</div>