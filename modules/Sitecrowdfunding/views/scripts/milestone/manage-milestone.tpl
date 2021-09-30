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
<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Manage Milestones', 'sectionDescription'=> 'Manage the milestones of this project.')); ?>
    <div class="sitecrowdfunding_dashboard_form">
        <div class="clr">
            <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'milestone' , 'action'=>'add-milestone', 'project_id' => $this->project_id), $this->translate('Add New Milestone'), array('class' => 'icon seaocore_icon_add')) ?>
        </div>
        <div class="organization-div">
            <h3>
                <?php echo $this->translate("Milestones"); ?>
            </h3>
            <?php if(count($this->milestones) > 0): ?>
            <?php foreach($this->milestones as $milestone): ?>
            <?php echo $this->htmlLink(
            array(
            'route' => 'sitecrowdfunding_extended',
            'controller' => 'milestone',
            'action' => 'delete',
            'milestone_id' => $milestone['milestone_id'],
            ),
            $this->translate('Delete'), array(
            'class' => 'buttonlink smoothbox seaocore_icon_remove',
            'style' => 'float: right; color: #FF0000; padding-top: 10px;'
            )) ?>
            <?php echo $this->htmlLink(
            array(
            'route' => 'sitecrowdfunding_milestoneedit',
            'controller' => 'milestone',
            'action' => 'edit-milestone',
            'milestone_id' => $milestone['milestone_id'],
            'project_id' => $this->project->project_id,
            ),
            $this->translate('Edit'), array(
            'class' => 'buttonlink seaocore_icon_edit',
            'style' => 'float: right; color: #FF0000; padding-top: 10px;padding-right: 5px'
            )) ?>
            <ul class="organization-list" >
                <li>
                    <img style="width: 80px;height: 80px" src="<?php echo !empty($milestone['logo']) ? $milestone['logo'] : $defaultLogo; ?>"/>
                </li>
                <li>
                    <span><?php echo $this->translate("Milestone name : "); ?></span>
                    <span><?php echo $milestone['title']; ?></span>
                </li>
                <li >
                    <span><?php echo  $this->translate("Milestone status : "); ?></span>
                    <span><?php echo  $this->statusLabels[$milestone['status']]; ?></span>
                </li>
                <li>
                    <span><?php echo  $this->translate("Milestone start date : "); ?></span>
                    <span><?php echo  date('Y-m-d',strtotime($milestone['start_date'])); ?></span>
                </li>
                <?php if(!empty($milestone['end_date']) && $milestone['end_date'] != null): ?>
                <li>
                    <span><?php echo  $this->translate("Milestone end date : "); ?></span>
                    <span><?php echo  date('Y-m-d',strtotime($milestone['end_date'])); ?></span>
                </li>
                <?php endif; ?>
                <li>
                    <span><?php echo  $this->translate("Milestone description : "); ?></span>
                    <span><?php echo  $milestone['description']; ?></span>
                </li>
                <li>
                    <span><?php echo  $this->translate("To achieve milestone : "); ?></span>
                    <span><?php echo  $milestone['question']; ?></span>
                </li>
            </ul>
            <?php endforeach;?>
            <?php else: ?>
            <div class="tip">
                            <span>
                                <?php echo $this->translate('No Milestone for this project'); ?>
                            </span>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<style type="text/css">
    .sitecrowdfunding_dashboard_form{
        padding: 10px;
    }
    .organization-div{
        padding-top: 20px
    }
    .organization-list{
        border-bottom: 1px solid #f2f0f0;
        padding: 10px 0px 10px 0px;
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
    .organization-list-view{
        text-decoration: underline;
        font-weight: bold;
    }
</style>