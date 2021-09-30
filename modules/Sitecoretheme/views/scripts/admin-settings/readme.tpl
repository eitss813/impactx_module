<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2>
    <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>
<div class="seaocore_admin_tabs tabs">
    <ul class="navigation">
        <li class="active">
            <a href="<?php echo $this->baseUrl() . '/admin/sitecoretheme/settings/readme' ?>" ><?php echo 'Please go through these important points and proceed by clicking the button at the bottom of this page.'; ?></a>

        </li>
    </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecoretheme/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo 'Proceed'; ?> </button>

<script type="text/javascript" >
    function form_submit() {
        var url = '<?php echo $this->url(array('module' => 'sitecoretheme', 'controller' => 'settings'), 'admin_default', true) ?>';
        window.location.href = url;
    }
</script>