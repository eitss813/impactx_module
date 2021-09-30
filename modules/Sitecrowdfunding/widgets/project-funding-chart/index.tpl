<div class="funding_chart_custom"><div id="chart" style="display: flex;
    justify-content: center;"></div>
    <ul class="backed_amount">
        <li>
            <a class="see_all_backers_btn" onclick="seeAllBackers()" href="javascript:void(0);">
                <?php echo $this->translate("Funded by %s people", $this->memberCount); ?>
                <?php if($this->orgCount > 0): ?>
                <?php echo $this->translate(" and %s organization", $this->orgCount); ?>
                <?php endif; ?>
            </a>
        </li>
        <li class="fright project_status_info">

            <?php $days = Engine_Api::_()->sitecrowdfunding()->findDays($this->project->funding_end_date); ?>
            <?php $daysToStart = Engine_Api::_()->sitecrowdfunding()->findDays($this->project->funding_start_date); ?>
            <?php
                        $currentDate = date('Y-m-d');
                        $projectStartDate = date('Y-m-d', strtotime($this->project->funding_start_date));
            ?>
            <?php if ($this->project->state == 'successful') : ?>
            <?php echo $this->translate("Funding Successful"); ?>
            <?php elseif ($this->project->state == 'failed') : ?>
            <?php echo $this->translate("Funding Failed"); ?>
            <?php elseif ($this->project->state == 'draft') : ?>
            <?php echo $this->translate("In Draft mode"); ?>
            <?php elseif (strtotime($currentDate) < strtotime($projectStartDate)): ?>
            <?php echo $daysToStart; ?>
            <?php echo $this->translate(array('%s Day to Live', '%s Days to Live', $daysToStart), ''); ?>
            <?php elseif ($this->project->lifetime): ?>
            <?php echo $this->translate('Life Time'); ?>
            <?php elseif ($days >= 1): ?>
            <?php echo $days; ?>
            <?php echo $this->translate(array('%s Day Left', '%s Days Left', $days), ''); ?>
            <?php else: ?>
            <?php echo $this->translate($this->project->getProjectFundingStatus()); ?>
            <?php endif; ?>
        </li>
    </ul>
</div>
<style>
    .funding_chart_custom{
   
    }
</style>
<script src='https://cdn.plot.ly/plotly-latest.min.js'></script>
<script type="text/javascript">
    window.addEvent('domready',function () {
        var data = [{
            marker:{
                colors:[
                    "#D3D3D3",
                    <?php
                        /* $totalItem = count($this->fundingData);
                        foreach($this->fundingData as $key => $item){
                            if($key === $totalItem - 1){
                                echo "'#D3D3D3',";
                            }else{
                                echo "'#" . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)."',";
                            }
                        } */
                        foreach($this->fundingData as $item){
                            if($item['title']  == 'Already Funded') {
                                 echo "'#f08b0f',";
                            }
                            if($item['title']  == 'Family Contribution') {

                                 echo "'#2054bb',";
                            }
                        }
                    ?>
                ]
            },
            values: [
                <?php
                    foreach($this->fundingData as $item){
                        echo number_format((float)$item['funding_amount'], 2, '.', '').",";
                    }
                ?>
            ],
            labels: [
            <?php
                foreach($this->fundingData as $item){
                    echo "'" . $item['title'] ." - ". Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item['funding_amount'])."',";
                }
            ?>
            ],
            type: 'pie',
            hoverinfo: "label+percent",
            textposition: "outside",
            automargin: true
        }];

        var layout = {
            title: '',
            // height: 400,
            // width: 200,
            width: 210,
            height: 300,
            margin: {t: 0, b: 20, l: 0, r: 0},
           // margin: {
           //      l: 60,
           //      r: 10,
           //      b: 0,
           //      t: 10,
           //      pad: 4
           //  },
            showlegend: true,
        legend: {x: 0.1, y: 6.5}
        };

        Plotly.newPlot('chart', data, layout);

    })


    // Load google charts
    //google.charts.load('current', {'packages':['corechart']});
    //google.charts.setOnLoadCallback(drawChart);

    var inputdata = [
        ['Funding', 'Fund raised'],
        <?php
            foreach($this->fundingData as $item){
                echo "['" . $item['title'] ." - ". Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item['funding_amount']) . "'," . number_format((float)$item['funding_amount'], 2, '.', ''). "],";
            }
        ?>
    ]
    // Draw the chart and set the chart values
    function drawChart() {
        var data = google.visualization.arrayToDataTable(inputdata);
         console.log('inputdata');
        var formatter = new google.visualization.NumberFormat(
                {negativeColor: 'red', negativeParens: true, pattern: '$###,###'});
        formatter.format(data, 1);
        // Optional; add a title and set the width and height of the chart
        var options = {
            //'pieSliceText': 'value',
            'pieSliceText': 'value-and-percentage',
            'title':'Total fundings: ' +'<?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->fundingAmount); ?>' ,
            'width':900,
            'height':500};

        // Display the chart inside the <div> element with id="piechart"
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
</script>
<?php if(empty($this->fundingData) || count($this->fundingData) == 0): ?>
<style>
    #chart{
        display: none;
    }
    div#chart {
        display: flex !important;
        justify-content: center;
    }
    .infolayer {
        transform: translateX(-92px) !important;
    }
</style>
<?php endif; ?>
<style>
    .modebar-container{
        display: none;
    }
    .legend .legendtext{
        font-size: 10px !important;
    }
</style>
