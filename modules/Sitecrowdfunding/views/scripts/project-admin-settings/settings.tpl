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
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js'); ?>

<div class="sitecrowdfunding_dashboard_content">

    <div class="layout_middle">

        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl',
        array('project' => $this->project, 'sectionTitle' => 'Admin Settings', 'sectionDescription' => "Admin
        Settings")); ?>

        <div class="sitecrowdfunding_dashboard_content">

            <div class="fright button_grp">

                <input type="hidden" id='count_div' value='0' />
                <a style="font-weight: unset !important;" class="button smoothbox"
                   href="<?php echo $this->escape($this->url(array('controller'=>'dashboard','action' => 'add-member-role', 'project_id' => $this->project_id), 'sitecrowdfunding_extended', true)); ?>">
                    <span><?php echo $this->translate("Add Member Roles"); ?></span>
                </a>

                <a style="font-weight: unset !important;" class="button smoothbox"
                   href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'project_id' => $this->project_id), 'sitecrowdfunding_project_member', true)); ?>">
                    <span><?php echo $this->translate("Add Team Members"); ?></span>
                </a>

                <a style="font-weight: unset !important;" class="button smoothbox"
                   href="<?php echo $this->escape($this->url(array( 'controller'=>'temp' ,'action' => 'add-admin-members', 'project_id' => $this->project_id), 'sitecrowdfunding_createspecific', true)); ?>">
                    <?php echo $this->translate("Add Project Administrators"); ?>
                </a>

            </div>

            <br/><br/>

            <!-- Leaders -->
            <div class="sitecrowdfunding_leaders">

                <h3 class="form_title"> <?php echo $this->translate('Project Administrator(s)'); ?> </h3>

                <?php if (count($this->adminMembers) == 0 ): ?>
                    <div class="tip">
                        <span>No Project Administrators</span>
                    </div>
                <?php endif; ?>

                <?php foreach ($this->adminMembers as $adminMember): ?>
                <?php if (1 ): ?>
                     <div id='<?php echo $adminMember->user_id ?>_page_main' class='sitecrowdfunding_leaders_list'>
                    <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $adminMember->user_id ?>_pagethumb'>
                        <a href="<?php echo $adminMember->getHref();?>">
                            <?php echo $this->itemBackgroundPhoto($adminMember, null, null, array('tag' => 'i')); ?>
                        </a>
                    </div>
                    <div id='<?php echo $adminMember->user_id ?>_page' class="sitecrowdfunding_leaders_detail">

                        <?php if ($this->project->owner_id != $adminMember->user_id): ?>
                        <div class="sitecrowdfunding_leaders_cancel">
                            <?php if ($this->owner_id != $adminMember->user_id) : ?>
                            <span class="sitecrowdfunding_link_wrap mright5">
                                    <i class="seaocore_txt_red seaocore_icon_remove_square"></i>
                                <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended', 'controller' => 'dashboard', 'action' => 'demote', 'project_id' => $this->project->getIdentity(), 'user_id' => $adminMember->getIdentity()), $this->translate('Remove as admin'), array(
                                        'class' => 'smoothbox'
                                    ))?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <h2><?php echo $this->htmlLink($adminMember->getHref(), $adminMember->getTitle()) ?></h2>

                    </div>
                </div>
                <?php endif; ?>
  
                <?php endforeach; ?>
                <?php foreach ($this->externalMembers as $member): ?>
                   <div id='' class='sitecrowdfunding_leaders_list'>
                       <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                           <a href="javascript:void(0);">
                               <?php $itemphoto = $this->layout()->staticBaseUrl . "application/modules/User/externals/images/clock.png"; ?>
                               <i class="bg_item_photo bg_thumb_profile bg_item_photo_user "
                                  style=" background-image:url('<?php echo $itemphoto?>');width: 103px;
    height: 104px;"></i>
                           </a>
                       </div>
                    <div id='' class="sitecrowdfunding_leaders_detail">
                        <div class="sitecrowdfunding_leaders_cancel">
                            <span class="sitecrowdfunding_link_wrap mright5">
                                    <i class="seaocore_txt_red seaocore_icon_remove_square"></i>
                                <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_project_member',
                                'controller' => 'member', 'action' => 'leave',
                                'project_id' =>  null,
                                'user_id' => null,
                                'membership_id'=> null,
                                'member_email'=> $member->member_email, 'list_id' =>  $member->list_id),
                                $this->translate('Remove Members'), array(
                                                    'class' => 'smoothbox'
                                                ))?>
                                </span>
                        </div>

                        <h2><?php echo $member->member_email; ?></h2>
                        <h2><?php echo 'Invitation sent, pending acceptance'; ?></h2>

                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <br/><br/>

            <!-- Manage Members -->
            <div class="sitecrowdfunding_leaders">

                <h3 class="form_title"> <?php echo $this->translate('Team Member(s)'); ?> </h3>

                <?php if ( count($this->paginator) == 0 && count($this->pendingInvites) == 0 ): ?>
                    <div class="tip">
                        <span>No Team Members</span>
                    </div>
                <?php endif; ?>

                <?php foreach ($this->paginator as $item): ?>
                    <?php $user_id = $item['user_id']; ?>
                    <?php $user = Engine_Api::_()->getItem('user', $user_id); ?>

                <div id='<?php echo $user_id ?>_page_main' class='sitecrowdfunding_leaders_list'>
                    <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                        <a href="<?php echo $user->getHref();?>">
                            <?php echo $this->itemBackgroundPhoto($user, 'thumb.profile', null, array('tag' => 'i')); ?>
                        </a>
                    </div>

                    <div id='<?php echo $user_id ?>_page'
                         class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">

                        <div class="sitecrowdfunding_leaders_cancel">
                                <span class="sitecrowdfunding_link_wrap mright5">

                                    <?php if ($this->project->owner_id != $item['user_id'] ): ?>
                                    <?php if ($item['active']==0 && $item['user_approved']==0 && $item['resource_approved']==1 ) : ?>

                                    <a class="button smoothbox"
                                       href="<?php echo $this->escape($this->url(array( 'action' => 'accept-member', 'project_id' => $item['project_id'] , 'user_id' => $user_id ), 'sitecrowdfunding_project_member', true)); ?>">
                                            <span><?php echo $this->translate("Accept"); ?></span>
                                        </a>

                                        <a class="button smoothbox"
                                           href="<?php echo $this->escape($this->url(array( 'action' => 'reject-member', 'project_id' => $item['project_id'] , 'user_id' => $user_id ), 'sitecrowdfunding_project_member', true)); ?>">
                                            <span><?php echo $this->translate("Reject"); ?></span>
                                        </a>

                                    <?php endif; ?>

                                    <?php if ($item['active']==1 && $item['user_approved']==1 && $item['resource_approved']==1 ) : ?>

                                       <span class="sitecrowdfunding_link_wrap mright5">
                                            <i class="seaocore_txt_red seaocore_icon_remove_square"></i>
                                           <?php if ($user_id!=0 ) : ?>
                                               <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_project_member', 'controller' => 'member', 'action' => 'leave', 'project_id' =>  $item['project_id'], 'user_id' => $user_id), $this->translate('Remove Member'), array(
                                                    'class' => 'smoothbox'
                                                ))?>
                                           <?php endif; ?>
                                           <?php if ($user_id==0 ) : ?>
                                               <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_project_member', 'controller' => 'member', 'action' => 'leave', 'project_id' =>  $item['project_id'], 'user_id' => $user_id,'membership_id'=>$item['membership_id']), $this->translate('Remove Members'), array(
                                                    'class' => 'smoothbox'
                                                ))?>
                                           <?php endif; ?>
                                        </span>





                                    <?php endif; ?>

                                    <?php endif; ?>

                                </span>
                        </div>

                        <h2>
                            <?php if ($user_id==0 ) : ?>
                            <?php print_r($item['member_email']); ?>
                            <?php endif; ?>

                            <?php if ($user_id!=0 ) : ?>
                                 <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                            <?php endif; ?>

                        </h2>

                        <?php if ($this->project->owner_id != $item['user_id'] ): ?>
                        <h2>
                            <?php if ($user_id==0 ) : ?>
                            <?php echo $this->translate('Invite Awaiting for Approval'); ?>
                            <?php endif; ?>

                            <?php if ($user_id!=0 ) : ?>
                            <?php echo $this->translate('Member is approved'); ?>
                            <?php endif; ?>
                        </h2>
                        <?php endif; ?>

                        <?php if(!empty($item['title'])):?>
                        <h2>
                            <?php echo implode(', ', json_decode($item['title'])) ?>
                        </h2>
                        <?php endif; ?>
                    </div>
                </div>


                <?php endforeach; ?>

                <?php if(count($this->pendingInvites) > 0): ?>
                <?php foreach ($this->pendingInvites as $item): ?>
                <div id="<?php echo $item['id'] ?>_page_main" class='sitecrowdfunding_leaders_list'>
                    <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                        <a href="javascript:void(0);">
                            <?php $itemphoto = $this->layout()->staticBaseUrl . "application/modules/User/externals/images/clock.png"; ?>
                            <i class="bg_item_photo bg_thumb_profile bg_item_photo_user "
                               style=" background-image:url('<?php echo $itemphoto?>');width: 103px;
    height: 104px;"></i>
                        </a>
                    </div>

                    <div id="<?php echo $item['id'] ?>_page" class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">

                        <div class="sitecrowdfunding_leaders_cancel">
                            <span class="sitecrowdfunding_link_wrap mright5">
                                <a class="button smoothbox"
                                   href="<?php echo $this->escape($this->url(array( 'action' => 'remove-external-member', 'project_id' => $item['project_id'] , 'invite_id' => $item['id'] ), 'sitecrowdfunding_project_member', true)); ?>">
                                    <span><?php echo $this->translate("Remove Member"); ?></span>
                                </a>
                            </span>
                        </div>

                        <h2>
                            <?php echo $item['recipient_name'] ?>
                            <div>
                                <?php echo $item['recipient'] ?>
                            </div>
                        </h2>

                        <h2>
                            <?php echo $this->translate('Invitation sent, pending acceptance'); ?>
                        </h2>

                        <?php if(!empty($item['project_role'])): ?>
                        <h2>
                            <?php
                                        $roles_id = json_decode($item['project_role']);
                                        $roleName = array();
                                        foreach($roles_id as $role_id) {
                                            $roleName[] = Engine_Api::_()->getDbtable('roles',
                            'sitecrowdfunding')->getRoleName($role_id);
                            }
                            echo implode(', ', $roleName);
                            ?>
                        </h2>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <br/><br/>

            <!-- Manage Member Roles -->
            <div class="sitecrowdfunding_manage_member_role">

                <h3 class="form_title"> <?php echo $this->translate('Member Role(s)'); ?> </h3>

                <?php if (count($this->manageRolesHistories) == 0) : ?>
                    <div class="tip">
                        <span>
                            No Member Roles
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (count($this->manageRolesHistories) > 0) : ?>
                    <div class="sitecrowdfunding_manage_member_role_list clr">
                        <h2 class="fleft"><b><?php echo  $this->translate("Role") ?></b></h2>
                        <h2 class="sitecrowdfunding_manage_member_role_list_option fright"><b><?php echo $this->translate("Options") ?></b></h2>
                    </div>
                <?php endif; ?>

                <?php foreach ($this->manageRolesHistories as $item):?>
                    <div id='<?php echo $item->role_id ?>_page' class="sitecrowdfunding_manage_member_role_list clr">
                        <div class="fleft"><?php echo $item->role_name; ?></div>

                        <?php if (empty($item->is_admincreated)) : ?>
                            <div class="sitecrowdfunding_manage_member_role_list_option fright">
                                <?php $url = $this->url(array('controller'=>'dashboard','action' => 'delete-member-role'),
                                'sitecrowdfunding_extended', true);?>

                                <a href="javascript:void(0);"
                                   onclick="deleteMemberCategory('<?php echo $item->role_id?>', '<?php echo $url;?>', '<?php echo $this->project_id ?>')"
                                   ;><?php echo $this->translate('Delete');?></a>
                                | <?php if (!empty($this->is_ajax)) : ?>
                                <?php echo $this->htmlLink(array('controller'=>'dashboard','action' => 'edit-member-role',
                                'role_id' => $item->role_id, 'project_id' => $this->project_id), $this->translate('Edit'), array(' class' => 'smoothbox', 'onclick' => 'owner(this);return false')); ?>
                                <?php else : ?>d
                                <?php echo $this->htmlLink(array('controller'=>'dashboard','action' => 'edit-member-role',
                                'role_id' => $item->role_id, 'project_id' => $this->project_id), $this->translate('Edit'), array(' class' => 'smoothbox')); ?>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="sitecrowdfunding_manage_member_role_list_option fright">
                                <?php echo $this->translate('Delete'); ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>

<style>
    .bg_item_photo {
         border:  unset !important;
    }
    .sitecrowdfunding_leaders_detail a:hover {
        color: black !important;
    }
    .button_grp > a {
        margin: 0 10px;
    }
    .form_title{
        padding-bottom: 10px;
        border-bottom: 1px solid #f2f0f0;
        margin-top: 10px;
        font-size: 19px;
    }
    .sitecrowdfunding_leaders_detail{
        background: none !important;
        border: none !important;
        padding : 0 !important;
        font-size: 14px !important;
    }
    .sitecrowdfunding_manage_member_role_list_option{
        width: 300px !important;
    }
    .sitecrowdfunding_dashboard_content{
        -webkit-box-shadow: unset !important;
    }
</style>
<script type="text/javascript" >
    var submitformajax = 1;
    var manage_admin_formsubmit = 1;
    function owner(thisobj) {
        var Obj_Url = thisobj.href ;
        Smoothbox.open(Obj_Url);
    }
</script>