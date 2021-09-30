<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _themeColor.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
?>
<script type="text/javascript">
    function hexcolorTonumbercolor(hexcolor) {
        var hexcolorAlphabets = "0123456789ABCDEF";
        var valueNumber = new Array(3);
        var j = 0;
        if (hexcolor.charAt(0) == "#")
            hexcolor = hexcolor.slice(1);
        hexcolor = hexcolor.toUpperCase();
        for (var i = 0; i < 6; i += 2) {
            valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i + 1));
            j++;
        }
        return(valueNumber);
    }

    window.addEvent('domready', function () {

        var r = new MooRainbow('myRainbow4', {
            id: 'myDemo4',
            'startColor': hexcolorTonumbercolor("<?php echo $coreSettings->getSetting('sitecoretheme.theme.color', '#44bbff') ?>"),
            'onChange': function (color) {
                $('sitecoretheme_theme_color').value = color.hex;
                if (color.hex == '#444444') {
                    $('sitecoretheme_theme_color').setStyles({'background-color': color.hex, 'color': '#ffffff'});
                } else {
                    $('sitecoretheme_theme_color').setStyles({'background-color': color.hex});
                }
            }
        });
    });
</script>

<?php
$themeColor = $coreSettings->getSetting('sitecoretheme.theme.color', '#44bbff');
$textColor = '#444444';
if ($themeColor == '#444444') {
    $textColor = '#ffffff';
}
echo '
	<div id="sitecoretheme_theme_color-wrapper" class="form-wrapper">
		<div id="sitecoretheme_theme_color-label" class="form-label">
			<label for="sitecoretheme_theme_color" class="optional">
				' . $this->translate('Theme Color') . '
			</label>
		</div>
		<div id="sitecoretheme_theme_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the theme color for your site. (Click on the rainbow below to choose your color.)') . '</p>
			<input style="color:' . $textColor . ';background-color:' . $themeColor . '" name="sitecoretheme_theme_color" id="sitecoretheme_theme_color" value=' . $themeColor . ' type="text">
			<input name="myRainbow4" id="myRainbow4" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/rainbow.png" link="true" type="image">  
                        <a style="margin-bottom:12px;" target="_blank" href="application/modules/Sitecoretheme/externals/images/screenshots/theme-color.png" class="buttonlink sitecoretheme_icon_view mleft5"></a>
		</div>
	</div>
'
?>