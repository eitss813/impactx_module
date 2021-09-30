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
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
  $language = $_COOKIE['en4_language'];
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
  var myLatlng;
  function initializeSidebarMap() {
    var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    var myOptions = {
      zoom: <?php echo $this->location->zoom; ?>,
      center: myLatlng,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("sitemember_view_map_canvas_sidebar"), myOptions);

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "<?php echo str_replace('"', ' ', $this->sitemember->getTitle()) ?>"

    });

    google.maps.event.addListener(marker, 'click', function() {
      //infowindow.open(map,marker);
    });

    $$('.tab_layout_sitemember_location_sidebar_sitemember').addEvent('click', function() {
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?>);
      map.setCenter(myLatlng);
    });

    google.maps.event.addListener(map, 'click', function() {
      //infowindow.close();
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?>);
      map.setCenter(myLatlng);
    });
  }
</script>

<div class="sitemember_profile_map b_dark clr">
  <ul>
    <li class="seaocore_map">
      <div id="sitemember_view_map_canvas_sidebar" style="height:<?php echo $this->height; ?>px"></div>
    </li>
  </ul>
</div>	

<div class='clr o_hidden'>
  <ul class="sitemember_side_widget sitemember_profile_member_info">
    <li class="clr">
      <i class="seao_icon_strip seao_icon seao_icon_location"></i>
      <div class="o_hidden">
        <?php echo $this->location->location; ?> - <b>
          <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->locationitem_id, 'resouce_type' => 'seaocore'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?></b>
      </div>
    </li>
  </ul>
</div>

<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>

<script type="text/javascript">
  window.addEvent('domready', function() {
    initializeSidebarMap();
  });
</script>