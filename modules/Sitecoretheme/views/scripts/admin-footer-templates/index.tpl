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
<h2><?php echo SITECORETHEME_PLUGIN_NAME ?></h2>

<?php if (count($this->navigation)): ?>
	<div  class='seaocore_admin_tabs tabs clr'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>

	<div  class='seaocore_sub_tabs tabs clr'>
		<?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render() ?>
	</div>
<?php endif; ?>
<?php
$this->form->setDescription("Here, you can manage footer templates.");
$this->form->getDecorator('Description')->setOption('escape', false);
?>
<div class='seaocore_settings_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>
<?php
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$verticalFooterBackground = $coreSettings->getSetting('sitecoretheme.footer.background', 2);
$verticalFooterShowLogo = $coreSettings->getSetting('sitecoretheme.footer.show.logo', 1);
$verticalTwitterFeed = $coreSettings->getSetting('sitecoretheme.twitter.feed', 0);

$showVerticalFooterTemplate = $coreSettings->getSetting('sitecoretheme.footer.templates', 2);
$localeMultiOptions = Engine_Api::_()->sitecoretheme()->getLanguageArray();
$total_allowed_languages = Count($localeMultiOptions);
?>
<style type="text/css">
	.dnone {
		display: none !important;
	}
</style>
<script type="text/javascript">
  function showFooterBackgroundImage(val) {

    if (val == 1) {
      $('sitecoretheme_footer_backgroundimage-wrapper').style.display = 'none';
    } else {
      $('sitecoretheme_footer_backgroundimage-wrapper').style.display = 'block';
    }

  }
  function showFooterLogo(val) {
    if (val == 1) {
      $('sitecoretheme_footer_select_logo-wrapper').style.display = 'block';
    } else {
      $('sitecoretheme_footer_select_logo-wrapper').style.display = 'none';
    }
  }

  function showTwitterFeed(val) {
    if (val == 1) {
      $('sitecoretheme_twitterCode-wrapper').show();
			$('sitecoretheme_fotter_content_heading-wrapper').hide();
			$('sitecoretheme_fotter_content_item-wrapper').hide();
			$('sitecoretheme_fotter_content_viewType-wrapper').hide();
			$('sitecoretheme_fotter_content_limit-wrapper').hide();
			$('sitecoretheme_fotter_content_sort-wrapper').hide();
    } else {
      $('sitecoretheme_twitterCode-wrapper').hide();
		  $('sitecoretheme_fotter_content_heading-wrapper').show();
			$('sitecoretheme_fotter_content_item-wrapper').show();
			$('sitecoretheme_fotter_content_viewType-wrapper').show();
			$('sitecoretheme_fotter_content_limit-wrapper').show();
			$('sitecoretheme_fotter_content_sort-wrapper').show();
    }
  }

  function displayFooterHtmlBlock(val) {
    var style = val == 4 ? 'block' : 'none';
<?php
if (!empty($localeMultiOptions)) {
	foreach ($localeMultiOptions as $key => $label) {
		?>
		    if ($("sitecoretheme_footer_lending_page_block_<?php echo $key; ?>-wrapper"))
		      $("sitecoretheme_footer_lending_page_block_<?php echo $key; ?>-wrapper").style.display = style;
		<?php
	}
}
?>
		  $('sitecoretheme_twitterCode-wrapper').addClass('dnone');
      $('sitecoretheme_twitter_feed-wrapper').addClass('dnone');
      $('sitecoretheme_mobile-wrapper').addClass('dnone');
      $('sitecoretheme_mail-wrapper').addClass('dnone');
      $('sitecoretheme_website-wrapper').addClass('dnone');
			
			$('sitecoretheme_fotter_content_heading-wrapper').addClass('dnone');
      $('sitecoretheme_fotter_content_item-wrapper').addClass('dnone');
      $('sitecoretheme_fotter_content_viewType-wrapper').addClass('dnone');
      $('sitecoretheme_fotter_content_sort-wrapper').addClass('dnone');
			$('sitecoretheme_fotter_content_limit-wrapper').addClass('dnone');
			$$('.sitecoretheme_footer_exp_colps').addClass('dnone');
    if (val == 4) {
      $('sitecoretheme_twitterCode-wrapper').removeClass('dnone');
      $('sitecoretheme_twitter_feed-wrapper').removeClass('dnone');
      $('sitecoretheme_mobile-wrapper').removeClass('dnone');
      $('sitecoretheme_mail-wrapper').removeClass('dnone');
      $('sitecoretheme_website-wrapper').removeClass('dnone');
      $('sitecoretheme_footer_show_logo-wrapper').removeClass('dnone');
      $('sitecoretheme_footer_select_logo-wrapper').removeClass('dnone');
			$('sitecoretheme_fotter_content_heading-wrapper').removeClass('dnone');
      $('sitecoretheme_fotter_content_item-wrapper').removeClass('dnone');
      $('sitecoretheme_fotter_content_viewType-wrapper').removeClass('dnone');
      $('sitecoretheme_fotter_content_sort-wrapper').removeClass('dnone');
			$('sitecoretheme_fotter_content_limit-wrapper').removeClass('dnone');
			$$('.sitecoretheme_footer_exp_colps').removeClass('dnone');
			showTwitterFeed($('sitecoretheme_twitter_feed-1').checked ? 1:0);
			
    } else if (val == 3) {
      $('sitecoretheme_footer_show_logo-wrapper').removeClass('dnone');
      $('sitecoretheme_footer_select_logo-wrapper').removeClass('dnone');
    } else {
      $('sitecoretheme_footer_show_logo-wrapper').addClass('dnone');
      $('sitecoretheme_footer_select_logo-wrapper').addClass('dnone');
    }

  }
  showFooterBackgroundImage('<?php echo $verticalFooterBackground; ?>');
  showFooterLogo('<?php echo $verticalFooterShowLogo; ?>');
  displayFooterHtmlBlock('<?php echo $showVerticalFooterTemplate; ?>');
  showTwitterFeed('<?php echo $verticalTwitterFeed; ?>');
</script>