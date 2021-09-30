<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'isFundingSection'=> true, 'sectionTitle' => 'External funding', 'sectionDescription'=> 'Manage external funding for this project' )); ?>
    <div class="sitecrowdfunding_project_form">
        <div class="clr add-funding-btn">
            <?php echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'funding' , 'action'=>'add-external-funding', 'project_id' => $this->project_id), $this->translate('Add External funding'), array('class' => 'icon seaocore_icon_add')) ?>
        </div>
        <div class="organization-div">
        <h3>
            <?php echo $this->translate("External funding"); ?>
        </h3>
        <?php if(count($this->externalfunding) > 0): ?>
        <?php foreach($this->externalfunding as $org): ?>
        <div class="org_container">
            <div class="org_left">
                <div class="org_logo">
                    <img style="width: 80px;height: 80px" src="<?php echo !empty($org['logo']) ? $org['logo'] : $defaultLogo ?>"/>
                    <p><?php echo $org['type']; ?></p>
                </div>
                <div class="org_title_desc">
                    <h3 class="organization-header">
                        <?php if(!empty($org['link'])): ?>
                            <?php echo $this->htmlLink($org['link'], $org['title']. ' - '.Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency( $org['amount'] ), array('target' => '_blank')) ?>
                        <?php else: ?>
                            <?php echo $org['title']. ' - '.Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency( $org['amount'] ) ?>
                        <?php endif; ?>
                    </h3>
                    <div>
                        <h4>
                        <?php echo "Funded on: ".date('Y-m-d',strtotime($org['funding_date'])) ?>
                        </h4>
                    </div>
                    <div>
                        <?php echo $org['notes']; ?>
                    </div>
                </div>
            </div>
            <div class="org_options">

                <?php echo $this->htmlLink(
                array(
                'route' => 'sitecrowdfunding_extended',
                'controller' => 'funding',
                'action' => 'delete-external-funding',
                'externalfunding_id' => $org['externalfunding_id'],
                ),
                $this->translate('Delete'), array(
                'class' => 'buttonlink smoothbox seaocore_icon_remove',
                'style' => 'float: right; color: #FF0000; padding-top: 10px;padding-left: 10px;'
                )) ?>

                <?php echo $this->htmlLink(
                array(
                'route' => 'sitecrowdfunding_externalfunding_edit',
                'controller' => 'funding',
                'action' => 'edit-external-funding',
                'externalfunding_id' => $org['externalfunding_id'],
                'project_id' => $this->project->project_id
                ),
                $this->translate('Edit'), array(
                'class' => 'buttonlink seaocore_icon_edit',
                'style' => 'float: right; color: #FF0000; padding-top: 10px;'
                )) ?>



            </div>
        </div>
        <?php endforeach;?>
        <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('No External funding for this project'); ?>
            </span>
        </div>
        <?php endif; ?>
    </div>
    </div>
</div>
</div>
</div>
<script type="text/javascript">

</script>
<style type="text/css">
    /*edit funding form*/
    .add-funding-btn{
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .sitecrowdfunding_project_form{
        padding: 10px;
        border: 1px solid #eee;
    }
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