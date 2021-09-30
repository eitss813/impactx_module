<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: video-banner.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
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
  <span>
     To set up this section place <?php echo SITECORETHEME_PLUGIN_NAME ?> - Video Banner widget via layout editor.
  </span>
</div>
<div class='seaocore_settings_form'>
  <br>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  var form = document.getElementById("form-upload");

  function showVideoUrl() {
    if (form.elements["sitecoretheme_landing_videobanner_videoType"].value == 1) {
      $('sitecoretheme_landing_videobanner_videoEmbed-wrapper').style.display = 'none';
      $('sitecoretheme_landing_videobanner_videoUrl-wrapper').style.display = 'block';
    } else {
      $('sitecoretheme_landing_videobanner_videoEmbed-wrapper').style.display = 'block';
      $('sitecoretheme_landing_videobanner_videoUrl-wrapper').style.display = 'none';
    }
  }

  showVideoUrl();

</script>