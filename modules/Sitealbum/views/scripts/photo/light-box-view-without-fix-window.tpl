<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: light-box-view-without-fix-window.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $showCursor = 0; ?>
<?php if (!empty($this->viewer_id) && (!empty($this->canRate))): ?>
  <?php $showCursor = 1; ?>
<?php endif; ?>

<style type="text/css">
<?php if ($showCursor == 0) { ?>
    .photo_lightbox_white_content .seao_rating_star_generic{
      cursor: default;
    }
<?php } ?>
</style>

<script type="text/javascript">

  var photoLightbox = 1;

</script>

<div>
  <div class="photo_lightbox_options">
    <a onclick = "closeLightBoxAlbum();" class="close" title="<?php echo $this->translate('Close'); ?>" ></a>
  </div>
  <?php if (empty($this->isajax)): ?>
    <div id="ads_hidden" style="display: none;" >
      <?php echo $this->content()->renderWidget("seaocore.lightbox-ads", array('limit' => 1)) ?>
    </div>
    <div id='image_div_album'>
    <?php endif; ?>
    <?php
    $showLink = false;
    if (isset($this->params['type']) && !empty($this->params['type'])):
      if ($this->type_count > 1):
        $showLink = true;
      endif;
    elseif ($this->album->photos_count > 1):
      $showLink = true;
    endif;
    ?>
    <?php if ($showLink): ?>
      <div class="photo_lightbox_options" id="sitealbum_photo_scroll">
        <a onclick="photopaginationSitealbum('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->prevPhoto, array_merge($this->params, array('offset' => $this->PrevOffset)))) ?>', 1)" class="pre" title="<?php echo $this->translate('Previous'); ?>" ></a>
        <a onclick="photopaginationSitealbum('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>', 1)" class="nxt" title="<?php echo $this->translate('Next'); ?>" ></a>
      </div>
    <?php endif; ?>
    <div class="photo_lightbox_photo_detail sitealbum_lightbox_photo_detail" id="photo_lightbox_photo_detail">
      <?php if (isset($this->params['type']) && !empty($this->params['type'])): ?>    
        <b><?php echo $this->translate(ucfirst($this->displayTitle)); ?></b>        
        <br />
        <?php echo $this->translate('%1$s By %2$s', $this->htmlLink($this->album, $this->album->getTitle()), $this->album->getOwner()->__toString()); ?>
      <?php else: ?>
        <?php echo $this->translate('%1$s By %2$s', $this->htmlLink($this->album, $this->album->getTitle()), $this->album->getOwner()->__toString()); ?>
        |
        <?php
        echo $this->translate('Photo %1$s of %2$s', $this->locale()->toNumber($this->getPhotoIndex + 1), $this->locale()->toNumber($this->album->photos_count))
        ?>
      <?php endif; ?>
    </div>  
    <div class="photo_lightbox_image_content sitealbum_lightbox_image_content">
      <div id='media_image_div_sitealbum' class="photo_lightbox_image_content_media">
        <?php if ($this->viewPermission): ?>      
          <a id='media_photo_next' <?php if ($showLink): ?> onclick="photopaginationSitealbum('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>', 1)" <?php endif; ?> title="<?php echo $this->photo->getTitle() ?>">
             <?php
             echo $this->itemPhoto($this->photo, 'thumb.normal', $this->photo->getTitle(), array(
                 'id' => 'media_photo',
                 'class' => "lightbox_photo",
             ));
             ?>  
          </a>      
        <?php else: ?>
         <?php if ($this->albumPasswordProtected): ?>
             <?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_lightboxPasswordProtection.tpl'; ?>
          <?php else:?>
             <div class="tip">
              <span><?php echo $this->translate('You do not have the permission to view this photo.'); ?> </span>
            </div>
          <?php endif;?>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($this->viewPermission): ?>
      <?php
      $viewer_id = $this->viewer()->getIdentity();
      if ($this->canComment):
        ?>
        <div class="photo_lightbox_user_options sitealbum_lightbox_user_options" id="photo_lightbox_user_options">
          <a id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>like_link" <?php if ($this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> onclick="en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '1');" href="javascript:void(0);" class="photo_lightbox_like" title="<?php echo $this->translate('Like This'); ?>"><?php echo $this->translate('Like'); ?></a>
          <a id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>unlike_link" <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?> style="display:none;" <?php endif; ?> onclick="en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '1');" href="javascript:void(0);" class="photo_lightbox_unlike" title="<?php echo $this->translate('Unlike This'); ?>"><?php echo $this->translate('Unlike'); ?></a>
          <a href="javascript:void(0);" onclick=" if ($('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>')) {
          $('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>').style.display = 'none';
        }
        $('comments-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>_0').style.display = '';
        $('comments-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>_0').body.parentNode.children[0].focus();" class="photo_lightbox_comment" title="<?php echo $this->translate('Post Comment'); ?>" ><?php echo $this->translate('Comments'); ?></a>
        </div>
      <?php endif; ?>
      <?php if ($this->canEdit): ?>
        <div class="photo_lightbox_user_right_options sitealbum_lightbox_user_right_options" id="photo_lightbox_user_right_options">
          <a class="icon_photos_lightbox_rotate_ccw"  onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.rotate(<?php echo $this->photo->getIdentity() ?>, 90, '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>').addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_rotate_ccw')
        }.bind(this));
        loadingImageSitealbum();" title="<?php echo $this->translate("Rotate Left"); ?>" ></a>
          <a class="icon_photos_lightbox_rotate_cw" onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.rotate(<?php echo $this->photo->getIdentity() ?>, 270, '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>').addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_rotate_cw')
        }.bind(this));
        loadingImageSitealbum();" title="<?php echo $this->translate("Rotate Right"); ?>" ></a>
          <a class="icon_photos_lightbox_flip_horizontal" onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>').addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_flip_horizontal')
        }.bind(this));
        loadingImageSitealbum();" title="<?php echo $this->translate("Flip Horizontal"); ?>" ></a>
          <a class="icon_photos_lightbox_flip_vertical"  onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>').addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_flip_vertical')
        }.bind(this));
        loadingImageSitealbum();" title="<?php echo $this->translate("Flip Vertical"); ?>"></a>
        </div>
      <?php endif ?>
      <?php if ($this->canMakeFeatured && !$this->allowView): ?>
        <div class="tip">
          <span>
            <?php echo $this->translate("SITEALBUM_PHOTO_VIEW_PRIVACY_MESSAGE"); ?>
          </span>
        </div>
      <?php endif; ?>
      <difv class="photo_lightbox_content" > 
        <div id="photo_lightbox_text">
          <div class="photo_lightbox_content_left">
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)): ?>
              <div class="photo_detail">
                <?php if ($this->canEdit || !empty($this->photo->title)): ?>
                  <div class="photo_lightbox_photo_description widthfull" id="link_seaocore_title" style="display:block;">
                    <?php if ($this->canEdit): ?>
                      <span class="lightbox_photo_description_edit_icon">
                        <a href="javascript:void(0);" onclick="showeditPhotoTitleSA()" title=" <?php echo $this->translate('Edit this title'); ?> "></a>
                      </span>
                    <?php endif; ?>
                    <span id="seaocore_title" class="lightbox_photo_description">
                      <?php if (!empty($this->photo->title)): ?>
                        <?php echo $this->photo->getTitle() ?>
                      <?php elseif ($this->canEdit): ?>
                        <a href="javascript:void(0);" onclick="showeditPhotoTitleSA()" >  <?php echo $this->translate('Add a title'); ?> </a>
                      <?php endif; ?>
                    </span>
                  </div>
                <?php endif; ?>
                <div class="photo_lightbox_photo_description"  >
                  <div id="edit_seaocore_title" style="display: none;">

                    <input type="text"  name="edit_title" id="editor_seaocore_title" title="<?php echo $this->translate('Add a title'); ?>" value="<?php echo $this->photo->title; ?>"/>
                    <div>
                      <button name="save" onclick="saveeditPhotoTitleSA('<?php echo $this->photo->getIdentity(); ?>', '<?php echo $this->resource_type; ?>')"><?php echo $this->translate('Save'); ?></button>
                      <button name="cancel" onclick="showeditPhotoTitleSA();"><?php echo $this->translate('Cancel'); ?></button>
                    </div>
                  </div>
                  <div id="seaocore_title_loading" style="display: none;" >
                    <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/loader.gif' /></center>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <div class="photo_detail">
              <span class="owner">
                <?php
                echo $this->translate('by %1$s', $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle()));
                ?>
                | <?php echo $this->timestamp($this->photo->modified_date) ?>
              </span>
            </div>
            <div class="photo_options">
              <?php if ($this->canTag): ?>
                <a href='javascript:void(0);' onclick='taggerInstanceSitealbum.begin();'><?php echo $this->translate('Tag This Photo'); ?></a>
              <?php endif; ?>

              <?php if(!$this->photo->location) :?>
                <?php if (SEA_PHOTOLIGHTBOX_EDITLOCATION && $this->canEdit && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)): ?>
                <div class="sitealbum_lightbox_editlocation"><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'edit-location', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');return false;"> 
                   <?php echo $this->translate("Edit Location") ?></a>
                </div>
                <?php endif; ?>
             <?php else:?>
             
             <?php if (SEA_PHOTOLIGHTBOX_EDITLOCATION && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)): ?>
                <div class="sitealbum_lightbox_editlocation">
                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->photo->seao_locationid, 'resouce_type' => 'seaocore'), $this->photo->location, array('onclick' => 'openSmoothbox(this);return false', 'title' => $this->photo->location));?>
                    <?php if($this->canEdit):?>
                        <a class="buttonlink seaocore_icon_edit" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'edit-location', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');return false;"> 
                         <i><?php //echo $this->translate("Edit Location") ?></i>
                        </a>
                    <?php endif; ?>
                </div>
              <?php endif; ?>
            <?php endif;?>       
        
              <?php if (!empty($viewer_id)): ?>
                <?php if (SEA_PHOTOLIGHTBOX_SHARE): ?>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
          return false;" >
                       <?php echo $this->translate("Share") ?>
                  </a>
                <?php endif; ?>
                <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
          return false;" >
                       <?php echo $this->translate("Report") ?>
                  </a>
                <?php endif; ?>

                <?php if ((SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.makeprofile.photo', 1)) || $this->viewer_id == $this->photo->owner_id): ?>
                  <a href="<?php echo $this->url(array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true); ?>" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>');
          return false;" > <?php echo $this->translate("Make Profile Photo") ?></a>
                   <?php endif; ?>
                 <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_MAKEALBUMCOVER && $this->canEdit && $this->makeAlbumCover): ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'make-album-cover', 'album' => $this->album->getGuid(), 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Make Album Main Photo") ?></a>
              <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_GETLINK && $this->canEdit): ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'get-link', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
        return false;" > <?php echo $this->translate("Get Link") ?></a>
                 <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_SENDMAIL && $this->canEdit): ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'tell-a-friend', 'photo' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
        return false;" > <?php echo $this->translate("Tell a Friend") ?></a>
                 <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_MOVETOOTHERALBUM && $this->canEdit && $this->movetotheralbum): ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'move-to-other-album', 'album' => $this->album->getGuid(), 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Move To Other Album") ?></a>
              <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>
                <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
                <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->photo->getPhotoUrl()) . '&file_id=' . $this->photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download') ?></a>
              <?php endif; ?>
              <?php if ($this->canMakeFeatured && $this->allowView): ?>           
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->subject()->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")) ?>
                <a href="javascript:void(0);"  onclick='featuredPhoto("<?php echo $this->subject()->getGuid() ?>");'><span id="featured_sitealbum_photo" <?php if ($this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitealbum_photo" <?php if (!$this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>           
              <?php endif; ?>
              <?php if ($this->canDelete): ?>

                <?php
                if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()):
                  echo $this->htmlLink(array('route' => 'sitealbum_extended', 'controller' => 'photo', 'action' => 'delete', 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'delete', 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), 'sitealbum_extended', true)) . "'); return false;"));
                else:
                  echo $this->htmlLink(array('route' => 'sitealbum_extended', 'controller' => 'photo', 'action' => 'delete', 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'delete', 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity()), 'sitealbum_extended', true)) . "'); return false;"));
                endif;
                ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="photo_lightbox_content_middle">
            <?php if ($this->canEdit || !empty($this->photo->description)): ?>
              <div class="photo_lightbox_photo_description widthfull" id="link_sitealbum_description" style="display:block;">
                <?php if ($this->canEdit): ?>
                  <span class="lightbox_photo_description_edit_icon">
                    <a href="javascript:void(0);" onclick="showeditDescriptionSitealbum()" title=" <?php echo $this->translate('Edit this caption'); ?> "></a>
                  </span>
                <?php endif; ?>
                <span id="sitealbum_description" class="lightbox_photo_description">
                  <?php if (!empty($this->photo->description)): ?>
                    <?php echo nl2br($this->photo->getDescription()) ?>
                  <?php elseif ($this->canEdit): ?>
                    <a href="javascript:void(0);" onclick="showeditDescriptionSitealbum()" >  <?php echo $this->translate('Add a caption'); ?> </a>
                  <?php endif; ?>
                </span>
              </div>
            <?php endif; ?>
            <div class="photo_lightbox_photo_description"  >
              <div id="edit_sitealbum_description" style="display: none;">
                <textarea rows="2" cols="10"  name="edit_description" id="editor_sitealbum_description" title="<?php echo $this->translate('Add a caption'); ?>" ><?php echo $this->photo->description; ?></textarea>
                <div>
                  <button name="save" onclick="saveeditPhotoDescriptionSA('<?php echo $this->photo->getIdentity(); ?>')"><?php echo $this->translate('Save'); ?></button>
                  <button name="cancel" onclick="showeditDescriptionSitealbum();"><?php echo $this->translate('Cancel'); ?></button>
                </div>
              </div>
              <div id="sitealbum_description_loading" style="display: none;" >
                <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/loader.gif'   /></center>
              </div>
            </div>
            <?php if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) : ?>
              <div class="seaotagcheckinshowlocation">
                <?php
                // Render LOCAION WIDGET
                echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin");
                ?>
              </div>
            <?php endif; ?>
            <div class="photo_lightbox_photo_tags" id="media_tags" style="display: none;">
              <?php echo $this->translate('In this photo:'); ?>
            </div>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) : ?>     
              <?php if (!empty($this->canRate)): ?>
                <div id="album_rating" class="rating" onmouseout="photo_rating_out();">
                  <span id="rate_photo_1" class="seao_rating_star_generic" <?php if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(1);"<?php endif; ?> onmouseover="photo_rating_over(1);"></span>
                  <span id="rate_photo_2" class="seao_rating_star_generic" <?php if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(2);"<?php endif; ?> onmouseover="photo_rating_over(2);"></span>
                  <span id="rate_photo_3" class="seao_rating_star_generic" <?php if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(3);"<?php endif; ?> onmouseover="photo_rating_over(3);"></span>
                  <span id="rate_photo_4" class="seao_rating_star_generic" <?php if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(4);"<?php endif; ?> onmouseover="photo_rating_over(4);"></span>
                  <span id="rate_photo_5" class="seao_rating_star_generic" <?php if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(5);"<?php endif; ?> onmouseover="photo_rating_over(5);"></span>
                  <span id="photo_rating_text" class="rating_text fnone"><?php echo $this->translate('click to rate'); ?></span>
                </div>
              <?php else:
                ?>
                <div id="album_rating" class="rating" onmouseout="photo_rating_out();">
                  <span id="rate_photo_1" class="seao_rating_star_generic" ></span>
                  <span id="rate_photo_2" class="seao_rating_star_generic"></span>
                  <span id="rate_photo_3" class="seao_rating_star_generic"></span>
                  <span id="rate_photo_4" class="seao_rating_star_generic"  ></span>
                  <span id="rate_photo_5" class="seao_rating_star_generic"></span>
                  <span id="photo_rating_text" class="rating_text fnone"><?php echo $this->translate('click to rate'); ?></span>
                </div>
              <?php
              endif;
            endif;
            ?>

            <div id="photo_view_comment" >
              <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listLightboxComment.tpl'; ?>
            </div>
          </div>
          <div class="photo_lightbox_content_right" id="ads">     
          </div>
        </div>
    </div>
  <?php endif; ?>
  <?php if (empty($this->isajax)): ?>
  </div>
<?php endif; ?>
</div>

<script type="text/javascript">
  var taggerInstanceSitealbum;
  if (window.parent.defaultLoad)
    window.parent.defaultLoad = false;

  var existingTags =<?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>;

  function getTaggerInstanceSitealbum() {
    if (!$('media_photo_next'))
      return;
    taggerInstanceSitealbum = new SEAOTagger('media_photo_next', {
      'title': '<?php echo $this->string()->escapeJavascript($this->translate('Tag This Photo')); ?>',
      'description': '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.')); ?>',
      'createRequestOptions': {
        'url': '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
        'data': {
          'subject': '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions': {
        'url': '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data': {
          'subject': '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions': {
        'container': $('media_photo_next')
      },
      'tagListElement': 'media_tags',
      'existingTags': existingTags,
      'suggestProto': 'request.json',
      'suggestParam': "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
      'guid': <?php echo ( $this->viewer()->getIdentity() ? "'" . $this->viewer()->getGuid() . "'" : 'false' ) ?>,
      'enableCreate': <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete': <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>

    });

    var onclickNext = $('media_photo_next').getProperty('onclick');
    taggerInstanceSitealbum.addEvents({
      'onBegin': function() {
        $('media_photo_next').setProperty('onclick', '');
      },
      'onEnd': function() {
        $('media_photo_next').setProperty('onclick', onclickNext);
      },
      'onCreateTag': function(params) {
        existingTags.push(params);
      },
      'onRemoveTag': function(id) {
        for (var i = 0; i < existingTags.length; i++) {
          if (existingTags[i].id == id) {
            existingTags.splice(i, 1);
            break;
          }
        }
      }
    });

  }
  en4.core.runonce.add(function() {
    var descEls = $$('.albums_viewmedia_info_caption');
    if (descEls.length > 0) {
      descEls[0].enableLinks();
    }

    setTimeout("getTaggerInstanceSitealbum()", 1250);
  });

  window.addEvent('keyup', function(e) {
    if (e.target.get('tag') == 'html' ||
            e.target.get('tag') == 'body' ||
            e.target.get('tag') == 'a') {
      if (e.key == 'right') {
        photopaginationSitealbum(getNextPhotoSitealbum(), 1);
      } else if (e.key == 'left') {
        photopaginationSitealbum(getPrevPhotoSitealbum(), 1);
      }
    }

    if (e.key == 'esc') {
      closeLightBoxAlbum();
    }
  });

  function getPrevPhotoSitealbum() {
    return '<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->prevPhoto, array_merge($this->params, array('offset' => $this->PrevOffset)))) ?>';
  }
  function getNextPhotoSitealbum() {
    return '<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>';
  }
</script>
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) : ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      var pre_rate = <?php echo $this->photo->rating; ?>;
      var update_permission = <?php echo $this->update_permission; ?>;
      var rated = '<?php echo $this->rated; ?>';
      var photo_id = <?php echo $this->photo->photo_id; ?>;
      var total_votes = <?php echo $this->rating_count; ?>;
      var viewer = <?php echo $this->viewer_id; ?>;
      new_text = '';

      var photo_rating_over = window.photo_rating_over = function(rating) {
        if (rated == 1 && update_permission == 0) {
          $('photo_rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
          //set_photo_rating();
        } else if (viewer == 0) {
          $('photo_rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
        } else {
          $('photo_rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
          for (var x = 1; x <= 5; x++) {
            if (x <= rating) {
              $('rate_photo_' + x).set('class', 'seao_rating_star_generic rating_star_y');
            } else {
              $('rate_photo_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
            }
          }
        }
      }

      var photo_rating_out = window.photo_rating_out = function() {
        if (new_text != '') {
          $('photo_rating_text').innerHTML = new_text;
        }
        else {
          $('photo_rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
        }
        if (pre_rate != 0) {
          set_photo_rating();
        }
        else {
          for (var x = 1; x <= 5; x++) {
            $('rate_photo_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
          }
        }
      }

      var set_photo_rating = window.set_photo_rating = function() {
        var rating = pre_rate;
        if (new_text != '') {
          $('photo_rating_text').innerHTML = new_text;
        }
        else {
          $('photo_rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
        }
        for (var x = 1; x <= parseInt(rating); x++) {
          $('rate_photo_' + x).set('class', 'seao_rating_star_generic rating_star_big');
        }

        for (var x = parseInt(rating) + 1; x <= 5; x++) {
          $('rate_photo_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
        }

        var remainder = Math.round(rating) - rating;
        if (remainder <= 0.5 && remainder != 0) {
          var last = parseInt(rating) + 1;
          $('rate_photo_' + last).set('class', 'seao_rating_star_generic rating_star_half_y');
        }
      }

      var photorate = window.photorate = function(rating) {
        $('photo_rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
//        for (var x = 1; x <= 5; x++) {
//          $('rate_photo_' + x).set('onclick', '');
//        }
        (new Request.JSON({
          'format': 'json',
          'url': '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'rate'), 'default', true) ?>',
          'data': {
            'format': 'json',
            'rating': rating,
            'photo_id': photo_id
          },
          'onRequest': function() {
//            rated = 1;
//            total_votes = total_votes + 1;
//            pre_rate = (pre_rate + rating) / total_votes;
//            set_photo_rating();
          },
          'onSuccess': function(responseJSON, responseText)
          {
            pre_rate = responseJSON[0].rating;
            set_photo_rating();
            $('photo_rating_text').innerHTML = responseJSON[0].total + "<?php echo $this->translate(' ratings')?>";
            new_text = responseJSON[0].total + "<?php echo $this->translate(' ratings')?>";
          }
        })).send();

      }
      set_photo_rating();
    });
  </script>
<?php endif; ?>

<script type="text/javascript">

  function checkPasswordProtection(obj) {

      var flag = true;
      if ($('password_error'))
          $('password_error').destroy();

      if (obj['password'] && obj['password'].value == '') {
          liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'sitealbum_protection_error', 'id': 'password_error'}).inject($('password-element'));
          flag = false;
      }

      if (flag) {
          url = '<?php echo $this->url(array('action' => 'check-password-protection', 'album_id' => $this->album->album_id), "sitealbum_general"); ?>';
          var request = new Request.JSON({
              url: url,
              method: 'post',
              data: {
                  format: 'html',
                  album_id: '<?php echo $this->album->album_id; ?>',
                  password: obj['password'].value
              },
              //responseTree, responseElements, responseHTML, responseJavaScript
              onSuccess: function (responseJSON) {
                  if (responseJSON.status == 0) {

                      if ($('password_error'))
                          $('password_error').destroy();

                      liElement = new Element('span', {'html': '<?php echo $this->translate("This is not valid password. Please try again."); ?>', 'class': 'sitealbum_protection_error', 'id': 'password_error'}).inject($('password-element'));
                      flag = false;
                  } else {
                      closeLightBoxAlbum();
                      openLightBoxAlbum("<?php echo $this->photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>");
                  }
              }});
          request.send();
      }
      return false;
  }
</script>

<style type="text/css">
  .sitealbum_protection_error {
      color:#FF0000;
      display:block;
      font-size:11px;
      padding-top:5px;
  }
</style>