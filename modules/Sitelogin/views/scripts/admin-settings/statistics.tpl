<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    statistics.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<h2>
    <?php echo $this->translate("Social Login and Sign-up Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
<?php endif; ?>

<?php
$serviceColors = array(
  'facebook' => '#3D5998',
  'twitter' => '#4EA4DD',
  'linkedin' => '#3271B8',
  'google' => '#DB4437',
  'pinterest' => '#BD081C',
  'flickr' => '#00AFF0',
  'instagram' => '#FFA520',
  'outlook' => '#EC2127',
  'vk' => '#4c6c91',
  'yahoo' => '#9F07D4',
);
?>
<h3>
  Statistics
</h3>
<p>
  Here, you can view the stats for number of Signups on your website with each of the social networking site. 
</p>
<br />

<br />
<?php
$data = $slices = array();
$data[] = array('Social Networking Service', 'Signup');
?>
<div class='sitelogin_social_services_wapper'>
  <div class="sitelogin_social_services_statistics">
      <div class="total_heading"><b>Total Signup With Social Sites : <?php echo $this->serviceStatistics['total'] ?></b></div>

    <?php if( $this->serviceStatistics['total'] ): ?>
      <div class="sitelogin_social_services_statistics_graph_c">
        <div class="sitelogin_social_services_statistics_graph" id="piechart">
        </div>
      </div>
    <?php endif; ?>
      <div>
        <ul>
          <?php foreach( $this->serviceNames as $service => $serviceName ): ?>
            <?php
            $color = $serviceColors[$service];
            $count = $this->serviceStatistics['data'][$service]
            ?>
            <li class="sitelogin_social_service_<?php echo $service ?>" style="border-color: <?php echo $color ?>">
              <span></span>
              <span><?php echo $serviceName ?></span>
              <span style="color: <?php echo $color ?>"><?php echo $count ?></span>
              <?php $data[] = array($serviceName, $count) ?>
              <?php $slices[] = array('color' => $color); ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
  </div>
</div>
<?php if( !empty($slices) ): ?>
  <?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitelogin/externals/scripts/charts/loader.js')
  ?> 
  <!--  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
  <script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable(<?php echo $this->jsonInline($data) ?>);
      var options = {
        title: '',
        slices: <?php echo $this->jsonInline($slices) ?>,
        // is3D: true,
        // pieHole: 0.1,
      };
      var chart = new google.visualization.PieChart(document.getElementById('piechart'));
      chart.draw(data, options);
    }
  </script>
<?php endif; ?>