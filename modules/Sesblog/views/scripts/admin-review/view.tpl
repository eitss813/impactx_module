<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: view.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<div class="sesbasic_view_stats_popup">
  <h3>Statics of <?php echo $this->item->title;  ?> </h3>
  <table>
    <tr>
      <td><?php echo $this->translate('Title') ?>:</td>
      <td><?php if(!is_null($this->item->title) && $this->item->title != '') {
        echo  $this->item->title ;
        } else { 
        echo "-";
        } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Pros') ?>:</td>
      <td><?php if(!is_null($this->item->pros) && $this->item->pros != '') {
        echo  $this->item->pros ;
        } else { 
        echo "-";
        } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Cons') ?>:</td>
      <td><?php if(!is_null($this->item->cons) && $this->item->cons != '') {
        echo  $this->item->cons ;
        } else { 
        echo "-";
        } ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Owner') ?>:</td>
      <td><?php echo  $this->item->getOwner(); ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Comments') ?>:</td>
      <td><?php echo $this->item->comment_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Likes') ?>:</td>
      <td><?php echo $this->item->like_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Views') ?>:</td>
      <td><?php echo $this->locale()->toNumber($this->item->view_count) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Date') ?>:</td>
      <td><?php echo $this->item->creation_date; ;?></td>
    </tr>
  </table>
  <br />
  <button onclick='javascript:parent.Smoothbox.close()'>
    <?php echo $this->translate("Close") ?>
  </button>
</div>
