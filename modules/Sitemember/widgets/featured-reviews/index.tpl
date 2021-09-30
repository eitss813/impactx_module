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

<ul class="seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
<?php foreach ($this->paginator as $review): ?>  
    <li>
    <?php $rating_avg = Engine_Api::_()->getDbtable('userInfo', 'seaocore')->getColumnValue($review->resource_id, 'rating_avg'); ?>  
      <?php $item = Engine_Api::_()->getItem('user', $review->resource_id); ?>
      <?php echo $this->htmlLink($item, $this->itemPhoto($item, 'thumb.icon')) ?>
      <div class='seaocore_sidebar_list_info'>
        <div class="seaocore_sidebar_list_title">
  <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->getTitle(), 20), array('title' => $review->getTitle())) ?>
        </div>	
        <div class="seaocore_sidebar_list_details">
  <?php echo $this->translate(" for "); ?>
          <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 20), array('title' => $item->getTitle())) ?>
        </div>
        <div class='seaocore_sidebar_list_details'>  
  <?php echo $this->showRatingStarMember($rating_avg, 'user', 'small-star'); ?>
        </div>	
      </div>  
      <div class="clr sm_review_quotes">
        <b class="c-l fleft"></b>
  <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($review->getDescription(), 100) ?>
        <b class="c-r fright"></b>
      </div>    
    </li>
<?php endforeach; ?>
</ul>
