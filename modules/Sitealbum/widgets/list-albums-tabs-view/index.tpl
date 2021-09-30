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

<?php if ($this->is_ajax_load): ?>
    <?php if (($this->paginator->getTotalItemCount() > 0) && empty($this->is_ajax) && !empty($this->titleLink)): ?>
        <span class="fright sitealbum_top_link">
            <?php echo $this->translate($this->titleLink); ?>
        </span>
    <?php endif; ?>

    <?php if (empty($this->is_ajax)): ?>
        <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1): ?>
                <div class="tabs_alt tabs_parent">
                    <ul id="main_tabs">
                        <?php foreach ($this->tabs as $tab): ?>
                            <?php $class = $tab == $this->activTab ? 'active' : '' ?>
                            <?php
                            $pos = strpos($tab, "albums");
                            $str = substr($tab, 0, $pos);
                            ?>
                            <li class = '<?php echo $class ?>'  id = '<?php echo 'sitealbum_' . $tab . '_tab'.$this->identity ?>'>
                                <a href='javascript:void(0);'  onclick="tabSwitchSitealbum_<?php echo $this->identity; ?>('<?php echo $tab; ?>');"><?php echo $this->translate(ucwords(str_replace('_', ' ', $str))); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div id="hideResponse_div" style="display: none;"></div>
            <div id="sitelbum_albums_tabs_<?php echo $this->identity; ?>">   
            <?php endif; ?>
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
                <?php if ($this->is_ajax != 2): ?>

                    <ul class="thumbs sitealbum_thumbs <?php if ($this->infoOnHover): ?> sitealbum_view_onhover <?php endif; ?>" id ="sitealbum_list_tab_album_content">
                    <?php endif; ?>
                    <?php foreach ($this->paginator as $album): ?>
                        <li class="o_hidden" style="margin:<?php echo $this->marginPhoto ?>px;<?php if ($this->infoOnHover): ?>height: <?php echo $this->photoHeight ?>px;<?php else: ?>height: <?php echo $this->columnHeight ?>px; <?php endif; ?>" >
                            <?php if ($this->infoOnHover): ?>
                                <?php
                                $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $album->getHref());
                                $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $album->getHref();
                                ?>
                                <div class="seao_share_links">
                                    <div class="social_share_wrap">
                                        <?php if ((!empty($this->albumInfo) && in_array('facebook', $this->albumInfo))): ?>
                                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook"></a>
                                        <?php endif; ?>
                                        <?php if ((!empty($this->albumInfo) && in_array('twitter', $this->albumInfo))): ?>
                                            <a href="https://twitter.com/share?text='<?php echo $album->getTitle(); ?>'" target="_blank" class="seao_icon_twitter"></a>
                                        <?php endif; ?>
                                        <?php if ((!empty($this->albumInfo) && in_array('linkedin', $this->albumInfo))): ?>
                                            <a href="https://www.linkedin.com/shareArticle?mini=true&url='<?php echo $object_link; ?>'" target="_blank" class="seao_icon_linkedin"></a>
                                        <?php endif; ?>
                                        <?php if ((!empty($this->albumInfo) && in_array('google', $this->albumInfo))): ?>
                                            <a href="https://plus.google.com/share?url='<?php echo $urlencode; ?>'&t=<?php echo $album->getTitle(); ?>" target="_blank" class="seao_icon_google_plus"></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($album->photo_id): ?>
                                <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
                                <?php 
                                  echo $this->itemBackgroundPhoto($album,($album->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $album->getTitle(), array('class' => "$className"));
                                ?>
                                </a>
                            <?php else: ?>
                                <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" >  
                                 <?php 
                                 echo  $this->itemBackgroundPhoto($album,'thumb.normal', $album->getTitle(), array('class' => "$className",'id' => "sitealbum_".$album->album_id));
                                ?>
                                                              
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($this->albumInfo)) : ?>
                                <div <?php if($this->infoOnHover):?> onclick="openAlbumViewPage('<?php echo $album->getHref(); ?>');" <?php endif;?> class="sitealbum_thumb_info" style="width: <?php echo $this->photoWidth; ?>px ;">
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
                                    </div>

                                    <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                                        <div class="seao_listings_stats">
                                            <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
                                            <div title="<?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php echo $this->albumInfo($album, $this->albumInfo, array('truncationLocation' => $this->truncationLocation, 'infoOnHover' => $this->infoOnHover)); ?>

                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($this->is_ajax != 2): ?>  
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate('No albums have been created yet.'); ?>
                        <?php if ($this->canCreate): ?>
                        <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                            <?php echo $this->translate('%1$sClick here%2$s to add an album!', '<a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_photo_lightbox" href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
                        <?php else:?>
                        <?php echo $this->translate('%1$sClick here%2$s to add an album!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
                        <?php endif;?>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>   
            <?php if (empty($this->is_ajax)): ?>    
            </div>
            <div class="clr" id="scroll_bar_height"></div>
            <?php if (!empty($this->showViewMore)): ?>
                <div class="seaocore_view_more mtop10" id="sitealbum_albums_tabs_view_more" onclick="viewMoreTabAlbum()">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'id' => 'sitealbum_album_viewmore_link',
                        'class' => 'buttonlink icon_viewmore'
                    ))
                    ?>
                </div>
                <div class="seaocore_view_more mtop10" id="sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>" style="display: none;">
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
                    <?php echo $this->translate("Loading ...") ?>
                </div>
              <?php endif; ?>
              </div>
    <?php endif; ?>

    <?php if (empty($this->is_ajax)): ?>
        <script type="text/javascript">

            var tabSwitchSitealbum_<?php echo $this->identity; ?> = function (tabName) {
                var showContent = "<?php echo $this->showContent ?>";
        <?php foreach ($this->tabs as $tab): ?>
                    if ($('<?php echo 'sitealbum_' . $tab . '_tab'.$this->identity ?>')) {
                        $('<?php echo 'sitealbum_' . $tab . '_tab'.$this->identity ?>').erase('class');
                    }
        <?php endforeach; ?>
                if ($('sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>'))
                    $('sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>').style.display = 'none';
                if ($('sitealbum_' + tabName + '_tab<?php echo $this->identity; ?>'))
                    $('sitealbum_' + tabName + '_tab<?php echo $this->identity; ?>').set('class', 'active');
                if ($('sitelbum_albums_tabs_<?php echo $this->identity; ?>')) {
                    $('sitelbum_albums_tabs_<?php echo $this->identity; ?>').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/loader.gif" class="sitealbum_loader_img" /></center>';
                }
                if ($('sitealbum_albums_tabs_view_more'))
                    $('sitealbum_albums_tabs_view_more').style.display = 'none';
                var params = {
                    requestParams:<?php echo json_encode($this->param) ?>
                };
                var request = new Request.HTML({
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/list-albums-tabs-view',
                    'data': $merge(params.requestParams, {
                        format: 'html',
                        isajax: 1,
                        loaded_by_ajax: true,
                        tabName: tabName,
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('sitelbum_albums_tabs_<?php echo $this->identity; ?>').innerHTML = responseHTML;
        <?php if (!empty($this->showViewMore)): ?>
                            //hideViewMoreLinkSiteAlbumAlbum();
                            hideViewMoreLink(showContent);
        <?php endif; ?>
                    }
                });

                request.send();
            }
        </script>
    <?php endif; ?>
    <?php if (!empty($this->showViewMore)): ?>
        <script type="text/javascript">

        //      en4.core.runonce.add(function() {
        //        hideViewMoreLinkSiteAlbumAlbum();
        //      });

            function getNextPageSiteAlbumAlbum() {
                return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
            }

        //      function hideViewMoreLinkSiteAlbumAlbum() {
        //        if ($('sitealbum_albums_tabs_view_more'))
        //          $('sitealbum_albums_tabs_view_more').style.display = '<?php //echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' )      ?>';
        //      }

            function viewMoreTabAlbum()
            {
                $('sitealbum_albums_tabs_view_more').style.display = 'none';
                $('sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->param) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'post',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/list-albums-tabs-view',
                    'data': $merge(params.requestParams, {
                        format: 'html',
                        isajax: 2,
                        loaded_by_ajax: true,
                        tabName: '<?php echo $this->activTab ?>',
                        page: getNextPageSiteAlbumAlbum(),
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var photocontainer = $('hideResponse_div').getElement('.layout_sitealbum_list_albums_tabs_view').innerHTML;
                        $('sitealbum_list_tab_album_content').innerHTML = $('sitealbum_list_tab_album_content').innerHTML + photocontainer;


        <?php if ($this->showContent == 2): ?>
                            if ($('sitealbum_albums_tabs_view_more'))
                                $('sitealbum_albums_tabs_view_more').style.display = 'none';
        <?php endif; ?>
                        if ($('sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>'))
                            $('sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>').style.display = 'none';




                        //$('sitealbum_albums_tabs_loding_image_<?php echo $this->identity; ?>').style.display = 'none';

                        $('hideResponse_div').innerHTML = "";
                    }
                }));

                return false;

            }
        </script>
    <?php endif; ?>

    <?php if ($this->showContent == 2): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('sitealbum_albums_tabs_view_more').style.display = 'block';
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });</script>
    <?php elseif ($this->showContent == 1): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('sitealbum_albums_tabs_view_more').style.display = 'block';
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php endif; ?>

    <script type="text/javascript">

        function hideViewMoreLink(showContent) {
            if (showContent == 2) {
                $('sitealbum_albums_tabs_view_more').style.display = 'none';
                var totalCount = '<?php echo $this->paginator->count(); ?>';
                var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

                function doOnScrollLoadPage()
                {
                    if ($('scroll_bar_height') && typeof ($('scroll_bar_height').offsetParent) != 'undefined') {
                        var elementPostionY = $('scroll_bar_height').offsetTop;
                    } else {
                        var elementPostionY = $('scroll_bar_height').y;
                    }
                    if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 20)) {
                        if ((totalCount != currentPageNumber) && (totalCount != 0)) {
                            viewMoreTabAlbum();
                        }
                    }
                }
                window.onscroll = doOnScrollLoadPage;
            } else if (showContent == 1)
            {
                var view_more_content = $('sitealbum_albums_tabs_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMoreTabAlbum();
                });
            }
        }
    </script>





<?php else: ?>

    <div id="layout_sitealbum_list_albums_tabs_view<?php echo $this->identity; ?>">
        <div class="layout_core_container_tabs">
            <div class="tabs_alt tabs_parent">
                <ul id="main_tabs">
                    <?php foreach ($this->tabs as $tab): ?>
                        <?php $class = $tab == $this->activTab ? 'active' : '' ?>
                        <?php
                        $pos = strpos($tab, "albums");
                        $str = substr($tab, 0, $pos);
                        ?>
                        <li class = '<?php echo $class ?>'  id = '<?php echo 'sitealbum_' . $tab . '_tab'.$this->identity ?>'>
                            <a href='javascript:void(0);'  onclick="tabSwitchSitealbum_<?php echo $this->identity; ?>('<?php echo $tab; ?>');"><?php echo $this->translate(ucwords(str_replace('_', ' ', $str))); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="seaocore_content_loader"></div>
        </div>
    </div>

    <?php if (!$this->detactLocation): ?>
        <script type="text/javascript">
            window.addEvent('domready', function () {
                en4.sitealbum.ajaxTab.sendReq({
                    loading: false,
                    requestParams: $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                    responseContainer: [$('layout_sitealbum_list_albums_tabs_view<?php echo $this->identity; ?>')]
                });
            });
        </script>
    <?php else: ?>

        <script type="text/javascript">
            var requestParams = $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>'})
            var params = {
                'detactLocation': <?php echo $this->detactLocation; ?>,
                'responseContainer': 'layout_sitealbum_list_albums_tabs_view<?php echo $this->identity; ?>',
                requestParams: requestParams
            };

            en4.seaocore.locationBased.startReq(params);
        </script> 
    <?php endif; ?>
<?php endif; ?>

