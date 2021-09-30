<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');

$latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.latitude', 0);
$longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.longitude', 0);
$locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$baseUrl = $view->baseUrl();
$markerclusterer1Icon = $baseUrl . "/externals/map/markerclusterer1.png";

?>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
        <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
        }
    });
</script>

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


    <div class="global_form">
            <?php $totalItems = $this->paginator->getTotalItemCount(); ?>

            <?php if ($totalItems > 0): ?>
                <div class="count_div">
                    <h3><?php echo $this->translate('%s transaction(s) found. Total amount for %s transaction is %s', $totalItems,$totalItems,Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->totalAmount)) ?> <span id="sort_spinner"></span> </h3>
                </div>

                <!-- Map-->
                <div id="transaction_location_map_container" class="map">
                    <br/>
                    <div class="list_map_content" id="listlocation_browse_map_canvas" style="height: 400px"></div>
                </div>

                <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>

            <?php else: ?>
                    <div class="count_div">
                        <h3><?php echo $this->translate('%s transaction(s) found. Total amount for %s transaction is %s', $totalItems,$totalItems,Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->totalAmount)) ?> <span id="sort_spinner"></span> </h3>
                    </div>

                    <!-- Map-->
                    <div id="transaction_location_map_container" class="map">
                        <br/>
                        <div class="list_map_content" id="listlocation_browse_map_canvas" style="height: 400px"></div>
                    </div>

                    <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>

           <?php endif; ?>

            <div id="payment_request_table"> 
                <?php if ($totalItems > 0): ?>
                    <div class="sitecrowdfunding_detail_table">
                        <table class="transaction_table">
                            <tr class="sitecrowdfunding_detail_table_head">

                                <!-- Transaction Id -->
                                <?php $class = ( $this->sort_field === 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'asc');">
                                        <?php echo $this->translate("Transaction Id") ?>
                                    </a>
                                </th>

                                <!--Project Name -->
                                <?php $class = ( $this->sort_field === 'project_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title_big <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_name', 'asc');">
                                        <?php echo $this->translate("Project Name") ?>
                                    </a>
                                </th>

                                <!--Backer’s Name -->
                                <?php $class = ( $this->sort_field === 'user_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title_big <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('user_name', 'asc');">
                                        <?php echo $this->translate("Backer’s Name") ?>
                                    </a>
                                </th>

                                <!-- Transaction Amount -->
                                <?php $class = ( $this->sort_field === 'transaction_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('transaction_amount', 'asc');">
                                        <?php echo $this->translate("Transaction Amount") ?>
                                    </a>
                                </th>

                                <!-- Commission Amount -->
                              <!--  <?php $class = ( $this->sort_field === 'commission_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('commission_amount', 'asc');">
                                        <?php echo $this->translate("Commission Amount") ?>
                                    </a>
                                </th> -->

                                <!-- Gateway -->
                                <?php $class = ( $this->sort_field === 'gateway' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('gateway', 'asc');">
                                        <?php echo $this->translate("Gateway") ?>
                                    </a>
                                </th>

                                <!-- Payment Status -->
                                <?php $class = ( $this->sort_field === 'payment_status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('payment_status', 'asc');">
                                        <?php echo $this->translate("Payment Status") ?>
                                    </a>
                                </th>

                                <!-- Paid On -->
                                <?php $class = ( $this->sort_field === 'date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title_big <?php echo $class; ?>">
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('date', 'asc');">
                                        <?php echo $this->translate("Date") ?>
                                    </a>
                                </th>

                                <!-- Options -->
                                <th class="header_title"><?php echo $this->translate("Options") ?></th>

                            </tr>
                            <?php foreach ($this->paginator as $payment) : ?>
                                <tr>
                                    <?php $user = Engine_Api::_()->getItem('user', $payment->user_id); ?>
                                    <?php $backer = Engine_Api::_()->getItem('sitecrowdfunding_backer', $payment->source_id); ?>
                                    <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $payment->project_id); ?>
                                    <td class="header_title"><?php echo $payment->transaction_id ?></td>
                                    <td class="header_title_big"  title="<?php echo $project->getTitle(); ?>">
                                        <?php if(!empty($project->getTitle())):?>
                                            <a href="<?php echo $project->getHref(); ?>"  target='_blank'>
                                                <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($project->getTitle(), 25) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo $project->getHref(); ?>"  target='_blank'>
                                                -
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="header_title_big" title="<?php echo $user->getTitle(); ?>">
                                        <?php if(!empty($user->getTitle())):?>
                                            <a href="<?php echo $user->getHref(); ?>"  target='_blank'>
                                                <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($user->getTitle(), 25) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo $user->getHref(); ?>"  target='_blank'>
                                                -
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="header_title"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->amount) ?></td>
                                   <!-- <td class="header_title"><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($payment->commission_value) ?></td> -->
                                    <td class="header_title"><?php echo $payment->gateway_title ?></td>
                                    <td class="header_title"><?php echo $backer->paymentStatus(); ?></td>
                                    <td class="header_title_big"><?php echo date('M d, Y', strtotime($payment->timestamp)); ?></td>
                                    <td class="header_title txt_center">
                                        <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'dashboard', 'action' => 'detail', 'project_id' => $payment->project_id, 'transaction_id' => $payment->transaction_id, 'tab' => 'transaction'), 'default', true) ?>')"><?php echo $this->translate("Details") ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="tip">
                        <span>
                            <?php echo $this->translate('No transactions'); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
    </div>
</div>

<div id="hidden_ajax_data" style="display: none;"></div>

<script type="text/javascript">


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

    // paginate
    function pageAction(page) {
        $('page').value = page;
        $('paginate_search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    // hide time elements
    function hideTimeElements() {
        if ($('start_cal-minute'))
            $('start_cal-minute').style.display = 'none';
        if ($('start_cal-ampm'))
            $('start_cal-ampm').style.display = 'none';
        if ($('start_cal-hour'))
            $('start_cal-hour').style.display = 'none';
        if ($('end_cal-minute'))
            $('end_cal-minute').style.display = 'none';
        if ($('end_cal-ampm'))
            $('end_cal-ampm').style.display = 'none';
        if ($('end_cal-hour'))
            $('end_cal-hour').style.display = 'none';
    }

    // success ajax
    function onSuccessAjax() {
        hideTimeElements();
    }

    function renderCalendarIcon(){
        // show the calendar again
        new Calendar({ 'start_cal-date': 'm/d/Y' }, {classes: ['seaocore_event_calendar']});
        new Calendar({ 'end_cal-date': 'm/d/Y' }, {classes: ['seaocore_event_calendar']});
    }

    //render data
    function ajaxRenderData(){
        // start date
        var date = $('calendar_output_span_start_cal-date').get('text');
        var hour = $('start_cal-hour').value;
        var minute = $('start_cal-minute').value;
        var ampm = $('start_cal-ampm').value;
        if(date === 'Select a date'){
            start_cal = null;
        }else{
            start_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };
        }

        // end date
        var date = $('calendar_output_span_end_cal-date').get('text');
        var hour = $('end_cal-hour').value;
        var minute = $('end_cal-minute').value;
        var ampm = $('end_cal-ampm').value;
        if(date === 'Select a date'){
            end_cal = null;
        }else{
            end_cal = {'date': date, 'hour': hour, 'minute': minute, 'ampm': ampm, };
        }

        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/transactions/get-transactions/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
            data: {
                format: 'html',
                search: 1,
                subject: en4.core.subject.guid,
                tab_link:"all_transactions",
                page: $('page').value,
                user_name: $('user_name').value,
                user_id: $('user_id').value,
                project_id: $('project_id').value,
                project_name: $('project_name').value,
                transaction_min_amount: $('transaction_min_amount').value,
                transaction_max_amount: $('transaction_max_amount').value,
                commission_min_amount: $('commission_min_amount').value,
                commission_max_amount: $('commission_max_amount').value,
                payment_status: $('payment_status').value,
                sort_field: $('sort_field').value,
                sort_direction: $('sort_direction').value,
                start_cal: start_cal,
                end_cal: end_cal,
                location:  $('location').value,
                locationParams:  $('locationParams').value,
                locationmiles:  $('locationmiles').value
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

                onSuccessAjax();
                renderCalendarIcon();
                showOrHideMap();
            }
        }));
    }

    function showOrHideMap(){
        var location = $('location').value;
        var locationmiles =  $('locationmiles').value;
        var totalItem = '<?php echo $this->paginator->getTotalItemCount(); ?>';
        // show and hide the map
        if(
            (location === '' || location === null )
            &&
            (locationmiles == 0)
        ){
            $('transaction_location_map_container').hide();
        }else if(totalItem == 0){
            $('transaction_location_map_container').hide();
        }else {
            $('transaction_location_map_container').show();
            resetMarkers();
            initialize();
        }

    }

    //on ready event
    en4.core.runonce.add(function () {
        showOrHideMap();
        onSuccessAjax();

        // add search click event
        $('search').addEvent('click', function (e) {
            var isValidatedYn= true;

            var transaction_min_amount = $('transaction_min_amount').value;
            var transaction_max_amount = $('transaction_max_amount').value;
            var commission_min_amount = $('commission_min_amount').value;
            var commission_max_amount = $('commission_max_amount').value;
            var sort_field = $('sort_field').value;
            var sort_direction = $('sort_direction').value;
            var location_miles = $('locationmiles').value;
            var location = $('location').value;

            transaction_min_amount = transaction_min_amount.replace(/,/g, '');
            transaction_max_amount = transaction_max_amount.replace(/,/g, '');
            commission_min_amount = commission_min_amount.replace(/,/g, '');
            commission_max_amount = commission_max_amount.replace(/,/g, '');

            var date = $('calendar_output_span_start_cal-date').get('text');
            var hour = $('start_cal-hour').value;
            var minute = $('start_cal-minute').value;
            var ampm = $('start_cal-ampm').value;
            var startDate = null;
            if(date === 'Select a date'){
                startDate = null;
            }else{
                startDate = date;
            }
            var date = $('calendar_output_span_end_cal-date').get('text');
            var hour = $('end_cal-hour').value;
            var minute = $('end_cal-minute').value;
            var ampm = $('end_cal-ampm').value;
            var endDate = null;
            if(date === 'Select a date'){
                endDate = null;
            }else{
                endDate = date;
            }

            // date validation
            if( (startDate === null || startDate === "") &&
                (endDate !== null && endDate !== "" ) ) {
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both Start and End Date need to be filled';
            }else if( (startDate !== null && startDate !== "") &&
                (endDate === null || endDate === "" ) ) {
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both Start and End Date need to be filled';
            } else if(startDate!==null && startDate!=='' && endDate!==null && endDate!='' ) {
                var dateStart = new Date(startDate);
                var dateEnd = new Date(endDate);
                if(dateStart > dateEnd ) {
                   isValidatedYn= false;
                   $('error_msg_container').style.display="block";
                   $('error_msg_container').innerHTML = 'Start Date must be lesser then End date';
                }
            }

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


            if(transaction_min_amount!==null && transaction_min_amount!=='' && transaction_max_amount!==null && transaction_max_amount!='' ) {
                if(parseFloat(transaction_min_amount) > parseFloat(transaction_max_amount)){
                    isValidatedYn = false;
                    $('error_msg_container').style.display="block";
                    $('error_msg_container').innerHTML = 'Transaction min must be lesser then max amount';
                }
            }

            if(commission_min_amount!==null && commission_min_amount!=='' && commission_max_amount!==null && commission_max_amount!='' ) {
                if(parseFloat(commission_min_amount) > parseFloat(commission_max_amount)){
                    isValidatedYn = false;
                    $('error_msg_container').style.display="block";
                    $('error_msg_container').innerHTML = 'Commission min must be lesser then max amount';
                }
            }

            if(location_miles !== "0" && (location === '' || location === null || location === undefined) ){
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both location and miles are required';
            }

          if(isValidatedYn == true) {
              e.stop();
              $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
              ajaxRenderData();
          }

        });

        // add clear click event
        $('clear').addEvent('click', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            $('page').value = 1;
            $('user_name').value = null;
            $('user_id').value = null;
            $('project_name').value = null;
            $('project_id').value = null;
            $('transaction_min_amount').value = null;
            $('transaction_max_amount').value = null;
            $('commission_min_amount').value = null;
            $('commission_max_amount').value = null;
            $('payment_status').value = null;
            $('sort_field').value = null;
            $('sort_direction').value = null;
            $('location').value = null;
            $('locationParams').value = null;
            $('locationmiles').value = 0;
            document.getElementById("calendar_output_span_start_cal-date").textContent="Select a date";
            document.getElementById("calendar_output_span_end_cal-date").textContent="Select a date";

            // start cal
            if($('start_cal')){
                $('start_cal').value = null;
            }
            if($('start_cal-date')){
                $('start_cal-date').value = null;
            }
            if($('start_cal-hour')){
                $('start_cal-hour').value = null;
            }
            if($('start_cal-minute')){
                $('start_cal-minute').value = null;
            }
            if($('start_cal-ampm')){
                $('start_cal-ampm').value = null;
            }

            // end date
            if($('end_cal')){
                $('end_cal').value = null;
            }
            if($('end_cal-date')){
                $('end_cal-date').value = null;
            }
            if($('end_cal-hour')){
                $('end_cal-hour').value = null;
            }
            if($('end_cal-minute')){
                $('end_cal-minute').value = null;
            }
            if($('end_cal-ampm')){
                $('end_cal-ampm').value = null;
            }

            showOrHideMap();
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

        // project autocomeplete
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

    });

    // Map Variables
    var finalMarkers = [];
    var bounds = null;
    var customMarkers = [];
    var markerCluster = null;
    var clusterStyles = [{
        textColor: 'white',
        url: "<?php echo $markerclusterer1Icon; ?>",
        height: 50,
        width: 50
    }];
    var mcOptions = {
        gridSize: 50,
        styles: clusterStyles,
        maxZoom: 15
    };
    var map = null;
    var infoWindow = null;

    // Map functions
    function createMarker(latlng, html, name) {

        bounds.extend(latlng);

        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: name,
            label: {
                text: html.length.toString(),
                color: 'white',
                fontSize: '12px',
                fontWeight: "bold"
            },
            _totalItemCount: html.length
        });

        customMarkers.push(marker);

        var projectContent = '';

        if (html.length > 1) {
            projectContent = `<h2 class="project_map_header">${html.length} Projects</h2>`;
        }

        for (let j = 0; j < html.length; j++) {
            projectContent = projectContent + '<br/>' + html[j].contentString;
        }

        google.maps.event.addListener(marker, 'click', function () {
            infoWindow.setContent(projectContent);
            infoWindow.open(map, marker);
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

    }

    function setMarker() {

        <?php if (count($this->locations) > 0) : ?>

            <?php foreach ($this->locations as $location) : ?>

                // obtain the attribues of each marker
                var lat = <?php echo $location->latitude ?>;

                var lng =<?php echo $location->longitude ?>;

                var point = new google.maps.LatLng(lat, lng);

                <?php if(!empty($location->project_id) && !empty($this->list[$location->project_id]->project_id)):?>

                    var project_id = <?php echo $this->list[$location->project_id]->project_id;?>;
                    var title = "<?php echo addslashes($this->list[$location->project_id]->getTitle()) ?>";

                    // create the marker
                    var contentString = "<?php
                        echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapProjectInfoWindowContent.tpl', array(
                            'project_id' => $this->list[$location->project_id]->project_id,
                            'location_id' => $location->location_id,
                        )), false);
                    ?>";

                    var markerContent = {
                        "project_id":project_id,
                        "contentString":contentString
                    };

                    var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
                    if(markerIndex != -1){
                        if(
                            (finalMarkers[markerIndex].projects.some(function(o){return o["project_id"] === markerContent.project_id})) == false
                        ) {
                            finalMarkers[markerIndex].projects.push(markerContent);
                            finalMarkers[markerIndex].title = finalMarkers[markerIndex].title + " , " + title;
                        }
                    } else{
                        finalMarkers.push({
                            "title":title,
                            "point":point,
                            "lat":lat,
                            "lng":lng,
                            "projects":[markerContent]
                        });
                    }

                <?php endif; ?>

            <?php endforeach; ?>

            // Once all the final markers is got
            for (let i = 0; i < finalMarkers.length; i++) {
                createMarker(finalMarkers[i].point, finalMarkers[i].projects,finalMarkers[i].title);
            }
            google.maps.event.trigger(map, 'resize');

        <?php endif; ?>

    }

    function resetMarkers(){
        for (let i = 0; i < customMarkers.length; i++) {
            customMarkers[i].setMap(null);
        }
        if(markerCluster != null){
            markerCluster.clearMarkers();
        }
        finalMarkers = [];
        customMarkers = [];
        markerCluster = null;
    }

    function initialize() {

        var defaultLatlng = new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>);

        // create the map
        var mapOptions = {
            zoom: 16,
            center: defaultLatlng,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("listlocation_browse_map_canvas"), mapOptions);
        bounds = new google.maps.LatLngBounds();
        bounds.extend(defaultLatlng);

        //Create and open InfoWindow.
        infoWindow = new google.maps.InfoWindow({
            maxWidth: 400,
            maxHeight: 400
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

        setMarker();

        // Marker Cluster
        markerCluster = new MarkerClusterer(map, customMarkers, mcOptions);

    };

</script>  

<style>
    /*for min max css start */
    div#transaction_min_amount-wrapper {
        display: inline;
        float: left;
        width: 58%;
    }

    div#transaction_max_amount-wrapper {
        display: inline;
        width: 27%;
    }
    div#transaction_max_amount-label {
        display:none;
    }
    input#transaction_max_amount {
        margin-left: 3%;
        width: 97%;
    }
    /*for min max css end*/

    div#commission_min_amount-wrapper, div#commission_max_amount-wrapper,
    div#project_name-wrapper{
         display: none;
    }
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

    @media (max-width: 767px){
        div#transaction_min_amount-wrapper{
            width: 100% !important;
        }
        input#transaction_max_amount {
            margin-left: 0% !important;
            width: 100% !important;
        }

    }

</style>