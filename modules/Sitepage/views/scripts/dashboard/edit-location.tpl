<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-location.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>


<div class="generic_layout_container layout_middle">
<div class="generic_layout_container layout_core_content">
<?php //include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class='layout_middle'>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
	<?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Location', 'sectionDescription' => '')); ?>
	<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	?>
  <div class="sitepage_edit_content">
  <div id="show_tab_content">
  	<div class="sitepage_editlocation_wrapper">
			<?php if (!empty($this->location)): ?>
				<h4><?php echo $this->translate('Edit Location') ?></h4>
				<p>
					<?php echo $this->translate('Edit location by clicking on "Edit Location" below. You can also accurately mark the position of your entity on the map by dragging-and-dropping the marker(shown in red color) on the map at the right position, and then click on "Save Changes" to save the position.') ?>
				</p>
				<br />
				<div class="edit_form">
					<div class="global_form_box">
						<div>
							<?php if (!empty($this->location->locationname)) : ?>
								<div class="formlocation_edit_label"><?php echo $this->translate('Location Title: ');?></div><div class="formlocation_add"><?php echo $this->location->locationname ?></div>
							<?php endif; ?>
							<div class="formlocation_edit_label"><?php echo $this->translate('Location: ');?></div>
							<?php
								echo $this->htmlLink(array(
								'route' => 'sitepage_dashboard',
								'page_id' => $this->sitepage->page_id,
								'location_id' => $this->location->location_id,
								'action' => 'edit-address'
								), $this->translate('Edit Location'), array(
								
								'class' => 'smoothbox icon_sitepages_map_edit buttonlink fright',
								));
							?>
							<div class="formlocation_add"><?php echo $this->location->location ?></div>
						</div>
					</div>
				</div>
				<div class="edit_form">
					<?php echo $this->form->render($this); ?>
				</div>	
				<div class="global_form">
						<div>
							<div class="seaocore_map" style="padding:0px;">
								<div id="mapCanvas"></div>
								<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
								<?php if (!empty($siteTitle)) : ?>
								<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
								<?php endif; ?>
							</div>	
						</div>
				</div>            
			<?php else: ?>
				<div class="tip">
					<span>
					<?php echo $this->translate('You have not added a location for your page. Click'); ?>
											<a  onclick="javascript:Smoothbox.open('<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit-address', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true) ?>');"
													href="javascript:void(0);"><?php echo $this->translate('here'); ?></a>
							<?php echo $this->translate('to add it.'); ?>
					</span>
				</div>
			<?php endif; ?>
			</div>
		</div>
  </div>
  </div>
</div>
</div>
<?php if (!empty($this->location)): ?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">
  var map;
  var geocoder = new google.maps.Geocoder();
  var tresponses;
  function geocodePosition(pos) {
    geocoder.geocode({
      latLng: pos
    }, function(responses) {
      if (responses && responses.length > 0) {
        updateMarkerAddress(responses[0].formatted_address);
        //	tresponses=responses;
        var len_add = responses[0].address_components.length;

        document.getElementById('address').value='';
        document.getElementById('country').value='';
        document.getElementById('state').value ='';
        document.getElementById('city').value ='';
        document.getElementById('zipcode').value ='';
        for (i=0; i< len_add; i++) {


          var types_location = responses[0].address_components[i].types;

          if(types_location=='country,political'){

            document.getElementById('country').value = responses[0].address_components[i].long_name;
          }else if(types_location=='administrative_area_level_1,political')
          {
            document.getElementById('state').value = responses[0].address_components[i].long_name;
          }else if(types_location=='administrative_area_level_2,political')
          {
            document.getElementById('city').value = responses[0].address_components[i].long_name;
          }else if(types_location=='postal_code')
          {
            document.getElementById('zipcode').value = responses[0].address_components[i].long_name;
          }
          else if(types_location=='street_address')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;

          }else if(types_location=='locality,political')
          {  if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;
          }else if(types_location=='route')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;
          }else if(types_location=='sublocality,political')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;

          }

        }

        document.getElementById('zoom').value=map.getZoom();
      } else {
        document.getElementById('address').value='';
        document.getElementById('country').value='';
        document.getElementById('state').value ='';
        document.getElementById('city').value ='';
        updateMarkerAddress('Cannot determine address at this location.');
      }
    });
  }


  function updateMarkerPosition(latLng) {
    document.getElementById('latitude').value = latLng.lat();
    document.getElementById('longitude').value = latLng.lng();
  }

  function updateMarkerAddress(str) {
    document.getElementById('formatted_address').value = str;
  }

  function initialize() {
    var latLng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    map = new google.maps.Map(document.getElementById('mapCanvas'), {
      zoom: <?php echo $this->location->zoom; ?>,
      center: latLng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var marker = new google.maps.Marker({
      position: latLng,
      title: 'Point Location',

      map: map,
      draggable: true
    });

   
    // Add dragging event listeners.
    google.maps.event.addListener(marker, 'dragstart', function() {
      updateMarkerAddress('Dragging...');
    });

    google.maps.event.addListener(marker, 'drag', function() {
      // updateMarkerStatus('Dragging...');
      updateMarkerPosition(marker.getPosition());
    });

    google.maps.event.addListener(marker, 'dragend', function() {
      //  updateMarkerStatus('Drag ended');



      geocodePosition(marker.getPosition());
    });
  }

  // Onload handler to fire off the app.
  google.maps.event.addDomListener(window, 'load', initialize);
</script>

<?php endif; ?>