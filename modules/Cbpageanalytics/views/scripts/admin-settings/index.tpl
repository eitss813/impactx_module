<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */
?>

<h2>
    <?php echo $this->translate('CB - Page Analytics Plugin') ?>
</h2>

<p>
<?php echo $this->translate("Here you can manage global setting for your plugin, based on these setting Page Analytics will track the page visits. You can enable/disable page tracking by your plugin. If you need more help please contact at <a href='mailto:support@consecutivebytes.com'>support@consecutivebytes.com</a> or create a ticket from your user panel on our <a href='http://www.consecutivebytes.com' target='_blank'>website</a>.") ?>
</p>
<br><br>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
