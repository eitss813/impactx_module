<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div>
    <?php $days = $this->days; ?>
    <?php $daysToStart = $this->daysToStart; ?>
    <?php
    $currentDate = date('Y-m-d');
    $projectStartDate = $this->startDate;
    ?>
    <?php if ($this->project->funding_state == 'draft') : ?>
    <div class="sitecrowdfunding_project_status_draft sitecrowdfunding_project_status">
        <span><?php echo $this->translate("Draft"); ?></span>
    </div>
    <?php elseif($this->project->funding_state == 'submitted'): ?>
    <div class="sitecrowdfunding_project_status_draft sitecrowdfunding_project_status">
        <span><?php echo $this->translate("Under Review"); ?></span>
    </div>
    <?php elseif ($this->project->is_fund_raisable && $this->project->is_gateway_configured && $this->project->funding_state == 'published' && strtotime($currentDate) >= strtotime($projectStartDate)): ?>
    <div class="sitecrowdfunding_project_status_ongoing sitecrowdfunding_project_status">
        <span><?php echo $this->translate("Published"); ?></span>
    </div>
    <?php elseif ($this->project->is_fund_raisable && !$this->project->is_gateway_configured && $this->project->funding_state == 'published'): ?>
    <div class="sitecrowdfunding_project_status_ongoing sitecrowdfunding_project_status">
        <span><?php echo $this->translate("Configure Payment Methods"); ?></span>
    </div>
    <?php elseif ($this->project->is_fund_raisable && $this->project->funding_state == 'published'): ?>
    <div class="sitecrowdfunding_project_status_successful sitecrowdfunding_project_status">
        <span><?php echo $this->translate("Published"); ?></span>
    </div>
    <?php elseif ($this->project->funding_state == 'successful') : ?>
    <div class="sitecrowdfunding_project_status_successful sitecrowdfunding_project_status">
        <span><?php echo $this->translate('Completed'); ?></span>
    </div>
    <?php elseif ($this->project->funding_state == 'failed') : ?>
    <div class="sitecrowdfunding_project_status_failed sitecrowdfunding_project_status">
        <span><?php echo $this->translate('Failed'); ?></span>
    </div>
    <?php elseif ($this->project->funding_state == 'rejected') : ?>
    <div class="sitecrowdfunding_project_status_failed sitecrowdfunding_project_status">
        <span><?php echo $this->translate('Rejected'); ?></span>
    </div>
    <?php else: ?>
    <div class="sitecrowdfunding_project_status_draft sitecrowdfunding_project_status">
        <span><?php echo $this->translate("Draft"); ?></span>
    </div>
    <?php endif; ?>
</div>

