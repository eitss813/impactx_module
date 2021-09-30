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
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_rating.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>

<ul class="seaocore_sidebar_list sm_reviews_breakdowns">
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
    <li>
      <?php echo $this->translate(array('Total <b>%s</b> Review', 'Total <b>%s</b> Reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)) ?>
    </li>
  <?php endif; ?>
  <li>
    <b><?php echo $this->translate("Ratings Breakdown"); ?></b>
    <div class="sm_rating_breakdowns">
      <ul>
        <?php foreach ($this->ratingCount as $i => $count): ?>
          <li>
            <div class="left"><?php echo $this->translate(array("%s star&nbsp;:", "%s stars&nbsp;:", $i), $i); ?></div>
            <?php $pr = $count > 0 ? ($count * 100 / $this->totalReviews) : 0; ?>
            <div class="count"><?php echo $count; ?></div>
            <div class="rate_bar b_medium">
              <span style="width:<?php echo $pr; ?>%;" <?php echo empty($count) ? "class='sm_border_none'" : "" ?>></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </li>
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
    <li>
      <?php echo $this->translate("%s out of %1s reviews have recommendations.", '<b>' . $this->totalRecommend . '</b>', '<b>' . $this->totalReviews . '</b>'); ?>
    </li>
  <?php endif; ?>
</ul>