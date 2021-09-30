<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: email.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
 	var sitemailtemplates = '<?php echo $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>';
  window.addEvent('domready', function() { 
    var e1 = $('sitepage_insightemail-1');
    var e2 = $('sitepage_demo');
    $('sitepage_insightmail_options-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    if(sitemailtemplates == 0) {
			$('sitepage_header_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitepage_bg_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitepage_title_color-wrapper').setStyle('display', (e1.checked ?'block':'none'));
			$('sitepage_site_title-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    }
    $('sitepage_demo-wrapper').setStyle('display', (e1.checked ?'block':'none'));
    $('sitepage_admin-wrapper').setStyle('display', (e2.checked && e1.checked ?'block':'none'));
 
 
 	  
    $('sitepage_insightemail-0').addEvent('click', function(){
      $('sitepage_insightmail_options-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      if(sitemailtemplates == 0) {
				$('sitepage_header_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitepage_bg_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitepage_title_color-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
				$('sitepage_site_title-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      }
      $('sitepage_demo-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
      $('sitepage_admin-wrapper').setStyle('display', ($(this).checked ?'none':'block'));
    });
 
    $('sitepage_insightemail-1').addEvent('click', function(){
      $('sitepage_insightmail_options-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      if(sitemailtemplates == 0) {
				$('sitepage_header_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitepage_bg_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitepage_title_color-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
				$('sitepage_site_title-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      }
      $('sitepage_demo-wrapper').setStyle('display', ($(this).checked ?'block':'none'));
      $('sitepage_admin-wrapper').setStyle('display', (e2.checked && $(this).checked ?'block':'none'));
    });
       
    $('sitepage_demo').addEvent('click', function(){
      $('sitepage_admin-wrapper').setStyle('display', ($(this).checked && e1.checked ?'block':'none'));
    });
  });
</script>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
