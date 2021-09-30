<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$baseURL = $this->baseUrl();
?>

<script type="text/javascript">
  $$('.layout_sitecoretheme_banner_images').each(function (el) {
    el.inject($('global_header'), 'after');
  });
  en4.core.runonce.add(function () {

    // Images loop
    var durationOfRotateImage = <?php echo!empty($this->defaultDuration) ? $this->defaultDuration : 500; ?>;
    var slideshowDivObj = $('slide-images');
    var imagesObj = slideshowDivObj.getElements('img');
    var indexOfRotation = 0;

    imagesObj.each(function (img, i) {
      if (i > 0) {
        img.set('opacity', 0);
      }
    });

    var show = function () {
      imagesObj[indexOfRotation].fade('out');
      indexOfRotation = indexOfRotation < imagesObj.length - 1 ? indexOfRotation + 1 : 0;
      imagesObj[indexOfRotation].fade('in');
    };
    show.periodical(durationOfRotateImage);

    // Words loop
    var wordsObj = slideshowDivObj.getElements('.bannerimage-text');
    var indexOfWordRotation = 0;
    wordsObj.each(function (img, i) {
      if (i > 0) {
        img.set('opacity', 0);
      }
    });
    var showWords = function () {
      wordsObj[indexOfWordRotation].fade('out');
      indexOfWordRotation = indexOfWordRotation < wordsObj.length - 1 ? indexOfWordRotation + 1 : 0;
      wordsObj[indexOfWordRotation].fade('in');
    };
    showWords.periodical(durationOfRotateImage);
  });
</script>

<style type="text/css">
  .layout_sitecoretheme_banner_images #slide-images {
    width: <?php echo!empty($this->slideWidth) ? $this->slideWidth . 'px;' : '100%'; ?>;
    height: <?php echo $this->slideHeight . 'px'; ?>;
  }
</style>

<div id="slide-images" class="sitecoretheme_slideblock">
  <?php
  foreach( $this->list as $imagePath ):
    if( !is_array($imagePath) ):
      $iconSrc = "application/modules/Sitecoretheme/externals/images/" . $imagePath;
    else:
      $iconSrc = Engine_Api::_()->sitecoretheme()->displayPhoto($imagePath['file_id'], 'thumb.icon');
    endif;
    if( !empty($iconSrc) ):
      ?>
      <div class="slideblok_image">
        <img src="<?php echo $iconSrc; ?>" />
      </div>
      <?php
    endif;
  endforeach;
  ?>
  <?php
    $iconSrc1 = "application/modules/Sitecoretheme/externals/images/bannerImages/1.jpg";
    $iconSrc2 = "application/modules/Sitecoretheme/externals/images/bannerImages/2.jpg";
    $iconSrc3 = "application/modules/Sitecoretheme/externals/images/bannerImages/3.jpg";
    $iconSrc4 = "application/modules/Sitecoretheme/externals/images/bannerImages/4.jpg";
    $iconSrc5 = "application/modules/Sitecoretheme/externals/images/bannerImages/5.jpg";
    $iconSrc6 = "application/modules/Sitecoretheme/externals/images/bannerImages/6.jpg";
    $iconSrc7 = "application/modules/Sitecoretheme/externals/images/bannerImages/7.jpg";
    $iconSrc8 = "application/modules/Sitecoretheme/externals/images/bannerImages/8.jpg";
  ?>
  <div class="slideblok_image">
    <img src="<?php echo $iconSrc1; ?>" />
  </div>
  <?php /* <div class="slideblok_image">
    <img src="<?php echo $iconSrc2; ?>" />
  </div> */?>
  <div class="slideblok_image">
    <img src="<?php echo $iconSrc3; ?>" />
  </div>
  <div class="slideblok_image">
    <img src="<?php echo $iconSrc4; ?>" />
  </div>
  <div class="slideblok_image">
    <img src="<?php echo $iconSrc5; ?>" />
  </div>
  <?php /* <div class="slideblok_image">
    <img src="<?php echo $iconSrc6; ?>" />
  </div> */?>
  <?php /* <div class="slideblok_image">
    <img src="<?php echo $iconSrc7; ?>" />
  </div> */?>
  <div class="slideblok_image">
    <img src="<?php echo $iconSrc8; ?>" />
  </div>
  <section class="bannerimage-text">
    <div>
      <h1><i>"Our biggest challenge in this new century is to take an idea that seems abstract – sustainable development – and turn it into a reality for all the world’s people"</i> </h1>
      <article>By Kofi Annan</article>
    </div>
  </section>
  <section class="bannerimage-text" style="opacity: 0">
    <div>
      <h1><i>“Never doubt that a small group of thoughtful, committed citizens can change the world. Indeed, it is the only thing that ever has.”</i> </h1>
      <article>By Margaret Mead</article>
    </div>
  </section>
  <section class="bannerimage-text" style="opacity: 0">
    <div>
      <h1><i>“The activist is not the man who says the river is dirty. The activist is the man who cleans up the river.”</i></h1>
      <article>By Ross Perot</article>
    </div>
  </section>
  <section class="bannerimage-text" style="opacity: 0">
    <div>
      <h1><i>“Do not wait for extraordinary circumstances to do good action; try to use ordinary situations.”</i></h1>
      <article>By Jean-Paul Richter</article>
    </div>
  </section>
</div>
<style>
  .layout_sitecoretheme_banner_images .bannerimage-text article{
    font-size: 22px;
  }
  .layout_sitecoretheme_banner_images .slideblok_image img {
    object-fit: cover;
    object-position: top !important;
  }
</style>