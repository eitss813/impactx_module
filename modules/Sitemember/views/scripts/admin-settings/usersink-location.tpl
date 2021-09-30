<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: usersink-locations.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">

  function getLoading() {
    document.getElementById('global_form').style.display = 'none';
    document.getElementById('add_progress_bar').style.display = 'block';
  }

</script>
<?php if (empty($this->error)): ?>
  <div id="add_progress_bar" style="display:none;margin:30px 10px 10px;">
    <div class="settings"><form style="width:450px;"><div class="p15"><div class="bold">Please do not refresh or close this page, until the process is running.<br /><br /><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/progress-bar.gif" alt="Loading.." /></center></div></div></form></div>
  </div>
<?php endif; ?>
	<?php if (empty($this->error)): ?>

		<div id="global_form">
			<form method="post" class="global_form_popup">
				<div>
					<h3><?php echo $this->translate('Sync Member Locations with Google Places'); ?></h3>
					<p><?php echo $this->translate("As we have used Google Places API which will 1000 requests per day. Thus, you will only be able to sync upto 1000 Members in a day. Make sure that you sync rest of the Members later by visiting this setting again."); ?></p>
					<br />

					<?php $memberCount = count($this->row);
					if (empty($memberCount)) :
						?>
						<div class="tip">
							<span> 
								<?php echo $this->translate('There are currently no Members on your site to be synced with Google Places.'); ?>
							</span>
						</div>			
						<button type='submit' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>			
					<?php endif; ?>

					<?php if (!empty($memberCount)) : ?>
						<p>
							<input type="hidden" name="confirm" value=""/>
							<button type='submit' onClick="getLoading()"><?php echo $this->translate('Sync Member Locations'); ?></button>
							or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
						</p>
					<?php endif; ?>
				</div>
			</form>
		</div>
	<?php else: ?>
		<div class="seaocore_tip"><span><?php echo "You have crossed your limit 1000 requests of Google Places API. Please try again after 24 hour to sync your remaining members." ?></span></div>
		<?php if ( !empty( $this->userWithWrongAddress ) ) :?>
			<div> There are some users which have wrong Address. Please check </div>
			<br>
			<br>
			<?php foreach ( $this->userWithWrongAddress as $key => $user) : ?>
				<tr>
		            <td style="padding-right: 10px "><?php echo $user->user_id ?></td>
		            <td  style="padding-right: 10px " class='admin_table_bold'>
		              <?php echo $this->htmlLink($user->getHref(),
		                  $this->string()->truncate($user->getTitle(), 16),
		                  array('target' => '_blank'))?>
		            </td>
		            <td  style="padding-right: 10px " class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $user->user_id)->getHref(), $this->item('user', $user->user_id)->username, array('target' => '_blank')) ?></td>
		            <td  style="padding-right: 10px " class='admin_table_email'>
		                <a href='mailto:<?php echo $user->email ?>'><?php echo $user->email ?></a>
		            </td>
	            </tr>
			<?php endforeach; ?>

		<?php endif; ?>
	<?php endif; ?>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>