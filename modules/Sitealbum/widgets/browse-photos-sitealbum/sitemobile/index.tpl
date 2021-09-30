<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if ($this->is_ajax_load): ?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <?php if(!$this->autoContentLoad) : ?>
    <div class="album-listing">
      <ul id='browsesitealbums_ul'>
  <?php endif;?>
      <?php foreach ($this->paginator as $album): ?>
        <li>
          <a href="<?php echo $album->getHref(); ?>" class="listing-btn">
            <?php //echo $this->itemPhoto($album, 'thumb.icon'); ?>
            <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png'; $temp_url=$album->getPhotoUrl('thumb.main'); ?>
            <span class="listing-thumb lazy" style="background-image: url(<?php echo $url; ?>);" data-src="<?php echo $temp_url; ?>" data-src-mobile="<?php echo $album->getPhotoUrl('thumb.mobile'); ?>" data-src-mobile-wide="<?php echo $album->getPhotoUrl('thumb.mobile-wid'); ?>" data-src-tablet="<?php echo $album->getPhotoUrl('thumb.mobile-wid'); ?>" > </span>
            <h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
            <p class="ui-li-aside"><?php echo $this->locale()->toNumber(isset($album->photos_count) ? $album->photos_count : $album->count()); ?></p>
          </a>
          <p class="list-owner">
            <?php echo $this->translate('by'); ?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle()) ?>
          </p>
          <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
            <?php if($album->likes()->getLikeCount() > 0 || $album->comment_count > 0) : ?>
              <a class="listing-stats ui-link-inherit" onclick='sm4.core.comments.comments_likes_popup("<?php echo $album->getType();?>", <?php echo $album->getIdentity();?>, "<?php echo $this->url(array('module' => 'core', 'controller' => 'photo-comment', 'action' => 'list'), 'default', 'true'); ?>")'>
                <?php if($album->likes()->getLikeCount() > 0) : ?> 
                  <span class="f_small"><?php echo $this->locale()->toNumber($album->likes()->getLikeCount()); ?></span>
                  <i class="ui-icon-thumbs-up-alt"></i>
              <?php endif;?>
              <?php if($album->comment_count > 0) : ?>
                  <span class="f_small"><?php echo $this->locale()->toNumber($album->comment_count) ?></span>
                  <i class="ui-icon-comment"></i>
              <?php endif;?>
              </a>
            <?php endif;?>
          <?php endif;?>
        </li>
      <?php endforeach; ?>
  <?php if(!$this->autoContentLoad) : ?>
    </ul>
  </div>
  <?php endif ?>
  <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php
    echo $this->paginationControl(
            $this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues
    ));
    ?>
  <?php endif; ?>
<?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an album with that criteria.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'upload'),'sitealbum_general', true) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>    
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an album yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
        var browseSiteAlbumWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/Sitealbum/name/browse-albums-sitealbum';  
        sm4.core.runonce.add(function() { 
         
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : browseSiteAlbumWidgetUrl, 'activeRequest' : false, 'container' : 'browsesitealbums_ul' }; 
          });
         
   <?php } ?>    
</script>
<?php else: ?>
    <div id="layout_sitealbum_browse_albums_sitealbum_<?php echo $this->identity; ?>">
    </div>
<script type="text/javascript">
    var requestParams = $.extend(<?php echo json_encode($this->params);?>, {'content_id': '<?php echo $this->identity;?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer' : 'layout_sitealbum_browse_albums_sitealbum_<?php echo $this->identity;?>',
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