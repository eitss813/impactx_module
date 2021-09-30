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
    
<?php  endif; ?>
<?php
if ($this->showLightBox  && $this->paginator->getCurrentPageNumber() < 2 ):
    include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>

<?php
$normalPhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.photo.width', 375);
$normalLargePhotoWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.photo.width', 720);
$sitealbum_last_photoid = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.last.photoid');
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
?>
<?php if ($this->is_ajax_load): ?>
    <?php if (empty($this->is_ajax)): ?>
        <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1): ?>
                <div class="tabs_alt tabs_parent">
                    <ul id="main_tabs">
                        <?php foreach ($this->tabs as $tab): ?>
                            <?php $class = $tab == $this->activTab ? 'active' : '' ?>
                            <?php
                            $pos = strpos($tab, "photos");
                            $str = substr($tab, 0, $pos);
                            ?>
                            <li class ='<?php echo $class ?>' id ='<?php echo 'sitealbum_' . $tab . '_tab' ?>'>
                                <a href='javascript:void(0);' onclick="tabSwitchSitealbumPhoto('<?php echo $tab; ?>');"><?php echo $this->translate(ucwords(str_replace('_', ' ', $str))); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div id="hideResponse_div" style="display: none;"></div>
            <div id="sitelbum_photos_tabs">
            <?php endif; ?>
            <?php if ($this->paginator->getTotalItemCount() > 0): ?>
                <?php
                if ($this->photoWidth > $normalLargePhotoWidth):
                    $photo_type = 'thumb.main';
                elseif ($this->photoWidth > $normalPhotoWidth):
                    $photo_type = 'thumb.medium';
                else:
                    $photo_type = 'thumb.normal';
                endif;
                ?>
                <?php if ($this->showPhotosInJustifiedView == 1) : ?>
                    <?php if ($this->is_ajax != 2): $photo_type = 'thumb.main';?>
                        <div class="sitealbum_thumbs <?php echo $className ?>" id ="sitealbum_list_tab_photo_content">
                        <?php endif; ?> 
                        <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->activTab_limit; ?>
                        <?php foreach ($this->paginator as $item): ?>
                            <?php
                            if ($this->activTab == 'featured' || $this->activTab == 'random'):
                                $i = 0;
                            endif;
                            ?>
                            <div class="prelative">
                                <a  class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $item->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item, array_merge($this->params, array('offset' => $i))); ?>");
                                            return false;' <?php endif; ?>>
                                    <?php echo $this->itemPhoto($item,($item->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle());?>
                                </a>
                                
                                <?php if (!empty($this->photoInfo)): ?>
                                  <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
                                      <span class="show_photo_des">          
                                          <?php
                                          $owner = $item->getOwner();
                                          $parent = $item->getParent();
                                          ?>
                                          <?php if (in_array('albumTitle', $this->photoInfo)): ?>
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
                                <?php endif; ?>
                                
                            </div> 
                            <?php $i++; ?>
                        <?php endforeach; ?>
                        <?php if ($this->is_ajax != 2): ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if ($this->is_ajax != 2): ?>
                        <ul class="sitealbum_thumbs <?php echo $className ?>" id ="sitealbum_list_tab_photo_content">
                        <?php endif; ?> 
                        <?php $i = ($this->paginator->getCurrentPageNumber() - 1) * $this->activTab_limit; ?>
                        <?php foreach ($this->paginator as $item): ?>
                            <?php
                            if ($this->activTab == 'featured' || $this->activTab == 'random'):
                                $i = 0;
                            endif;
                            ?>
                            <li class="o_hidden" style="margin:<?php echo $this->marginPhoto ?>px;height: <?php echo $this->columnHeight ?>px;">
                                <div class="prelative widthfull">
                                    <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>"  <?php if ($this->showLightBox): ?>
                                           onclick="openLightBoxAlbum('<?php echo $item->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item, array_merge($this->params, array('offset' => $i))) ?>');
                                                   return false;"
                                       <?php endif; ?> >
                                        <?php 
                                       $attributArray=array('width:'.$this->photoWidth.'px !important;','height:'.$this->photoHeight.'px !important;','background-size:cover !important;',);
                                       echo $this->itemBackgroundPhoto($item,($item->photo_id <= $sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle(), array('style' => $attributArray));
                                       ?>
                                    </a>
                                    <?php if (!empty($this->photoInfo)): ?>
                                        <?php if (in_array('ownerName', $this->photoInfo) || in_array('albumTitle', $this->photoInfo)): ?>
                                            <span class="show_photo_des">          
                                                <?php
                                                $owner = $item->getOwner();
                                                $parent = $item->getParent();
                                                ?>
                                                <?php if (in_array('albumTitle', $this->photoInfo)): ?>
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
                                <div class="sitealbum_thumb_info" style="width: <?php echo $this->photoWidth; ?>px !important;">     
                                        <?php if (in_array('photoTitle', $this->photoInfo)): ?>
                                            <span class="thumbs_title bold">
                                                <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                                    <a href="javascript:void(0);" <?php if ($this->showPhotosInLightbox && $this->showLightBox): ?>
                                                           onclick="openLightBoxAlbum('<?php echo $item->getPhotoUrl() ?>', '<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item, array_merge($this->params, array('offset' => $i))) ?>');
                                                                   return false;"
                                                       <?php endif; ?> ><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->photoTitleTruncation); ?></a>
                                                   <?php else: ?>
                                                       <?php echo $this->htmlLink($item, Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->photoTitleTruncation)) ?>
                                                   <?php endif; ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php echo $this->albumInfo($item, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>                
                                    </div>
                                <?php endif; ?>
                            </li>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <div class="tip">
                    <span>
                        <?php echo $this->translate('No photos have been uploaded yet.'); ?>
                        <?php if ($this->canCreate): ?>
                        <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                            <?php echo $this->translate('%1$sClick here%2$s to add photos!', '<a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_photo_lightbox" href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
                        <?php else:?>
                         <?php echo $this->translate('%1$sClick here%2$s to add photos!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
                        <?php endif;?>
                        <?php endif; ?>
                        
                    </span>
                </div>
            <?php endif; ?>
            <?php if (empty($this->is_ajax)): ?>
            </div>
            <div class="clr" id="scroll_bar_height"></div>
            <?php if (!empty($this->showViewMore)): ?>
                <div class="seaocore_view_more mtop10" id="sitealbum_photos_tabs_view_more" onclick="viewMoreTabPhotos()">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'id' => 'sitealbum_photo_viewmore_link',
                        'class' => 'buttonlink icon_viewmore'
                    ))
                    ?>
                </div>
                <div class="seaocore_view_more mtop10" id="sitealbum_photos_tabs_loding_image" style="display: none;">
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
                    <?php echo $this->translate("Loading ...") ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($this->is_ajax)): ?>
        <script type="text/javascript">

            var tabSwitchSitealbumPhoto = function (tabName) {
                var showContent = "<?php echo $this->showContent ?>";
        <?php foreach ($this->tabs as $tab): ?>
                    if ($('<?php echo 'sitealbum_' . $tab . '_tab' ?>')) {
                        $('<?php echo 'sitealbum_' . $tab . '_tab' ?>').erase('class');
                    }
        <?php endforeach; ?>
                if ($('sitealbum_photos_tabs_loding_image'))
                    $('sitealbum_photos_tabs_loding_image').style.display = 'none';
                if ($('sitealbum_' + tabName + '_tab'))
                    $('sitealbum_' + tabName + '_tab').set('class', 'active');
                if ($('sitelbum_photos_tabs')) {
                    $('sitelbum_photos_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/loader.gif" class="sitealbum_loader_img" /></center>';
                }
                if ($('sitealbum_photos_tabs_view_more'))
                    $('sitealbum_photos_tabs_view_more').style.display = 'none';

                var params = {
                    requestParams:<?php echo json_encode($this->param) ?>
                };
                var request = new Request.HTML({
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/list-photos-tabs-view',
                    'data': $merge(params.requestParams, {
                        format: 'html',
                        isajax: 1,
                        loaded_by_ajax: true,
                        tabName: tabName,
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('sitelbum_photos_tabs').innerHTML = responseHTML;
        <?php if (!empty($this->showViewMore)): ?>
                            hideViewMoreLink(showContent);
        <?php endif; ?>
                    showJustifiedView('sitealbum_list_tab_photo_content',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>,'<?php echo $this->lastRow ?>' );
                    if(SmoothboxSEAO)
                     SmoothboxSEAO.bind( $('sitelbum_photos_tabs'));
                    }
                });
                request.send();
            }
        </script>
    <?php endif; ?>



    <?php if (!empty($this->showViewMore)): ?>
        <script type="text/javascript">
            //      en4.core.runonce.add(function() {
            //        hideViewMoreLinkSiteAlbumPhoto();
            //      });

            function getNextPageSiteAlbumPhoto() {
                return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
            }

            //      function hideViewMoreLinkSiteAlbumPhoto() {
            //        if ($('sitealbum_photos_tabs_view_more'))
            //          $('sitealbum_photos_tabs_view_more').style.display = '<?php //echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' )        ?>';
            //      }


            function viewMoreTabPhotos()
            {
                $('sitealbum_photos_tabs_view_more').style.display = 'none';
                $('sitealbum_photos_tabs_loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->param) ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'post',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/list-photos-tabs-view',
                    'data': $merge(params.requestParams, {
                        format: 'html',
                        isajax: 2,
                        tabName: '<?php echo $this->activTab ?>',
                        page: getNextPageSiteAlbumPhoto(),
                        loaded_by_ajax: true,
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var photocontainer = $('hideResponse_div').getElement('.layout_sitealbum_list_photos_tabs_view').innerHTML;
                        isJqueryExist='undefined' != typeof window.jQuery;
                        if (isJqueryExist)
                            jQuery('#sitealbum_list_tab_photo_content').append(photocontainer);
                        else
                            $('sitealbum_list_tab_photo_content').innerHTML = $('sitealbum_list_tab_photo_content').innerHTML + photocontainer;
        <?php if ($this->showContent == 2): ?>
                            if ($('sitealbum_photos_tabs_view_more'))
                                $('sitealbum_photos_tabs_view_more').style.display = 'none';
        <?php endif; ?>
                        if ($('sitealbum_photos_tabs_loding_image'))
                            $('sitealbum_photos_tabs_loding_image').style.display = 'none';
                        $('hideResponse_div').innerHTML = "";
                        if (isJqueryExist) 
                            jQuery('#sitealbum_list_tab_photo_content').justifiedGallery('norewind');
                       
                    }
                }));

                return false;

            }
        </script>
    <?php endif; ?>

    <?php if ($this->showContent == 2): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('sitealbum_photos_tabs_view_more').style.display = 'block';
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });</script>
    <?php elseif ($this->showContent == 1): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                $('sitealbum_photos_tabs_view_more').style.display = 'block';
                hideViewMoreLink('<?php echo $this->showContent; ?>');
            });
        </script>
    <?php endif; ?>

    <script type="text/javascript">

        function hideViewMoreLink(showContent) {
            if (showContent == 2) {
                $('sitealbum_photos_tabs_view_more').style.display = 'none';
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
                            viewMoreTabPhotos();
                        }
                    }
                }
                window.onscroll = doOnScrollLoadPage;
            } else if (showContent == 1)
            {
                var view_more_content = $('sitealbum_photos_tabs_view_more');
                view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
                view_more_content.removeEvents('click');
                view_more_content.addEvent('click', function () {
                    viewMoreTabPhotos();
                });
            }
        }
    </script>

<?php else: ?>

    <div id="layout_sitealbum_list_photos_tabs_view<?php echo $this->identity; ?>">
        <div class="layout_core_container_tabs">
            <div class="tabs_alt tabs_parent">
                <ul id="main_tabs">
                    <?php foreach ($this->tabs as $tab): ?>
                        <?php $class = $tab == $this->activTab ? 'active' : '' ?>
                        <?php
                        $pos = strpos($tab, "photos");
                        $str = substr($tab, 0, $pos);
                        ?>
                        <li class ='<?php echo $class ?>' id ='<?php echo 'sitealbum_' . $tab . '_tab' ?>'>
                            <a href='javascript:void(0);' onclick="tabSwitchSitealbumPhoto('<?php echo $tab; ?>');"><?php echo $this->translate(ucwords(str_replace('_', ' ', $str))); ?></a>
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
                    requestParams: $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>','justifiedViewId': 'sitealbum_list_tab_photo_content'}),
                    responseContainer: [$('layout_sitealbum_list_photos_tabs_view<?php echo $this->identity; ?>')]
                });
            });
        </script>
    <?php else: ?>

        <script type="text/javascript">
            var requestParams = $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>'})
            var params = {
                'detactLocation': <?php echo $this->detactLocation; ?>,
                'responseContainer': 'layout_sitealbum_list_photos_tabs_view<?php echo $this->identity; ?>',
                requestParams: requestParams
            };

            en4.seaocore.locationBased.startReq(params);
        </script> 
    <?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
        $$('.core_main_album').getParent().addClass('active');
    </script>