<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Cbpageanalytics/externals/scripts/jquery-1.12.4.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Cbpageanalytics/externals/scripts/jquery-ui.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Cbpageanalytics/externals/styles/jquery-ui.css'); ?>

<style>
    div.search select{
        height: 30px;
        max-width: 250px;
    }
</style>

<h2>
    <?php echo $this->translate('CB - Page Analytics Plugin') ?>
</h2>

<p>
    <?php echo $this->translate("Here you can view the graphical representation of the page tracking done by your plugin. You can view graphs of all pages, a single page or all the pages of a single user. It also allows you to view graphs by day, week, month and year. If you need more help please contact at <a href='mailto:support@consecutivebytes.com'>support@consecutivebytes.com</a> or create a ticket from your user panel on our <a href='http://www.consecutivebytes.com' target='_blank'>website</a>.") ?>
</p>
<br><br>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div>
    <p><?php echo $this->translate("Here, you can keep track of visits on all pages. You can also get visual graphs of daily, weekly, monthly and yearly traffic.");?></p>
    <br>
</div>

<div class="admin_search">
    <div class="search">
        <?php echo $this->formFilter->render($this) ?>
    </div>
</div>
<br>

<div id="admin_statistics" class="admin_statistics">
    <div class="admin_statistics_nav">
        <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>
        <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
    </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        var currentArgs = {};

        var processStatisticsFilter = function (formElement) {
            var vals = formElement.toQueryString().parseQueryString();
            vals.offset = 0;
            buildChart(vals);
            return false;
        };

        var processStatisticsPage = function (count) {
            var args = $merge(currentArgs);
            args.offset += count;
            buildChart(args);
        };

        var updateFormOptions = function () {
            var periodEl = $$('form').getElement('#period');
            var chunkEl = $$('form').getElement('#chunk');

            switch (periodEl.get('value')[0]) {
                case 'dd':
                    chunkEl.setStyle('display', 'none');
                    break
                case 'ww':
                    chunkEl.setStyle('display', '');

                    var children = chunkEl.getChildren()[0];
                    for (var i = 0, l = children.length; i < l; i++) {
                        if (['dd'].indexOf(children[i].get('value')) == -1) {
                            children[i].setStyle('display', 'none');
                            if (children[i].get('selected')) {
                                children[i].set('selected', false);
                            }
                        } else {
                            children[i].setStyle('display', '');
                        }
                    }

                    break;
                case 'MM':
                    chunkEl.setStyle('display', '');
                    var children = chunkEl.getChildren()[0];
                    for (var i = 0, l = children.length; i < l; i++) {
                        if (['dd', 'ww'].indexOf(children[i].get('value')) == -1) {
                            children[i].setStyle('display', 'none');
                            if (children[i].get('selected')) {
                                children[i].set('selected', false);
                            }
                        } else {
                            children[i].setStyle('display', '');
                        }
                    }

                    break;
                case 'y':
                    chunkEl.setStyle('display', '');
                    var children = chunkEl.getChildren()[0];
                    for (var i = 0, l = children.length; i < l; i++) {
                        if (['dd', 'ww', 'MM'].indexOf(children[i].get('value')) == -1) {
                            children[i].setStyle('display', 'none');
                            if (children[i].get('selected')) {
                                children[i].set('selected', false);
                            }
                        } else {
                            children[i].setStyle('display', '');
                        }
                    }

                    break;
                default:
                    chunkEl.setStyle('display', 'none');
                    break;
            }
        };

        var buildChart = function (args) {
            currentArgs = args;
            $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

            var url = new URI('<?php echo (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-data')) ?>');
            url.setData(args);
            while ($('my_chart').firstChild) {
                $('my_chart').removeChild($('my_chart').firstChild);
            }

            $('loading').setStyle('display', '').inject($('my_chart'));
            var req = new Request.JSON({
                url: url.toString(),
                data: {
                    format: 'json'
                },
                onComplete: function (responseJSON) {
                    $('loading').setStyle('display', 'none').inject($('admin_statistics'));
                    google.charts.setOnLoadCallback(drawChart(responseJSON));
                }
            });

            (function () {
                req.send();
            }).delay(250);
        };

        window.addEvent('load', function () {
            updateFormOptions();
            $('period').addEvent('change', function (event) {
                updateFormOptions();
            });

            buildChart({
                'page': '1',
                'chunk': 'dd',
                'period': 'dd',
                'start': 0,
                'offset': 0
            });
        });

        google.charts.load('current', {'packages': ['corechart']});

        function drawChart(response) {
            var data = [];

            for (var key in response.data) {
                if (response.data.hasOwnProperty(key)) {
                    for (var k in response.data[key]) {
                        data.push([k, response.data[key][k]]);
                    }
                }
            }
            
            var dataTable = new google.visualization.DataTable();

            dataTable.addColumn('string', 'Page');
            dataTable.addColumn('number', 'Views');
            dataTable.addRows(data);
            
            var options = {
                title: response.title,
                legend: {position: 'bottom'}
            };

            var chart = new google.visualization.LineChart(document.getElementById('my_chart'));
            chart.draw(dataTable, options);
        }
    </script>

    <div id="my_chart" class="my_chart"></div>
    <div id="loading" style="display: none"></div>
</div>

<style>
    .my_chart {
        height: 450px;
        width: 1000px
    }

    #loading {
        width: inherit;
        height: inherit;
        background-position: center 10%;
        background-repeat: no-repeat;
        background-image: url(application/modules/Core/externals/images/large-loading.gif);
    }
</style>

<script>jQuery.noConflict();</script>
<script type="text/javascript">
    var names = <?php echo json_encode($this->users); ?>
    
    var namesArray = [];
    jQuery.each(names, function (key, value) {
        namesArray.push({id: key, name: value});
    });
    
    jQuery('#user').keyup(function () {
        jQuery('#user').autocomplete({
            source: function (request, response) {
                response(jQuery.map(namesArray, function (value, key) {

                    var name = value.name.toUpperCase();
                    if (name.indexOf(request.term.toUpperCase()) != -1) {
                        return {
                            label: value.name,
                            value: value.id
                        }
                    } else {
                        return null;
                    }
                }));
            },
            select: function (event, ui) {
                event.preventDefault();
                jQuery('#user').val(ui.item.label);
            }
        });
    });
</script> 