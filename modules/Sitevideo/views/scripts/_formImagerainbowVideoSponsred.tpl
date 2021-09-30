<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowSponsred.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<?php $sponsored_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.sponsoredcolor', '#FC0505'); ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        var s = new MooRainbow('myRainbow2', {
            id: 'myDemo2',
            'startColor': hexcolorTonumbercolor("<?php echo $sponsored_color ?>"),
            'onChange': function(color) {
                $('sitevideo_video_sponsoredcolor').value = color.hex;
            }
        });
    });
</script>

<?php
echo '
	<div id="sponsored_color-wrapper" class="form-wrapper">
		<div id="sponsored_color-label" class="form-label">
			<label for="sponsored_color" class="optional">
				' . $this->translate('Sponsored Label Color') . '
			</label>
		</div>
		<div id="sponsored_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the "SPONSORED" labels. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitevideo_video_sponsoredcolor" id="sitevideo_video_sponsoredcolor" value=' . $sponsored_color . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
