<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfundingintegration
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 6590 2015-1-22 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function setModuleName(item_module, type) {
        window.location.href = "<?php echo $this->url(array('module' => 'sitecrowdfundingintegration', 'controller' => 'modules', 'action' => 'add'), 'admin_default', true) ?>/item_module/" + item_module;
    }
</script>

<h2>
    <?php echo $this->translate("Crowdfunding / Fundraising / Donations Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfundingintegration', 'controller' => 'modules'), $this->translate("Back to Manage Modules"), array('class' => 'seaocore_icon_back buttonlink')) ?>
<br style="clear:both;" /><br />

<div class='settings'>
    <?php echo $this->form->render($this); ?>
</div>