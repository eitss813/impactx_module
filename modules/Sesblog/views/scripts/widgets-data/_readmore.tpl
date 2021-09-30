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
<?php if(in_array('readmore', $allParams['show_criteria'])):?>
  <div class="sesblog_list_readmore"><a class="sesblog_animation" href="<?php echo $item->getHref();?>"><?php echo $this->translate('More');?></a></div>
<?php endif;?>
