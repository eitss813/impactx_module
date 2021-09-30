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

<div class="outcome-output-div">
    <div class="outcome_list">
        <h3>
            <?php echo $this->translate("Outcomes"); ?>
        </h3>

        <?php if ($this->project->owner_id == $this->viewer_id): ?>
        <div class="clr">
            <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended','controller'=> 'outcome' , 'action'=>'add-outcome', 'project_id' => $this->project_id), $this->translate('Add Outcome'), array('class' => 'button seaocore_icon_add outcome_btn')) ?>
        </div>
        <br/>
        <?php endif; ?>


        <?php if(count($this->outcomes) > 0): ?>
        <?php foreach($this->outcomes as $outcome): ?>
        <div class="outcome_item">
            <div class="outcome_info">
                <h3 class="outcome_name"><b><?php echo $outcome['title']; ?></b></h3>
                <div class="outcome_options">

                    <?php if ($this->project->owner_id == $this->viewer_id): ?>

                    <?php echo $this->htmlLink(
                    array(
                    'route' => 'sitecrowdfunding_extended',
                    'controller' => 'outcome',
                    'action' => 'delete-outcome',
                    'outcome_id' => $outcome['outcome_id'],
                    ),
                    $this->translate('Delete'), array(
                    'class' => 'button smoothbox seaocore_icon_remove outcome_btn',
                    )) ?>

                    <?php echo $this->htmlLink(
                    array(
                    'route' => 'sitecrowdfunding_extended',
                    'controller' => 'outcome',
                    'action' => 'edit-outcome',
                    'outcome_id' => $outcome['outcome_id'],
                    'project_id' => $this->project->project_id,
                    ),
                    $this->translate('Edit'), array(
                    'class' => 'button seaocore_icon_edit outcome_btn'
                    )) ?>

                    <?php endif; ?>
                </div>
            </div>
            <br/>
            <div class="outcome_info">
                <h3 class="outcome_name">Excepted no of jobs: <b><?php echo $outcome['no_of_jobs']; ?></b></h3>
            </div>
            <br/>
            <div class="outcome_info">
                <span class="outcome_name">Desire outcomes: </span> <?php echo $outcome['desire_desc']; ?>
            </div>
            <br/>
            <div class="outcome_description">
                <?php echo $outcome['description']; ?>
            </div>
        </div>
        <?php endforeach;?>
        <?php else: ?>
        <div class="tip">
                <span>
                    <?php echo $this->translate('No Outcomes for this project'); ?>
                </span>
        </div>
        <?php endif; ?>

    </div>

    <br/>

    <div class="output_list">
        <h3>
            <?php echo $this->translate("Outputs"); ?>
        </h3>

        <?php if ($this->project->owner_id == $this->viewer_id): ?>
        <div class="clr">
            <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended','controller'=> 'output' , 'action'=>'add-output', 'project_id' => $this->project_id), $this->translate('Add Output'), array('class' => 'button seaocore_icon_add output_btn')) ?>
        </div>
        <br/>
        <?php endif; ?>

        <?php if(count($this->outputs) > 0): ?>
        <?php foreach($this->outputs as $output): ?>
        <div class="output_item">
            <div class="output_info">
                <h3 class="output_name"><b><?php echo $output['title']; ?></b></h3>
                <div class="output_options">
                    <?php if ($this->project->owner_id == $this->viewer_id): ?>

                    <?php echo $this->htmlLink(
                    array(
                    'route' => 'sitecrowdfunding_extended',
                    'controller' => 'output',
                    'action' => 'delete-output',
                    'output_id' => $output['output_id'],
                    ),
                    $this->translate('Delete'), array(
                    'class' => 'button smoothbox seaocore_icon_remove output_btn',
                    )) ?>

                    <?php echo $this->htmlLink(
                    array(
                    'route' => 'sitecrowdfunding_extended',
                    'controller' => 'output',
                    'action' => 'edit-output',
                    'output_id' => $output['output_id'],
                    'project_id' => $this->project->project_id,
                    ),
                    $this->translate('Edit'), array(
                    'class' => 'button seaocore_icon_edit output_btn'
                    )) ?>

                    <?php endif; ?>
                </div>
            </div>
            <br/>
            <div class="output_description">
                <?php echo $output['description']; ?>
            </div>
        </div>
        <?php endforeach;?>
        <?php else: ?>
        <div class="tip">
                <span>
                    <?php echo $this->translate('No Outputs for this project'); ?>
                </span>
        </div>
        <?php endif; ?>
    </div>

</div>

<style type="text/css">
    .outcome-output-div{
        margin-top: 20px;
        padding: 0px 10px;
    }
    .outcome_item{
        padding: 10px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        margin-bottom: 10px;
    }
    .outcome_info .outcome_name{
        float: left;
    }
    .outcome_info .outcome_options{
        float: right;
    }
    .outcome_info{
        margin-bottom: 10px;
    }
    .outcome_options .seaocore_icon_remove{
        margin-right: 10px;
    }
    .outcome_btn{
        padding: 3px 8px !important;
        font-weight: normal !important;
    }

    .output_item{
        padding: 10px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        margin-bottom: 10px;
    }
    .output_info .output_name{
        float: left;
    }
    .output_info .output_options{
        float: right;
    }
    .output_info{
        margin-bottom: 10px;
    }
    .output_options .seaocore_icon_remove{
        margin-right: 10px;
    }
    .output_btn{
        padding: 3px 8px !important;
        font-weight: normal !important;
    }
</style>