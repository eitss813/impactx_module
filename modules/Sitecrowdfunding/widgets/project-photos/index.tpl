<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<?php if ($this->loaded_by_ajax): ?>
<script type="text/javascript">
    var params = {
        requestParams:<?php echo json_encode($this->params) ?>,
    responseContainer: $$('.layout_sitecrowdfunding_project_photos')
    }
    en4.sitecrowdfunding.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
</script>
<?php endif; ?>
<?php if ($this->showContent): ?>

<a id="sitecrowdfunding_photo_anchor" class="pabsolute"></a>
<script type="text/javascript">
    var sitecrowdfundingPhotoPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
    var paginateSitecrowdfundingPhoto = function (page) {
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
        responseContainer: $$('.layout_sitecrowdfunding_project_photos')
    }
        params.requestParams.content_id = <?php echo sprintf('%d', $this->identity) ?>;
        params.requestParams.page = page;
        en4.sitecrowdfunding.ajaxTab.sendReq(params);

    }
</script>

<?php if ($this->can_edit): ?>
<script type="text/javascript">
    var SortablesInstance;
    var isJustifiedView = <?php echo $this->showPhotosInJustifiedView; ?>;
    var albumElements = null;
    if (isJustifiedView == 1) {
        $$('.thumbs_nocaptions > div').addClass('sortable');
    }
    else {
        $$('.thumbs_nocaptions > li').addClass('sortable');
    }
    en4.core.runonce.add(function () {
        SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
            clone: true,
            constrain: true,
            //handle: 'span',
            onComplete: function (e) {
                if (isJustifiedView == 1) {
                    jQuery('#photos_layout').justifiedGallery();
                    albumElements = $$('.thumbs_nocaptions > div');
                }
                else {
                    albumElements = $$('.thumbs_nocaptions > li');
                }
                var ids = [];
                albumElements.each(function (el) {
                    ids.push(el.get('id').match(/\d+/)[0]);
                });
                // Send request
                var url = en4.core.baseUrl + 'sitecrowdfunding/album/order';
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
<?php $url = $this->url(array('project_id' => $this->project->project_id), "sitecrowdfunding_photoalbumupload", true); ?>
<?php if ($this->total_images): ?>
<?php /*if ($allowedUpload): ?>
<div class="seaocore_add">
    <a href='<?php echo $url; ?>'  class='seaocore_icon_add'><?php echo $this->translate('Add Photos'); ?></a>
    <?php if ($this->can_edit): ?>
    <a href='<?php echo $this->url(array('project_id' => $this->project->project_id), "sitecrowdfunding_albumspecific", true) ?>'  class='seaocore_icon_edit_sqaure'><?php echo $this->translate('Edit Photos'); ?></a>
    <?php endif; ?>
</div>
<?php endif; */ ?>

<?php if ($this->showPhotosInJustifiedView == 0) : ?>
<ul class="sitecrowdfunding_thumbs thumbs_nocaptions" id="photos_layout">
    <?php else : ?>
    <div class="sitecrowdfunding_thumbs thumbs_nocaptions" id="photos_layout" >
        <?php endif; ?>
        <?php foreach ($this->paginator as $image): ?>
        <?php if ($this->showPhotosInJustifiedView == 0) : ?>
        <li id="thumbs-photo-<?php echo $image->photo_id ?>" >
            <div class="prelative">
                <a href="<?php echo $this->url(array('project_id' => $image->project_id, 'photo_id' => $image->photo_id), "sitecrowdfunding_image_specific") ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $this->url(array('project_id' => $image->project_id, 'photo_id' => $image->photo_id), "sitecrowdfunding_image_specific") ?>");
                return false;' <?php endif; ?> class="thumbs_photo" title="<?php echo $this->translate($image->title) ?>">
                <div>

                    <?php

                                        if($image) {
												echo $this->itemPhoto($image, 'thumb.large', $image->getTitle(), array('style' => 'width: <?php echo $this->width; ?>px !important;  height:  <?php echo $this->height; ?>px!important;', 'tag' => 'span'));
                    }




                    ?>
                </div>

                </a>
            </div>
        </li>
        <?php else : ?>
        <div class="prelative" id="thumbs-photo-<?php echo $image->photo_id ?>" >
            <a href="<?php echo $this->url(array('project_id' => $image->project_id, 'photo_id' => $image->photo_id), "sitecrowdfunding_image_specific") ?>" <?php if (SEA_LIST_LIGHTBOX) : ?> onclick='openSeaocoreLightBox("<?php echo $this->url(array('project_id' => $image->project_id, 'photo_id' => $image->photo_id), "sitecrowdfunding_image_specific") ?>");
            return false;' <?php endif; ?> class="thumbs_photo" title="<?php echo $this->translate($image->title) ?>">
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
    <a href='<?php echo $this->url(array('project_id' => $this->project->project_id), "sitecrowdfunding_photoalbumupload", true) ?>'  class='buttonlink icon_sitecrowdfunding_photo_new'><?php echo $this->translate('Add Photos'); ?></a>
</div>
<div class="tip">
                <span>
                    <?php echo $this->translate('You have not added any photo in your project. %1$sClick here%2$s to add your first photo.', "<a href='$url'>", "</a>"); ?>
                </span>
</div>
<?php else : ?>
<?php echo $this->translate('You have not added any photo in your project.'); ?>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->paginator->count() > 1): ?>
<div >
    <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
    <div id="user_group_members_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => 'paginateSitecrowdfundingPhoto(sitecrowdfundingPhotoPage - 1)', 'class' => 'buttonlink icon_previous')); ?>
    </div>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
    <div id="user_group_members_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => 'paginateSitecrowdfundingPhoto(sitecrowdfundingPhotoPage + 1)', 'class' => 'buttonlink_right icon_next')); ?>
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
<style>
    img._site_lazyload.thumb_large.item_photo_sitecrowdfunding_photo.loaded {

        height:190px;
        width:160px;
    }

</style>
