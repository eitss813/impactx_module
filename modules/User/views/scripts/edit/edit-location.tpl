<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editaddress.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<div class="sitecrowdfunding_form_popup">
    <?php echo $this->form->render($this); ?>
    <a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>
</div>
<script type="text/javascript">
en4.core.runonce.add(function() {
		if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
			var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
			<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
		}
});
</script>

