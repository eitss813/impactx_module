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
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php if ($this->is_ajax_load): ?>

  <?php if (!empty($this->titleLink)) : ?>
    <span class="fright sitealbum_top_link">
      <?php echo $this->translate($this->titleLink); ?>
    </span>
  <?php endif; ?> 

  <?php
  if (!empty($this->num_of_slideshow)) {
    ?>
    <script type="text/javascript">

      en4.core.runonce.add(function() {
        if (document.getElementsByClassName == undefined) {
          document.getElementsByClassName = function(className)
          {
            var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
            var allElements = document.getElementsByTagName("*");
            var results = [];

            var element;
            for (var i = 0; (element = allElements[i]) != null; i++) {
              var elementClass = element.className;
              if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
                results.push(element);
            }

            return results;
          }
        }

        var globalContentElement = en4.seaocore.getDomElements('content');
        var width = $(globalContentElement).getElement(".featured_slideshow_wrapper").clientWidth;
        $(globalContentElement).getElement(".featured_slideshow_mask").style.width = (width - 10) + "px";
        var divElements = document.getElementsByClassName('featured_slidebox');
        for (var i = 0; i < divElements.length; i++)
          divElements[i].style.width = (width - 10) + "px";

        var handles8_more = $$('#handles8_more span');
        var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
        var nS8 = new noobSlide({
          box: $('sitealbum_featured_album_im_te_advanced_box'),
          items: $$('#sitealbum_featured_album_im_te_advanced_box h3'),
          size: (width - 10),
          handles: $$('#handles8 span'),
          addButtons: {previous: $('sitealbum_featured_album_prev8'), stop: $('sitealbum_featured_album_stop8'), play: $('sitealbum_featured_album_play8'), next: $('sitealbum_featured_album_next8')},
          interval: 5000,
          fxOptions: {
            duration: 500,
            transition: '',
            wait: false
          },
          autoPlay: true,
          mode: 'horizontal',
          onWalk: function(currentItem, currentHandle) {

            //		// Finding the current number of index.
            var current_index = this.items[this.currentIndex].innerHTML;
            var current_start_title_index = current_index.indexOf(">");
            var current_last_title_index = current_index.indexOf("</span>");
            // This variable containe "Index number" and "Title" and we are finding index.
            var current_title = current_index.slice(current_start_title_index + 1, current_last_title_index);
            // Find out the current index id.
            var current_index = current_title.indexOf("_");
            // "current_index" is the current index.
            current_index = current_title.substr(0, current_index);

            // Find out the caption title.
            var current_caption_title = current_title.indexOf("_caption_title:") + 15;
            var current_caption_link = current_title.indexOf("_caption_link:");
            // "current_caption_title" is the caption title.
            current_caption_title = current_title.slice(current_caption_title, current_caption_link);
            var caption_title = current_caption_title;
            // "current_caption_link" is the caption title.
            current_caption_link = current_title.slice(current_caption_link + 14);


            var caption_title_lenght = current_caption_title.length;
            if (caption_title_lenght > 30)
            {
              current_caption_title = current_caption_title.substr(0, 30) + '..';
            }

            if (current_caption_title != null && current_caption_link != null)
            {
              $('sitealbum_featured_album_caption').innerHTML = current_caption_link;
            }
            else {
              $('sitealbum_featured_album_caption').innerHTML = '';
            }


            $('sitealbum_featured_album_current_numbering').innerHTML = current_index + '/' + num_of_slidehsow;
          }
        });

        //more handle buttons
        nS8.addHandleButtons(handles8_more);
        //walk to item 3 witouth fx
        nS8.walk(0, false, true);
      });
    </script>
  <?php } ?>

  <div class="featured_slideshow_wrapper">
    <div class="featured_slideshow_mask">
      <div id="sitealbum_featured_album_im_te_advanced_box" class="featured_slideshow_advanced_box">
        <?php $image_count = 1; ?>
        <?php foreach ($this->show_slideshow_object as $type => $album) : ?>
          <div class='featured_slidebox'>
            <div class='featured_slidshow_img'><?php echo $this->htmlLink($album->getHref(), $this->itemPhoto($album, 'thumb.profile')) ?> </div>
            <div class='featured_slidshow_content'>

              <h3 style='display:none'><span><?php echo $image_count++ . '_caption_title:' . $album->title . '_caption_link:' . $this->htmlLink($album->getHref(), $this->translate("View Album &raquo;"), array('class' => 'featured_slideshow_view_link', 'title' => $album->getTitle())) . '</span>' ?></h3>

              <?php if (!empty($this->albumInfo)) : ?>
                <div class="sitealbum_thumb_info">

                  <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                    <h5 class="o_hidden">
                      <?php echo $this->htmlLink($album, Engine_Api::_()->seaocore()->seaocoreTruncateText($album->getTitle(), $this->albumTitleTruncation)) ?>
                    </h5>
                  <?php endif; ?>

                  <?php if (in_array('ownerName', $this->albumInfo)): ?>
                    <p>
                      <?php echo $this->translate('by'); ?>
                      <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                    </p>
                  <?php endif; ?>

                  <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                    <div class="seao_listings_stats" title="Photos">
                      <i class="seao_icon_strip seao_icon seao_icon_photo"></i>
                      <div title="<?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $album->photos_count), $this->locale()->toNumber($album->photos_count)) ?></div>
                    </div>
                  <?php endif; ?>

                  <?php echo $this->albumInfo($album, $this->albumInfo, array('truncationLocation' => $this->truncationLocation)); ?>

                  <?php if ($album->description && ($this->truncationDescription > 0)) : ?>
                    <p class="clr">
                      <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($album->description, $this->truncationDescription) ?>
                      <?php if (Engine_String::strlen($album->description) > $this->truncationDescription): ?>
                        <?php echo $this->htmlLink($album->getHref(), $this->translate('More &raquo;')) ?>
                      <?php endif; ?>
                    </p>

                  <?php endif; ?>
                </div>

              <?php endif; ?>
            </div>
          </div>
        <?php endforeach;
        ?>
      </div>
    </div>

    <div class="featured_slideshow_option_bar">
      <div>
        <p class="buttons">
          <span id="sitealbum_featured_album_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span>
          <span id="sitealbum_featured_album_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title=<?php echo $this->translate("Stop") ?> ></span>
          <span id="sitealbum_featured_album_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title=<?php echo $this->translate("Play") ?> ></span>
          <span id="sitealbum_featured_album_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
        </p>
      </div>
      <span id="sitealbum_featured_album_caption"></span>
      <span id="sitealbum_featured_album_current_numbering" class="featured_slideshow_pagination"></span>
    </div>
  </div>  

<?php else: ?>

  <div id="layout_sitealbum_featured_albums_slideshow_<?php echo $this->identity; ?>">
  </div>

  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitealbum_featured_albums_slideshow_<?php echo $this->identity; ?>',
      requestParams: requestParams
    };

    en4.seaocore.locationBased.startReq(params);
  </script> 

<?php endif; ?>

