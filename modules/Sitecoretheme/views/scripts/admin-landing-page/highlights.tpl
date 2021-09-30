<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: highlights.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
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
<div class="tip">
    <span><?php echo $this->translate("To set up this section place ".SITECORETHEME_PLUGIN_NAME." - Highlights Block widget on your landing page via layout editor.") ?></span>
</div>
<?php
  $settingsUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'highlights'), 'admin_default', false);
  $editUrl = $this->url(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'list-highlights'), 'admin_default', false);
?>
<div class='tabs seaocore_sub_tabs'>
  <ul class="navigation">    
    <li  class="<?php echo ($this->selectedMenuType == 'view')? 'active': ''; ?>">
      <a href="<?php echo $settingsUrl; ?>"><?php echo $this->translate("Settings"); ?></a>
    </li>
    <li  class="<?php echo ($this->selectedMenuType == 'edit')? 'active': ''; ?>">
      <a href="<?php echo $editUrl; ?>"><?php echo $this->translate("Manage Highlights Block"); ?></a>
    </li>
  </ul>
</div>

<div class='seaocore_settings_form'>
  <br>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  var form = document.getElementById("form-upload");
  window.addEvent('domready', function () {
    showVideoUrl();
  });


  function showVideoUrl() {
    if (form.elements["sitecoretheme_landing_highlights_attachVideo"].value == 1) {
      $('sitecoretheme_landing_highlights_videoEmbed-wrapper').style.display = 'block';
    } else {
      $('sitecoretheme_landing_highlights_videoEmbed-wrapper').style.display = 'none';
    }
  }

</script>