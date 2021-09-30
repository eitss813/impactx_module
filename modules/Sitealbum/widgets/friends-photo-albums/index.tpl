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
<?php
if ($this->showLightBox):
  include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<?php if (empty($this->is_ajax)) : ?>
  <!--<h3><?php echo $this->translate("Friends’ Photo Albums") ?></h3>-->
  <ul id='friends-photo-albums' class="seaocore_sidebar_list sitealbum_sidebar">
  <?php endif; ?>
  <?php
  foreach ($this->friendsPhoto as $value):
    $itemAlbum = $value['album'];
    ?>
    <li>
      <?php foreach ($value['photo'] as $item): ?>
        <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $item->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item); ?>");
              return false;' <?php endif; ?> title="<?php echo $item->getTitle(); ?>" >
          <?php
            echo $this->itemBackgroundPhoto($item, 'thumb.normal', $item->getTitle(), array());
          ?>
        </a> 
      <?php endforeach; ?>

      <?php if (!empty($this->albumInfo)): ?>
        <div class="sitealbum_sidebar_des">
          <?php if (in_array('albumTitle', $this->albumInfo)): ?>
            <?php echo $this->translate('%1$s', $this->htmlLink($itemAlbum->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($itemAlbum->getTitle(), $this->albumTitleTruncation))); ?>
          <?php endif; ?>

          <?php if (in_array('ownerName', $this->albumInfo)): ?>
            <?php echo $this->translate(' by %1$s', $itemAlbum->getOwner()->__toString()); ?>
          <?php endif; ?>
        </div>

        <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
          <div class="seao_listings_stats">
            <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
            <div title="<?php echo $this->translate(array('%s photo', '%s photos', $itemAlbum->photos_count), $this->locale()->toNumber($itemAlbum->photos_count)) ?>" class="o_hidden">
              <?php echo $this->translate(array('%s photo', '%s photos', $itemAlbum->photos_count), $this->locale()->toNumber($itemAlbum->photos_count)) ?>
            </div>
          </div>
        <?php endif; ?>

        <?php echo $this->albumInfo($itemAlbum, $this->albumInfo, array('truncationLocation' => $this->truncationLocation)); ?>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  <li>
    <div id="friend_album_photo_loding_image" style="display: none;" class="seaocore_sidebar_more_link"> 
      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="" class="sitealbum_sidebar_loader_img" />
    </div>
    <a  id="friend_album_photo_see_more" onclick="SeeMore();" href="javascript:void(0);" class="seaocore_sidebar_more_link"><?php echo $this->translate('See More') ?></a>
  </li>
  <?php if (empty($this->is_ajax)) : ?>
  </ul>
<?php endif; ?>
<script type="text/javascript">
      var submit_topageprofile = true;
      function SeeMore() {
        $('friend_album_photo_see_more').style.display = 'none';
        $('friend_album_photo_loding_image').style.display = 'block';
        submit_topageprofile = false;
        var params = {
          requestParams:<?php echo json_encode($this->params) ?>
        };
        en4.core.request.send(new Request.HTML({
          method: 'post',
          'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/friends-photo-albums',
          'data': $merge(params.requestParams, {
            format: 'html',
            isajax: 1,
          }),
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('friend_album_photo_see_more').style.display = 'block';
            $('friend_album_photo_loding_image').style.display = 'none';
            $('friends-photo-albums').innerHTML = responseHTML;
            var friendPhotocontainer = $('friends-photo-albums').getElement('.layout_sitealbum_friends_photo_albums').innerHTML;
            $('friends-photo-albums').innerHTML = friendPhotocontainer;
          }
        }));

        return false;

      }
</script>