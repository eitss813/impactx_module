<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    seaocore_content_type = '<?php echo $this->resource_type; ?>';
</script>


<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/follow.js');
?>
<?php
  $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php $minHeight=300;
if($this->sitepage->sponsored)
$minHeight =$minHeight +20;
if($this->sitepage->featured)
$minHeight =$minHeight +20;
?>
<div class="sitepage_cover_information_wrapper">

    <div class='sitepage_cover_wrapper' id="sitepage_cover_photo" style='min-height:<?php echo $minHeight;?>px; height:<?php echo (!empty($this->sitepage->page_cover) || !empty($this->can_edit)) ? $this->columnHeight:$minHeight; ?>px;'  >
    </div>
    <?php if($this->showContent):?>
    <div class="sitepage_cover_information b_medium">
        <?php if (in_array('mainPhoto', $this->showContent)): ?>
        <div class="sp_coverinfo_profile_photo_wrapper">
            <div class="sp_coverinfo_profile_photo b_dark">
                <?php if (!empty($this->sitepage->sponsored)): ?>
                <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
                if (!empty($sponsored)) { ?>
                <div class="sitepage_profile_sponsorfeatured" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                <?php echo $this->translate('SPONSORED'); ?>
            </div>
            <?php } ?>
            <?php endif; ?>
            <div class='sitepage_photo <?php if ($this->can_edit) : ?>sitepage_photo_edit_wrapper<?php endif; ?>'>
                <?php if (!empty($this->can_edit)) : ?>
                <a href="<?php echo $this->url(array('action' => 'profile-picture', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true) ?>" class="sitepage_photo_edit">
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/edit_pencil.png', '') ?>
                    <?php echo $this->translate('Change Picture'); ?>
                </a>
                <?php endif; ?>
                <table>
                    <tr valign="middle">
                        <td>
                            <?php echo $this->itemPhoto($this->sitepage, 'thumb.profile', '', array('align' => 'left')); ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?php if (!empty($this->sitepage->featured)): ?>
            <?php $feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.image', 1);
            if (!empty($feature)) { ?>
            <div class="sitepage_profile_sponsorfeatured"  style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.color', '#0cf523'); ?>;'>
            <?php echo $this->translate('FEATURED'); ?>
        </div>
        <?php } ?>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="sp_coverinfo_buttons" id="create_project_web">

    <!-- Create Project-->
    <?php if(empty($this->viewer_id)):?>

    <div class="seaocore_follow_button_wrap fleft initiative_edit_container" style="margin-left: 10px">
        <a  class="edit_btn seaocore_follow_button button user_auth_link" href="javascript:void(0);">
            <i class="seaocore_icon_edit"></i>
            <span><?php echo $this->translate('Edit') ?></span>
        </a>
    </div>
    <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
        <a  class="create_btn seaocore_follow_button button create_project_btn user_auth_link" href="javascript:void(0);">
            <i class="seaocore_icon_edit"></i>
            <span><?php echo $this->translate('Create Project') ?></span>
        </a>
    </div>

    <?php else: ?>
    <div class="seaocore_follow_button_wrap fleft initiative_edit_container" style="margin-left: 10px">
        <?php $editURL = $this->url(array('action' => 'overview', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true);?>
        <a  class="edit_btn seaocore_follow_button button" href="<?php echo $editURL; ?>">
            <i class="seaocore_icon_edit"></i>
            <span ><?php echo $this->translate('Edit') ?></span>
        </a>
    </div>
    <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
        <a class="create_btn seaocore_follow_button button create_project_btn user_auth_link" href='<?php echo $this->url(array('controller' => 'project-create', 'action' => 'step-zero', 'page_id' => $this->sitepage->page_id ), 'sitecrowdfunding_create_with_page', true) ?>'>
        <i class="seaocore_icon_edit"></i>
        <span ><?php echo $this->translate('Create Project') ?></span>
        </a>
    </div>

     <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
        <a class="create_btn seaocore_follow_button button create_project_btn user_auth_link" href='<?php echo $this->url(array( 'action' => 'create', 'org_id' => $this->sitepage->page_id ), 'sesblog_general', true) ?>'>
       <i class="fa fa-plus" aria-hidden="true" style="color: #333333"></i>
        <span ><?php echo $this->translate('Create Blog') ?></span>
        </a>
    </div>

    <?php endif; ?>

    <!-- Get Link-->
    <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
        <a class="create_btn seaocore_follow_button button create_project_btn" href='javascript:void(0);' onclick='javascript:showSmoothBox("shorturl/get-link/subject/sitepage_page_<?php echo $this->sitepage->page_id;?>")'>
            <i class="fa fa-link" style="color: #333333"></i>
            <span ><?php echo $this->translate('Get Link') ?></span>
        </a>
    </div>

    <div class="sitecoretheme_search">
        <div id="sitecoretheme_fullsite_search_org">
            <form id="organisation_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="post" class="search_form" target="_blank">
                <input type="hidden" id="page_no" name="page_no" value="1"/>
                <input type="hidden" id="tab_link" name="tab_link" value="all_tab"/>
                <input type="hidden" id="searched_from_page" name="searched_from_page" value="organisation"/>
                <input type="hidden" id="searched_from_page_id" name="searched_from_page_id" value="<?php echo $this->sitepage->page_id; ?>" />
                <input type="hidden" id="searched_from_initiative_id" name="searched_from_initiative_id"  value=null />
                <input type="hidden" id="searched_from_project_id" name="searched_from_project_id" value=null />
                <input type="text"   id='global_search_org_field' name='query' autocomplete="off" style="height: 28px !important;width: 147px;" placeholder="<?php echo $this->translate("Search here...") ;?> "/>
                <input type="hidden" id="type" name="type" value="everything_in_organization"/>
                <input type="hidden" id="sdg_goal_id" name="sdg_goal_id" value=null />
                <input type="hidden" id="sdg_target_id" name="sdg_target_id" value=null />
                <input type="hidden" id="category_id" name="category_id" value=null />
                <input type="hidden" id="search_only_in_project" name="search_only_in_project" value=true />
                <button style="height: 30px !important;" id="responsive_search_toggle_search" class="responsive_search_toggle_search" >
                    <i style="display: flex;justify-content: center" class="fa fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <?php if($this->viewer_id):?>
        <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
            <a class="create_btn seaocore_follow_button button create_project_btn" href="<?php echo $this->url(array('action' => 'home' ), 'user_general', true) ?>">
                <i class="fa fa-home" style="color: #333333"></i>
                <span ><?php echo $this->translate('Activities') ?></span>
            </a>
        </div>
    <?php else:?>
        <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
            <a class="create_btn seaocore_follow_button button create_project_btn" href="<?php echo 'pages/activities' ?>">
                <i class="fa fa-home" style="color: #333333"></i>
                <span ><?php echo $this->translate('Activities') ?></span>
            </a>
        </div>
    <?php endif;?>


    <?php /*if (in_array('likeButton', $this->showContent)): ?>
    <div>
        <?php echo $this->content()->renderWidget("seaocore.like-button"); ?>
    </div>
    <?php endif;*/ ?>
    <?php /* if (in_array('followButton', $this->showContent)): ?>
    <div>
        <?php echo $this->content()->renderWidget("seaocore.seaocore-follow"); ?>
    </div>
    <?php endif;*/ ?>
    <!--<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
              <?php $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id);
              if (empty($joinMembers) && in_array('joinButton', $this->showContent) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
                  <div>
                  <?php if (!empty($this->viewer_id)) : ?>
                      <?php if (!empty($this->sitepage->member_approval)): ?>
                          <a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'join', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa plus_icon"></i><span><?php echo $this->translate("Join Page"); ?></span></a>
                      <?php else: ?>
                          <a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'request', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa plus_icon"></i><span><?php echo $this->translate("Join Page"); ?></span></a>
                      <?php endif; ?>
                  <?php endif; ?>
                  </div>
              <?php endif; ?>-->


    <!-- <?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = "Leave");
     if (!empty($hasMembers) && in_array('leaveButton', $this->showContent) && $this->viewer_id != $this->sitepage->owner_id && !empty($this->allowPage)): ?>
                 <div>
         <?php if ($this->viewer_id) : ?>
                         <a  class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'leave', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa plus_icon"></i><span><?php echo $this->translate("Leave Page"); ?></span></a>
         <?php endif; ?>
                 </div>
             <?php endif; ?>

             <?php if (in_array('addButton', $this->showContent)): ?>
                 <?php $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($this->viewer_id, $this->sitepage->page_id, $params = 'Invite'); ?>
                 <?php if (!empty($hasMembers) && !empty($this->can_edit)) : ?>
                 <div>
                     <a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
                 </div>
                 <?php elseif (!empty($hasMembers) && empty($this->sitepage->member_invite)): ?>
                 <div>
                     <a class="sitepage_button" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepagemember', true)); ?>'); return false;" ><i class="fa add_people"></i><span><?php echo $this->translate("Add People"); ?></span></a>
                 </div>
                 <?php endif; ?>
             <?php endif; ?>
         <?php endif; ?>-->

    <!-- <div class="seaocore_profile_option_btn main_project_info_setting">
         <a href="javascript:void(0);" onclick="showPulDownOptions();"><i class="icon_cog"></i></a>
         <ul class="seaocore_profile_options_pulldown" id="menu_settings_options_pulldown" style="display:none;">
             <li>
                 <?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'action' => 'overview', 'page_id' => $this->sitepage->page_id), $this->translate("Edit Page"), array('class' => 'buttonlink seaocore_icon_edit')); ?>
             </li>
             <li>
                 <?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'action' => 'get-link', 'page_id' => $this->sitepage->page_id), $this->translate("Get Link"), array('class' => 'buttonlink smoothbox seao_icon_sharelink_square')); ?>
             </li>
         </ul>
     </div> -->

    <?php /*
        <?php if(!empty($sitepage->declined) ): ?>
    <button class="page_declined_btn"><?php echo $this->translate("Declined")?></button>
    <?php endif; ?>

    <?php if( !empty($sitepage->pending) ): ?>
    <button class="page_approval_pending_btn"><?php echo $this->translate("Approval Pending")?></button>
    <?php endif; ?>

    <?php if( !empty($sitepage->approved) ): ?>
    <button class="page_approved_btn"><?php echo $this->translate("Approved")?></button>
    <?php endif; ?>

    <?php if( empty($sitepage->approved) ): ?>
    <button class="page_dis_approved_btn"><?php echo $this->translate("Dis-Approved")?></button>
    <?php endif; ?>
    */ ?>

    <!-- <div class="section_header_info">
         <div class="status_container">
             <h3 class="status_text_custom"><?php echo $this->sitepage->state; ?></h3>
         </div>
     </div> -->


</div>
<div class="sp_coverinfo_status">
    <?php if (in_array('title', $this->showContent)): ?>
    <h2><?php echo $this->sitepage->getTitle() ?></h2>
    <?php if ($this->isVerified): ?>
    <img class="verify_icon" src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/verify.png'; ?>">
    <span class="sitepage_tip">Verified Page<i></i></span>
    <style type="text/css">
        .sp_coverinfo_status .verify_icon { padding: 0px 5px; height: 1em; }
        .sp_coverinfo_status>h2 { display: inline-block; }
        .sp_coverinfo_status .sitepage_tip { margin-left: 5px !important; background-color: rgba(0,0,0,0.8); border-radius: 4px; color: #fff; box-shadow: 1px 0px 1px 1px #ababab; }
        .verify_icon:hover + .sitepage_tip { display: inline-block; }
    </style>
    <?php endif; ?>
    <?php endif; ?>

    <div class="sp_coverinfo_stats seaocore_txt_light">

        <?php $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding'); ?>
        <?php $projects = $pagesTable->getPageProjects($this->sitepage->page_id); ?>
        <!-- <?php $projectsCount = count($projects); ?> -->
        <?php
         $allPartnerPages = Engine_Api::_()->getDbtable('pages', 'sitepage')->getPageDetailsWithProjectsCustomCountForPageIds($this->sitepage->page_id);
         $projectsCount = count($allPartnerPages) > 0 ? $allPartnerPages[0]->projects_count : 0;
        ?>
        <?php $admin = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminUserLocation($this->sitepage->page_id); ?>
        <?php $adminCount = count($projects); ?>

        <?php $partnerCount = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPagesCount($this->sitepage->page_id); ?>

        <a class="followers"  href="javascript:void(0);">
            <?php echo $this->translate(array('%s followers', '%s followers', $this->follow_count),$this->locale()->toNumber($this->follow_count)); ?>
        </a>
        &middot;
        <a class="members"  href="javascript:void(0);">
            <?php echo $this->translate(array('%s members', '%s members', $this->member_count),$this->locale()->toNumber($this->member_count)); ?>
        </a>
        &middot;
        <?php /*
            <a href="javascript:void(0);">
        <?php echo $this->translate(array('%s admin', '%s admin', $adminCount),$this->locale()->toNumber($adminCount)); ?>
        </a>
        &middot;
        */ ?>
        <a class="projects" href="javascript:void(0);">
            <?php echo $this->translate(array('%s projects', '%s projects', $projectsCount),$this->locale()->toNumber($projectsCount)); ?>
        </a>

        <?php /*
            <a href="javascript:void(0);">
        <?php echo $this->translate(array('%s partners', '%s partners', $partnerCount),$this->locale()->toNumber($partnerCount)); ?>
        </a>
        &middot;
        */ ?>
        <div class="test">
            <!-- Follow Button -->
            <div class="sitepage_follow_edit_btns" id="create_project_mobile">

                <!-- Get Link-->
                <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
                    <a class="create_btn seaocore_follow_button button create_project_btn" href='javascript:void(0);' onclick='javascript:showSmoothBox("/shorturl/get-link/subject/sitepage_page_<?php echo $this->sitepage->page_id;?>")'>
                        <i class="fa fa-link" style="color: #333333"></i>
                        <span ><?php echo $this->translate('Get Link') ?></span>
                    </a>
                </div>

                <!-- Create Project-->
                <?php if(empty($this->viewer_id)):?>
                <div class="seaocore_follow_button_wrap fleft initiative_edit_container" style="margin-left: 10px">
                    <a class="edit_btn seaocore_follow_button button user_auth_link" href="javascript:void(0);">
                        <i class="seaocore_icon_edit"></i>
                        <span><?php echo $this->translate('Edit') ?></span>
                    </a>
                </div>
                <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
                    <a  class="create_btn seaocore_follow_button button create_project_btn user_auth_link" href="javascript:void(0);">
                        <i class="seaocore_icon_edit"></i>
                        <span><?php echo $this->translate('Create Project') ?></span>
                    </a>
                </div>

                <?php else: ?>
                <div class="seaocore_follow_button_wrap fleft initiative_edit_container" style="margin-left: 10px">
                    <?php $editURL = $this->url(array('action' => 'overview', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true);?>
                    <a class="edit_btn seaocore_follow_button button" href="<?php echo $editURL; ?>">
                        <i class="seaocore_icon_edit"></i>
                        <span ><?php echo $this->translate('Edit') ?></span>
                    </a>
                </div>
                <div class="seaocore_follow_button_wrap fleft initiative_edit_container">
                    <a class="create_btn seaocore_follow_button button create_project_btn user_auth_link" href='<?php echo $this->url(array('controller' => 'project-create', 'action' => 'step-zero', 'page_id' => $this->sitepage->page_id ), 'sitecrowdfunding_create_with_page', true) ?>'>
                    <i class="seaocore_icon_edit"></i>
                    <span><?php echo $this->translate('Create Project') ?></span>
                    </a>
                </div>

                <?php endif; ?>

                <?php if(empty($this->viewer_id)):?>

                <div class="seaocore_follow_button_wrap fleft">
                    <a  style="border: 1px solid #44AEC1 !important;" class="seaocore_follow_button button user_auth_link" href="javascript:void(0);">
                        <i class="follow"></i>
                        <span style="color: white !important;"><?php echo $this->translate('Follow') ?></span>
                    </a>
                </div>



                <?php else: ?>

                <!-- Follow Button -->
                <div class="seaocore_follow_button_wrap fleft button seaocore_follow_button_active" id="<?php echo $this->resource_type ?>_unfollows_<?php echo $this->resource_id;?>" style =' display:<?php echo $this->isFollow ?"inline-block":"none"?>' >
                    <a style="border: 1px solid #44AEC1 !important;" class="seaocore_follow_button button seaocore_follow_button_following" href="javascript:void(0);">
                        <i class="following"></i>
                        <span style="color: white!important;"><?php echo $this->translate('Following') ?></span>
                    </a>
                    <a  class="seaocore_follow_button button seaocore_follow_button_unfollow" href="javascript:void(0);" onclick = "seaocore_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
                        <i class="unfollow"></i>
                        <span style="color: white!important;"><?php echo $this->translate('Unfollow') ?></span>
                    </a>
                </div>

                <div class="seaocore_follow_button_wrap fleft" id="<?php echo $this->resource_type ?>_most_follows_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($this->isFollow) ?"inline-block":"none"?>'>
                    <a style="border: 1px solid #44AEC1 !important;" class="seaocore_follow_button button" href="javascript:void(0);" onclick = "seaocore_content_type_follows('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
                        <i class="follow"></i>
                        <span style="color: white!important;"><?php echo $this->translate('Follow') ?></span>
                    </a>
                </div>

                <input type ="hidden" id = "<?php echo $this->resource_type; ?>_follow_<?php echo $this->resource_id;?>" value = '<?php echo $this->isFollow ? $this->isFollow :0; ?>' />

                <?php /*
                        <div class="seaocore_follower_count fleft"  id= "<?php echo $this->resource_type ?>_num_of_follow_<?php echo $this->resource_id;?>">
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->follow_count),$this->locale()->toNumber($this->follow_count)); ?></a>
            </div >
            */ ?>



            <?php endif; ?>

        </div>




    </div>

</div>

<?php /*
      <div class="sp_coverinfo_stats seaocore_txt_light">
<?php if (in_array('likeCount', $this->showContent) && isset($this->sitepage->like_count)): ?>
<a id= "sitepage_page_num_of_like_<?php echo $this->sitepage->page_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => 'sitepage_page', 'resource_id' => $this->sitepage->page_id, 'call_status' => 'public'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s like', '%s likes', $this->sitepage->like_count),$this->locale()->toNumber($this->sitepage->like_count)); ?></a>
<?php endif; ?>

<?php if (in_array('followCount', $this->showContent) && isset($this->sitepage->follow_count)): ?>
<?php if (in_array('likeCount', $this->showContent) && isset($this->sitepage->like_count)): ?>
&middot;
<?php endif; ?>
<a id= "sitepage_page_num_of_follows_<?php echo $this->sitepage->page_id;?>" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'follow', 'action'=>'get-followers', 'resource_type'	=> 'sitepage_page', 'resource_id' => $this->sitepage->page_id, 'format' => 'smoothbox', 'call_status' => 'public'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s follower', '%s followers', $this->sitepage->follow_count),$this->locale()->toNumber($this->sitepage->follow_count)); ?></a>
<?php endif; ?>

<?php if (in_array('memberCount', $this->showContent) && isset($this->sitepage->member_count) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
<?php //if (in_array('likeCount', $this->statistics) && isset($this->sitepage->like_count)): ?>
&middot;
<?php //endif; ?>
<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
if ($this->sitepage->member_title && $memberTitle) { ?>
<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->sitepage->member_count . ' ' .  $this->sitepage->member_title;?></a>
<?php } else { ?>
<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action'=>'member-join', 'page_id' => $this->sitepage->page_id, 'params' => 'memberJoin', 'format' => 'smoothbox'), 'sitepagemember_approve'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s member', '%s members', $this->sitepage->member_count),$this->locale()->toNumber($this->sitepage->member_count)); ?></a>
<?php 	} ?>
<?php endif; ?>

</div>
*/ ?>
</div>
</div>
<?php endif; ?>
</div>
<div class="clr"></div>
<script type="text/javascript">
    document.seaoCoverPhoto= new SitepageCoverPhoto({
        block :$('sitepage_cover_photo'),
        photoUrl:'<?php echo $this->url(array('action' => 'get-cover-photo', 'page_id' => $this->sitepage->page_id), 'sitepage_profilepage', true); ?>',
        buttons:'seao_cover_options',
        positionUrl :'<?php echo $this->url(array('action' => 'reset-position-cover-photo', 'page_id' => $this->sitepage->page_id), 'sitepage_dashboard', true); ?>',
        position :<?php  echo $this->cover_params ? json_encode($this->cover_params): json_encode(array('top' => 0, 'left' => 0)); ?>
    });
    var $j = jQuery.noConflict();

    $j(".responsive_search_toggle_search").click(function() {
        $j('#sdg_target_id').val(null);
        $j('#sdg_goal_id').val(null);
    });
    $j(".followers").click(function() {
        $j('html,body').animate({
                scrollTop: $j("#scroll_link").offset().top - 70},
            'slow');
    });
    $j(".members").click(function() {
        $j('html,body').animate({
                scrollTop: $j("#scroll_link").offset().top - 70},
            'slow');
    });
    $j(".projects").click(function() {
        $j('html,body').animate({
                scrollTop: $j("#scroll_link_project").offset().top - 75},
            'slow');
    });

    // bind search i/p
    window.addEvent('domready', function() {

        var requestURL = '<?php echo $this->url(array('module' => 'sitecoretheme', 'controller' => 'general', 'action' => 'get-search-content'), "default", true) ?>'+'?page_id= '+'<?php echo $this->sitepage->page_id?>';
        console.log('requestURL',requestURL);
        contentAutocomplete = new Autocompleter.Request.JSON('global_search_org_field', requestURL, {
            'postVar': 'text',
            'cache': false,
            'minLength': 1,
            'selectFirst': false,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest adsearch-autosuggest adsearch-stoprequest',
            'maxChoices': 8,
            'indicatorClass': 'vertical-search-loading',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                console.log('token --',token);
                if (typeof token.label != 'undefined') {
                    var seeMoreText = '<?php echo $this->string()->escapeJavascript($this->translate('See more results for') . ' '); ?>';
                    if (token.type == 'no_resuld_found') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'id': 'sitecoretheme_search_' + token.type});
                        new Element('div', {'html': token.label, 'class': 'autocompleter-choicess'}).inject(choice);
                        choice.inject(this.choices);
                        choice.store('autocompleteChoice', token);
                        return;
                    }
                    if (token.item_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'item_url': token.item_url, onclick: 'javascript: showSearchResultPage("' + token.item_url + '")'});
                        var divEl = new Element('div', {
                            'html': token.type ? this.options.markQueryValueCustom.call(this, (token.label)) : token.label,
                            'class': 'autocompleter-choice'
                        });

                        new Element('div', {
                            'html': token.type, //this.markQueryValue(token.type)
                            'class': 'seaocore_txt_light f_small'
                        }).inject(divEl);

                        divEl.inject(choice);
                        new Element('input', {
                            'type': 'hidden',
                            'value': JSON.encode(token)
                        }).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.item_url == 'seeMoreLink') {
                        var titleAjax1 = encodeURIComponent($('global_search_org_field').value);
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': '', 'id': 'stopevent', 'item_url': ''});
                        new Element('div', {'html': seeMoreText + '"' + titleAjax1 + '"', 'class': 'autocompleter-choicess', onclick: 'javascript:seeMoreSearchResults()'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                }
            },
            markQueryValueCustom: function (str) {
                return (!this.options.markQuery || !this.queryValue) ? str
                    : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<b>$1</b>');
            },
        });

        $('global_search_org_field').addEvent('keydown', function (event) {
            if (event.key == 'enter') {
                $('sitecoretheme_fullsite_search_org').submit();
            }
        });
    });
</script>

<script type="text/javascript">
    function showSmoothBox(url) {
        Smoothbox.open(url);
    }
    function showPulDownOptions() {
        if ($('menu_settings_options_pulldown').style.display == 'none') {
            $('menu_settings_options_pulldown').style.display = "block";
        } else {
            $('menu_settings_options_pulldown').style.display = "none";
        }
    }


    // called when page loaded
    window.addEvent('domready', function () {
        // delete the above search form
        $j('#sitecoretheme_fullsite_search').remove();
    });

</script>
<style>
    .edit_btn,.create_btn {
        background-color: #f9f9f9 !important;
        border: 1px solid #0b0b0b !important;
        color: black !important;
    }
    .edit_btn > .seaocore_icon_edit,
    .create_btn > .seaocore_icon_edit{
        color: black !important;
    }
    .edit_btn.button:hover,
    .create_btn.button:hover{
        background-color: unset !important;
    }
    .main_project_info_setting{
        margin-top: 3px;
        margin-left: 10px !important;
        margin-right: 10px !important;
    }
    .main_project_info_setting ul {
         padding: 5px;
         box-sizing: border-box;
    }
    .status_container {
        background: gray;
        border-radius: 5%;
        min-width: 100px;
        text-align: center;
    }
    .status_text_custom {
        font-size: 18px;
        color: white;
        padding: 4px;
        text-transform: uppercase;
    }
    #create_project_mobile > .initiative_edit_container {
        display: none;
    }
    .generic_layout_container.layout_sitepage_page_profile_navigator {
        display: none !important;
    }
    .seaocore_follow_button_wrap.fleft {
        margin-left: 10px;
        cursor: pointer;
    }
    .sp_coverinfo_buttons >  .sitecoretheme_search {
         float: right;
         margin-left: 10px;
         display: flex;
         align-items: center;
     }
    .sp_coverinfo_buttons > div {
        float: right !important;
    }
    a.seaocore_follow_button.button.seaocore_follow_button_following {
        margin-top: 5px;
    }
    @media (max-width: 767px) {

        div#sitecoretheme_fullsite_search_org {
            display: none;
        }

        a.create_btn.seaocore_follow_button.button.create_project_btn {
            display: none;
        }
        a.edit_btn.seaocore_follow_button.button {
            display: none;
        }
        .sitecoretheme_search {
            margin-top: 25%;
            margin-right: 4% !important;
        }
        #follow_mobile{
            position: absolute;
            top: 40px;
        }
        div#create_project_mobile{
            height:100px;
        }
        #edit-button-view{
            position: unset !important;
        }
        #create_project_web > .initiative_edit_container {
            display: none;
        }
        #create_project_mobile > .initiative_edit_container {
            display: block !important;
        }
        div#create_project_mobile {
            position: relative;
            left: 5px;
        }
        div#sitepage_page_most_follows_7 {
            left: 0px;
            position: absolute;
            top: 40px;
            /*left: 5px;*/
            /*position: relative;*/
        }
        div#sitepage_page_unfollows_7 {
            /*position: relative;*/
            /*left: 5px;*/
            /* position: relative; */
            left: 0px;
            position: absolute;
            top: 40px;
        }
        }
    }
    /*button color for slide cover*/
    a.seaocore_follow_button.button {
        padding: 4px 5px !important;
        height: 28px !important;
        font-weight: unset !important;
    }
    ul.tag-autosuggest {
        margin-top: 22px;
        max-height: 200px;
        overflow-y: auto !important;
    }
</style>