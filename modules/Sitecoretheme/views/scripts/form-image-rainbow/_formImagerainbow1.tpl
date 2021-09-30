<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow1.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$gradient_color_first = '' ;
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecoretheme/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/styles/mooRainbow.css');
?>	
<script type="text/javascript">
    window.addEvent('domready', function() { 
        var r = new MooRainbow('myRainbow1', { 
            id: 'myDemo1',
            'startColor': ($('gradient_color_first').value).hexToRgb(),
            'onChange': function(color) { 
                $('gradient_color_first').value = color.hex;
            }
        });
    });	
</script>
<?php echo 
'<div id="gradient_color_first-wrapper" class="form-wrapper">
		<div id="gradient_color_first-label" class="form-label">
			<label for="gradient_color_first" class="optional">Select first gradient color (Click on the rainbow below to choose your color.)</label>
		</div>
		<div id="gradient_color_first-element" class="form-element">
			<input name="gradient_color_first" id="gradient_color_first" value="' .$gradient_color_first. '" type="text">
			<input name="myRainbow1" id="myRainbow1" src="' .$this->layout()->staticBaseUrl. 'application/modules/Sitecoretheme/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>';