<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!$this->is_ajax): ?>
  <?php include APPLICATION_PATH . '/application/modules/Sitealbum/widgets/albumlocation-search/index.tpl' ?>
<?php endif; ?>
<style type="text/css">
  .layout_sitealbum_bylocation_album .seaocore_browse_list_photo{
    width: <?php echo $this->photoWidth; ?>px;
    height:<?php echo $this->photoHeight; ?>px;
  }
  .layout_sitealbum_bylocation_album .thumbs_photo span{
    width: <?php echo $this->photoWidth; ?>px !important; 
    height:<?php echo $this->photoHeight; ?>px!important; 
    background-size: cover;
    display:block;
  }
</style>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">

  var current_page = '<?php echo $this->current_page; ?>';
  var paginatePageLocations = function(page) {
    var formElements = document.getElementById('album_filter_form');
    var parms = formElements.toQueryString();
    var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&page=' + page;
    document.getElementById('album_location_loding_image').style.display = '';
//    var url = en4.core.baseUrl + 'widget/index/mod/sitealbum/name/bylocation-album';
    var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
    clearOverlays();
    gmarkers = [];
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': url,
      'data': param,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('album_location_loding_image').style.display = 'none';
        document.getElementById('albumlocation_location_map_anchor').getParent().innerHTML = responseHTML;
        setMarker();
      }
    })
            );
  }
</script>
<script type="text/javascript">
  var pageAction = function(page) {
    paginatePageLocations(page);
  }
</script>

<?php if (empty($this->is_ajax)) : ?>
  <div class="eventlocation_browse_location" id="eventlocation_browse_location" >
    <?php if (count($this->paginator) > 0): ?>
      <div class="eventlocation_map_container_right" id ="eventlocation_map_container_right"></div>
      <div id="eventlocation_map_container" class="eventlocation_map_container absolute" style="visibility:hidden;">
        <div class="eventlocation_map_container_topbar" id='eventlocation_map_container_topbar' style ='display:none;'>
          <a id="largemap" href="javascript:void(0);" onclick="smallLargeMap(1)" class="bold fleft">&laquo; <?php echo $this->translate('Large Map'); ?></a>
          <a id="smallmap" href="javascript:void(0);" onclick="smallLargeMap(0)" class="bold fleft"><?php echo $this->translate('Small Map'); ?> &raquo;</a>
        </div>

        <div class="eventlocation_map_container_map_area fleft seaocore_map" id="eventlocation_map">
          <div class="eventlocation_map_content" id="eventlocation_browse_map_canvas" ></div>
          <?php $siteTitle = $this->coreApi->core_general_site_title; ?>
          <?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="sitealbum_adseresult_list" id="eventlocation_content_content">
    <?php endif; ?>

    <a id="albumlocation_location_map_anchor" class="pabsolute"></a>
    <?php if (count($this->paginator) > 0): ?>
      <?php
      if ($this->photoWidth > $this->normalLargePhotoWidth):
        $photo_type = 'thumb.main';
      elseif ($this->photoWidth > $this->normalPhotoWidth):
        $photo_type = 'thumb.medium';
      else:
        $photo_type = 'thumb.normal';
      endif;
      ?>
      <ul class="seaocore_browse_list" id="seaocore_browse_list"><?php if (!empty($this->is_ajax)) : ?>	
          <li style="border:none"><p>
              <?php echo $this->translate(array('%s album found.', '%s albums found.', $this->totalresults), $this->locale()->toNumber($this->totalresults)) ?>
            </p></li>
          <?php foreach ($this->paginator as $item): ?>
            <?php if (!empty($item->location) || !empty($this->locationVariable)) : ?>
              <li>
                <div class="seaocore_browse_list_photo">
                  <a title= '<?php echo $item->getTitle() ?>' target="_parent" class="thumbs_photo" href="<?php echo $item->getHref(); ?>">
                    <?php $className = !empty($item->location) ? "seao_common_add_tooltip_link marker_photo_" . $item->album_id : 'un_location_eventlocation' ?>
                    <?php 
                      echo  $this->itemBackgroundPhoto($item, ($item->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, $item->getTitle(), array('class' => "$className"));
                    ?>
                  </a>
                </div>

                <div class='seaocore_browse_list_info'>    
                  <?php if (!empty($this->albumInfo)) : ?>
                    <?php if (in_array('albumTitle', $this->albumInfo)): ?>
                      <div class='seaocore_browse_list_info_title'>
                        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getTitle()), array('title' => $this->translate($item->getTitle()), 'target' => '_parent', 'class' => !empty($item->location) ? "seao_common_add_tooltip_link marker_" . $item->album_id : 'un_location_eventlocation')); ?>
                      </div>
                    <?php endif; ?>

                    <?php if (in_array('ownerName', $this->albumInfo)): ?>
                      <div class="seaocore_browse_list_info_date">
                        <?php echo $this->translate('by'); ?>
                        <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                      </div>
                    <?php endif; ?>

                    <?php if (in_array('totalPhotos', $this->albumInfo)): ?>
                      <div class="seao_listings_stats">
                        <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
                        <div title="<?php echo $this->translate(array('%s photo', '%s photos', $item->photos_count), $this->locale()->toNumber($item->photos_count)) ?>"  class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $item->photos_count), $this->locale()->toNumber($item->photos_count)) ?></div>
                      </div>
                    <?php endif; ?>
                    <div class="seaocore_browse_list_info_date">	
                      <?php echo $this->albumInfo($item, $this->albumInfo, array('truncationLocation' => $this->truncationLocation)); ?>
                    </div>
                    <?php if (!empty($item->location)): ?>                   	
                      <?php if (!empty($item->distance) && isset($item->distance)): ?>
                        <div class="seaocore_browse_list_info_stat">
                          <?php if (!$this->coreApi->getSetting('sitealbumproximity.search.kilometer', 0)): ?>
                            <b><?php echo $this->translate("approximately %s miles", round($item->distance, 2)); ?></b>
                          <?php else: ?>
                            <b><?php
                            $distance = (1 / 0.621371192) * $item->distance;
                            echo $this->translate("approximately %s kilometers", round($distance, 2));
                            ?></b>
                            <?php endif; ?>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                  <div class="seaocore_browse_list_info_blurb">
                    <?php echo $this->viewMore($item->description) ?>
                  </div>
                </div>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
        <div class="clr eventlocation_browse_location_paging" style="margin-top:10px;">
          <?php echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "sitealbum"), array("orderby" => $this->orderby)); ?>
          <?php if (count($this->paginator) > 1): ?>
            <div class="fleft" id="album_location_loding_image" style="display: none;margin:5px;">
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
            </div>
          <?php endif; ?>
        </div>	
      <?php endif; ?>
    <?php else: ?>
      <div class="tip"> 
        <span><?php echo $this->translate('Nobody has created an album with that criteria.'); ?></span>
      </div>
    <?php endif; ?> 
    <?php if (empty($this->is_ajax)) : ?>
    </div>
  </div>

  <script type="text/javascript" >

    /* moo style */
    window.addEvent('domready', function() {
      //smallLargeMap(1);
      var globalContentElement = en4.seaocore.getDomElements('content');
      var Clientwidth = $(globalContentElement).getElement(".layout_sitealbum_bylocation_album").clientWidth;

      var offsetWidth = $('eventlocation_map_container').offsetWidth;
      $('eventlocation_browse_map_canvas').setStyle("height", offsetWidth);

      if (document.getElementById("smallmap"))
        document.getElementById("smallmap").style.display = "none";
      if ($('eventlocation_map_right'))
        $('eventlocation_map_right').style.display = 'none';

  <?php if ($this->paginator->count() > 0): ?>
        initialize();
  <?php endif; ?>
    });

    if ($('seaocore_browse_list')) {

      var elementStartY = $('eventlocation_map').getPosition().x;
      var offsetWidth = $('eventlocation_map_container').offsetWidth;
      var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);


      function setMapContent() {

        if (!$('seaocore_browse_list')) {
          return;
        }

        var element = $("eventlocation_map_container");
        if (element.offsetHeight > $('seaocore_browse_list').offsetHeight) {
          if (!element.hasClass('absolute')) {
            element.addClass('absolute');
            element.removeClass('fixed');
            if (element.hasClass('bottom'))
              element.removeClass('bottom');
          }
          return;
        }

        var elementPostionStartY = $('seaocore_browse_list').getPosition().y;
        var elementPostionStartX = $('eventlocation_map_container').getPosition().x;
        var elementPostionEndY = elementPostionStartY + $('seaocore_browse_list').offsetHeight - element.offsetHeight;

        if (((elementPostionEndY) < window.getScrollTop())) {
          if (element.hasClass('absolute'))
            element.removeClass('absolute');
          if (element.hasClass('fixed'))
            element.removeClass('fixed');
          if (!element.hasClass('bottom'))
            element.addClass('bottom');
        }
        else if (((elementPostionStartY) < window.getScrollTop())) {
          if (element.hasClass('absolute'))
            element.removeClass('absolute');
          if (!element.hasClass('fixed'))
            element.addClass('fixed');
          if (element.hasClass('bottom'))
            element.removeClass('bottom');
          element.setStyle("right", actualRightPostion);
          element.setStyle("width", offsetWidth);
        }
        else if (!element.hasClass('absolute')) {
          element.addClass('absolute');
          element.removeClass('fixed');
          if (element.hasClass('bottom'))
            element.removeClass('bottom');
        }
      }

      window.addEvent('scroll', function() {
        setMapContent();
      });

    }

    function smallLargeMap(option) {
      if (option == '1') {
        $('eventlocation_browse_map_canvas').setStyle("height", '400px');
        document.getElementById("largemap").style.display = "none";
        document.getElementById("smallmap").style.display = "block";
        if (!$('eventlocation_map_container').hasClass('eventlocation_map_container_exp'))
          $('eventlocation_map_container').addClass('eventlocation_map_container_exp');
      } else {
        $('eventlocation_browse_map_canvas').setStyle("height", offsetWidth);
        document.getElementById("largemap").style.display = "block";
        document.getElementById("smallmap").style.display = "none";
        if ($('eventlocation_map_container').hasClass('eventlocation_map_container_exp'))
          $('eventlocation_map_container').removeClass('eventlocation_map_container_exp');

      }
      setMapContent();
      google.maps.event.trigger(map, 'resize');
    }
  </script>
  <script type="text/javascript" >
    function owner(thisobj) {
      var Obj_Url = thisobj.href;
      Smoothbox.open(Obj_Url);
    }
  </script>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
?>
  <script type="text/javascript" >
    //<![CDATA[
    // this variable will collect the html which will eventually be placed in the side_bar
    var side_bar_html = "";

    // arrays to hold copies of the markers and html used by the side_bar
    // because the function closure trick doesnt work there
    var gmarkers = [];
    var infoBubbles;
    var markerClusterer = null;
    // global "map" variable
    var map = null;
    // A function to create the marker and set up the event window function
    function createMarker(latlng, name, html, album_title, album_id) {
      var contentString = html;
      if (name == 0) {
        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          title: album_title,
          // album_id : album_id,
          animation: google.maps.Animation.DROP,
          zIndex: Math.round(latlng.lat() * -100000) << 5
        });
      }
      else {
        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          title: album_title,
          //album_id: album_id,
          draggable: false,
          animation: google.maps.Animation.BOUNCE
        });
      }

      gmarkers.push(marker);
      google.maps.event.addListener(marker, 'click', function() {
        google.maps.event.trigger(map, 'resize');
        map.setCenter(marker.position);
        //map.setZoom(<?php //echo '5';                        ?> );
        infoBubbles.open(map, marker);
        infoBubbles.setContent(contentString);
      });

      //Show tooltip on the mouse over.
      $$('.marker_' + album_id).each(function(locationMarker) {
        locationMarker.addEvent('mouseover', function(event) {
          google.maps.event.trigger(map, 'resize');
          map.setCenter(marker.position);
          infoBubbles.open(map, marker);
          infoBubbles.setContent(contentString);
        });
      });

      //Show tooltip on the mouse over.
      $$('.marker_photo_' + album_id).each(function(locationMarker) {
        locationMarker.addEvent('mouseover', function(event) {
          google.maps.event.trigger(map, 'resize');
          map.setCenter(marker.position);
          infoBubbles.open(map, marker);
          infoBubbles.setContent(contentString);
        });
      });
    }

    function initialize() {
      // create the map
      var myOptions = {
        zoom: 1,
        center: new google.maps.LatLng(0, 0),
        //  mapTypeControl: true,
        // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }

      map = new google.maps.Map(document.getElementById("eventlocation_browse_map_canvas"),
              myOptions);

      google.maps.event.addListener(map, 'click', function() {
  <?php if ($this->paginator->count() > 0): ?>
          infoBubbles.close();
  <?php endif; ?>
      });
      setMarker();

    }

    function clearOverlays() {
      infoBubbles.close();
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

    function setMapCenterZoomPoint(bounds, maplocation) {
      if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
        var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
      }
      if (bounds && bounds.center_lat && bounds.center_lng) {
        maplocation.setCenter(new google.maps.LatLng(bounds.center_lat, bounds.center_lng), <?php echo $this->coreApi->getSetting('sitetagcheckin.map.zoom', 2) ?>);
      } else {
        maplocation.setCenter(new google.maps.LatLng(lat, lng), <?php echo $this->coreApi->getSetting('sitetagcheckin.map.zoom', 2) ?>);
      }
      if (bds) {
        maplocation.setCenter(bds.getCenter());
        maplocation.fitBounds(bds);
      }
    }

    infoBubbles = new InfoBubble({
      maxWidth: 400,
      maxHeight: 400,
      shadowStyle: 1,
      padding: 0,
      backgroundColor: '<?php echo $this->coreApi->getSetting('seaocore.tooltip.bgcolor', '#ffffff'); ?>',
      borderRadius: 5,
      arrowSize: 10,
      borderWidth: 1,
      borderColor: '#2c2c2c',
      disableAutoPan: true,
      hideCloseButton: false,
      arrowPosition: 50,
      //backgroundClassName: 'sitetag_checkin_map_tip',
      arrowStyle: 0
    });
  </script>

  <style type="text/css">
    #eventlocation_browse_map_canvas {
      width: 100% !important;
      height: 400px;
      float: left;
    }
    #eventlocation_browse_map_canvas > div{
      height: 300px;
    }
    #infoPanel {
      float: left;
      margin-left: 10px;
    }
    #infoPanel div {
      margin-bottom: 5px;
    }
  </style>
<?php endif; ?>

<script type="text/javascript" >

  function setMarker() {
    clearOverlays();

<?php if (count($this->paginator) > 0) : ?>
  <?php foreach ($this->paginator as $location) : ?>
    <?php if (!empty($location->location)) : ?>
          // obtain the attribues of each marker
          var lat = '<?php echo $location->latitude ?>';
          var lng = '<?php echo $location->longitude ?>';
          var point = new google.maps.LatLng(lat, lng);
          var album_id = '<?php echo $location->album_id; ?>';
          var sponsored = 0;
          // create the marker

      <?php $album_id = $location->album_id; ?>
          var contentString = '<div id="content">' +
                  '<div id="siteNotice">' +
                  '</div>' + '  <div class="sitealbum_map_location">' +
                  '<div class="sitealbum_map_location_title">' +
                  '<?php
      echo $this->htmlLink($location->getHref(), $this->string()->escapeJavascript($location->getTitle()), array('title' => $this->string()->escapeJavascript($location->getTitle()), 'target' => '_parent'));
      ?>' +
                  '<div class="clr"></div>' +
                  '</div>' +
                  '<div class="sitealbum_map_location_photo" >' +
                  '<?php echo $this->htmlLink($location->getHref(), $this->itemPhoto($location, 'thumb.icon'), array('title' => $this->string()->escapeJavascript($location->getTitle()), 'target' => '_parent')); ?>' +
                  '</div>' +
                  '<div class="sitealbum_map_location_info">' +
                  '<div class="sitealbum_map_location_stat">' +
                  '<?php echo $this->translate("By: "); ?><?php echo $this->htmlLink($location->getOwner()->getHref(), $this->string()->escapeJavascript($location->getOwner()->getTitle()), array('class' => 'thumbs_author')) ?> ' +
                  '</div>' +
                  '<div class="seao_listings_stats">' +
                  '<i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>' +
                  '<div title="<?php echo $this->translate(array('%s photo', '%s photos', $location->photos_count), $this->locale()->toNumber($location->photos_count)) ?>"  class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $location->photos_count), $this->locale()->toNumber($location->photos_count)) ?></div>' +
                  '</div>' +
                  '<div class="seao_listings_stats">' +
                  '<i class="seao_icon_strip seao_icon seao_icon_time" title="Creation Date"></i>' +
                  '<div class="o_hidden">' +
      <?php
      $statistics = '';

      $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $location->like_count), $this->locale()->toNumber($location->like_count))) . ', ';


      $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $location->comment_count), $this->locale()->toNumber($location->comment_count)));
      $statistics = trim($statistics);
      $statistics = rtrim($statistics, ',');
      ?>
          '<?php echo $statistics; ?>' +
                  '</div>' +
                  '</div>' +
                  '<div class="sitealbum_map_location_stat">' +
                  "<?php $this->translate("Location: ");
      echo $this->string()->escapeJavascript($location->location); ?> " +
                  '</div>' +
                  '</div>' +
                  '<div class="clr"></div>' +
                  ' </div>' +
                  '</div>';
          var marker = createMarker(point, sponsored, contentString, "<?php echo str_replace('"', ' ', $location->getTitle()); ?>", album_id);
    <?php endif; ?>
  <?php endforeach; ?>
      $('eventlocation_map_container').style.display = 'block';
      google.maps.event.trigger(map, 'resize');
<?php else: ?>
      $('eventlocation_map_container').style.display = 'none';
<?php endif; ?>
    $$('.un_location_eventlocation').addEvent('mouseover', function(event) {
      infoBubbles.close();
    });
  }
</script>
