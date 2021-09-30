<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: header.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    .btns{
        display: flex;
        justify-content: center;
        margin-top: 5px;
    }
    .btn_container{
        display: flex;
        flex-direction: column;
    }
    .submit_for_approval_btn{
        margin-right: 5px;
        background: #37bb6f;
        border: 1px solid #2F954E;
        color: #fff !important;
    }
    .rejected_notes_btn{
        text-align: center;
        text-decoration: underline !important;
    }
    .view_project_btn{
        margin-left: 5px;
    }
    .sitecrowdfunding_dashboard_header_title {
        color: #444 !important;
        font-size: 24px !important;
    }
    .sitecrowding_dashboard_header_container{
        display: flex;
        flex-direction: column;
    }
    .sitecrowdfunding_dashboard_header_description{
        width: 60%;
        line-height: 20px;
        margin-right: 10px;
        margin-top: 15px;
    }
    .sitecrowdfunding_dashboard_header_btn1{
        display: flex;
        flex-direction: column
    }
    .sitecrowdfunding_dashboard_header_btn1 a{
        margin-top: 5px;
        margin-bottom: 5px;
        padding: 3px 8px;
        border-radius: 3px;
    }
    .view_project_btn{
        color: #ffffff !important;
        background: #44AEC1;
    }
    .sitecrowdfunding_dashboard_header_description {
        width: 95% !important;
    }
    .title-fundstatus{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }
    .fund-status{
        border: 1px solid;
        border-radius: 5px;
        background-color: #44AEC1;
        color: white;
        padding-right: 20px;
        padding-bottom: 3px;
        bottom: 1px;
        padding-top: 3px;
        position: relative;
        padding-left: 20px;
        padding: -3px;
    }
    .sitecrowdfunding_dashboard_header {
        padding: 0px 0px !important;
    }


</style>
<div class="sitecrowdfunding_dashboard_header" style="display: flex;flex-direction: row;justify-content: space-between">
    <div class="sitecrowding_dashboard_header_container" style="width: 100%;">
        <div class="title-fundstatus">
            <span class="sitecrowdfunding_dashboard_header_title">
                <?php echo $this->sectionTitle; ?>
            </span>
            <?php if($this->project->is_fund_raisable): ?>
                <span title="Funding Status" class="fund-status">
                    <?php echo $this->project->funding_state; ?>
                </span>
            <?php endif; ?>
        </div>
        <span class="sitecrowdfunding_dashboard_header_description">
            <?php echo $this->sectionDescription; ?>
        </span>
    </div>
    <!-- Hide the project status -->
    <?php /*
    <div class="sitecrowdfunding_dashboard_header_btn1" style="width: 25%;">
        <?php if($this->isFundingSection === true): ?>
            <div style="display: flex; flex-direction: column">
                <?php echo $this->content()->renderWidget('sitecrowdfunding.project-funding-status'); ?>
                <?php if($this->project->funding_state === 'rejected'): ?>
                    <?php echo $this->htmlLink(array('route'=> 'sitecrowdfunding_extended', 'controller' => 'status', action => 'view-notes', 'project_id' => $this->project->getIdentity(), 'is_funding' => 1, format=> 'smoothbox' ), $this->translate('View rejected reasons'), array("class" => 'rejected_notes_btn smoothbox', 'style' => 'background: none !important;color: #444 !important;') ) ?>
                <?php endif; ?>
            </div>
            <div class="btns">
                <?php if($this->project->funding_state === 'draft' || $this->project->funding_state === 'rejected' ): ?>
                    <?php echo $this->htmlLink(array('route'=> 'sitecrowdfunding_extended', 'controller' => 'status', action => 'submit-funding', 'project_id' => $this->project->getIdentity(), format=> 'smoothbox' ), $this->translate('Submit for approval'), array("class" => 'submit_for_approval_btn smoothbox')) ?>
                <?php endif; ?>
                <?php echo $this->htmlLink($this->project->getHref(), $this->translate('View this Project'), array("class" => 'view_project_btn')) ?>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column">
                <?php echo $this->content()->renderWidget('sitecrowdfunding.project-status'); ?>
                <?php if($this->project->state === 'rejected'): ?>
                    <?php echo $this->htmlLink(array('route'=> 'sitecrowdfunding_extended', 'controller' => 'status', action => 'view-notes', 'project_id' => $this->project->getIdentity(), 'is_funding' => 0, format=> 'smoothbox' ), $this->translate('View rejected reasons'), array("class" => 'rejected_notes_btn smoothbox', 'style' => 'background: none !important;color: #444 !important;') ) ?>
                <?php endif; ?>
            </div>
            <div class="btns">
                <?php if($this->project->state === 'draft' || $this->project->state === 'rejected' ): ?>
                    <?php echo $this->htmlLink(array('route'=> 'sitecrowdfunding_extended', 'controller' => 'status', action => 'submit', 'project_id' => $this->project->getIdentity(), format=> 'smoothbox' ), $this->translate('Submit for approval'), array("class" => 'submit_for_approval_btn smoothbox')) ?>
                <?php endif; ?>
                <?php echo $this->htmlLink($this->project->getHref(), $this->translate('View this Project'), array("class" => 'view_project_btn')) ?>
            </div>
        <?php endif; ?>
    </div>
    */ ?>
</div>