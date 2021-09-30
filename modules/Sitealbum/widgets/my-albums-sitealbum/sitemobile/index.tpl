<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
<?php if(!$this->autoContentLoad) : ?>
  <div class="sm-content-list ui-list-manage-page">
    <ul data-role="listview" data-inset="false" id='managealbum_ul'>
<?php endif;?>      
      <?php foreach ($this->paginator as $album): ?>
        <li <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> data-icon="ellipsis-vertical" <?php else : ?> data-icon="cog" <?php endif?> data-inset="true">
          <a href="<?php echo $album->getHref() ?>">
            <?php echo $this->itemPhoto($album, 'thumb.icon') ?>
            <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
              <h3><?php echo $album->getTitle() ?></h3>
              <p class="t_light"><?php echo $this->translate(array('%s Photo', '%s Photos', $album->count()), $this->locale()->toNumber($album->count())); ?></p>
            <?php else : ?>
              <div class="ui-list-content">
                <h3><?php echo $album->getTitle() ?></h3>
              </div>
              <p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count()) ?></p>
            <?php endif ?>
          </a>
          <a href="#manage_<?php echo $album->getGuid() ?>" data-rel="popup" data-transition="pop"></a>
          <div data-role="popup" id="manage_<?php echo $album->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                <h3><?php echo $album->getTitle() ?></h3>
              <?php endif ?>
               <?php if($this->canCreate): ?>
                <a href="<?php echo $this->url(array('action' => 'upload', 'album_id' => $album->album_id), 'album_general', 'true'); ?>" class="ui-btn-default ui-btn-action">
                <?php echo $this->translate('Add Photos'); ?>
              </a>
               <?php endif;?> 
              <a href="<?php echo $this->url(array('action' => 'edit', 'album_id' => $album->album_id), 'album_specific', 'true'); ?>" class="ui-btn-default ui-btn-action">
                <?php echo $this->translate('Edit Settings'); ?>
              </a>
              <a href="<?php echo $this->url(array('action' => 'delete', 'album_id' => $album->album_id, 'format' => 'smoothbox'), 'album_specific', 'true'); ?>"  class="smoothbox ui-btn-default ui-btn-danger">
                <?php echo $this->translate('Delete Album'); ?>
              </a>
             <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>   
                <a href="#" data-rel="back" class="ui-btn-default">
                  <?php echo $this->translate('Cancel'); ?>
                </a>
             <?php endif ?>
            </div> 
          </div>
        </li>
      <?php endforeach; ?>
    <?php if(!$this->autoContentLoad) : ?>
    </ul>
    <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php echo $this->paginationControl($this->paginator, null, null); ?>
    <?php endif; ?>
  </div>
<?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any albums yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a href="' . $this->url(array('action' => 'upload')) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
        var mySiteAlbumWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/Sitealbum/name/my-albums-sitealbum'; 
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : null, 'contentUrl' : mySiteAlbumWidgetUrl, 'activeRequest' : false, 'container' : 'managealbum_ul' };  
          });
   <?php } ?>    
</script>