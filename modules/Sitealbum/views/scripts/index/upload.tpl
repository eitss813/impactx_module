<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<script type="text/javascript">
  var updateTextFields = function()
  {
    var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper', '#sitealbum_location-wrapper',
      '#auth_view-wrapper', '#auth_comment-wrapper', '#auth_tag-wrapper', '#tags-wrapper'];
    fieldToggleGroup = $$(fieldToggleGroup.join(','))
    if ($('album').get('value') == 0) {
      fieldToggleGroup.show();
    } else {
      fieldToggleGroup.hide();
    }
  }
  en4.core.runonce.add(updateTextFields);

  window.addEvent('domready', function() {
    if ($('sitealbum_location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
      var autocomplete = new google.maps.places.Autocomplete(document.getElementById('sitealbum_location'));
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
        var locationParams = '{"location" :"' + document.getElementById('sitealbum_location').value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
        data.name = place.name;
        data.google_id = place.id;
        data.latitude = place.geometry.location.lat();
        data.longitude = place.geometry.location.lng();
        data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
        data.icon = place.icon;
        data.types = place.types.join(',');
        data.prefixadd = data.types.indexOf('establishment') > -1 ? en4.core.language.translate('at') : en4.core.language.translate('in');
        data.resource_guid = 0;
        data.type = 'place';
        data.reference = place.reference;
        var dataHash = new Hash(data);
        dataHashStr = dataHash.toQueryString();
        document.getElementById('dataParams').value = dataHashStr;
        document.getElementById('locationParams').value = locationParams;

      });
    }

  });

<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 1)): ?>
    en4.core.runonce.add(function()
    {
      new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'album'), 'default', true) ?>', {
        'postVar': 'text',
        'minLength': 1,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'className': 'tag-autosuggest',
        'customChoices': true,
        'filterSubset': true, 'multiple': true,
        'injectChoice': function(token) {
          var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
          new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
          choice.inputValue = token;
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
      });
    });
<?php endif; ?>

    var getProfileType = function(category_id) {
      var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitealbum')->getMapping(array('category_id', 'profile_type'))); ?>;
      for (i = 0; i < mapping.length; i++) {
        if (mapping[i].category_id == category_id)
          return mapping[i].profile_type;
      }
      return 0;
    }
    en4.core.runonce.add(function() {
      var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
      if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
        $(defaultProfileId).setStyle('display', 'none');
      }
    });
</script>
<?php if (Engine_Api::_()->seaocore()->isMobile()): ?>
<style type="text/css">
#form-upload #submit-wrapper {
    display: block;
}
</style>
<?php endif;  ?>
<?php echo $this->form->render($this) ?>
