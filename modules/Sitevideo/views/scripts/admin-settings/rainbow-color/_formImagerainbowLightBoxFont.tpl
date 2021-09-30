<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add-special-channels.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
     var s = new MooRainbow('myRainbow2', {
      id: 'myDemo2',
      'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.fontcolor', '#FFFFFF') ?>"),
      'onChange': function(color) {
        $('sitevideo_lightbox_fontcolor').value = color.hex;
      }
    });
			
  });
</script>

<?php
echo '
	<div id="sitevideo_lightbox_fontcolor-wrapper" class="form-wrapper">
		<div id="sitevideo_lightbox_fontcolor-label" class="form-label">
			<label for="sitevideo_lightbox_fontcolor" class="optional">
				' . $this->translate('Font Color of Statistics') . '
			</label>
		</div>
		<div id="sitevideo_lightbox_fontcolor-element" class="form-element">
			<p class="description">' . $this->translate('Select a color for the statistics (Likes, Views and Comments) to be shown in the Video Lightbox viewer. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitevideo_lightbox_fontcolor" id="sitevideo_lightbox_fontcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lightbox.fontcolor', '#FFFFFF') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="'. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
