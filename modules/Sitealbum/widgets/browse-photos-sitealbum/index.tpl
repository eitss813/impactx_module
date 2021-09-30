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
if ($this->showPhotosInJustifiedView == 1 && ($this->paginator->getCurrentPageNumber() < 2 || $this->showContent==1)):
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/justifiedGallery.css'); 
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/jquery.justifiedGallery.js');
?>

<script type="text/javascript">

jQuery.noConflict();

</script>
<?php  endif; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<?php
if ($this->showLightBox && ($this->paginator->getCurrentPageNumber() <2  || $this->showContent==1)):
    include_once APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_lightboxPhoto.tpl';
endif;
?>

 <?php 

    if (empty($this->is_ajax)) : ?>
        <script type="text/javascript">
            function viewMorePhoto()
            {
                $('seaocore_view_more').style.display = 'none';
                $('loding_image').style.display = '';
                var params = {
                    requestParams:<?php echo json_encode($this->params); ?>
                };
                en4.core.request.send(new Request.HTML({
                    method: 'post',
                    'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/browse-photos-sitealbum',
                    'data': $merge(params.requestParams, {
                        format: 'html',
                        isajax: 1,
                        page: getNextPage()
                    }),
                    evalScripts: true,
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('hideResponse_div').innerHTML = responseHTML;
                        var photocontainer = $('hideResponse_div').getElement('.layout_sitealbum_browse_photos_sitealbum').innerHTML;                
                        isJqueryExist='undefined' != typeof window.jQuery;
                        if (isJqueryExist) 
                            jQuery('#photos_layout<?php echo $this->identity; ?>').append(photocontainer);
                        else
                            $('photos_layout<?php echo $this->identity; ?>').innerHTML = $('photos_layout<?php echo $this->identity; ?>').innerHTML + photocontainer;
                        $('loding_image').style.display = 'none';
                        $('hideResponse_div').innerHTML = "";
                        if (isJqueryExist) 
                         jQuery('#photos_layout<?php echo $this->identity; ?>').justifiedGallery('norewind');
                        
        }
                }));
                return false;
            }
        </script>
<?php endif; ?>

<?php if ($this->is_ajax_load): ?>
    <?php
    $i = 0;
    ?>
    <?php
    if ($this->photoWidth > $this->normalLargePhotoWidth):
        $photo_type = 'thumb.main';
    elseif ($this->photoWidth > $this->normalPhotoWidth):
        $photo_type = 'thumb.medium';
    else:
        $photo_type = 'thumb.normal';
    endif;
    ?>
    <?php if ($this->showPhotosInJustifiedView == 1) : ?>
        <?php if($this->paginator->getCurrentPageNumber() <2 || $this->showContent==1) : ?>
        <div class="sitealbum_thumbs thumbs_nocaptions" id="photos_layout<?php echo $this->identity; ?>">
            <?php endif; ?>
            <?php if($this->paginator->getTotalItemCount() > 0):?>
            <?php foreach ($this->paginator as $item): ?>
                <div class="prelative">
                     <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $item->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item); ?>");
                                return false;' <?php endif; ?>>
                        <?php echo $this->itemPhoto($item, 'thumb.main', $item->getTitle());?>
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
                    <?php endif; ?>
                </div>
            <?php $i++; ?>
            <?php endforeach; ?>
            <?php else:?>
            <div class="tip">
                <span><?php echo $this->translate("No photos found in this criteria.");?></span>
            </div>
            <?php endif;?>
         <?php if($this->paginator->getCurrentPageNumber() <2 || $this->showContent==1) : ?>
        </div>
        <?php endif; ?>
    <?php else : ?>
        <?php if($this->paginator->getCurrentPageNumber() <2 || $this->showContent==1) : ?>
        <ul class="sitealbum_thumbs thumbs_nocaptions" id="photos_layout<?php echo $this->identity; ?>">
        <?php endif; ?> 
           <?php foreach ($this->paginator as $item): ?>
                <li>
                    <div class="prelative">
                        <a class="thumbs_photo" href="<?php echo $item->getHref(); ?>" <?php if ($this->showLightBox): ?> onclick='openLightBoxAlbum("<?php echo $item->getPhotoUrl() ?>", "<?php echo Engine_Api::_()->sitealbum()->getLightBoxPhotoHref($item); ?>");
                                    return false;' <?php endif; ?> >
                            
                        <?php 
                        $attributeArray=array( 'height:'.$this->photoHeight.'px !important;', 'width:'.$this->photoWidth.'px !important;','background-size:cover !important;');
                        echo $this->itemBackgroundPhoto($item,($item->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle(), array('style' => $attributeArray));
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
                                <span class="title">
                                    <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->photoTitleTruncation)) ?>
                                </span>
                            <?php endif; ?>
                            <?php echo $this->albumInfo($item, $this->photoInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                        </div>
                    <?php endif; ?> 
                </li>
                <?php $i++; ?>
            <?php endforeach; ?>
        <?php if($this->paginator->getCurrentPageNumber() <2 || $this->showContent==1) : ?>
        </ul>
        <?php endif; ?>
    <?php endif; ?>
    
     <?php if($this->paginator->getCurrentPageNumber() <2) : ?>
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
    <div id="hideResponse_div"></div>
    <?php endif; ?>
<?php else: ?>

    <div id="layout_sitealbum_list_popular_photos_<?php echo $this->identity; ?>">
        <!--    <div class="seaocore_content_loader"></div>-->
    </div>

    <script type="text/javascript">
      
        var requestParams = $merge(<?php echo json_encode($this->param); ?>, {'content_id': '<?php echo $this->identity; ?>'});
        var params = {
            'detactLocation': <?php echo $this->detactLocation; ?>,
            'responseContainer': 'layout_sitealbum_list_popular_photos_<?php echo $this->identity; ?>',
            requestParams: requestParams
        };

        en4.seaocore.locationBased.startReq(params);
    </script> 
<?php endif; ?>
   
<script>
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
   /* echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => false,
        'query' => $this->params
    ));*/
    ?>
         <?php
      echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitealbum"), array(  'pageAsQuery' => false,'query' => $this->params));
      ?>
    <?php endif; ?>

<?php if($this->showPhotosInJustifiedView==1 && ($this->paginator->getCurrentPageNumber() < 2  || $this->showContent==1)): ?>
<script type="text/javascript">
    showJustifiedView('photos_layout<?php echo $this->identity; ?>',<?php echo $this->rowHeight ?>,<?php echo $this->maxRowHeight ?>,<?php echo $this->margin ?>,'<?php echo $this->lastRow ?>' );
</script>
<?php endif; ?>