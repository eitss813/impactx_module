<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    edit.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css');
$this->headScript()
->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/dynamic.js');
?>



<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>

            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array('sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Forms', 'sectionDescription' => '')); ?>

            <div>
                <?php echo $this->htmlLink(array('controller' => 'manageforms','action' => 'create','page_id'=>$this->page_id),$this->translate('Add New Form'), array('class' => 'button smoothbox')) ?>
            </div>

            <div class="sitepage_edit_content" id="forms_main_container">

              <!-- Search Form -->
              <!--
              <div >
                    <?php if ($this->search_exists): ?>
                            <div class="yndform_manage_form_admin_search clearfix" id="form_search" style="display: block">
                                <?php echo $this->form->render($this);?>
                            </div>
                    <?php endif; ?>
                    <?php if (!$this->search_exists): ?>
                    <div class="yndform_manage_form_admin_search clearfix" id="form_search" style="display: none">
                        <?php echo $this->form->render($this);?>
                    </div>
                    <?php endif; ?>

                    <div >
                        <button id="search_button_show"  onclick="searchBoxShow()" style="float: right;display: block"> Search <i class="fa fa-eye" aria-hidden="true"></i></button>
                        <button id="search_button_hide"  onclick="searchBoxHide()" style="float: right;display: none"> Search <i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                    </div>
                </div>
               -->

                <div id='yndform_manage_form_table'>

                    <?php if (count($this->paginator) > 0): ?>

                        <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>

                        <div style="text-align: center">
                            <span id="spinner_container"></span>
                        </div>

                        <div class="count_div">
                            <h3><?php echo $this->translate('%s Forms(s) found.', $this->paginator->getTotalItemCount()) ?>
                        </div>

                        <div class="sitecrowdfunding_detail_table">
                            <table class="forms_table">
                                <tr class="sitecrowdfunding_detail_table_head">

                                    <!-- Sno -->
                                    <th class="header_title">
                                        <a class="table_heading">
                                            <?php echo $this->translate("Id") ?>
                                        </a>
                                    </th>

                                    <!--Title -->
                                    <?php $class = ( $this->sort_field === 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                    <th class="header_title_big <?php echo $class; ?>">
                                        <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('title', 'asc');">
                                            <?php echo $this->translate("Title") ?>
                                        </a>
                                    </th>

                                    <!-- enable -->
                                    <?php $class = ( $this->sort_field === 'enable' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                    <th class="header_title_big <?php echo $class; ?>">
                                        <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('enable', 'asc');">
                                            <?php echo $this->translate("Status") ?>
                                        </a>
                                    </th>

                                    <!-- view_count -->
                                    <?php $class = ( $this->sort_field === 'view_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                    <th class="header_title_big <?php echo $class; ?>">
                                        <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'asc');">
                                            <?php echo $this->translate("Views") ?>
                                        </a>
                                    </th>

                                    <!-- total_entries -->
                                    <?php $class = ( $this->sort_field === 'total_entries' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                    <th class="header_title_big <?php echo $class; ?>">
                                        <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('total_entries', 'asc');">
                                            <?php echo $this->translate("Submissions") ?>
                                        </a>
                                    </th>

                                    <!-- assigned_projects -->
                                    <?php $class = ( $this->sort_field === 'projects_assigned' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                    <th class="header_title_big <?php echo $class; ?>">
                                        <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('projects_assigned', 'asc');">
                                            <?php echo $this->translate("Assigned") ?>
                                        </a>
                                    </th>


                                    <th class="header_title">
                                        <a>
                                            <?php echo $this->translate("Options") ?>
                                        </a>
                                    </th>

                                </tr>
                                <?php foreach ($this->paginator as $key=>$item): ?>
                                    <tr>
                                        <td><?php echo $item->form_id ?></td>
                                        <td><?php echo $item->title ?></td>
                                        <td>
                                            <label class="switch">
                                                <?php $assign_status =  true; ?>
                                                <input class="custom_toggle" id="custom_toggle_<?php echo $item->form_id; ?>" type="checkbox" onclick="updateStatus(<?php echo $item->form_id; ?>,<?php echo $item->enable; ?>)" <?php echo $item->enable ? "checked" : ""; ?> >
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td><?php echo $item->view_count ?></td>
                                        <td>
                                            <?php
                                                $sub_count = Engine_Api::_()->impactx()->getEntriesCountByFormId($item->form_id);
                                                // $assigned_projects = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
                                                // $sub_count = $assigned_projects->getEntriesCountByFormId($item->form_id);

                                             ?>
                                            <a  href="<?php echo $this->url(array('controller' => 'manageforms','action' => 'list','form_id' => $item->form_id,'page_id' => $this->page_id)); ?>">
                                                <?php echo count($sub_count); ?>
                                            </a>
                                            <?php //echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms' , 'action' => 'list', 'form_id' => $item->form_id,'page_id' => $this->page_id), $this->translate($item->total_entries))?>
                                        </td>
                                        <td>
                                            <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'manageforms','action' => 'select-project','tab_link' =>'all_projects','form_id' => $item->form_id,'page_id' => $this->page_id)); ?>">

                                                <?php
                                                  $form_assigned = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
                                                  $form_assigned_count = $form_assigned->getFormsAssiginedCountByPageId($item->form_id,$this->page_id);
                                                ?>
                                                <?php echo $form_assigned_count; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="yndform_option_btn">
                                              <div style="display: flex">
                                                 [
                                                  <p style="margin-right: 7px;margin-left: 4px;"><a href='<?php echo $this->baseUrl();?>/organizations/manageforms/fields?option_id=<?php echo $item->option_id ?>&id=<?php echo $item->form_id ?>&page_id=<?php echo $this->page_id; ?>'><?php echo $this->translate(" Fields") ?></a></p>

                                                  <p style="margin-right: 7px;margin-left: 4px;">  <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'manageforms','action' => 'select-project','tab_link' =>'all_projects','form_id' => $item->form_id,'page_id' => $this->page_id)); ?>"> <?php echo $this->translate("Assign") ?></a></p>


                                                  <p style="margin-right: 7px;"><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms' , 'action' => 'settings', 'form_id' => $item->form_id,'page_id' => $this->page_id), $this->translate("Settings"))?></p>


                                                  <p style="margin-right: 7px;"><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms', 'action' => 'clone', 'form_id' => $item->form_id,'page_id'=>$this->page_id), $this->translate("Clone"), array('class' => 'smoothbox'))?></p>

                                                  <?php if($item->projects_assigned == 0): ?>
                                                     <p style="margin-right: 4px;"><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms', 'action' => 'delete', 'id' => $item->form_id,'page_id'=>$this->page_id), $this->translate("Delete"), array('class' => 'smoothbox'))?></p>
                                                  <?php endif; ?>
                                                  ]

                                              </div>

                                              <!--  <span class="ynicon" style="text-align: center"><i class="fa fa-ellipsis-h" style="color: black" aria-hidden="true"></i></span> -->
                                                <!--  <ul class="yndform_option_items" style="display: none;">

                                                   <li class="yndform_option_item">
                                                       <a href='<?php echo $this->baseUrl();?>/organizations/manageforms/main-info?option_id=<?php echo $item->option_id ?>&form_id=<?php echo $item->form_id ?>&page_id=<?php echo $this->page_id; ?>'><?php echo $this->translate("Edit") ?></a>
                                                   </li>

                                                   <li class="yndform_option_item">
                                                       <a href='<?php echo $this->baseUrl();?>/organizations/manageforms/fields?option_id=<?php echo $item->option_id ?>&id=<?php echo $item->form_id ?>&page_id=<?php echo $this->page_id; ?>'><?php echo $this->translate("Manage Fields") ?></a>
                                                   </li>
                                                   <li class="yndform_option_item">
                                                       <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms' , 'action' => 'settings', 'form_id' => $item->form_id,'page_id' => $this->page_id), $this->translate("Settings"))?>
                                                   </li>
                                                   <li class="yndform_option_item">
                                                       <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms', 'action' => 'clone', 'form_id' => $item->form_id,'page_id'=>$this->page_id), $this->translate("Clone"), array('class' => 'smoothbox'))?>
                                                   </li>
                                                   <li class="yndform_option_item">
                                                       <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'organizations', 'controller' => 'manageforms', 'action' => 'delete', 'id' => $item->form_id,'page_id'=>$this->page_id), $this->translate("Delete"), array('class' => 'smoothbox'))?>
                                                   </li>

                                                </ul>
                                                -->
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="tip">
                             <span>
                                 <?php echo $this->translate("There are no forms available.") ?>
                             </span>
                        </div>
                    <?php endif; ?>
                </div>

             </div>
         </div>
     </div>
 </div>

<div id="hidden_ajax_data" style="display: none;"></div>

 <style>
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
     #custom_toggle:checked + .slider:before {
         -webkit-transform: translateX(26px);
         -ms-transform: translateX(26px);
         transform: translateX(26px);
     }
     #custom_toggle:checked + .slider {
         background-color: #2196F3;
     }

     #custom_toggle:focus + .slider {
         box-shadow: 0 0 1px #2196F3;
     }

     #custom_toggle:checked + .slider:before {
         -webkit-transform: translateX(26px);
         -ms-transform: translateX(26px);
         transform: translateX(26px);
     }
     .custom_toggle:checked + .slider {
         background-color: #2196F3;
     }

     .custom_toggle:focus + .slider {
         box-shadow: 0 0 1px #2196F3;
     }
     div#category_id-wrapper {
         display: none;
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
     .yndform_option_item a{
         margin: 0;
         padding: 0 7px;
         text-align: left;
         height: 32px;
         display: block;
         line-height: 32px;
         font-size: 12px;
         color: #555;
         border-top: 1px solid #eaeaea;
     }
     .fa-ellipsis-h:before {
         cursor: pointer;
     }
     .yndform_option_items{
         position: absolute !important;
         background: #fff !important;
         min-width: 90px;
         right: unset !important;
         border-radius: 3px;
         -webkit-border-radius: 3px;
         -moz-border-radius: 3px;
         z-index: 999 !important;
         top: unset !important;
         border: 1px solid #ddd;
         border-top: 0;
         height: auto !important;
         background: white !important;
     }
     #search {
         float: right;
     }
     th.admin_table_short {
     }
     span.ynicon {

         align-items: center;
         text-align: center;
         display: flex;
     }
     #filter_form{
         display: flex;
         padding: 20px;
         border: 1px solid #eee;
         margin-bottom: 20px;
         align-items: center;
     }
     #search{
         border-radius: 25px;
         margin-left: 10px;
     }
     div#to_date-wrapper {
       width: 18%;
     }
     div.form-label {
         display: none;
     }
     .yndform_manage_form_admin_search .search {
         padding: 0;
         width: 100%;
         box-sizing: border-box;
         -moz-box-sizing: border-box;
         -webkit-box-sizing: border-box;
     }
     .yndform_manage_form_admin_search .search #filter_form .form-wrapper {
         width: 20%;
         padding: 0 5px;
         box-sizing: border-box;
         -moz-box-sizing: border-box;
         -webkit-box-sizing: border-box;
         margin: 0;
     }
     .yndform_manage_fields_fields div.yndform_manage_fields_fields_bg {
         background-image: url(/application/modules/Yndynamicform/externals/images/manga_field_bg.png?c=21);
         background-position: center 100px;
         background-repeat: no-repeat;
         height: 100%;
         padding: 15px;
         width: 100%;
         min-height: 1000px;
     }
     .yndform_manage_fields_fields.yndform_manage_fields_bg div.yndform_manage_fields_fields_bg {
         background-color: #f3faff;
         border: 2px dashed #5f93b4;
         background-image: none;
     }
     .yndform_option_items{
         /* position: absolute; */
         background: rgb(255, 255, 255);
         min-width: 17px;
         right: 0px;
         border-radius: 3px;
         z-index: 999;
         width: auto;
         top: 28px;
         /* flex-wrap: wrap; */
         height: 10% !important;
         border-width: 0px 1px 1px;
         border-right-style: solid;
         height: auto !important;
         border-bottom-style: solid;
         border-left-style: solid;
         border-right-color: rgb(221, 221, 221);
         border-top-color: rgb(221, 221, 221) !important;
         border-bottom-color: rgb(221, 221, 221);
         border-left-color: rgb(221, 221, 221);
         border-image: initial;
         border-top-style: initial;
         /* border-top-color: initial; */

     }
     .yndform_option_item{
         margin-right: 7px;
         font-size: 13px;
     }
     #select_project{
         color: white !important;
         padding: 7px 16px;
         font-size: 14px;
         background-color: #44AEC1;
         color: #ffffff;
         border: 2px solid #44AEC1;
         cursor: pointer;
         outline: none;
         position: relative;
         overflow: hidden;
         -webkit-transition: all 500ms ease 0s;
         -moz-transition: all 500ms ease 0s;
         -o-transition: all 500ms ease 0s;
         transition: all 500ms ease 0s;
         -moz-border-radius: 3px;
         -webkit-border-radius: 3px;
         border-radius: 3px;
         -webkit-box-sizing: border-box;
         -mox-box-sizing: border-box;
         box-sizing: border-box;
     }
     .buttons {
         margin-top: 5px;
         margin-bottom: 9px;
     }
     table.admin_table {
         width: 100%;
     }
     .global_form div.form-label{
         min-width: 173px !important;
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


     @media(max-width:1250px){
         .yndform_manage_form_admin_search .search #filter_form .form-wrapper{
             width:200px !important;
             margin-top:10px;
             margin-bottom:10px;
         }
         #search{
             margin-top:10px;
             margin-bottom:10px;
         }
         #status-label{
             width:150px !important;
         }
         #filter_form{
             flex-wrap: wrap;
         }
     }

     /* table style */
     .table_heading {
         color: #5ba1cd !important;
     }
     th.header_title {
          width: 10%;
     }
     .forms_table tr th.admin_table_direction_asc > a,
     .forms_table tr th > a.admin_table_direction_asc {
         background-image: url(/application/modules/Core/externals/images/admin/move_up.png?c=350);
     }
     .forms_table tr th.admin_table_direction_desc > a,
     .forms_table tr th > a.admin_table_direction_desc {
         background-image: url(/application/modules/Core/externals/images/admin/move_down.png?c=350);
     }
     .forms_table tr th.admin_table_ordering > a,
     .forms_table tr th > a.admin_table_ordering {
         font-style: italic;
         padding-right: 20px;
         background-position: 100% 50%;
         background-repeat: no-repeat;
     }

 </style>


<script type="text/javascript">
    var ynDynamicFormCalendar= {
        currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
        monthNames: [
            '<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
        ],
        monthNamesShort: [
            '<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
        ],
        dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',
        ],
        dayNamesShort: ['<?php echo $this->translate('Su') ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        firstDay: 0,
        isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
    showMonthAfterYear: false,
        yearSuffix: ''
    };

    jQuery(document).ready(function(){
        jQuery.datepicker.setDefaults(ynDynamicFormCalendar);
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'',
            buttonImageOnly: true,
            buttonText: '',
        });
        jQuery('#to_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '<?php echo $this -> translate("Select date")?>'
        });
    });

    var confirm_delete = false;

    function multiDelete() {
        if (!confirm_delete) {
            Smoothbox.open('<?php echo $this->url(array('module' => 'yndynamicform', 'controller' => 'manage', 'action' => 'multi-delete-confirm'), 'admin_default', true); ?>');
            return false;
        } else {
            return true;
        }
    }

    function submitForm() {
        confirm_delete = true;
        $('yndform_manage_form_table').submit();
    }

    function selectAll() {
        var i;
        var multidelete_form = $('yndform_manage_form_table');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                if ($(inputs[i]).hasClass('checkbox')) {
                    inputs[i].checked = inputs[2].checked;
                }
            }
        }
    }

    function changeOrder(sortField, sortDirection){
        var currentOrderField = "<?php echo $this->sort_field;?>";
        var currentOrderDirection = "<?php echo $this->sort_direction;?>";

        if (sortField === currentOrderField) {
            sortDirection = (currentOrderDirection === 'asc' ? 'desc' : 'asc');
        } else {
            sortField = sortField;
            sortDirection = sortDirection
        }

        $('spinner_container').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var params = {
            "fieldOrder" :  sortField,
            "direction" : sortDirection
        };
        ajaxRenderData(params);
    }

    function pageAction(page) {
        $('spinner_container').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var params = {
            "page" :  page
        };
        ajaxRenderData(params);
    }


    function searchBoxShow() {
        document.getElementById('form_search').style.display="block";
        document.getElementById('search_button_show').style.display="none";
        document.getElementById('search_button_hide').style.display="block";
    }
    function searchBoxHide() {
        document.getElementById('form_search').style.display="none";
        document.getElementById('search_button_show').style.display="block";
        document.getElementById('search_button_hide').style.display="none";
    }

    window.addEvent('domready', function() {
        dynamicOptions();
    });

    function updateStatus(form_id,status){
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = null;
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/update-status',
            method: 'POST',
            data: {
                format: 'json',
                form_id:form_id,
                status : status
            },
            onRequest: function () {
                //console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
                location.reload();
            }
        })
        request.send();
    }

    function ajaxRenderData(params){
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/manageforms/manage/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
            data: params,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hidden_ajax_data').innerHTML = responseHTML;
            $('forms_main_container').innerHTML = $('hidden_ajax_data').getElement('#forms_main_container').get('html');
            $('hidden_ajax_data').innerHTML = '';


            if($('spinner_container')){
                $('spinner_container').innerHTML = '';
            }

        }
    }));
    }

</script>

<style>
    .count_div > h3{
        font-weight: bold;
        margin-bottom: 0px !important;
    }
    .count_div{
        background-color: #f0f0f0;
        margin: 10px 0px !important;
        padding: 10px;
    }
</style>