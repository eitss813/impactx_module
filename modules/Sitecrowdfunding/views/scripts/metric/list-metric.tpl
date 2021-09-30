<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl',
    array('project' => $this->project, 'sectionTitle'=> 'Metric Details','sectionDescription' => '')); ?>
    <br/><br/>
    <div class="sitepage_edit_content">
        <div id="show_tab_content">
            <div class="count_div">
                <h3><?php echo count($this->metrics);?> metrics(s) found.  </h3>
            </div>
            <table class="transaction_table admin_table seaocore_admin_table">
                <thead>
                    <tr class="sitecrowdfunding_detail_table_head">
                        <th class="header_title_small">S.No </th>
                        <th class="header_title_big">Name </th>
                        <th class="header_title_big">Description </th>
                        <th class="header_title">Unit </th>
                        <th class="header_title">Visibility </th>
                    </tr>
                </thead>
                <?php $i=1;?>
                <?php foreach ($this->metrics as $metric):?>
                    <tr>
                        <td class="header_title_small">
                            <?php echo $i; ?>
                        </td>
                        <td class="header_title_big">
                            <?php echo $metric->metric_name; ?>
                        </td>
                        <td class="header_title_big">
                            <?php echo $metric->metric_description; ?>
                        </td>
                        <td class="header_title">
                            <?php echo $metric->metric_unit; ?>
                        </td>
                        <td class="header_title">
                            <label class="switch">
                                <input class="custom_toggle" type="checkbox" onclick="visibility('<?php echo $metric->metric_id ;?>')" <?php echo $metric->project_side_visibility ? " checked" : "" ; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                    <?php $i++;?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<style>

    th.header_title_small {
        width: 5%;
    }

    th.header_title_big {
        width: 15%;
    }
    th.header_title {
        width: 10%;
    }

    table.transaction_table.admin_table.seaocore_admin_table {
        width: 100%;
    }

    table.admin_table tbody tr:nth-child(even) {
        background-color: #f8f8f8
    }

    table.admin_table td{
        padding: 10px;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        border-bottom: 1px solid #aaa;
        font-weight: bold;
        height: 45px;
        padding-top: 7px;
        padding-bottom: 7px;
        white-space: nowrap;
        color: #5ba1cd !important;
    }
    .admin_table_centered {
        text-align: center;
    }


    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom_toggle:checked + .slider {
        background-color: #2196F3;
    }

    .custom_toggle:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    .custom_toggle:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<script>
    function visibility(metric_id){
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'sitecrowdfunding/metric/visible-metric',
            method: 'POST',
            data: {
                format: 'json',
                metric_id: metric_id
            },
            onRequest: function () {
                //console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
                location.reload();
            }
        });
        request.send();
    }
</script>
