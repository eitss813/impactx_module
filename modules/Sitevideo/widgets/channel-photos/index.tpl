<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
if ($this->showPhotosInJustifiedView == 1 && $this->paginator->getCurrentPageNumber() < 2):
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/justifiedGallery.css');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/jquery.justifiedGallery.js');
    ?>
<?php endif; ?>
<?php if ($this->showPhotosInJustifiedView == 1) : ?>
    <script type="text/javascript">
        jQuery.noConflict();
    </script>
<?php endif; ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>

<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitevideo_channel_photos')
        }
        en4.sitevideo.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>
<?php if ($this->showContent): ?>

    <a id="sitevideo_photo_anchor" class="pabsolute"></a>
    <script type="text/javascript">
        var sitevideoPhotoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
        var paginateSitevideoPhoto = function (page) {
            var params = {
                requestParams:<?php echo json_encode($this->params) ?>,
                responseContainer: $$('.layout_sitevideo_channel_photos')
            }
            params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
            params.requestParams.page = page;
            en4.sitevideo.ajaxTab.sendReq(params);

        }
    </script>

    <?php if ($this->can_edit): ?>
        <script type="text/javascript">
            var SortablesInstance;

            en4.core.runonce.add(function () {
                $$('.thumbs_nocaptions > li').addClass('sortable');
                SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
                    clone: true,
                    constrain: true,
                    //handle: 'span',
                    onComplete: function (e) {
                        var ids = [];
                        $$('.thumbs_nocaptions > li').each(function (el) {
                            ids.push(el.get('id').match(/\d+/)[0]);
                        });
                        //console.log(ids);

                        // Send request
                        var url = en4.core.baseUrl + 'sitevideo/album/order';
                        var request = new Request.JSON({
                            'url': url,
                            'data': {
                                format: 'json',
                                order: ids,
                                'subject': en4.core.subject.guid
                            }
                        });
                        request.send();
                    }
                });
            });
        </script>
    <?php endif; ?>
    <?php $allowedUpload = $this->allowed_upload_photo && Engine_Api::_()->user()->getViewer()->getIdentity(); ?>
    <?php $url = $this->url(array('channel_id' => $this->channel->channel_id), "sitevideo_photoalbumupload", true); ?>
    <?php if ($this->total_images): ?>
        <?php if ($allowedUpload): ?>
            <div class="seaocore_add">
                <a href='<?php echo $url; ?>'  class='seaocore_icon_add'><?php echo $this->translate('Add Photos'); ?></a>
                <?php if ($this->can_edit): ?>
                    <a href='<?php echo $this->url(array('channel_id' => $this->channel->channel_id), "sitevideo_albumspecific", true) ?>'  class='sitevideo_icon_edit'><?php echo $this->translate('Edit Photos'); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($this->showPhotosInJustifiedView == 0) : ?>
            <ul class="sitevideo_thumbs thumbs_nocaptions" id="photos_layout">
            <?php else : ?>
                <div class="sitevideo_thumbs thumbs_nocaptions" id="photos_layout" >
                <?php endif; ?>
                <?php foreach ($this->paginator as $image): ?>
                    <?php if ($this->showPhotosInJustifiedView == 0) : ?> 
                        <li id="thumbs-photo-<?php echo $image->photo_id ?>" >
                            <div class="prelative">
                                <a href="<?php echo $this->url(array('channel_id' => $image->channel_id, 'photo_id' => $image->photo_id), "sitevideo_image_specific") ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $this->url(array('channel_id' => $image->channel_id, 'photo_id' => $image->photo_id), "sitevideo_image_specific") ?>");
                                                            return false;' <?php endif; ?> class="thumbs_photo" title="<?php echo $image->title ?>">
                                                            <?php echo $this->itemBackgroundPhoto($image, 'thumb.large', $image->getTitle(), array('tag' => 'span',)); ?>
                                </a>
                            </div>
                        </li>
                    <?php else : ?>
                        <div class="prelative">
                            <a href="<?php echo $this->url(array('channel_id' => $image->channel_id, 'photo_id' => $image->photo_id), "sitevideo_image_specific") ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $this->url(array('channel_id' => $image->channel_id, 'photo_id' => $image->photo_id), "sitevideo_image_specific") ?>");
                                                        return false;' <?php endif; ?> class="thumbs_photo" title="<?php echo $image->title ?>">
                                <?php echo $this->itemPhoto($image, 'thumb.large', $image->getTitle(), array()); ?>
                            </a>
                        </div>
                    <?php endif; ?>


                <?php endforeach; ?>
                <?php if ($this->showPhotosInJustifiedView == 0) : ?>
            </ul>
        <?php else : ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <?php if ($allowedUpload): ?>
            <div class="seaocore_add">
                <a href='<?php echo $this->url(array('channel_id' => $this->channel->channel_id), "sitevideo_photoalbumupload", true) ?>'  class='buttonlink icon_sitevideos_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
            </div>
            <div class="tip">
                <span>
                    <?php echo $this->translate('You have not added any photo in your channel. %1$sClick here%2$s to add your first photo.', "<a href='$url'>", "</a>"); ?>
                </span>
            </div>
        <?php else : ?>
            <?php echo $this->translate('You have not added any photo in your channel.'); ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->paginator->count() > 1): ?>
        <div >
            <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
                <div id="user_group_members_previous" class="paginator_previous">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSitevideoPhoto(sitevideoPhotoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
                <div id="user_group_members_next" class="paginator_next">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSitevideoPhoto(sitevideoPhotoPage + 1)', 'class' => 'buttonlink_right icon_next')); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if ($this->showPhotosInJustifiedView == 1): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
        showJustifiedView('photos_layout',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>, '<?php echo $this->lastRow ?>');
    });
    </script>
<?php endif; ?>