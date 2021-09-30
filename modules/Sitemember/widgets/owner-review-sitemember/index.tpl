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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js'); ?>

<?php if ($this->loaded_by_ajax): ?>
  <script type="text/javascript">
    var params = {
      requestParams:<?php echo json_encode($this->params) ?>,
      responseContainer: $$('.layout_sitemember_owner_review_sitemember')
    }
    en4.sitemember.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
  </script>
<?php endif; ?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_rating.css');
?>

<?php if ($this->showContent): ?>
  <?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitemember'); ?>
  <?php $reviewDescriptionsTable = Engine_Api::_()->getDbtable('reviewDescriptions', 'sitemember'); ?>
  <div id="owner_review"></div>

  <?php if (!empty($this->totalReviews)): ?>
    <?php if (empty($this->isajax)) : ?>
      <!--User Over All Rating Block End -->
      <!--Filters Starts-->
      <?php if ($this->paginator->getTotalItemCount() > 1): ?>
        <div class="sm_profile_userreview_filters b_medium" <?php if (!$this->sitemember_proscons): ?> style="border:none;"<?php endif; ?>>

          <?php if (($this->paginator->getTotalItemCount() > 1)): ?>
            <div class="sm_profile_userreview_filters_options">
              <label><?php echo $this->translate("Sort By:"); ?></label>
              <select onchange="loadUserReviews('<?php echo $this->reviewOption ?>', this.value, '<?php echo $this->rating_value ?>', '<?php echo $this->current_page; ?>');" name="sortReviews" class="searchTarget"> 
                <option value="creationDate"><?php echo $this->translate("Most Recent"); ?></option>	
                <option value="helpful"><?php echo $this->translate("Most Helpful"); ?></option> 
                <option value="highestRating"><?php echo $this->translate("Highest Rating"); ?></option>  
                <option value="lowestRating"><?php echo $this->translate("Lowest Rating"); ?></option>
                <option value="featured"><?php echo $this->translate("Featured"); ?></option>
                <option value="recommend"><?php echo $this->translate("Recommended"); ?></option>
              </select>
            </div>
            <div class="sm_profile_userreview_filters_options">
              <label><?php echo $this->translate("Rating:"); ?></label>
              <select onchange="loadUserReviews('<?php echo $this->reviewOption ?>', '<?php echo $this->reviewOrder ?>', this.value, '<?php echo $this->current_page; ?>');" name="sortReviewsByRating" class="searchTarget"> 
                <option value=""><?php echo $this->translate(""); ?></option>   
                <option value="5"><?php echo $this->translate("5 Stars"); ?></option>  
                <option value="4"><?php echo $this->translate("4 Stars"); ?></option> 
                <option value="3"><?php echo $this->translate("3 Stars"); ?></option>
                <option value="2"><?php echo $this->translate("2 Stars"); ?></option> 
                <option value="1"><?php echo $this->translate("1 Star"); ?></option>  
              </select>
            </div>
          <?php endif ?>
        </div>
      <?php endif ?>
      <!--Filters Ends-->
    <?php endif; ?>
  <?php endif; ?>

  <div id="sitemember_owner_review_content">

    <?php if (( $this->paginator->getTotalItemCount() > 0)): ?>
      <ul class="sm_reviews_listing">
        <?php foreach ($this->paginator as $review): ?>
          <li class="b_medium">
            <div class='sitemember_browse_list_photo'>
              <?php $user = Engine_Api::_()->getItem('user', $review->resource_id); ?>
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', '', array('align' => 'center'))) ?>
            </div>
            <div class="sm_reviews_listing_title">
              <?php $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitemember')->profileRatingbyCategory($review->review_id); ?>
              <?php $rating_value = 0; ?>
              <?php foreach ($ratingData as $reviewcat): ?>
                <?php if (empty($reviewcat['ratingparam_name'])): ?>
                  <?php
                  $rating_value = $reviewcat['rating'];
                  break;
                  ?>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php $countR = 0; ?>
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 2) : ?>
                <?php $countR = count($ratingData); ?>
              <?php endif; ?>
              <div class="sm_ur_show_rating_star">
                <span class="fright">
                  <span class="fleft">
                    <?php echo $this->showRatingStarMember($rating_value, 'user', 'big-star'); ?>
                  </span>
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
                            <div class="parameter_value" style="margin: 0px 0px 5px;">
                              <?php echo $this->showRatingStarMember($reviewcat['rating'], 'user', 'big-star'); ?>
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
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
            </div>
            <div class="sm_reviews_listing_stat seaocore_txt_light">
              <?php echo $this->htmlLink($review->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($review->title, 60), array('title' => $review->title)) ?> - 
              <?php echo $this->timestamp(strtotime($review->modified_date)); ?>
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && $review->recommend): ?>
                <span class="fright sm_reviews_listing_recommended">
                  <?php echo '<span>' . $this->translate('Recommended') . '</span>'; ?>  
                  <span class='sm_icon_tick sm_icon'></span>
                <?php endif; ?>
            </div>  
          </li>
        <?php endforeach; ?>
        <?php if ($this->paginator->count() > 1): ?>
          <div class="o_hidden mtop10">
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
              <div class="paginator_previous">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                    'onclick' => 'paginateSitemember(sitememberPage - 1)',
                    'class' => 'buttonlink icon_previous'
                ));
                ?>
              </div>
            <?php endif; ?>
            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
              <div class="paginator_next">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                    'onclick' => 'paginateSitemember(sitememberPage + 1)',
                    'class' => 'buttonlink_right icon_next'
                ));
                ?>
              </div>
            <?php endif; ?>
          </div>	
        <?php endif; ?> 
      </ul>
    <?php elseif (!empty($this->rating_value)): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has written a review with that criteria.'); ?> 
        </span>
      </div>    
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("No reviews have been written by this member yet."); ?>	
        </span>
      </div>
    <?php endif; ?>
  </div>

  <script type="text/javascript">
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

    function showReviews(option) {
      document.getElementById('consmeviews').className = "";
      document.getElementById('fullreviews').className = "";
      document.getElementById('prosmeviews').className = "";
      if (option === "prosonly") {
        document.getElementById('prosmeviews').className = "onView";
      } else if (option === "consonly") {
        document.getElementById('consmeviews').className = "onView";
      } else if (option === "fullreviews") {
        document.getElementById('fullreviews').className = "onView";
      }
      loadUserReviews(option, '<?php echo $this->reviewOrder ?>', '<?php echo $this->rating_value ?>', '<?php echo $this->current_page; ?>');
    }

    var sitememberPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
    var paginateSitemember = function(page) {
      loadUserReviews('<?php echo $this->reviewOption ?>', '<?php echo $this->reviewOrder ?>', '<?php echo $this->rating_value ?>', page);
    }

    function loadUserReviews(option, order, rating, page) {
      $('sitemember_owner_review_content').innerHTML = '<div class="seao_smoothbox_lightbox_loading"></div>';
      var url = en4.core.baseUrl + 'widget/index/mod/sitemember/name/owner-review-sitemember';
      en4.core.request.send(new Request.HTML({
        'url': url,
        'data': {
          'format': 'html',
          'subject': en4.core.subject.guid,
          'option': option,
          'rating_value': rating,
          'page': page,
          'order': order,
          'isajax': '1',
          'user_id': '<?php echo $this->user_id; ?>',
          'itemProsConsCount': '<?php echo $this->itemProsConsCount ?>',
          'itemReviewsCount': '<?php echo $this->itemReviewsCount ?>'
        }
      }), {
        'element': $('sitemember_owner_review_content')
      });
    }
  </script>
<?php endif; ?>