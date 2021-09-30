<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AvpCategoryLine.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    window.addEvent('domready', function () {
        var s = new MooRainbow('borderColor', {
            id: 'rainbow_border',
            'startColor': hexcolorTonumbercolor("#000000"),
            'onChange': function (color) {
                $('border_color').value = color.hex;
                previewBadge(1);
            }
        });

    });
</script>

<?php

echo '
	<div id="border_color-wrapper" class="form-wrapper">
		<div id="border_color-label" class="form-label">
			<label for="border_color" class="optional">
				' . $this->translate('Badge Border Color') . '
			</label>
		</div>
		<div id="border_color-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the border of the badge. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="border_color" id="border_color" value= #000000 type="text" style="width:80px;">
			<input name="borderColor" id="borderColor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>