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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<div class="sesblog_tags_cloud_blog sesbasic_bxs ">
  <ul class="sesblog_tags_cloud_list">
    <?php foreach($this->tagCloudData as $valueTags):?>
      <?php if($valueTags['text'] == '' && empty($valueTags['text'])) continue; ?>
      <li><a href="<?php echo $this->url(array('module' =>'sesblog', 'action' => 'browse'),'sesblog_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>"><b><?php echo $valueTags['text'] ?></b><sup><?php echo $valueTags['itemCount']; ?></sup></a></li>
    <?php endforeach;?>
  </ul>
</div>
