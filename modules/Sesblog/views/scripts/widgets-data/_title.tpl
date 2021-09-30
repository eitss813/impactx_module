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
  $truncation = $this->truncation;
  $allParams = $this->allParams;
  $divclass = $this->divclass;
?>
<?php if(in_array('title', $allParams['show_criteria'])): ?>
  <div class="<?php echo $divclass; ?>">
    <?php if(strlen($item->getTitle()) > $truncation):?>
      <?php $title = mb_substr($item->getTitle(),0,$truncation).'...';?>
      <?php echo $this->htmlLink($item->getHref(),$title,array('title'=>$item->getTitle())); ?>
    <?php else: ?>
      <?php echo $this->htmlLink($item->getHref(),$item->getTitle(),array('title'=>$item->getTitle())  ) ?>
    <?php endif;?>
      <?php if(in_array('verifiedLabel', $allParams['show_criteria']) && $item->verified):?>
      <i class="sesbasic_verified_icon" title="Verified"></i>
    <?php endif;?>
  </div>
<?php endif;?>
