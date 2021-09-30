<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-location.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
//Location auto suggest
sm4.core.runonce.add(function(){  
if($.mobile.activePage.find('#location').get(0)) {
    var autocomplete = new google.maps.places.Autocomplete($.mobile.activePage.find('#location').get(0));
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

    $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
    $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());

  });  
}
});
</script>

