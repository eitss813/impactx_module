<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo_dashboard.css');
?>

<?php if ($this->can_edit): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_DashboardNavigation.tpl'; ?>
<?php else: ?>
    <div class="sitevideo_view_top">
        <?php echo $this->htmlLink($this->channel->getHref(), $this->itemPhoto($this->channel, 'thumb.icon', '', array('align' => 'left'))) ?>
        <p>	
            <?php echo $this->channel->__toString() ?>	
            <?php echo $this->translate('&raquo; '); ?>
            <?php echo $this->htmlLink($this->channel->getHref(array('tab' => $this->tab_id)), $this->translate('Photos')) ?>
        </p>
    </div>
    <?php endif; ?>

    <div class="sitevideo_dashboard_content">
        <?php if ($this->can_edit): ?>
            <?php echo $this->partial('application/modules/Sitevideo/views/scripts/dashboard/header.tpl', array('channel' => $this->channel)); ?>
        <?php endif; ?>
        <?php echo $this->form->render($this) ?>

</div>
<?php if ($this->can_edit): ?>
    </div>
<?php endif; ?>