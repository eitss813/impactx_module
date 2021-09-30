<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<h2><?php echo $this->translate("Advanced Photo Albums Plugin") ?></h2>

<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    var url = '<?php echo $this->url(array('id' => null)) ?>';
    window.location.href = url + '/index/id/' + level_id;
  }
</script>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<div class="clear seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>