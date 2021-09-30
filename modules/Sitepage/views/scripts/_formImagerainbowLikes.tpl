<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowLikes.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/mooRainbow.css');
?>

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var s = new MooRainbow('myRainbow2', { 
      id: 'myDemo2',
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.graphlike.color', '#CD6839') ?>"),
      'onChange': function(color) {
        $('sitepage_graphlike_color').value = color.hex;
      }
    });
  });
</script>

<?php
echo '
 	<div id="sitepage_graphlike_color-wrapper" class="form-wrapper">
 		<div id="sitepage_graphlike_color-label" class="form-label">
 			<label for="sitepage_graphlike_color" class="optional">
 				' . $this->translate('Likes Line Color') . '
 			</label>
 		</div>
 		<div id="sitepage_graphlike_color-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the line which is used to represent Likes in the graph. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="sitepage_graphlike_color" id="sitepage_graphlike_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.graphlike.color', '#CD6839') . ' type="text">
 			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>