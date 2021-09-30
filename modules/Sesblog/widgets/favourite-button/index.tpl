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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<div class="sesblog_button">
  <a href="javascript:;" data-url="<?php echo $this->subject_id ; ?>" class="sesbasic_animation sesblog_favourite_sesblog_blog_<?php echo $this->subject_id ?> sesblog_favourite_sesblog_blog_view <?php echo ($this->favStatus) ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php if($this->favStatus):?><?php echo $this->translate('Un-Favourite');?><?php else:?><?php echo $this->translate('Favourite');?><?php endif;?></span></a>
</div>
