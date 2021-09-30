<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<?php if( !$this->video || $this->video->status !=1 ):
  echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
  return; // Do no render the rest of the script in this mode
endif; ?>
<script type="text/javascript">
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

  function videoRate(rating,video_id,page_id) {
 $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Thank you for rating!'); ?>");
    for (var x = 1; x <= 5; x++) {
      $.mobile.activePage.find('#rate_' + x).attr('onclick', '');
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      'url': '<?php echo $this->url(array('module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
      'data': {
        'format': 'json',
        'rating': rating,
        'video_id' : video_id,
        'page_id': page_id
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
    $.mobile.activePage.data('pre_rate',<?php echo $this->video->rating; ?>);
    $.mobile.activePage.data('rated', '<?php echo $this->rated; ?>');
    $.mobile.activePage.data('total_votes',<?php echo $this->rating_count; ?>);
    $.mobile.activePage.data('page_id',<?php echo $this->video->page_id; ?>);
    $.mobile.activePage.data('video_id',<?php echo $this->video->video_id; ?>);
    set_rating();
  });

  function tagAction(tag){
    $.mobile.activePage.find('#tag').val(tag);
    $.mobile.activePage.find('#filter_form').submit();
  }

</script>
<div class="ui-page-content sitepagevideo-view-page">
	<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
	  <input type="hidden" id="tag" name="tag" value=""/>
	</form>
  <!--VIDEO PLAYER CONDITION FOR MOBILE APP AND MOBILE.-->
    <?php if ($this->video->type == 3): ?>
      <div class="video-player prelative">
        <?php if ($this->video->duration): ?>
          <div class="video-duration">
            <strong>
              <?php
              if ($this->video->duration >= 3600) {
                $duration = gmdate("H:i:s", $this->video->duration);
              } else {
                $duration = gmdate("i:s", $this->video->duration);
              }
              echo $duration;
              ?>
            </strong>	
          </div>
        <?php endif ?>
        <a onclick="window.videoPlayer.player('<?php echo $this->video_location ?>')" >
          <?php
          if ($this->video->photo_id) {
              echo $this->itemPhoto($this->video, 'thumb.profile');
          } else {
              echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
          }
          ?>
          <span></span>
          <i class="ui-icon ui-icon-play"></i>
        </a>
      </div>
    <?php elseif ($this->video->type == 1 && 0): ?>
      <div class="video-player prelative">
        <?php if ($this->video->duration): ?>
          <div class="video-duration">
            <strong>
              <?php
              if ($this->video->duration >= 3600) {
                $duration = gmdate("H:i:s", $this->video->duration);
              } else {
                $duration = gmdate("i:s", $this->video->duration);
              }
              echo $duration;
              ?>
            </strong>	
          </div>
        <?php endif ?>
        <a onclick="window.videoPlayer.youtube('<?php echo $this->video->code ?>')">
          <?php
          if ($this->video->photo_id) :
            echo $this->itemPhoto($this->video, 'thumb.profile');
          else:
            echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png" />';
          endif;
          ?>
          <span></span>
          <i class="ui-icon ui-icon-play"></i>
        </a>
      </div>
    <?php else: ?>
      <div class="sm-ui-video-embed"><?php echo $this->videoEmbedded ?></div>
    <?php endif; ?>
<div class="video-title">
    <?php echo $this->video->getTitle() ?>
  </div>
          
  <div class="video-stats t_light f_small">  
    <?php echo $this->translate('By'); ?>
    <?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>  
  </div> 
          
  <div class="video-stats t_light f_small">
    <?php echo $this->timestamp($this->video->creation_date) ?> 
    <?php if ($this->category): ?>
      - 
      <?php
      echo $this->htmlLink(array(
          'route' => 'video_general',
          'QUERY' => array('category' => $this->category->category_id)
              ), $this->translate($this->category->category_name)
      )
      ?>
    <?php endif; ?>
    <?php if (count($this->videoTags)): ?>
      -
      <?php foreach ($this->videoTags as $tag): ?>
        <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>&nbsp;
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
          
  <div class="video-stats t_light f_small">
    <?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
  </div>  

  <?php if (!empty($this->video->description)): ?>
    <div class="sm-ui-cont-cont-des f_small">
         <?php echo nl2br($this->viewMore($this->translate($this->video->description), 80));  ?>
      <?php //echo nl2br($this->video->description) ?> 
    </div>
  <?php endif ?>

  <div class="sm-ui-video-rating">
    <div id="video_rating" class="rating" onmouseout="rating_out();" valign="top">
      <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(1,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(1);"></span>
      <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(2,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(2);"></span>
      <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(3,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(3);"></span>
      <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(4,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(4);"></span>
      <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(5,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(5);"></span>
      <div id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></div>
    </div>
  </div>	
</div>
<script type="text/javascript">
 sm4.core.runonce.add(function() { 
             $('.layout_page_sitepagevideo_index_view').find('.layout_sitemobile_sitemobile_headingtitle').html('');    
          });
</script>