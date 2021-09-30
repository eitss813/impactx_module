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
if( !empty( $this->bgImage) ) :  ?>
  <style>
    .layout_sitecoretheme_stats_block {
      background-image: url(<?php echo $this->bgImage; ?>);
    }
  </style>
<?php endif;  ?>

<div class="sitecoretheme_counter_container" id ='sitecoretheme_counter_container'>
	<div class="sitecoretheme_container">
		<div class="sitecoretheme_counter_statistic">
      <?php if( !empty((int) $this->count1) ) :  ?>
        <div class="sitecoretheme_counter_statistic_3">
          <div class="sitecoretheme_counter_wrapper">
            <div class="stats_icon"><img src="<?php echo $this->icon1Url; ?>"></div>
			  <div class="stats_info">
				<h4><?php echo $this->translate($this->count1) ?>+</h4>
				<p><?php echo $this->translate($this->stat1) ?></p>
			  </div>
          </div>
        </div>
      <?php endif;  ?>

      <?php if( !empty((int) $this->count2) ) :  ?>
        <div class="sitecoretheme_counter_statistic_3">
          <div class="sitecoretheme_counter_wrapper">
            <div class="stats_icon"><img src="<?php echo $this->icon2Url; ?>"></div>
		    <div class="stats_info">
				<h4><?php echo $this->translate($this->count2) ?>+</h4>
				<p><?php echo $this->translate($this->stat2) ?></p>
			</div>
          </div>
        </div>
      <?php endif;  ?>

      <?php if( !empty((int) $this->count3) ) :  ?>
        <div class="sitecoretheme_counter_statistic_3">
          <div class="sitecoretheme_counter_wrapper">
            <div class="stats_icon"><img src="<?php echo $this->icon3Url; ?>"></div>
		    <div class="stats_info">
				<h4><?php echo $this->translate($this->count3) ?>+</h4>
				<p><?php echo $this->translate($this->stat3) ?></p>
			</div>
          </div>
        </div>
      <?php endif;  ?>

      <?php if( !empty((int) $this->count4) ) :  ?>
        <div class="sitecoretheme_counter_statistic_3">
          <div class="sitecoretheme_counter_wrapper">
            <div class="stats_icon"><img src="<?php echo $this->icon4Url; ?>"></div>
		    <div class="stats_info">
				<h4><?php echo $this->translate($this->count4) ?>+</h4>
				<p><?php echo $this->translate($this->stat4) ?></p>
			</div>
          </div>
        </div>
      <?php endif;  ?>
		</div>
	</div>
</div>
<script type="text/javascript">
  var alreadyStarted = false;
  window.addEventListener("scroll", function() {
    var el = $('sitecoretheme_counter_container');
    var rect = el.getBoundingClientRect();
    if (!alreadyStarted && (rect.top - el.offsetTop) <= 500) {
      alreadyStarted = true;
      $$('.sitecoretheme_counter_wrapper').each(function (el) {
        startCounter(el.getElement('h4'), 0, parseInt(el.getElement('h4').get('text').replace('+', '')), 1000);
      });
    }
  });
  function startCounter(el, start, end, duration) {
    var current = start;
    var interval = Math.abs(Math.ceil(end / duration));
    var timer = setInterval(function() {
      current += interval;
      if (current >= end) {
        el.innerHTML = end + '+';
        clearInterval(timer);
      }
      el.innerHTML = current + '+';
    }, 1);
  }
</script>