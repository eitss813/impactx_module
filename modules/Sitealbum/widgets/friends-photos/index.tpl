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

<?php $className = 'layout_sitealbum_friends_photos_' . $this->identity; ?>

<style type="text/css">
  .<?php echo $className ?> {
    width: <?php echo $this->photoWidth; ?>px !important; 
    height:  <?php echo $this->photoHeight; ?>px!important; 
    background-size: cover !important;
  }
</style>

<?php
if ($this->showLightBox):
  include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>

<?php if (empty($this->is_ajax)) : ?>
  <ul id='friends-photos' class="sitealbum_thumbs thumbs_nocaptions">
  <?php endif; ?>
  <?php
  if ($this->photoWidth > $this->normalLargePhotoWidth):
    $photo_type = 'thumb.main';
  elseif ($this->photoWidth > $this->normalPhotoWidth):
    $photo_type = 'thumb.medium';
  else:
    $photo_type = 'thumb.normal';
  endif;
  ?>
  <?php foreach ($this->friendsPhoto as $item): ?>
    <li>
      <div class="prelative">
        <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick="openLightBoxAlbum('<?php echo $item->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item) ?>');
                return false;" <?php endif; ?> >
        <?php 
        echo $this->itemBackgroundPhoto($item, ($item->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle(), array('class' => "$className"));        
        ?>         
        </a>
        <?php if (!empty($this->photoInfo)): ?>
          <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
            <span class="show_photo_des"> 
              <?php
              $owner = $item->getOwner();
              $parent = $item->getParent();
              if (in_array('albumTitle', $this->photoInfo)):
                ?>
                <div class="photo_title">
                  <?php echo $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(), 25)) ?>
                </div>
              <?php endif; ?>
              <?php if (in_array('ownerName', $this->photoInfo)): ?>
                <div>
                  <span class="photo_owner fleft"><?php echo $this->translate('by %1$s', $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(), 25))); ?></span>
                  <span class="fright sitealbum_photo_count">
                    <span class="photo_like">  <?php echo $item->like_count; ?></span> 
                    <span class="photo_comment">  <?php echo $item->comment_count; ?></span>
                  </span>
                </div>
              <?php endif; ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="sitealbum_thumb_info">
          <?php if (in_array('photoTitle', $this->photoInfo)): ?>
            <span class="thumbs_title bold">
              <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->photoTitleTruncation)) ?>
            </span>
          <?php endif; ?>

          <?php echo $this->albumInfo($item, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>  
        </div>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  <div>
    <div id="friend_photo_loding_image" style="display: none;" class="seaocore_sidebar_more_link clr mtop5"> 
      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="" class="sitealbum_sidebar_loader_img" />
    </div>
    <a id="friend_photo_see_more" onclick="SeeMorePhoto();" href="javascript:void(0);" class="seaocore_sidebar_more_link clr mtop5"><?php echo $this->translate('See More') ?></a>
  </div>
  <?php if (empty($this->is_ajax)) : ?>
  </ul>
<?php endif; ?>
<script type="text/javascript">
          var submit_topageprofile = true;
          function SeeMorePhoto()
          {
            submit_topageprofile = false;
            $('friend_photo_see_more').style.display = 'none';
            $('friend_photo_loding_image').style.display = 'block';
            var params = {
              requestParams:<?php echo json_encode($this->params) ?>
            };
            en4.core.request.send(new Request.HTML({
              'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/friends-photos',
              'data': $merge(params.requestParams, {
                format: 'html',
                isajax: 1,
              }),
              evalScripts: true,
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('friend_photo_see_more').style.display = 'block';
                $('friend_photo_loding_image').style.display = 'none';
                $('friends-photos').innerHTML = responseHTML;
                var friendPhotoscontainer = $('friends-photos').getElement('.layout_sitealbum_friends_photos').innerHTML;
                $('friends-photos').innerHTML = friendPhotoscontainer;
              }
            }));

            return false;

          }
</script>