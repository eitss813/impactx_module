<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowSponsred.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<?php $featured_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828'); ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        var s = new MooRainbow('myRainbow3', {
            id: 'myDemo3',
            'startColor': hexcolorTonumbercolor("<?php echo $featured_color ?>"),
            'onChange': function(color) {
                $('sitecrowdfunding_featuredcolor').value = color.hex;
            }
        });
    });
</script>

<?php
echo '
	<div id="featured_color-wrapper" class="form-wrapper">
		<div id="featured_color-label" class="form-label">
			<label for="featured_color" class="optional">
				' . $this->translate('Featured Label Color') . '
			</label>
		</div>
		<div id="featured_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the "FEATURED" label. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitecrowdfunding_featuredcolor" id="sitecrowdfunding_featuredcolor" value=' . $featured_color . ' type="text">
			<input name="myRainbow3" id="myRainbow3" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
