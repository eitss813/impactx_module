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
<?php if(in_array('location', $allParams['show_criteria']) && $item->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.enable.location', 1)){ ?>
  <div class="sesblog_list_stats sesbasic_text_light sesblog_list_location">
    <span>
      <i class="fa fa-map-marker"></i>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1)) { ?>
        <a href="<?php echo $this->url(array('resource_id' => $item->getIdentity(), 'resource_type'=> $item->getType(), 'action'=>'get-direction'), 'sesbasic_get_direction', true) ;?>" class="opensmoothboxurl" title="<?php echo $item->location;?>"><?php echo $item->location;?></a>
      <?php } else { ?>
        <?php echo $item->location;?>
      <?php } ?>
    </span>
  </div>
<?php } ?>
