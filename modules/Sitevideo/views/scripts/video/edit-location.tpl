<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-location.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<div class="global_form_popup">
  <?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    if (document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
      var autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'));
      google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
          return;
        }

        var address = '', country = '', state = '', zip_code = '', city = '';
        var data = {};
        if (place.address_components) {
          var len_add = place.address_components.length;

          for (var i = 0; i < len_add; i++) {
            var types_location = place.address_components[i]['types'][0];
            if (types_location === 'country') {
              country = place.address_components[i]['long_name'];
            } else if (types_location === 'administrative_area_level_1') {
              state = place.address_components[i]['long_name'];
            } else if (types_location === 'administrative_area_level_2') {
              city = place.address_components[i]['long_name'];
            } else if (types_location === 'zip_code') {
              zip_code = place.address_components[i]['long_name'];
            } else if (types_location === 'street_address') {
              if (address === '')
                address = place.address_components[i]['long_name'];
              else
                address = address + ',' + place.address_components[i]['long_name'];
            } else if (types_location === 'locality') {
              if (address === '')
                address = place.address_components[i]['long_name'];
              else
                address = address + ',' + place.address_components[i]['long_name'];
            } else if (types_location === 'route') {
              if (address === '')
                address = place.address_components[i]['long_name'];
              else
                address = address + ',' + place.address_components[i]['long_name'];
            } else if (types_location === 'sublocality') {
              if (address === '')
                address = place.address_components[i]['long_name'];
              else
                address = address + ',' + place.address_components[i]['long_name'];
            }
          }
        }
        var locationParams = '{"location" :"' + document.getElementById('location').value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
        data.name = place.name;
        data.google_id = place.id;
        data.latitude = place.geometry.location.lat();
        data.longitude = place.geometry.location.lng();
        data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
        data.icon = place.icon;
        data.types = place.types.join(',');
        data.prefixadd=data.types.indexOf('establishment') > -1 ? en4.core.language.translate('at'):en4.core.language.translate('in');
        data.resource_guid = 0;
        data.type = 'place';
        data.reference = place.reference;
        var dataHash = new Hash(data);
        dataHashStr= dataHash.toQueryString();
        document.getElementById('dataParams').value = dataHashStr;
        document.getElementById('locationParams').value = locationParams;
      });
    }
  });
</script>