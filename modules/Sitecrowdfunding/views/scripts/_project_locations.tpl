<!-- Used in initiative landing page -->
<?php

$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/core.js');

$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");

$latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.latitude', 0);
$longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.longitude', 0);
$locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);

?>

<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        if(document.getElementById('customLocation') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('customLocation'));
            <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/customLocation.tpl'; ?>
        }
    });
</script>

<div>
    <div class="search_form">
        <?php echo $this->locationForm->render($this); ?>
    </div>
    <div id="location_container">

        <div class="location_count">
            <div class="sitecrowdfunding_location_top_links" style="margin-top: 0;font-size: 17px;display: flex;justify-content: center;font-weight: bold">
                <?php echo $this->translate(array('%s project found', '%s projects found', $this->projectsCount), $this->locale()->toNumber($this->projectsCount)) ?>
                <span id="search_spinner" style="margin-left: 10px"></span>
            </div>
        </div>

        <div class="location_map">
            <div id="project_location_map_none"></div>
            <div class="list_browse_location sitecrowdfunding_list_browse_location" id="list_browse_location" >
                <div class="list_map_container_right sitecrowdfunding_list_map_container_right" id ="list_map_container_right"></div>
                <div id="list_map_container" class="list_map_container sitecrowdfunding_list_map_container absolute">
                    <div class="list_map_container_map_area sitecrowdfunding_list_map_container_map_area fleft seaocore_map" id="listlocation_map">
                        <div class="list_map_content" id="listlocation_browse_map_canvas" ></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="location_project_list">
            <?php if ($this->projectsCount > 0): ?>

                <div class="sitecrowdfunding_browse_list sitecrowdfunding_location_browse_box" id="seaocore_browse_list">
                    <div id="dynamic_app_info_sitecrowdfunding_<?php echo $this->identity; ?>">
                        <div class="sitecrowdfunding_container" id="grid_view_sitecrowdfunding_">
                            <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_grid_view.tpl'; ?>
                        </div>
                    </div>
                </div>

                <?php if ($this->projectsCount > 1): ?>
                    <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>
                <?php endif; ?>

            <?php else: ?>

                <div class="tip">
                    <span>No Projects</span>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    var mapGetDirection;
    var myLatlng;
    var finalMarkers = [];
    var bounds = null;

    // arrays to hold copies of the markers and html used by the side_bar
    // because the function closure trick doesnt work there
    var infoBubbles;
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

    // global "map" variable
    var map = null;
    var infoWindow = null;

    // Page submit
    var pageAction = function (page) {
        $('page').value = page;
        submitLocationForm();        appendCheckbox();
    };

    window.document.onload = function(e){
        console.log("document.onload", e, Date.now() ,window.tdiff,
            (window.tdiff[0] = Date.now()) && window.tdiff.reduce(fred) );
    }
    /***** Map *******/

    function smallLargeMap(option) {
        if (option == '1') {
            $('listlocation_browse_map_canvas').setStyle("height", '400px');
            if (!$('list_map_container').hasClass('list_map_container_exp'))
                $('list_map_container').addClass('list_map_container_exp');
        } else {
            $('listlocation_browse_map_canvas').setStyle("height", offsetWidth);
            if ($('list_map_container').hasClass('list_map_container_exp'))
                $('list_map_container').removeClass('list_map_container_exp');

        }
        // setMapContent();
        google.maps.event.trigger(map, 'resize');
    }

    // A function to create the marker and set up the event window function
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
            _totalItemCount:html.length
        });

        customMarkers.push(marker);

        var projectContent = '';

        if(html.length > 1){
            projectContent = `<h2 class="project_map_header">${html.length} Projects</h2>`;
        }

        for(let j=0; j<html.length ;j++){
            projectContent = projectContent + '<br/>' +html[j].contentString;
        }

        google.maps.event.addListener(marker, 'click', function () {
            infoWindow.setContent(projectContent);
            infoWindow.open(map, marker);
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

    }

    // Map initialize
    function initialize() {
        appendCheckbox();
        var defaultLatlng = new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>);

        // create the map
        var mapOptions = {
            zoom: 16,
            center: defaultLatlng,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("listlocation_browse_map_canvas"),mapOptions);
        bounds = new google.maps.LatLngBounds();
        bounds.extend(defaultLatlng);

        //Create and open InfoWindow.
        infoWindow = new google.maps.InfoWindow({
            maxWidth: 400,
            maxHeight : 400
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

        setMarker();
        smallLargeMap(1);

        // Marker Cluster
        markerCluster = new MarkerClusterer(map,customMarkers,mcOptions);

    };

    // set markers
    function setMarker() {

        <?php if (count($this->locations) > 0) : ?>

            <?php foreach ($this->locations as $location) : ?>

                // obtain the attribues of each marker
                var lat = <?php echo $location->latitude ?>;

                var lng =<?php echo $location->longitude ?>;

                var point = new google.maps.LatLng(lat, lng);

                var project_id = <?php echo $this->list[$location->project_id]->project_id ?>;

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
                    ){
                        finalMarkers[markerIndex].projects.push(markerContent);
                        finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;
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
        markerCluster.clearMarkers();
        finalMarkers = [];
        customMarkers = [];
        markerCluster = null;
    }

    /*** Form ***/
    function submitLocationForm() {

        var initiative_id = <?php echo $this->initiative_id?>;
        var page_id = <?php echo $this->page_id?>;

        $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

        console.log('check 1',document.getElementById("locationsearch").checked );
        en4.core.request.send(new Request.HTML({
            method: 'post',
            url: en4.core.baseUrl + 'sitepage/initiatives/landing-page',
            data: {
                tab_link: "project_locations",
                initiative_id:initiative_id,
                page_id: page_id,
                search_enabled: true,
                page: $('page').value,
                search_str: $('search_str').value,
                customLocation: $('customLocation').value,
                customLocationMiles: $('customLocationMiles').value,
                customLocationParams: $('customLocationParams').value,
                projectlocation: $('projectlocation').value,
                locationsearch: $('locationsearch').value
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').innerHTML;
                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('landing_page_projects'));
                en4.core.runonce.trigger();
                if($('search_spinner')){
                    $('search_spinner').innerHTML = '';
                }
                locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'project_city');
                resetMarkers();
                initialize();
                setButton();
                let checkBoxStatus = window.localStorage.getItem('checkBoxStatus');
                 // console.log('check 2', window.localStorage.getItem('checkBoxStatus'));
                if(  checkBoxStatus == 'true') {
                    document.getElementById("locationsearch").checked = true;
                    document.getElementById('customLocation-wrapper').style.display='block';
                    document.getElementById('customLocationMiles-wrapper').style.display='block';
                }
            }
        }), {
            "force": true
        });
    }

    /*** submit buttons ***/
    function setButton(){

        // search click link
        $('search_btn').addEvent('click', function (e) {
            e.stop();
            submitLocationForm();
        });

        $('clear').addEvent('click', function (e) {
            document.getElementById("filter_form").reset();
            $('search_str').value = null;
            $('customLocation').value = null;
            $('customLocationMiles').value = 0;
            $('customLocationParams').value = null;
            $('projectlocation').value = null;
            submitLocationForm();
        });

    }
    function setSpecificLocationDatas(specificLocation) {

        // if(specificLocation == 'Type search') {
        //     console.log('specificLocation',specificLocation);
        //     document.getElementById('customLocation-wrapper').style.display='block';
        //     document.getElementById('customLocationMiles-wrapper').style.display='block';
        // }
    }
    function locationSearch(value) {

      //  console.log('locationSearch',document.getElementById("locationsearch").checked);
        if( document.getElementById("locationsearch").checked == true) {
            window.localStorage.setItem('checkBoxStatus', document.getElementById("locationsearch").checked);
            document.getElementById('customLocation-wrapper').style.display='block';
            document.getElementById('customLocationMiles-wrapper').style.display='block';
        }else {
            window.localStorage.setItem('checkBoxStatus', document.getElementById("locationsearch").checked);
            document.getElementById('customLocation-wrapper').style.display='none';
            document.getElementById('customLocationMiles-wrapper').style.display='none';
        }
    }
   function appendCheckbox() {
       var checkbox = document.createElement('input');
       checkbox.type = "checkbox";
       checkbox.name = "locationsearch";
       checkbox.value = "value";
       checkbox.id = "locationsearch";
       checkbox.class = "locationsearch";
       checkbox.setAttribute("onclick", 'locationSearch(this.value);');

       var label = document.createElement('label')
       label.htmlFor = "id";
       label.class = "option";
       label.appendChild(document.createTextNode('Type Search Location'));
         if(document.getElementById('projectlocation-element')) {
             document.getElementById('projectlocation-element').appendChild(checkbox);
             document.getElementById('projectlocation-element').appendChild(label);
         }


   }

</script>

<style>
    div#customLocation-wrapper, div#customLocationMiles-wrapper {
     display: none;
    }
    div#customLocation-wrapper, div#customLocationMiles-wrapper,div#projectlocation-wrapper,div#search_str-wrapper{
        width: 19%;
    }
    .form-elements {
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }
    .search_form .form-elements{
        max-width: unset !important;
    }
    .search_form .form-elements > div{
        display: inline-block;
    }
    .search_form .form-elements > div {
       margin-right: 10px;
    }
    .search_form .form-elements > button {
        float: right;
        margin: 30px 30px 0px 0px;
    }

    p.hint {
        opacity: .6;
        margin-top: 0;
    }
    #locationsearch-wrapper{
        max-width: 18%;
        min-width:13%;
        height: 90px;
        align-items: center;
        display: flex;
        justify-content: center;
    }

    div#projectlocation-element {
        font-weight: 500;
    }
    #locationsearch-element{
        display: flex;
        width: 100%;
        height: 100%;
        align-items: center;
        margin-top: 10px;
    }
    #filter_form label{
        margin-bottom:0px !important;
    }
    div#locationsearch-label {
        min-width: unset !important;
    }
    @media (max-width: 767px) {
        .form-elements {
            display: flex !important;
            flex-direction: column;
            flex-wrap: wrap !important;
        }
        div#customLocation-wrapper, div#customLocationMiles-wrapper,div#projectlocation-wrapper,div#search_str-wrapper{
            width: 100% !important;
        }
        .form-elements  button{
          align-self: center;
        }
    }
</style>