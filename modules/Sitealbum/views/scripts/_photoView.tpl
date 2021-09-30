<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _photoView.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $view = Zend_Registry::get('Zend_View');
    if(!$this->showbuttons)
        $this->showbuttons = Zend_Controller_Front::getInstance()->getRequest()->getParam('showbuttons');
if ($this->showLightBox):
  include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>

<script type="text/javascript">
  function seaocore_content_type_likes(resource_id, resource_type) {

    content_type_undefined = 0;
    var content_type = resource_type;
    if (resource_type == '') {
      content_type_undefined = 1;
      var content_type = resource_type;
    }

    // SENDING REQUEST TO AJAX
    var request = seaocore_content_create_like(resource_id, resource_type, content_type);

    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
      if (content_type_undefined == 0) {
        if (responseJSON.like_id) {
          if ($(content_type + '_like_' + resource_id))
            $(content_type + '_like_' + resource_id).value = responseJSON.like_id;
          if ($(content_type + '_most_likes_' + resource_id))
            $(content_type + '_most_likes_' + resource_id).style.display = 'none';
          if ($(content_type + '_unlikes_' + resource_id))
            $(content_type + '_unlikes_' + resource_id).style.display = 'table-cell';
          //en4.core.comments.like(content_type, resource_id);
        } else {
          if ($(content_type + '_like_' + resource_id))
            $(content_type + '_like_' + resource_id).value = 0;
          if ($(content_type + '_most_likes_' + resource_id))
            $(content_type + '_most_likes_' + resource_id).style.display = 'table-cell';
          if ($(content_type + '_unlikes_' + resource_id))
            $(content_type + '_unlikes_' + resource_id).style.display = 'none';
          //en4.core.comments.unlike(content_type, resource_id);
        }
      }
    });
  }

  function seaocore_content_create_like(resource_id, resource_type, content_type) {
    if ($(content_type + '_like_' + resource_id)) {
      var like_id = $(content_type + '_like_' + resource_id).value
    }
    var request = new Request.JSON({
      url: en4.core.baseUrl + 'seaocore/like/like',
      data: {
        format: 'json',
        'resource_id': resource_id,
        'resource_type': resource_type,
        'like_id': like_id
      }
    });
    request.send();
    return request;
  }
</script>
<script type="">
  var is_location_ajax = '<?php echo $this->isajax; ?>';
</script>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()
        ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/tagger/tagger.js')
        ->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
$this->headTranslate(array('Save', 'Cancel', 'delete'));
?>
<?php
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
if (empty($fbmodule) || empty($fbmodule->enabled) || $fbmodule->version <= '4.2.3')
  $enable_facebookse = 0;
else
  $enable_facebookse = 1;
?>
<div id="layout_middle">
  <!--FACEBOOK LIKE BUTTON START HERE-->
  <?php if ($enable_facebookse) : ?>
    <div >
      <script type="text/javascript">
        var fblike_moduletype = 'album_photo';
        var fblike_moduletype_id = '<?php echo $this->photo->getIdentity(); ?>'
      </script>
      <?php echo '<br />' . Engine_Api::_()->facebookse()->isValidFbLike('album'); ?>
    </div>
  <?php endif; ?> 
  <div class='albums_viewmedia'>
    <?php if (!$this->message_view): ?>
      <div class="albums_viewmedia_nav">
        <div>
          <?php
          echo $this->translate('Photo %1$s of %2$s in %3$s', $this->locale()->toNumber($this->getPhotoIndex + 1), $this->locale()->toNumber($this->album->photos_count), (string) $this->translate($this->album->getTitle()))
          ?>
        </div>
        <?php if ($this->album->photos_count > 1): ?>
          <div>
            <a href="<?php echo $this->escape($this->previousPhoto->getHref()) ?>" onclick="photoPaginationDefaultView('<?php echo $this->escape($this->previousPhoto->getHref()) ?>');
                    return false;"  title="<?php echo $this->translate('Previous'); ?>" > <?php echo $this->translate('Previous') ?></a>
            <a href="<?php echo $this->escape($this->nextPhoto->getHref()) ?>" onclick="photoPaginationDefaultView('<?php echo $this->escape($this->nextPhoto->getHref()) ?>');
            return false;"  title="<?php echo $this->translate('Next'); ?>" ><?php echo $this->translate('Next'); ?></a>

          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <div class='albums_viewmedia_info o_hidden'>
      <div class='album_viewmedia_container' id='media_photo_div'>

        <a id='media_photo_next'   <?php if ($this->album->photos_count > 1): ?> href='<?php echo $this->escape($this->nextPhoto->getHref()) ?>' onclick="photoPaginationDefaultView('<?php echo $this->escape($this->nextPhoto->getHref()) ?>');
          return false;" <?php endif; ?> >  
             <?php
             echo $this->itemPhoto($this->photo, 'thumb.profile', $this->photo->getTitle(), array(
              'id' => 'media_photo',
            ));
             ?>     
        </a>   
      </div>

      <?php if ($this->showbuttons): ?>
        <div class="sitealbum_viewmedia_photooptions">
          <div class="sitealbum_viewmedia_leftblock">
            <span>
              <?php if (!empty($this->viewer_id)): ?>
                <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike('album_photo', $this->photo->getIdentity()); ?>
                <a id="album_photo_unlikes_<?php echo $this->photo->getIdentity(); ?>" style ='display:<?php echo $hasLike ? "table-cell" : "none" ?>' href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $this->photo->getIdentity(); ?>', 'album_photo');"><?php echo $this->translate('Unlike') ?></a>
                <a id="album_photo_most_likes_<?php echo $this->photo->getIdentity(); ?>" style ='display:<?php echo empty($hasLike) ? "table-cell" : "none" ?>' href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $this->photo->getIdentity(); ?>', 'album_photo');"><?php echo $this->translate('Like') ?></a>
              <?php endif; ?> 
              <?php if (!empty($this->viewer_id)): ?>
                <a onclick="$('comments-form_'+ '<?php echo $this->photo->getGuid();?>_0').style.display = '';
              $('comments-form_' + '<?php echo $this->photo->getGuid();?>_0').body.parentNode.children[0].focus();" href="javascript:void(0);"><?php echo $this->translate('Comment');?></a>
                 <?php endif; ?>
                 <?php if (!empty($this->viewer_id)): ?>
                <input type ="hidden" id = "album_photo_like_<?php echo $this->photo->getIdentity(); ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
              <?php endif; ?>
            </span>
          </div>
          <div class="sitealbum_viewmedia_rightblock">
            <span>
              <?php if ($this->canTag): ?>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Tag This Photo'), array('onclick' => 'taggerInstance.begin();')) ?>
              <?php endif; ?>
              <?php if ((!$this->message_view && !empty($this->viewer_id) && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.makeprofile.photo', 1)) || $this->viewer_id == $this->photo->owner_id): ?><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>');
            return false;" > <?php echo $this->translate("Make Profile Photo") ?></a><?php endif; ?>
            </span>
          </div>
        </div>
      <?php endif; ?>

      <br />
      <a></a>
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
      <?php if ($this->photo->getTitle()): ?>
        <div class="albums_viewmedia_info_title">
          <?php echo $this->photo->getTitle(); ?>
        </div>
      <?php endif; ?>
      <?php if ($this->photo->getDescription()): ?>
        <div class="albums_viewmedia_info_caption">
          <?php echo nl2br($this->photo->getDescription()) ?>
        </div>
      <?php endif; ?>
      <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;">
        <?php echo $this->translate('In this photo:') ?>
      </div>

      <?php if (!empty($this->viewDisplayHR)) : ?>
        <div class="albums_viewmedia_info_footer_horizontal">
          <?php if ($this->canMakeFeatured && !$this->allowView): ?>
            <div class="tip">
              <span>
                <?php echo $this->translate("SITEALBUM_PHOTO_VIEW_PRIVACY_MESSAGE"); ?>
              </span>
            </div>
          <?php endif; ?>
          <div class="fleft"><?php echo $this->content()->renderWidget("sitealbum.user-ratings"); ?></div>
          <?php if ($this->canEdit): ?>
            <div class="albums_viewmedia_info_actions">
              <a class="buttonlink icon_photos_rotate_ccw" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.album.rotate(<?php echo $this->photo->getIdentity() ?>, 90).addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_rotate_ccw')
            }.bind(this));" title="<?php echo $this->translate("Rotate Left"); ?>">&nbsp;</a>
              <a class="buttonlink icon_photos_rotate_cw" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.album.rotate(<?php echo $this->photo->getIdentity() ?>, 270).addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_rotate_cw')
            }.bind(this));" title="<?php echo $this->translate("Rotate Right"); ?>" >&nbsp;</a>
              <a class="buttonlink icon_photos_flip_horizontal" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.album.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal').addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_flip_horizontal')
            }.bind(this));" title="<?php echo $this->translate("Flip Horizontal"); ?>" >&nbsp;</a>
              <a class="buttonlink icon_photos_flip_vertical" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.album.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical').addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_flip_vertical')
            }.bind(this));" title="<?php echo $this->translate("Flip Vertical"); ?>">&nbsp;</a>
            </div>
            <?php endif ?>
          <div class="albums_viewmedia_info_date">
          	<?php if(isset($this->photo->date_taken) && $this->photo->date_taken &&  $this->photo->date_taken != '0000-00-00 00:00:00'):?>
              <?php echo $this->translate('Taken Date %1$s', $this->timestamp($this->photo->date_taken)); ?>  
            <?php endif;?> 
            <?php echo $this->translate('Added %1$s', $this->timestamp($this->photo->modified_date)) ?>
            <?php if ($this->canTag): ?>
              - <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Tag This Photo'), array('onclick' => 'taggerInstance.begin();')) ?>
            <?php endif; ?>
            <?php if ($this->canEdit): ?>
              - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit'), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('action' => 'edit'))) . "'); return false;")) ?>
                 <?php endif; ?>
                 <?php if ($this->canDelete): ?>
              - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete'), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('action' => 'delete'))) . "'); return false;")) ?>
                 <?php endif; ?>
            <?php if (!$this->message_view): ?>
              <?php if (SEA_PHOTOLIGHTBOX_SHARE): ?>
                - <a href="<?php echo $this->url(Array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true); ?>" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Share") ?></a>
              <?php endif; ?>
              <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>
                - <a href="<?php echo $this->url(Array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>" onclick="showSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Report") ?></a>
                   <?php endif; ?>
                   <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.display.lightbox', 1)) : ?> 
                - <a class="thumbs_photo" href="<?php echo $this->photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $this->photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>");
                return false;' <?php endif; ?>><?php echo $this->translate("Open Photo Viewer");?></a>
                   <?php endif; ?>
                   <?php if (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO): ?>
                - <a href="<?php echo $this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true); ?>" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>');
              return false;" > <?php echo $this->translate("Make Profile Photo") ?></a>
    <?php endif; ?>
              <?php if (SEA_PHOTOLIGHTBOX_MOVETOOTHERALBUM && $this->movetotheralbum): ?>
                - <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'move-to-other-album', 'album' => $this->album->getGuid(), 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Move To Other Album") ?></a>
              <?php endif; ?>

    <?php if (SEA_PHOTOLIGHTBOX_GETLINK): ?>
                - <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'get-link', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Get Link") ?></a>
    <?php endif; ?>

          <?php if (SEA_PHOTOLIGHTBOX_SENDMAIL): ?>
                - <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'tell-a-friend', 'photo' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Tell a Friend") ?></a>
        <?php endif; ?>
        <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>
                <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
                - <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->photo->getPhotoUrl()) . '&file_id=' . $this->photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download') ?></a>
    <?php endif; ?>
            <?php endif; ?>       
            <?php if ($this->canMakeFeatured && $this->allowView): ?>
              - <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->subject()->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")) ?>
              - <a href="javascript:void(0);"  onclick='featuredPhoto();' ><span id="featured_sitealbum_photo" <?php if ($this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitealbum_photo" <?php if (!$this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>
        <?php endif; ?>
          </div>      
        </div>
<?php endif; ?>
    </div>

<div class="albums_viewmedia_left_info">
 <br><h2>    
  <?php if( !$this->message_view): ?>
  <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->htmlLink($this->album, $this->translate($this->album->getTitle()))); ?>
  <?php else: ?>
    <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->album->getTitle()); ?>
  <?php endif; ?>
  </h2>
  <?php if ("" != $this->album->getDescription()): ?>
    <div>
    <p class="photo-description">
   		<b class=" dblock" style="margin-bottom: 5px;"><?php echo $this->translate("DESCRIPTION :") ?></b>
      <?php echo $this->album->getDescription() ?>
    </p>
    </div>
  <?php endif ?>
  
  
<?php
    //RENDER FACEBOOK COMMENT WIDGET IF HE HAS ENABLED THIS.

    if ($enable_facebookse) {

      if (Engine_Api::_()->facebookse()->showFBCommentBox('album') != 1) {
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
      }
      if (Engine_Api::_()->facebookse()->showFBCommentBox('album')) {
        $curr_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        echo $this->content()->renderWidget("facebookse.facebookse-comments", array('module_type' => 'album', 'curr_url' => $curr_url, 'subject' => $this->subject()->getGuid(), 'task' => 1, 'type' => 'album_photo', 'id' => $this->photo->getIdentity()));
      }
    } else {
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    }
    ?>
</div>
          <?php if (empty($this->viewDisplayHR)) : ?>
      <aside class="albums_viewmedia_info_footer">
  <?php if ($this->canMakeFeatured && !$this->allowView): ?>
          <div class="tip">
            <span>
              <?php echo $this->translate("SITEALBUM_PHOTO_VIEW_PRIVACY_MESSAGE"); ?>
            </span>
          </div>
            <?php endif; ?>
        <section class="albums_viewmedia_info_date">
          <div>
            <div class="sitealbum_photoAction"><?php echo $this->translate("Album:") ?> <?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle(), array('title' => $this->album->getTitle())) ?></div>


            <?php if ($this->photo->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)): ?>
              <div class="sitealbum_photoAction"><?php echo $this->translate("Taken at:") ?> <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->photo->seao_locationid, 'resouce_type' => 'seaocore'), $this->photo->location, array('onclick' => 'owner(this);return false', 'title' => $this->photo->location)); ?></div>
            <?php endif; ?>

            <div class="photo_right_content_top_r">
            <?php if(isset($this->photo->date_taken) && $this->photo->date_taken):?>
              <?php if( $this->photo->date_taken != '0000-00-00 00:00:00'):?>
                    <?php echo $this->timestamp($this->photo->date_taken); ?>
                    <span class="timestamp middot">&middot;</span>
                  <?php endif;?>   
              <div class="timestamp seao_postdate"><div class="seao_postdate_tip" style="margin-left: 0;"><?php echo $this->translate('Posted on %1$s', $this->timestamp($this->photo->modified_date)) ?></div></div>
            <?php endif;?>
            </div>
               
            <div class="mbot10 mtop10"><?php echo $this->content()->renderWidget("sitealbum.user-ratings"); ?></div>

            <?php if ($this->canEdit): ?>

                <?php if ($this->canTag): ?>
                <div><i class="sitealbum_tag_icon"></i>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Tag This Photo'), array('onclick' => 'taggerInstance.begin();')) ?>
                </div>
              <?php endif; ?>

    <?php if (SEA_PHOTOLIGHTBOX_EDITLOCATION && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)): ?><div><i class="sitealbum_location_icon"></i>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'edit-location', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Edit Location") ?></a></div> <?php endif; ?>

              <div><i class="sitealbum_edit_icon"></i><?php echo $this->htmlLink(array('route' => 'sitealbum_extended', 'controller' => 'photo', 'action' => 'edit', 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Edit'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'edit', 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")); ?></div>
  <?php endif; ?>

            <?php if ($this->canDelete): ?>
              <div><i class="sitealbum_delete_icon"></i>
                      <?php echo $this->htmlLink(array('route' => 'sitealbum_extended', 'controller' => 'photo', 'action' => 'delete', 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'delete', 'album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")); ?>
              </div>
            <?php endif; ?>

  <?php if ($this->canMakeFeatured && $this->allowView): ?>
              <div><i class="sitealbum_photo_icon"></i><?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->subject()->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")) ?></div>

              <div><i class="sitealbum_featured_icon"></i><a href="javascript:void(0);"  onclick='featuredPhoto();' ><span id="featured_sitealbum_photo" <?php if ($this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitealbum_photo" <?php if (!$this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a></div>
            <?php endif; ?>
          </div>

          <div>

            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.display.lightbox', 1)) : ?> 
              <div><a class="thumbs_photo" href="<?php echo $this->photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $this->photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($this->photo); ?>");
              return false;' <?php endif; ?>><?php echo $this->translate("Open Photo Viewer");?></a></div>
            <?php endif; ?>

            <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?><div><iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
                <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->photo->getPhotoUrl()) . '&file_id=' . $this->photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download') ?></a>
              </div><?php endif; ?>

            <?php if ($this->canEdit) : ?>
              <?php if (SEA_PHOTOLIGHTBOX_MAKEALBUMCOVER && $this->makeAlbumCover): ?>
                <div>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'make-album-cover', 'album' => $this->album->getGuid(), 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Make Album Main Photo") ?></a></div>
                <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_MOVETOOTHERALBUM && $this->movetotheralbum): ?>
                <div>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'move-to-other-album', 'album' => $this->album->getGuid(), 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Move To Other Album") ?></a></div>
                      <?php endif; ?>

              <?php if (SEA_PHOTOLIGHTBOX_GETLINK): ?>
                <div><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'get-link', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Get Link") ?></a></div>
              <?php endif; ?>

                      <?php if (SEA_PHOTOLIGHTBOX_SENDMAIL): ?>
                <div><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'tell-a-friend', 'photo' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Tell a Friend") ?></a></div>
    <?php endif; ?>
            <?php endif; ?>

  <?php if (!$this->message_view && !empty($this->viewer_id)): ?>
                <?php if ((SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.makeprofile.photo', 1)) || $this->viewer_id == $this->photo->owner_id): ?><div><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>');
              return false;" > <?php echo $this->translate("Make Profile Photo") ?></a></div><?php endif; ?>

                   <?php if (SEA_PHOTOLIGHTBOX_SHARE): ?><div>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Share") ?></a></div><?php endif; ?>

                   <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?><div><a href="javascript:void(0);" onclick="showSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
              return false;" > <?php echo $this->translate("Report") ?></a></div><?php endif; ?>
              <?php endif; ?>
          </div>

          <div>
                 <?php if ($this->canEdit): ?>
              <div class="albums_viewmedia_info_actions">
                <a class="buttonlink icon_photos_rotate_ccw" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.sitealbum.rotate(<?php echo $this->photo->getIdentity() ?>, 90).addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_rotate_ccw')
            }.bind(this));" title="<?php echo $this->translate("Rotate Left"); ?>">&nbsp;</a>
                <a class="buttonlink icon_photos_rotate_cw" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.sitealbum.rotate(<?php echo $this->photo->getIdentity() ?>, 270).addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_rotate_cw')
            }.bind(this));" title="<?php echo $this->translate("Rotate Right"); ?>" >&nbsp;</a>
                <a class="buttonlink icon_photos_flip_horizontal" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.sitealbum.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal').addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_flip_horizontal')
            }.bind(this));" title="<?php echo $this->translate("Flip Horizontal"); ?>" >&nbsp;</a>
                <a class="buttonlink icon_photos_flip_vertical" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');
            en4.sitealbum.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical').addEvent('complete', function() {
              this.set('class', 'buttonlink icon_photos_flip_vertical')
            }.bind(this));" title="<?php echo $this->translate("Flip Vertical"); ?>">&nbsp;</a>
              </div>
      <?php endif ?>
          </div> 
        </section>      
      </aside>
    <?php endif; ?>

  </div>
</div>

<script type="text/javascript">
      var taggerInstance;
      var defaultLoad = true;
      var existingTags =<?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>;
      function getTaggerInstanceSitealbum() {
        taggerInstance = new SEAOTagger('media_photo_next', {
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
        taggerInstance.addEvents({
          'onBegin': function() {
            $('media_photo_next').setProperty('onclick', 'return false;');
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
        if (defaultLoad) {
          if (e.target.get('tag') == 'html' ||
                  e.target.get('tag') == 'body') {
            if (e.key == 'right') {
              photoPaginationDefaultView(getNextPhotoDefault());
            } else if (e.key == 'left') {
              photoPaginationDefaultView(getPrevPhotoDefault());
            }
          }
        }
      });

      function getPrevPhotoDefault() {
        return '<?php echo $this->escape($this->previousPhoto->getHref()) ?>';
      }
      function getNextPhotoDefault() {
        return '<?php echo $this->escape($this->nextPhoto->getHref()) ?>';
      }

      var photoPaginationDefaultView = function(url)
      {

        if (history.replaceState)
          history.replaceState({}, document.title, url);
        else {
          window.location.hash = url;
        }
        $('media_photo').src = "<?php echo $baseUrl ?>application/modules/Sitealbum/externals/images/loader.gif";
        $('media_photo').style.marginTop = '150px';
        en4.core.request.send(new Request.HTML({
          url: url,
          data: {
            format: 'html',
            showbuttons: '<?php echo $this->showbuttons ?>',
            viewDisplayHR: '<?php echo $this->viewDisplayHR ?>',
            isajax: 1
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('default_image_div').innerHTML = responseHTML;
            if (typeof FB != 'undefined') {
              FB.XFBML.parse();
            }
          }
        }));
      };
      function showSmoothBox(url)
      {

        Smoothbox.open(url);
        parent.Smoothbox.close;
      }
</script>
<script type="text/javascript">
  function featuredPhoto()
  {
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'sitealbum/photo/featured',
      'data': {
        format: 'html',
        'subject': '<?php echo $this->subject()->getGuid() ?>'
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('featured_sitealbum_photo').style.display == 'none') {
          $('featured_sitealbum_photo').style.display = "";
          $('un_featured_sitealbum_photo').style.display = "none";
        } else {
          $('un_featured_sitealbum_photo').style.display = "";
          $('featured_sitealbum_photo').style.display = "none";
        }
      }
    }));

    return false;
  }

</script>
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>