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
$reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitemember');
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>
<?php if(empty($this->isAjax)) :?>
<h2><?php echo $this->translate("Most Reviewed Members"); ?></h2>
<br />
<?php endif;?>
<?php $viewer_id = $this->viewer->getIdentity(); ?>

<?php if ($this->totalResults): ?>
<?php if(empty($this->isAjax)) :?>
  <div id="dynamic_app_info_sitemember">
    <div>
      <ul class="seaocore_browse_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" id ="top_reviewed">
          <?php endif;?>
        <?php foreach ($this->paginator as $sitemember): ?>
          <li class="b_medium" >
            <div class='sitemember_browse_list_photo b_medium'>
                              <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>
                <?php if($this->circularImage):?>
                    <?php
                    $url = $sitemember->getPhotoUrl('thumb.profile');
                    if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                    endif;
                    ?>

                    <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                    <span style="background-image: url(<?php echo $url; ?>);"></span>
                    </a>
                <?php else:?>

                <?php echo $this->htmlLink($sitemember->getHref(), $this->itemPhoto($sitemember, 'thumb.profile', '', array('align' => 'center', 'class' => 'sea_add_tooltip_link', 'rel' => "$rel"))); ?>
            <?php endif;?>
            </div>
            <div class='seao_rating_breakdown fright'>
              <div class="sm_rating_breakdowns">
                <ul>
                  <?php
                  $ratingCount = array();
                  for ($i = 5; $i > 0; $i--) {
                    $ratingCount[$i] = Engine_Api::_()->getDbtable('ratings', 'sitemember')->getNumbersOfUserRating($sitemember->user_id, 'user', 0, $i, $this->viewer->getIdentity(), 'user');
                  }
                  ?>
                  <?php foreach ($ratingCount as $i => $count): ?>
                    <li>
                      <div class="left"><?php echo $this->translate(array("%s star&nbsp;:", "%s stars&nbsp;:", $i), $i); ?></div>
                      <?php $pr = $count > 0 ? ($count * 100 / $sitemember->review_count) : 0; ?>
                      <div class="count"><?php echo $count; ?></div>
                      <div class="rate_bar b_medium">
                        <span style="width:<?php echo $pr; ?>%;" <?php echo empty($count) ? "class='sm_border_none'" : "" ?>></span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <div class='seao_rating_info fleft mleft5'>
              <div class='seaocore_browse_list_info_title'>
                <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle())); ?>
                <?php echo $this->showRatingStarMember($sitemember->rating_avg, "user", 'big-star'); ?>
                <div class="clear"></div>
              </div>
            </div>
            <?php $url = $this->url(array('user_id' => $sitemember->user_id), 'sitemember_review_memberreviews', true); ?> 
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?> 
              <div class='seao_rating_stat_review fleft mleft5'>
                <a href="<?php echo $url; ?>"><?php echo $this->translate(array('%s review', '%s reviews', $sitemember->review_count), $this->locale()->toNumber($sitemember->review_count)) ?></a>
              </div>   
            <?php elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0): ?>
              <div class='seao_rating_stat_review fleft mleft5'>
                <a href="<?php echo $url; ?>"><?php echo $this->translate(array('%s vote', '%s votes', $sitemember->review_count), $this->locale()->toNumber($sitemember->review_count)) ?></a>
              </div>
            <?php endif; ?>

            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
              <?php
              $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user', 'resource_id' => $sitemember->user_id));
              $totalRecommend = $recommendpaginator->getTotalItemCount();
              ?>
              <div class='seao_rating_stat_recommend fleft mleft5'>
                <?php $url = $this->url(array('user_id' => $sitemember->user_id, 'order' => 'recommend'), 'sitemember_review_memberreviews', true); ?>
                <a href="<?php echo $url; ?>"><?php echo $this->translate(array('%s recommendation', '%s recommendations', $totalRecommend), $this->locale()->toNumber($totalRecommend)) ?></a>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
        <?php if(empty($this->isAjax)) :?>
      </ul>
    </div>
  </div>
<?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No matching results were found for members."); ?>
    </span>
  </div>
<?php endif; ?>

<?php  if ($this->paginator->count() > 1 && $this->paginator->count() > $this->page && empty($this->isAjax)): ?>
            <div id="pagination_container">
                <div class="seaocore_view_more" id="topreviewed_viewmore" style="display: none;">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'id' => 'topreviewed_viewmore_link',
                        'class' => 'buttonlink icon_viewmore'
                    ))
                    ?>
                </div>

                <div id="topreviewed_loading" style="display: none;" class="seaocore_view_more">
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="Loading" />
                    <?php echo $this->translate("Loading ...") ?>
                </div>
                <div class="seaocore_view_more" id="topreviewed_noviewmore" style="display: none;">
                    <?php echo $this->translate('No matching results were found for members.'); ?>
                </div>
            </div>
        <?php endif; ?>
        
<script type="text/javascript">  

     en4.core.runonce.add(function() {
        <?php if ($this->paginator->count() > 1 && $this->paginator->count() > $this->page): ?>
            if ($('topreviewed_viewmore')) {
                window.onscroll = doOnScrollLoadTopRaters;
                $('topreviewed_viewmore').style.display = '';
                //$('feed_viewmore').style.display = 'none';
                $('topreviewed_loading').style.display = 'none';
p
                $('topreviewed_viewmore_link').removeEvents('click').addEvent('click', function(event) {
                    event.stop();
                    getNextTopReviewed();
                });
            }

        <?php else: ?>
            window.onscroll = '';
            <?php if ($this->page > 1) : ?>
                $('topreviewed_noviewmore').style.display = 'block';
                $('topreviewed_loading').style.display = 'none';
                $('topreviewed_viewmore').style.display = 'none';
            <?php endif; ?>
        <?php endif; ?>
    }); 
    
    var doOnScrollLoadTopRaters = function()
    {
        if ($('topreviewed_viewmore')) {
            if (typeof($('topreviewed_viewmore').offsetParent) != 'undefined') {
                var elementPostionY = $('topreviewed_viewmore').offsetTop;
            } else {
                var elementPostionY = $('topreviewed_viewmore').y;
            }
            if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
                getNextTopReviewed();
            }
        }
    }
</script>

<script type="text/javascript">  
    
   function getNextTopReviewed() {
  
   if($('topreviewed_noviewmore')) {
        $('topreviewed_noviewmore').style.display = 'none';
      }
      if($('topreviewed_loading')) {
        $('topreviewed_loading').style.display = 'block';
      }      
        
     if($('topreviewed_viewmore')) {
        $('topreviewed_viewmore').style.display = 'none';
      }
      
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
          isAjax : 1,
          values : <?php echo  Zend_Json_Encoder::encode($this->values);?>
        },
         onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                Elements.from(responseHTML).inject($('top_reviewed'));
                en4.core.runonce.trigger();
            }
      }));
  } 
    
</script>