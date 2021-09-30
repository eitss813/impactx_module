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
        var s = new MooRainbow('linkColor', {
            id: 'rainbow_link',
            'startColor': hexcolorTonumbercolor("#4E81A1"),
            'onChange': function (color) {
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
				' . $this->translate('Badge Link Hover Color') . '
			</label>
		</div>
		<div id="link_color-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the link on-hover in the badge. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="link_color" id="link_color" value= #4E81A1 type="text" style="width:80px;">
			<input name="linkColor" id="linkColor" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>