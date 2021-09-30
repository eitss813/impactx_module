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
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Outcome', 'sectionDescription'=> 'Manage the Outcome of this project.')); ?>
    <div class="sitecrowdfunding_dashboard_form">
        <div class="clr">
            <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'outcome' , 'action'=>'add-outcome', 'project_id' => $this->project_id), $this->translate('Add Outcome'), array('class' => 'button seaocore_icon_add outcome_btn')) ?>
        </div>
        <div class="organization-div">

            <div class="organization-list">
                <?php if(count($this->outcomes) > 0): ?>
                <?php foreach($this->outcomes as $outcome): ?>

                <div class="outcome_item">
                    <div class="outcome_info">
                        <h3 class="outcome_name"><b><?php echo $outcome['title']; ?></b></h3>
                        <div class="outcome_options">

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

                        </div>
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
                                <?php echo $this->translate('No Outcome for this project'); ?>
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

    .outcome_btn{
        padding: 3px 8px !important;
        font-weight: normal !important;
    }

    .outcome_info{
        margin-bottom: 10px;
    }

    .outcome_options .seaocore_icon_remove{
        margin-right: 10px;
    }
</style>