<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteuseravatar
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formImagerainbowFeatured.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
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
$backgroundColor = !empty($_POST['backgroundColor']) ? $_POST['backgroundColor']:  $settings->getSetting('siteuseravatar.backgroundColor', '#30a7ff');
?>
<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
    var hexcolorAlphabets = "0123456789ABCDEF";
    var valueNumber = new Array(3);
    var j = 0;
    if (hexcolor.charAt(0) == "#")
      hexcolor = hexcolor.slice(1);
    hexcolor = hexcolor.toUpperCase();
    for (var i = 0; i < 6; i += 2) {
      valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i + 1));
      j++;
    }
    return(valueNumber);
  }

  window.addEvent('domready', function () {

    var r = new MooRainbow('myRainbow2', {

      id: 'myDemo1',
      'startColor': hexcolorTonumbercolor("<?php echo $backgroundColor ?>"),
      'onChange': function (color) {
        $('backgroundColor').value = color.hex;
      }
    });
    useBKColor($('enableBackgroundColor-1').get('checked') ? 1 : 0);
  });
</script>

<?php
echo '
	<div id="backgroundColor-wrapper" class="form-wrapper">
		<div id="backgroundColor-label" class="form-label">
			<label for="backgroundColor" class="optional">
				' . $this->translate('Background Color') . '
			</label>
		</div>
		<div id="backgroundColor-element" class="form-element">
			<p class="description">' . $this->translate('Select the color to be show in background of the avatar initials. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="backgroundColor" id="backgroundColor" value=' . $backgroundColor . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="' . $this->layout()->staticBaseUrl . 'application/modules/Siteuseravatar/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
  function useBKColor(option) {
    if (option == 1) {
      $('backgroundColor-wrapper').style.display = 'block';
      $('fontColor-wrapper').style.display = 'block';
    } else {
      $('backgroundColor-wrapper').style.display = 'none';
      $('fontColor-wrapper').style.display = 'none';
    }
  }

</script>