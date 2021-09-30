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
include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl';
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Sitemember/externals/scripts/_class.noobSlide.packed.js");
?>

<?php if ($this->is_ajax_load): ?>

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
        };
      }

      var width = $("featured_slideshow_wrapper<?php echo $this->identity ?>").clientWidth;
      $("featured_slideshow_mask<?php echo $this->identity ?>").style.width = (width - 10) + "px";
      var divElements = $("featured_slideshow_mask<?php echo $this->identity ?>").getElements('.featured_slidebox');
      for (var i = 0; i < divElements.length; i++)
        divElements[i].style.width = (width - 10) + "px";

      var handles8_more = $$('.handles8_more span');
      var num_of_slidehsow = "<?php echo $this->num_of_slideshow; ?>";
      var nS8 = new noobSlide({
        box: $('sitemember_featured_<?php echo $this->identity ?>_im_te_advanced_box'),
        items: $$('#sitemember_featured_<?php echo $this->identity ?>_im_te_advanced_box h3'),
        size: (width - 10),
        handles: $$('#handles8 span'),
        addButtons: {previous: $('sitemember_featured_<?php echo $this->identity ?>_prev8'), stop: $('sitemember_featured_<?php echo $this->identity ?>_stop8'), play: $('sitemember_featured_<?php echo $this->identity ?>_play8'), next: $('sitemember_featured_<?php echo $this->identity ?>_next8')},
        interval: 5000,
        fxOptions: {
          duration: 500,
          transition: '',
          wait: false
        },
        autoPlay: true,
        mode: 'horizontal',
        onWalk: function(currentItem, currentHandle) {
          // Finding the current number of index.
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
            $('sitemember_featured_<?php echo $this->identity ?>_caption').innerHTML = current_caption_link;
          }
          else {
            $('sitemember_featured_<?php echo $this->identity ?>_caption').innerHTML = '';
          }
          $('sitemember_featured_<?php echo $this->identity ?>_current_numbering').innerHTML = current_index + '/' + "<?php echo $this->num_of_slideshow; ?>";
        }
      });

      //more handle buttons
      //nS8.addHandleButtons(handles8_more);
      //walk to item 3 witouth fx
      nS8.walk(0, false, true);
    });
  </script>

  <div class="featured_slideshow_wrapper clr <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" id="featured_slideshow_wrapper<?php echo $this->identity ?>">
    <div class="featured_slideshow_mask" id="featured_slideshow_mask<?php echo $this->identity ?>" style="height:195px;">
      <div id="sitemember_featured_<?php echo $this->identity ?>_im_te_advanced_box" class="featured_slideshow_advanced_box">

        <?php $image_count = 1; ?>
        <?php foreach ($this->show_slideshow_object as $item): ?>
    <?php $rel = 'user' . ' ' . $item->user_id; ?>

          <div class='featured_slidebox'>
            <div class='featured_slidshow_img'>
              <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $item->featured): ?>
                <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></i>
              <?php endif; ?>

                <?php if($this->circularImage):?>
                        <?php
                        $url = $item->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                <a href="<?php echo $item->getHref() ?>" class="sitemember_thumb">
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                </a>
                        <?php else:?>
              <?php echo $this->htmlLink($item->getHref(array('profile_link' => 1)), $this->itemPhoto($item, 'thumb.profile', array('class' => 'sea_add_tooltip_link', 'rel' => "$rel"))); ?>
<?php endif;?>
                <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($item->sponsored)): ?>
                <div class="seaocore_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
                <?php echo $this->translate('Sponsored'); ?>
                </div>
    <?php endif; ?>
            </div>

            <div class='featured_slidshow_content'>
              <?php
              $tmpBody = strip_tags($item->displayname);
              $title = ( Engine_String::strlen($tmpBody) > $this->truncation ? Engine_String::substr($tmpBody, 0, $this->truncation) . '..' : $tmpBody );
              ?>

    <?php if (!empty($this->showTitle)): ?>
                <div class="o_hidden fleft">
                  <h5 class="fleft"> <?php echo $this->htmlLink($item->getHref(), $title, array('title' => $item->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => "$rel")) ?></h5> 
                </div>
              <?php endif; ?>

              <?php
              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($item->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($item->user_id);
                $user = Engine_Api::_()->getItem('user', $item->user_id);
                $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                ?>
                <?php if (($verify_count >= $verify_limit)): ?>
                    <span class="siteverify_tip_wrapper">
                        <i class="sitemember_list_verify_label mleft5"></i>
                        <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                    </span>
                  <?php
                endif;
              endif;
              ?>

              <?php
              if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
                $online_status = Engine_Api::_()->sitemember()->isOnline($item->user_id);
                ?>
                <span class="fright seaocore_txt_light bold mtop5">
                  <?php if (!empty($online_status)) : ?>
                    <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                    <?php echo $this->translate("Online"); ?>
                <?php endif; ?>
                </span>
    <?php endif; ?>

              <h3 style='display:none'><span><?php echo $image_count++ . '_caption_title:' . $item->username . '_caption_link:' . $this->htmlLink($item->getHref(), $this->translate("View Member &raquo;"), array('class' => 'featured_slideshow_view_link', 'title' => $item->getTitle())) . '</span>' ?></h3>
              <?php if (!empty($this->statistics)) : ?>
                <?php
                $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
                echo $this->memberInfo($item, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading));
                ?>
    <?php endif; ?>
            </div>
          </div>
  <?php endforeach; ?>
      </div>
    </div>
    <div class="featured_slideshow_option_bar">
      <div>
        <p style="<?php
        if ($image_count <= 2): echo "display:none;";
        endif;
        ?>">
          <span id="sitemember_featured_<?php echo $this->identity ?>_prev8" class="featured_slideshow_controllers-prev featured_slideshow_controllers prev" title="Previous" ></span>
          <span id="sitemember_featured_<?php echo $this->identity ?>_stop8" class="featured_slideshow_controllers-stop featured_slideshow_controllers" title="Stop"></span>
          <span id="sitemember_featured_<?php echo $this->identity ?>_play8" class="featured_slideshow_controllers-play featured_slideshow_controllers" title="Play"></span>
          <span id="sitemember_featured_<?php echo $this->identity ?>_next8" class="featured_slideshow_controllers-next featured_slideshow_controllers" title="Next" ></span>
        </p>
      </div>
      <span id="sitemember_featured_<?php echo $this->identity ?>_caption"></span>
      <span id="sitemember_featured_<?php echo $this->identity ?>_current_numbering" class="featured_slideshow_pagination" style="<?php
      if ($image_count <= 2): echo "display:none;";
      endif;
      ?>"></span>
    </div>
  </div>
<?php else: ?>

  <div id="layout_sitemember_slideshow_sitemember_<?php echo $this->identity; ?>">
  </div>

  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitemember_slideshow_sitemember_<?php echo $this->identity; ?>',
      requestParams: requestParams
    };

    en4.seaocore.locationBased.startReq(params);
  </script>

<?php endif; ?>

<style type="text/css" >
.sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::before, .sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::after {
    background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;
	}
</style>