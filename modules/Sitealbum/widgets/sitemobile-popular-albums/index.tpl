



<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->is_ajax_load): ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>

<?php if (!$this->viewmore) : ?>
  <div id="main_layout" class="ui-page-content">
  <?php endif; ?>  
  <?php if ($this->is_ajax_load): ?>
    <?php $isLarge = ($this->columnWidth > 170); ?>
    <?php if (!$this->viewmore): ?>
      <div class="album-listing">
      <ul>
        <?php endif; ?>             
        <?php foreach ($this->sitealbums as $album):?>
          <li>
            <a href="<?php echo $album->getHref(); ?>" class="listing-btn">
              <?php //echo $this->itemPhoto($album, 'thumb.icon'); ?>
              <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png';
              $temp_url = $album->getPhotoUrl('thumb.main'); ?>
              <span class="listing-thumb lazy" style="background-image: url(<?php echo $url; ?>);" data-src="<?php echo $temp_url; ?>" data-src-mobile="<?php echo $album->getPhotoUrl('thumb.mobile'); ?>" data-src-mobile-wide="<?php echo $album->getPhotoUrl('thumb.mobile-wid'); ?>" data-src-tablet="<?php echo $album->getPhotoUrl('thumb.mobile-wid'); ?>" > </span>

              <?php if (!empty($this->albumInfo)) : ?>
                <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                  <h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
                <?php endif; ?>
                <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                  <p class="ui-li-aside"><?php echo $this->locale()->toNumber(isset($album->photos_count) ? $album->photos_count : $album->count()); ?></p>
              <?php endif; ?>
              </a>
                <?php if (in_array('ownerName', $this->albumInfo)): ?>
                <p class="list-owner">
                  <?php echo $this->translate('by'); ?>
                <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle()) ?>
                </p>
              <?php endif; ?>
              <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                  <?php if ($album->likes()->getLikeCount() > 0 || $album->comment_count > 0) : ?>
                  <a class="listing-stats ui-link-inherit" onclick='sm4.core.comments.comments_likes_popup("<?php echo $album->getType(); ?>", <?php echo $album->getIdentity(); ?>, "<?php echo $this->url(array('module' => 'core', 'controller' => 'photo-comment', 'action' => 'list'), 'default', 'true'); ?>")'>
          <?php if ($album->likes()->getLikeCount() > 0) : ?> 
                      <span class="f_small"><?php echo $this->locale()->toNumber($album->likes()->getLikeCount()); ?></span>
                      <i class="ui-icon-thumbs-up-alt"></i>
                    <?php endif; ?>
          <?php if ($album->comment_count > 0) : ?>
                      <span class="f_small"><?php echo $this->locale()->toNumber($album->comment_count) ?></span>
                      <i class="ui-icon-comment"></i>
                  <?php endif; ?>
                  </a>
                <?php endif; ?>
                <?php endif; ?>
              <p>
              <?php echo $this->albumInfo($album, $this->albumInfo, array('truncation' => $this->truncation)); ?>
              </p>
          <?php endif; ?>
          </li>
        <?php endforeach; ?>
  <?php if (!$this->viewmore): ?> 
        </ul>
      </div>
    <?php endif; ?>
<?php else: ?>
    <div id="layout_sitealbum_sitemobile_popular_albums_<?php echo $this->identity; ?>">
    </div>
  <?php endif; ?>
    <?php if ($this->params['page'] < 2 && $this->totalCount > ($this->params['page'] * $this->params['limit'])) : ?>
    <div class="feed_viewmore clr" style="margin-bottom: 5px;">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
       'id' => 'feed_viewmore_link',
       'class' => 'ui-btn-default icon_viewmore',
       'onclick' => 'sm4.switchView.viewMoreEntity(' . $this->identity . ',widgetUrl)'
      ))
      ?>
    </div>
    <div class="seaocore_loading feeds_loading" style="display: none;">
      <i class="icon_loading"></i>
    </div>
<?php endif; ?> 
  <script type="text/javascript">
    var widgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitealbum/name/sitemobile-popular-albums';
    sm4.core.runonce.add(function() {
      var currentpageid = $.mobile.activePage.attr('id') + '-' + <?php echo $this->identity; ?>;
      sm4.switchView.pageInfo[currentpageid] = $.extend({}, sm4.switchView.pageInfo[currentpageid], {'viewType': '<?php echo $this->viewType; ?>', 'params': <?php echo json_encode($this->params) ?>, 'totalCount': <?php echo $this->totalCount; ?>});

    });
  </script>


<?php if (!$this->viewmore) : ?>
  </div>
  <style type="text/css">
    .ui-collapsible-content{padding-bottom:0;}
  </style>
<?php endif; ?>
<?php else: ?>
  <div id="layout_sitealbum_sitemobile_popular_albums_<?php echo $this->identity; ?>">
  </div>
<script type="text/javascript">
  var requestParams = $.extend(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
  var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitealbum_sitemobile_popular_albums_<?php echo $this->identity; ?>',
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
      requestParams: requestParams
  };
  sm4.core.runonce.add(function() {
    setTimeout((function() {
      $.mobile.loading().loader("show");
    }), 100);

    sm4.core.locationBased.startReq(params);
  });
</script>
<?php endif; ?>