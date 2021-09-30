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
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>
<?php
  $language = $_COOKIE['en4_language'];
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<script type="text/javascript">
  if(document.getElementById('sitemember_map_city')) {
    window.addEvent('domready', function() {
      new google.maps.places.Autocomplete(document.getElementById('sitemember_map_city'));
      showOtherLocationSetting('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1) ?>');
    });
  }

  function showOtherLocationSetting(options) {

    if (options == 0) {
      if ($('seaocore_locationdefault-wrapper'))
        $('seaocore_locationdefault-wrapper').style.display = 'none';
      if ($('sitemember_proximity_search_kilometer-wrapper'))
        $('sitemember_proximity_search_kilometer-wrapper').style.display = 'none';
      if ($('seaocore_locationdefaultmiles-wrapper'))
        $('seaocore_locationdefaultmiles-wrapper').style.display = 'none';
      if ($('sitemember_map_city-wrapper'))
        $('sitemember_map_city-wrapper').style.display = 'none';
      if ($('sitemember_map_zoom-wrapper'))
        $('sitemember_map_zoom-wrapper').style.display = 'none';
    }
    else {
      if ($('seaocore_locationdefault-wrapper'))
        $('seaocore_locationdefault-wrapper').style.display = 'block';
      if ($('sitemember_proximity_search_kilometer-wrapper'))
        $('sitemember_proximity_search_kilometer-wrapper').style.display = 'block';
      if ($('seaocore_locationdefaultmiles-wrapper'))
        $('seaocore_locationdefaultmiles-wrapper').style.display = 'block';
      if ($('sitemember_map_city-wrapper'))
        $('sitemember_map_city-wrapper').style.display = 'block';
      if ($('sitemember_map_zoom-wrapper'))
        $('sitemember_map_zoom-wrapper').style.display = 'block';
    }
  }
</script>

<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.isActivate', 0)): ?>
  <?php
  $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
  echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP);
  ?>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings mtop15'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
