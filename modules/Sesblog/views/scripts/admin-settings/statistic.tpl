<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: statistic.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<?php 
  $table = Engine_Api::_()->getDbTable('blogs', 'sesblog');
  $albumTable = Engine_Api::_()->getDbTable('albums', 'sesblog');
  $photoTable = Engine_Api::_()->getDbTable('photos', 'sesblog'); 
?>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate("Blogs Statistics") ?> </h3>
      <p class="description">
        <?php echo $this->translate("Below are some valuable statistics for the Blogs created on this site:"); ?>
      </p>
      <table class='admin_table' style="width: 50%;">
        <tbody>
          <tr>
            <td><strong class="bold"><?php echo "Total Blogs:" ?><strong></td>
            <td><?php echo $table->getItemCount(); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Approved Blogs:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'is_approved')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Featured Blogs:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'featured')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Sponsored Blogs:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'sponsored')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Reviews:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'rating'));; ?></td>
          </tr>  
          <tr>
            <td><strong class="bold"><?php echo "Total Verified Blogs:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'verified')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Favourite Blogs:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'favourite_count')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Comments:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'comment_count')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Views:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'view_count')); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Likes:" ?><strong></td>
            <td><?php echo $table->getItemCount(array('columnName' => 'like_count')); ?></td>
          </tr>  
          <tr>
            <td><strong class="bold"><?php echo "Total Blog Albums:" ?><strong></td>
            <td><?php echo $albumTable->getItemCount(); ?></td>
          </tr>
          <tr>
            <td><strong class="bold"><?php echo "Total Blog Photos:" ?><strong></td>
            <td><?php echo $photoTable->countPhotos(); ?></td>
          </tr>
          <?php if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('sesvideo')):?>
						<tr>
							<td><strong class="bold"><?php echo "Total Blog Videos:" ?><strong></td>
							<td><?php echo Engine_Api::_()->sesblog()->getVideoTotalCount(); ?></td>
						</tr>
          <?php endif;?>      
        </tbody>
      </table>
    </div>
  </form>
</div>
