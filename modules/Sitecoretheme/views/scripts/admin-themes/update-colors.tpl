<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: update-colors.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2><?php echo $this->translate(SITECORETHEME_PLUGIN_NAME) ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs tabs clr'>
		<?php
		// Render the menu
		//->setUlClass()
		echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	</div>
<?php endif; ?>
<div class='seaocore_sub_tabs tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
</div>

<div class='clear'>
  <div class='settings'>

		<?php echo $this->form->render($this); ?>

  </div>
</div>
<script type="text/javascript">
  function spwThemeUpdateMethod(showElement) {
    var hideElement = showElement == 'group' ? 'single' : 'group';
    $$('.constant_color_' + hideElement + '_element').hide();
    $$('.constant_color_' + showElement + '_element').show();
    
    if(showElement == 'group') {
      $('sitecoretheme_header_constants-wrapper').hide();
      $('sitecoretheme_footer_constants-wrapper').hide();
      $('sitecoretheme_body_constants-wrapper').hide();
      $$('.sitecoretheme_constant_color_group_element').show();
    } else {
      $('sitecoretheme_header_constants-wrapper').show();
      $('sitecoretheme_footer_constants-wrapper').show();
      $('sitecoretheme_body_constants-wrapper').show();
      $$('.sitecoretheme_constant_color_group_element').hide();
    }
  }
  spwThemeUpdateMethod($('sitecoretheme_update_method').value);
</script>