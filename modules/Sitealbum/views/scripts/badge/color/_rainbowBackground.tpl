<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _rainbowBackground.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
    var s = new MooRainbow('backgroundColor', {
      id: 'rainbow_background',
      'startColor': hexcolorTonumbercolor("#FFFFFF"),
      'onChange': function(color) {
        $('background_color').value = color.hex;
        previewBadge(1);
      }
    });
		
  });
</script>

<?php

echo '
	<div id="background_color-wrapper" class="form-wrapper">
		<div id="background_color-label" class="form-label">
			<label for="background_color" class="optional">
				' . $this->translate('Badge Background Color') . '
			</label>
		</div>
		<div id="background_color-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the background in the badge. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="background_color" id="background_color" value= #FFFFFF type="text" style="width:80px;">
			<input name="backgroundColor" id="backgroundColor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>