<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">

// filter the dropdown of form used to filter graphical stats
function filterDropdown(element) {
    var optn1 = document.createElement("OPTION");
		optn1.text = '<?php echo $this->translate("By Week") ?>';
		optn1.value = '<?php echo Zend_Date::WEEK; ?>';
    var optn2 = document.createElement("OPTION");
		optn2.text = '<?php echo $this->translate("By Month") ?>';
		optn2.value = '<?php echo Zend_Date::MONTH; ?>';

    switch(element.value) {
      case 'ww':
			removeOption('ww');
			removeOption('MM');
      break;

      case 'MM':
			addOption(optn1,'ww' );
			removeOption('MM');
      break;

      case 'y':
			addOption(optn1,'ww' );
			addOption(optn2,'MM' );
      break;
    }
  }

	// add an option to the dropdown
  function addOption(option,value )
  {
    var addoption = false;
		for (var i = ($('chunk').options.length-1); i >= 0; i--) {
			var val = $('chunk').options[ i ].value; 
			if (val == value) {
				addoption = true;
				break; 
			}
		}
		if(!addoption) {
			$('chunk').options.add(option);
		}
  }

	// remove an option from the dropdown
	function removeOption(value) 
  {
    for (var i = ($('chunk').options.length-1); i >= 0; i--) 
    { 
      var val = $('chunk').options[ i ].value; 
      if (val == value) {
				$('chunk').options[i] = null;
				break; 
      }
    } 
  }
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
	<div class="generic_layout_container layout_core_content">
	<?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

		<div class="layout_middle">
			<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
			<?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Insights', 'sectionDescription' => '')); ?>
			<div class="sitepage_edit_content">
					<div id="show_tab_content">
						<div class="sitepage_edit_insights_table">
						    <table>
								<thead>
									<tr>
									  	<th><?php echo $this->translate('Total Views') ?></th>
										  <th><?php echo $this->translate('Total Likes') ?></th>
											<?php if(!empty($this->show_comments)) : ?>
												<th><?php echo $this->translate('Total Comments') ?></th>
											<?php endif; ?>
										<th><?php echo $this->translate('Monthly Active Users') ?></th>
									</tr>
								</thead>
						      <tbody>
									<tr>
										<td><?php echo number_format($this->total_views); ?></td>
										<td><?php echo number_format($this->total_likes); ?></td>
											<?php if(!empty($this->show_comments)) : ?>
												<td><?php echo number_format($this->total_comments); ?></td>
											<?php endif; ?>
										<td><?php echo number_format($this->total_users); ?></td>
									</tr>
						      </tbody>
						    </table>
						</div>
						<div class="sitepage_edit_insights">
							<div>
							    <h4><?php echo $this->translate("Page Insights") ?></h4>
							    <p>
							      <?php echo $this->translate("Use the below filter to observe various metrics of your page over different time periods.") ?>
							    </p>
								<div class="sitepage_edit_insights_search">
									<?php echo $this->filterForm->render($this) ?>
								</div>
							    <div class="sitepage_statistics" id ="sitepage_statistics">
							    	<div class="sitepage_edit_insights_nav">
										<a id="admin_stats_offset_previous"  class='icon_previous' onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>
										<a id="admin_stats_offset_next"   class='icon_next' onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
									</div>
									<div id="my_chart" class="my_chart"></div>
									<div id="loading" style="display: none"></div>
									<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
									  <script type="text/javascript">
									    var currentArgs = {};
									    var processStatisticsFilter = function(formElement) {
									      var vals = formElement.toQueryString().parseQueryString();
									      vals.offset = 0;
									      buildChart(vals);
									      return false;
									    }
									    var processStatisticsPage = function(count) {
									      var args = $merge(currentArgs);
									      args.offset += count;
									      buildChart(args);
									    }
									    var buildChart = function(args) {
									      currentArgs = args;
									      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));
									      var url = new URI('<?php echo $this->url(array('action' => 'chart-data')) ?>');
									      url.setData(args);
									      while ($('my_chart').firstChild) {
									        $('my_chart').removeChild($('my_chart').firstChild);
									      }
									      $('loading').setStyle('display', '').inject($('my_chart'));
									      var req = new Request.JSON({
									        url : url.toString(),
									        data : {
									          format : 'json',
									        },
									        onComplete : function(responseJSON) {
									          $('loading').setStyle('display', 'none').inject($('sitepage_statistics'));
									          google.charts.setOnLoadCallback(drawChart(responseJSON));
									        }
									      });
									      (function() {
									        req.send();
									      }).delay(250);
									    }
									      buildChart({
									        'type' : 'all',
									        'mode' : 'normal',
									        'chunk' : 'dd',
									        'period' : 'ww',
									        'start' : 0,
									        'offset' : 0
									    });
									    google.charts.load('current', {'packages':['corechart']});
									    function drawChart(response) {
									    if(response.case == "all") {
									    	var data = google.visualization.arrayToDataTable(response.data);
									    }
									    else {
									    	var data = [];
									    	for (var key in response.data) {
									        if (response.data.hasOwnProperty(key)) {
									          data.push([key, response.data[key]]);
									        }
									    }
									      var data = google.visualization.arrayToDataTable(data);
									    }
									      var options = {
									        title: response.title,
									        legend: { position: 'bottom' }
									      };
									      var chart = new google.visualization.LineChart(document.getElementById('my_chart'));
									      chart.draw(data, options);
									    }
									  </script>
									</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>  
</div>