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
<?php if(isset($this->my_blogs) && $this->my_blogs) { ?> 
  <div class="sesblog_options_buttons sesblog_list_options sesbasic_clearfix">
    <?php if($this->can_edit) { ?>
      <a href="<?php echo $this->url(array('action' => 'edit', 'blog_id' => $item->blog_id), 'sesblog_specific', true); ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate('Edit Blog'); ?>"><i class="fa fa-edit"></i></a>
    <?php } ?>
    <?php if ($this->can_delete){ ?>
      <a href="<?php echo $this->url(array('action' => 'delete', 'blog_id' => $item->blog_id), 'sesblog_specific', true); ?>" class="sesbasic_icon_btn" title="<?php echo $this->translate('Delete Blog'); ?>" onclick='opensmoothboxurl(this.href);return false;'><i class="fa fa-trash"></i></a>
    <?php } ?>
  </div>
<?php } ?>
