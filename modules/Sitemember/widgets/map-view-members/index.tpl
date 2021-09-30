<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>

<?php if ($this->paginator->count() > 0): ?>
  <?php 
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
   
  ?>
 <?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/markerclusterer.js");
?>
<?php endif; ?>

<div id="sitemember_location_map_none" style="display: none;"></div>

  <?php $latitude = $this->settings->getSetting('sitemember.map.latitude', 0); ?>
  <?php $longitude = $this->settings->getSetting('sitemember.map.longitude', 0); ?>
  <?php $defaultZoom = $this->settings->getSetting('sitemember.map.zoom', 1); ?>
  <?php $enableBouce = $this->enableBounce; ?>
  <?php $locationMarker = $this->settings->getSetting('sitemember.location.marker', 0); ?>

  <?php if ($this->paginator->count() > 0): ?>
 
    <?php if (empty($this->is_ajax)): ?>

      <script>
        function sendAjaxRequestSR() {

          var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $('dynamic_app_info_sitemember_' +<?php echo sprintf('%d', $this->identity) ?>)
          }
          params.requestParams.page = getNextPage();

          params.requestParams.content_id = '<?php echo $this->identity ?>';
          $('seaocore_view_more_<?php echo $this->identity ?>').style.display = 'none';
          $('seaocore_loading').style.display = '';
          var url = en4.core.baseUrl + 'widget';

          var request = new Request.HTML({
            method: 'get',
            url: url,
            data: $merge(params.requestParams, {
              format: 'html',
              subject: en4.core.subject.guid,
              is_ajax: true,
              loaded_by_ajax: true
            }),
            evalScripts: true,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
              if (params.requestParams.page == 1) {
                params.responseContainer.empty();
                Elements.from(responseHTML).inject(params.responseContainer);
                initialize();
              } else {
                var element = new Element('div', {
                  'html': responseHTML
                });

                params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');
              }
              en4.core.runonce.trigger();
              Smoothbox.bind(params.responseContainer);
            }
          });
          en4.core.request.send(request);
        }
      </script>

    <?php endif; ?>

    <div id="dynamic_app_info_sitemember_<?php echo $this->identity; ?>">

        <div class="sitemember_browse_lists_view_options b_medium">
          <div class="fleft">
            <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
          </div>
        </div>

      <div id="sitemember_map_canvas_view_browse">
        <div class="seaocore_map clr o_hidden">
          <div id="sitemember_browse_map_canvas" class="sitemember_list_map"> </div>
          <div class="clear mtop10"></div>
          <?php $siteTitle = $this->settings->core_general_site_title; ?>
          <?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
          <?php endif; ?>
        </div>
        <?php if ($this->flageSponsored && $enableBouce): ?>
          <a href="javascript:void(0);" onclick="toggleBounce();" class="fleft sitemember_list_map_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <?php endif; ?>
      </div>
      <div class="clear"></div>
      <div class="seaocore_pagination"></div>
      <div class="clr" id="scroll_bar_height"></div>
      <div id="seaocore_view_more_<?php echo $this->identity ?>" class="seaocore_view_more mtop10" style="display: none;">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => '',
            'class' => 'buttonlink icon_viewmore'
        ))
        ?>
      </div>
      <div class="seaocore_loading" id="seaocore_loading" style="display: none;">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' class='mright5' />
        <?php echo $this->translate("Loading ...") ?>
      </div>
    <?php endif; ?>
  </div>

<?php if (empty($this->is_ajax)): ?>

    <script type="text/javascript">
      var markerClusterer = null;
      // arrays to hold copies of the markers and html used by the side_bar
      // because the function closure trick doesnt work there
      var gmarkers = [];
      // global "map" variable
      var map = null;
      // A function to create the marker and set up the event window function
      function createMarker(latlng, name, html, location, count,photo) {
        
        var image = (photo) ? photo : ('https:' == document.location.protocol ? 'https://' : 'http://') + 'chart.googleapis.com/chart?chst=d_map_pin_letter&chco=FFFFFF,008CFF,000000&chld=' + count + '|008CFF|000000';

        var contentString = html;
        if (name == 0) {
          var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: location,
            icon: image,
            count: count,
            animation: google.maps.Animation.DROP,
            zIndex: Math.round(latlng.lat() * -100000) << 5
          });
        }
        else {
          var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: location,
            icon: image,
            count: count,
            draggable: false,
            animation: google.maps.Animation.BOUNCE
          });
        }
        gmarkers.push(marker);
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(contentString);
          google.maps.event.trigger(map, 'resize');
          infowindow.open(map, marker);
        });
      }

      function initialize() {

        // create the map
        var myOptions = {
          zoom: <?php echo $defaultZoom ?>,
          center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
          navigationControl: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("sitemember_browse_map_canvas"), myOptions);
        google.maps.event.addListener(map, 'click', function() {
          infowindow.close();
          google.maps.event.trigger(map, 'resize');
        });

        clearOverlays();
      }

      function clearOverlays() {
        infowindow.close();
        google.maps.event.trigger(map, 'resize');

        if (gmarkers) {
          for (var i = 0; i < gmarkers.length; i++) {
            gmarkers[i].setMap(null);
          }
        }

        if (markerClusterer) {
          markerClusterer.clearMarkers();
        }
      }

      en4.core.runonce.add(function() {
        <?php if ($this->paginator->count() > 0): ?>
            initialize();
        <?php endif; ?>
      });
    </script>
<?php endif; ?>

<?php if ($this->paginator->count() > 0): ?>

    <script type="text/javascript">
      //<![CDATA[
      // this variable will collect the html which will eventually be placed in the side_bar
      en4.core.runonce.add(function() {
        <?php if (count($this->locations) > 0) : ?>
        <?php foreach ($this->locations as $location): ?>
        <?php if(!$locationMarker) : ?> 
          <?php $user = Engine_Api::_()->user()->getUser($location['resource_id']); ?>
          <?php endif; ?>
          
          var photo = '<?php echo ($user) ? $user->getPhotoUrl("thumb.icon") : null ?>';
          // obtain the attribues of each marker
          var lat = <?php echo $location['latitude'] ?>;
          var lng =<?php echo $location['longitude'] ?>;
          var point = new google.maps.LatLng(lat, lng);
            <?php if (!empty($enableBouce)): ?>
                var sponsored = '<?php echo $this->sitemember[$location->locationitem_id]->sponsored ?>';
            <?php else: ?>
                var sponsored = 0;
            <?php endif; ?>
            var contentString = "<?php echo $this->string()->escapeJavascript($this->partial('application/modules/Sitemember/views/scripts/_mapInfoWindowContent.tpl', array(
                  'sitemember' => $this->sitemember[$location->locationitem_id],
                  'statistics' => $this->statistics,
                  'customParams' => $this->customParams,
                  'custom_field_title' => $this->custom_field_title,
                  'custom_field_heading' => $this->custom_field_heading,
              )), false);
            ?>";
            var marker = createMarker(point, sponsored, contentString, "<?php echo $location->location ?>", 1,photo);

        <?php endforeach; ?>
        <?php endif; ?>

        markerClusterer = new MarkerClusterer(map, gmarkers, {
          zoomOnClick: true,
          styles:[{
              url: "https://googlemaps.github.io/js-marker-clusterer/images/m1.png",
              width: 53,
              height:53,
              fontFamily:"comic sans ms",
              textColor:"white",  
          }]
        });

        google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
          var info = new google.maps.MVCObject;
          info.set('position', cluster.center_);
          var markers = cluster.getMarkers();

          for (var i = 1; i < markers.length; i++) {
            if (info.position != markers[i].position) {
              return;
            }
          }
          if (marker) {
            marker.setMap(null);
            marker = null;
          }
        });
      });
      var infowindow = new google.maps.InfoWindow({
        size: new google.maps.Size(250, 50)
      });
      function toggleBounce() {
        for (var i = 0; i < gmarkers.length; i++) {
          if (gmarkers[i].getAnimation() != null) {
            gmarkers[i].setAnimation(null);
          }
        }
      }
    </script>
<?php endif; ?>
    
<script type="text/javascript">
    
    en4.core.runonce.add(function() {
      $('seaocore_view_more_<?php echo $this->identity ?>').style.display = 'block';
      hideViewMoreLink();
    });

    function getNextPage() {
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }

    function hideViewMoreLink() {
      var view_more_content_map_view = $('seaocore_view_more_<?php echo $this->identity ?>');
      view_more_content_map_view.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalResults == 0 ? 'none' : '' ) ?>');
      view_more_content_map_view.removeEvents('click');
      view_more_content_map_view.addEvent('click', function() {
        sendAjaxRequestSR();
      });
    }
</script>    

<script type="text/javascript" >
    google.maps.event.trigger(map, 'resize');
    map.setZoom(<?php echo $defaultZoom ?>);
    map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
</script>    

