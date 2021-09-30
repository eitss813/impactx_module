<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Advanced Videos / Channels / Playlists Plugin') ?></h2>
<div class="seaocore_admin_tabs clr">
    <ul class="navigation">
        <li class="active">
            <a href="<?php echo $this->baseUrl() . '/admin/sitevideo/settings/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>

        </li>
    </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Continue') ?> </button>

<script type="text/javascript" >

    function form_submit() {

        var url = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'settings', 'action' => 'index'), 'admin_default', true) ?>';
        window.location.href = url;
    }

</script>