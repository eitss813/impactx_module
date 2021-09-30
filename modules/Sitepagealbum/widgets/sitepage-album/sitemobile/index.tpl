<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->paginator->getTotalItemCount()): ?>
<?php if (!$this->autoContentLoad) : ?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagealbum_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
<?php endif; ?>
  
<?php if (!$this->autoContentLoad) : ?>
    <div class="album-listing">
      <ul id="browsesitepagealbum_ul">
        <?php endif; ?>
        <?php foreach ($this->paginator as $albums): ?>
          <li id="thumbs-photo-<?php echo $albums->photo_id ?>">
            <a class="listing-btn" href="<?php echo $albums->getHref(array('page_id' => $albums->page_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug())); ?>">
              
              <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/images/photo_thumb.png'; $temp_url=$albums->getPhotoUrl('thumb.main'); if(!empty($temp_url)): $url=$albums->getPhotoUrl('thumb.main'); endif;?>
              <span class="listing-thumb" style="background-image: url(<?php echo $url; ?>);"> </span>
              
              <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
              <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count()) ?></p>
            </a>             	
            <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $albums->page_id); ?>
            <p class="list-owner">
              <?php echo $this->translate("in ") ?>
              <?php echo $this->htmlLink($sitepage_object->getHref(), $sitepage_object->getTitle()) ?>
            </p>
          <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>  
            <?php if($albums->likes()->getLikeCount() > 0 || $albums->comment_count > 0) : ?>
              <a class="listing-stats ui-link-inherit" onclick='sm4.core.comments.comments_likes_popup("<?php echo $albums->getType();?>", <?php echo $albums->getIdentity();?>, "<?php echo $this->url(array('module' => 'core', 'controller' => 'photo-comment', 'action' => 'list'), 'default', 'true'); ?>")'>
                <?php if($albums->likes()->getLikeCount() > 0) : ?> 
                  <span class="f_small"><?php echo $this->locale()->toNumber($albums->likes()->getLikeCount()); ?></span>
                  <i class="ui-icon-thumbs-up-alt"></i>
              <?php endif;?>
              <?php if($albums->comment_count > 0) : ?>
                  <span class="f_small"><?php echo $this->locale()->toNumber($albums->comment_count) ?></span>
                  <i class="ui-icon-comment"></i>
              <?php endif;?>
              </a>
            <?php endif;?>
          <?php endif;?>
        </li>		      
      <?php endforeach; ?>
        <?php if (!$this->autoContentLoad) : ?>
    </ul>
</div>
<?php endif;?>
<?php if( $this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
    sm4.core.runonce.add(function() {
      var ul_id = 'browsesitepagealbum_ul';

      //Autoscrolling 
      var browseEventWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitepagealbum/name/sitepage-album';
      var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
      sm4.core.Module.core.activeParams[activepage_id] = {'currentPage': '<?php echo sprintf('%d', $this->page) ?>', 'totalPages': '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues': <?php echo json_encode($this->formValues); ?>, 'contentUrl': browseEventWidgetUrl, 'activeRequest': false, 'container': ul_id};
    });
<?php } ?>
</script>