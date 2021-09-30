<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/star-rating.css');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_rating.css');
?>

<?php $showCursor = 0; ?>
<?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?>
  <?php $showCursor = 1; ?>
<?php endif; ?>

<div class="sm_up_overall_rating b_medium">
  <div id="" class="rating" onmouseout="rating_out();">
    <span id="rate2_1" class="sm_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?> onclick="rate(1);" onmouseover="rating_over(1);" <?php endif; ?> ></span>
    <span id="rate2_2" class="sm_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?> onclick="rate(2);" onmouseover="rating_over(2);" <?php endif; ?>></span>
    <span id="rate2_3" class="sm_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?> onclick="rate(3);" onmouseover="rating_over(3);" <?php endif; ?>></span>
    <span id="rate2_4" class="sm_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?> onclick="rate(4);" onmouseover="rating_over(4);" <?php endif; ?>></span>
    <span id="rate2_5" class="sm_rating_star_big_generic" <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?> onclick="rate(5);" onmouseover="rating_over(5);" <?php endif; ?>></span>
    <?php if (!empty($this->viewer_id) && (empty($this->rating_exist) || (!empty($this->rating_exist) && ($this->update_permission))) && ( $this->viewer_id != $this->user->user_id ) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 0)): ?><span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span><?php endif; ?>
  </div>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {
      var pre_rate = '<?php echo $this->rating_users; ?>';
      var user_id = '<?php echo $this->user->getIdentity(); ?>';
      new_text = '';

      var rating_over = window.rating_over = function(rating) {
        for (var x = 1; x <= 5; x++) {
          if (x <= rating) {
            $('rate2_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big');
            if ($('rate1_' + x)) {
              $('rate1_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big');
            }
          } else {
            $('rate2_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big_disabled');
            if ($('rate1_' + x)) {
              $('rate1_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big_disabled');
            }
          }
        }
      }

      var rating_out = window.rating_out = function() {


        if (pre_rate != 0) {
          set_rating();
        }
        else {
          for (var x = 1; x <= 5; x++) {
            $('rate2_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big_disabled');
          }
        }
      }

      var set_rating = window.set_rating = function() {
        var rating = pre_rate;
        $$('.sm_rating_star_big_generic').each(function(el) {
          el.set('class', 'sm_rating_star_big_generic sm_rating_star_big');
        });
        for (var x = parseInt(rating) + 1; x <= 5; x++) {
          $('rate2_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big_disabled');
          if ($('rate1_' + x)) {
            $('rate1_' + x).set('class', 'sm_rating_star_big_generic sm_rating_star_big_disabled');
          }
        }

        var remainder = Math.round(rating) - rating;
        if (remainder <= 0.5 && remainder != 0) {
          var last = parseInt(rating) + 1;
          $('rate2_' + last).set('class', 'sm_rating_star_big_generic sm_rating_star_big_half');
          if ($('rate1_' + last)) {
            $('rate1_' + last).set('class', 'sm_rating_star_big_generic sm_rating_star_big_half');
          }
        }
      }

      var rate = window.rate = function(rating) {
        (new Request.JSON({
          'format': 'json',
          'url': '<?php echo $this->url(array('module' => 'sitemember', 'controller' => 'review', 'action' => 'rate'), 'default', true) ?>',
          'data': {
            'format': 'json',
            'rating': rating,
            'user_id': user_id
          },
          'onRequest': function() {
          },
          'onSuccess': function(responseJSON, responseText)
          {
            pre_rate = responseJSON[0].rating;
            set_rating();
          }
        })).send();

      }
      set_rating();
    });
</script>

<style type="text/css">

  <?php if ($showCursor == 0) { ?>
    .layout_sitemember_user_ratings .sm_rating_star_big_generic{
      cursor: default;
    }
  <?php } ?>
</style>