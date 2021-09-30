<div id="initiative_metric">

    <?php $initiativeMetrics = $this->metrics; ?>

    <?php if($initiativeMetrics->getTotalItemCount() > 0 ): ?>
        <div class="initiative_container">
            <div class="sitecoretheme_counter_container" id ='sitecoretheme_counter_container'>
                <div class="sitecoretheme_container">
                    <div class="sitecoretheme_counter_statistic">
    
                        <!-- Prev Icon Page -->
                        <?php if($initiativeMetrics->getTotalItemCount() > 4 && $this->metric_page_no != 1 ):?>
                            <div id="prev_spinner" class="arrow-button prev-button" style="z-index: 999;">
                                <i onclick="slidePrevMetrics('<?php echo $this->metric_page_no; ?>')" style="font-size: 19px; display: flex;justify-content: center; color: white;margin-bottom: 4px;" class="fa fa-angle-left" aria-hidden="true">
                                </i>
                            </div>
                        <?php endif; ?>

                        <!-- list -->
                        <div id="metric_list" class="metric_list" style="display: flex;justify-content: center;width: 100%">
                            <?php foreach($initiativeMetrics as $initiativeMetric): ?>
                                <?php $metric_id = $initiativeMetric['metric_id'];?>
                                <div class="sitecoretheme_counter_statistic_3" style="cursor: pointer" onclick='redirectMetricPage("<?php echo $this->url(array( 'module' => 'sitepage' ,'controller' => 'metrics', 'action' => 'index', 'metric_id' => $metric_id), 'default', true) ?>")' >
                                    <div class="sitecoretheme_counter_wrapper">
                                        <div class="metrics_info_details">
                                            <?php $total_aggregate_value = 0; ?>

                                            <?php
                                                // get field_id
                                                $field_ids = array();
                                                foreach (Engine_Api::_()->fields()->getFieldsMeta('yndynamicform_entry') as $field) {
                                                    if ($field->type == 'metrics') {
                                                        $fieldMeta = Engine_Api::_()->fields()->getField($field->field_id, 'yndynamicform_entry');
                                                        if ($fieldMeta->config['selected_metric_id'] == $metric_id) {
                                                            $field_ids[] = $field->field_id;
                                                        }
                                                    }
                                                }
                                            ?>

                                            <?php if (count($field_ids)):?>
                                                <?php

                                                    $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
                                                    $entryTableName = $entryTable->info('name');

                                                    $valuesTableName = 'engine4_yndynamicform_entry_fields_values';

                                                    // get total aggregate value
                                                    $project_aggregate_value = $entryTable->select()
                                                        ->setIntegrityCheck(false)
                                                        ->from($entryTableName, array("SUM($valuesTableName.value) as project_aggregate"))
                                                        ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
                                                        ->where("$valuesTableName.field_id in (?)", $field_ids)
                                                        ->where("$entryTableName.project_id IS NOT NULL")
                                                        ->where("$entryTableName.user_id IS NULL")
                                                        ->query()
                                                        ->fetchColumn();

                                                    $user_aggregate_value = $entryTable->select()
                                                        ->setIntegrityCheck(false)
                                                        ->from($entryTableName, array("SUM($valuesTableName.value) as user_aggregate"))
                                                        ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
                                                        ->where("$valuesTableName.field_id in (?)", $field_ids)
                                                        ->where("$entryTableName.project_id IS NULL")
                                                        ->where("$entryTableName.user_id IS NOT NULL")
                                                        ->query()
                                                        ->fetchColumn();

                                                    $project_aggregate_value = $project_aggregate_value;
                                                    $user_aggregate_value = $user_aggregate_value;
                                                    $totalAggregateValue = (int)$project_aggregate_value + (int)$user_aggregate_value;

                                            ?>
                                                <h4><?php echo  $totalAggregateValue.' '.$initiativeMetric['metric_unit']; ?></h4>
                                            <?php else:?>
                                                <h4><?php echo  '0 '.$initiativeMetric['metric_unit']; ?></h4>
                                            <?php endif;?>


                                            <p><?php echo $initiativeMetric['metric_name']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;?>
                        </div>
    
                        <!-- Next Icon Page -->
                        <?php if($initiativeMetrics->getTotalItemCount() > 4 && $this->metric_page_no != $initiativeMetrics->getPages()->pageCount ):?>
                            <div id="next_spinner" class="arrow-button next-button" style="">
                                <i onclick="slideNextMetrics('<?php echo $this->metric_page_no; ?>')"      style="font-size: 19px; display: flex;justify-content: center; color: white;margin-bottom: 4px;"
                                   class="fa fa-angle-right" aria-hidden="true">
                                </i>
                            </div>
                        <?php endif; ?>
    
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="hidden_ajax_metrics_data" style="display: none;"></div>

<script>
    function slidePrevMetrics(page_no) {
        var spinner_name = 'metric_list';
        var page_nos = parseInt(page_no) - 1;
        var url = en4.core.baseUrl + "<?php echo $this->ajaxUrlPath;?>"+"/metric_page_no/"+page_nos;
        $(spinner_name).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var request = new Request.HTML({
            url: url,
            data:{
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0
            },
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_metrics_data').innerHTML = responseHTML;
                $('initiative_metric').innerHTML = $('hidden_ajax_metrics_data').getElement('#initiative_metric').innerHTML;
                $('hidden_ajax_metrics_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($(spinner_name));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }
    function slideNextMetrics(page_no) {
        var spinner_name = 'metric_list';
        var page_nos = parseInt(page_no) + 1;
        var url = en4.core.baseUrl + "<?php echo $this->ajaxUrlPath;?>"+"/metric_page_no/"+page_nos;
        $(spinner_name).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var request = new Request.HTML({
            url: url,
            data:{
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0
            },
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_metrics_data').innerHTML = responseHTML;
                $('initiative_metric').innerHTML = $('hidden_ajax_metrics_data').getElement('#initiative_metric').innerHTML;
                $('hidden_ajax_metrics_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($(spinner_name));
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }

    function redirectMetricPage(url) {
        let protocol = "<?php echo ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http'; ?>";
        let host= "<?php echo $_SERVER['HTTP_HOST']; ?>";
        var full_url = protocol+"://"+host+url;
        window.location.href = full_url;
    }
</script>

<style>
    .sitecoretheme_counter_statistic {
        width: 100%;
        float: left;
        font-size: 0;
        text-align: center;
        position: relative;
        /* top: -91px; */
        bottom: 39px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: unset !important;
    }
    .sitecoretheme_counter_statistic {
        width: 100%;
        float: left;
        font-size: 0;
        text-align: center;
    }
    .sitecoretheme_counter_statistic_3 {
        background: #fff;
        margin: 2%;
        padding: 35px 20px;
        box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);
        border-radius: 3px;
        width: 25%;
        height: 179px;
        text-align: center;
        align-items: center;
        justify-content: center;
        display: flex;
    }
    .sitecoretheme_counter_statistic_3 {
        position: relative;
        padding-left: 10px;
        padding-right: 10px;
        box-sizing: border-box;
        display: inline-block;
        vertical-align: top;
        width: 25%;
    }
    .sitecoretheme_counter_wrapper {
        text-align: center;
        width: 100%;
        float: left;
        overflow: hidden;
    }
    .sitecoretheme_counter_wrapper .metrics_info_details {
        padding-left: unset !important;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        vertical-align: top;
        width: calc(100% - 0px);
    }
    .sitecoretheme_counter_wrapper .metrics_info_details {
        padding-left: 45px;
        position: relative;
        display: inline-block;
        vertical-align: top;
        width: calc(100% - 100px);
    }
    @media (min-width: 921px) {
        .sitecoretheme_counter_wrapper h4 {
            font-size: 33px;
            line-height: 33px;
            text-align: center;
        }
    }
    .sitecoretheme_counter_wrapper h4 {
        float: none !important;
        text-align: center;
        margin-top: 0;
        font-weight: 400;
        color: #222;
        margin-bottom: 0;
        padding: 0;
    }
    .sitecoretheme_counter_wrapper p {
        display: flex;
        justify-content: center;
    }
    .metrics_info_details p {
        text-align: center;
    }
    .sitecoretheme_counter_wrapper p {
        margin-top: 3px;
        width: 100%;
        font-weight: 300;
        font-size: 14px;
        line-height: 26px;
        float: left;
        color: #222;
        letter-spacing: .8px;
        text-transform: uppercase;
    }
    #next_spinner {
        background-color: #44AEC1;
        float: right;
        position: relative;
        bottom: 138px;
        border-radius: 26px;
        width: 15px;
        padding: 4px 8px;
        cursor: pointer;
        position: unset !important;
    }
    #prev_spinner {
        background-color: #44AEC1;
        border-radius: 26px;
        width: 15px;
        padding: 4px 8px;
        cursor: pointer;
        position: unset !important;
    }
    .arrow-button.prev-button {
        background-color: #44AEC1;
        border-radius: 26px;
        width: 15px;
        padding: 4px 8px;
        position: relative;
        top: 114px !important;
    }
</style>