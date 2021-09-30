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

<?php 
  $baseUrl = $this->layout()->staticBaseUrl; 
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css');
  $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
  $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); 
?>

<div class="sesbasic_cloud_widget sesbasic_clearfix <?php if($this->viewtype): ?> sesbasic_cloud_widget_full <?php endif; ?>" style="background-color:#<?php echo $this->widgetbgcolor; ?>;">
  <h3 style="background-color:#<?php echo $this->buttonbgcolor; ?>;color:#<?php echo $this->textcolor; ?>;"><img src="application/modules/Sesblog/externals/images/trading_icon.png" /><?php echo $this->translate("Trending Topics"); ?></h3>
  <a href="<?php echo $this->url(array('action' => 'tags'),'sesblog_general',true);?>" class="sesbasic_more_link clear" style="background-color:#<?php echo $this->buttonbgcolor; ?>;color:#<?php echo $this->textcolor; ?>;"><?php echo $this->translate("See All Tags");?> &raquo;</a>
  <div class="sesblog_tags_horizantal_blogs sesbasic_bxs sesbasic_horrizontal_scroll ">  
    <ul class="sesblog_tags_cloud_list">
      <?php foreach($this->paginator as $valueTags):?>
        <?php if($valueTags['text'] == '' || empty($valueTags['text'] )) continue; ?>
        <li><a style="background-color:#<?php echo $this->buttonbgcolor; ?>;color:#<?php echo $this->textcolor; ?>;" href="<?php echo $this->url(array('module' =>'sesblog','controller' => 'index', 'action' => 'browse'),'sesblog_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>">       #<?php echo $this->translate($valueTags['text']); ?></a></li>
      <?php endforeach;?>
    </ul>
  </div>
</div>
