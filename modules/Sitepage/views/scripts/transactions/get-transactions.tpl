<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: contact.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/assets/css/simplemodal.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/simple-modal/simple-modal.js' ?>"></script>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$baseUrl = $view->baseUrl();
$markerClusterFilePath = $baseUrl . "/externals/map/markerclusterer.js";
$this->headScript()->appendFile($markerClusterFilePath);
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>

<?php $tab_link= $this->params['tab_link']; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Transactions', 'sectionDescription' => 'List all transactions')); ?>
            <div class="sitepage_edit_content">

                <!-- Show menu only if projectIds is present -->
                <?php if(count($this->projectsIds) > 0): ?>

                    <!--Menus-->
                    <div class="transaction_menu headline sitecrowdfunding_inner_menu">
                        <div class='tabs sitecrowdfunding_nav'>
                            <ul class='transaction_menu_nav navigation'>
                                <li>
                                    <a id="all_transactions" href="javascript:void(0);" onclick="selected_ui('all_transactions')" >
                                        <?php echo $this->translate('All Transactions'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a id="transaction_by_projects" href="javascript:void(0);" onclick="selected_ui('transaction_by_projects')" >
                                        <?php echo $this->translate('Transaction By Projects'); ?>
                                    </a>
                                </li>
                               <!-- <li>
                                    <a id="transaction_by_location" href="javascript:void(0);" onclick="selected_ui('transaction_by_location')" >
                                        <?php echo $this->translate('Transactions By Location'); ?>
                                    </a>
                                </li>-->
                            </ul>
                        </div>
                    </div>

                    <!-- Containers-->
                    <div id="transaction_container">
                        <?php if ( $tab_link == 'all_transactions') : ?>
                            <div id="all_transaction_content" class="transaction_container_all_transactions_container">
                                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_all_transactions.tpl'; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $tab_link == 'transaction_by_projects') : ?>
                            <div id="transaction_by_projects_content" class="transaction_container_transaction_by_project_container">
                                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_project_transactions.tpl'; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $tab_link == 'transaction_by_location') : ?>
                            <div id="transaction_by_projects_content" class="transaction_container_transaction_by_location_container">
                                <?php include_once APPLICATION_PATH .'/application/modules/Sitecrowdfunding/views/scripts/_project_transaction_location.tpl' ; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="hidden_ajax_data" style="display: none;"></div>

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
</div>
<style>
    .transaction_menu {
        text-align: center;
    }
    .transaction_menu_nav > li > a{
        font-size: 18px !important;
    }
    .headline .tabs > ul > li > a.active {
        border-color: #44AEC1;
        color: #44AEC1;
    }
    .headline{
        display: block !important;
    }
</style>

<script type="text/javascript">

    function loadAllTransactionsUI(){
        new Calendar({ 'start_cal-date': 'm/d/Y' }, {classes: ['seaocore_event_calendar']});
        new Calendar({ 'end_cal-date': 'm/d/Y' }, {classes: ['seaocore_event_calendar']});
    }

    function  loadTransactionByProjectsUI(){

    }

    function  loadTransactionByLocationUI(){
        initialize();
    }

    function unsetParams(tabLink,params){
        let newParams  = Object.assign({}, params);
        if(tabLink === 'all_transactions'){
            // reset transaction_by_projects params
            delete newParams.project_name;
            delete newParams.project_id;
            delete newParams.state;
            delete newParams.funding_state;
            delete newParams.goal_amount_min;
            delete newParams.goal_amount_max;
            delete newParams.funding_amount_min;
            delete newParams.funding_amount_max;
            delete newParams.sort_field;
            delete newParams.sort_direction;
            delete newParams.user_id;
            delete newParams.user_name;
            delete newParams.total_funders_min;
            delete newParams.total_funders_max;
        }
        if(tabLink === 'transaction_by_projects'){
            // reset all_transactions params
            delete newParams.user_name;
            delete newParams.user_id;
            delete newParams.project_id;
            delete newParams.project_name;
            delete newParams.transaction_min_amount;
            delete newParams.transaction_max_amount;
            delete newParams.commission_min_amount;
            delete newParams.commission_max_amount;
            delete newParams.payment_status;
            delete newParams.sort_field;
            delete newParams.sort_direction;
            delete newParams.start_cal;
            delete newParams.end_cal;
        }
        newParams.page = 1;
        return newParams;
    }

    // menu select function
    function selected_ui(tabLink){

        // remove active for other classes
        $('all_transactions').removeClass('active');
        $('transaction_by_projects').removeClass('active');
       <!-- $('transaction_by_location').removeClass('active'); -->
        // Add class
        $(tabLink).addClass('active');

        $('transaction_container').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
        };

        var paramsData = unsetParams(tabLink,params.requestParams);

        var request = new Request.HTML({
            url: en4.core.baseUrl + "organizations/transactions/get-transactions/page_id/" + <?php echo sprintf('%d', $this->page_id) ?>,
            data: $merge(paramsData, {
                format: 'html',
                subject: en4.core.subject.guid,
                tab_link: tabLink
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('transaction_container').innerHTML = $('hidden_ajax_data').getElement('#transaction_container').innerHTML;
                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('transaction_container'));
                en4.core.runonce.trigger();
                if(tabLink === 'all_transactions'){
                    loadAllTransactionsUI();
                }
                if(tabLink ==='transaction_by_projects'){
                    loadTransactionByProjectsUI();
                }
                if(tabLink ==='transaction_by_location'){
                    loadTransactionByLocationUI();
                }
            }
        });
        request.send();
    }

    // called when page is loaded
    window.addEvent('domready', function () {

        // set menu highlight
        tab_link = "<?php echo $tab_link; ?>";
        $(tab_link).addClass('active');

    });
</script>
