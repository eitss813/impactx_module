<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-webpage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

	<div class="layout_middle">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
	 <div class="sitepage_edit_content">
		<div class="sitepage_edit_header">
			<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
			<?php if($this->sitepage->draft == 0) echo $this->htmlLink(array('route' => 'sitepage_publish', 'page_id' => $this->sitepage->page_id), $this->translate('Mark As Live'), array('class'=>'smoothbox')) ?>
			<h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
		</div>
    <div id="show_tab_content">
			<div class="sitepage_report_head">
				<h3><?php echo $this->translate('View Page Report') ?></h3>
				<div class="cmad_hr_link">
					<a href='<?php echo $this->url(array('page_id' => $this->page_id), 'sitepage_reports', true) ?>'>&laquo; <?php echo $this->translate('Generate Another Report')?></a>
				</div>	
			</div>
		
			<div class="sitepage_report_table">
				<table>
					<thead>
						<tr>
							<th><?php echo $this->translate('Time Summary') ?></th>
							<th><?php echo $this->translate('Duration') ?></th>
						</tr>	
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo $this->translate($this->values['time_summary']); ?>
							</td>
							<td>
								<?php
										$startTime = $endTime = date('Y-m-d');
										if(!empty($this->values['time_summary'])) {
											if($this->values['time_summary'] == 'Monthly') {
												$startTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_start'], date('d'), $this->values['year_start']));
												$endTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_end'], date('d'), $this->values['year_end']));
											}
											else {
												if (!empty($this->values['start_daily_time'])) {
													$start = $this->values['start_daily_time'];
												}
												if (!empty($this->values['start_daily_time'])) {
												$end = $this->values['end_daily_time'];
												}

												$labelDate_start = new Zend_Date();
												$labelDate_start->set($start);
												$startTime = $this->locale()->toDate($labelDate_start, array('size' => 'long'));

												$labelDate_end = new Zend_Date();
												$labelDate_end->set($end);
												$endTime = $this->locale()->toDate($labelDate_end, array('size' => 'long'));
											}
										}
										echo $startTime.$this->translate(" to "). $endTime;
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

<?php if(count($this->rawdata)) : ?>
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
		<div class="sitepage_total_reports">
			<div><?php echo $this->translate(array("<span> %s </span> View", "<span> %s </span> Views", $this->total_views), $this->locale()->toNumber($this->total_views)) ?></div>
			<div><?php
							if(empty($this->totallikes)) { 
								$this->totallikes = 0;
							}
							echo $this->translate(array("<span> %s </span> Like", "<span> %s </span> Likes", $this->totallikes), $this->locale()->toNumber($this->totallikes))
							?></div>
			<div><?php 
							if(empty($this->totalcomments)) { 
								$this->totalcomments = 0;
							}
							echo $this->translate(array("<span> %s </span> Comment", "<span> %s </span> Comments", $this->totalcomments), $this->locale()->toNumber($this->totalcomments)) ?></div>
			<div><?php echo $this->translate(array("<span> %s </span> Active User", "<span> %s </span> Active Users", $this->totalusers), $this->locale()->toNumber($this->totalusers)) ?></div>
		</div>
		<div class="sitepage_reports_list_wrapper">
			<div id='stat_table'>
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
			</div>
		</div>
  </div>
</div>
            </div>
</div>
<?php elseif(!count($this->rawdata) && $this->post == 1) :?>
	<div class="tip">
  	<span>
    	<?php echo $this->translate("There are no activities found in the selected date range.") ?>
    </span>
  </div>
<?php endif; ?>