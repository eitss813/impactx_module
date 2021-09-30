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

<?php $showCursor = 0; ?>
<?php if (!empty($this->viewer_id) && (!empty($this->canRate))): ?>
  <?php $showCursor = 1; ?>
<?php endif; ?>

<?php if (!empty($this->canRate)): ?>
  <div id="album_rating" class="rating" onmouseout="rating_out();">
    <span id="rate_1" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
    <span id="rate_2" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
    <span id="rate_3" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
    <span id="rate_4" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
    <span id="rate_5" class="seao_rating_star_generic" <?php  if(!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
    <span id="rating_text" class="rating_text fnone"><?php echo $this->translate('click to rate'); ?></span>
  </div>
<?php else:
  ?>
  <div id="album_rating" class="rating" onmouseout="rating_out();">
    <span id="rate_1" class="seao_rating_star_generic" ></span>
    <span id="rate_2" class="seao_rating_star_generic"></span>
    <span id="rate_3" class="seao_rating_star_generic"></span>
    <span id="rate_4" class="seao_rating_star_generic"  ></span>
    <span id="rate_5" class="seao_rating_star_generic"></span>
    <span id="rating_text" class="rating_text fnone"><?php echo $this->translate('click to rate'); ?></span>
  </div>
<?php
endif;
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
      var subject_pre_rate = <?php echo $this->subject->rating; ?>;
      var update_permission = <?php echo $this->update_permission; ?>;
      var subject_rated = '<?php echo $this->rated; ?>';
      var subject_id = '<?php echo $this->subject->getIdentity(); ?>';
      var subject_type = '<?php echo $this->subject->getType(); ?>';
      var subject_total_votes = <?php echo $this->rating_count; ?>;
      var viewer = '<?php echo $this->viewer_id; ?>';
      subject_new_text = '';
      var rating_over = window.rating_over = function(rating) {
        if (subject_rated == 1 && update_permission == 0) {
          $('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript(($this->translate('you already rated'))) ?>";
          //set_rating();
        }else if (viewer == 0) {
          $('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('please login to rate')); ?>";
        } else {
          $('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('click to rate')); ?>";
          for (var x = 1; x <= 5; x++) {
            if (x <= rating) {
              $('rate_' + x).set('class', 'seao_rating_star_generic rating_star_y');
            } else {
              $('rate_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
            }
          }
        }
      };

      var rating_out = window.rating_out = function() {
        if (subject_new_text != '') {
          $('rating_text').innerHTML = subject_new_text;
        }
        else {
          $('rating_text').innerHTML = " <?php echo $this->string()->escapeJavascript($this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count))) ?>";
        }
        if (subject_pre_rate != 0) {
          set_rating();
        }
        else {
          for (var x = 1; x <= 5; x++) {
            $('rate_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
          }
        }
      };

      var set_rating = window.set_rating = function() {
        var subject_rating = subject_pre_rate;
        if (subject_new_text != '') {
          $('rating_text').innerHTML = subject_new_text;
        }
        else {
          $('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count))) ?>";
        }
        for (var x = 1; x <= parseInt(subject_rating); x++) { 
          $('rate_' + x).set('class', 'seao_rating_star_generic rating_star_y');
        }

        for (var x = parseInt(subject_rating) + 1; x <= 5; x++) {
          $('rate_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
        }

        var remainder = Math.round(subject_rating) - subject_rating;
        if (remainder <= 0.5 && remainder != 0) {
          var last = parseInt(subject_rating) + 1;
          $('rate_' + last).set('class', 'seao_rating_star_generic rating_star_half_y');
        }
      };

      var rate = window.rate = function(rating) {
        $('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('Thanks for rating!')); ?>";
        (new Request.JSON({
          'format': 'json',
          'url': '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
          'data': {
            'format': 'json',
            'rating': rating,
            'subject_id': subject_id,
            'subject_type': subject_type
          },
          'onRequest': function() { 
          },
          'onSuccess': function(responseJSON, responseText) 
          { 
            subject_pre_rate = responseJSON[0].rating;
            set_rating();
            $('rating_text').innerHTML = responseJSON[0].total + '<?php echo $this->string()->escapeJavascript($this->translate(" ratings")) ?>';
            subject_new_text = responseJSON[0].total + '<?php echo $this->string()->escapeJavascript($this->translate("ratings")) ?>';
          }
        })).send();
      };
      set_rating();
    });
</script>

<style type="text/css">
<?php if ($showCursor == 0) { ?>
    .layout_sitealbum_user_ratings .seao_rating_star_generic{
      cursor: default;
    }
<?php } ?>
</style>
