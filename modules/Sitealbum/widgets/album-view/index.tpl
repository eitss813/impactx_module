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
<?php
if ($this->showPhotosInJustifiedView == 1 && $this->paginator->getCurrentPageNumber() < 2):
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/justifiedGallery.css');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/jquery.justifiedGallery.js');
    ?>
    <script type="text/javascript">
        jQuery.noConflict();
    </script>
<?php endif; ?>
<?php if ($this->paginator->getCurrentPageNumber() < 2):
    ?>
    <style type="text/css">
        .album_view > li{
            margin:<?php echo $this->marginPhoto ?>px;
        }
        .album_view .thumbs_photo > span{
            width: <?php echo $this->photoWidth; ?>px !important; 
            height:  <?php echo $this->photoHeight; ?>px !important; 
            background-size: cover !important;
        }
        .album_view .sitealbum_thumb_info{
            width: <?php echo $this->photoWidth; ?>px !important; 
        }
    </style>
    <?php
endif;
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');
?>
<?php
if (empty($this->is_ajax)):
    if ($this->showLightBox):
        include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
    endif;

    $this->headScript()->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/album_comment.js');
    ?>
<?php endif; ?>

<?php if ($this->comment_view == "false") : ?>
    <?php if (empty($this->is_ajax)) : ?>
        <?php if ($this->mine || $this->canEdit): ?>
            <script type="text/javascript">
                function SortablesInstance() {
                    var SortablesInstance;
                    var isJustifiedView = <?php echo $this->showPhotosInJustifiedView; ?>;
                    var albumElements = null;
                    if (isJustifiedView == 1) {
                        $$('.album_view > div').addClass('sortable');
                    }
                    else {
                        $$('.album_view > li').addClass('sortable');
                    }
                    SortablesInstance = new Sortables($$('.album_view'), {
                        clone: true,
                        constrain: true,
                        //handle: 'span',
                        onComplete: function (e) {
                            albumElements = null;
                            if (isJustifiedView == 1) {
                                jQuery('#photos_layout').justifiedGallery();
                                albumElements = $$('.album_view > div');
                            }
                            else {
                                albumElements = $$('.album_view > li');
                            }
                            
                            var ids = [];
                            albumElements.each(function (el) {
                                ids.push(el.get('id').match(/\d+/)[0]);
                            });
                            // Send request
                            var url = '<?php echo $this->url(array('action' => 'order', 'album_id' => $this->album->album_id), 'sitealbum_specific') ?>';
                            var request = new Request.JSON({
                                'url': url,
                                'data': {
                                    format: 'json',
                                    order: ids
                                }
                            });
                            request.send();
                        }
                    });
                }
            </script>
        <?php endif; ?>
        <script type="text/javascript">
            function viewMorePhoto()
            {
                $('seaocore_view_more').style.display = 'none';
                $('loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'post',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/album-view',
                    'data': $merge(params.requestParams, {
                        format: 'html',
                        'subject': '<?php echo $this->subject()->getGuid() ?>',
                        isajax: 1,
                        page: getNextPage()
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var photocontainer = $('hideResponse_div').getElement('.layout_sitealbum_album_view').innerHTML;
                        isJqueryExist = 'undefined' != typeof window.jQuery;
                        if (isJqueryExist)
                            jQuery('#photos_layout').append(photocontainer);
                        else
                            $('photos_layout').innerHTML = $('photos_layout').innerHTML + photocontainer;
                        $('loding_image').style.display = 'none';
                        $('hideResponse_div').innerHTML = "";
                        if (isJqueryExist)
                            jQuery('#photos_layout').justifiedGallery('norewind');
        <?php if ($this->mine || $this->canEdit): ?>
                            SortablesInstance();
        <?php endif; ?>
                    }
                }));
                return false;
            }

            function showSmoothBox(url)
            {
                Smoothbox.open(url);
                parent.Smoothbox.close;
            }

            var showPhotoToggleContent = function (element_id) {
                var el = $(element_id);
                el.toggleClass('sea_photo_box_open');
                el.toggleClass('sea_photo_box_closed');
            };

        </script>

        <div class="sitealbum_album_photos"> 
            <?php
            if ($this->photoWidth > $this->normalLargePhotoWidth):
                $photo_type = 'thumb.main';
            elseif ($this->photoWidth > $this->normalPhotoWidth):
                $photo_type = 'thumb.medium';
            else:
                $photo_type = 'thumb.normal';
            endif;
            ?>
            <?php if ($this->showPhotosInJustifiedView == 0) : ?>
                <ul  class="thumbs_nocaptions sitealbum_thumbs album_view" id="photos_layout">
                <?php else : ?>
                    <div class="thumbs_nocaptions sitealbum_thumbs album_view" id="photos_layout" >
                        <?php $photo_type = 'thumb.main'; ?>
                    <?php endif ?>
                <?php endif ?> 


                <?php foreach ($this->paginator as $photo): ?>
                    <?php if ($this->showPhotosInJustifiedView == 0) : ?>  
                        <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="o_hidden" style="height:<?php echo $this->columnHeight; ?>px" >
                            <div class="prelative widthfull">     
                                <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo); ?>");
                                            return false;' <?php endif; ?>>

                                <?php
                                   echo $this->itemBackgroundPhoto($photo, ($photo->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle(), array());
                                ?>
                                </a>  
                            <?php else : ?>
                                <div class="prelative" id="thumbs-photo-<?php echo $photo->photo_id ?>" >
                                    <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo); ?>");
                                                return false;' <?php endif; ?>>
                                        <?php echo $this->itemPhoto($photo,($photo->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle());?>
                                    </a>
                                <?php endif; ?>
                                <?php if (($this->viewer()->getIdentity() && (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO || SEA_PHOTOLIGHTBOX_REPORT || SEA_PHOTOLIGHTBOX_SHARE )) || (SEA_PHOTOLIGHTBOX_MOVETOOTHERALBUM && $this->canEdit && $this->movetotheralbum) || (SEA_PHOTOLIGHTBOX_MAKEALBUMCOVER && $this->canEdit && ($this->album->photo_id != $photo->photo_id) && $this->movetotheralbum) || SEA_PHOTOLIGHTBOX_DOWNLOAD): ?> 
                                    <?php if ($this->canEdit): ?>
                                        <div class="sitealbum_thumbs_editalbum">
                                            <a href="javascript:void(0);" onclick="showPhotoToggleContent('photos_options_area')" class="sitealbum_edit_photo">
                                                <i></i>
                                            </a>
                                            <div id="photos_options_area" class="sitealbum_edit_photo_option">

                                                <?php if ($this->viewer()->getIdentity()): ?>
                                                    <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>
                                                        <a class="sitealbum_report" href="javascript:void(0);" onclick="showSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>');
                                                                return false;" ><?php echo $this->translate("Report") ?></a>
                                                       <?php endif; ?>

                                                    <?php if ((SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.makeprofile.photo', 1)) || $this->viewer_id == $photo->owner_id): ?>
                                                        <a class="sitealbum_photo" href="<?php echo $this->url(array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true); ?>" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>');
                                                                return false;" > <?php echo $this->translate("Make Profile Photo") ?></a>
                                                       <?php endif; ?>
                                                   <?php endif; ?>

                                                <?php if (SEA_PHOTOLIGHTBOX_MAKEALBUMCOVER && $this->canEdit && ($this->album->photo_id != $photo->photo_id) && $this->movetotheralbum): ?>
                                                    <a class="sitealbum_photo" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'make-album-cover', 'album' => $this->album->getGuid(), 'photo' => $photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Make Album Main Photo") ?></a>
                                                <?php endif; ?>

                                                <?php if (SEA_PHOTOLIGHTBOX_MOVETOOTHERALBUM && $this->canEdit && $this->movetotheralbum): ?>
                                                    <a class="sitealbum_move" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'photo', 'action' => 'move-to-other-album', 'album' => $this->album->getGuid(), 'photo' => $photo->getGuid(), 'format' => 'smoothbox'), 'default', true); ?>');" > <?php echo $this->translate("Move To Other Album") ?></a>
                                                <?php endif; ?>

                                                <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>
                                                                                                                                                                                                                                                    <!--<iframe src="about:blank" style="display:none" name="downloadframe"></iframe>-->
                                                    <a class="sitealbum_download" href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($photo->getPhotoUrl()) . '&file_id=' . $photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download') ?></a>
                                                <?php endif; ?>

                                                <?php if (!empty($this->viewer_id) && SEA_PHOTOLIGHTBOX_SHARE): ?>
                                                    <a class="sitealbum_share" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => 'album_photo', 'id' => $photo->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), 'default', true); ?>');" > <?php echo $this->translate("Share") ?></a>
                                                <?php endif; ?>
                                            </div> 
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                    <span class="show_photo_des">
                                        <div>
                                            <span class="photo_owner fleft" title ="<?php echo $photo->getTitle(); ?>"><?php echo $photo->getTitle(); ?></span>

                                            <span class="fright sitealbum_photo_count">
                                                <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                                <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                            </span>
                                        </div>
                                    </span>
                                <?php endif; ?>


                            </div>
                            <?php if ($this->showPhotosInJustifiedView == 0) : ?>
                                <div class="sitealbum_thumb_info">
                                    <?php echo $this->albumInfo($photo, $this->photoInfo); ?>
                                </div>
                        </li>    
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty($this->is_ajax) && $this->showPhotosInJustifiedView == 0) : ?>
            </ul>
        <?php else : ?>
        </div>
    <?php endif; ?>
    <?php if (empty($this->is_ajax)) : ?>
        <?php if (empty($this->count)): ?>
            <div style="width:80% !important;margin:0px;">
                <div class="tip">
                    <span>
                        <?php echo $this->translate('There are no photos in this album.') ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <br />
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                'id' => '',
                'class' => 'buttonlink icon_viewmore'
            ))
            ?>
        </div>
        <div class="seaocore_view_more" id="loding_image" style="display: none;">
            <img src='<?php echo $baseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
        </div>
        <div id="hideResponse_div"></div>
    <?php endif;
    ?>

    <script>
    <?php if ($this->mine || $this->canEdit): ?>
            SortablesInstance();
    <?php endif; ?>

        function getNextPage() {
            return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }

        function hideViewMoreLink(showContent) {

            if (showContent == 3) {
                $('seaocore_view_more').style.display = 'none';
                var totalCount = '<?php echo $this->paginator->count(); ?>';
                var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';
                var count = '<?php echo $this->count; ?>';

                function doOnScrollLoadAlbum()
                {
                    if (typeof ($('seaocore_view_more').offsetParent) != 'undefined') {
                        var elementPostionY = $('seaocore_view_more').offsetTop;
                    } else {
                        var elementPostionY = $('seaocore_view_more').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 30)) {
                        if ((totalCount != currentPageNumber) && (count != 0))
                            viewMorePhoto();
                    }
                }

                window.onscroll = doOnScrollLoadAlbum;
            }
            else if (showContent == 2)
            {
                var view_more_content = $('seaocore_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMorePhoto();
                });
            }

        }
    </script>

    <?php if ($this->showContent == 3): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php elseif ($this->showContent == 2): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php else: ?>
        <script type="text/javascript">
            $('seaocore_view_more').style.display = 'none';
        </script>
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => false,
            'query' => $this->params
        ));
        ?>
    <?php endif; ?>
<?php endif; ?>

<?php if (empty($this->is_ajax) && !isset($_GET['otherView'])) : ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl'; ?>
    <?php
endif;
?>
<?php if ($this->showPhotosInJustifiedView == 1 && $this->paginator->getCurrentPageNumber() < 2): ?>
    <script type="text/javascript">
        showJustifiedView('photos_layout',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>, '<?php echo $this->lastRow ?>');
    </script>
<?php endif; ?>