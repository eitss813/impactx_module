<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<style type="text/css">
  div.thumb_photo{
    height:  <?php echo $this->photoHeight; ?>px !important;
    width: <?php echo $this->photoWidth; ?>px !important; 
  }
  div.thumb_photo a.thumb_img {
    width: <?php echo $this->photoWidth; ?>px !important; 
    height:  <?php echo $this->photoHeight; ?>px !important; 
    background-size: cover ;
  }
</style>

<?php
if ($this->showLightBox):
  include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
$i = 0;
?>
<?php if ($this->count > 0): ?>
  <?php
  if ($this->photoWidth > $this->normalLargePhotoWidth):
    $photo_type = 'thumb.main';
  elseif ($this->photoWidth > $this->normalPhotoWidth):
    $photo_type = 'thumb.medium';
  else:
    $photo_type = 'thumb.normal';
  endif;
  ?>
  <?php if (empty($this->is_ajax)) : ?>
    <div id='photo_image_recent' class="seaocore_photo_strips">
    <?php endif; ?>		
    <?php foreach ($this->paginator as $photo): ?>
      <div class="thumb_photo">
        <?php $type = ($photo->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type ?>
         <?php echo $this->itemBackgroundPhoto($photo, $type, $photo->getTitle() ,array(
          'class' => 'thumb_img',
          'href' => $photo->getHref(),
          'tag' => 'a',
          'onclick' => $this->showLightBox ? 'openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo
            Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array(offset => $i))) ?>");return false;' : '',
          ));
          ?>
        <?php if ($this->canEdit): ?>
          <div id='hide_<?php echo $photo->getIdentity(); ?>' class="photo_hide">
            <a href="javascript:void(0);" title="<?php echo $this->translate('Hide this photo'); ?>" onclick="hideAlbumPhoto(<?php echo $photo->photo_id; ?>);" ></a>

          </div>
        <?php endif; ?>
      </div>
      <?php $i++; ?>
    <?php endforeach; ?>
    <?php for ($i = $this->hidePhoto; $i < $this->limit - $this->count; $i++): ?>
      <div class="thumb_photo">
      </div>
    <?php endfor; ?>
    <?php if ($this->hidePhoto && $this->canEdit && $this->count < $this->limit): ?>
      <div class="thumb_photo">
        <a href='javascript:void(0);' class='thumb_img' style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/photoreset.png);' title="<?php echo $this->translate('Reset Photo Strip'); ?>" onclick='opensmoothbox("<?php echo $this->url(array('action' => 'unhide-photo', 'user_id' => $this->subject()->getIdentity()), 'sitealbum_general', true) ?>");
                return false;'>
        </a>
      </div>
    <?php endif; ?>
    <?php if (empty($this->is_ajax)) : ?>
    </div> 
  <?php endif; ?>

  <script type="text/javascript">

          var submit_topageprofile = true;
          function hideAlbumPhoto(photo_id)
          {
            submit_topageprofile = false;
            var params = {
              requestParams:<?php echo json_encode($this->param) ?>
            }
            en4.core.request.send(new Request.HTML({
              method: 'post',
              'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/photo-strips',
              'data': $merge(params.requestParams, {
                format: 'html',
                'subject': '<?php echo $this->subject()->getGuid() ?>',
                isajax: 1,
                //                itemCountPerPage: '<?php echo $this->limit ?>',
                hide_photo_id: photo_id
              }),
              evalScripts: true,
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('photo_image_recent').innerHTML = responseHTML;
              }
            }));

            return false;

          }

          function ShowPhotoPage(pageurl) {
            if (submit_topageprofile) {
              window.location = pageurl;
            }
            else {
              submit_topageprofile = true;
            }
          }


          function opensmoothbox(url) {
            Smoothbox.open(url);
          }
  </script>
<?php endif; ?>

