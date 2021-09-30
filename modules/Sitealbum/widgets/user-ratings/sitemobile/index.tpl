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

<?php //if (!empty($this->canRate)): ?>
    <div class="sm-ui-video-rating">
      <div id="video_rating" class="rating" onmouseout="rating_out();" valign="top">
        <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
        <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
        <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
        <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
        <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
        <div id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></div>
      </div>
    </div>	
<?php //else:
  ?>
<!--  <div id="album_rating" class="rating" onmouseout="rating_out();">
    <span id="rate_1" class="rating_star_big_generic" ></span>
    <span id="rate_2" class="rating_star_big_generic"></span>
    <span id="rate_3" class="rating_star_big_generic"></span>
    <span id="rate_4" class="rating_star_big_generic"  ></span>
    <span id="rate_5" class="rating_star_big_generic"></span>
    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span>
  </div>-->
<?php //endif;
?>
<script type="text/javascript">
      var subject_pre_rate = <?php echo $this->subject->rating; ?>;
      var update_permission = <?php echo $this->update_permission; ?>;
      var subject_rated = '<?php echo $this->rated; ?>';
      var subject_id = '<?php echo $this->subject->getIdentity(); ?>';
      var subject_type = '<?php echo $this->subject->getType(); ?>';
      var subject_total_votes = <?php echo $this->rating_count; ?>;
      var viewer = '<?php echo $this->viewer_id; ?>';
      subject_new_text = '';
  function rating_over(rating) {
    if ($.mobile.activePage.data('rated') == 1) {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('you have already rated'); ?>");
    }
    else if ( <?php echo $this->viewer_id; ?> === 0) {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Only logged-in user can rate'); ?>");
    }
    else {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Please click to rate'); ?>");
      for (var x = 1; x <= 5; x++) {
        if (x <= rating) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big');
        } else {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }
  }

  function rating_out() {
    $.mobile.activePage.find('#rating_text').html(" <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
       if ($.mobile.activePage.data('pre_rate') !== 0) {
         set_rating();
       }
       else {
         for (var x = 1; x <= 5; x++) {
           $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
         }
       }
  }

  function set_rating() {
    var rating = $.mobile.activePage.data('pre_rate');
        var current_total_rate = $.mobile.activePage.data('current_total_rate');
        if (current_total_rate) {
          var current_total_rate = $.mobile.activePage.data('current_total_rate');
          if (current_total_rate === 1) {
            $.mobile.activePage.find('#rating_text').html(current_total_rate + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
          }
          else {
            $.mobile.activePage.find('#rating_text').html(current_total_rate + '<?php echo $this->string()->escapeJavascript($this->translate(" ratings")) ?>');
          }
        }
        else {
          $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
        }

        for (var x = 1; x <= parseInt(rating); x++) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big');
        }

        for (var x = parseInt(rating) + 1; x <= 5; x++) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
        }

        var remainder = Math.round(rating) - rating;
        if (remainder <= 0.5 && remainder != 0) {
          var last = parseInt(rating) + 1;
          $.mobile.activePage.find('#rate_' + last).attr('class', 'rating_star_big_generic rating_star_big_half');
        }
  }

  function rate(rating) {
 $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Thank you for rating!'); ?>");
    for (var x = 1; x <= 5; x++) {
      $.mobile.activePage.find('#rate_' + x).attr('onclick', '');
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      'url': '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
      'data': {
        'format': 'json',
            'rating': rating,
            'subject_id': subject_id,
            'subject_type': subject_type
      },
      beforeSend: function() {
        $.mobile.activePage.data('rated', 1);
        var total_votes = $.mobile.activePage.data('total_votes');
        total_votes = total_votes+1;
        var pre_rate = ($.mobile.activePage.data('pre_rate') + rating) / total_votes;
        $.mobile.activePage.data('total_votes', total_votes);
        $.mobile.activePage.data('pre_rate', pre_rate);
        set_rating();
      },
      success: function(response)
      {
        $.mobile.activePage.find('#rating_text').html(sm4.core.language.translate(['%1$s rating', '%1$s ratings', response[0].total], response[0].total));
        $.mobile.activePage.data('current_total_rate', response[0].total);
      }
    });

  }

  sm4.core.runonce.add(function() {
    $.mobile.activePage.data('pre_rate',<?php echo $this->subject->rating; ?>);
    $.mobile.activePage.data('rated', '<?php echo $this->rated; ?>');
    $.mobile.activePage.data('total_votes',<?php echo $this->rating_count; ?>);
    set_rating();
  });

  function tagAction(tag){
    $.mobile.activePage.find('#tag').val(tag);
    $.mobile.activePage.find('#filter_form').submit();
  }

</script>

<style type="text/css">
<?php if ($showCursor == 0) { ?>
    .layout_sitealbum_user_ratings .rating_star_big_generic{
      cursor: default;
    }
<?php } ?>
</style>
