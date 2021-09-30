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

<?php $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitemember'); ?>
<ul class="sm_up_overall_rating b_medium seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
  <li class="sm_up_overall_rating_title o_hidden">
    <div class="fright"><?php echo $this->showRatingStarMember($this->rating_avg, 'overall', 'big-star'); ?></div>
    <div class="o_hidden"><?php echo $this->translate('Overall Rating') ?></div>
  </li>

  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
    <li class="sm_up_overall_rating_stat clr">
      <?php echo $this->translate(array('Based on %s review', 'Based on %s reviews', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)); ?>
    </li>
  <?php elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0): ?>
    <li class="sm_up_overall_rating_stat clr">
      <?php echo $this->translate(array('Based on %s vote', 'Based on %s votes', $this->totalReviews), $this->locale()->toNumber($this->totalReviews)); ?>
    </li>
  <?php endif; ?>

  <?php if (count($this->ratingData) > 1 && $this->ratingParameter): ?>
    <li class="sm_up_overall_rating_paramerers clr">
      <?php foreach ($this->ratingData as $reviewcat): ?>
        <?php if (!empty($reviewcat['ratingparam_id'])): ?>
          <div class="o_hidden">
            <div class="parameter_value">
              <?php echo $this->showRatingStarMember($reviewcat['avg_rating'], 'user', 'small-box', $reviewcat['ratingparam_name']); ?>
            </div>
            <div class="parameter_title">
              <b><?php echo $this->translate($reviewcat['ratingparam_name']); ?></b>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </li>
  <?php endif; ?>

  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
    <?php
    $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user', 'resource_id' => $this->user_id));
    $totalRecommend = $recommendpaginator->getTotalItemCount();
    ?>
    <li class="sm_up_overall_rating_title mtop10">
      <?php echo $this->translate("Recommendations") ?>
    </li>
    <li class="sm_up_overall_rating_stat">
      <?php echo $this->translate(array('Recommended by %1$s member (%2$s)', 'Recommended by %1$s members (%2$s)', $totalRecommend, '<b>' . $this->recommend_percentage . '%</b>'), $totalRecommend, '<b>' . $this->recommend_percentage . '%</b>'); ?>
    </li>
  <?php endif; ?> 


  <li>
    <?php if ($this->totalReviews == 1): ?>
      <?php $more_link = $this->totalReviews . $this->translate(' Review'); ?>
    <?php else: ?>
      <?php $more_link = $this->translate('All ') . $this->totalReviews . $this->translate(' Reviews'); ?>
    <?php endif; ?>
    <a class="sm_up_overall_rating_more_link" href="javascript:void(0);" onclick='showMemberReviewTab();
        return false;'><?php echo $more_link; ?> &nbsp;&raquo;</a>
  </li>
</ul>

<script type="text/javascript">

      function showMemberReviewTab() {

        if ($('main_tabs')) {
          tabContainerSwitch($('main_tabs').getElement('.tab_' + '<?php echo $this->contentDetails->content_id ?>'));
        }

        var params = {
          requestParams:<?php echo json_encode($this->contentDetails->params) ?>,
          responseContainer: $$('.layout_sitemember_user_review_sitemember')
        }

        params.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
        en4.sitemember.ajaxTab.sendReq(params);

        if ($('main_tabs')) {
          location.hash = 'main_tabs';
        }
      }

</script>   