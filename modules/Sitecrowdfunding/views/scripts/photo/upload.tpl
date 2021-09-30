<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css');
?>

<?php if ($this->can_edit): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<?php else: ?>
    <div class="sitecrowdfunding_view_top">
        <?php echo $this->htmlLink($this->project->getHref(), $this->itemPhoto($this->project, 'thumb.icon', '', array('align' => 'left'))) ?>
        <p>	
            <?php echo $this->translate($this->project->__toString()) ?>	
            <?php echo $this->translate('&raquo; '); ?>
            <?php echo $this->htmlLink($this->project->getHref(array('tab' => $this->tab_id)), $this->translate('Photos')) ?>
        </p>
    </div>
<?php endif; ?>

<div class="sitecrowdfunding_dashboard_content">
    <?php if ($this->can_edit): ?>
        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project)); ?>
    <?php endif; ?>
    <?php echo $this->form->render($this) ?>
</div>
<?php if ($this->can_edit): ?>
    </div>
<?php endif; ?>