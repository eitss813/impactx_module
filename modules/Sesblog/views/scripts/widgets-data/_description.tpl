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
  $truncation = $this->truncation;
  $allParams = $this->allParams;
  $desc = $this->showDes;
?>
<?php if(in_array($desc, $allParams['show_criteria'])) { ?>
  <div class="sesblog_list_contant">
    <p class="sesblog_list_des sesbasic_text_light">
      <?php echo $item->getDescription($truncation); ?>
    </p>
  </div>
<?php } ?>
