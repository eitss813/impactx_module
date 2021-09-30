<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Projects', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">

                <!-- Show menu only if projectIds is present -->
                <?php if(count($this->projectsIds) > 0): ?>
                <div class="clr" id="transaction_main_container">
                    <div class="seaocore_settings_form">
                        <div class="settings">
                            <div  id="error_msg_outer_container">
                                <div id="error_msg_container" style="display: none"></div>
                            </div>
                            <?php echo $this->searchForm->render($this) ?>
                            <div id="search_spinner"></div>
                        </div>
                    </div>

                    <div>
                        <?php $totalItems = $this->paginator->getTotalItemCount(); ?>
                        <?php if ($totalItems > 0): ?>
                        <div class="count_div">
                            <h3><?php echo $this->translate('%s project(s) found.', $totalItems) ?> <span id="sort_spinner"></span> </h3>
                        </div>
                        <div>
                            <div class="fleft">
                                <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>
                            </div>
                            <div class="fright scramble_order_btn">
                                <a class="button" href="javascript:void(0);" onclick="javascript:openScrambleOrder(<?php echo $this->page_id?>);">
                                    Scramble Order
                                </a>
                            </div>
                        </div>
                        <br/>
                        <?php endif; ?>
                        <br/>

                        <div id="show_tab_content">
                            <?php if ($totalItems > 0): ?>
                            <div class="manageproject_table_scroll">
                                <table class="transaction_table admin_table seaocore_admin_table">
                                    <thead>
                                    <tr class="sitecrowdfunding_detail_table_head">

                                        <!-- Project Name -->
                                        <?php $class = ( $this->sort_field === 'project_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title_big <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_name', 'asc');">
                                                Project<br/>Name
                                            </a>
                                        </th>

                                        <!-- Owner-->
                                        <?php $class = ( $this->sort_field === 'owner' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title_big <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('owner', 'asc');">
                                                <?php echo $this->translate("Owner") ?>
                                            </a>
                                        </th>

                                        <!-- Order-->
                                        <?php $class = ( $this->sort_field === 'project_order' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_order', 'asc');">
                                                <?php echo $this->translate("Order") ?>
                                            </a>
                                        </th>

                                        <!-- Project Status -->
                                        <?php $class = ( $this->sort_field === 'project_status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_status', 'asc');">
                                                Project<br/>Status
                                            </a>
                                        </th>

                                        <!--Published/Unpublished-->
                                        <th>Published/<br/>Unpublished</th>

                                        <!-- funding_status -->
                                        <?php $class = ( $this->sort_field === 'funding_status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('funding_status', 'asc');">
                                                Funding <br/>Status
                                            </a>
                                        </th>

                                        <!-- Goal Amount-->
                                        <?php $class = ( $this->sort_field == 'goal_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('goal_amount', 'asc');">
                                                Goal <br/>Amount
                                            </a>
                                        </th>

                                        <!--Funding Enable/Disable -->
                                        <th class="header_title">Funding<br/>Enable/<br/>Disable</th>

                                        <!--Payment Edit -->
                                        <th class="header_title">Payment<br/>Edit</th>

                                        <!-- Message Project Owner -->
                                        <th class="header_title_big">Message<br/>Project <br/> Owner</th>

                                        <!-- creation date-->
                                        <?php $class = ( $this->sort_field == 'date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                        <th class="header_title_big <?php echo $class; ?>" >
                                            <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('date', 'asc');">Creation <br/> Date</a>
                                        </th>

                                    </tr>
                                    </thead>

                                    <?php foreach ($this->paginator as $item) : ?>
                                    <tr>
                                        <td class="header_title_big" title="<?php echo $item->getTitle() ?>">
                                            <a href="<?php echo $this->url(array('project_id' => $item->project_id, 'slug' => $item->getSlug()), "sitecrowdfunding_entry_view") ?>"  target='_blank'>
                                            <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 25) ?></a>
                                        </td>

                                        <td class="header_title_big" title="<?php echo $item->getOwner()->getTitle() ?>">
                                            <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 25), array('target' => '_blank')) ?>
                                        </td>
                                        <td class="header_title" title="">
                                            <input style="width: 50px;"  type="text" value="<?php echo $item->project_order; ?>"
                                                   id="<?php echo $item->project_id; ?>">
                                            <p class="save" onclick="updateOrder(<?php echo $item->project_id; ?>)">Save</p>
                                        </td>


                                        <td class="header_title_big"><?php echo $item->state; ?></td>

                                        <!-- if project status is these, then make toggle true -->
                                        <?php $liveProjectStatus = array("published");?>

                                        <td class="header_title">
                                            <label class="switch">
                                                <?php /* <input class="custom_toggle" type="checkbox" onclick="approveProject(<?php echo $item->project_id; ?>)" <?php echo $item->approved ? " checked" : ""; ?> > */?>
                                                <input class="custom_toggle" type="checkbox" onclick="approveProject(<?php echo $item->project_id; ?>)" <?php echo in_array($item->state, $liveProjectStatus) ? " checked" : ""; ?> >
                                                <span class="slider round"></span>
                                            </label>
                                        </td>

                                        <td class="header_title">
                                            <?php echo $item->is_fund_raisable ? $item->funding_state : ' - '; ?>
                                        </td>

                                        <td class="header_title"><?php echo $item->is_fund_raisable ? $item->goal_amount : ' - ' ?></td>

                                        <td class="header_title">
                                            <?php if($item->is_fund_raisable): ?>
                                            <label class="switch">
                                                <input class="custom_toggle" type="checkbox" onclick="approveFunding(<?php echo $item->project_id; ?>)" <?php echo $item->funding_approved ? " checked" : "" ; ?> >
                                                <span class="slider round"></span>
                                            </label>
                                            <?php else: ?>
                                            -
                                            <?php endif;?>
                                        </td>

                                        <td class="header_title">
                                            <?php if($item->is_fund_raisable): ?>
                                            <label class="switch">
                                                <input class="custom_toggle" type="checkbox" onclick="approvePayment(<?php echo $item->project_id; ?>)" <?php echo $item->is_payment_details_editable ? " checked" : "" ; ?> >
                                                <span class="slider round"></span>
                                            </label>
                                            <?php else: ?>
                                            -
                                            <?php endif;?>
                                        </td>


                                        <td class="header_title_big">
                                            <a class=" send_msg smoothbox" href="<?php echo $this->url(array('action' => 'message-project-admin','project_id' => $item->project_id), "messages_general") ?>">
                                            Send Message
                                            </a>

                                        </td>

                                        <td class="header_title_big">
                                            <?php echo gmdate('M d,Y', strtotime($item->creation_date)) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>


                                </table>
                            </div>
                            <?php else: ?>
                            <div class="tip">
                                        <span>
                                            <?php echo $this->translate('No projects'); ?>
                                        </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="tip">
                        <span>
                            <?php echo $this->translate('No projects'); ?>
                        </span>
                </div>
                <?php endif; ?>

                <div id="hidden_ajax_data" style="display: none;"></div>

            </div>
        </div>
    </div>
</div>

<style>
    .save{
        color: #2a88c3 !important;
        cursor: pointer;
        margin-left: 11px;
        text-decoration: underline;
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
    .send_msg{
        /* padding: 5px!important; */
        color: #5ba1cd !important;
        font-size: 12px !important;
        text-decoration: underline !important;
    }
    table.admin_table tbody tr:nth-child(even) {
        background-color: #f8f8f8
    }
    .manageproject_table_scroll {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 20px;
    }
    table.admin_table td{
        padding: 10px;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        border-bottom: 1px solid #aaa;
        font-weight: bold;
        padding-top: 7px;
        padding-bottom: 7px;
        white-space: nowrap;
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

    .transaction_table thead tr th.admin_table_direction_asc > a,
    .transaction_table thead tr th > a.admin_table_direction_asc {
        background-image: url(/application/modules/Core/externals/images/admin/move_up.png?c=350);
    }
    .transaction_table thead tr th.admin_table_direction_desc > a,
    .transaction_table thead tr th > a.admin_table_direction_desc {
        background-image: url(/application/modules/Core/externals/images/admin/move_down.png?c=350);
    }
    .transaction_table thead tr th.admin_table_ordering > a,
    .transaction_table thead tr th > a.admin_table_ordering {
        font-style: italic;
        padding-right: 20px;
        background-position: 100% 50%;
        background-repeat: no-repeat;
    }
    #buttons-wrapper{
        text-align: center;
    }

    #sort_field-wrapper, #sort_direction-wrapper {
        display: none;
    }
    .count_div {
        padding: 5px;
        background-color: #f0f0f0;
        margin: 0 15px !important;
    }
    .count_div > h3 {
        font-weight: bold;
        margin-bottom: 0px !important;
    }

    ul.tag-autosuggest {
        margin-top: 22px;
        max-height: 100px;
        overflow-y: auto !important;
    }

    .table_heading{
        color: #5ba1cd !important;
    }
    .table_heading:hover{
        text-decoration: unset !important;
    }

    #error_msg_container {
        display: flex;
        padding: 7px;
        color: #D8000C;
    }
    #error_msg_outer_container {
        margin-top: 11px;
    }

    #search_spinner{
        text-align: center;
        margin-bottom: 20px;
    }

    .global_form div.form-label{
        min-width: 180px !important;
    }

</style>

<script type="text/javascript">

    function changeOrder (orderField, orderDirection) {
        var currentOrderField = $('sort_field').value;
        var currentOrderDirection = $('sort_direction').value;

        if (orderField === currentOrderField) {
            $('sort_direction').value = (currentOrderDirection === 'asc' ? 'desc' : 'asc');
        } else {
            $('sort_field').value = orderField;
            $('sort_direction').value = orderDirection;
        }
        $('sort_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    };
    function pageAction(page) {
        $('page').value = page;
        $('paginate_search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    function approveFunding(id){
        var request1 = new Request.JSON({
            url: en4.core.baseUrl + 'sitecrowdfunding/project-create/approve-funding',
            method: 'POST',
            data: {
                format: 'json',
                project_id: id
            },
            onRequest: function () {
                //console.log('debugging request')
            },
            onSuccess: function (responseJSON) {
             //   ajaxRenderData();
            }
        })
        request1.send();
    }
    function approvePayment(id){
        var request1 = new Request.JSON({
            url: en4.core.baseUrl + 'sitepage/project-payments/approve-payment',
            method: 'POST',
            data: {
                format: 'json',
                project_id: id
            },
            onRequest: function () {
                //console.log('debugging request')
            },
            onSuccess: function (responseJSON) {
              //  location.reload();
              //  ajaxRenderData();
            }
        })
        request1.send();
    }
    function approveProject(id){
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'sitecrowdfunding/project-create/approve-project',
            method: 'POST',
            data: {
                format: 'json',
                project_id: id
            },
            onRequest: function () {
                //console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
             //   ajaxRenderData();
            }
        })
        request.send();
    }
    function updateOrder(id){
        var  value = document.getElementById(id).value;
        var request1 = new Request.JSON({
            url: en4.core.baseUrl + 'sitecrowdfunding/project-create/update-order',
            method: 'POST',
            data: {
                format: 'json',
                project_id: id,
                project_order: value
            },
            onRequest: function () {
                //console.log('debugging request')
            },
            onSuccess: function (responseJSON) {
              // location.reload();
             //   ajaxRenderData();
            }
        })
        request1.send();
    }

    function openScrambleOrder(page_id){
        var htmlstr = `<div id="smoothbox_window" style="background-color: white;">
                <div id="global_content_simple">
                    <div class="global_form_popup">
                        <div>
                            <h3> Scramble Order </h3>
                            <p> Are you sure that you want to scramble the order ? This action cannot be undone.</p>
                            <p>&nbsp;</p>
                            <p>
                                <a style="color: white !important;" class="button" href="javascript:void(0);" onclick="javascript:scrambleOrder(${page_id});">Yes</a>
                                or
                                <a href="javascript:void(0);" onclick="Smoothbox.close();">No</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>`;
        Smoothbox.open(htmlstr, {autoResize : true});
    }

    function scrambleOrder(page_id){
        if(page_id) {
            var request1 = new Request.JSON({
                url: en4.core.baseUrl + 'sitecrowdfunding/project-create/scramble-project-order',
                method: 'POST',
                data: {
                    format: 'json',
                    page_id: page_id
                },
                onRequest: function () {
                    console.log('debugging request')
                },
                onSuccess: function (responseJSON) {
                    location.reload();
                }
            });
            request1.send();
        }
    }

    //render data
    function ajaxRenderData(){
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/dashboard/manage-projects/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
        data: {
            format: 'html',
                search: 1,
                subject: en4.core.subject.guid,
                page:$('page').value,
                project_name : $('project_name').value,
                project_id: $('project_id').value,
                project_order: $('project_order').value,
                user_name: $('user_name').value,
                user_id:$('user_id').value,
                project_status:$('project_status').value,
                funding_status:$('funding_status').value,
                is_published_yn:$('is_published_yn').value,
                is_funding_enabled_yn:$('is_funding_enabled_yn').value,
                is_payment_edit:$('is_payment_edit').value,
                goal_amount_min:$('goal_amount_min').value,
                goal_amount_max:$('goal_amount_max').value,
                sort_field:$('sort_field').value,
                sort_direction:$('sort_direction').value
        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hidden_ajax_data').innerHTML = responseHTML;
            $('transaction_main_container').innerHTML = $('hidden_ajax_data').getElement('#transaction_main_container').get('html');
            $('hidden_ajax_data').innerHTML = '';
            if($('paginate_search_spinner')){
                $('paginate_search_spinner').innerHTML = '';
            }
            if($('search_spinner')){
                $('search_spinner').innerHTML = '';
            }
            if($('sort_spinner')){
                $('sort_spinner').innerHTML = '';
            }
        }
    }));
    }

    //on ready event
    en4.core.runonce.add(function () {

        // users autocomplete
        var userAutoComplete = new Autocompleter.Request.JSON('user_name', '<?php echo $this->url(array('action' => 'get-users'), 'sitepage_transaction', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
        userAutoComplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
        });

        // project autocomplete
        var projectAutoComplete = new Autocompleter.Request.JSON('project_name', '<?php echo $this->url(array('action' => 'get-projects' , 'page_id' => $this->page_id ), 'sitepage_transaction', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
        projectAutoComplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('project_id').value = selected.retrieve('autocompleteChoice').id;
        });

        // clear click
        $('clear').addEvent('click', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            $('page').value = 1;
            $('project_name').value = null;
            $('project_id').value = null;
            $('project_order').value = null;
            $('user_name').value = null;
            $('user_id').value = null;
            $('project_status').value = null;
            $('funding_status').value = null;
            $('is_published_yn').value = null;
            $('is_funding_enabled_yn').value = null;
            $('is_payment_edit').value = null;
            $('goal_amount_min').value = null;
            $('goal_amount_max').value = null;
            $('sort_field').value = null;
            $('sort_direction').value = null;
            ajaxRenderData();
        });

        // search click link
        $('search').addEvent('click', function (e) {

            var isValidatedYn= true;

            var goal_amount_min = $('goal_amount_min').value;
            var goal_amount_max = $('goal_amount_max').value;

            goal_amount_min = goal_amount_min.replace(/,/g, '');
            goal_amount_max = goal_amount_max.replace(/,/g, '');

            var sort_field = $('sort_field').value;
            var sort_direction = $('sort_direction').value;

            // sort field validation
            if( (sort_field === null || sort_field === "") &&
                (sort_direction !== null && sort_direction !== "" ) ) {
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both sort by and direction need to be filled';
            }else if( (sort_field !== null && sort_field !== "") &&
                (sort_direction === null || sort_direction === "" ) ) {
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both sort by and direction need to be filled';
            }

            // goal amt validation
            if(goal_amount_min!==null && goal_amount_min!=='' && goal_amount_max!==null && goal_amount_max!=='' ) {
                if(parseFloat(goal_amount_min) > parseFloat(goal_amount_max)){
                    isValidatedYn = false;
                    $('error_msg_container').style.display="block";
                    $('error_msg_container').innerHTML = 'Goal min must be lesser then max amount';
                }
            }

            if(isValidatedYn == true) {
                e.stop();
                $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                $('page').value = 1;
                ajaxRenderData();
            }

        });
    });
</script>
<style>
    .scramble_order_btn{
        margin-top: 18px;
    }
</style>