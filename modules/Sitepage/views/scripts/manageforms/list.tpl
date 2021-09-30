<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/dynamic.js');

?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">

    <div class="generic_layout_container layout_core_content">

        <div class="layout_middle">

            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>

            <?php
               $formId =  $this->form_id;
             $formmappingTable = Engine_Api::_()->getDbtable('formmappings', 'impactx');
               $formmappingTable->isRoleForm($formId);
               
               if($formmappingTable->isRoleForm($formId)) { ?>
               <div style="display: flex;">
                 <a href="javascript:void(0)" onclick="goBack()" class="yndform_backform" ><span style="font-size: 18px;" class="ynicon yn-arr-left">Back &nbsp;</span></a>
               
                     <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
                'sitepage_id'=>$this->sitepage->page_id,
                'sectionTitle'=> 'Form Submissions : '.$this->yndform->getTitle(),
                'sectionDescription' => ''
                )); ?>
               </div>
               <?php } else { ?>
            
            <div style="display: flex;">

                <?php echo $this->htmlLink(array(
                'module' => 'organizations',
                'controller' => 'manageforms',
                'action' => 'manage',
                'page_id'=>$this->page_id
                ), '<span class="ynicon yn-arr-left">'.$this->translate('Back ').'&nbsp;</span>',array(
                'class' => 'yndform_backform'
                ))
                ?>

                <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
                'sitepage_id'=>$this->sitepage->page_id,
                'sectionTitle'=> 'Form Submissions : '.$this->yndform->getTitle(),
                'sectionDescription' => ''
                )); ?>

            </div>
<?php } ?>
            <div class="sitepage_edit_content">


                <!--<button id="form_submitted_btn" class="accordion form_submitted" onclick="openAccordion('form_submitted',1)">Forms Submitted -  (<?php echo $this->totalSubmission ; ?>) <span id="form_submitted_spinner"></span></button>
-->
                <div id="form_submitted_panel" class="panel form_submitted_panel">
                    <?php if ($this->form_submitted_paginator): ?>

                    <div style="float: right">
                        <?php echo $this->htmlLink(array(
                        'route' => 'default',
                        'module' => 'sitepage',
                        'controller' => 'manageforms',
                        'action' => 'export-form-submission-as-csv',
                        'form_id' => $this->form_id
                        ), $this->translate('Export To Excel'),
                        array('class' => 'seaocore_icon_exports', 'title' => 'Export')) ?>
                    </div>
                    <br/><br/>

                    <div>
                        <table class="yndform_my_entries_table" style="width: 100%">
                            <thead>
                            <tr>
                                <th field="entry_id">
                                    <a href="javascript:void(0);" onclick="changeOrder('entry_id', 'ASC')">
                                        <?php echo $this->translate("ID") ?>
                                    </a>
                                </th>

                                <th><?php echo $this->translate("Submitted By") ?></th>

<!--                                <th><?php echo $this->translate("Submitted Project") ?></th>-->

                                <th field="creation_date">
                                    <a href="javascript:void(0);" onclick="changeOrder('creation_date', 'ASC')">
                                        <?php echo $this->translate("Submission Time") ?>
                                    </a>
                                </th>

                                <th><?php echo $this->translate("View Submission") ?></th>
                                <th><?php echo $this->translate("Allow Edit") ?></th>
                                <th><?php echo $this->translate("Publish Metric") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->form_submitted_paginator as $entry): ?>
                            <tr>
                                <td>
                                    <?php echo $this->htmlLink(array(
                                    'route' => 'yndynamicform_entry_specific',
                                    'module' => 'yndynamicform',
                                    'controller' => 'entries', 'action' =>'view',
                                    'entry_id' => $entry->getIdentity()),
                                    '#'.$entry->getIdentity());
                                    ?>
                                </td>

                                <td>
                                    <?php
                                            if ($entry && $entry->owner_id) {
                                    echo $entry->getOwner();
                                    } else if ($entry->user_email) {
                                    echo "<a href='mailto:$entry->user_email'>" . $entry->user_email . "</a>";
                                    } else {
                                    echo $this->translate('Anonymous');
                                    }
                                    
                                    if( isset($entry->submission_status) && !empty($entry->submission_status) && ($entry->submission_status == 'preview') ) {
                                        echo ' (test)';
                                    }
                                    ?>
                                </td>

                              <!--  <td>
                                    <?php// if ($entry && $entry->project_id): ?>
                                    <?php// $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id); ?>
                                    <?php// if ($project): ?>
                                    <?php// echo $this->htmlLink($project->getHref(), $this->translate($project->getTitle())) ?>
                                    <?php// endif; ?>
                                    <?php// else: ?>
                                    -
                                    <?php //endif; ?>
                                </td> -->

                                <td>
                                    <?php
                                                $options = array();$options['format'] = 'H:m a, F';
                                                echo $this->locale()->toDateTime($entry->creation_date, $options);
                                    ?>
                                </td>

                                <td>
                                    <ul style="display: flex;border-left: 0;border-right: 0;padding: 12px;">
                                        <li>
                                            <?php
                                            
                                                echo $this->htmlLink(array('route' => 'yndynamicform_entry_specific', 'action' => 'view', 'entry_id' => $entry->getIdentity(), 'is_popup' => 1), 'view', array('class' => 'smoothbox'));
                                            
                                            /*
                                             echo $this->htmlLink(array(
                                            'route' => 'yndynamicform_entry_specific',
                                            'module' => 'yndynamicform',
                                            'controller' => 'entries', 'action' =>'view',
                                            'type'=>'org','page_id'=>$this->page_id,'id'=>$entry->project_id,
                                            'entry_id' => $entry->getIdentity()), $this->translate('View'))
                                            */
                                            ?>
                                        </li>
                                        <!--  <?php if ($entry->isEditable()) {
                                          echo '<li>';
                                              echo $this->htmlLink(array(
                                              'route' => 'yndynamicform_entry_specific',
                                              'module' => 'yndynamicform',
                                              'controller' => 'entries', 'action' => 'edit',
                                              'entry_id' => $entry->getIdentity()), $this->translate('Edit'));
                                              echo '</li>';
                                          }
                                          ?>
                                          <?php if ($entry->isDeletable()) {
                                          echo '<li>';
                                              echo $this->htmlLink(array(
                                              'route' => 'yndynamicform_entry_specific',
                                              'module' => 'yndynamicform',
                                              'controller' => 'entries', 'action' => 'delete',
                                              'entry_id' => $entry->getIdentity()), $this->translate('Delete'),array(
                                              'class' => 'smoothbox'
                                              ));
                                              echo '</li>';
                                          }
                                          ?>
                                          -->
                                    </ul>
                                </td>

                                <td>
                                    <label class="switch">
                                        <?php $assign_status =  $entry->allow_edit;
                                        echo "test".$entry->allow_edit; ?>
                                        <input class="custom_toggle" id="custom_toggle_<?php echo  $entry->getIdentity(); ?>"   onclick="updateStatus(<?php echo  $entry->getIdentity(); ?>)"   type="checkbox"  <?php echo $assign_status ? "checked" : ""; ?> >
                                        <span class="slider round"></span>
                                    </label>
                                </td>

                                <td>
                                    <label class="switch">
                                        <?php $publish = $entry->publish;?>
                                        <input class="custom_toggle" id="publish_toggle_<?php echo  $entry->getIdentity(); ?>"   onclick="updatePublishStatus(<?php echo $entry->getIdentity(); ?>)"   type="checkbox"  <?php echo $publish ? "checked" : ""; ?> >
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br />
                    <div>
                        <?php $this->params['tab'] = 'form_submitted'; ?>
                        <?php
                            echo $this->paginationControl($this->form_submitted_paginator, null, null, array(
                        'query' => array(
                        tab => 'form_submitted'
                        )
                        ));
                        ?>
                    </div>
                    <?php else: ?>
                    <div class="tip">
                            <span>
                                <?php echo $this->translate("No entries found") ?>
                            </span>
                    </div>
                    <?php endif;?>
                </div>

                <br/>

               <!-- <button id="form_assigned_btn" class="accordion form_assigned" onclick="openAccordion('form_assigned',1)">Forms Assigned - (<?php echo $this->totalAssign ; ?>) <span id="form_assigned_spinner"></span></button>
-->
                <div id="form_assigned_panel" class="panel form_assigned_panel">
                    <?php if ($this->form_assigned_paginator): ?>
                    <div>
                        <table class="yndform_my_entries_table" style="width: 89%">
                            <thead>
                            <tr>
                                <th field="entry_id">
                                    <a href="javascript:void(0);" >
                                        <?php echo $this->translate("ID") ?>
                                    </a>
                                </th>
                                <th>
                                    <?php echo $this->translate("Project Assigned") ?>
                                </th>
                                <th>
                                    <?php echo $this->translate("User Assigned") ?>
                                </th>
                                <th>
                                    <?php echo $this->translate("Assign") ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->form_assigned_paginator as $entry): ?>
                            <?php
                                         if($entry->project_id) {
                            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id);
                            $commonn = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id);
                            }else if($entry->user_id) {
                            $user = Engine_Api::_()->user()->getUser($entry->user_id);
                            $commonn = Engine_Api::_()->user()->getUser($entry->user_id);
                            }
                            ?>

                            <tr class="projectid_<?php echo $entry && $commonn ? $commonn->getIdentity() : null; ?>">

                                <td>
                                    <?php if($entry && $entry->project_id): ?>
                                    <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id); ?>
                                    <?php if($project): ?>
                                    <?php echo '#'.$project->getIdentity(); ?>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if($entry && $entry->user_id): ?>
                                    <?php echo '#'.$user->getIdentity(); ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($entry && $entry->project_id): ?>
                                    <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id); ?>
                                    <?php if($project): ?>
                                    <?php echo $this->htmlLink($project->getHref(), $this->translate($project->getTitle())); ?>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($entry && $entry->user_id): ?>
                                    <?php echo $this->htmlLink($user->getHref(), $this->translate($user->getTitle())); ?>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <label class="switch" style="margin-top: 3px;">

                                        <?php if($entry && $entry->project_id): ?>
                                        <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id); ?>
                                        <?php if($project): ?>
                                        <?php $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
                                        $assign_status =  $tablePage->getProjectAssiginedCountByFormIds($this->form_id,$project->getIdentity(),$this->page_id);
                                        ?>
                                        <input class="custom_toggle" id="custom_toggle_<?php echo $entry->project_id; ?>" type="checkbox" onclick="assignFormToAllProjects(<?php echo $this->form_id;?>,<?php echo $project->getIdentity(); ?>)"  <?php echo $assign_status ? 'checked':'';?> >
                                        <?php else: ?>
                                        <?php
                                                             $assign_status =  0;
                                                        ?>
                                        <?php endif; ?>

                                        <span class="slider round"></span>
                                        <?php endif; ?>


                                        <?php if($entry && $entry->user_id): ?>
                                        <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->user_id); ?>
                                        <?php $tablePage = Engine_Api::_()->getDbtable('projectforms', 'sitepage');
                                        $assign_status =  $tablePage->getUserAssiginedCountByFormIds($this->form_id,$entry->user_id,$this->page_id);
                                        ?>
                                        <input class="custom_toggle" id="custom_toggle_<?php echo $entry->user_id; ?>" type="checkbox" onclick="assignFormToAllUser(<?php echo $this->form_id;?>,<?php echo $entry->user_id; ?>)"  <?php echo $assign_status ? 'checked':'';?> >
                                        <span class="slider round"></span>
                                        <?php endif; ?>
                                    </label>
                                </td>

                            </tr>

                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br />
                    <div>
                        <?php $this->params; ?>
                        <?php
                            echo $this->paginationControl($this->form_assigned_paginator, null, null, array(
                        'query' => array(
                        tab => 'form_assigned'
                        )
                        ));
                        ?>
                    </div>
                    <?php else: ?>
                    <div class="tip">
                            <span>
                                <?php echo $this->translate("No entries found") ?>
                            </span>
                    </div>
                    <?php endif;?>
                </div>

            </div>

        </div>
    </div>

</div>

<div id="hidden_ajax_data" style="display: none;"></div>

<script>
      function goBack() {
  window.history.back();
}
    window.onload = function () {
        let tab = '<?php echo $this->tab; ?>';
        if(tab == 'form_submitted') {
            document.getElementsByClassName('form_submitted_panel')[0].style.display = 'block';
            document.getElementsByClassName('form_assigned_panel')[0].style.display = 'none';
        } else if(tab == 'form_assigned') {
            document.getElementsByClassName('form_submitted_panel')[0].style.display = 'none';
            document.getElementsByClassName('form_assigned_panel')[0].style.display = 'block';
        }
    }
    //updateEntryEditStatusAction
    function updateStatus(entry_id){

        var assign_status = document.getElementById("custom_toggle_"+entry_id).checked;
        console.log('assign_status',assign_status);
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/update-entry-edit-status',
            method: 'POST',
            data: {
                format: 'json',
                entry_id: entry_id,
                status : assign_status
            },
            onRequest: function () {
                //console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
           //    location.reload();
            }
        })
        request.send();
    }
    
    // Update publish status in the table
    function updatePublishStatus(entry_id){
        var publish_status = document.getElementById("publish_toggle_"+entry_id).checked;
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/update-entry-publish-status',
            method: 'POST',
            data: {
                format: 'json',
                entry_id: entry_id,
                status : publish_status
            },
            onRequest: function () {
                //console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
           //    location.reload();
            }
        })
        request.send();
    }

    // assign form to all projects
    function  assignFormToAllProjects(form_id,project_id) {
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = document.getElementById('custom_toggle_'+project_id).checked ? 1 : 0;

        if(assign_status == 0) {
            document.getElementsByClassName('projectid_'+project_id)[0].style.display= "none";
        }

        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/assign-forms',
            method: 'POST',
            data: {
                format: 'json',
                form_id:form_id,
                page_id:page_id,
                status:assign_status,
                project_id:project_id
            },
            onRequest: function () {
            },
            onSuccess: function (responseJSON) {
            }
        });
        request.send();
    }

    // assign form to all projects
    function  assignFormToAllUser(form_id,user_id) {
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = document.getElementById('custom_toggle_'+user_id).checked ? 1 : 0;

        if(assign_status == 0) {
            document.getElementsByClassName('projectid_'+user_id)[0].style.display= "none";
        }

        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/assign-forms-user',
            method: 'POST',
            data: {
                format: 'json',
                form_id:form_id,
                page_id:page_id,
                status:assign_status,
                user_id:user_id
            },
            onRequest: function () {
            },
            onSuccess: function (responseJSON) {
            }
        });
        request.send();
    }

    function openAccordion(tab,page_no){

        $(tab+'_spinner').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var request = new Request.HTML({
            url: en4.core.baseUrl + "organizations/manageforms/list/form_id/"+<?php echo sprintf('%d', $this->form_id) ?> +"/page_id/" + <?php echo sprintf('%d', $this->page_id) ?>,
        data: {
            format: 'html',
                subject: en4.core.subject.guid,
                tab:tab,
                page_no:page_no
        },
        evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hidden_ajax_data').innerHTML = responseHTML;
            var data = $('hidden_ajax_data').getElement('#'+tab+'_panel').innerHTML;
            $('hidden_ajax_data').innerHTML = '';

            $(tab+'_spinner').innerHTML = '';

            Smoothbox.bind($(tab+'_panel'));
            en4.core.runonce.trigger();

            if(tab == 'form_submitted') {
                document.getElementsByClassName('form_submitted_panel')[0].style.display = 'block';
                document.getElementsByClassName('form_assigned_panel')[0].style.display = 'none';
            } else if(tab == 'form_assigned') {
                document.getElementsByClassName('form_submitted_panel')[0].style.display = 'none';
                document.getElementsByClassName('form_assigned_panel')[0].style.display = 'block';
            }
        }
    });
        request.send();

    }

    function pageAction(page_no) {
        var tab = '<?php echo $this->tab; ?>';
        openAccordion(tab,page_no);
    }

</script>

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


    button#search_button_show {
        display: none !important;
    }
    .ynicon {
        color: #0087c3 !important;
        font-size: 21px !important;
    }
    .section_header_details {
        margin-top: 2px;
    }
    td {
        /*   padding: 10px; */
    }
    div, td{
        text-align: center !important;
    }
    table.admin_table {
        width: 100%;
    }
    th {
        background-color: #f5f5f5;
        padding: 10px;
        font-weight: bold;
        white-space: nowrap;
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
    th.admin_table_short {
        width: 1%;
    }
    #global_page_yndynamicform-admin-entries-list table thead tr th:nth-of-type(2) {
        width: 4%;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        font-weight: bold;
        white-space: nowrap;
    }
    th.header_title_big {
        width: 15%;
    }
    th.header_title {
        width: 10%;
    }

    div#advsearch-wrapper {
        display: none;
    }
    #search {
        border-radius: 25px;
    }
    .yn-arr-left:before {
        top: 2px;
        position: relative;
    }
    span.ynicon.yn-arr-left {
        /* font-size: 17px !important; */
    }
    .yndform_title_parent {
        display: flex;
        justify-content: flex-end;
    }

    #elements-wrapper{
        display: flex;
        align-items: center;
        padding: 20px;
        border: 1px solid #eee
    }
    .form-wrapper {
        width: 23%;
        padding: 0 5px;
        margin-top: 10px;
    }
    .form-label{
        display:none;
    }
    .span.ynicon.yn-arr-left{
        font-size: 16px;
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .yn-arr-left:before {
        margin-right: 2px;
        margin-top: 3px;
        font-size: 22px;
    }
    a.yndform_backform {
        color: #44AEC1;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        font-weight: bold;
        white-space: nowrap;
    }
    #global_page_yndynamicform-admin-entries-list table thead tr th:nth-of-type(2) {
        width: 4%;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        font-weight: bold;
        white-space: nowrap;
    }
    li {
        margin-left: 7px;
    }
    #global_page_yndynamicform-admin-entries-list .yndform_manage_form_admin_search .search #filter_form #elements-wrapper {
        display: flex;
        display: -webkit-flex;
        display: -moz-flex;
        width: 100%;
        border: 1px solid #eee;
        padding: 15px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
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

    @media(max-width:1100px){
        .form-wrapper{
            width:200px !important;
        }
        #elements-wrapper{
            flex-wrap: wrap;
        }
    }
    @media (max-width: 767px) {
        #search-element #search {
            transform: unset !important;
        }
    }

    .accordion {
        margin-bottom: 4px;
        background-color: #68a9c6;
        color: white;
        cursor: pointer;
        padding: 11px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
    }

    .active, .accordion:hover {

        background-color: #4bacd7;
    }

    .panel {
        margin-top: 6px;
        padding: 0 11px;
        display: none;
        overflow: hidden;
    }
</style>

