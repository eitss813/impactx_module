<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowTitle.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() { 
    var s = new MooRainbow('myRainbow2', { 
      id: 'myDemo2',
      'startColor': [58, 142, 246],
      'onChange': function(color) {
        $('sitepage_title_color').value = color.hex;
      }
    });
  });
</script>

<?php

echo '
 	<div id="sitepage_title_color-wrapper" class="form-wrapper">
 		<div id="sitepage_title_color-label" class="form-label">
 			<label for="sitepage_title_color" class="optional">
 				' . $this->translate('Email Template Header Title Text Color') . '
 			</label>
 		</div>
 		<div id="sitepage_title_color-element" class="form-element">
 			<p class="description">' . $this->translate('Select the color of the header title text of email template. (Click on the rainbow below to choose your color.)') . '</p>
 			<input name="sitepage_title_color" id="sitepage_title_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.color', '#ffffff') . ' type="text">
 			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/rainbow.png" link="true" type="image">
 		</div>
 	</div>
 '
?>