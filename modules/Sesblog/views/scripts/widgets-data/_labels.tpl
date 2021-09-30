<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _listView.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php 
  $item = $this->item;
  $allParams = $this->allParams;
?>
<?php if(in_array('featuredLabel', $allParams['show_criteria']) || in_array('sponsoredLabel', $allParams['show_criteria'])): ?>
  <div class="sesblog_list_labels ">
    <?php if(in_array('sponsoredLabel', $allParams['show_criteria']) && $item->sponsored == 1):?>
        <p class="sesblog_label_sponsored"><?php echo $this->translate('Sponsored');?></p>
      <?php endif;?>
      <?php if(in_array('featuredLabel', $allParams['show_criteria']) && $item->featured == 1):?>
        <p class="sesblog_label_featured"><?php echo $this->translate('Featured');?></p>
      <?php endif;?>
  </div>
<?php endif;?>
