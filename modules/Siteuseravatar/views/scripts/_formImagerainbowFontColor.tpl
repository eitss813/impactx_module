<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteuseravatar
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImageraionbowSponsored.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteuseravatar/externals/scripts/mooRainbow.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteuseravatar/externals/styles/mooRainbow.css');
?>

<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
$fontColor = !empty($_POST['fontColor']) ? $_POST['fontColor']: $settings->getSetting('siteuseravatar.fontColor', '#FFFFFF');
?>

<script type="text/javascript">
  window.addEvent('domready', function () {
    var s = new MooRainbow('myRainbow1', {
      id: 'myDemo2',
      'startColor': hexcolorTonumbercolor("<?php echo $fontColor ?>"),
      'onChange': function (color) {
        $('fontColor').value = color.hex;
      }
    });

  });
</script>

<?php
echo '
	<div id="fontColor-wrapper" class="form-wrapper">
		<div id="fontColor-label" class="form-label">
			<label for="fontColor" class="optional">
				' . $this->translate('Foreground Color') . '
			</label>
		</div>
		<div id="fontColor-element" class="form-element">
			<p class="description">' . $this->translate('Select the color in which you want to show the avatar initials. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="fontColor" id="fontColor" value=' . $fontColor . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteuseravatar/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>
