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
<br/>
<?php $url = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'slider'), 'admin_default', true); ?>
<!--<div class="tip">
  <span><?php //echo $this->translate("If you want to add or modify the logo of Landing Page, please <a href='$url'>click here</a>.") ?></span>
</div>-->
<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">
  var headerStyleBaseOptions = function () {
		$('sitecoretheme_header_menu_fixed-wrapper').show();
		if($('sitecoretheme_header_sitemenu_fixed-wrapper'))
			$('sitecoretheme_header_sitemenu_fixed-wrapper').hide();
		if ($('sitecoretheme_header_menu_submenu-wrapper'))
        $('sitecoretheme_header_menu_submenu-wrapper').hide();
			
		if ($('sitecoretheme_header_style-3').checked) {      
			if($('sitecoretheme_header_sitemenu_fixed-wrapper'))
				$('sitecoretheme_header_sitemenu_fixed-wrapper').show();
			
			$('sitecoretheme_header_menu_fixed-wrapper').hide();
    }else if ($('sitecoretheme_header_style-2').checked) {      
      if ($('sitecoretheme_header_menu_submenu-wrapper'))
        $('sitecoretheme_header_menu_submenu-wrapper').show();
    } else {
      
			
    }
  };
  var setTheMainMenuOptions = function (options) {
    if (options == 2) {
      $('sitecoretheme_header_style-wrapper').hide();
      $('sitecoretheme_header_menu_fixed-wrapper').hide();
      if ($('sitecoretheme_header_desktop_totalmenuwith_dots-wrapper'))
        $('sitecoretheme_header_desktop_totalmenuwith_dots-wrapper').hide();
      if ($('sitecoretheme_header_desktop_totalmenu-wrapper'))
        $('sitecoretheme_header_desktop_totalmenu-wrapper').hide();
			if($('sitecoretheme_header_sitemenu_fixed-wrapper'))
				$('sitecoretheme_header_sitemenu_fixed-wrapper').hide();
			
      $('sitecoretheme_header_menu_style-wrapper').show();
      $('sitecoretheme_header_menu_alwaysOpen-wrapper').show();
      if ($('sitecoretheme_header_menu_submenu-wrapper'))
        $('sitecoretheme_header_menu_submenu-wrapper').show();
      if ($('sitecoretheme_header_vmenu_icon-wrapper'))
        $('sitecoretheme_header_vmenu_icon-wrapper').show();
    } else {
      $('sitecoretheme_header_menu_style-wrapper').hide();
      $('sitecoretheme_header_menu_alwaysOpen-wrapper').hide();
      if ($('sitecoretheme_header_vmenu_icon-wrapper'))
        $('sitecoretheme_header_vmenu_icon-wrapper').hide();
			if($('sitecoretheme_header_sitemenu_fixed-wrapper'))
				$('sitecoretheme_header_sitemenu_fixed-wrapper').show();
      if ($('sitecoretheme_header_desktop_totalmenu-wrapper'))
        $('sitecoretheme_header_desktop_totalmenu-wrapper').show();
      $('sitecoretheme_header_menu_fixed-wrapper').show();
      $('sitecoretheme_header_style-wrapper').show();
      if ($('sitecoretheme_header_desktop_totalmenuwith_dots-wrapper'))
        $('sitecoretheme_header_desktop_totalmenuwith_dots-wrapper').show();
      headerStyleBaseOptions();
    }
  }
  setTheMainMenuOptions(<?php echo $this->settings('sitecoretheme.header.menu.position', 1) ?>);
	
	function sitemenuWidgetOptions(options) {
		if(!$('sitecoretheme_header_siteminimenu_enable-wrapper')) {
			return;
		}
		if ($('sitecoretheme_header_siteminimenu_enable-1').checked) {
			if($('sitecoretheme_header_display_cart-wrapper'))
			  $('sitecoretheme_header_display_cart-wrapper').show();
		}else {
			if($('sitecoretheme_header_display_cart-wrapper'))
			  $('sitecoretheme_header_display_cart-wrapper').hide();
		}
	}
	sitemenuWidgetOptions(<?php echo $this->settings('sitecoretheme.header.siteminimenu.enable', 1) ?>);
</script>
<style type="text/css">
	#sitecoretheme_header_minimenu_label-label,
  #sitecoretheme_header_header_label-label {
    display: block;
    width: 100%;
    font-size: 20px;
  }
  </style>