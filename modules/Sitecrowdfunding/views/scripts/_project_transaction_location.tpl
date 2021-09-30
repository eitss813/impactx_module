<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/core.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");

$latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.latitude', 0);
$longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.longitude', 0);
$locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$baseUrl = $view->baseUrl();
$markerclusterer1Icon = $baseUrl . "/externals/map/markerclusterer1.png";

?>

<div class="clr" id="transaction_by_location_main_container">

    <div class="seaocore_settings_form">
        <div class="settings">
            <div id="error_msg_outer_container">
                <div id="error_msg_container" style="display: none"></div>
            </div>
            <?php echo $this->locationForm->render($this) ?>
            <div id="search_spinner"></div>
        </div>
    </div>

    <div class="global_form">

        <?php $totalItems = $this->paginator->getTotalItemCount(); ?>

        <?php if ($totalItems > 0): ?>
            <div class="count_div">
                <h3><?php echo $this->translate('%s project(s) found.', $totalItems) ?> <span id="sort_spinner"></span></h3>
            </div>
        <?php endif; ?>

        <div class="list_map_content" id="listlocation_browse_map_canvas" ></div>

        <div id="payment_request_table">
            <?php if ($totalItems > 0): ?>
            <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?><br/>
            <div class="sitecrowdfunding_detail_table">
                <table class="transaction_table">
                    <tr class="sitecrowdfunding_detail_table_head">

                        <!-- project id-->
                        <th class="header_title">
                            <a class="table_heading" href="javascript:void(0);">
                                <?php echo $this->translate("Project Id") ?>
                            </a>
                        </th>

                        <!-- Project Name -->
                        <th class="header_title_big">
                            <a class="table_heading" href="javascript:void(0);">
                                <?php echo $this->translate("Project Name") ?>
                            </a>
                        </th>

                        <!-- Owner-->
                        <th class="header_title_big">
                            <a class="table_heading" href="javascript:void(0);">
                                <?php echo $this->translate("Owner") ?>
                            </a>
                        </th>

                        <!-- Project Status -->
                        <th class="header_title">
                            <a class="table_heading" href="javascript:void(0);">
                                <?php echo $this->translate("Project Status") ?>
                            </a>
                        </th>

                        <!-- funding_status -->
                        <th class="header_title">
                            <a class="table_heading" href="javascript:void(0);">
                                <?php echo $this->translate("Funding Status") ?>
                            </a>
                        </th>

                        <!-- Goal Amount -->
                        <th class="header_title">
                            <a class="table_heading" href="javascript:void(0);">
                                <?php echo $this->translate("Goal Amount") ?>
                            </a>
                        </th>

                    </tr>

                    <?php foreach ($this->paginator as $item): ?>
                    <tr>

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
                                <?php echo $this->htmlLink($item->getOwner()->getHref(),
                                Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 25),
                                array('target' => '_blank')) ?>
                            <?php else: ?>
                                <?php echo $this->htmlLink($item->getOwner()->getHref(), '-', array('target' => '_blank'))
                                ?>
                            <?php endif; ?>
                        </td>

                        <td class="header_title"><?php echo $item->state; ?></td>

                        <td class="header_title"><?php echo $item->is_fund_raisable ? $item->funding_state : ' - '; ?>
                        </td>

                        <td class="header_title"><?php echo $item->is_fund_raisable ?
                            Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->goal_amount) : ' - '; ?>
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


<script type="text/javascript">

    /*** Map ***/
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
                        finalMarkers[markerIndex].projects.push(markerContent);
                        finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;
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

    /*** Page click ***/
    function pageAction(page) {
        $('page').value = page;
        $('paginate_search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    /*** ajax data ***/
    function ajaxRenderData(){
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/transactions/get-transactions/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
            method: 'POST',
            data: {
                subject: en4.core.subject.guid,
                search: 1,
                page: $('page').value,
                tab_link:"transaction_by_location"
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('transaction_by_location_main_container').innerHTML = $('hidden_ajax_data').getElement('#transaction_by_location_main_container').get('html');
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

    /*** submit buttons ***/
    en4.core.runonce.add(function () {

        // search click link
        $('search').addEvent('click', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            ajaxRenderData();
        });

        // search click link
        $('clear').addEvent('click', function (e) {
            e.stop();
            $('page').value = 1;
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            ajaxRenderData();
        });

    });

</script>

<style>
    #listlocation_browse_map_canvas{
        height: 400px;
    }
</style>