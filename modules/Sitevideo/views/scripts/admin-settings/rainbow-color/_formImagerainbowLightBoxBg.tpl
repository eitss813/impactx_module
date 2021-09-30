<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowLightboxBg.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
    var s = new MooRainbow('myRainbow1', {
      id: 'myDemo1',
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.bgcolor', '#0A0A0A') ?>"),
      'onChange': function(color) {
        $('sitevideo_lightbox_bgcolor').value = color.hex;
      }
    });
			
  });
</script>

<?php
echo '
	<div id="sitevideo_lightbox_bgcolor-wrapper" class="form-wrapper">
		<div id="sitevideo_lightbox_bgcolor-label" class="form-label">
			<label for="sitevideo_lightbox_bgcolor" class="optional">
				' . $this->translate('Videos Lightbox Background Color') . '
			</label>
		</div>
		<div id="sitevideo_lightbox_bgcolor-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the background of the lightbox displaying videos. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitevideo_lightbox_bgcolor" id="sitevideo_lightbox_bgcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.bgcolor', '#0A0A0A') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>