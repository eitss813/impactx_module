<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    statistics.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo 'One Time Password (OTP) Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
      <div class='siteotpverifier_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
    <?php endif; ?>
    
     <p>
  <?php echo $this->translate("Use below filters to observe statistics related to SMS sent to users on your website over different time periods.") ?>
      </p>
      <div class="admin_statistics_search search" >
  <?php echo $this->formFilterGraph->render($this) ?>
      </div>
<br><br><br><br>
    <div style="clear:both;height:15px;"></div>
  <div class="admin_statistics" id="admin_statistics">
    <div class="admin_statistics_nav">
      <a id="admin_stats_offset_previous"  class='buttonlink icon_previous' onclick="processStatisticsPage(-1);" href="javascript:void(0);" ><?php echo $this->translate("Previous") ?></a>
      <a id="admin_stats_offset_next" class='buttonlink_right icon_next' onclick="processStatisticsPage(1);" href="javascript:void(0);" style="display:none;float:right;"><?php echo $this->translate("Next") ?></a>
    </div>

    <div id="my_chart" class="my_chart"></div>
    <center id ="loading" style="display: none;"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteotpverifier/externals/images/loading.gif' /></center>
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
            var req = new Request.JSON({
              url : url.toString(),
              data : {
                format : 'json',
              },
              onRequest: function () {
                $('loading').show();
              },
              onComplete : function(responseJSON) {
                google.charts.setOnLoadCallback(drawChart(responseJSON));
                $('loading').hide();
              }
            });
            (function() {
              req.send();
            }).delay(250);
          }
            buildChart({
              'type' : 'amazon',
              'mode' : 'all',
              'chunk' : 'dd',
              'period' : 'ww',
              'start' : 0,
              'offset' : 0,
              'ad_subject': 'ad'
          });
          google.charts.load('current', {'packages':['line']});
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
              chart: {
                title: response.title,
              },
              series: {
                // Gives each series an axis name that matches the Y-axis below.
                0: {axis: 'yaxis'},
              },
              axes: {
                // Adds labels to each axis; they don't have to match the axis names.
                y: {
                  yaxis: {label: "<?php echo $this->translate('Number of Messages') ?>"},
                }
              }
            };
            var chart = new google.charts.Line(document.getElementById('my_chart'));
            chart.draw(data, options);
          }
        </script>
    </div>  
  <script type="text/javascript">
 function onModeChange() {
    if($('mode') && $('mode').value == 'all'){
      $('type').getParent().show();
    } else {
      $('type').getParent().hide();
    }
 }
  </script> 
<script type="text/javascript">
  // Get Json data of period and chunk.
   var periodOption = JSON.parse( '<?php echo $this->periodOption ?>');
  var chunkOption = JSON.parse( '<?php echo $this->chunkOption ?>' );
  var periodOptionKey = JSON.parse( '<?php echo $this->periodOptionKey ?>');
  var chunkOptionKey = JSON.parse( '<?php echo $this->chunkOptionKey ?>');
  window.onload = function() {
    $('chunk').getElements('option').invoke('remove');
    addOption('ww','dd');
  };
  $('period').addEvent('change',function(event) {
    
    var currentPeriod = this.getElement(':selected').value;
    var currentchunk = $('chunk').getElement(':selected').value;
    // Remove option here. 
    $('chunk').getElements('option').invoke('remove');
    // Now add options.
    addOption( currentPeriod, currentchunk );
  });
  function addOption( currentPeriod, currentchunk ) {
    //Add an element   
    for (var chunkKey in chunkOption ) {
      if( periodOptionKey.indexOf( currentPeriod ) >= chunkOptionKey.indexOf( chunkKey ) || chunkOptionKey.indexOf( currentPeriod ) == -1 ) {
        var newOption = new Option( chunkOption[ chunkKey ],chunkKey);
        newOption.inject($('chunk'))
      }
    }
    // Set default value
    $('chunk').value = chunkOptionKey[ 0 ] ;
  }
  </script>
  <style type="text/css">
    a.icon_next{
      padding-right: 20px;
      background-position: top right;
      background-repeat: no-repeat;
      font-weight: bold;
    } 
    .cadmc_statistics_search {
      margin-bottom: 30px;
    }
    .custom-divs {
      display: inline-block;
      margin-top: 10px;
    }
    .custom-divs-first {
      display: inline-block;
      margin-top: 10px;
    }
    object{
          margin-top: 50px;
    }
    .label-field{
      margin-top: 5px; 
    }
	  .search button.label-field {
		  margin-top: 0;
	  }
  </style>