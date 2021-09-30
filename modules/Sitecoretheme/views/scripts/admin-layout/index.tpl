<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>

<div class='seaocore_admin_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<div class='seaocore_sub_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
</div>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<?php $iconSet = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.layout.container.headding.style', 1); ?>
<script type="text/javascript">
	en4.core.runonce.add(function(){
		var option = '<?php echo $iconSet; ?>';
		showOption(option); 
	});
	function showOption(value) {
		if(value == 5) {
			$('sitecoretheme_landing_heading_icon-wrapper').style.display = 'block';
		}
		else {
			$('sitecoretheme_landing_heading_icon-wrapper').style.display = 'none';
		}
	}
</script>