<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: light-box-view-fix-window.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $showCursor = 0; ?>
<?php if (!empty($this->viewer_id) && (!empty($this->canRate))): ?>
  <?php $showCursor = 1; ?>
<?php endif; ?>
<script type="text/javascript">

  var photoLightbox = 1;

</script>

<style type="text/css">
  <?php if ($showCursor == 0) { ?>
    .photo_lightbox_content_wrapper .seao_rating_star_generic{
      cursor: default;
    }
  <?php } ?>
</style>

<?php if (empty($this->isajax)): ?>
  <div id="ads_hidden" style="display: none;" >  
  </div>
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
<div class="photo_lightbox_cont">
  <div class="photo_lightbox_left" id="photo_lightbox_left" style="right: 1px;">
    <table width="100%" height="100%">
      <tr>
        <td width="100%" height="100%" valign="middle">
          <div class="photo_lightbox_image" id='media_image_div_sitealbum'>     
            <?php if ($this->viewPermission): ?>      
              <div id='media_photo_next' <?php if ($showLink): ?> onclick="getSitealbumPhoto('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>', 1, '<?php echo $this->nextPhoto->getPhotoUrl() ?>')" <?php endif; ?> >
                <?php
//                echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
//                    'id' => 'media_photo',
//                    'class' => "lightbox_photo"
//                ));
                ?>  

                <img id ="media_photo" class="lightbox_photo" src="<?php echo $this->photo->getPhotoUrl() ?>" style="max-width: 943px; max-height: 265px;"></img>

              </div>
              <div class="photo_lightbox_swf" style="display: none;" id="full_mode_photo_button" onclick="switchFullModePhoto(true);">
                <div class="photo_lightbox_cm_f"></div>    
              </div>
              <div id="comment_count_photo_button" class="photo_lightbox_cm_box" style="display: none;" onclick="switchFullModePhoto(false);" title="<?php echo $this->translate('Show Comments'); ?>">

                <div class="photo_lightbox_cm_cc"><?php echo $this->photo->like_count ?></div>
                <div class="photo_lightbox_cml_c"></div>  

                <div class="photo_lightbox_cm_cc"><?php echo $this->photo->comment_count ?></div>
                <div class="photo_lightbox_cm_c" ></div>

              </div>
            <?php else: ?>
             
              
              	<?php if ($this->albumPasswordProtected): ?>
                   <?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_lightboxPasswordProtection.tpl'; ?>
                <?php else:?>
                	 <div class="tip">
                    <span><?php echo $this->translate('You do not have the permission to view this photo.'); ?> </span>
                  </div>
                <?php endif;?>
                
              
              
              <div  style="display: none;" id="full_mode_photo_button"></div>
              <div id="comment_count_photo_button" style="display: none;"></div>
            <?php endif; ?>
            <div id="full_screen_display_captions_on_image" style="display: none;">
              <?php if (!empty($this->photo->description) || (!empty($this->photo->title) && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0))): ?>    
                <div class="photo_lightbox_stc">                 
                  <?php if (!empty($this->photo->title) && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)): ?>
                    <b><?php echo $this->photo->getTitle() ?></b>
                    <?php if (!empty($this->photo->description)): ?>
                      <br />
                    <?php endif; ?>
                  <?php endif; ?>
                  <?php if (!empty($this->photo->description)): ?>	
                    <span id="full_screen_display_captions_on_image_dis">
                      <?php echo $this->photo->getDescription() ?>
                    </span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($showLink): ?>
            <div class="photo_lightbox_options" id="sitealbum_photo_scroll">    
                <?php if($this->prevPhoto):?>
              <div class="photo_lightbox_pre" onclick="getSitealbumPhoto('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->prevPhoto, array_merge($this->params, array('offset' => $this->PrevOffset)))) ?>', 1, '<?php echo $this->prevPhoto->getPhotoUrl() ?>')" title="<?php echo $this->translate('Previous'); ?>" ><i></i></div>
              <?php endif;?>
              <div onclick="getSitealbumPhoto('<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>', 1, '<?php echo $this->nextPhoto->getPhotoUrl() ?>')"  title="<?php echo $this->translate('Next'); ?>" class="photo_lightbox_nxt"><i></i></div>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    </table>
    <?php if ($this->canMakeFeatured && !$this->allowView): ?>
      <div class="tip photo_lightbox_privacy_tip">
        <span>
          <?php echo $this->translate("SITEALBUM_PHOTO_VIEW_PRIVACY_MESSAGE"); ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
  <div class="photo_lightbox_right" id="photo_lightbox_right_content"> 
    <div id="main_right_content_area"  style="height: 100%">
      <div id="main_right_content" class="scroll_content">        
        <div id="photo_right_content" class="photo_lightbox_right_content">
          <?php if ($this->viewPermission): ?>
            <div class='photo_right_content_top'>
              <div class='photo_right_content_top_l'>
                <?php echo $this->htmlLink($this->album->getOwner()->getHref(), $this->itemPhoto($this->album->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo', 'title' => $this->album->getOwner()->getTitle())); ?>
              </div>
              <div class='photo_right_content_top_r'>
                <?php echo $this->album->getOwner()->__toString() ?> 
                <?php if(isset($this->photo->date_taken) && $this->photo->date_taken):?>
                  <?php if( $this->photo->date_taken != '0000-00-00 00:00:00'):?>
                    <?php echo $this->timestamp($this->photo->date_taken); ?>
                    <span class="timestamp middot">&middot;</span>
                  <?php endif;?>   
                	<div class="timestamp seao_postdate"><div class="seao_postdate_tip"><?php echo $this->translate('Posted on %1$s', $this->timestamp($this->photo->modified_date)) ?></div></div> 
                <?php endif;?>  
              </div>
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)): ?>
                <div class="photo_right_content_top_title" style="margin-top:5px;">
                  <?php if ($this->canEdit || !empty($this->photo->title)): ?>
                    <div id="link_seaocore_title" style="display:block;">
                      <span id="seaocore_title">
                        <?php if (!empty($this->photo->title)): ?>
                          <?php echo $this->photo->getTitle() ?>
                        <?php elseif ($this->canEdit): ?>
                          <?php echo $this->translate('Add a title'); ?>
                        <?php endif; ?>
                      </span>
                      <?php if ($this->canEdit): ?>
                        <a href="javascript:void(0);" onclick="showeditPhotoTitleSA()" class="photo_right_content_top_title_edit"><?php echo $this->translate('Edit'); ?></a>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <div id="edit_seaocore_title" class="photo_right_content_top_edit" style="display: none;">
                    <input type="text"  name="edit_title" id="editor_seaocore_title" title="<?php echo $this->translate('Add a title'); ?>" value="<?php echo $this->photo->title; ?>" />
                    <div class="buttons">
                      <button name="save" onclick="saveeditPhotoTitleSA('<?php echo $this->photo->getIdentity(); ?>', '<?php echo $this->resource_type; ?>')"><?php echo $this->translate('Save'); ?></button>
                      <button name="cancel" onclick="showeditPhotoTitleSA();"><?php echo $this->translate('Cancel'); ?></button>
                    </div>
                  </div>
                  <div id="seaocore_title_loading" style="display: none;" >
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
                  </div>
                </div>	
              <?php endif; ?>
            </div>  

            <div class="photo_right_content_top_title photo_right_content_top_caption">
              <?php if ($this->canEdit || !empty($this->photo->description)): ?>
                <div id="link_sitealbum_description" style="display:block;">
                  <span id="sitealbum_description" class="lightbox_photo_description">
                    <?php if (!empty($this->photo->description)): ?>
                      <?php echo $this->viewMore($this->photo->getDescription(), 400, 5000, 400, true); ?>
                    <?php elseif ($this->canEdit): ?>
                      <?php echo $this->translate('Add a caption'); ?>
                    <?php endif; ?>
                  </span>
                  <?php if ($this->canEdit): ?>
                    <a href="javascript:void(0);" onclick="showeditDescriptionSitealbum()" class="photo_right_content_top_title_edit"><?php echo $this->translate('Edit'); ?></a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              <div id="edit_sitealbum_description" class="photo_right_content_top_edit" style="display: none;">
                <textarea rows="2" cols="10"  name="edit_description" id="editor_sitealbum_description" title="<?php echo $this->translate('Add a caption'); ?>" ><?php echo $this->photo->description; ?></textarea>
                <div class="buttons">
                  <button name="save" onclick="saveeditPhotoDescriptionSA('<?php echo $this->photo->getIdentity(); ?>')"><?php echo $this->translate('Save'); ?></button>
                  <button name="cancel" onclick="showeditDescriptionSitealbum();"><?php echo $this->translate('Cancel'); ?></button>
                </div>
              </div>
              <div id="sitealbum_description_loading" style="display: none;" >
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
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
            <div class="photo_right_content_tags" id="media_tags" style="display: none;">
              <?php echo $this->translate('In this photo:'); ?>
            </div>

            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) : ?>        
              <?php if (!empty($this->canRate)): ?>
                <div id="album_rating" class="rating" onmouseout="photo_rating_out();">
                  <span id="rate_photo_1" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(1);"<?php endif; ?> onmouseover="photo_rating_over(1);"></span>
                  <span id="rate_photo_2" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(2);"<?php endif; ?> onmouseover="photo_rating_over(2);"></span>
                  <span id="rate_photo_3" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(3);"<?php endif; ?> onmouseover="photo_rating_over(3);"></span>
                  <span id="rate_photo_4" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(4);"<?php endif; ?> onmouseover="photo_rating_over(4);"></span>
                  <span id="rate_photo_5" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="photorate(5);"<?php endif; ?> onmouseover="photo_rating_over(5);"></span>
                  <span id="rating_photo_text" class="rating_text fnone"><?php echo $this->translate('click to rate'); ?></span>
                </div>
              <?php else:
                ?>
                <div id="album_rating" class="rating" onmouseout="photo_rating_out();">
                  <span id="rate_photo_1" class="seao_rating_star_generic" ></span>
                  <span id="rate_photo_2" class="seao_rating_star_generic"></span>
                  <span id="rate_photo_3" class="seao_rating_star_generic"></span>
                  <span id="rate_photo_4" class="seao_rating_star_generic"  ></span>
                  <span id="rate_photo_5" class="seao_rating_star_generic"></span>
                  <span id="rating_photo_text" class="rating_text fnone"><?php echo $this->translate('click to rate'); ?></span>
                </div>
              <?php
              endif;
            endif;
            ?>

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
            
            <div id="photo_view_comment" class="photo_right_content_comments">
              <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listLightboxComment.tpl'; ?>
            </div> 
          <?php endif; ?>
        </div>       
      </div>
    </div>
    <div class="photo_right_content_add" id="ads">
      <?php if (empty($this->isajax)): ?>
        <?php echo $this->content()->renderWidget("seaocore.lightbox-ads", array('limit' => 1)) ?>
      <?php endif; ?>
    </div>
  </div> 
</div>

<div id="close_all_photos" class="sea_val_photos_box_wrapper_overlay" style="height:0px; " onclick="closeAllPhotoContener()"></div>
<div id="close_all_photos_btm" class="sea_val_photos_box_wrapper_overlay_btm" onclick="closeAllPhotoContener()"  style="height:0px;" ></div>
<div id="all_photos" class="sea_val_photos_box_wrapper" style="height:0px;">
  <div class="sea_val_photos_box_header">
    <?php echo $this->album->getTitle() ?>
    (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->photos_count), $this->locale()->toNumber($this->album->photos_count)); ?>)
  </div>
  <div class="photo_lightbox_close" onclick="closeAllPhotoContener()"></div>
  <div id="main_photos_contener"> 
    <div id="photos_contener" class="lb_photos_contener scroll_content sea_val_photos_thumbs_wrapper">
      <div class="sea_val_photos_box_loader">
        <img alt="<?php echo $this->translate("Loading...") ?>" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/loader-large.gif" />
      </div>
    </div>
  </div>  
</div>
<div class="lightbox_btm_bl">
  <?php if ($this->viewPermission): ?>
    <div class="lightbox_btm_bl_left">
      <?php if ($this->album->photos_count > 1 && (!isset($this->params['type']) || empty($this->params['type']))): ?>
        <div class="lightbox_btm_bl_btn" style="" onclick="showAllPhotoContener('<?php echo $this->album->getIdentity() ?>', '<?php echo $this->photo->getIdentity() ?>', '<?php echo $this->album->photos_count ?>')"> 
          <span class="b-a-Ua lightbox_btm_bl_btn_t"><?php echo $this->translate('View All'); ?></span>
          <span class="lightbox_btm_bl_btn_i"></span>
        </div>
      <?php endif; ?>
      <div id="photo_owner_lb_fullscreen" style="display: none;" class='lightbox_btm_bl_left_photo'>
        <?php echo $this->htmlLink($this->album->getOwner()->getHref(), $this->itemPhoto($this->album->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo')); ?>
      </div>  
      <div class="lightbox_btm_bl_left_links">

        <div class="lbbll_ml" id="photo_owner_titile_lb_fullscreen" style="display: none;">
          <?php echo $this->album->getOwner()->__toString() ?>
        </div>
        <div class="lbbll_s" id="photo_owner_titile_lb_fullscreen_sep" style="display: none;">-</div>

        <?php if (isset($this->params['type']) && !empty($this->params['type'])): ?>    
          <div class="lbbll_ml"><?php echo $this->translate(ucfirst($this->displayTitle)); ?></div>
          <div class="lbbll_s">-</div>
        <?php endif; ?>        
        <div class="lbbll_ml">
          <?php echo $this->htmlLink($this->album, $this->album->getTitle()) ?>
        </div>     
        <br class="clr" />   	

        <?php if (!isset($this->params['type']) || empty($this->params['type'])): ?>        
          <div class="lbbll_ol"><?php
            echo $this->translate('%1$s of %2$s', $this->locale()->toNumber($this->getPhotoIndex + 1), $this->locale()->toNumber($this->album->photos_count))
            ?></div>
          <div class="lbbll_s">-</div>
        <?php endif; ?>       
        <div class="lbbll_ol">
          <?php echo $this->timestamp($this->photo->modified_date) ?>
        </div>  
        <?php if (($this->viewer()->getIdentity() && (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO || SEA_PHOTOLIGHTBOX_REPORT )) || SEA_PHOTOLIGHTBOX_DOWNLOAD ||(SEA_PHOTOLIGHTBOX_MAKEALBUMCOVER && $this->canEdit && $this->makeAlbumCover) ||($this->canEdit && (SEA_PHOTOLIGHTBOX_GETLINK || SEA_PHOTOLIGHTBOX_SENDMAIL))|| (SEA_PHOTOLIGHTBOX_EDITLOCATION && $this->canEdit && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) || (SEA_PHOTOLIGHTBOX_MOVETOOTHERALBUM && $this->canEdit && $this->movetotheralbum) ||($this->canMakeFeatured && $this->allowView) || $this->canDelete): ?> 
          <div class="lbbll_s">-</div>
          <div class="lbbll_ol p_r">
            <div id="photos_options_area" class="lbbll_ol_uop">
              <?php if (SEA_PHOTOLIGHTBOX_EDITLOCATION && $this->canEdit && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)): ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'edit-location', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
          return false;" > <?php echo $this->translate("Edit Location") ?></a> <?php endif; ?>

              <?php if ($this->viewer()->getIdentity()): ?>            
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
            <span onclick="showPhotoToggleContent('photos_options_area')" class="op_box">
              <?php echo $this->translate('Options'); ?>
              <span class="sea_pl_at"></span>        
            </span>        
          </div>
        <?php endif; ?>  
      </div>
    </div>

    <div class="lightbox_btm_bl_right">
      <?php if ($this->enablePinit): ?>
        <div class="seaocore_pinit_button">
          <a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->photo->getHref()); ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $this->photo->getPhotoUrl()) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $this->photo->getPhotoUrl()); ?>&description=<?php echo $this->photo->getTitle(); ?>" class="pin-it-button" count-layout="horizontal"  id="new_pin" >Pin It</a>

          <script type="text/javascript" >
    en4.core.runonce.add(function() {
    new Asset.javascript('http://assets.pinterest.com/js/pinit.js', {});
    });
          </script>
        </div>
      <?php endif; ?>
      <?php echo $this->socialShareButton(); ?>
      <?php if ($this->canEdit): ?>
        <div class="lightbox_btm_bl_rop" id="">
          <a class="icon_photos_lightbox_rotate_ccw" onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.rotate(<?php echo $this->photo->getIdentity() ?>, 90).addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_rotate_ccw')
        }.bind(this));" title="<?php echo $this->translate("Rotate Left"); ?>" ></a>
          <a class="icon_photos_lightbox_rotate_cw" onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.rotate(<?php echo $this->photo->getIdentity() ?>, 270).addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_rotate_cw')
        }.bind(this));" title="<?php echo $this->translate("Rotate Right"); ?>" ></a>
          <a class="icon_photos_lightbox_flip_horizontal" onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal').addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_flip_horizontal')
        }.bind(this));" title="<?php echo $this->translate("Flip Horizontal"); ?>" ></a>
          <a class="icon_photos_lightbox_flip_vertical" onclick="$(this).set('class', 'icon_loading');
        en4.sitealbum.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical').addEvent('complete', function() {
          this.set('class', 'icon_photos_lightbox_flip_vertical')
        }.bind(this));" title="<?php echo $this->translate("Flip Vertical"); ?>"></a>
        </div>
      <?php endif ?>

      <?php if ($this->canTag): ?>      
        <span class="lightbox_btm_bl_btn" onclick='taggerInstanceSitealbum.begin();'><?php echo $this->translate('Tag This Photo'); ?></span>     
      <?php endif; ?>

      <?php
      $viewer_id = $this->viewer()->getIdentity();
      if ($this->canComment):
        ?>   
        <span class="lightbox_btm_bl_btn" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>like_link" <?php if ($this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> onclick="en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '1');" title="<?php echo $this->translate('Press L to Like'); ?>"><?php echo $this->translate('Like'); ?></span>
        <span class="lightbox_btm_bl_btn" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>unlike_link" <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?> style="display:none;" <?php endif; ?> onclick="en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '1');" title="<?php echo $this->translate('Press L to Unlike'); ?>"><?php echo $this->translate('Unlike'); ?></span>
        <span class="lightbox_btm_bl_btn" onclick="if (fullmode_photo) {
          switchFullModePhoto(false);
        }
        if ($('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>')) {
          $('comment-form-open-li_<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>').style.display = 'none';
        }
        $('comments-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>_0').style.display = '';
        $('comments-form_<?php echo $this->subject()->getType() . "_" . $this->subject()->getIdentity() ?>_0').body.parentNode.children[0].focus();"  ><?php echo $this->translate('Comments'); ?></span>  
            <?php endif; ?>     
            <?php if (!empty($viewer_id) && SEA_PHOTOLIGHTBOX_SHARE): ?>
        <span class="lightbox_btm_bl_btn"  onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), 'default', true)); ?>');
        return false;" >
                <?php echo $this->translate("Share") ?>
        </span>
      <?php endif; ?>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.gotophoto', 1)): ?>  
        <a href="<?php echo $this->subject()->getHref() ?>"  style="text-decoration:none;">
          <span class="lightbox_btm_bl_btn lightbox_btm_bl_btn_photo"> 
            <i></i>
            <?php echo $this->translate("Go to Photo") ?>
          </span>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>



<script type="text/javascript">

  var prev_photo = '<?php echo $this->prevPhoto;?>';
  var prev_url = '<?php echo $this->prevPhotoUrl;?>';

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
      'enableDelete': <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>,
      'enableShowToolTip': true,
      'showToolTipRequestOptions': {
        'url': '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'show-tooltip-tag'), 'default', true) ?>',
        'data': {
          'subject': '<?php echo $this->subject()->getGuid() ?>'
        }

      }
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
        if (existingTags.length < 1)
          $("media_tags").style.display = "none";
      }
    });

  }

  en4.core.runonce.add(function() {

    var descEls = $$('.lightbox_photo_description');
    if (descEls.length > 0) {
      descEls[0].enableLinks();
    }

    if ($('editor_sitealbum_description'))
      $('editor_sitealbum_description').autogrow();
    $('ads').style.bottom = "-500px";
    resetPhotoContent();
    (function() {
      if (!$('main_right_content_area'))
        return;
      rightSidePhotoContent = new SEAOMooVerticalScroll('main_right_content_area', 'main_right_content', {});
    }).delay(500);
  });
  var addPhotoPaginationKeyEvent = "<?php echo $showLink ?>";
  if ($type(keyDownEventsSitealbumPhoto))
    document.removeEvent("keydown", keyDownEventsSitealbumPhoto);
  var keyDownEventsSitealbumPhoto = function(e) {
    if (e.target.get('tag') == 'html' ||
            e.target.get('tag') == 'body' ||
            (e.target.get('tag') == 'div' && !e.target.getParent('.compose-container')) ||
            e.target.get('tag') == 'span' ||
            e.target.get('tag') == 'a') {
      if (e.key == 'right') {
        if (addPhotoPaginationKeyEvent == 0)
          return;
        getSitealbumPhoto(getNextPhotoSitealbum(), 1, '<?php echo $this->nextPhoto->getPhotoUrl() ?>');
      } else if (e.key == 'left') {
        if (addPhotoPaginationKeyEvent == 0)
          return;
        if(prev_photo) {
            getSitealbumPhoto(getPrevPhotoSitealbum(), 1, prev_url);
        }
      } else if (e.key == 'esc') {
        closeLightBoxAlbum();
      }

    }
  };
  if ($type(keyUpLikeEventSitealbumPhoto))
    document.removeEvent("keyup", keyUpLikeEventSitealbumPhoto);
  var keyUpLikeEventSitealbumPhoto = function(e) {

<?php if ($this->canComment && $this->viewPermission): ?>
      if (e.key == 'l' && (
              e.target.get('tag') == 'html' ||
              e.target.get('tag') == 'span' ||
              (e.target.get('tag') == 'div' && !e.target.getParent('.compose-container')) ||
              e.target.get('tag') == 'a' ||
              e.target.get('tag') == 'body')) {
        var photo_like_id = "<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>";
        if ($(photo_like_id + "unlike_link") && $(photo_like_id + "unlike_link").style.display == "none") {
          en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '1');
        } else if ($(photo_like_id + "like_link") && $(photo_like_id + "like_link").style.display == "none") {
          en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '1');
        }
      }
<?php endif; ?>

  };
  document.addEvents({
    'keyup': keyUpLikeEventSitealbumPhoto,
    'keydown': keyDownEventsSitealbumPhoto
  });

  function getPrevPhotoSitealbum() {
    return '<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->prevPhoto, array_merge($this->params, array('offset' => $this->PrevOffset)))) ?>';
  }
  function getNextPhotoSitealbum() {
    return '<?php echo $this->escape(Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->nextPhoto, array_merge($this->params, array('offset' => $this->NextOffset)))) ?>';
  }
  function showSmoothBox(url)
  {
    Smoothbox.open(url);
    parent.Smoothbox.close;
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
          $('rating_photo_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
          //set_photo_rating();
        } else if (viewer == 0) {
          $('rating_photo_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
        } else {
          $('rating_photo_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
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
          $('rating_photo_text').innerHTML = new_text;
        }
        else {
          $('rating_photo_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
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
          $('rating_photo_text').innerHTML = new_text;
        }
        else {
          $('rating_photo_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
        }
        for (var x = 1; x <= parseInt(rating); x++) {
          $('rate_photo_' + x).set('class', 'seao_rating_star_generic rating_star_y');
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
        $('rating_photo_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
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
            $('rating_photo_text').innerHTML = responseJSON[0].total + "<?php echo $this->translate(' ratings')?>";
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
  