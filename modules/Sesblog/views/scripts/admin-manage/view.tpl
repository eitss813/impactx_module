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
<?php $baseURL = $this->layout()->staticBaseUrl; ?>
<div class="sesbasic_view_stats_popup">
  <h3>Details</h3>
  <table>
  	<tr>
			<td colspan="1"><img src="<?php echo $this->item->getPhotoUrl(); ?>" style="height:75px; width:75px;"/></td>
			<td>
				<?php if(!is_null($this->item->getTitle()) && $this->item->getTitle() != ''):?>
					<?php echo  $this->item->getTitle();?>
				<?php else:?> 
					<?php echo "-";?>
				<?php endif; ?>
		  </td>
	  </tr>
    <tr>
      <td><?php echo $this->translate('Owner') ?>:</td>
      <td><?php echo  $this->item->getOwner(); ?></td>
    </tr>
		<?php if(isset($this->item->rating)){ ?>
			<tr>
				<td><?php echo $this->translate('Rating') ?>:</td>
				<td>
				<?php if($this->item->rating != 0) { ?>
					<span title="<?php echo $this->translate(array('%s rating', '%s ratings', round($this->item->rating,1)), $this->locale()->toNumber(round($this->item->rating,1)))?>"><i class="fa fa-star"></i><?php echo round($this->item->rating,1).'/5';?></span>
					<?php }else{ echo "-";} ?>
				</td>
			</tr>
	  <?php } ?>
		<?php if(isset($this->item->ip_address)){ ?>
			<tr>
				<td><?php echo $this->translate('Ip Address') ?>:</td>
				<td>
				  <?php if(!is_null($this->item->ip_address) && $this->item->ip_address != ''):?>
						<?php echo  $this->item->ip_address ;?>
					<?php else:?> 
						<?php echo "-";?>
					<?php endif; ?>
				</td>
			</tr>
		<?php } ?>
    <?php  if(isset($this->item->offtheday)){ ?>
			<tr>
				<td><?php echo $this->translate('Of The Day') ?>:</td>
				<td><?php  if($this->item->offtheday == 1){ ?>
					<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
					<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
				</td>
			</tr>
    <?php } ?>
     <tr>
      <td><?php echo $this->translate('Featured') ?>:</td>
      <td><?php  if($this->item->featured == 1){ ?>
				<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
				<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
     </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Sponsored') ?>:</td>
      <td><?php  if($this->item->sponsored == 1){ ?>
				<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
				<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
     </td>
    </tr>
    <?php  if(isset($this->item->verified)){ ?>
			<tr>
				<td><?php echo $this->translate('Verified') ?>:</td>
				<td><?php  if($this->item->verified == 1){ ?>
					<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
					<img src="<?php echo $baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
				</td>
			</tr>
    <?php } ?>
    <?php if(isset($this->item->comment_count)){ ?>
    <tr>
      <td><?php echo $this->translate('Comments Count') ?>:</td>
      <td><?php echo $this->item->comment_count ?></td>
    </tr>
    <?php } ?>
    <?php if(isset($this->item->like_count)){ ?>
    <tr>
      <td><?php echo $this->translate('Likes Count') ?>:</td>
      <td><?php echo $this->item->like_count ?></td>
    </tr>
    <?php } ?>
    <?php if(isset($this->item->view_count)){ ?>
    <tr>
      <td><?php echo $this->translate('Views Count') ?>:</td>
      <td><?php echo $this->locale()->toNumber($this->item->view_count) ?></td>
    </tr>
    <?php } ?>
    <?php if(isset($this->item->favourite_count)){ ?>
    <tr>
      <td><?php echo $this->translate('Favourites Count') ?>:</td>
      <td><?php echo $this->locale()->toNumber($this->item->favourite_count) ?></td>
    </tr>
    <?php } ?>
    <?php if(isset($this->item->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog_enable_location', 1)){ ?>
    <tr>
      <td><?php echo $this->translate('Location') ?>:</td>
      <td><?php if(!is_null($this->item->location)) echo $this->item->location; else echo '-' ;?></td>
    </tr>
    <?php } ?>
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
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
