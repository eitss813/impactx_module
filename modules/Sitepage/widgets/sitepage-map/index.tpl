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
$projectMapIcon = $baseUrl . "/externals/map/project.png";
$organisationMapIcon = $baseUrl . "/externals/map/organisation.png";
$followersMapIcon = $baseUrl . "/externals/map/follower.png";
$membershipMapIcon = $baseUrl . "/externals/map/member.png";
$partnersMapIcon = $baseUrl . "/externals/map/partner.png";
$adminMapIcon = $baseUrl . "/externals/map/admin.png";
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
        var map  = null;
        var customMarkers = [];
        var markerCluster = null;
        var bounds = null;
        var i = 0;
        var finalMarkers = [];
        var infoWindow = null;

        var showOrganisationYn = true;
        var showProjectsYn = true;
        var showFollowersYn = false;
        var showMembersYn = false;
        var showAdminsYn = false;
        var showPartnersYn = false;

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

        var adminIcon = {
                url: "<?php echo $adminMapIcon; ?>", // url
        };

        var organisationIcon = {
                url: "<?php echo $organisationMapIcon; ?>", // url
        };

        var projectIcon = {
                url: "<?php echo $projectMapIcon; ?>", // url
        };

        var followersIcon = {
                url: "<?php echo $followersMapIcon; ?>", // url
        };

        var membershipIcon = {
                url: "<?php echo $membershipMapIcon; ?>", // url
        };

        var partnersIcon = {
                url: "<?php echo $partnersMapIcon; ?>", // url
        };

        var defaultIcon = {
                url: "<?php echo $defaultMapIcon; ?>", // url
        };

        var sitepagelat = 0;
        var sitepagelng = 0;

        function initialize() {

                $('showOrganisationYn').checked = showOrganisationYn;
                $('showProjectsYn').checked = showProjectsYn;
                $('showFollowersYn').checked = showFollowersYn;
                $('showMembersYn').checked = showMembersYn;
                $('showAdminsYn').checked = showAdminsYn;
                $('showPartnersYn').checked = showPartnersYn;

                var mapOptions = {
                        navigationControl: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var sitepageLatlng = null;

                // Set sitepage location is set
                sitepagelat = <?php echo (empty($this->location->latitude) ? 0 : $this->location->latitude) ?>;
                sitepagelng = <?php echo (empty($this->location->longitude) ? 0 : $this->location->longitude) ?>;

                bounds = new google.maps.LatLngBounds();

                if(sitepagelat !=0 && sitepagelng !=0 ) {
                        sitepageLatlng = new google.maps.LatLng(sitepagelat,sitepagelng);
                        mapOptions.zoom = 16;
                        mapOptions.center = sitepageLatlng;

                        bounds = new google.maps.LatLngBounds();
                        bounds.extend(sitepageLatlng);
                }

                map = new google.maps.Map(document.getElementById("sitepage_map_view_map_canvas"), mapOptions);

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
                        icon = membershipIcon;
                }else if(type=='FOLLOWER'){
                        icon = followersIcon;
                }else if(type=='PROJECT'){
                        icon = projectIcon;
                }else if(type=='PARTNER'){
                        icon = partnersIcon;
                }else if(type=='ADMIN'){
                        icon = adminIcon;
                }else if(type=='ORGANISATION'){
                        icon = organisationIcon;
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

                $$('.tab_layout_sitepage_sitepage_map').addEvent('click', function () {
                        map.fitBounds(bounds);
                });

        }

        // set organisation markers
        function setOrganisationMarkers(){

                if( (sitepagelat!=null && sitepagelng!=null) && (sitepagelat!=0 && sitepagelng!=0) && (sitepagelat!='' && sitepagelng!='') ){

                        var lat = sitepagelat;

                        var lng = sitepagelng;

                        var page_id = <?php echo $this->sitepage->page_id ?>;

                        var title = "Organisation: <?php echo str_replace('"', ' ', $this->sitepage->getTitle()) ?>";

                        var contentString = "<?php
                                echo $this->string()->escapeJavascript($this->partial('application/modules/Sitepage/views/scripts/_mapOrganisationInfoWindowContent.tpl', array(
                                        'sitepage' => $this->sitepage,
                                        'location' => $this->location,
                                        'page_type' => 'Organisation'
                                )), false);
                        ?>";

                        var point = new google.maps.LatLng(lat, lng);

                        var markerContent = {
                                "id":page_id,
                                "type":"ORGANISATION",
                                "contentString":contentString,
                                "title":title,
                        };

                        var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
                        if(markerIndex != -1){
                                finalMarkers[markerIndex].markersContent.push(markerContent);
                                finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;

                                if(finalMarkers[markerIndex].type == 'ORGANISATION'){
                                        finalMarkers[markerIndex].type = 'ORGANISATION';
                                }else{
                                        finalMarkers[markerIndex].type = 'DIFFERENT';
                                }

                        } else{
                                finalMarkers.push({
                                        "title":title,
                                        "type": "ORGANISATION",
                                        "point":point,
                                        "lat":lat,
                                        "lng":lng,
                                        "markersContent":[markerContent]
                                });
                        }
                }
        }

        // set project markers
        function setProjectMarkers(){
                <?php  if (count($this->projectsList) > 0) : ?>
                        <?php foreach ($this->projectsList as $item) : ?>

                                var lat = <?php echo $item['latitude'] ?>;

                                var lng =<?php echo $item['longitude'] ?>;

                                var project_id = <?php echo $item['project_id'] ?>;

                                var title = "<?php echo str_replace('"', ' ', Engine_Api::_()->getItem('sitecrowdfunding_project', $item['project_id'])->getTitle()) ?>";
                                title = 'Project: ' + title;

                                var contentString = "<?php
                                        echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapProjectInfoWindowContent.tpl', array(
                                                'project_id' => $item['project_id'],
                                                'location_id' => $item['location_id'],
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

                        <?php endforeach; ?>

                <?php endif; ?>
        }

        // set followers markers
        function setFollowersMarkers(){
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

        // set members markers
        function setMembersMarkers(){
                <?php  if (count($this->membersList) > 0) : ?>

                        <?php foreach ($this->membersList as $item) : ?>

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
                                        finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;

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

        // set partner markers
        function setPartnersMarkers(){
                <?php  if (count($this->partnerPages) > 0) : ?>

                        <?php foreach ($this->partnerPages as $item) : ?>

                                var lat = <?php echo $item['latitude'] ?>;

                                var lng =<?php echo $item['longitude'] ?>;

                                var page_id = <?php echo $item['page_id'] ?>;

                                var title =  <?php echo '"'.Engine_Api::_()->getItem('sitepage_page', $item['page_id'])->getTitle().'"' ?>;
                                title = 'Partner: ' + title;

                                var contentString = "<?php
                                        echo $this->string()->escapeJavascript($this->partial('application/modules/Sitepage/views/scripts/_mapOrganisationInfoWindowContent.tpl', array(
                                                'sitepage' =>Engine_Api::_()->getItem('sitepage_page', $item['page_id']),
                                                'location' => $item,
                                                'page_type' => 'Partner Organisation'
                                        )), false);
                                ?>";

                                var point = new google.maps.LatLng(lat, lng);

                                var markerContent = {
                                        "id":page_id,
                                        "type":"PARTNER",
                                        "contentString":contentString,
                                        "title":title,
                                };

                                var markerIndex = finalMarkers.findIndex(m => m.lat === lat && m.lng ===lng);
                                if(markerIndex != -1){
                                        finalMarkers[markerIndex].markersContent.push(markerContent);
                                        finalMarkers[markerIndex].title = finalMarkers[markerIndex].title +" , "+title;

                                        if(finalMarkers[markerIndex].type == 'PARTNER'){
                                                finalMarkers[markerIndex].type = 'PARTNER';
                                        }else{
                                                finalMarkers[markerIndex].type = 'DIFFERENT';
                                        }

                                } else{
                                        finalMarkers.push({
                                                "title":title,
                                                "type": "PARTNER",
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
                <?php  if (count($this->manageadmins) > 0) : ?>

                        <?php foreach ($this->manageadmins as $item) : ?>

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


<div class="sitepage_map b_dark clr">
    <div>
        <form class="global_form checkbox_options_form">
            <div>
                <div>
                    <div class="form-elements">
                        <div class="form-wrapper">
                            <div class="form-element">
                                <ul class="form-options-wrapper checkbox_options">
                                    <li>
                                        <input type="checkbox" id="showOrganisationYn" name="showOrganisationYn" onclick="toggleCheckbox(this)"><label id="showOrganisationLabel" for="Organisation">Organisation</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" id="showProjectsYn" name="showProjectsYn" onclick="toggleCheckbox(this)"><label id="showProjectsLabel" for="Projects">Projects</label>
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
                                    <li>
                                        <input type="checkbox" id="showPartnersYn" name="showPartnersYn" onclick="toggleCheckbox(this)"><label id="showPartnersLabel" for="Partners">Partners</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br/>


    </div>
    <div id="sitepage_map_view_map_canvas"></div>
</div>

<script type="text/javascript">


    function toggleCheckbox(element){
        showOrganisationYn = $('showOrganisationYn').checked;
        showProjectsYn = $('showProjectsYn').checked;
        showFollowersYn = $('showFollowersYn').checked;
        showMembersYn = $('showMembersYn').checked;
        showAdminsYn = $('showAdminsYn').checked;
        showPartnersYn = $('showPartnersYn').checked;
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
        if(showOrganisationYn === true){
            setOrganisationMarkers();
        }
        if(showProjectsYn === true){
            setProjectMarkers();
        }
        if(showFollowersYn === true){
            setFollowersMarkers();
        }
        if(showMembersYn === true){
            setMembersMarkers();
        }
        if(showAdminsYn === true){
            setAdminMarkers();
        }
        if(showPartnersYn === true){
            setPartnersMarkers();
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
    .sitepage_map #sitepage_map_view_map_canvas{
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
    #showOrganisationLabel{
        background-color: #9c0bc0;
        color: white;
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
    #showPartnersLabel{
        background-color: #ce1e1e;
        color: white;
    }
</style>