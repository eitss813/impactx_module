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
<?php if ($this->ownerPage): ?>
  <h3><?php echo $this->translate("%s's Reviews", $this->user->getOwner()->toString()); ?></h3>
<?php else: ?>
  <h3><?php echo $this->translate("%s's Reviewed Members", $this->user->getOwner()->toString()); ?></h3>
<?php endif; ?>

<ul class="sm_profile_side_listing sm_side_widget">
  <?php foreach ($this->reviews as $review): ?>
    <li>
      <?php if ($this->ownerPage): ?>
        <?php $user = Engine_Api::_()->getItem('user', $review->owner_id); ?>
      <?php else: ?>
        <?php $user = Engine_Api::_()->getItem('user', $review->resource_id); ?>
      <?php endif; ?>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $user->getTitle()), array('title' => $user->getTitle())) ?>

      <div class='sm_profile_side_listing_info'>

        <div class='sm_profile_side_listing_title'>
          <?php echo $this->htmlLink($user->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($user->getTitle(), $this->title_truncation), array('title' => $user->getTitle())) ?>
        </div>

        <div class='sm_profile_side_listing_stats'>
          <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, $this->title_truncation), array('title' => $review->title)) ?>
        </div>

        <div class='seaocore_sidebar_list_details'>
          <?php echo $this->showRatingStarMember($review->rating, "user", 'small-star'); ?>
        </div>          

      </div>
    </li>
  <?php endforeach; ?>
</ul>