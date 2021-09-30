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
  <a href="javascript:;" data-url="<?php echo $this->subject_id ; ?>" class="sesbasic_animation sesblog_like_sesblog_blog_view  sesblog_like_sesblog_blog_<?php echo $this->subject_id ?>"><i class="fa <?php echo $this->likeClass;?>"></i><span><?php echo $this->likeText; ?></span></a>
</div>
