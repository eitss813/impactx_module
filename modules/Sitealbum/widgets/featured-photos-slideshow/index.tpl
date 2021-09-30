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
<?php if ($this->type == 'zndp'): ?>
  <?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/slideshow.js');
  ?>
  <?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/slideshow.kenburns.js');
  ?>

  <?php
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/slideshow.css');
  ?>
  <?php
else:
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
  $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_noobslideshow.css');
endif;
?>
<?php
if ($this->is_ajax_load):
  if ($this->type == 'noob'): 
    include APPLICATION_PATH . '/application/modules/Sitealbum/views/scripts/_noobSlideshow.tpl';
  else :
    $var = '';
    $image_count = 0;
    foreach ($this->paginator as $item):
      $ret_str = '';
      $str = Engine_Api::_()->seaocore()->seaocoreTruncateText($item->description, $this->captionTruncation);
      $photoUrl = $item->getPhotoUrl();
      $thumbPhotoUrl = $item->getPhotoUrl('thumb.normal');
      for ($i = 0; $i < strlen($str); $i++) {
        if (substr($str, $i, 1) != " ") {
          $string_world = trim(substr($str, $i, 1));
          if ($string_world == "'") {
            $string_world = "\'";
          }
          $ret_str .= $string_world;
        } else {
          while (substr($str, $i, 1) == " ") {
            $i++;
          }
          $ret_str.= " ";
          $i--; // *** 
        }
      }
      if (empty($ret_str)) {
        $ret_str = '<span></span>';
      }
      if ($this->total_images == $image_count + 1) {
        $url = $item->getHref();
        $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" . ",caption:" . "'$ret_str'" . "," . " href:" . "'$url'" . "},";
      } else {
        $url = $item->getHref();
        $var .= "'$photoUrl':" . "{thumb:" . "'$thumbPhotoUrl'" . ",caption:" . "'$ret_str'" . "," . " href:" . "'$url'" . "},";
      }
      $image_count++;
    endforeach;

    $var = trim($var, ',');

    if (!$this->caption)
      $caption = false;
    else
      $caption = true;

    if (!$this->showThumbnailInZP)
      $showThumbnailInZP = false;
    else
      $showThumbnailInZP = true;

    if (!$this->showController)
      $showControllerInZP = false;
    else
      $showControllerInZP = true;
    ?>

    <script type="text/javascript">
      var url_base = '<?php echo $this->layout()->staticBaseUrl; ?>';</script>
    <script type="text/javascript">
      //  <![CDATA[
      en4.core.runonce.add(function() {
        var data = {
    <?php echo $var; ?>
        };
        var myShow = new Slideshow.KenBurns('id_siteslideshow',
                data, {slide: 0,
          captions: '<?php echo $caption; ?>',
          thumbnails: '<?php echo $showThumbnailInZP; ?>',
          controller: '<?php echo $showControllerInZP; ?>',
          delay: <?php echo $this->delay; ?>,
          duration: <?php echo $this->duration; ?>,
          height: <?php echo $this->height ?>,
          hu: '',
          width: <?php echo $this->width ?>,
          titles: true});
      });
      //]]>	
    </script>

    <style type="text/css">

      .slideshow-thumbnails{
        width:<?php echo $this->width ?>px;
      }
      .slideshow-thumbnails ul {
        background: #ffffff;
      }
      .slideshow-thumbnails-active {
        background-color: #000000;
        opacity: 1;
      }
      .slideshow-thumbnails-inactive {
        background-color: #000000;
        opacity: .5;
      }
      .slideshow-thumbnails img {
        width: <?php
        $temp_width_s = ($this->thumb_width > 90) ? $this->thumb_width : 91;
        echo $temp_width_s - 10;
        ?>px;
      }

      .slideshow-captions{background:  #000000;bottom: 65px;width:<?php echo $this->width - 20 ?>px;}		
      /*.slideshow-thums-off .slideshow-captions{bottom:0;}*/

      .slideshow-captions-hidden {
        height: 0;
        opacity: 0;
      }
      .slideshow-captions-visible {
        height: 22px;
        opacity: .7;
      }

      .slideshow-controller-hidden { 
        opacity: 0;
      }
      .slideshow-controller-visible {
        opacity: 1;
      }
      .slideshow-thumbnails-active {
        opacity: 1;
      }
      .slideshow-thumbnails-inactive {
        opacity: .5;
      }
    </style>

    <?php if ($this->showThumbnailInZP): ?>
      <?php $height1 = $this->height + 65; ?>
      <div id="id_siteslideshow" class="slideshow" style="height:<?php echo $height1 ?>px;">
        <?php //if($this->total_ur != 1):  ?>
        <?php if ($this->target == 1): ?>
          <span style="display:none;"><a href="" target="_blank"></a></span>
        <?php else: ?>
          <span style="display:none;"><a href="" target="_self"></a></span>
        <?php endif; ?>
        <?php //endif;    ?>
      </div>
    <?php else: ?>
      <div id="id_siteslideshow" class="slideshow slideshow-thums-off">
        <?php //if($this->total_ur != 1):  ?>
        <?php if ($this->target == 1): ?>
          <span style="display:none;"><a href="" target="_blank"></a></span>
        <?php else: ?>
          <span style="display:none;"><a href="" target="_self"></a></span>
        <?php endif; ?>
        <?php //endif;    ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <div style="clear:both;"></div>
<?php else: ?>

  <div id="layout_sitealbum_featured_photos_slideshow_<?php echo $this->identity; ?>">
    <!--    <div class="seaocore_content_loader"></div>-->
  </div>

  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitealbum_featured_photos_slideshow_<?php echo $this->identity; ?>',
      requestParams: requestParams
    };

    en4.seaocore.locationBased.startReq(params);
  </script> 

<?php endif; ?>
