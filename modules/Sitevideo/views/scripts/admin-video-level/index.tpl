<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?></h2>

<script type="text/javascript">
    var fetchLevelSettings = function (level_id) {
        var url = '<?php echo $this->url(array('id' => null)) ?>';
        window.location.href = url + '/index/id/' + level_id;
    }
</script>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>
<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs clr'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render(); ?>
    </div>
<?php endif; ?>
<div class="clear seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>