<?php
/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Member Avatars Plugin') ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<?php
include_once APPLICATION_PATH .
        '/application/modules/Siteuseravatar/views/scripts/admin-settings/faq_help.tpl';
?>
