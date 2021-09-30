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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css') ?>
<?php $defaultURL =  $this->layout()->staticBaseUrl. "application/modules/User/externals/images/nophoto_user_thumb_profile.png" ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php if (empty($this->is_ajax)) : ?>
    <div class="layout_middle">
        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Manage Members', 'sectionDescription' => 'Below you can see all the members of this project.')); ?>
        <div class="sitecrowdfunding_dashboard_content">
            <div id="show_tab_content">
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
                <div class="global_form">
                    <div class="sitecrowdfunding_leaders">
                        <!-- <h3> <?php echo $this->translate('Manage Members'); ?> </h3>
                        <p class="form-description"><?php echo $this->translate("Below you can see all the members of this project") ?></p> -->
                        <div class="fright">
                            <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'project_id' => $this->project_id), 'sitecrowdfunding_project_member', true)); ?>">
                                <span><?php echo $this->translate("Add People"); ?></span>
                            </a>
                        </div>
                        <br/>
                        <br />
                        <?php foreach ($this->paginator as $item): ?>
                            <?php $user_id = $item['user_id']; ?>
                            <?php $user = Engine_Api::_()->getItem('user', $user_id); ?>
                            <div id='<?php echo $user_id ?>_page_main'  class='sitecrowdfunding_leaders_list'>
                                <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                                    <a href="<?php echo $user->getHref();?>">
                                        <?php echo $this->itemBackgroundPhoto($user, 'thumb.profile', null, array('tag' => 'i')); ?>
                                    </a>
                                </div>
                                <div id='<?php echo $user_id ?>_page' class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">
                                    <div class="sitecrowdfunding_leaders_cancel">
                                        <span class="sitecrowdfunding_link_wrap mright5">
                                            <?php if ($item['active']==0 && $item['user_approved']==0 && $item['resource_approved']==1 ) : ?>
                                                <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'accept-member', 'project_id' => $item['project_id'] , 'user_id' => $user_id ), 'sitecrowdfunding_project_member', true)); ?>">
                                                    <span><?php echo $this->translate("Accept"); ?></span>
                                                </a>
                                                <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'reject-member', 'project_id' => $item['project_id'] , 'user_id' => $user_id ), 'sitecrowdfunding_project_member', true)); ?>">
                                                    <span><?php echo $this->translate("Reject"); ?></span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($item['active']==1 && $item['user_approved']==1 && $item['resource_approved']==1 ) : ?>
                                                <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'leave', 'project_id' => $item['project_id'] , 'user_id' => $user_id ), 'sitecrowdfunding_project_member', true)); ?>">
                                                    <span><?php echo $this->translate("Remove Member"); ?></span>
                                                </a>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <h2>
                                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                                    </h2>
                                    <h2>
                                        <?php if ($item['active']==0 && $item['user_approved']==0 && $item['resource_approved']==1 ) : ?>
                                            <?php echo $this->translate('Awaiting for Approval'); ?>
                                        <?php endif; ?>
                                        <?php if ($item['active']==1 && $item['user_approved']==1 && $item['resource_approved']==1 ) : ?>
                                            <?php echo $this->translate('Member is approved'); ?>
                                        <?php endif; ?>
                                    </h2>
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
                        <div id="<?php echo $item['id'] ?>_page_main"  class='sitecrowdfunding_leaders_list'>
                            <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                                <a href="javascript:void(0);">
                                    <i class="bg_item_photo bg_thumb_profile bg_item_photo_user " style=" background-image:url('<?php echo $defaultURL?>');"></i>
                                </a>
                            </div>
                            <div id="<?php echo $item['id'] ?>_page" class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">
                                <div class="sitecrowdfunding_leaders_cancel">
                                            <span class="sitecrowdfunding_link_wrap mright5">
                                                <a class="button smoothbox" href="<?php echo $this->escape($this->url(array( 'action' => 'remove-external-member', 'project_id' => $item['project_id'] , 'invite_id' => $item['id'] ), 'sitecrowdfunding_project_member', true)); ?>">
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
                                    <?php echo $this->translate('External Member'); ?>
                                </h2>
                                <?php if(!empty($item['project_role'])): ?>
                                <h2>
                                    <?php
                                        $roles_id = json_decode($item['project_role']);
                                        $roleName = array();
                                        foreach($roles_id as $role_id) {
                                            $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->getRoleName($role_id);
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


                </div>
                <br />
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>
<style type="text/css">
    .global_form > div > div{background:none;border:none;padding:0px;}
    .sitecrowdfunding_leaders_detail a:hover{
        color: #444
    }
    /*.pending_invites{*/
    /*    list-style-type: decimal;*/
    /*    padding: 10px;*/
    /*    margin-left: 10px;*/
    /*}*/
    /*.pending_invites_div{*/
    /*    padding: 20px;*/
    /*    box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);*/
    /*}*/
</style>
