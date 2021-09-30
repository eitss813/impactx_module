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
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_rating.css');
?>
<?php if(empty($this->isAjax)) :?>
<h2><?php echo $this->translate("Top Raters"); ?></h2>
<br />
<?php endif;?>
<?php $viewer_id = $this->viewer->getIdentity(); ?>

<?php if ($this->totalResults): ?>
<?php if(empty($this->isAjax)) :?>
  <div id="dynamic_app_info_sitemember">
    <div>
      <ul class="seaocore_browse_list" id="top_raters">
    <?php endif;?>
        <?php foreach ($this->paginator as $sitemember): ?>
          <li class="b_medium">
            <div class='sitemember_browse_list_photo b_medium'>
              <?php $user = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
              <?php $rel = 'user' . ' ' . $user->user_id; ?>
        <?php if($this->circularImage):?>
            <?php
            $url = $user->getPhotoUrl('thumb.profile');
            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
            endif;
            ?>

            <a href="<?php echo $user->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
            <span style="background-image: url(<?php echo $url; ?>);"></span>
            </a>
        <?php else:?>

            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile', '', array('align' => 'center', 'class' => 'sea_add_tooltip_link', 'rel' => "$rel"))); ?>
        <?php endif;?>
            </div>
            <div class='seao_rating_info fleft mleft5'>
              <div class='seaocore_browse_list_info_title'>
                <?php echo $this->htmlLink($user->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($user->getTitle(), $this->title_truncation), array('title' => $user->getTitle())); ?>
                <div class="clear"></div>
              </div>
            </div>
            <?php $url = $this->url(array('user_id' => $sitemember->user_id), 'sitemember_review_ownerreviews', true); ?>    
            <div class='seao_rating_stat_review fleft mleft5'>
              <a href="<?php echo $url; ?>"><?php echo $this->translate(array('%s vote', '%s votes', $sitemember->rating_count), $this->locale()->toNumber($sitemember->rating_count)) ?></a>
            </div>
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
      <?php echo $this->translate("No matching results were found for raters."); ?>
    </span>
  </div>
<?php endif; ?>

<?php  if ($this->paginator->count() > 1 && $this->paginator->count() > $this->page && empty($this->isAjax)): ?>
            <div id="pagination_container">
                <div class="seaocore_view_more" id="topraters_viewmore" style="display: none;">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'id' => 'topraters_viewmore_link',
                        'class' => 'buttonlink icon_viewmore'
                    ))
                    ?>
                </div>

                <div id="topraters_loading" style="display: none;" class="seaocore_view_more">
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="Loading" />
                    <?php echo $this->translate("Loading ...") ?>
                </div>
                <div class="seaocore_view_more" id="topraters_noviewmore" style="display: none;">
                    <?php echo $this->translate('No matching results were found for raters.'); ?>
                </div>
            </div>
        <?php endif; ?>
        
<script type="text/javascript">  

     en4.core.runonce.add(function() {
        <?php if ($this->paginator->count() > 1 && $this->paginator->count() > $this->page): ?>
            if ($('topraters_viewmore')) {
                window.onscroll = doOnScrollLoadTopRaters;
                $('topraters_viewmore').style.display = '';
                //$('feed_viewmore').style.display = 'none';
                $('topraters_loading').style.display = 'none';
p
                $('topraters_viewmore_link').removeEvents('click').addEvent('click', function(event) {
                    event.stop();
                    getNextTopRaters();
                });
            }

        <?php else: ?>
            window.onscroll = '';
            <?php if ($this->page > 1) : ?>
                $('topraters_noviewmore').style.display = 'block';
                $('topraters_loading').style.display = 'none';
                $('topraters_viewmore').style.display = 'none';
            <?php endif; ?>
        <?php endif; ?>
    }); 
    
    var doOnScrollLoadTopRaters = function()
    {
        if ($('topraters_viewmore')) {
            if (typeof($('topraters_viewmore').offsetParent) != 'undefined') {
                var elementPostionY = $('topraters_viewmore').offsetTop;
            } else {
                var elementPostionY = $('topraters_viewmore').y;
            }
            if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
                getNextTopRaters();
            }
        }
    }
</script>

<script type="text/javascript">  
    
   function getNextTopRaters() {
   
       if($('topraters_noviewmore')) {
        $('topraters_noviewmore').style.display = 'none';
      }
      if($('topraters_loading')) {
        $('topraters_loading').style.display = 'block';
      }      
        
     if($('topraters_viewmore')) {
        $('topraters_viewmore').style.display = 'none';
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
                Elements.from(responseHTML).inject($('top_raters'));
                en4.core.runonce.trigger();
            }
      }));
  } 
    
</script>  