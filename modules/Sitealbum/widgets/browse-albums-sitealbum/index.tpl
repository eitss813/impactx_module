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
<div class="browse-album">
  <?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>
  <div id="sitealbum_location_none" style="display: none;"></div>  
  <?php if ($this->is_ajax_load): ?>
    <?php $className = 'browse_albums_sitealbum' . $this->identity; ?>
    <style type="text/css">
      /*  .<?php // echo $className                                                 ?> > li{
          margin:<?php // echo $this->marginPhoto                                                 ?>px;
        }*/

      .<?php echo $className ?> .thumbs_photo > span{
        width: <?php echo $this->photoWidth; ?>px !important; 
        height:  <?php echo $this->photoHeight; ?>px!important; 
        background-size: cover !important;
      }
      .<?php echo $className ?> .sitealbum_thumb_info{
        width: <?php echo $this->photoWidth; ?>px !important;
      }
    </style>
    <?php if (empty($this->is_ajax)) : ?>
      <?php $doNotShowTopContent = 0; ?>

      <?php if ($this->categoryName && !empty($this->categoryObject->top_content)): ?>
        <h4 class="album_browse_lists_view_options_head mbot10" style="display: inherit;">
          <?php echo $this->translate($this->categoryObject->category_name); ?>
        </h4>

        <?php $doNotShowTopContent = 1; ?>
      <?php endif; ?>

      <?php if ($this->category_id && !$this->subcategory_id): ?>
        <div class="album_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('album_category', $this->category_id)->top_content; ?></div>
      <?php elseif ($this->subcategory_id && $this->category_id): ?>
        <div class="album_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('album_category', $this->subcategory_id)->top_content; ?></div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($this->enablePhotoRotation): ?>
      <script>
        var switchAlbum;
      </script>
    <?php endif; ?>

    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
			<form id='filter_form' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'sitealbum_general', true) ?>' style='display: none;'>
					<input type="hidden" id="profile_type" name="profile_type"  value=""/>
					<input type="hidden" id="page" name="page"  value=""/>
					<input type="hidden" id="tag" name="tag"  value=""/>
					<input type="hidden" id="tag_id" name="tag_id"  value=""/>
					<input type="hidden" id="city" name="city"  value=""/>
					<input type="hidden" id="categoryname" name="categoryname"  value=""/>
					<input type="hidden" id="subcategoryname" name="subcategoryname"  value=""/>
					<input type="hidden" id="Latitude" name="Latitude"  value=""/>
					<input type="hidden" id="Longitude" name="Longitude" value=""/>
					<input type="hidden" id="advanced_search" name="advanced_search" value=""/>
			</form>
      <?php if (empty($this->is_ajax)) : ?>
        <div class="sitealbum_browse_lists_view_options b_medium">
          <div class="fleft"> 
            <?php if ($this->categoryName && $doNotShowTopContent != 1): ?>
              <h4 class="album_browse_lists_view_options_head">
                <?php echo $this->translate($this->categoryName); ?>
              </h4>
            <?php endif; ?>
            <?php echo $this->translate(array('%s album found.', '%s albums found.', $this->totalCount), $this->locale()->toNumber($this->totalCount)) ?>
          </div>
        </div>

        
      
      <ul class="thumbs sitealbum_thumbs <?php echo $className ?> <?php if($this->infoOnHover):?> sitealbum_view_onhover <?php endif;?>"  id ="sitealbum_browse_list">    <?php endif; ?>
        <?php
        if ($this->photoWidth > $this->normalLargePhotoWidth):
          $photo_type = 'thumb.main';
        elseif ($this->photoWidth > $this->normalPhotoWidth):
          $photo_type = 'thumb.medium';
        else:
          $photo_type = 'thumb.normal';
        endif;
        ?>
        <?php
        foreach ($this->paginator as $album):
          $album_photo_url = '';
          if ($album->photo_id):
            $photo_item = Engine_Api::_()->getItem('album_photo', $album->photo_id);
            if ($photo_item):
              $album_photo_url = $photo_item->getPhotoUrl(($album->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type);
            endif;
          endif;
          ?> 
          <li class="o_hidden" style="margin:<?php echo $this->marginPhoto ?>px;<?php if($this->infoOnHover):?>height: <?php echo $this->photoHeight ?>px;<?php else:?>height: <?php echo $this->columnHeight ?>px; <?php endif;?>">
            <?php if($this->infoOnHover):?>
            	<?php
            $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $album->getHref());
            $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $album->getHref();
            ?>
            <div class="seao_share_links">
               <div class="social_share_wrap">
                    <?php if (in_array('facebook', $this->albumInfo)): ?>
                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook"></a>
                    <?php endif;?>
                    <?php if (in_array('twitter', $this->albumInfo)): ?>
                    <a href="https://twitter.com/share?text='<?php echo $album->getTitle(); ?>'" target="_blank" class="seao_icon_twitter"></a>
                    <?php endif;?>
                    <?php if (in_array('linkedin', $this->albumInfo)): ?>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url='<?php echo $object_link; ?>'" target="_blank" class="seao_icon_linkedin"></a>
                    <?php endif;?>
                    <?php if (in_array('google', $this->albumInfo)): ?>
                    <a href="https://plus.google.com/share?url='<?php echo $urlencode; ?>'&t=<?php echo $album->getTitle(); ?>" target="_blank" class="seao_icon_google_plus"></a>
                    <?php endif;?>
                </div>
            </div>
              <?php endif;?>
              
            <?php if ($album_photo_url): ?>
              <?php if ($this->enablePhotoRotation) : ?>
                <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" >     
                <?php 
                    $attributeArray=array('id' => "sitealbum_".$album->album_id);
                    if ($album->photos_count > 1)
                    {
                      $attributeArray['onmouseover']= "switchAlbum = false; photoPaginationDefaultView($album->album_id, $album->photo_id);";
                      $attributeArray['onmouseout']= "switchAlbum = true; StopPhotoPaginationDefaultView($album->album_id, $album_photo_url);";
                    }
                    echo $this->itemBackgroundPhoto($album, ($album->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $album->getTitle(), $attributeArray);
                  ?>
                
                
                </a>
              <?php else: ?>
                <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" >     
                 <?php 
                echo $this->itemBackgroundPhoto($album,($album->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $album->getTitle(), array('id' => "sitealbum_".$album->album_id));
                                 
               ?>
                
                </a>
              <?php endif; ?>
            <?php else: ?>
              <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" >  
                <?php 
                echo $this->itemBackgroundPhoto($album,'thumb.normal', $album->getTitle(), array('id' => "sitealbum_".$album->album_id));
                ?>
              </a>
            <?php endif; ?>

            <?php if (!empty($this->albumInfo)) : ?>
              <div class="sitealbum_thumb_info" <?php if($this->infoOnHover):?> onclick="openAlbumViewPage('<?php echo $album->getHref(); ?>');" <?php endif;?>>
                <div class="thumbs_info mtop5">
                  <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                    <span class="thumbs_title bold">
                      <?php echo $this->htmlLink($album, $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($album->getTitle(), $this->albumTitleTruncation))) ?>
                    </span>
                  <?php endif; ?>

                  <?php if (in_array('ownerName', $this->albumInfo)): ?>
                    <span class="dblock mtop5">
                      <?php echo $this->translate('by'); ?>
                      <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                    </span>
                  <?php endif; ?>
                </div>

                <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                  <div class="seao_listings_stats">
                    <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
                    <div title="<?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?></div>
                  </div>
                <?php endif; ?>

                <?php echo $this->albumInfo($album, $this->albumInfo, array('truncationLocation' => $this->truncationLocation, 'infoOnHover' =>$this->infoOnHover)); ?>
                <?php
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1) && in_array('profileField', $this->albumInfo)):
                  //CUSTOM FIELD DISPLAY WORK
                  $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitealbum/View/Helper', 'Sitealbum_View_Helper');
                  $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($album);
                  ?>
                  <div class="sitealbum_quick_specs seao_listings_stats">
                    <?php
                    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) :
                      echo $this->FieldValueLoopQuickInfoSitealbum($album, $fieldStructure, $this->customParams);
                    endif;
                    ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      
      <div id="scroll_bar_height"></div>
      <?php if (empty($this->is_ajax)) : ?>
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => '',
              'class' => 'buttonlink icon_viewmore'
          ))
          ?>
        </div>
        <div class="seaocore_view_more" id="loding_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
        <div id="hideResponse_div"> </div>
      <?php endif; ?>

    <?php elseif (isset($this->params['tag_id']) || isset($this->params['category_id'])): ?>
      <div class="tip mtop10">
        <span>
          <?php echo $this->translate('Nobody has created an album with that criteria.'); ?>
          <?php if ($this->canCreate): ?>
            <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
                <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_photo_lightbox" href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
            <?php else:?>
               <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
            <?php endif;?> 
            
          <?php endif; ?>
        </span>
      </div>
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has created an album yet.'); ?>
          <?php if ($this->canCreate): ?>
            <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
            <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_photo_lightbox" href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
            <?php else:?>
            <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
            <?php endif;?>
          <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>

    <?php if (empty($this->is_ajax)) : ?>
      <?php if ($this->category_id && !$this->subcategory_id): ?>
        <div class="album_browse_cat_cont clr"> <?php echo Engine_Api::_()->getItem('album_category', $this->category_id)->bottom_content; ?> </div>
      <?php elseif ($this->subcategory_id && $this->category_id): ?>
        <div class="album_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('album_category', $this->subcategory_id)->bottom_content; ?></div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($this->is_ajax)) : ?>
      <script type="text/javascript">
        function viewMorePhoto()
        {
          $('seaocore_view_more').style.display = 'none';
          $('loding_image').style.display = '';
          var params = {
            requestParams:<?php echo json_encode($this->params) ?>
          };
          setTimeout(function() {
            en4.core.request.send(new Request.HTML({
              'url': en4.core.baseUrl + 'widget/index/mod/sitealbum/name/browse-albums-sitealbum',
              data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                page: getNextPage(),
                isajax: 1,
                loaded_by_ajax: true
              }),
              evalScripts: true,
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hideResponse_div').innerHTML = responseHTML;
                var photocontainer = $('hideResponse_div').getElement('.browse-album').innerHTML;
                $('sitealbum_browse_list').innerHTML = $('sitealbum_browse_list').innerHTML + photocontainer;
                $('loding_image').style.display = 'none';
                $('hideResponse_div').innerHTML = "";
              }
            }));
          }, 800);

          return false;
        }
      </script>
    <?php endif; ?>

    <?php if ($this->showContent == 3): ?>
      <script type="text/javascript">
        en4.core.runonce.add(function() {
          hideViewMoreLink('<?php echo $this->showContent; ?>');
        });</script>
    <?php elseif ($this->showContent == 2): ?>
      <script type="text/javascript">
        en4.core.runonce.add(function() {
          hideViewMoreLink('<?php echo $this->showContent; ?>');
        });</script>
    <?php else: ?>
      <script type="text/javascript">
        en4.core.runonce.add(function() {
          $('seaocore_view_more').style.display = 'none';
        });
      </script>
      <?php
      echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "sitealbum"), array("orderby" => $this->orderby));
      ?>
    <?php endif; ?>

    <script type="text/javascript">
      var pageAction = function(page) {

        var form;
        if ($('filter_form')) {
          form = document.getElementById('filter_form');
        }

        form.elements['page'].value = page;

        form.submit();
      }
    </script>

    <script type="text/javascript">

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
            if($('scroll_bar_height')) {
              if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
                var elementPostionY = $('scroll_bar_height').offsetTop;
              } else {
                var elementPostionY = $('scroll_bar_height').y;
              }
              if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
                if ((totalCount != currentPageNumber) && (totalCount != 0))
                  viewMorePhoto();
              }
            }
          }
          window.onscroll = doOnScrollLoadAlbum;

        }
        else if (showContent == 2)
        {
          var view_more_content = $('seaocore_view_more');
          view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
          view_more_content.removeEvents('click');
          view_more_content.addEvent('click', function() {
            viewMorePhoto();
          });
        }
      }

  <?php if ($this->enablePhotoRotation): ?>
        var albumId;
        //CHANGING PHOTO ON MOUSEOVER WORK
        var photoPaginationDefaultView = function(album_id, photo_id)
        {
          albumId = album_id;
          var params = {
            requestParams:<?php echo json_encode($this->params) ?>
          };
          en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'widget/index/mod/sitealbum/name/browse-albums-sitealbum',
            data: $merge(params.requestParams, {
              format: 'json',
              photoPagination: 1,
              photo_id: photo_id,
              album_id: album_id,
              loaded_by_ajax: true
            }),
            evalScripts: true,
            onSuccess: function(responseJSON) {
              if ((responseJSON.album_id == albumId) && (switchAlbum == false)) {
                $('sitealbum_' + album_id).style.background = 'url(' + responseJSON.photo_url + ')';
                photoPaginationDefaultView(album_id, responseJSON.photo_id);
              }
            }
          }), {
            "force": true
          });
        };
        var StopPhotoPaginationDefaultView = function(album_id, album_photo_url) {
          switchAlbum = true;
          $('sitealbum_' + album_id).style.background = 'url(' + album_photo_url + ')';
        };
  <?php endif; ?>
    </script>

  <?php else: ?>

    <div id="layout_sitealbum_browse_albums_sitealbum_<?php echo $this->identity; ?>">
    </div>

    <script type="text/javascript">
      var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
      var params = {
        'detactLocation': <?php echo $this->detactLocation; ?>,
        'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
        'responseContainer': 'layout_sitealbum_browse_albums_sitealbum_<?php echo $this->identity; ?>',
        requestParams: requestParams,
      };

      en4.seaocore.locationBased.startReq(params);

    </script>  
  <?php endif; ?>
</div>
