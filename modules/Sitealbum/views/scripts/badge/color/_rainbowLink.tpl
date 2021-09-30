<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _rainbowLink.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		var s = new MooRainbow('linkColor', {
			id: 'rainbow_link',
			'startColor': hexcolorTonumbercolor("#4E81A1"),
			'onChange': function(color) {
				$('link_color').value = color.hex;
         previewBadge(1);
			}
		});

	});
</script>

<?php
echo '
	<div id="link_color-wrapper" class="form-wrapper">
		<div id="link_color-label" class="form-label">
			<label for="link_color" class="optional">
				'. $this->translate('Badge Link Hover Color').'
			</label>
		</div>
		<div id="link_color-element" class="form-element">
			<p class="description">'.$this->translate('Select a color for the link on-hover in the badge. (Click on the rainbow below to choose your color.)').'</p>
			<input name="link_color" id="link_color" value= #4E81A1 type="text" style="width:80px;">
			<input name="linkColor" id="linkColor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>