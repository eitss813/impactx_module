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

$target = '';
if( !empty($this->new_tab) ) {
  $target = '_blank';
}
?>
<script>
  new WOW().init();
</script>
<div class="sitecoretheme_static_buttons sitecoretheme_static_buttons_<?php echo $this->style; ?>">
<div class="sitecoretheme_static_buttons_content_inner">
 
	
<div class="sitecoretheme_static_image_inner">
<span style="background-image: url('<?php echo $this->sidePhoto ?>');"></span>
  </div>

  <div class="sitecoretheme_static_buttons_inner wow slideInRight animated">
    <ul>
      <?php if( !empty(trim($this->title1)) ) : ?>
        <li>
          <a class = "button_link" href="<?php echo $this->url1 ?>" target="<?php echo $target ?>" class="wow animated slideInUp">
            <span class="_icon">
              <span>
              <img src="<?php echo $this->icon1Url; ?>">
              <img class="cta_dnone" src="<?php echo $this->icon1HoverUrl; ?>">
              </span>
            </span>
            <span class="_heading"><?php echo $this->translate($this->title1) ?></span>

            <?php if( $this->body1 ): ?>
              <p class="_body"> <?php echo $this->translate($this->body1); ?></p>
            <?php endif; ?>
          </a>
        </li>
      <?php endif; ?>

      <?php if( !empty(trim($this->title2)) ) : ?>
        <li>
          <a class = "button_link" href="<?php echo $this->url2 ?>" target="<?php echo $target ?>" class="wow animated slideInUp">
            <span class="_icon">
              <span>
                <img src="<?php echo $this->icon2Url; ?>">
                <img class="cta_dnone" src="<?php echo $this->icon2HoverUrl; ?>">
              </span>
              </span>
            <span class="_heading"><?php echo $this->translate($this->title2) ?></span>
            
            <?php if( $this->body1 ): ?>
              <p class="_body">   <?php echo $this->translate($this->body2); ?></p>
            <?php endif; ?>

          </a>
        </li>
      <?php endif; ?>

      <?php if( !empty(trim($this->title3)) ) : ?>
        <li>
          <a class = "button_link" href="<?php echo $this->url3 ?>" target="<?php echo $target ?>" class="wow animated slideInUp">
            <span class="_icon">
              <span>
                <img src="<?php echo $this->icon3Url; ?>">
                <img class="cta_dnone" src="<?php echo $this->icon3HoverUrl; ?>">
              </span>
            </span>
            <span class="_heading"><?php echo $this->translate($this->title3) ?></span>

            <?php if( $this->body1 ): ?>
              <p class="_body"><?php echo $this->translate($this->body3); ?></p>
            <?php endif; ?>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
	</div>
</div>

<script type="text/javascript">
  $$('.button_link').addEvent('mouseenter', function (event) {
    event.target.getChildren('._icon span img')[0].addClass('cta_dnone');
    event.target.getChildren('._icon span img')[1].removeClass('cta_dnone');
  });
  $$('.button_link').addEvent('mouseleave', function (event) {
    event.target.getChildren('._icon span img')[0].removeClass('cta_dnone');
    event.target.getChildren('._icon span img')[1].addClass('cta_dnone');
  });
</script>
<style type="text/css">
  .cta_dnone {
    display: none;
  }
</style>