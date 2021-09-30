<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formColor.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/styles/mooRainbow.css');
?>
<script type="text/javascript">
  window.addEvent('domready', function () {
    var theme_color_s = new MooRainbow('<?php echo $this->name ?>', {
      id: '<?php echo $this->name ?>',
      'startColor': hexcolorTonumbercolor("<?php echo $this->value ?>"),
      'onChange': function (color) {
        $('<?php echo $this->name ?>').value = color.hex;
        $('<?php echo $this->name ?>').setStyle('backgroundColor', color.hex);
        theme_color_s.okButton.click();
      }
    });
  });
</script>


<div id="<?php echo $this->name ?>-wrapper" class="form-wrapper <?php echo $this->class ?>" style="<?php echo $this->style ?>">
	<div id="<?php echo $this->name ?>-label" class="form-label">
		<label for="<?php echo $this->name ?>" class="optional">
			<?php echo $this->label ?>
		</label>
	</div>
	<div id="<?php echo $this->name ?>-element" class="form-element">
		<p class="description"><?php echo $this->description ?></p>
		<input name="<?php echo $this->name ?>" id="<?php echo $this->name ?>" style="background-color:<?php echo $this->value ?>" value='<?php echo $this->value ?>' type="text">
	</div>
</div>