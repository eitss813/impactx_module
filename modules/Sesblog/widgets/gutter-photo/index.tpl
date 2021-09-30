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
<div class="sesblog_onear_photo_three">
	<?php if($this->title) { ?>
    <p class="about_title"><?php echo $this->translate($this->title);?></p>
  <?php } ?>
  <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner),array('class' =>  ($this->photoviewtype == 'square') ? 'sesblogs_gutter_photo_square' : 'sesblogs_gutter_photo')) ?>
	<?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'sesblogs_gutter_name')); ?>
</div>
