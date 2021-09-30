<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowFundedCircle.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<?php $fundedcirclecolor = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.fundedcirclecolor', '#FC0505'); ?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        var s = new MooRainbow('myRainbow4', {
            id: 'myDemo4',
            'startColor': hexcolorTonumbercolor("<?php echo $fundedcirclecolor ?>"),
            'onChange': function(color) {
                $('sitecrowdfunding_fundedcirclecolor').value = color.hex;
            }
        });
    });
</script>

<?php
echo '
	<div id="fundedcirclecolor-wrapper" class="form-wrapper">
		<div id="fundedcirclecolor-label" class="form-label">
			<label for="fundedcirclecolor" class="optional">
				' . $this->translate('Funded Ratio Circle Fill Color') . '
			</label>
		</div>
		<div id="fundedcirclecolor-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the Funded ratio circle on project profile page. (Click on the rainbow below to choose your color.) [Note: ‘Funded Ratio Circle’ is visible only when ‘Content Cover Photo and Information’ widget is placed on Project Profile page.]') . '</p>
			<input name="sitecrowdfunding_fundedcirclecolor" id="sitecrowdfunding_fundedcirclecolor" value=' . $fundedcirclecolor . ' type="text">
			<input name="myRainbow4" id="myRainbow4" src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
