<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php if($this->title == '') { ?>
  <h3><?php echo $this->translate('People Like This %s', ucfirst(str_replace('sesblog_','', $this->subject->getType()))); ?></h3>
<?php } ?>
<ul class="sesbasic_sidebar_block sesbasic_user_grid_list sesbasic_clearfix">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <?php $user = Engine_Api::_()->getItem('user', $item->poster_id) ?>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title' => $user->getTitle())); ?>
    </li>
  <?php endforeach; ?>
  <?php if($this->paginator->getTotalItemCount() > $this->limit_data){ ?>
    <li>
      <a href="javascript:;" onclick="getLikeData('<?php echo $this->subject()->getIdentity(); ?>','<?php echo urlencode($this->translate($this->title)); ?>')" class="sesbasic_user_grid_list_more">
      <?php echo '+';echo $this->paginator->getTotalItemCount() - $this->limit_data ; ?>
      </a>
    </li>
 <?php } ?>
</ul>
<script type="application/javascript">
  function getLikeData(value,title){
    if(value) {
      url = en4.core.staticBaseUrl+'sesblog/index/like-item/item_id/'+value+'/title/'+title+'/item_type/<?php echo $this->subject()->getType(); ?>';
      openURLinSmoothBox(url);	
      return;
    }
  }
</script>
