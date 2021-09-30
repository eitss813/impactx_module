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
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/wow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/animate.css');
?>


 <script>
	  new WOW().init();
</script>

<?php if( !empty($this->description) ) : ?>
  <div class="widgets_title_border">
    <span></span>
    <i></i>
    <span></span>
  </div>
  <div class="widgets_title_description">
    <?php echo $this->translate($this->description); ?>
  </div>
<?php endif; ?>

<?php
$totalCount = count($this->highlights);
$leftCount = ceil($totalCount / 2);
?>
<section class="sitecoretheme_middleimage_withicons">
 	<div class="sitecoretheme_middle_content_left">
    <?php for( $index = 0; $index < $leftCount; $index++ ) : ?>
      <?php
        $iconUrl = $defaultIcon = $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/highlights/highlight_'.$this->highlights[$index]['highlights_id'].'.png';
        if($this->highlights[$index]['file_id']) {
           $icon = Engine_Api::_()->storage()->get($this->highlights[$index]['file_id']);
           $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
        } 
      ?>
      <div class="sitecoretheme_middle_content_item wow animated fadeInLeft">
        <div class="sitecoretheme_middle_content_item_inner">
          <div class="sitecoretheme_content_icon">
            <span>
              <img src="<?php echo $iconUrl; ?>">
            </span>
          </div>
          <div class="sitecoretheme_content_info">
            <h3>
                <?php echo $this->translate($this->highlights[$index]['title']) ?>
            </h3>
            <p>
              <?php echo $this->translate($this->highlights[$index]['description']) ?>
            </p>
          </div>
        </div>
      </div>
    <?php endfor; ?>
 	</div>

 	<div class="sitecoretheme_middle_image_block">
    <?php
    $src = "application/modules/Sitecoretheme/externals/images/sitemiddle-img.png";
    if( !empty($this->highlightsSettings['image']) ) {
      $src = $this->highlightsSettings['image'];
    }
    ?>
    <img src="<?php echo $src ?>">
    <?php if( !empty($this->highlightsSettings['attachVideo']) ): ?>      
      <a href='javascript:void(0)'  onclick="openVerticalHighlightsBlocks()" class="seao_smoothbox">
        <i class="fa fa-play"></i>
      </a>
    <?php endif; ?>
 	</div>

 	<div class="sitecoretheme_middle_content_right">
    <?php for( $index; $index < $totalCount; $index++ ) : ?>
      <?php
        $iconUrl = $defaultIcon = $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/highlights/highlight_'.$this->highlights[$index]['highlights_id'].'.png';
        if($this->highlights[$index]['file_id']) {
           $icon = Engine_Api::_()->storage()->get($this->highlights[$index]['file_id']);
           $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
        } 
      ?>
      <div class="sitecoretheme_middle_content_item wow animated fadeInRight">
        <div class="sitecoretheme_middle_content_item_inner">
          <div class="sitecoretheme_content_icon">
            <span>
              <img src="<?php echo $iconUrl; ?>">
            </span>
          </div>
          <div class="sitecoretheme_content_info">
            <h3>
                <?php echo $this->translate($this->highlights[$index]['title']) ?>
            </h3>
            <p>
              <?php echo $this->translate($this->highlights[$index]['description']) ?>
            </p>
          </div>
        </div>
      </div>
    <?php endfor; ?>
 	</div>
</section>
<?php if( !empty($this->highlightsSettings['attachVideo']) ): ?>
<script type="text/javascript">
  function openVerticalHighlightsBlocks() {
    var code = '<?php echo $this->string()->escapeJavascript($this->settings('sitecoretheme.landing.highlights.videoEmbed', ''), false); ?>';
    var el =new Element('div', {
      'class': '',
      html: code
    });
    Smoothbox.open(el);
  }
</script>
<?php endif; ?>