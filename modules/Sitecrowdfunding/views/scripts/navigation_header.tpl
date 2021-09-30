<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: navigation_views.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<div class="headline">
    <span style="font-size: 24px">
        <?php echo $this->translate('Dashboard'); ?>:
        <?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle()) ?>
    </span>
    <div class="fright" id="fright">
        <div>
              <a class="view_project_btn" href='<?php echo $this->url(array( 'action' => 'create', 'project_id' => $this->project->project_id ), 'sesblog_general', true) ?>'>
       <i class="fa fa-plus" aria-hidden="true" style="color: #333333"></i>
        <span ><?php echo $this->translate('Create Blog') ?></span>
        </a>&nbsp;
            <span title="Project Status" class="sitecrowdfunding_project_status_successful sitecrowdfunding_project_status"><?php echo $this->project->state ?></span>
            <?php echo $this->htmlLink($this->project->getHref(), $this->translate('View this Project'), array("class" =>
            'view_project_btn' , 'target' => '_blank')) ?>
        </div>

    </div>
   
</div>

<style>
    .view_project_btn {
        margin-top: 5px;
        margin-bottom: 5px;
        padding: 7px;
        border-radius: 3px;
        font-size: 14px;
    }
    @media(max-width:767px){
        #fright {
            margin-top: 6px;
        }
    }
</style>