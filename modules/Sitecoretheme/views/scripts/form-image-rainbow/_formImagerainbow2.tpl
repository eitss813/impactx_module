<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbow2.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$gradient_color_second = '';
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemenu/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/styles/mooRainbow.css');
?>	
<script type="text/javascript">
    window.addEvent('domready', function() { 
        var r = new MooRainbow('myRainbow2', { 
            id: 'myDemo2',
            'startColor': ($('gradient_color_second').value).hexToRgb(),
            'onChange': function(color) { 
                $('gradient_color_second').value = color.hex;
            }
        });
    });	
</script>
<?php echo 
'<div id="gradient_color_second-wrapper" class="form-wrapper">
		<div id="gradient_color_second-label" class="form-label">
			<label for="gradient_color_second" class="optional">Select second gradient color (Click on the rainbow below to choose your color.)</label>
		</div>
		<div id="gradient_color_second-element" class="form-element">
			<input name="gradient_color_second" id="gradient_color_second" value="' .$gradient_color_second. '" type="text">
			<input name="myRainbow2" id="myRainbow2" src="' .$this->layout()->staticBaseUrl. 'application/modules/Sitecoretheme/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>';