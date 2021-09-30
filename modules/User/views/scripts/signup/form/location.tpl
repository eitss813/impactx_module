<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 10143 2014-03-26 16:18:25Z andres $
 * @author     John
 */
?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>

<?php echo $this->form->render($this) ?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
            <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/locationWithAddress.tpl'; ?>
        }
    });
</script>

