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

<?php if(in_array('by', $allParams['show_criteria']) || in_array('ownerPhoto', $allParams['show_criteria'])){ ?>
  <?php if($this->viewType == 1) { ?>
    <div class="sesblog_list_stats sesbasic_text_light">
      <span>
        <?php echo $this->translate("Posted by") ?> <?php if(in_array('ownerPhoto', $allParams['show_criteria'])){ ?><?php echo $this->htmlLink($item->getOwner()->getParent(), $this->itemPhoto($item->getOwner()->getParent(), 'thumb.icon')); ?><?php } ?><?php if(in_array('by', $allParams['show_criteria'])) { ?><?php echo $this->htmlLink($item->getOwner()->getHref(),$item->getOwner()->getTitle() ) ?><?php } ?>
      </span>
    </div>
  <?php } else if($this->viewType == 2) { ?>
    <div class="sesblog_list_stats admin_img sesbasic_text_light">
      <span>
        <?php if(in_array('ownerPhoto', $allParams['show_criteria'])){ ?>
          <?php echo $this->htmlLink($item->getOwner()->getParent(), $this->itemPhoto($item->getOwner()->getParent(), 'thumb.icon')); ?>
        <?php } ?>
        <?php if(in_array('by', $allParams['show_criteria'])){ ?>
          <?php echo $this->translate("by") ?> <?php echo $this->htmlLink($item->getOwner()->getHref(),$item->getOwner()->getTitle() ) ?>
        <?php } ?>
      </span>
    </div>
  <?php } else if($this->viewType == 3) { ?>
    <div class="sesblog_list_stats _owner sesbasic_text_light">
      <span>
        <?php echo $this->translate("Posted by") ?> <?php if(in_array('ownerPhoto', $allParams['show_criteria'])){ ?><?php echo $this->htmlLink($item->getOwner()->getParent(), $this->itemPhoto($item->getOwner()->getParent(), 'thumb.icon')); ?><?php } ?><?php if(in_array('by', $allParams['show_criteria'])){ ?><?php echo $this->htmlLink($item->getOwner()->getHref(),$item->getOwner()->getTitle() ) ?><?php } ?>
      </span>
    </div>

  <?php } ?>
<?php } ?>
