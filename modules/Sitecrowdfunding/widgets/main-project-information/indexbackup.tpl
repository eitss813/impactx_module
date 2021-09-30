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
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.css' ?>" rel="stylesheet">
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/styles/custom.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.js' ?>"></script>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>

<div class="main_project_info">
    <div class="main_project_common_container">
        <div class="main_project_common_sub_container">
            <div class="main_project_info_container">
                <div class="main_project_info_icon" title="<?php echo $this->parentOrganization['title'] ?>">
                    <?php $src = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
                    <img src="<?php echo !empty($this->parentOrganization['logo']) ? $this->parentOrganization['logo'] : $src ?>" alt="">
                    <?php if(!empty($this->parentOrganization['title']) ): ?>
                    <a class="main_project_parent_title" href="<?php echo !empty($this->parentOrganization['link']) ? $this->parentOrganization['link'] : 'javascript:void(0);'  ?>" > <b>Organisation Name :</b> <?php echo $this->parentOrganization['title'] ?></a>
                    <?php endif; ?>
                </div>

                <!--<div class="main_project_info_icon">
                     <?php if (in_array('category', $this->projectOption)) : ?>
                    <?php $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $this->project->category_id); ?>
                    <?php if ($category->file_id): ?>
                    <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
                    <?php echo $this->itemPhoto(Engine_Api::_()->storage()->get($category->file_id), null, null, array('style' => 'width: 16px; height: 16px;'));?>
                    <?php elseif ($category->font_icon): ?>
                    <i class="fa <?php echo $category->font_icon; ?>"></i>
                    <?php else: ?>
                    <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
                    <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
                    <?php endif; ?>
                    <p><?php echo $this->htmlLink($category->getHref(), $category->getTitle()) ?></p>
                    <?php endif; ?>
                </div> -->
                <div class="main_project_info_desc">
                    <?php if (in_array('title', $this->projectOption)) : ?>
                    <div class="main_project_info_title">
                        <h3>
                            <?php //echo $this->htmlLink($this->project->getHref(), $this->string()->truncate($this->string()->stripTags($this->project->getTitle()), $this->titleTruncation), array('title' => $this->project->getTitle())) ?>
                            <a style="cursor: default" href="javascript:void(0);"><?php echo $this->project->getTitle(); ?></a>
                        </h3>
                    </div>
                    <?php endif; ?>

                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1) && in_array('location', $this->projectOption) && $this->project->location) : ?>
                    <div class="main_project_owner_location mbot5" title="<?php echo $this->project->location ?>">
                        <i class="seao_icon_location"></i>
                        <?php echo $this->string()->truncate($this->string()->stripTags($this->project->location)); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (in_array('shareOptions', $this->projectOption)) : ?>
                    <div class="main_project_info_socialshare_btns">
                        <?php echo $this->sitecrowdfundingShareLinksCustom($this->project, array('facebook', 'twitter', 'linkedin','community')); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="main_project_sub_info_desc">
                <?php if(!empty($this->project->desire_desc) || !empty($this->project->help_desc)): ?>
                <ul style="list-style-type:none;padding-left: 10px;">
                    <?php if(!empty($this->project->desire_desc)): ?>
                    <li>
                        <?php echo $this->project->desire_desc; ?>
                    </li>
                    <?php endif; ?>
                    <?php /*if(!empty($this->project->help_desc)): ?>
                    <li>
                        <?php echo $this->project->help_desc; ?>
                    </li>
                    <?php endif;*/ ?>
                </ul>
                <?php endif; ?>
                <?php /* if (in_array('description', $this->projectOption)) : ?>
                <div title="<?php echo $this->string()->truncate($this->string()->stripTags($this->project->description), 250) ?>">
                    <?php echo $this->string()->truncate($this->string()->stripTags($this->project->description), $this->descriptionTruncation) ?>
                </div>
                <?php endif; */?>
            </div>
        </div>


        <div class="main_project_info_options">

            <div class="main_project_info_status">
                <?php //echo $this->project->isFundingApproved() ? $this->content()->renderWidget("sitecrowdfunding.project-funding-status") : $this->content()->renderWidget("sitecrowdfunding.project-status"); ?>
                <?php echo $this->content()->renderWidget("sitecrowdfunding.project-status"); ?>
            </div>

            <div class="main_project_sub_info_funding">
                <?php if($this->project->is_fund_raisable): ?>
                <?php
                $fundedAmount = $this->project->getFundedAmount();
                $fundedRatio = $this->project->getFundedRatio();
                $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
                $goalAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->project->goal_amount);
                ?>
                <?php if (in_array('fundedAmount', $this->projectOption)) : ?>
                <div class="main_project_info_amount">
                    <?php echo $fundedAmount; ?> of <span><?php echo $goalAmount; ?></span> goal
                    <!--<span><?php echo $fundedAmount; ?></span> <?php echo $this->translate("of %s goal", $goalAmount); ?>-->
                </div>
                <?php endif; ?>
                <?php
                // Add Progressvive bar
                if (in_array('fundingRatio', $this->projectOption)) {
                echo $this->fundingProgressiveBar($fundedRatio);
                }
                ?>
                <ul class="backed_amount">
                    <?php if (in_array('backerCount', $this->projectOption)) : ?>
                    <li>
                        <a class="see_all_backers_btn" onclick="seeAllBackers()" href="javascript:void(0);">
                            <?php //echo $this->translate("Funded by %s people", $this->project->backer_count); ?>
                            <?php echo $this->translate("Funded by %s people", $this->memberCount); ?>
                            <?php if($this->orgCount > 0): ?>
                            <?php echo $this->translate(" and %s organization", $this->orgCount); ?>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (in_array('daysLeft', $this->projectOption)) : ?>
                    <li class="fright project_status_info">

                        <?php $days = Engine_Api::_()->sitecrowdfunding()->findDays($this->project->funding_end_date); ?>
                        <?php $daysToStart = Engine_Api::_()->sitecrowdfunding()->findDays($this->project->funding_start_date); ?>
                        <?php
                        $currentDate = date('Y-m-d');
                        $projectStartDate = date('Y-m-d', strtotime($this->project->funding_start_date));
                        ?>
                        <?php if ($this->project->state == 'successful') : ?>
                        <?php echo $this->translate("Funding Successful"); ?>
                        <?php elseif ($this->project->state == 'failed') : ?>
                        <?php echo $this->translate("Funding Failed"); ?>
                        <?php elseif ($this->project->state == 'draft') : ?>
                        <?php echo $this->translate("In Draft mode"); ?>
                        <?php elseif (strtotime($currentDate) < strtotime($projectStartDate)): ?>
                        <?php echo $daysToStart; ?>
                        <?php echo $this->translate(array('%s Day to Live', '%s Days to Live', $daysToStart), ''); ?>
                        <?php elseif ($this->project->lifetime): ?>
                        <?php echo $this->translate('Life Time'); ?>
                        <?php elseif ($days >= 1): ?>
                        <?php echo $days; ?>
                        <?php echo $this->translate(array('%s Day Left', '%s Days Left', $days), ''); ?>
                        <?php else: ?>
                        <?php echo $this->translate($this->project->getProjectFundingStatus()); ?>
                        <?php endif; ?>
                    </li>
                </ul>
                <?php endif; ?>
                <?php endif; ?>
                <ul class="backed_amount has_favourite_label">
                    <?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($this->project->getType(), $this->project->getIdentity()); ?>
                    <?php if($hasFavourite):?>
                    <li><h3><?php echo $this->translate("You are following."); ?></h3></li>
                    <?php endif; ?>
                </ul>
                <ul class="backed_amount has_joined_label">
                    <?php $isMemberJoined = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->isMemberJoined($this->project->getIdentity()); ?>
                    <?php if($this->viewer_id != $this->project->owner_id): ?>
                    <?php if($isMemberJoined):?>
                    <li><h3><?php echo $this->translate("You are now a member."); ?></h3></li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="main_project_info_options_inside">
                <div>
                    <div class="main_project_info_follow_join_count_container">

                        <div class="main_project_info_follow_count">
                            <h2 class="follow">Followers</h2>
                            <h2 id="<?php echo $this->resource_type.'_total_no_of_favourite_'.$this->resource_id; ?>" class="follow_count"><?php echo $this->noOfFollowingCount; ?></h2>
                        </div>
                        <!--<div class="main_project_info_members_count">
                            <h2 class="follow">Members</h2>
                            <h2 id="<?php echo $this->resource_type.'_total_no_of_member_'.$this->resource_id; ?>" class="follow_count"><?php echo $this->noOfMembersCount; ?></h2>
                        </div>-->
                        <?php
                            $projectStartDate = date('Y-m-d', strtotime($this->project->funding_start_date));
                        $currentDate = date('Y-m-d');
                        if($this->project->is_fund_raisable): ?>
                        <div class="main_project_info_members_count">
                            <h2 class="backer">Funders</h2>
                            <h2 class="backer_count">
                                <?php echo $this->total_backer_count; ?>
                            </h2>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="main_project_info_follow_join_btn_container">
                            <?php if (in_array('shareOptions', $this->projectOption)) : ?>
                            <?php echo $this->sitecrowdfundingCustomBtn($this->project, array('favourite','back-btn')); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- <div>
                    <?php if($this->project->isFundingApproved()): ?>
                        <div class="main_project_info_members_count">
                            <h2 class="backer">Backers</h2>
                            <h2 class="backer_count"><?php echo $this->project->backer_count; ?></h2>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array('backButton', $this->projectOption)) : ?>
                    <?php echo $this->content()->renderWidget("sitecrowdfunding.back-project", array("title" => 'Back This Project',"backTitle" => "Donate Now")); ?>
                    <?php endif; ?>
                </div> -->

            </div>

            <?php if($this->viewer_id): ?>
            <?php $backedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->project->getFundedAmount(true)); ?>
            <?php if($backedAmount) : ?>
            <div class="donated_msg">
                <h2><?php echo $this->translate('You have donated '. $backedAmount. ' to this project'); ?></h2>
            </div>
            <?php endif;?>
            <?php endif; ?>

            <?php /*if(!$this->project->is_gateway_configured): ?>
            <div class="donated_msg">
                <h2><?php echo $this->translate('Payments information missing.'); ?></h2>
            </div>
            <?php endif; */ ?>

        </div>

    </div>
    <?php if (in_array('optionsButton', $this->projectOption)): ?>
    <?php $coreMenus = Engine_Api::_()->getApi('menus', 'core');
    $this->navigationProfile = $coreMenus->getNavigation("sitecrowdfunding_project_profile");
    ?>
    <?php if (count($this->navigationProfile) > 0): ?>
    <div class="seaocore_profile_option_btn main_project_info_setting">
        <a href="javascript:void(0);" onclick="showPulDownOptions();" style="display: none"><i class="icon_cog"></i></a>
        <ul class="seaocore_profile_options_pulldown" id="menu_settings_options_pulldown" style="display:none;">
            <li>
                <?php echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setPartial(array('_navIcons.tpl', 'sitecrowdfunding'))->render(); ?>
            </li>
        </ul>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</div>

<div class="main_project_photo_map_info">
    <div class="main_project_photo_video_info">
        <?php
        if ($this->showPhoto) :
        $photoUrl = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
        if ($this->project->photo_id) {
        $photoUrl = $this->project->getPhotoUrl();
        }
        ?>
        <div>
            <div id="sitecrowdfunding_edit_media_thumb_custom" class="sitecrowdfunding_edit_media_thumb " style="height: 470px">
                <?php //echo $this->htmlLink($this->project->getHref(), "<img style='height:" . $this->columnHeight . "px' " . "  src='" . $photoUrl . "'>") ?>
                <img id="main_photo_custom_id" style='height:470px;display: none' src="<?php echo $photoUrl; ?>" />
                <img id="display_photo_custom_id" style='height:470px' src="<?php echo $this->project->getPhotoUrl('thumb.cover'); ?>" />
                <div class="cover_tip_wrap ">
                    <div class="cover_tip drag_img_custom">Drag to Reposition Cover Photo</div>
                </div>
            </div>
            <?php $canEdit = Engine_Api::_()->sitecrowdfunding()->isEditPrivacy($this->project->parent_type, $this->project->parent_id, $this->project); ?>
            <?php if($canEdit): ?>
            <div style="margin-top: 10px;margin-bottom: 10px">
                <button id="set_position_custom">Reposition</button>
                <button id="save_position_custom">Save</button>
                <button id="cancel_position_custom">Cancel</button>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="sitecrowdfunding_edit_media_thumb sitecrowdfunding_edit_media_thumb_video" style="height: <?php echo $this->columnHeight; ?>px">
            <?php
                if ($this->video->photo_id)
            $photoUrl = $this->itemPhoto($this->video, '', '', array('style' => "height:{$this->columnHeight}px;"));
            else
            $photoUrl = "<img src='" . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png' . "' style='height: {$this->columnHeight}px'>";
            ?>
            <a href='<?php echo $this->video->getHref(); ?>' target="_blank">
                <?php echo $photoUrl; ?>
                <div class="main_project_info_video"><div><i class="fa fa-play-circle"></i></div></div>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <div class="main_project_map_info">
        <h3 class="overview_custom_title">About The Project</h3>
        <?php echo $this->content()->renderWidget("sitecrowdfunding.project-overview"); ?>
    </div>
</div>
<script type="text/javascript">

    function showPulDownOptions() {
        var parent = $('menu_settings_options_pulldown').getParent('.seaocore_profile_option_btn');
//        if (parent) {
//            var rightPostion = document.body.getCoordinates().width - parent.getCoordinates().left - parent.getCoordinates().width;
//            $('menu_settings_options_pulldown').inject(document.body);
//            $('menu_settings_options_pulldown').setStyles({
//                'position': 'absolute',
//                'top': parent.getCoordinates().bottom,
//                'right': rightPostion
//            });
//        }
//
        if ($('menu_settings_options_pulldown').style.display == 'none') {
            $('menu_settings_options_pulldown').style.display = "block";
        } else {
            $('menu_settings_options_pulldown').style.display = "none";
        }
        document.body.removeEvents('click').addEvent('click', function (event) {
            if ($('menu_settings_options_pulldown').style.display == 'block' && event.target != '' && event.target.id != 'polldown_options_cover_photo' && event.target.className != 'icon_down' && event.target.className != 'icon_cog') {
                $('menu_settings_options_pulldown').style.display = 'none';
            }
        });

    }
    var $j = jQuery.noConflict();
    function seeAllBackers() {
        btnBackers = $$('.layout_sitecrowdfunding_project_backers_button');
        btnBackers[0].click()
        setTimeout(function () {
            $j('html, body').animate({
                scrollTop: $j(`.layout_sitecrowdfunding_project_backers`).offset().top - 70
            }, 1000);
        })
        // backerWidget = $$('.tab_layout_sitecrowdfunding_project_backers');
        // if (backerWidget && backerWidget[0] && backerWidget[0].getElement('a')) {
        //     backerWidget[0].getElement('a').click();
        //     var myElement = $(document.body);
        //     var myFx = new Fx.Scroll(myElement).start(0, $$('.layout_sitecrowdfunding_main_project_information')[0].offsetHeight);
        // }
    }
    window.addEvent('domready', function () {
        backerWidget = $$('.tab_layout_sitecrowdfunding_project_backers');
        if (!(backerWidget && backerWidget[0] && backerWidget[0].getElement('a'))) {
            //$('seeAllBackers').hide();
        }
    });
</script>
<style>
    .overview_custom_title{
        border-bottom: 1px solid #f2f0f0;
        padding-bottom: 5px;
        padding-top: 5px;
    }
    .donated_msg{
        display: flex;
        justify-content: center;
        padding: 10px;
    }
    .main_project_info_img_tag{
        width: 150px;
        height: 150px;
        /* padding-right: 10px; */
        border-radius: 6px;
        margin-right: 10px
    }
    .main_project_info_status{
        display: flex;
        justify-content: flex-end;
    }
    .main_project_info{
        border-bottom: 1px dashed #d9d8d8
    }
    .main_project_common_container{
        display: flex !important;
        width: 100%;
    }
    .main_project_common_sub_container{
        width: 65%;
        border-right: 1px dashed #d9d8d8;
    }
    .backed_amount{
        width: 100%;
        display: flex !important;
        justify-content: space-between !important;
    }
    .main_project_info_title{
        margin-top: 5px;
        margin-bottom: 10px;
    }
    .main_project_info_title > h3 > a{
        font-size: 24px;
    }
    .main_project_parent_org{
        display: flex;
        align-items: center;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .main_project_parent_img{
        width: 25px; height: 25px;
    }
    .main_project_parent_title{
        font-size: 14px;
        padding-left: 5px;
        text-decoration: underline;
    }
    .main_project_owner_location{
        margin-top: 10px !important;
        margin-bottom: 10px !important;
    }
    .seao_icon_location{
        margin-top: 10px !important;
        margin-bottom: 10px !important;
        margin-left: 5px !important;
    }
    i.seao_icon_location::before {
        font-size: 25px !important;
    }
    .main_project_info_icon img {
        width: 120px;
        height: 120px;
        background: none !important;
        padding: 0px !important;
    }
    .main_project_info_icon {
        display: flex !important;
        flex-direction: column !important;
        padding-right: 20px !important;
    }
    .main_project_info_container{
        display:flex !important;
        padding: 10px;
    }
    .main_project_info_options{
        display: flex !important;
        width: 35%;
        padding: 10px;
        flex-direction: column;
    }
    .main_project_info_options_inside{
        display: flex;
        justify-content: center;
        padding: 10px;
    }
    .see_all_backers_btn{
        text-decoration: underline !important;
    }

    .main_project_info_follow_join_count_container{
        display: flex;
        justify-content: space-around;
    }
    .main_project_info_follow_count,.main_project_info_members_count{
        text-align: center;
    }

    .main_project_sub_info{
        display: flex;
        width: 100%;
        border: 1px dashed #d9d8d8;
    }
    .main_project_sub_info_desc{
        padding: 10px;
    }
    .main_project_sub_info_funding{
        padding: 10px;
    }


    .main_project_photo_map_info{
        display: flex;
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        justify-content: space-between;
    }
    .main_project_photo_video_info{
        margin-right: 20px;
    }
    .main_project_map_info{
        margin-left: 20px;
    }
    .main_project_photo_video_info,.main_project_map_info{
        width: 50%;
    }
    .main_project_photo_video_info img{
        /*width: 100% !important;*/
        /*object-fit: contain !important;*/
    }
    #display_photo_custom_id{
        width: 100% !important;
        object-fit: cover !important;
    }
    .has_joined_label> li > h3,
    .has_favourite_label > li > h3{
        color: #44AEC1;
        font-weight: bold;
        margin-top: 10px;
    }

    ul.feed > li .feed_item_body .sitecrowdfunding_review_rich_content .sitecrowdfunding_activity_feed_img .aaf-feed-photo img{
        width: 60% !important;
    }
    .layout_page_sitecrowdfunding_project_view h3{
        font-weight: 500 !important;
        font-size: 18px !important;
    }
    .compose-content{
        min-height: 65px !important;
    }
    .cover_tip_wrap{
        line-height: 26px;
        position: absolute;
        text-align: center;
        top: 49%;
        width: 100%;
    }
    .cover_tip{
        background-color: rgba(0, 0, 0, .4);
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        color: #fff;
        display: inline;
        font-size: 13px;
        font-weight: bold;
        padding: 4px 9px 6px 29px;
    }
    .cropper-container{
        opacity: 0.5;
    }
    #sitecrowdfunding_edit_media_thumb_custom{
        position: relative;
    }
</style>

<script>
    var $j = jQuery.noConflict();
    $j(document).ready(function() {

    })
    window.addEventListener('DOMContentLoaded', function () {
        var image = document.querySelector('#main_photo_custom_id');
        var imagedisplay = document.querySelector('#display_photo_custom_id');
        var button = document.getElementById('set_position_custom')
        var save = document.getElementById('save_position_custom')
        var cancel = document.getElementById('cancel_position_custom')
        var result = document.getElementById('sitecrowdfunding_edit_media_thumb_custom')
        if(image){
            var cropper = new Cropper(image, {
                viewMode: 3,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                modal: false,
                guides: false,
                highlight: false,
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
            });
            $j('#cancel_position_custom').hide();
            $j('#save_position_custom').hide();
            button.onclick = function(){
                $j('#display_photo_custom_id').hide();
                $j('#set_position_custom').hide();
                $j('#save_position_custom').show();
                $j('.cropper-container').show();
                $j('.cover_tip_wrap').show()
                $j('#cancel_position_custom').show();
            }
            cancel.onclick = function() {
                $j('#display_photo_custom_id').show();
                $j('#set_position_custom').show();
                $j('#save_position_custom').hide();
                $j('.cropper-container').hide();
                $j('.cover_tip_wrap').hide()
                $j('#cancel_position_custom').hide();

            }

            save.onclick = function () {
                let canvas = cropper.getCroppedCanvas();
                saveData(cropper.getData().x + ":" + cropper.getData().y + ":" + canvas.width + ":" + canvas.height)
            }
        }

    });

    function saveData(coordinates){
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'sitecrowdfunding/project-create/save-cropped-image',
            method: 'POST',
            data: {
                format: 'json',
                coordinates: coordinates,
                project_id: '<?php echo $this->project->project_id ?>',
                photo_id: '<?php echo $this->project->photo_id; ?>'
            },
            onRequest: function () {
                console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
                console.log('debugging res',responseJSON)
                setTimeout(function() {
                    window.location.reload()
                })
            }
        })
        request.send();
    }
</script>
<style>
    #main_photo_custom_id {
        max-width: 100%;
    }
    .cropper-container{
        display: none;
    }
    .cover_tip_wrap{
        display: none;
    }
    .sitecrowdfunding_project_status{
        box-sizing: border-box;
        color: #fff;
        padding: 3px !important;
        border-radius: 3px;
        text-align: center;
    }
    @media(max-width:767px){
        .generic_layout_container.layout_sitecrowdfunding_project_profile_navigator {
            display: none !important;
        }
        .generic_layout_container.layout_sitecrowdfunding_project_profile_settings {
            display: none !important;
        }
    }
</style>