<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-address.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $language = $_COOKIE['en4_language'];
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<div class="global_form_popup">
  <?php echo $this->form->render($this); ?>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    if (document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
      var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
    }
  });
</script>

