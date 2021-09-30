<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2017-05-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<h2>
    <?php echo 'Crowdfunding / Fundraising / Donations Plugin'; ?>
</h2>

<div class='seaocore_admin_tabs clr'>
    <ul class="navigation">
        <li class="active">
            <a href="<?php echo $this->baseUrl() . '/admin/Sitecrowdfunding/settings/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
        </li>
    </ul>
</div>		

<?php
include_once APPLICATION_PATH .
        '/application/modules/Sitecrowdfunding/views/scripts/admin-settings/faq_help.tpl';
?>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>

<script type="text/javascript" >
    function form_submit() {

        var url = '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'settings'), 'admin_default', true) ?>';
        window.location.href = url;
    }
</script>