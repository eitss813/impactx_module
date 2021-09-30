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

<?php $review = $this->reviews; ?>
<?php $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember'); ?>
<?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitemember'); ?>
<?php $reviewDescriptionsTable = Engine_Api::_()->getDbtable('reviewDescriptions', 'sitemember'); ?>

<div id="profile_review" class="pabsolute"></div>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_rating.css');
?>

<div class="o_hidden">
  <div class="sm_view_top">
    <?php echo $this->htmlLink($this->user->getHref(array('profile_link' => 1)), $this->itemPhoto($this->user, 'thumb.icon', $this->user->getTitle()), array('class' => "thumb_icon", 'title' => $this->user->getTitle())) ?>
    <div class="sm_review_view_right">
      <?php //echo $this->content()->renderWidget("sitemember.review-button", array('user_guid' => $this->user->getGuid(), 'user_profile_page' => 1, 'identity' => $this->identity)) ?>
    </div>
    <h2>
      <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle()) ?>
    </h2> 
  </div>

  <div class="sm_profile_review b_medium sm_review_block">
    <div class="sm_profile_review_left">
      <div class="sm_profile_review_title">
        <?php if (empty($reviewcatTopbox['ratingparam_name'])): ?>
          <?php echo $this->translate("Average User Rating"); ?>
        <?php endif; ?>
      </div>
      <?php $iteration = 1; ?>
      <div class="sm_profile_review_stars">
        <span class="sm_profile_review_rating">
          <span class="fleft">
            <?php echo $this->showRatingStarMember(Engine_Api::_()->getDbtable('userInfo', 'seaocore')->getColumnValue($this->user->getIdentity(), 'rating_users'), 'user', 'big-star'); ?>
          </span>
          <?php if (count($this->ratingDataTopbox) > 1): ?>
            <i class="arrow_btm fleft"></i>
          <?php endif; ?>
        </span>	
      </div>

      <?php if (count($this->ratingDataTopbox) > 1): ?>
        <div class="sm_ur_bdown_box_wrapper br_body_bg b_medium">
          <div class="sm_ur_bdown_box">
            <div class="sm_profile_review_title">
              <?php echo $this->translate("Average User Rating"); ?>
            </div>
            <div class="sm_profile_review_stars">
              <?php echo $this->showRatingStarMember(Engine_Api::_()->getDbtable('userInfo', 'seaocore')->getColumnValue($this->user->getIdentity(), 'rating_users'), 'user', 'big-star'); ?>
            </div>

            <div class="sm_profile_rating_parameters">
              <?php $iteration = 1; ?>
              <?php foreach ($this->ratingDataTopbox as $reviewcatTopbox): ?>
                <?php if (!empty($reviewcatTopbox['ratingparam_name'])): ?>	         
                  <div class="o_hidden">
                    <div class="parameter_title">
                      <?php echo $this->translate($reviewcatTopbox['ratingparam_name']) ?>
                    </div>
                    <div class="parameter_value">
                      <?php echo $this->showRatingStarMember($reviewcatTopbox['avg_rating'], 'user', 'small-box', $reviewcatTopbox['ratingparam_name']); ?>     
                    </div>
                    <div class="parameter_count"><?php //echo $this->sitemember->getNumbersOfUserRating('user', $reviewcatTopbox['ratingparam_id']);  ?></div>
                  </div>
                <?php endif; ?>
                <?php $iteration++; ?>
              <?php endforeach; ?>
            </div>
            <div class="clr"></div>
          </div>
        </div>
      <?php endif; ?>

      <div class="sm_profile_review_stat clr">
        <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)); ?>
      </div>

      <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
        <?php
        $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user', 'resource_id' => $this->user_id));
        $totalRecommend = $recommendpaginator->getTotalItemCount();
        ?>
        <div class="sm_profile_review_stat clr">
          <?php echo $this->translate(array('Recommended by %1$s member (%2$s)', 'Recommended by %1$s members (%2$s)', $totalRecommend, '<b>' . $this->recommend_percentage . '%</b>'), $totalRecommend, '<b>' . $this->recommend_percentage . '%</b>'); ?>
        </div>
      <?php endif; ?> 

      <?php if (!empty($this->viewer_id) && $this->can_create && empty($this->isajax)): ?>
        <?php $rating_value_2 = 0; ?>	
        <?php if (!empty($this->reviewRateMyData)): ?>	
          <?php foreach ($this->reviewRateMyData as $reviewRateData): ?>
            <?php if ($reviewRateData['ratingparam_id'] == 0): ?>
              <?php $rating_value_2 = $reviewRateData['rating']; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        <div class="sm_profile_review_title mtop5" id="review-my-rating">
          <?php echo $this->translate("My Rating"); ?>
        </div>	
        <div class="sm_profile_review_stars">
          <?php echo $this->showRatingStarMember($rating_value_2, 'user', 'big-star', 'My Rating'); ?>		     
        </div>
      <?php endif; ?>
    </div>

    <!--Rating Breakdown Hover Box Starts-->
    <div class="sm_profile_review_right">
      <div class="sm_rating_breakdowns">
        <div class="sm_profile_review_title">
          <?php echo $this->translate("Ratings Breakdown"); ?>
        </div>
        <ul>
          <?php for ($i = 5; $i > 0; $i--): ?>
            <li>
              <div class="left"><?php echo $this->translate(array("%s star&nbsp;:", "%s stars&nbsp;:", $i), $i); ?></div>
              <?php
              $count = Engine_Api::_()->getDbtable('ratings', 'sitemember')->getNumbersOfUserRating($this->user->getIdentity(), 'user', 0, $i, $this->viewer_id, 'user');
              $pr = $count ? ($count * 100 / $this->totalReviews) : 0;
              ?>
              <div class="count"><?php echo $count; ?></div>
              <div class="rate_bar b_medium">
                <span style="width:<?php echo $pr; ?>%;" <?php echo empty($count) ? "class='sm_border_none'" : "" ?>></span>
              </div>
            </li>
<?php endfor; ?>
        </ul>
      </div>
      <div class="clr"></div>
    </div>
    <!--Rating Breakdown Hover Box Ends-->
  </div>

  <ul class="sm_reviews_listing" id="profile_sitemember_content">
    <li>
      <div class="sm_reviews_listing_photo">
        <?php if ($review->owner_id): ?>
          <?php echo $this->htmlLink($review->getOwner()->getHref(), $this->itemPhoto($review->getOwner(), 'thumb.icon', $review->getOwner()->getTitle()), array('class' => "thumb_icon")) ?>
        <?php else: ?>
          <?php $itemphoto = $this->layout()->staticBaseUrl . "application/modules/User/externals/images/nophoto_user_thumb_icon.png"; ?>
          <img smc="<?php echo $itemphoto; ?>" class="thumb_icon" alt="" />
<?php endif; ?>
      </div>
      <div class="sm_reviews_listing_info">
        <div class=" sm_reviews_listing_title">
          <div class="sm_ur_show_rating_star">
            <?php $ratingData = $review->getRatingData(); ?>
            <?php
            $rating_value = 0;
            foreach ($ratingData as $reviewcat):
              if (empty($reviewcat['ratingparam_name'])):
                $rating_value = $reviewcat['rating'];
                break;
              endif;
            endforeach;
            ?>
            <span class="fright">  
              <span class="fleft">
              <?php echo $this->showRatingStarMember($rating_value, 'user', 'big-star'); ?>
              </span>
              <?php $countR = 0;
              if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 2) :
                ?>
                <?php $countR = count($ratingData); ?>
              <?php endif; ?>                
              <?php if ($countR > 1): ?>
                <i class="fright arrow_btm"></i>
            <?php endif; ?>
            </span>
                <?php if ($countR > 1): ?>
              <div class="sm_ur_show_rating  br_body_bg b_medium">
                <div class="sm_profile_rating_parameters sm_ur_show_rating_box">
                    <?php foreach ($ratingData as $reviewcat): ?>
                    <div class="o_hidden">
                        <?php if (!empty($reviewcat['ratingparam_name'])): ?>
                        <div class="parameter_title">
                          <?php echo $this->translate($reviewcat['ratingparam_name']); ?>
                        </div>
                        <div class="parameter_value">
                        <?php echo $this->showRatingStarMember($reviewcat['rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
                        </div>
                        <?php else: ?>
                        <div class="parameter_title">
                          <?php echo $this->translate("Overall Rating"); ?>
                        </div>	
                        <div class="parameter_value">
                        <?php echo $this->showRatingStarMember($reviewcat['rating'], $review->type, 'big-star'); ?>
                        </div>
                    <?php endif; ?> 
                    </div>
              <?php endforeach; ?>
                </div>
              </div>
          <?php endif; ?>
          </div>
          <?php if ($review->featured): ?>
            <i class="sm_icon seaocore_icon_featured fright" title="<?php echo $this->translate('Featured'); ?>"></i> 
<?php endif; ?>	
          <div class="sm_review_title"><?php echo $review->getTitle() ?></div>
        </div>
        <div class="sm_reviews_listing_stat seaocore_txt_light">
            <?php if ($review->recommend && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0)): ?>
            <span class="fright sm_profile_userreview_recommended">
            <?php echo $this->translate('Recommended'); ?>
              <span class='sm_icon_tick sm_icon'></span>
            </span>
          <?php endif; ?>
          <?php echo $this->timestamp(strtotime($review->modified_date)); ?> - 
          <?php if (!empty($review->owner_id)): ?>
            <?php echo $this->translate('by'); ?> <?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle()) ?>
          <?php else: ?>
  <?php echo $this->translate('by'); ?> <?php echo $review->anonymous_name; ?>
        <?php endif; ?>
        </div> 
        <div class="clr"></div>
          <?php if ($review->pros): ?>
          <div class="sm_reviews_listing_proscons">
            <b><?php echo $this->translate("Pros") ?>: </b>
          <?php echo $review->pros ?> 
          </div>
<?php endif; ?>
          <?php if ($review->cons): ?>
          <div class="sm_reviews_listing_proscons"> 
            <b><?php echo $this->translate("Cons") ?>: </b>
          <?php echo $review->cons ?>
          </div>
        <?php endif; ?>

          <?php if ($review->getDescription()): ?>
          <div class="sm_reviews_listing_proscons">
            <b><?php echo $this->translate("Summary") ?>: </b>
          <?php echo $review->body ?>
          </div>
          <?php endif; ?>

        <div class="feed_item_link_desc">
            <?php $this->reviewDescriptions = $reviewDescriptionsTable->getReviewDescriptions($this->reviews->review_id); ?>
            <?php if (count($this->reviewDescriptions) > 0): ?>
            <div class="sitemember_profile_info_des_update sm_review_block">        
  <?php foreach ($this->reviewDescriptions as $value) : ?>
                    <?php if ($value->body): ?>
                  <div class="b_medium">
                    <div class="sitemember_profile_info_des_update_date">
                      <?php echo $this->translate("Updated On %s", $this->timestamp(strtotime($value->modified_date))); ?>
                    </div>
                    <div>
                  <?php echo $value->body; ?>
                    </div>
                  </div>
              <?php endif; ?> 
            <?php endforeach; ?>
            </div> 
        <?php endif; ?> 
        </div>
        <?php
        include APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/_formReplyReview.tpl';
        ?> 
      </div>
    </li>
  </ul>
<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listNestedComment.tpl';
    ?>

  <div class="clr o_hidden b_medium sm_review_view_footer">  
    <div class="fleft">

    </div>      
    <div class="o_hidden sm_review_view_paging">
        <?php $pre = $this->reviews->getPreviousReview(); ?>
        <?php if ($pre): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink($pre->getHref(), $this->translate('Previous'), array(
              'class' => 'buttonlink icon_previous'
          ));
          ?>
        </div>
      <?php endif; ?>
        <?php $next = $this->reviews->getNextReview(); ?>
        <?php if ($next): ?>
        <div id="user_group_members_previous" class="paginator_next">
          <?php
          echo $this->htmlLink($next->getHref(), $this->translate('Next'), array(
              'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>
<?php endif; ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  var seaocore_content_type = '<?php echo $this->reviews->getType(); ?>';
  en4.core.runonce.add(function() {
<?php if (count($this->ratingDataTopbox) > 1): ?>
      $$('.sm_profile_review_rating').addEvents({
        'mouseover': function(event) {
          document.getElements('.sm_ur_bdown_box_wrapper').setStyle('display', 'block');
        },
        'mouseleave': function(event) {
          document.getElements('.sm_ur_bdown_box_wrapper').setStyle('display', 'none');
        }});
      $$('.sm_ur_bdown_box_wrapper').addEvents({
        'mouseenter': function(event) {
          document.getElements('.sm_ur_bdown_box_wrapper').setStyle('display', 'block');
        },
        'mouseleave': function(event) {
          document.getElements('.sm_ur_bdown_box_wrapper').setStyle('display', 'none');
        }});
<?php endif; ?>
  });
</script>