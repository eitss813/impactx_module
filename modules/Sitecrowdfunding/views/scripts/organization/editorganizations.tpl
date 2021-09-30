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
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Edit Organization', 'sectionDescription' => 'Edit and manage the organizations of your project below.')); ?>
    <div class="sitecrowdfunding_dashboard_form">
        <!-- <h3><?php echo $this->translate("Edit Organization"); ?></h3>
        <p class="form-description"><?php echo $this->translate("Edit and manage the organizations of your project below."); ?> -->
        <div class="clr add-org-btn">
            <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'organization' , 'action'=>'create', 'project_id' => $this->project_id), $this->translate('Add New Organizations'), array('class' => 'icon seaocore_icon_add')) ?>
        </div>
        <div class="organization-div">
            <h3>
                <?php echo $this->translate("Listed Organizations"); ?>
            </h3>
            <?php if(count($this->internalorganizations) > 0): ?>
            <?php foreach($this->internalorganizations as $org): ?>
            <div class="org_container">
                <div class="org_left">
                    <div class="org_logo">
                        <img style="width: 80px;height: 80px" src="<?php echo $org['logo'] ?>"/>
                        <p><?php echo  $org['organization_type']; ?></p>
                    </div>
                    <div class="org_title_desc">
                        <h3 class="organization-header">
                            <?php echo $this->htmlLink($org['link'],  $org['title'], array('target' => '_blank')) ?>
                        </h3>
                        <div>
                            <?php echo  $org['description']; ?>
                        </div>
                    </div>
                </div>
                <div class="org_options">
                    <?php echo $this->htmlLink(
                    array(
                    'route' => 'sitecrowdfunding_organizationdelete',
                    'controller' => 'organization',
                    'action' => 'delete',
                    'org_id' => $org['project_page_id'],
                    'type' => 'internal',
                    ),
                    $this->translate('Delete'), array(
                    'class' => 'buttonlink smoothbox seaocore_icon_remove',
                    'style' => 'float: right; color: #FF0000; padding-top: 10px;'
                    )) ?>
                </div>
            </div>
            <?php endforeach;?>
            <?php else: ?>
            <div class="tip">
                            <span>
                                <?php echo $this->translate('No Listed Organizations for this project'); ?>
                            </span>
            </div>
            <?php endif; ?>
        </div>
        <div class="organization-div">
            <h3>
                <?php echo $this->translate("Unlisted Organizations"); ?>
            </h3>
            <?php if(count($this->externalorganizations) > 0): ?>
                <?php foreach($this->externalorganizations as $org): ?>
                    <div class="org_container">
                        <div class="org_left">
                            <div class="org_logo">
                                <img style="width: 80px;height: 80px" src="<?php echo !empty($org['logo']) ? $org['logo'] : $defaultLogo; ?>"/>
                                <?php if($org['organization_type'] === 'others'): ?>
                                <p><?php echo  $org['others']; ?></p>
                                <?php else:?>
                                <p><?php echo  $org['organization_type']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="org_title_desc">
                                <?php if(!empty($org['link'])):?>
                                    <h3 class="organization-header">
                                        <?php echo $this->htmlLink($org['link'],  $org['title'], array('target' => '_blank')) ?>
                                    </h3>
                                <?php else: ?>
                                    <h3><?php echo $org['title']; ?></h3>
                                <?php endif; ?>
                                <div>
                                    <?php echo  $org['description']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="org_options">
                            <?php echo $this->htmlLink(
                            array(
                            'route' => 'sitecrowdfunding_organizationdelete',
                            'controller' => 'organization',
                            'action' => 'delete',
                            'org_id' => $org['organization_id'],
                            'type' => 'external',
                            ),
                            $this->translate('Delete'), array(
                            'class' => 'buttonlink smoothbox seaocore_icon_remove',
                            'style' => 'float: right; color: #FF0000; padding-top: 10px;'
                            )) ?>
                        </div>
                    </div>
                <?php endforeach;?>
            <?php else: ?>
            <div class="tip">
                            <span>
                                <?php echo $this->translate('No Unlisted Organizations for this project'); ?>
                            </span>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<style type="text/css">
    .org_container{
        display: flex;
        border: 1px solid #f2f0f0;
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 10px;
        justify-content: space-between;
    }
    .org_left{
        display: flex;
    }
    .org_logo{
        padding-right: 15px;
        text-align: center;
        width: auto;
    }
    .org_options{
        min-width: 80px;
    }
    .sitecrowdfunding_dashboard_form{
        padding: 20px;
        border: 1px solid #f2f0f0;
    }
    .organization-div{
        padding-top: 20px
    }
    .add-org-btn{
        margin-top: 10px;
    }
    .organization-header{
        text-decoration: underline;
        font-weight: bold;
    }
</style>