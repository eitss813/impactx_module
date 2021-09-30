<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project)); ?>
        <?php if ($this->project->is_fund_raisable && !$this->project->is_gateway_configured) : ?>
            <div class="tip" style="padding-top: 20px;">
                <span><?php echo $this->translate("Please setup payment methods for this project."); ?></span>
            </div>
        <?php endif; ?>
        <div class="project-status-container">
            <?php echo $this->content()->renderWidget('sitecrowdfunding.project-status'); ?>
        </div>
        <div class="organization-div">
            <?php if(count($this->adminnotes) > 0): ?>
            <h3>
                <?php echo $this->translate("Admin notes"); ?>
            </h3>
            <?php foreach($this->adminnotes as $adminnote): ?>
            <ul class="organization-list" >
                <li>
                    <span><?php echo  $this->translate("Notes:"); ?></span>
                    <span><?php echo  $adminnote['description']; ?></span>
                </li>
                <li>
                    <span><?php echo  $this->translate("Created on:"); ?></span>
                    <span><?php echo  date('Y-m-d', strtotime($adminnote['created_date'])); ?></span>
                </li>
            </ul>
            <?php endforeach;?>
            <?php endif; ?>
        </div>
        <?php if($this->showform === true): ?>
            <div class="sitecrowdfunding_project_form">
                <?php echo $this->form->render(); ?>
            </div>
        <?php endif; ?>
</div>
</div>
<style type="text/css">
    /*edit funding form*/
    .sitecrowdfunding_project_form .global_form > div
    {
        padding: 0px !important;
    }
    .project-status-container{
        padding: 10px;
        width: 30%;
    }
    .organization-div{
        padding: 20px;
    }
    .organization-list{
        border-bottom: 1px solid #f2f0f0;
        padding: 5px 0px 5px 0px;
    }
    .organization-list li span{
        display: block;
        float: left;
        overflow: hidden;
        width: 175px;
        margin-right: 15px;

    }
    .organization-list > li > span + span {
        min-width: 0px;
        display: block;
        float: none;
        overflow: hidden;
        width: 400px
    }
    .organization-list > li > span + span {
        display: inline-block !important;
    }
</style>
