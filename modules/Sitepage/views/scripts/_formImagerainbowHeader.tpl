<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowHeader.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/scripts/mooRainbow.js" type="text/javascript"></script>

<?php

$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitepage/externals/styles/mooRainbow.css');
?> 

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var r = new MooRainbow('myRainbow1', { 
      id: 'myDemo1',
      'startColor': [58, 142, 246],
      'onChange': function(color) {
        $('sitepage_header_color').value = color.hex;
      }
    });
  });	
</script>

<?php

echo '
<div id="sitepage_header_color-wrapper" class="form-wrapper">
	<div id="sitepage_header_color-label" class="form-label">
		<label for="sitepage_header_color" class="optional">
			' . $this->translate('Email Template Header Background Color') . '
		</label>
	</div>
	<div id="sitepage_header_color-element" class="form-element">
		<p class="description">' . $this->translate('Select the color of the header background of email template. (Click on the rainbow below to choose your color.)') . '</p>
		<input name="sitepage_header_color" id="sitepage_header_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.header.color', '#79b4d4') . ' type="text">
		<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/rainbow.png" link="true" type="image">
	</div>
</div>
'
?>