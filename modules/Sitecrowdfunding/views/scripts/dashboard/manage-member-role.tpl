<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-leaders.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<script type="text/javascript" >
    var submitformajax = 1;
    var manage_admin_formsubmit = 1;
    function owner(thisobj) {
        var Obj_Url = thisobj.href ;
        Smoothbox.open(Obj_Url);
    }
</script>

<style type="text/css">
    .global_form > div > div{
        background:none;
        border:none;
        padding:0px;
    }
</style>
<?php $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php if (empty($this->is_ajax)) : ?>
        <div class="layout_middle">
            <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Manage Member Role','sectionDescription' => ($manageMemberSettings == 2 ? "Below you can see all the member roles added by you, which members of your page can choose. You can also add new member roles and delete them." : "Below you can see all the member roles added by you and our site administrators, which members of your page can choose. You can also add new member roles and delete them. Note that you can only delete the roles created by you.")  )); ?>
            <div class="sitecrowdfunding_dashboard_content">
                <div id="show_tab_content">
                <?php endif; ?>
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js'); ?>
                <div class="global_form">
                    <div>
                        <div class="sitecrowdfunding_manage_member_role">
                            <h3> <?php echo $this->translate('Manage Member Role'); ?> </h3>
                            <!--<?php $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.category.settings', 1);
                            if ($manageMemberSettings == 2) : ?>
                                <p class="form-description"><?php echo $this->translate("Below you can see all the member roles added by you, which members of your page can choose. You can also add new member roles and delete them.") ?></p>
                            <?php else : ?>
                                <p class="form-description"><?php echo $this->translate("Below you can see all the member roles added by you and our site administrators, which members of your page can choose. You can also add new member roles and delete them. Note that you can only delete the roles created by you.") ?></p>
                            <?php endif; ?>
                            <br /> -->

                            <?php if (count($this->manageRolesHistories) > 0) : ?>
                                <div class="sitecrowdfunding_manage_member_role_list clr">
                                    <div class="fleft"><b><?php echo  $this->translate("Role") ?></b></div>
                                    <div class="sitecrowdfunding_manage_member_role_list_option fright"><b><?php echo $this->translate("Options") ?></b></div>
                                </div>
                            <?php endif; ?>

                            <?php foreach ($this->manageRolesHistories as $item):?>
                                <div id='<?php echo $item->role_id ?>_page' class="sitecrowdfunding_manage_member_role_list clr">
                                    <div class="fleft"><?php echo $item->role_name; ?></div>
                                    <?php if (empty($item->is_admincreated)) : ?>
                                        <div class="sitecrowdfunding_manage_member_role_list_option fright">
                                            <?php $url = $this->url(array('controller'=>'dashboard','action' => 'delete-member-role'), 'sitecrowdfunding_extended', true);?>

                                            <a href="javascript:void(0);" onclick="deleteMemberCategory('<?php echo $item->role_id?>', '<?php echo $url;?>', '<?php echo $this->project_id ?>')"; ><?php echo $this->translate('Delete Member Role');?></a>
                                            | <?php if (!empty($this->is_ajax)) : ?>
                                            <?php echo $this->htmlLink(array('controller'=>'dashboard','action' => 'edit-member-role', 'role_id' => $item->role_id, 'project_id' => $this->project_id), $this->translate('Edit Member Role'), array(' class' => 'smoothbox', 'onclick' => 'owner(this);return false')); ?>
                                            <?php else : ?>
                                            <?php echo $this->htmlLink(array('controller'=>'dashboard','action' => 'edit-member-role', 'role_id' => $item->role_id, 'project_id' => $this->project_id), $this->translate('Edit Member Role'), array(' class' => 'smoothbox')); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="sitecrowdfunding_manage_member_role_list_option fright">
                                            <?php echo $this->translate('Delete Member Role'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <br />
                            <br />
                            <input type="hidden" id='count_div' value='0' />
                            <?php echo $this->htmlLink(array('controller'=>'dashboard','action' => 'add-member-role', 'project_id' => $this->project_id), $this->translate('Add Member Role'), array(' class' => 'common_btn smoothbox sitecrowdfunding_add_member_role_btn')); ?>
                        </div>
                    </div>
                </div>
                <br />
                <div id="show_tab_content_child"></div>
                <?php if (empty($this->is_ajax)) : ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>


