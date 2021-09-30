<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo 'Static Pages, HTML Blocks and Multiple Forms Plugin'; ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
	<?php
	// Render the menu
	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	?>
</div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>

<script type="text/javascript">
	if($('sitestaticpage_multilanguage-1')) {
		$('sitestaticpage_multilanguage-1').addEvent('click', function(){
				$('sitestaticpage_languages-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
		});
		$('sitestaticpage_multilanguage-0').addEvent('click', function(){
				$('sitestaticpage_languages-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
		});
		window.addEvent('domready', function() {
			$('sitestaticpage_languages-wrapper').setStyle('display', ($('sitestaticpage_multilanguage-1').checked ?'block':'none'));
		});
	}
</script>