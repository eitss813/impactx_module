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
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Output', 'sectionDescription'=> 'Manage the Output of this project.')); ?>
    <div class="sitecrowdfunding_dashboard_form">
        <div class="clr">
            <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'output' , 'action'=>'add-output', 'project_id' => $this->project_id), $this->translate('Add Output'), array('class' => 'button seaocore_icon_add output_btn')) ?>
        </div>
        <div class="organization-div">

            <div class="organization-list">
                <?php if(count($this->outputs) > 0): ?>
                    <?php foreach($this->outputs as $output): ?>

                        <div class="output_item">
                            <div class="output_info">
                                <h3 class="output_name"><b><?php echo $output['title']; ?></b></h3>
                                <div class="output_options">

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

    </div>
</div>

<style type="text/css">
    .sitecrowdfunding_dashboard_form{
        padding: 10px;
    }
    .organization-div{
        padding-top: 20px
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

    .output_btn{
        padding: 3px 8px !important;
        font-weight: normal !important;
    }

    .output_info{
        margin-bottom: 10px;
    }

    .output_options .seaocore_icon_remove{
        margin-right: 10px;
    }
</style>