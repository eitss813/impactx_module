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

  $user = $item->getOwner();
  $oldTimeZone = date_default_timezone_get();
  $convert_date = strtotime($item->creation_date);
  date_default_timezone_set($user->timezone);
  $month = date('F',$convert_date);
  $year = date('Y',$convert_date);
  $day = date('j',$convert_date);
?>
<?php if(in_array('creationDate', $allParams['show_criteria'])) { ?>
  <?php if($this->viewType == 1) { ?>
    <div class="sesblog_list_date">
      <span>
        <?php if($item->publish_date): ?>
          <span class="_day"><?php echo date('d',$convert_date);?></span>
          <span class="_month"><?php echo date('M',$convert_date);?></span>
        <?php else: ?>
          <span class="_day"><?php echo date('d',$convert_date);?></span>
          <span class="_month"><?php echo date('M',$convert_date);?></span>
        <?php endif; ?>
      </span>
    </div>
  <?php } else if($this->viewType == 2) { ?>
    <div class="sesblog_list_second_blog_date">
      <?php if($item->publish_date): ?>
        
        <p class=""><span class="month"><?php echo $month; ?></span> <span class="date"><?php echo $day; ?></span> <span class="year"><?php echo $year; ?></span></p>
      <?php else:  ?>
        <p class=""><span class="month"><?php echo date('M',$convert_date);?></span> <span class="date"><?php echo date('d',$convert_date);?></span> <span class="year"><?php echo date('Y',$convert_date);?></span></p>
      <?php endif; ?>
    </div>
  <?php } else if($this->viewType == 3) {  ?>
    <div class="sesblog_list_full_date_blog">
      <p class="sesbasic_text_light">
        <?php if($item->publish_date): ?>
          <?php echo date('M d, Y',$convert_date);?>
        <?php else: ?>
          <?php echo date('M d, Y',$convert_date);?>
        <?php endif; ?>
      </p>
    </div>
  <?php } else if($this->viewType == 4) { ?>
    <div class="sesblog_list_stats sesbasic_text_light"> 
      <span>
        <?php if($item->publish_date): ?>
          <?php echo date('M d, Y',$convert_date);?>
        <?php else: ?>
          <?php echo date('M d, Y',$convert_date);?>
        <?php endif; ?>
      </span>
    </div>
  <?php } else if($this->viewType == 5) { ?>
    <div class="sesblog_list_stats sesbasic_text_light">
      <?php if($item->publish_date): ?>
        <?php echo $this->translate("on "); ?><?php echo date('M d, Y',$convert_date);?>
      <?php else: ?>
        <?php echo date('M d, Y',$convert_date);?>
      <?php endif; ?>
    </div>
  <?php } else if($this->viewType == 6) { ?>
    <div class="sesblog_grid_date_blog sesbasic_text_light">
      <?php if($item->publish_date): ?>
        <?php echo $this->translate("Posted "); ?><?php echo date('M d, Y',$convert_date);?>
      <?php else: ?>
        <?php echo date('M d, Y',$convert_date);?>
      <?php endif; ?>
    </div>
  <?php } else if($this->viewType == 7) { ?>
    <div class="sesblog_list_stats sesblog_pinboard_date sesbasic_text_light">
      <?php if($item->publish_date): ?>
        <span><a href="<?php echo $this->url(array('action'=>'browse'),'sesblog_general',true).'?date='.date('Y-m-d',$convert_date); ?>"> <?php echo date('d M',$convert_date);?></a></span>
      <?php else: ?>
        <span><a href="<?php echo $this->url(array('action'=>'browse'),'sesblog_general',true).'?date='.date('Y-m-d',$convert_date); ?>"> <?php echo date('d M',$convert_date);?></a></span>
      <?php endif; ?>
    </div>
  <?php } ?>
<?php }  ?>
<?php date_default_timezone_set($oldTimeZone); ?>
