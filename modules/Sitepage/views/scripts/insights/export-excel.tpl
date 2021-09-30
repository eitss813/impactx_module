<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-excel.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(count($this->rawdata)) : ?>
	<?php if($this->values['format_report'] == '1') : ?>
    <?php
      header("Expires: 0");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      header("Content-type: application/vnd.ms-excel;charset:UTF-8");
      header("Content-Disposition: attachment; filename=Report.xls"); 
      print "\n"; // Add a line, unless excel error..
    ?>
  <?php endif; ?>
	<?php 
		switch($this->values['time_summary']) {

			case 'Monthly':
			$date_label = 'Month';
			break;

			case 'Daily':
			$date_label = 'Date';
			break;
		}
  ?>
	<table border="0">
		<tr>
			<th><?php echo $this->translate($date_label); ?></th>
			<th><?php echo $this->translate("Views") ?></th>
			<th><?php echo $this->translate("Likes") ?></th>
			<?php if(!empty($this->show_comments)) : ?>
				<th><?php echo $this->translate("Comments") ?></th>
			<?php endif; ?>
			<th><?php echo $this->translate("Active Users") ?></th>
		</tr>
		<?php foreach($this->rawdata as $key => $data) : ?>
				
			<tr>
				<td><?php echo $data['date_value']; ?></td>
				<td title='<?php echo $this->translate("Views") ?>'>
					<?php if(!empty($data['views'])) {
									echo number_format($data['views']);
								}
								else echo "0";
					?>
				</td>
				<td title='<?php echo $this->translate("Likes") ?>'>
					<?php if(!empty($data['likes'])) {
									echo number_format($data['likes']);
								}
								else echo "0";
					?>
				</td>
				<td title='<?php echo $this->translate("Comments") ?>'>
					<?php if(!empty($data['comments'])) {
									echo number_format($data['comments']);
								}
								else echo "0";
					?>
				</td>
				<td title='<?php echo $this->translate("Active Users") ?>'>
					<?php if(!empty($data['active_users'])) {
									echo number_format($data['active_users']);
								}
								else echo "0";
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>