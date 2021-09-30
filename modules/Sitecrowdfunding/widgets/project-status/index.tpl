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
    <?php if ($this->project->state == 'draft') : ?>
        <div class="sitecrowdfunding_project_status_draft sitecrowdfunding_project_status">
            <span><?php echo $this->translate("Draft"); ?></span>
        </div>
    <?php elseif($this->project->state == 'submitted'): ?>
        <div class="sitecrowdfunding_project_status_draft sitecrowdfunding_project_status">
            <span><?php echo $this->translate("Under Review"); ?></span>
        </div>
    <?php elseif ($this->project->state == 'published'): ?>
        <div class="sitecrowdfunding_project_status_successful sitecrowdfunding_project_status">
            <span><?php echo $this->translate("Published"); ?></span>
        </div>
    <?php elseif ($this->project->state == 'rejected') : ?>
        <div class="sitecrowdfunding_project_status_failed sitecrowdfunding_project_status">
            <span><?php echo $this->translate('Rejected'); ?></span>
        </div>
    <?php else: ?>
        <div class="sitecrowdfunding_project_status_draft sitecrowdfunding_project_status">
            <span><?php echo $this->translate("Draft"); ?></span>
        </div>
    <?php endif; ?>  
</div>

