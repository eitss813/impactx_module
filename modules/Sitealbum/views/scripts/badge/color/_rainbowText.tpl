<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _rainbowText.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		var s = new MooRainbow('textColor', {
			id: 'rainbow_text',
			'startColor': hexcolorTonumbercolor("#5F93B4"),
			'onChange': function(color) {
				$('text_color').value = color.hex;
         previewBadge(1);
			}
		});

	});
</script>

<?php
echo '
	<div id="text_color-wrapper" class="form-wrapper">
		<div id="text_color-label" class="form-label">
			<label for="text_color" class="optional">
				'. $this->translate('Badge Link Text Color').'
			</label>
		</div>
		<div id="text_color-element" class="form-element">
			<p class="description">'.$this->translate('Select a color for the text in the badge. (Click on the rainbow below to choose your color.)').'</p>
      <input name="text_color" id="text_color" value= #5F93B4 type="text" style="width:80px;">
			<input name="textColor" id="textColor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>