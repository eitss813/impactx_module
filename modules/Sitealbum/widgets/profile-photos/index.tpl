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
if ($this->showPhotosInJustifiedView == 1 && $this->page == 1):
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/justifiedGallery.css');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/jquery.justifiedGallery.js');
    ?>

    <script type="text/javascript">

        jQuery.noConflict();

    </script>
<?php endif; ?>
<?php
$normalPhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.photo.width', 375);
$normalLargePhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.photo.width', 720);
$sitealbum_last_photoid = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.last.photoid');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js'); ?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css')
?>
<?php
if ($this->page == 1 && $this->showLightBox):
    include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>
<?php if ($this->is_ajax_load): ?>
    <?php if ($this->showviewphotolink == 1 && ($this->page == 1 ) && !empty($this->content_map_id) && ($this->controller == 'profile') && ($this->action == "index")) : ?>
        <h4 style="overflow:hidden;">
            <a href="<?php echo $this->subject->getHref() . '/tab/' . $this->content_map_id . '/show_map_photo/3'; ?>" class="buttonlink sitetagcheckin_icon_map fright" style="font-weight:normal;"><?php echo $this->translate("View Photos on Map"); ?></a>
        </h4>
    <?php endif; ?>

    <script>
        en4.core.runonce.add(function () {
            isJqueryExist = ('undefined' != typeof window.jQuery && '<?php echo $this->showPhotosInJustifiedView == 1; ?>');
            if (isJqueryExist)
                var anchor = jQuery('#profile_albums<?php echo $this->identity ?>').parent();
            else
                var anchor = $('profile_albums<?php echo $this->identity ?>').getParent();

    <?php if ($this->paginator->getTotalItemCount() > 0) : ?>
                $('profile_lists_next_<?php echo $this->identity ?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
                $('seaocore_loading_<?php echo $this->identity ?>').style.display = 'none';
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
                $$('.host_profile_events_links_filter<?php echo $this->identity ?>').removeEvents('click').addEvent('click', function (event) {
                    var el = $(event.target);
                    if (!el.hasClass('host_profile_events_links_filter<?php echo $this->identity ?>') && !el.get('data-page'))
                        el = el.getParent('.host_profile_events_links_filter<?php echo $this->identity ?>');
                    var container;
                    if (el.get('data-page') == 1) {
                        if ($$('.seaocore_profile_list_more<?php echo $this->identity ?>'))
                            $$('.seaocore_profile_list_more<?php echo $this->identity ?>').setStyle('display', 'none');
                        $('profile_albums<?php echo $this->identity ?>').innerHTML = '<div class="seaocore_content_loader"></div>';
                        container = anchor;
                    } else {
                        if (isJqueryExist)
                            container = jQuery('#profile_albums<?php echo $this->identity ?>');
                        else
                            container = $('profile_albums<?php echo $this->identity ?>');
                        $('seaocore_loading_<?php echo $this->identity ?>').style.display = 'block';
                        $('profile_lists_next_<?php echo $this->identity ?>').style.display = 'none';
                    }
                    en4.core.request.send(new Request.HTML({
                        method: 'get',
                        url: en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data: $merge(<?php echo json_encode($this->param); ?>, {
                            format: 'html',
                            subject: en4.core.subject.guid,
                            page: el.get('data-page'),
                            viewType: el.get('data-fillter'),
                            isajax: true,
                            pagination: 1,
                            is_filtering: true,
                            identity: <?php echo $this->identity; ?>,
                            is_ajax_load: true
                        }),
                        evalScripts: true,
                        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                            if (el.get('data-page') == 1) {
                                if (isJqueryExist)
                                    container.html('');
                                else
                                    container.empty();
                            } else {
                                el.set('data-page', (parseInt(el.get('data-page')) + 1));
                            }
                            if (isJqueryExist)
                                container.append(responseHTML);
                            else
                                Elements.from(responseHTML).inject(container);
                            en4.core.runonce.trigger();
                            Smoothbox.bind(container);

                            if (SmoothboxSEAO)
                                SmoothboxSEAO.bind(container);
                            if (el.get('data-fillter') == 'yourphotos' || el.get('data-fillter') == 'likesphotos' || el.get('data-fillter') == 'photosofyou')
                            {
                                if (isJqueryExist)
                                {
                                    var controller = jQuery('#profile_albums<?php echo $this->identity; ?>').data('jg.controller');
                                    if (typeof controller === 'undefined') {
                                        showJustifiedView('profile_albums<?php echo $this->identity; ?>',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>, '<?php echo $this->lastRow ?>');
                                    }
                                    else
                                        jQuery('#profile_albums<?php echo $this->identity; ?>').justifiedGallery('norewind');
                                }

                            }
                        }
                    }));
                });

    <?php endif; ?>
        });

    </script>
    <?php if ($this->paginator->getCurrentPageNumber() < 2) : ?>

        <?php if (count($this->selectDispalyTabs) > 1 && (($this->totalAlbumsCount > 0 && $this->totalPhotosofyouCount > 0) || ($this->totalAlbumsCount > 0 && $this->totalYourphotosCount > 0) || ($this->totalYourphotosCount > 0 && $this->totalPhotosofyouCount > 0 && $this->totalLikesphotosCount > 0) && ($this->totalAlbumsCount > 0 && $this->totalLikesphotosCount > 0) )) : ?>

            <div class="sitealbum_browse_lists_view_options b_medium">
                <div class="fleft">

                    <?php if (in_array('yourphotos', $this->selectDispalyTabs) && ($this->totalYourphotosCount > 0)): ?>
                        <a href="javascript:void(0);"  class="<?php if ($this->viewType == 'yourphotos'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="yourphotos" ><?php
                            if ($this->viewer->getIdentity() == $this->subject->getIdentity()):
                                echo $this->translate('Your Photos');
                            else :
                                echo $this->translate("%s's Photos", ucfirst($this->subject->displayname));
                            endif;
                            ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalYourphotosCount); ?>)</a>
                        <?php if (in_array('photosofyou', $this->selectDispalyTabs) || in_array('albums', $this->selectDispalyTabs || in_array('likesphotos', $this->selectDispalyTabs))): ?>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (in_array('photosofyou', $this->selectDispalyTabs) && ($this->totalPhotosofyouCount > 0)): ?>
                        <a href="javascript:void(0);" class="<?php if ($this->viewType == 'photosofyou'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="photosofyou" ><?php
                            if ($this->viewer->getIdentity() == $this->subject->getIdentity()):
                                echo $this->translate('Photo of You');
                            else :
                                echo $this->translate('Photos of %s', ucfirst($this->subject->displayname));
                            endif;
                            ?> &nbsp;(<?php echo $this->locale()->toNumber($this->totalPhotosofyouCount); ?>)</a> 
                        <?php if (in_array('albums', $this->selectDispalyTabs) || in_array('likesphotos', $this->selectDispalyTabs)): ?>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (in_array('albums', $this->selectDispalyTabs) && ($this->totalAlbumsCount > 0)): ?>
                        <a href="javascript:void(0);"  class="<?php if ($this->viewType == 'albums'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="albums" ><?php echo $this->translate('Albums'); ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalAlbumsCount); ?>)</a>
                        <?php if (in_array('likesphotos', $this->selectDispalyTabs) && $this->totalLikesphotosCount > 0): ?>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php if (in_array('likesphotos', $this->selectDispalyTabs) && ($this->totalLikesphotosCount > 0)): ?>
                        <a href="javascript:void(0);"  class="<?php if ($this->viewType == 'likesphotos'): ?>bold un<?php endif; ?>host_profile_events_links_filter<?php echo $this->identity ?>" data-page = '1' data-fillter="likesphotos" ><?php
                            if ($this->viewer->getIdentity() == $this->subject->getIdentity()):
                                echo $this->translate('Liked Photos');
                            else :
                                echo $this->translate("Liked photos of %s's", ucfirst($this->subject->displayname));
                            endif;
                            ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalLikesphotosCount); ?>)</a>

                    <?php endif; ?>

                </div>

                <?php if ($this->showaddphoto && $this->viewer->getIdentity() == $this->subject->getIdentity() && $this->canCreate): ?>
                    <?php if (Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()): ?>
                        <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" data-SmoothboxSEAOClass="seao_add_photo_lightbox" class="icon_photos_new menu_album_quick album_quick_upload fright seao_smoothbox buttonlink"><?php echo $this->translate("Add Photos") ?></a>
                    <?php else: ?>
                        <a href="<?php echo $this->url(array('action' => 'upload'), 'sitealbum_general', true) ?>" class="icon_photos_new menu_album_quick album_quick_upload fright buttonlink"><?php echo $this->translate("Add Photos") ?></a>

                    <?php endif; ?>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($this->viewType == 'albums'): ?>
        <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
            <ul id="profile_albums<?php echo $this->identity ?>" class="thumbs sitealbum_thumbs <?php if ($this->infoOnHover): ?> sitealbum_view_onhover <?php endif; ?>"> <?php endif; ?>
            <?php if ($this->totalAlbumsCount > 0) : ?>
                <?php
                if ($this->albumPhotoWidth > $normalLargePhotoWidth):
                    $photo_type = 'thumb.main';
                elseif ($this->albumPhotoWidth > $normalPhotoWidth):
                    $photo_type = 'thumb.medium';
                else:
                    $photo_type = 'thumb.normal';
                endif;
                ?>

                <?php foreach ($this->paginator as $album): ?>
                    <li class="o_hidden" style="margin:<?php echo $this->margin_photo ?>px; <?php if ($this->infoOnHover): ?>height: <?php echo $this->albumPhotoHeight ?>px;<?php else: ?>height: <?php echo $this->albumColumnHeight ?>px; <?php endif; ?> width: auto;">
                        <?php if ($this->infoOnHover): ?>

                            <?php
                            $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->subject->getHref());
                            $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->subject->getHref();
                            ?>
                            <div class="seao_share_links">
                                <div class="social_share_wrap">
                                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook"></a>
                                    <a href="https://twitter.com/share?text='<?php echo $this->subject->getTitle(); ?>'" target="_blank" class="seao_icon_twitter"></a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url='<?php echo $object_link; ?>'" target="_blank" class="seao_icon_linkedin"></a>
                                    <a href="https://plus.google.com/share?url='<?php echo $urlencode; ?>'&t=<?php echo $this->subject->getTitle(); ?>" target="_blank" class="seao_icon_google_plus"></a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($album->photo_id): ?>
                            <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
                            <?php
                             $attribute_array=array('width:'.$this->albumPhotoWidth.'px !important;','height:'.$this->albumPhotoHeight.'px!important;','background-size:cover !important;');
                             echo $this->itemBackgroundPhoto($album, ($album->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $album->getTitle(), array('style' => $attribute_array));
                            ?>
                            </a>
                        <?php else: ?>
                            <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" >  
                          <?php
                             $attribute_array=array('width:'.$this->albumPhotoWidth.'px !important;','height:'.$this->albumPhotoHeight.'px!important;','background-size:cover !important;');
                              echo $this->itemBackgroundPhoto($album,'thumb.normal', $album->getTitle(), array('id'=>"sitealbum_".$album->album_id ,'style' => $attribute_array));
                            ?>  
                                                       
                            </a>
                        
                            
                          <?php endif; ?>

                        <div class="sitealbum_thumb_info" style="width: <?php echo $this->albumPhotoWidth; ?>px !important;" <?php if ($this->infoOnHover): ?> onclick="openAlbumViewPage('<?php echo $album->getHref(); ?>');" <?php endif; ?>>
                            <div class="thumbs_info">
                                <?php if (!empty($this->albumInfo) && in_array('albumTitle', $this->albumInfo)): ?>
                                    <span class="thumbs_title">
                                        <?php echo $this->htmlLink($album, Engine_Api::_()->seaocore()->seaocoreTruncateText($album->getTitle(), $this->titleTruncation)) ?>
                                    </span> 
                                <?php endif; ?>
                            </div>
                            <div class="seao_listings_stats">
                                <?php if (!empty($this->albumInfo) && in_array('totalPhotos', $this->albumInfo)): ?>
                                    <i title="Photos" class="seao_icon_strip seao_icon seao_icon_photo"></i>
                                    <div title="<?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php echo $this->albumInfo($album, $this->albumInfo, array('truncationLocation' => $this->truncationLocation, 'infoOnHover' => $this->infoOnHover)); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?> 
        </ul>
    <?php elseif ($this->viewType == 'photosofyou'): ?>
        <?php if ($this->showPhotosInJustifiedView == 1) : ?>
            <?php if ($this->page == 1) : ?>
                <div id="profile_albums<?php echo $this->identity; ?>" class="sitealbum_thumbs" >
                <?php endif; ?>
                <?php if (!empty($this->totalPhotosofyouCount)): ?>
                    <?php
                    $photo_type = 'thumb.main';
                    ?>
                    <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->limit; ?>
                    <?php foreach ($this->paginator as $value): ?>
                        <?php $photo = Engine_Api::_()->getItem('album_photo', $value->resource_id); ?>
                        <div class="prelative">
                            <a href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))); ?>");
                                        return false;' <?php endif; ?>>
                                <?php echo $this->itemPhoto($photo,($photo->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle()); ?>
                            </a>
                            <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                <span class="show_photo_des">
                                    <?php
                                    $owner = $photo->getOwner();
                                    $parent = $photo->getParent();
                                    ?>
                                    <div>
                                        <span class="photo_owner fleft"><?php echo $this->htmlLink($parent->getHref(), $parent->getTitle()); ?></span>
                                        <span class="fright sitealbum_photo_count">
                                            <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                            <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                        </span>
                                    </div>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($this->page == 1) : ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
                <ul id="profile_albums<?php echo $this->identity ?>" class="sitealbum_thumbs"> <?php endif; ?>
                <?php if (!empty($this->totalPhotosofyouCount)): ?>
                    <?php
                    if ($this->photoWidth > $normalLargePhotoWidth):
                        $photo_type = 'thumb.main';
                    elseif ($this->photoWidth > $normalPhotoWidth):
                        $photo_type = 'thumb.medium';
                    else:
                        $photo_type = 'thumb.normal';
                    endif;
                    ?>
                    <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->limit; ?>
                    <?php foreach ($this->paginator as $value): ?>
                        <?php $photo = Engine_Api::_()->getItem('album_photo', $value->resource_id); ?>
                        <li class="o_hidden" style="margin:<?php echo $this->margin_photo ?>px;height: <?php echo $this->photoColumnHeight ?>px;">
                            <div class="prelative widthfull">
                                <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))); ?>");
                                            return false;' <?php endif; ?>>

                                    <?php 
                                      $attribute_array=array('width:'.$this->photoWidth.'px !important;','height:'.$this->photoHeight.'px !important;','background-size:cover !important;');
                                      echo $this->itemBackgroundPhoto($photo, ($photo->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle(), array('style' => $attribute_array));
                                    ?>
                                </a>

                                <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                    <span class="show_photo_des">
                                        <?php
                                        $owner = $photo->getOwner();
                                        $parent = $photo->getParent();
                                        ?>
                                        <div>
                                            <span class="photo_owner fleft"><?php echo $this->htmlLink($parent->getHref(), $parent->getTitle()); ?></span>
                                            <span class="fright sitealbum_photo_count">
                                                <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                                <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                            </span>
                                        </div>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="info">
                                <?php if (!empty($this->photoInfo) && in_array('photoTitle', $this->photoInfo)): ?>
                                    <span class="thumbs_title bold">


                                        <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                            <a href="javascript:void(0);" <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                                   onclick="openLightBoxAlbum('<?php echo $photo->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))) ?>');
                                                           return false;"
                                               <?php endif; ?> ><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation); ?></a>
                                           <?php else: ?>
                                               <?php echo $this->htmlLink($photo, Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation)) ?>
                        <?php endif; ?>

                                    </span>
                                <?php endif; ?>
                                <?php if ($this->photoInfo): ?>
                                    <?php echo $this->albumInfo($photo, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                    <?php endif; ?> 
                            </div>
                        </li>
                        <?php $i++; ?>
                    <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        <?php endif; ?>
    <?php elseif ($this->viewType == 'yourphotos'): ?>

        <?php if ($this->showPhotosInJustifiedView == 1) : ?>
                <?php if ($this->page == 1) : ?>
                <div id="profile_albums<?php echo $this->identity; ?>" class="sitealbum_thumbs ">
                <?php endif; ?>
                <?php if (!empty($this->totalYourphotosCount)): ?>
                    <?php
                    $photo_type = 'thumb.main';
                    ?>
                    <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->limit; ?>
                <?php foreach ($this->paginator as $photo): ?>
                        <div class="prelative">
                            <a href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))); ?>");
                                        return false;' <?php endif; ?>>
                                <?php echo $this->itemPhoto($photo,($photo->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle());?>
                            </a>
                                <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                <span class="show_photo_des">
                                    <?php
                                    $owner = $photo->getOwner();
                                    $parent = $photo->getParent();
                                    ?>
                                    <div>
                                        <span class="photo_owner fleft"><?php echo $this->htmlLink($parent->getHref(), $parent->getTitle()) ?></span>
                                        <span class="fright sitealbum_photo_count">
                                            <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                            <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                        </span>
                                    </div>
                                </span>
                        <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php if ($this->page == 1) : ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
                <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
                <ul id="profile_albums<?php echo $this->identity ?>" class="sitealbum_thumbs"> <?php endif; ?>
                <?php if (!empty($this->totalYourphotosCount)): ?>
                    <?php
                    if ($this->photoWidth > $normalLargePhotoWidth):
                        $photo_type = 'thumb.main';
                    elseif ($this->photoWidth > $normalPhotoWidth):
                        $photo_type = 'thumb.medium';
                    else:
                        $photo_type = 'thumb.normal';
                    endif;
                    ?>
                    <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->limit; ?>
                <?php foreach ($this->paginator as $photo): ?>
                        <li class="o_hidden" style="margin:<?php echo $this->margin_photo ?>px;height: <?php echo $this->photoColumnHeight ?>px;">
                            <div class="prelative widthfull">
                                <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))); ?>");
                                            return false;' <?php endif; ?>>
                                    <?php 
                                        $attribute_array=array('width:'.$this->photoWidth.'px !important;','height:'.$this->photoHeight.'px !important;','background-size:cover !important;');
                                        echo $this->itemBackgroundPhoto($photo,($photo->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle(), array('style' => $attribute_array));
                                    ?>
                                </a>

                                    <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                    <span class="show_photo_des">
                                        <?php
                                        $owner = $photo->getOwner();
                                        $parent = $photo->getParent();
                                        ?>
                                        <div>
                                            <span class="photo_owner fleft"><?php echo $this->htmlLink($parent->getHref(), $parent->getTitle()) ?></span>
                                            <span class="fright sitealbum_photo_count">
                                                <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                                <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                            </span>
                                        </div>
                                    </span>
                    <?php endif; ?>
                            </div>

                            <div class="info">
                                    <?php if (!empty($this->photoInfo) && in_array('photoTitle', $this->photoInfo)): ?>
                                    <span class="thumbs_title bold">
                        <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                            <a href="javascript:void(0);" <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                                   onclick="openLightBoxAlbum('<?php echo $photo->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))) ?>');
                                                           return false;"
                                               <?php endif; ?> ><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation); ?></a>
                                           <?php else: ?>
                                        <?php echo $this->htmlLink($photo, Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation)) ?>
                                    <?php endif; ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($this->photoInfo): ?>
                        <?php echo $this->albumInfo($photo, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                        <?php endif; ?> 
                            </div>
                        </li>
                        <?php $i++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        <?php endif; ?>
    <?php elseif ($this->viewType == 'likesphotos'): ?>
            <?php if ($this->showPhotosInJustifiedView == 1) : ?>
                <?php if ($this->page == 1) : ?>
                <div id="profile_albums<?php echo $this->identity; ?>" class="sitealbum_thumbs" >
                <?php endif; ?>
                <?php if (!empty($this->totalLikesphotosCount)): ?>
                    <?php
                    $photo_type = 'thumb.main';
                    ?>
                <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->limit; ?>
                           <?php foreach ($this->paginator as $photo): ?>
                        <div class="prelative">
                            <a href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))); ?>");
                                        return false;' <?php endif; ?>>
                                <?php echo $this->itemPhoto($photo,($photo->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle()); ?>        
                            </a>
                                <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                <span class="show_photo_des">
                                    <?php
                                    $owner = $photo->getOwner();
                                    $parent = $photo->getParent();
                                    ?>
                                    <div>
                                        <span class="photo_owner fleft"><?php echo $this->htmlLink($parent->getHref(), $parent->getTitle()) ?></span>
                                        <span class="fright sitealbum_photo_count">
                                            <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                            <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                        </span>
                                    </div>
                                </span>
                        <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($this->page == 1) : ?>
                </div>
            <?php endif; ?>
            <?php else : ?>
                <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>
                <ul id="profile_albums<?php echo $this->identity ?>" class="sitealbum_thumbs"> <?php endif; ?>
                <?php if (!empty($this->totalLikesphotosCount)): ?>
                    <?php
                    if ($this->photoWidth > $normalLargePhotoWidth):
                        $photo_type = 'thumb.main';
                    elseif ($this->photoWidth > $normalPhotoWidth):
                        $photo_type = 'thumb.medium';
                    else:
                        $photo_type = 'thumb.normal';
                    endif;
                    ?>
                <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->limit; ?>
                <?php foreach ($this->paginator as $photo): ?>
                        <li class="o_hidden" style="margin:<?php echo $this->margin_photo ?>px;height: <?php echo $this->photoColumnHeight ?>px;">
                            <div class="prelative widthfull">
                                <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $photo->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))); ?>");
                                            return false;' <?php endif; ?>>
                                    <?php 
                                      $attribute_array=array('width:'.$this->photoWidth.'px !important;','height:'.$this->photoHeight.'px!important;','background-size:cover !important;');
                                      echo $this->itemBackgroundPhoto($photo,($photo->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $photo->getTitle(), array('style' => $attribute_array));
                                    ?>
                                </a>

                                    <?php if (!empty($this->photoInfo) && in_array('likeCommentStrip', $this->photoInfo)): ?>
                                    <span class="show_photo_des">
                                        <?php
                                        $owner = $photo->getOwner();
                                        $parent = $photo->getParent();
                                        ?>
                                        <div>
                                            <span class="photo_owner fleft"><?php echo $this->htmlLink($parent->getHref(), $parent->getTitle()) ?></span>
                                            <span class="fright sitealbum_photo_count">
                                                <span class="photo_like">  <?php echo $photo->like_count; ?></span> 
                                                <span class="photo_comment">  <?php echo $photo->comment_count; ?></span>
                                            </span>
                                        </div>
                                    </span>
                    <?php endif; ?>
                            </div>

                            <div class="info">
                                    <?php if (!empty($this->photoInfo) && in_array('photoTitle', $this->photoInfo)): ?>
                                    <span class="thumbs_title bold">
                                           <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                            <a href="javascript:void(0);" <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                                   onclick="openLightBoxAlbum('<?php echo $photo->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($photo, array_merge($this->params, array('offset' => $i))) ?>');
                                                           return false;"
                                               <?php endif; ?> ><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation); ?></a>
                                    <?php else: ?>
                                        <?php echo $this->htmlLink($photo, Engine_Api::_()->seaocore()->seaocoreTruncateText($photo->getTitle(), $this->photoTitleTruncation)) ?>
                                    <?php endif; ?>
                                    </span>
                                <?php endif; ?>

                    <?php if ($this->photoInfo): ?>
                            <?php echo $this->albumInfo($photo, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                        <?php endif; ?> 
                            </div>
                        </li>
                    <?php $i++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

            <?php if ($this->paginator->getCurrentPageNumber() < 2): ?>   
                <?php if ($this->paginator->getTotalItemCount() > 0) : ?>
            <div class="seaocore_profile_list_more<?php echo $this->identity ?>">
                <div id="profile_lists_next_<?php echo $this->identity ?>" class="seaocore_view_more mtop10 host_profile_events_links_filter<?php echo $this->identity ?>" data-page="<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>" data-fillter="<?php echo $this->viewType ?>">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'onclick' => '',
                        'class' => 'buttonlink_right icon_viewmore'
                    ));
                    ?>
                </div>
                <div class="seaocore_loading" id="seaocore_loading_<?php echo $this->identity ?>" style="display: none;">
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->showPhotosInJustifiedView == 1 && $this->paginator->getCurrentPageNumber() < 2): ?>
        <script type="text/javascript">
            showJustifiedView('profile_albums<?php echo $this->identity; ?>',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>, '<?php echo $this->lastRow ?>');
        </script>
    <?php endif; ?>
<?php else: ?>
    <div class="layout_sitealbum_profile_photos<?php echo $this->identity; ?>" id="layout_sitealbum_profile_photos<?php echo $this->identity; ?>">  </div>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            var params = {
                requestParams: $merge(<?php echo json_encode($this->param); ?>, {'justifiedViewId': 'profile_albums<?php echo $this->identity; ?>'}),
                responseContainer: $$('.layout_sitealbum_profile_photos<?php echo $this->identity; ?>')
            };
            en4.sitealbum.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
        });
    </script>

<?php endif; ?>

