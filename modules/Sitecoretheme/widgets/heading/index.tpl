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
 ?>
<span id="sitecoretheme_heading_block_<?php echo $this->identity; ?>">
  <h3><?php echo $this->translate($this->title); ?></h3>
  <?php if( $this->description ): ?>
    <div class="widgets_title_border">
      <span></span>
      <i></i>
      <span></span>
    </div>
    <div class="widgets_title_description">
      <?php echo $this->translate($this->description); ?>
    </div>
  <?php endif; ?>
</span>
<script type="text/javascript">
  (function () {
    var el = $('sitecoretheme_heading_block_<?php echo $this->identity; ?>').getParent('.layout_sitecoretheme_heading');
    var nextEl = el.getNext();
    if (nextEl && nextEl.hasClass('generic_layout_container')) {
      var i;
      var childEl = $('sitecoretheme_heading_block_<?php echo $this->identity; ?>').getChildren();
      for (i = childEl.length - 1; i >= 0; i--) {
        childEl[i].inject(nextEl, 'top');
      }
      el.destroy();
    } else {
      el.set('html', $('sitecoretheme_heading_block_<?php echo $this->identity; ?>').get('html'));
    }

  }).delay(50);
</script>