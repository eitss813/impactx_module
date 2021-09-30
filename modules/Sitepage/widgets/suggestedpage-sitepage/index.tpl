<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
?> 
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<ul class="sitepage_sidebar_list jq-sitepage_tooltip">
  <?php foreach ($this->suggestedsitepage as $sitepage): ?>
    <li>
      <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon')) ?>

      <div class="suggestsitepage_tooltip" style="display:none;">
        <div class="suggestsitepage_tooltip_content_outer">
          <div class="suggestsitepage_tooltip_content_inner">
            <div class="suggestsitepage_tooltip_arrow">
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/tooltip_arrow.png' alt="" />
            </div>
            <div class='suggestsitepages_tooltip_info'>
              <div class="title">
                <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $sitepage->getTitle()) ?>
                <span>
                  <?php if ($sitepage->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                </span>
                <span>
                  <?php if ($sitepage->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                </span>
              </div>
              <?php if ($this->sitereviewEnabled && $sitepage->rating > 0): ?>
                  <?php
                  $currentRatingValue = $sitepage->rating;
                  $difference = $currentRatingValue - (int) $currentRatingValue;
                  if ($difference < .5) {
                    $finalRatingValue = (int) $currentRatingValue;
                  } else {
                    $finalRatingValue = (int) $currentRatingValue + .5;
                  }
                  ?>

                  <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                    <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                    <?php endfor; ?>
                    <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                    <?php endif; ?>
                  </span>
                <?php endif; ?>
              
							<div class='suggestsitepages_tooltip_info_date clr'>
								<?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> 
								<?php if($postedBy && $sitepage->show_name):?>
									- <?php echo $this->translate('posted by'); ?>
									<?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>
								<?php endif;?>
							</div>
              <div class='suggestsitepages_tooltip_info_date'>
                <?php echo $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) ?>, 

								<?php if ($this->sitereviewEnabled): ?>
									<?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)) ?>,
								<?php endif; ?>

                <?php echo $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) ?>, 
                <?php echo $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class='sitepage_sidebar_list_info'>
        <div class='sitepage_sidebar_list_title'>
          <?php $sitepage_title = strip_tags($sitepage->title);
          $sitepage_title = Engine_String::strlen($sitepage_title) > 40 ? Engine_String::substr($sitepage_title, 0, 40) . '..' : $sitepage_title; ?>   
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $sitepage_title) ?>
        </div>
      </div>

    </li>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">
  /* moo style */
  window.addEvent('domready',function() {
    //opacity / display fix
    $$('.suggestsitepage_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-sitepage_tooltip li').each(function(el,i) {
      el.addEvents({
        'mouseenter': function() {
          el.getElement('div').fade('in');
        },
        'mouseleave': function() {
          el.getElement('div').fade('out');
        }
      });
    });

  });
</script>