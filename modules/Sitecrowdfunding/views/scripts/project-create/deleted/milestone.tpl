<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js');
?>
<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<div class="sitecrowdfunding_project_new_steps">

    <form class="global_form">
        <div>
            <div>
                <h3 >
                    Project Milestones
                </h3>
            </div>
        </div>
    </form>


    <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'project-create' , 'action'=>'add-milestone', 'project_id' => $this->project_id), $this->translate('Add Milestone'), array('class' => 'add_milestone_button button fright icon smoothbox seaocore_icon_add')) ?>
    <br/>
    <?php if(count($this->milestones) > 0): ?>

    <div style="padding-left: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
    border: 1px solid #f2f0f0;">
        <h4 style="font-weight: 800;">
            Milestones
        </h4>
        <?php echo $this->content()->renderWidget("sitecrowdfunding.project-milestone", array(project_id => $this->project_id)); ?>
    </div>
    <?php endif; ?>
    <!-- <div class="organization-div">
        <?php if(count($this->milestones) > 0): ?>
        <h3>
            <?php echo $this->translate("Milestones"); ?>
        </h3>
        <?php foreach($this->milestones as $milestone): ?>
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
            <li>
                <span><?php echo  $this->translate("Milestone end date : "); ?></span>
                <span><?php echo  date('Y-m-d',strtotime($milestone['end_date'])); ?></span>
            </li>
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
        <?php endif; ?>
    </div> -->

    <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
        <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
        <button name="execute" id="execute"  type="button" onclick="window.location.href='<?php echo $this->nextURL; ?>'">Next</button>
    </div>
</div>

<style type="text/css">
    .add_milestone_button{
        /*padding-left: 15px;*/
        font-weight: unset;
        margin: 10px;
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
</style>