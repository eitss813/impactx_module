<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<div class="sesblog_gutter_labels">
  <div class="sesblog_list_labels ">
    <?php if($this->subject->featured): ?>
      <p class="sesblog_label_featured"><?php echo $this->translate("FEATURED"); ?></p>
    <?php endif; ?>
    <?php if($this->subject->sponsored): ?>
      <p class="sesblog_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></p>
    <?php endif; ?>
    <?php if($this->subject->verified): ?>
      <p class="sesblog_label_verified"><?php echo $this->translate("VERIFIED"); ?></p>
    <?php endif; ?>
  </div>
</div>
