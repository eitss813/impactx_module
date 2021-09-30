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
        var s = new MooRainbow('textColor', {
            id: 'rainbow_text',
            'startColor': hexcolorTonumbercolor("#5F93B4"),
            'onChange': function (color) {
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
				' . $this->translate('Badge Link Text Color') . '
			</label>
		</div>
		<div id="text_color-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the text in the badge. (Click on the rainbow below to choose your color.)') . '</p>
      <input name="text_color" id="text_color" value= #5F93B4 type="text" style="width:80px;">
			<input name="textColor" id="textColor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>