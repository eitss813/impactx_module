<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//Get Map icons
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$baseUrl = $view->baseUrl();
$followMapIcon = $baseUrl . "/externals/map/follower.png";
$memberMapIcon = $baseUrl . "/externals/map/member.png";
$adminMapIcon = $baseUrl . "/externals/map/admin.png";
$projectMapIcon = $baseUrl . "/externals/map/project.png";
$defaultMapIcon = $baseUrl . "/externals/map/default.png";

$markerClusterFilePath = $baseUrl . "/externals/map/markerclusterer.js";
$markerclusterer1Icon = $baseUrl . "/externals/map/markerclusterer1.png";

//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");

$this->headScript()->appendFile($markerClusterFilePath);
?>
<script type="text/javascript">
    var myLatlng;
    var map  = null;
    var bounds = null;
    var customMarkers = [];
    var markerCluster = null;
    var i = 0;
    var finalMarkers = [];
    var infoWindow = null;

    var showProjectsYn = true;
    var showFollowersYn = false;
    var showMembersYn = false;
    var showAdminsYn = false;

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

    var followIcon = {
        url: "<?php echo $followMapIcon; ?>", // url
    };

    var adminIcon = {
        url: "<?php echo $adminMapIcon; ?>", // url
    };

    var memberIcon = {
        url: "<?php echo $memberMapIcon; ?>", // url
    };

    var projectIcon = {
        url: "<?php echo $projectMapIcon; ?>", // url
    };

    var defaultIcon = {
        url: "<?php echo $defaultMapIcon; ?>", // url
    };

    var projectlat = 0;
    var projectlng = 0;
    var projectLocZoom = 16;

    function initialize() {

        $('showProjectsYn').checked = showProjectsYn;
        $('showFollowersYn').checked = showFollowersYn;
        $('showMembersYn').checked = showMembersYn;
        $('showAdminsYn').checked = showAdminsYn;

        bounds = new google.maps.LatLngBounds();
        var myOptions = {
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var projectLatlng;

        // Set project location is set
        projectlat = <?php echo (empty($this->location->latitude) ? 0 : $this->location->latitude) ?>;
        projectlng = <?php echo (empty($this->location->longitude) ? 0 : $this->location->longitude) ?>;

        bounds = new google.maps.LatLngBounds();

        if(projectlat!=0 && projectlng!=0 ) {
            projectLatlng = new google.maps.LatLng(projectlat,projectlng);
            bounds.extend(projectLatlng);
            myOptions.zoom = projectLocZoom;
            myOptions.center = projectLatlng;
        }

        map = new google.maps.Map(document.getElementById("sitecrowdfunding_view_map_canvas"), myOptions);

        //Create and open InfoWindow.
        infoWindow = new google.maps.InfoWindow({
            maxWidth: 400,
            maxHeight : 400
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

    }

    // A function to create the marker and set up the event window function
    function createMarker(latlng, title, html, type) {

        bounds.extend(latlng);

        var icon =null;
        if(type=='MEMBER'){
            icon = memberIcon;
        }else if(type=='FOLLOWER'){
            icon = followIcon;
        }else if(type=='PROJECT'){
            icon = projectIcon;
        }else{
            icon = defaultIcon;
        }

        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: title,
            icon: icon,
            label: {
                text: html.length.toString(),
                color: 'white',
                fontSize: '12px',
                fontWeight: "bold"
            },
            _totalItemCount:html.length
        });

        customMarkers.push(marker);

        var contentStr = '';

        for(let j=0; j<html.length ;j++){
            contentStr = `${contentStr} <br/> ${html[j].contentString}`;
        }

        google.maps.event.addListener(marker, 'click', function () {
            infoWindow.setContent(contentStr);
            infoWindow.open(map, marker);
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

    }

    // set project markers
    function setProjectMarkers(){
        if( (projectlat!=null && projectlng!=null) && (projectlat!=0 && projectlng!=0) && (projectlat!='' && projectlng!='') ){

            var lat = projectlat;

            var lng = projectlng;

            var project_id = <?php echo $this->project->project_id ?>;

            var title = "Project: <?php echo str_replace('"', ' ', $this->project->getTitle()) ?>";

            var contentString = "<?php
                echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapProjectInfoWindowContent.tpl', array(
                    'project_id' =>  $this->project->project_id,
                    'location_id' => $this->location->location_id,
                )), false);
            ?>";

            var point = new google.maps.LatLng(lat, lng);

            var markerContent = {
                "id":project_id,
                "type":"PROJECT",
                "contentString":contentString,
                "title":title,
            };

            var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
            if(markerIndex != -1){
                finalMarkers[markerIndex].markersContent.push(markerContent);
                finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;

                if(finalMarkers[markerIndex].type == 'PROJECT'){
                    finalMarkers[markerIndex].type = 'PROJECT';
                }else{
                    finalMarkers[markerIndex].type = 'DIFFERENT';
                }

            } else{
                finalMarkers.push({
                    "title":title,
                    "type": "PROJECT",
                    "point":point,
                    "lat":lat,
                    "lng":lng,
                    "markersContent":[markerContent]
                });
            }
        }
    }

    // set member markers
    function setMembersMarkers(){
        <?php  if (count($this->memberList) > 0) : ?>
            <?php foreach ($this->memberList as $item) : ?>

                var lat = <?php echo $item['latitude'] ?>;

                var lng =<?php echo $item['longitude'] ?>;

                var user_id = <?php echo $item['user_id'] ?>;

                var title =  <?php echo '"'.Engine_Api::_()->getItem('user', $item['user_id'])->getTitle().'"' ?>;
                title = 'Member: ' + title;

                var contentString = "<?php
                    echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapUserInfoWindowContent.tpl', array(
                        'user_id' => $item['user_id'],
                        'user_type' => 'Member'
                    )), false);
                ?>";

                var point = new google.maps.LatLng(lat, lng);

                var markerContent = {
                    "id":user_id,
                    "type":"MEMBER",
                    "contentString":contentString,
                    "title":title,
                };

                var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
                if(markerIndex != -1){
                    finalMarkers[markerIndex].markersContent.push(markerContent);
                    finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +"  "+title;

                    if(finalMarkers[markerIndex].type == 'MEMBER'){
                        finalMarkers[markerIndex].type = 'MEMBER';
                    }else{
                        finalMarkers[markerIndex].type = 'DIFFERENT';
                    }

                } else{
                    finalMarkers.push({
                        "title":title,
                        "type": "MEMBER",
                        "point":point,
                        "lat":lat,
                        "lng":lng,
                        "markersContent":[markerContent]
                    });
                }

            <?php endforeach; ?>
        <?php endif; ?>
    }

    // set follower markers
    function setFollowerMarkers(){
        <?php  if (count($this->followerList) > 0) : ?>
            <?php foreach ($this->followerList as $item) : ?>

                var lat = <?php echo $item['latitude'] ?>;

                var lng =<?php echo $item['longitude'] ?>;

                var user_id = <?php echo $item['user_id'] ?>;

                var title =  <?php echo '"'.Engine_Api::_()->getItem('user', $item['user_id'])->getTitle().'"' ?>;
                title = 'Follower: ' + title;

                var contentString = "<?php
                    echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapUserInfoWindowContent.tpl', array(
                        'user_id' => $item['user_id'],
                        'user_type' => 'Follower'
                    )), false);
                ?>";

                var point = new google.maps.LatLng(lat, lng);

                var markerContent = {
                    "id":user_id,
                    "type":"FOLLOWER",
                    "contentString":contentString,
                    "title":title,
                };

                var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
                if(markerIndex != -1){
                    finalMarkers[markerIndex].markersContent.push(markerContent);
                    finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;

                    if(finalMarkers[markerIndex].type == 'FOLLOWER'){
                        finalMarkers[markerIndex].type = 'FOLLOWER';
                    }else{
                        finalMarkers[markerIndex].type = 'DIFFERENT';
                    }

                } else{
                    finalMarkers.push({
                        "title":title,
                        "type": "FOLLOWER",
                        "point":point,
                        "lat":lat,
                        "lng":lng,
                        "markersContent":[markerContent]
                    });
                }

            <?php endforeach; ?>
        <?php endif; ?>
    }

    // set admin markers
    function setAdminMarkers(){
        <?php  if (count($this->adminList) > 0) : ?>
            <?php foreach ($this->adminList as $item) : ?>

                var lat = <?php echo $item['latitude'] ?>;

                var lng =<?php echo $item['longitude'] ?>;

                var user_id = <?php echo $item['user_id'] ?>;

                var title =  <?php echo '"'.Engine_Api::_()->getItem('user', $item['user_id'])->getTitle().'"' ?>;
                title = 'Admin: ' + title;

                var contentString = "<?php
                    echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapUserInfoWindowContent.tpl', array(
                        'user_id' => $item['user_id'],
                        'user_type' => 'Admin'
                    )), false);
                ?>";

                var point = new google.maps.LatLng(lat, lng);

                var markerContent = {
                    "id":user_id,
                    "type":"ADMIN",
                    "contentString":contentString,
                    "title":title,
                };

                var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
                if(markerIndex != -1){
                    finalMarkers[markerIndex].markersContent.push(markerContent);
                    finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;

                    if(finalMarkers[markerIndex].type == 'ADMIN'){
                        finalMarkers[markerIndex].type = 'ADMIN';
                    }else{
                        finalMarkers[markerIndex].type = 'DIFFERENT';
                    }

                } else{
                    finalMarkers.push({
                        "title":title,
                        "type": "ADMIN",
                        "point":point,
                        "lat":lat,
                        "lng":lng,
                        "markersContent":[markerContent]
                    });
                }

            <?php endforeach; ?>
        <?php endif; ?>
    }

</script>
<h3>Map</h3>
<div class="sitecrowdfunding_profile_map b_dark clr">
    <ul class="sitepage_profile_location">
        <li class="seaocore_map">
            <div class="checkbox_options">
                <form class="global_form checkbox_options_form">
                    <div>
                        <div>
                            <div class="form-elements">
                                <div class="form-wrapper">
                                    <div class="form-element">
                                        <ul class="form-options-wrapper checkbox_options">
                                            <li>
                                                <input type="checkbox" id="showProjectsYn" name="showProjectsYn" onclick="toggleCheckbox(this)"><label id="showProjectsLabel" for="Project">Project</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="showFollowersYn" name="showFollowersYn" onclick="toggleCheckbox(this)"><label id="showFollowersLabel" for="Followers">Followers</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="showMembersYn" name="showMembersYn" onclick="toggleCheckbox(this)"><label id="showMembersLabel" for="Members">Members</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" id="showAdminsYn" name="showAdminsYn" onclick="toggleCheckbox(this)"><label id="showAdminsLabel" for="Admins">Admins</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="sitecrowdfunding_view_map_canvas"></div>
            <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
            <?php if (!empty($siteTitle)) : ?>
                <div class="seaocore_map_info">
                    <?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?>
                </div>
            <?php endif; ?>
        </li>
    </ul>
</div>	
<?php if($this->showAddress === true): ?>
<div class='profile_fields clr'>
    <h4><?php echo $this->translate('Location Information') ?></h4>
    <ul>
        <li>
            <span><strong><?php echo $this->translate('Location :'); ?></strong> </span>
            <span><b><?php echo $this->location->location; ?></b></span>
        </li>
        <?php if (!empty($this->location->formatted_address)): ?>
            <li>
                <span><strong><?php echo $this->translate('Formatted Address :'); ?></strong> </span>
                <span><?php echo $this->location->formatted_address; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->address)): ?>
            <li>
                <span><strong><?php echo $this->translate('Street Address :'); ?></strong> </span>
                <span><?php echo $this->location->address; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->city)): ?>
            <li>
                <span><strong><?php echo $this->translate('City :'); ?></strong></span>
                <span><?php echo $this->location->city; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->zipcode)): ?>
            <li>
                <span><strong><?php echo $this->translate('Zipcode :'); ?></strong></span>
                <span><?php echo $this->location->zipcode; ?> </span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->state)): ?>
            <li>
                <span><strong><?php echo $this->translate('State :'); ?></strong></span>
                <span><?php $this->location->state; ?></span>
            </li>
        <?php endif; ?>
        <?php if (!empty($this->location->country)): ?>
            <li>
                <span><strong><?php echo $this->translate('Country :'); ?></strong></span>
                <span><?php echo $this->location->country; ?></span>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
<script type="text/javascript">

    function toggleCheckbox(element){
        showProjectsYn = $('showProjectsYn').checked;
        showFollowersYn = $('showFollowersYn').checked;
        showMembersYn = $('showMembersYn').checked;
        showAdminsYn = $('showAdminsYn').checked;
        resetMarkers();
        refreshMarker();
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

    function refreshMarker(){
        if(showProjectsYn === true){
            setProjectMarkers();
        }
        if(showFollowersYn === true){
            setFollowerMarkers();
        }
        if(showMembersYn === true){
            setMembersMarkers();
        }
        if(showAdminsYn === true){
            setAdminMarkers();
        }

        // set markers
        // Once all the final markers is got
        for (let i = 0; i < finalMarkers.length; i++) {
            var marker = createMarker(finalMarkers[i].point, finalMarkers[i].title ,finalMarkers[i].markersContent, finalMarkers[i].type);
        }

        // Marker Cluster
        markerCluster = new MarkerClusterer(map,customMarkers,mcOptions);
    }

    window.addEvent('domready', function () {
        initialize();
        refreshMarker();
    });

</script>
<style type="text/css">
    .sitecrowdfunding_profile_map > #sitecrowdfunding_view_map_canvas{
        /* border-radius: 5px; */
        width: 100%;
        height: 450px;
    }
    @media only screen and (max-width: 920px){
        form.global_form .form-elements .form-element {
            width: 100% !important;
        }
    }
    .checkbox_options li{
        float: left;
        margin-bottom: 5px;
    }
    .checkbox_options{
        margin: 20px 0px;
        font-size: 14px;
    }
    .checkbox_options label {
        margin-right: 40px;
        padding: 5px;
    }
    .checkbox_options input{
        width: 20px;
        height: 20px;
        margin: 2px 15px 2px 2px;
    }
    #showProjectsLabel{
        background-color: #ec4908;
        color: white;
    }
    #showFollowersLabel{
        background-color: #ec5656;
        color: white;
    }
    #showMembersLabel{
        background-color: #0077b5;
        color: white;
    }
    #showAdminsLabel{
        background-color: #039c2e;
        color: white;
    }
</style>