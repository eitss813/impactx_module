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
<?php if(in_array('view', $allParams['show_criteria'])) { ?>
  <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="sesbasic_icon_view"></i><?php echo $item->view_count; ?></span>
<?php } ?>
<?php if(in_array('like', $allParams['show_criteria'])) { ?>
  <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="sesbasic_icon_like_o"></i><?php echo $item->like_count; ?></span>
<?php } ?>
<?php if(in_array('comment', $allParams['show_criteria'])) { ?>
  <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>"><i class="sesbasic_icon_comment_o"></i><?php echo $item->comment_count;?></span>
<?php } ?>
<?php if(in_array('favourite', $allParams['show_criteria']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.favourite', 1)) { ?>
  <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="sesbasic_icon_favourite_o"></i><?php echo $item->favourite_count;?></span>
<?php } ?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/_blogRatingStat.tpl';?>
<?php if(empty($this->readtime) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enablereadtime', 1) && in_array('readtime', $allParams['show_criteria']) && !empty($item->readtime)) { ?>
  <span><i class="far fa-clock"></i><?php echo $item->readtime ?>. <?php echo $this->translate("read"); ?></span>
<?php } ?>
