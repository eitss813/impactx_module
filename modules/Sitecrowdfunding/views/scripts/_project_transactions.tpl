<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/assets/css/simplemodal.css' ?>" rel="stylesheet">

<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/simple-modal.js' ?>"></script>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css');?>

<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<div class="clr" id="project_transaction_main_container">
    <div class="seaocore_settings_form">
        <div class="settings">
            <div  id="error_msg_outer_container">
                <div id="error_msg_container" style="display: none"></div>
            </div>
            <?php echo $this->searchForm->render($this) ?>
            <div id="search_spinner"></div>
        </div>
    </div>
    <div class="global_form">

        <?php $totalItems = $this->paginator->getTotalItemCount(); ?>

        <?php if ($totalItems > 0): ?>
            <div class="count_div">
                <h3><?php echo $this->translate('%s project(s) found.', $totalItems) ?> <span id="sort_spinner"></span> </h3>
            </div>
            <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>
        <?php endif; ?>

        <div id="payment_request_table">
            <?php if ($totalItems > 0): ?>
                <div class="sitecrowdfunding_detail_table">
                    <table class="transaction_table">
                        <tr class="sitecrowdfunding_detail_table_head">

                            <!-- project id-->
                            <?php $class = ( $this->sort_field === 'project_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_id', 'asc');">
                                    <?php echo $this->translate("Project Id") ?> 
                                </a>
                            </th>

                            <!-- Project Name -->
                            <?php $class = ( $this->sort_field === 'project_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title_big <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_name', 'asc');">
                                    <?php echo $this->translate("Project Name") ?> 
                                </a>
                            </th>

                            <!-- Owner-->
                            <?php $class = ( $this->sort_field === 'owner' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title_big <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('owner', 'asc');">
                                    <?php echo $this->translate("Owner") ?> 
                                </a>
                            </th>

                            <!-- Project Status -->
                            <?php $class = ( $this->sort_field === 'project_status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_status', 'asc');">
                                    <?php echo $this->translate("Project Status") ?> 
                                </a>
                            </th>

                            <!-- funding_status -->
                            <?php $class = ( $this->sort_field === 'funding_status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('funding_status', 'asc');">
                                    <?php echo $this->translate("Funding Status") ?> 
                                </a>
                            </th>

                            <!-- Goal Amount -->
                            <?php $class = ( $this->sort_field === 'goal_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('goal_amount', 'asc');">
                                    <?php echo $this->translate("Goal Amount") ?> 
                                </a>
                            </th>

                            <!-- Total Funding Amount -->
                            <?php $class = ( $this->sort_field === 'total_funding_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('total_funding_amount', 'asc');">
                                    <?php echo $this->translate("Total Funding Amount") ?>
                                </a>
                            </th>

                            <!-- Total Funders -->
                            <?php $class = ( $this->sort_field === 'total_funders' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                            <th class="header_title <?php echo $class; ?>">
                                <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('total_funders', 'asc');">
                                    <?php echo $this->translate("Total Funders") ?>
                                </a>
                            </th>

                            <th class="header_title"><?php echo $this->translate("Options") ?></th>

                        </tr>
                        <?php foreach ($this->paginator as $item): ?>
                            <tr>

                                <?php
                                    $fundingDatas = Engine_Api::_()->getDbTable('externalfundings','sitecrowdfunding')->getExternalFundingAmount($item->getIdentity());
                                    $totalFundingAmount = $fundingDatas['totalFundingAmount'];
                                    $memberCount = $fundingDatas['memberCount'];
                                    $orgCount = $fundingDatas['orgCount'];
                                    $total_backer_count = $fundingDatas['memberCount'] + $fundingDatas['orgCount'];
                                    $fundedAmount = $item->getFundedAmount();
                                ?>

                                <td class="header_title"><?php echo $item->getIdentity();?></td>

                                <td class="header_title_big" title="<?php echo $item->getTitle(); ?>">
                                    <?php if(!empty($item->getTitle())):?>
                                        <a href="<?php echo $this->url(array('project_id' => $item->project_id, 'slug' => $item->getSlug()), "sitecrowdfunding_entry_view") ?>"  target='_blank'>
                                            <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 25) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo $this->url(array('project_id' => $item->project_id, 'slug' => $item->getSlug()), "sitecrowdfunding_entry_view") ?>"  target='_blank'>
                                            -
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td class="header_title_big" title="<?php echo $item->getOwner()->getTitle(); ?>">
                                    <?php if(!empty($item->getOwner()->getTitle())):?>
                                        <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 25), array('target' => '_blank')) ?>
                                    <?php else: ?>
                                        <?php echo $this->htmlLink($item->getOwner()->getHref(), '-', array('target' => '_blank')) ?>
                                    <?php endif; ?>
                                </td>

                                <td class="header_title"><?php echo $item->state; ?></td>

                                <td class="header_title"><?php echo $item->is_fund_raisable ? $item->funding_state : ' - '; ?></td>

                                <td class="header_title"><?php echo $item->is_fund_raisable ? Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->goal_amount) : ' - '; ?></td>

                                <td class="header_title"><?php echo $item->is_fund_raisable ? Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount) :  '-';?></td>

                                <td class="header_title"><?php echo $item->is_fund_raisable ? $total_backer_count : ' - '; ?></td>

                                <td class="header_title txt_center">
                                    <?php if($item->is_fund_raisable): ?>
                                        <a href="javascript:void(0);" onclick="openDetails('<?php echo $item->getTitle(); ?>','<?php echo $item->project_id ;?>')">
                                            Funder Details
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
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

<div id="hidden_ajax_data" style="display: none;"></div>

<script>

    var changeOrder = function (orderField, orderDirection) {
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

    function openDetails(title,project_id){
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/transactions/project-transactions-details/project_id/'+project_id,
            data: {
                format: 'html'
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                var SM = new SimpleModal({
                    draggable: false,
                    hideFooter: true,
                    width: window.innerWidth - 100,
                });
                SM.show({
                    "title":title + ' - Backers Details',
                    "contents": responseHTML
                });
            }
        }));
    }

    function ajaxRenderData(){
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/transactions/get-transactions/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
            method: 'POST',
            data: {
                subject: en4.core.subject.guid,
                search: 1,
                tab_link:"transaction_by_projects",
                page: $('page').value,
                project_id: $('project_id').value,
                project_name: $('project_name').value,
                user_id: $('user_id').value,
                user_name: $('user_name').value,
                state: $('state').value,
                funding_state: $('funding_state').value,
                goal_amount_min: $('goal_amount_min').value,
                goal_amount_max: $('goal_amount_max').value,
                funding_amount_min: $('funding_amount_min').value,
                funding_amount_max: $('funding_amount_max').value,
                total_funders_min: $('total_funders_min').value,
                total_funders_max: $('total_funders_max').value,
                sort_field: $('sort_field').value,
                sort_direction: $('sort_direction').value
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('project_transaction_main_container').innerHTML = $('hidden_ajax_data').getElement('#project_transaction_main_container').get('html');
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
            }), {
        });
    }

    function pageAction(page) {
        $('page').value = page;
        $('paginate_search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    en4.core.runonce.add(function () {

        // search click link
        $('search').addEvent('click', function (e) {

            var isValidatedYn= true;

            var goal_amount_min = $('goal_amount_min').value;
            var goal_amount_max = $('goal_amount_max').value;

            var funding_amount_min = $('funding_amount_min').value;
            var funding_amount_max = $('funding_amount_max').value;

            var total_funders_min = $('total_funders_min').value;
            var total_funders_max = $('total_funders_max').value;

            goal_amount_min = goal_amount_min.replace(/,/g, '');
            goal_amount_max = goal_amount_max.replace(/,/g, '');

            funding_amount_min = funding_amount_min.replace(/,/g, '');
            funding_amount_max = funding_amount_max.replace(/,/g, '');

            total_funders_min = total_funders_min.replace(/,/g, '');
            total_funders_max = total_funders_max.replace(/,/g, '');

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

            // funding amt validation
            if(funding_amount_min!==null && funding_amount_min!=='' && funding_amount_max!==null && funding_amount_max!=='' ) {
                if(parseFloat(funding_amount_min) > parseFloat(funding_amount_max)){
                    isValidatedYn = false;
                    $('error_msg_container').style.display="block";
                    $('error_msg_container').innerHTML = 'Funding min must be lesser then max amount';
                }
            }

            // funding amt validation
            if(total_funders_min!==null && total_funders_min!=='' && total_funders_max!==null && total_funders_max!=='' ) {
                if(parseFloat(total_funders_min) > parseFloat(total_funders_max)){
                    isValidatedYn = false;
                    $('error_msg_container').style.display="block";
                    $('error_msg_container').innerHTML = 'Total Funders min must be lesser then max amount';
                }
            }


            if(isValidatedYn == true) {
                e.stop();
                $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                $('page').value = 1;
                ajaxRenderData();
            }




        });

        // search click link
        $('clear').addEvent('click', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            $('page').value = 1;
            $('state').value = null;
            $('funding_state').value = null;
            $('project_name').value = null;
            $('project_id').value = null;
            $('user_name').value = null;
            $('user_id').value = null;
            $('goal_amount_min').value = null;
            $('goal_amount_max').value = null;
            $('funding_amount_min').value = null;
            $('funding_amount_max').value = null;
            $('total_funders_min').value = null;
            $('total_funders_max').value = null;
            $('sort_field').value = null;
            $('sort_direction').value = null;
            ajaxRenderData();
        });


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
        var contentAutocomplete = new Autocompleter.Request.JSON('project_name', '<?php echo $this->url(array('action' => 'get-projects' , 'page_id' => $this->page_id ), 'sitepage_transaction', true) ?>', {
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
        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('project_id').value = selected.retrieve('autocompleteChoice').id;
        });

    });
</script>

<style>
    /*for min max css start */
    div#goal_amount_min-wrapper, div#funding_amount_min-wrapper, div#total_funders_min-wrapper{
        display: inline;
        float: left;
        width: 58%;
    }

    div#goal_amount_max-wrapper, div#funding_amount_max-wrapper, div#total_funders_max-wrapper{
        display: inline;
        width: 27%;
    }
    div#goal_amount_max-label, div#funding_amount_max-label, div#total_funders_max-label {
        display:none;
    }
    input#goal_amount_max , input#funding_amount_max,  input#total_funders_max{
        margin-left: 3%;
        width: 97% !important;
    }
    /*for min max css end*/

    th.header_title_big {
        width: 15%;
    }
    th.header_title {
        width: 10%;
    }
    ul.tag-autosuggest {
        margin-top: 22px;
        max-height: 100px;
        overflow-y: auto !important;
    }
    #buttons-wrapper{
        text-align: center;
    }
    #search_form{
        margin-bottom: 20px;
    }
    .count_div > h3{
        font-weight: bold;
        margin-bottom: 0px !important;
    }
    .count_div{
        padding: 5px;
        background-color: #f0f0f0;
        margin: 0 15px !important;
    }
    #error_msg_container {
        display: flex;
        padding: 7px;
        color: #D8000C;
    }
    #error_msg_outer_container {

        margin-top: 11px;
    }
    table.admin_table thead tr th.admin_table_direction_1 > a, table.admin_table thead tr th > a.admin_table_direction_1 {
        background-image: url(/application/modules/Core/externals/images/admin/move_up.png?c=350);
        background-repeat: no-repeat;
        font-style: italic;
        padding-right: 20px;
        background-position: 100% 50%;
    } table.admin_table thead tr th.admin_table_direction_2 > a, table.admin_table thead tr th > a.admin_table_direction_2 {
          background-image: url(/application/modules/Core/externals/images/admin/move_down.png?c=350);
          background-repeat: no-repeat;
          font-style: italic;
          padding-right: 20px;
          background-position: 100% 50%;
      }
    .table_heading{
        color: #5ba1cd !important;
    }
    .table_heading:hover{
        text-decoration: unset !important;
    }
    table th.admin_table_direction_1 > a {
        background-image: url(/application/modules/Core/externals/images/admin/move_up.png?c=350);
        background-repeat: no-repeat;
        font-style: italic;
        padding-right: 20px;
        background-position: 100% 50%;
    }
    table th.admin_table_direction_2 > a {
        background-image: url(/application/modules/Core/externals/images/admin/move_down.png?c=350);
        background-repeat: no-repeat;
        font-style: italic;
        padding-right: 20px;
        background-position: 100% 50%;
    }
    .table_heading{
        color: #5ba1cd !important;
    }
    .table_heading:hover{
        text-decoration: unset !important;
    }

    #sort_field-wrapper,#sort_direction-wrapper{
        display: none;
    }

    .transaction_table tr th.admin_table_direction_asc > a,
    .transaction_table tr th > a.admin_table_direction_asc {
        background-image: url(/application/modules/Core/externals/images/admin/move_up.png?c=350);
    }
    .transaction_table tr th.admin_table_direction_desc > a,
    .transaction_table tr th > a.admin_table_direction_desc {
        background-image: url(/application/modules/Core/externals/images/admin/move_down.png?c=350);
    }
    .transaction_table tr th.admin_table_ordering > a,
    .transaction_table tr th > a.admin_table_ordering {
        font-style: italic;
        padding-right: 20px;
        background-position: 100% 50%;
        background-repeat: no-repeat;
    }

    #search_spinner{
        text-align: center;
        margin-bottom: 20px;
    }
</style>