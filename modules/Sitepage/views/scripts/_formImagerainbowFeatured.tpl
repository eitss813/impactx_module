<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowFeatured.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
    var hexcolorAlphabets = "0123456789ABCDEF";
    var valueNumber = new Array(3);
    var j = 0;
    if(hexcolor.charAt(0) == "#")
      hexcolor = hexcolor.slice(1);
    hexcolor = hexcolor.toUpperCase();
    for(var i=0;i<6;i+=2) {
      valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
      j++;
    }
    return(valueNumber);
  }

  window.addEvent('domready', function() {

    var r = new MooRainbow('myRainbow1', {
    
      id: 'myDemo1',
      'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523') ?>"),
      'onChange': function(color) {
        $('sitepage_featured_color').value = color.hex;
      }
    });
    showfeatured("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.image', 1) ?>")
  });	
</script>

<?php
echo '
	<div id="sitepage_featured_color-wrapper" class="form-wrapper">
		<div id="sitepage_featured_color-label" class="form-label">
			<label for="sitepage_featured_color" class="optional">
				' . $this->translate('Featured Label Color') . '
			</label>
		</div>
		<div id="sitepage_featured_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the "FEATURED" labels. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="sitepage_featured_color" id="sitepage_featured_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0CF523') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>

<script type="text/javascript">
  function showfeatured(option) {
    if(option == 1) {
      $('sitepage_featured_color-wrapper').style.display = 'block';
    }
    else {
      $('sitepage_featured_color-wrapper').style.display = 'none';
    }
  }
</script>