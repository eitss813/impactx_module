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
<?php $className = 'list_albums_tabs_view' . $this->identity; ?>
<style type="text/css">
    .layout_sitealbum_my_albums_sitealbum .thumbs_photo span{
        width: <?php echo $this->photoWidth; ?>px !important; 
        height:<?php echo $this->photoHeight; ?>px!important; 
        background-size: cover;
        display:block;
    }
</style>
<?php if ($this->album_view_type == 2): ?>
    <style>
        .<?php echo $className ?> {
            width: <?php echo $this->photoWidth; ?>px !important; 
            height:  <?php echo $this->photoHeight; ?>px!important; 
            background-size: cover !important;
        }
    </style>
    <?php
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
    ?>
<?php endif; ?>
<?php if ($this->is_ajax_load): ?>
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <?php
        if ($this->photoWidth > $this->normalLargePhotoWidth):
            $photo_type = 'thumb.main';
        elseif ($this->photoWidth > $this->normalPhotoWidth):
            $photo_type = 'thumb.medium';
        else:
            $photo_type = 'thumb.normal';
        endif;
        ?>
        <ul class='<?php echo ($this->album_view_type == 2) ? 'thumbs sitealbum_thumbs sitealbum_view_onhover' : 'albums_manage'; ?>' id="albums_manage"> 
            <?php foreach ($this->paginator as $album): ?>
                <?php if ($this->album_view_type == 2): ?>
                    <li style="margin:<?php echo $this->margin_photo; ?>px;height: <?php echo $this->photoHeight ?>px;" >
                        <div class="seao_share_links">
                        <div class="social_share_wrap">
                            
                            <?php if ((!empty($this->albumInfo) && in_array('facebook', $this->albumInfo)) || empty($this->albumInfo)): ?>
                             <?php
            $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $album->getHref());
            $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $album->getHref();
            ?>
                                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook"></a>
                            <?php endif; ?>
                            <?php if ((!empty($this->albumInfo) && in_array('twitter', $this->albumInfo)) || empty($this->albumInfo)): ?>
                                <a href="https://twitter.com/share?text='<?php echo $album->getTitle(); ?>'" target="_blank" class="seao_icon_twitter"></a>
                            <?php endif; ?>
                            <?php if ((!empty($this->albumInfo) && in_array('linkedin', $this->albumInfo)) || empty($this->albumInfo)): ?>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url='<?php echo $object_link; ?>'" target="_blank" class="seao_icon_linkedin"></a>
                            <?php endif; ?>
                            <?php if ((!empty($this->albumInfo) && in_array('google', $this->albumInfo)) || empty($this->albumInfo)): ?>
                                <a href="https://plus.google.com/share?url='<?php echo $urlencode; ?>'&t=<?php echo $album->getTitle(); ?>" target="_blank" class="seao_icon_google_plus"></a>
                            <?php endif; ?>
                        </div>
                    </div>

                        <?php if ($album->photo_id): ?>
                            <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
                            <?php 
                              echo $this->itemBackgroundPhoto($album, ($album->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $album->getTitle(), array('class' => "$className"));
                            ?>
                            </a>
                        <?php else: ?>
                            <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" >   
                            <?php 
                              echo $this->itemBackgroundPhoto($album,'thumb.normal', $album->getTitle(), array('class' => "$className"));
                            ?>
                            
                            </a>
                        <?php endif; ?>



                        <?php if (!empty($this->albumInfo)) : ?>
                            <div class="sitealbum_thumb_info" style="width: <?php echo $this->photoWidth; ?>px" <?php if($this->infoOnHover):?> onclick="openAlbumViewPage('<?php echo $album->getHref(); ?>');" <?php endif;?>>
                                <div class="thumbs_info mtop5">
                                    <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                                        <span class="thumbs_title bold">
                                            <?php echo $this->htmlLink($album, Engine_Api::_()->seaocore()->seaocoreTruncateText($album->getTitle(), $this->albumTitleTruncation)) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (in_array('ownerName', $this->albumInfo)): ?>
                                        <span class="dblock mtop5">
                                            <?php echo $this->translate('by %1$s', $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author'))) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                                        <span title="<?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?>" class="o_hidden bold"><?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?></span>
                                    <?php endif; ?>    
                                    <?php
                                    $statistics = '<span> . </span>';

                                    if (!empty($this->albumInfo) && in_array('viewCount', $this->albumInfo)) {
                                        $statistics .= $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)) . '<span> . </span>';
                                    }

                                    if (!empty($album) && !empty($this->albumInfo) && in_array('likeCount', $this->albumInfo)) {
                                        $statistics .= $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count)) . '<span> . </span>';
                                    }

                                    if (!empty($this->albumInfo) && in_array('commentCount', $this->albumInfo)) {
                                        $statistics .= $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count));
                                    }

                                    $statistics = trim($statistics);
                                    $statistics = rtrim($statistics, ',');
                                    if (!empty($statistics)) {
                                        echo '<span title="' . $this->translate("Statistics") . '" class="o_hidden bold">' . $statistics . '</span>';
                                    }
                                    ?>
                                </div>
                                <?php echo $this->albumInfo($album, $this->albumInfo, array('truncationLocation' => $this->truncationLocation, 'infoOnHover' => $this->infoOnHover,'doNotShowStatistics'=>true)); ?>

                                <div class="albums_manage_options seao_listings_stats">
                                    <?php echo $this->htmlLink(array('route' => 'sitealbum_specific', 'action' => 'editphotos', 'album_id' => $album->album_id), $this->translate(''), array('class' => 'icon_photos_manage', 'title' => 'Manage Photos')); ?>
                                    <?php echo $this->htmlLink(array('route' => 'sitealbum_specific', 'action' => 'edit', 'album_id' => $album->album_id), $this->translate(''), array('class' => 'icon_photos_settings', 'title' => 'Edit')); ?>
                                    <?php echo $this->htmlLink(array('route' => 'sitealbum_specific', 'action' => 'delete', 'album_id' => $album->album_id, 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox icon_photos_delete', 'title' => 'Delete Album')); ?>
                                </div> 

                            </div>
                        <?php endif; ?>
                    </li>
                <?php else : ?>
                    <li>
                        <div class="albums_manage_photo">
                            <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
                            <?php
                               echo $this->itemBackgroundPhoto($album,($album->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $album->getTitle(), array('class' => "aaf-feed-photo-5-big"));
                            ?>
                            </a>
                        </div>
                        <div class="albums_manage_options">
                            <?php echo $this->htmlLink(array('route' => 'sitealbum_specific', 'action' => 'editphotos', 'album_id' => $album->album_id), $this->translate('Manage Photos'), array('class' => 'buttonlink icon_photos_manage')); ?>
                            <?php echo $this->htmlLink(array('route' => 'sitealbum_specific', 'action' => 'edit', 'album_id' => $album->album_id), $this->translate('Edit'), array('class' => 'buttonlink icon_photos_settings')); ?>
                            <?php echo $this->htmlLink(array('route' => 'sitealbum_specific', 'action' => 'delete', 'album_id' => $album->album_id, 'format' => 'smoothbox'), $this->translate('Delete Album'), array('class' => 'buttonlink smoothbox icon_photos_delete')); ?>
                        </div>
                        <div class="albums_manage_info">
                            <?php if (!empty($this->albumInfo)): ?>
                                <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                                    <h3><?php echo $this->htmlLink($album->getHref(), $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($album->getTitle(), $this->albumTitleTruncation))); ?></h3>
                                <?php endif; ?>

                                <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                                    <div class="seao_listings_stats">
                                        <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
                                        <div title="<?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?>" class="o_hidden">
                                            <?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php echo $this->albumInfo($album, $this->albumInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                            <?php endif; ?>

                            <?php if ($album->description && ($this->truncationDescription > 0)): ?>
                                <p class="clr">
                                    <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($album->description, $this->truncationDescription) ?>
                                    <?php if (Engine_String::strlen($album->description) > $this->truncationDescription): ?>
                                        <?php echo $this->htmlLink($album->getHref(), $this->translate('More &raquo;')) ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?php if (empty($this->is_ajax)) : ?>
            <div class = "seaocore_view_more mtop10" id="seaocore_view_more">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => '', 'class' => 'buttonlink icon_viewmore')); ?>
            </div>
            <div class="seaocore_view_more" id="loding_image" style="display: none;">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
            </div>
            <div id="hideResponse_div"> </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('You do not have any albums yet.'); ?>
                <?php if ($this->canCreate): ?>
                
                <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                    <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_photo_lightbox" href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
                <?php else:?>
                <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a  href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
                <?php endif;?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        $$('.core_main_album').getParent().addClass('active');
    </script>

    <?php if (empty($this->is_ajax)) : ?>
        <script type="text/javascript">
            function viewMorePhoto()
            {
                $('seaocore_view_more').style.display = 'none';
                $('loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'get',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/my-albums-sitealbum',
                    data: $merge(params.requestParams, {
                        format: 'html',
                        subject: en4.core.subject.guid,
                        page: getNextPage(),
                        isajax: 1,
                        loaded_by_ajax: true
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var photocontainer = $('hideResponse_div').getElement('.albums_manage').innerHTML;
                        $('albums_manage').innerHTML = $('albums_manage').innerHTML + photocontainer;
                        $('loding_image').style.display = 'none';
                        $('hideResponse_div').innerHTML = "";
                    }
                }));
                return false;
            }
        </script>
    <?php endif; ?>

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
            en4.core.runonce.add(function () {
                $('seaocore_view_more').style.display = 'none';
            });
        </script>
        <?php
//      echo $this->paginationControl($this->paginator, null, null);
        echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitealbum"), array("orderby" => $this->orderby));
        ?>
    <?php endif; ?>


    <script type="text/javascript">

        var pageAction = function (page) {
            window.location.href = en4.core.baseUrl + 'albums/manage/page/' + page;
        }

        function getNextPage() {
            return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }

        function hideViewMoreLink(showContent) {
            if (showContent == 3) {
                $('seaocore_view_more').style.display = 'none';
                var totalCount = '<?php echo $this->paginator->count(); ?>';
                var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

                function doOnScrollLoadAlbum()
                {
                    if (typeof ($('seaocore_view_more').offsetParent) != 'undefined') {
                        var elementPostionY = $('seaocore_view_more').offsetTop;
                    } else {
                        var elementPostionY = $('seaocore_view_more').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                        if ((totalCount != currentPageNumber) && (totalCount != 0))
                            viewMorePhoto();
                    }
                }
                window.onscroll = doOnScrollLoadAlbum;

            } else if (showContent == 2) {
                var view_more_content = $('seaocore_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMorePhoto();
                });
            }
        }
    </script>
<?php else: ?>

    <div id="layout_sitealbum_my_albums_sitealbum_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
        var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_sitealbum_my_albums_sitealbum_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script> 
<?php endif; ?>