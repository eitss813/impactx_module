<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _review.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<div class="sm_review_rich_content">
  <div class="sm_review_rich_content_title">
    <span class="fright">
      <?php echo $this->showRatingStarMember($this->ratingValue, $this->review->type ,'small-star'); ?>
    </span>
    <?php echo $this->htmlLink($this->review->getHref(), $this->review->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->review->getType() . ' ' . $this->review->getIdentity())) ?>
  </div>
  <div class="sm_review_rich_content_stats">
    <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($this->review->body, 50)) ?>
  </div>
</div>