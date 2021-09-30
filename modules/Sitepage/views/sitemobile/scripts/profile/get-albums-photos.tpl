<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-albums-photos.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sm-ui-content sm-choose-cover-photo">
  <div class="top">
    <?php if ($this->album_id || $this->recentAdded): ?>
      <div class="view-album">
        <span class="ui-icon ui-icon-picture"></span>  
        <a class="ui-link-inhirit" href="<?php echo $this->url(array('action' => 'get-albums-photos', 'page_id' => $this->sitepage->page_id, 'special' => 'cover'), 'sitepage_profilepagemobile', true); ?>"><b><?php echo $this->translate("View Albums") ?></b></a>
      </div> 
    <?php endif; ?>
    <h3><?php echo $this->translate("Choose a Cover") ?></h3>
  </div>
  <div class="sm-content-list ui-listgrid-view ui-listgrid-view-no-caption">
    <?php if ($this->album_id || $this->recentAdded): ?>
      <?php if ($this->paginator && $this->paginator->getTotalItemCount() > 0) : ?>
        <ul class="thumbs thumbs_nocaptions">
          <?php foreach ($this->paginator as $photo): ?>
            <li> 
              <a data-ajax="false" href="<?php echo $this->url(array('action' => 'upload-cover-photo', 'page_id' => $photo->page_id, 'photo_id' => $photo->photo_id, 'special' => 'cover'), 'sitepage_profilepagemobile', true); ?>" title="<?php echo $photo->title; ?>">
                <img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" style="height:80px;width:80px;"/>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="tip" style="margin-top:10px;">
          <span>
            <?php echo $this->translate("There are currently no photos available.") ?>
          </span>
        </div>
      <?php endif; ?>
    <?php else: ?> 
      <?php if ($this->paginator && $this->paginator->getTotalItemCount() > 0) : ?>
        <ul data-role="listview" data-icon="arrow-r">
          <?php foreach ($this->paginator as $albums): ?>
            <?php if ($albums->count() < 1): continue;
            endif; ?>
            <li class="sm-ui-browse-items"> 
              <?php if ($albums->photo_id != 0): ?>
                <a data-ajax="false" href="<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id, 'slug' => $albums->getSlug(), 'tab' => $this->identity_temp, 'special' => 'cover'), 'sitepage_profilepagemobile') ?>">
                  <p class="ui-li-aside-show ui-li-aside" style="display:none;"><?php echo $this->locale()->toNumber($albums->count())?></p>
                  <img src="<?php echo $albums->getPhotoUrl('thumb.icon'); ?>" />
                  <div class="ui-list-content">
                    <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
                  </div>
                  <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count())?></p>
                </a> 
              <?php else: ?>
                <a data-ajax="false" href="<?php echo $this->url(array('action' => 'get-albums-photos', 'page_id' => $this->sitepage->page_id, 'album_id' => $albums->album_id, 'special' => 'cover'), 'sitepage_profilepagemobile', true); ?>">
                  <p class="ui-li-aside-show ui-li-aside" style="display:none;"><?php echo $this->locale()->toNumber($albums->count())?></p>
                  <img src="<?php echo $albums->getPhotoUrl('thumb.icon'); ?>" />
                  <div class="ui-list-content">
                    <h3><?php echo $this->string()->chunk($this->string()->truncate($albums->getTitle(), 45), 10); ?></h3>
                  </div>
                  <p class="ui-li-aside"><?php echo $this->locale()->toNumber($albums->count())?></p>
                </a> 
              <?php endif; ?>
            </li>		      
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("There are currently no albums available.") ?>
          </span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>