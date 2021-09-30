<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo 'Advanced Payment Gateways / Stripe Connect Plugin' ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<?php
include_once APPLICATION_PATH .
        '/application/modules/Sitegateway/views/scripts/admin-settings/faq_help.tpl';
?>
