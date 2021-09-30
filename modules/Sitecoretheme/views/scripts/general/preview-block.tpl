<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: preview-block.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<div style="min-width: 1000px; margin: 50px">
  <?php
  echo $this->content()->renderWidget('sitecoretheme.block', array(
    'block_id' => $this->block->getIdentity()
  ))
  ?>
  <div class="ftr_button_block_section_ctr">
  <button onclick="parent.Smoothbox.close();" ><?php echo $this->translate('Close') ?></button>
  <button onclick="updatePhotoPositions()" ><?php echo $this->translate('Change Photo Positions') ?></button>
</div>
</div>

<script type="text/javascript">
  var updatePhotoPositions = function () {
    var el = $(document.body).getElement('.sitecoretheme_blocks_wapper');
    el.toggleClass('_sitecoretheme_blocks_left');
    el.toggleClass('_sitecoretheme_blocks_right');
  }
</script>