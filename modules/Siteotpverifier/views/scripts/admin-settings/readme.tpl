<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    readme.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo "One Time Password (OTP) Plugin"; ?>
</h2>
<div class="siteotpverifier_admin_tabs">
    <ul class="navigation">
        <li class="active">
            <a href="<?php echo $this->baseUrl() . '/admin/siteotpverifier/settings/readme' ?>" ><?php echo 'Please go through these important points and proceed by clicking the button at the bottom of this page.'; ?></a>

        </li>
    </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Siteotpverifier/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo 'Proceed to enter License Key'; ?> </button>

<script type="text/javascript" >
    function form_submit() {
        var url = '<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'settings'), 'admin_default', true) ?>';
        window.location.href = url;
    }
</script>