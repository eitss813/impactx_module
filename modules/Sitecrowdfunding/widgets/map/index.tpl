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

//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">

    // global "map" variable
    var map = null;
    var bounds = null;

    var projectIcon = {
        url: "<?php echo $projectMapIcon; ?>", // url
    };

    // Init the map
    function initialize() {

        var defaultLatlng = new google.maps.LatLng(39.305, -76.617);
        var mapOptions = {
            navigationControl: true,
            center: defaultLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoom: 16
        };

        map = new google.maps.Map(document.getElementById("sitecrowdfunding_map_view_canvas"), mapOptions);
        bounds = new google.maps.LatLngBounds();
        bounds.extend(defaultLatlng);

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);
    }

    // A function to create the marker and set up the event window function
    function createMarker(latlng, title, html, project_id,location_id) {
        var contentString = html;

        bounds.extend(latlng);

        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: title,
            icon: projectIcon
        });

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 400,
            maxHeight : 400
        });

        google.maps.event.addListener(marker, 'click', function () {
            infowindow.open(map, marker);
        });

        // Center the map to fit all markers on the screen
        map.fitBounds(bounds);

    }

    // set markers
    function setMarkers(){
        <?php  if (count($this->paginator) > 0) : ?>
            <?php foreach ($this->paginator as $item) : ?>

                var lat = <?php echo $item['latitude'] ?>;

                var lng =<?php echo $item['longitude'] ?>;

                var title = <?php echo '"'.str_replace('"', '', $item['title']).'"' ?>;

                var project_id = <?php echo $item['project_id'] ?>;

                var location_id = <?php echo $item['location_id'] ?>;

                var contentString = "<?php
                    echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapProjectInfoWindowContent.tpl', array(
                        'project_id' => $item['project_id'],
                        'location_id' => $item['location_id'],
                    )), false);
                ?>";

                var point = new google.maps.LatLng(lat, lng);

                var marker = createMarker(point, title ,contentString, project_id,location_id);

            <?php endforeach; ?>
        <?php endif; ?>
    }

</script>


<div class="sitecrowdfunding_map b_dark clr">
    <div id="sitecrowdfunding_map_view_canvas"></div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function () {
        initialize();
        setMarkers();
    });
</script>

<style type="text/css">
    .sitecrowdfunding_map #sitecrowdfunding_map_view_canvas {
        width: 100%;
        height: 450px;
    }
</style>