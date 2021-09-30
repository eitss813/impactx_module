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

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
  <?php if (empty($this->ownerMessage)): ?>
    <h2><?php echo $this->translate("Reviews for %s", $this->user->getOwner()->toString()); ?></h2>
    <br />
  <?php else: ?>
    <h2><?php echo $this->translate("%s's Reviewed Members", $this->user->getOwner()->toString()); ?></h2>
    <?php $url = $this->url(array('user_id' => $this->user->user_id), 'sitemember_review_memberreviews', true); ?>
    <?php $userName = $this->user->getTitle(); ?><br />
  <?php endif; ?>

<?php else : ?>

  <?php if (empty($this->ownerMessage)): ?>
    <h2><?php echo $this->translate("%s's Ratings", $this->user->getOwner()->toString()); ?></h2>
    <?php echo $this->translate("Below are ratings posted by other members for %s.", $this->user->getOwner()->toString()); ?><br /><br />
  <?php else: ?>
    <h2><?php echo $this->translate("%s's Rated Members", $this->user->getOwner()->toString()); ?></h2>
    <?php $url = $this->url(array('user_id' => $this->user->user_id), 'sitemember_review_memberreviews', true); ?>
    <?php $userName = $this->user->getTitle(); ?>
    <?php echo $this->translate("Below are members whom %1s has rated. You can also check out %2s's rated posted by other members.", $this->user->getOwner()->toString(), "<a href='$url'>$userName</a>"); ?><br /><br />
  <?php endif; ?>
<?php endif; ?>