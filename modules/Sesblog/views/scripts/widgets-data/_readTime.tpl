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
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && in_array('readtime', $allParams['show_criteria']) && !empty($item->readtime)) { ?>
  <div class="sesblog_list_stats sesbasic_text_light sesblog_read_time">
    <span><i class="far fa-clock"></i> <?php echo $item->readtime ?></span>
  </div>
<?php } ?>
