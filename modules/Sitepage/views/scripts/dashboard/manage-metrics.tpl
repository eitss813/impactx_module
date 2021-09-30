<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editstyle.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Metrics', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                <!-- <h3 style="font-size: 22px !important;"> Create Metric</h3>-->
                <button   onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'sitepage', 'controller' => 'dashboard', 'action' => 'edit-metrics',  'page_id' => $this->page_id,'metric_id'=>null, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" >Add Metric</button>
                <!-- <?php  echo $this->form->render(); ?> -->
            </div>
            <div class="sitepage_edit_content">
                <div id="show_tab_content">
                    <div class="count_div">
                        <h3><?php echo count($this->metricsList);?> metrics(s) found.  </h3>
                    </div>
                    <table class="transaction_table admin_table seaocore_admin_table">
                        <thead>
                        <tr class="sitecrowdfunding_detail_table_head">

                            <th class="header_title_small">S.No </th>

                            <!-- Menu Name -->
                            <th class="header_title_big">Name </th>

                            <!-- Menu Description -->
                            <th class="header_title_big">Description </th>

                            <!-- Menu Unit -->
                            <th class="header_title">Unit </th>

                            <!-- Menu Unit -->
                            <th class="header_title">Options </th>

                        </tr>
                        </thead>

                        <?php $i=1;?>
                        <?php foreach ($this->metricsList as $metric) { ?>
                        <tr>
                            <?php $metricUrl = $this->url(array('action' => 'index','metric_id' => $metric->metric_id), "sitepage_metrics");?>

                            <td class="header_title_small">
                                <?php echo $i; ?>
                            </td>
                            <td class="header_title_big">
                                <a href="<?php echo $metricUrl?>" title="<?php echo $metric->metric_name?>">
                                    <?php echo $metric->metric_name; ?>
                                </a>
                            </td>
                            <td class="header_title_big">
                                <?php echo $metric->metric_description; ?>
                            </td>
                            <td class="header_title">
                                <?php echo $metric->metric_unit; ?>
                            </td>
                            <td class="header_title">
                                <a  href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'sitepage', 'controller' => 'dashboard', 'action' => 'edit-metrics',  'page_id' => $this->page_id,'metric_id'=>$metric->metric_id, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" >Edit</a>
                            </td>
                        </tr>
                        <?php $i++;?>
                        <?php } ?>
                    </table>
                </div>
            </div>
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




</style>