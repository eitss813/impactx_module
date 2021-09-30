<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _DashboardNavigation.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css') ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css') ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.css' ?>" rel="stylesheet">
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/styles/custom.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.js' ?>"></script>
<script>
    var $j = jQuery.noConflict();

    function checkNextFun(){
        $j('#sitecrowdfunding_project_new_step_five_custom').submit()
    }

    function removePhotoProject(url) {
        window.location.href = url;
    }
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
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Manage Photos/Videos', 'sectionDescription'=> '')); ?>
    <div class="layout_middle">
        <div class="sitecrowdfunding_dashboard_content">

            <!-- Settings -->
            <div id="show_tab_content" class="photos_video_container">
                <h3 class="form_title">Main Project Profile Photo or Video</h3>
                <div class="global_form">
                    <div>
                        <?php  echo $this->settingsForm->render($this); ?>
                    </div>
                </div>

                <?php $photo_btn_label = $this->project->photo_id ? 'Change Photo' : 'Upload Photo'; ?>
                <?php $video_btn_label = $this->project->video_id ? 'Change Video' : 'Upload Video'; ?>

                <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_photo_extended", 'controller' => 'photo', 'action' => 'upload-main-photo', 'project_id' => $this->project_id, 'format' => 'smoothbox'), $this->translate($photo_btn_label), array('class' => 'upload_photo_button button fright smoothbox')) ?>
                <?php echo $this->htmlLink(array('route' => "sitecrowdfunding_photo_extended", 'controller' => 'photo', 'action' => 'upload-main-video', 'project_id' => $this->project_id, 'format' => 'smoothbox'), $this->translate($video_btn_label), array('class' => 'upload_video_button button fright smoothbox')) ?>

                <br/><br/>

                <?php if(!empty($this->photoUrl)):?>
                <div class="uploaded_photo_custom align_center" style="height: 500px">

                    <div  id="sitecrowdfunding_edit_media_thumb_custom" style="height: 470px;max-width: 650px">
                        <h2>
                            Uploaded Main Photo
                        </h2>
                        <img id="main_photo_custom_id" src="<?php echo $this->photoUrl; ?>" alt="" id="lassoImg" class="thumb_profile_edit thumb_profile item_photo_sitecrowdfunding_project ">
                        <img id="display_photo_custom_id" style='height:470px' src="<?php echo $this->project->getPhotoUrl('thumb.cover'); ?>" />
                        <div class="cover_tip_wrap ">
                            <div class="cover_tip drag_img_custom">Drag to Reposition Cover Photo</div>
                        </div>
                    </div>
                        <br>
                        <br>
                        <div style="margin-top: 10px;margin-bottom: 10px">
                            <button id="set_position_custom">Reposition</button>
                            <button id="save_position_custom">Save</button>
                            <button id="cancel_position_custom">Cancel</button>
                        </div>
                        <br>
                        <br>
                </div>
                <?php endif; ?>

                <div class="uploaded_video_custom align_center">
                    <?php if(!empty($this->item)):?>
                    <h2>
                        Uploaded Main Video
                    </h2>
                    <div class="sitecrowdfunding_edit_media_thumb">
                        <?php
                                    if ($this->item->photo_id)
                        echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.normal.custom'), array());
                        else
                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
                        ?>
                        <?php if ($this->item->duration): ?>
                        <div class="sitecrowdfunding_video_length">
                            <?php
                                            if ($this->item->duration > 360)
                            $duration = gmdate("H:i:s", $this->item->duration);
                            else
                            $duration = gmdate("i:s", $this->item->duration);
                            if ($duration[0] == '0')
                            $duration = substr($duration, 1);
                            echo $duration;
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <br/><br/>

            <?php /*
            <!-- Changephoto -->
            <div id="show_tab_content">
                <div class="global_form">
                    <div>
                        <?php echo $this->changePhotoForm->render($this); ?>
                    </div>
                </div>
            </div>
            */ ?>

            <!-- Photo -->
            <div id="show_tab_content" class="photos_container">
                <h3 class="form_title">Manage Photos</h3>
                <div class="global_form">
                    <div>
                        <?php include_once APPLICATION_PATH .'/application/modules/Sitecrowdfunding/views/scripts/_projectEditPhotos.tpl' ; ?>
                    </div>
                </div>
            </div>

            <br/><br/>

            <!-- Video -->
            <div id="show_tab_content" class="videos_container">
                <h3 class="form_title">Manage Videos</h3>
                <div class="global_form">
                    <div>
                        <?php include_once APPLICATION_PATH .'/application/modules/Sitecrowdfunding/views/scripts/_projectEditVideos.tpl' ; ?>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<style>
    .uploaded_photo_custom,.uploaded_video_custom{
        padding: 15px;
        text-align: center;
    }
    .align_center{
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }
    /*img.thumb_profile {*/
    /*    max-width: 400px;*/
    /*    max-height: 600px;*/
    /*}*/
    .sitecrowdfunding_video_length{
        text-align: center;
    }
    /*img.thumb_normal {*/
    /*    max-width: 280px;*/
    /*    max-height: 320px;*/
    /*}*/
    .upload_photo_button,.upload_video_button{
        /*padding-left: 15px;*/
        margin: 10px;
        font-weight: unset;
    }
    @media(max-width:767px){
        /*img.thumb_profile {*/
        /*    max-width: 200px;*/
        /*    max-height: 400px;*/
        /*}*/
        /*img.thumb_normal {*/
        /*    max-width: 140px;*/
        /*    max-height: 160px;*/
        /*}*/
    }
    .global_form > div > div{
        padding: 0 !important;
    }

    .bg_item_photo {
        background-size: contain !important;
        background-position: center !important;
    }
    .sitevideo_thumb_viewer{
        width: 283px !important;
        height: 300px !important;
    }

    .form_title{
        border-bottom: 1px solid #f2f0f0;
        letter-spacing: .2px;
        line-height: normal;
        font-size: 15px;
        padding: 15px 0;
    }
    .photos_container, .videos_container , .photos_video_container{
        border: 1px solid #f2f0f0;
        padding: 0 10px;
    }
    #display_photo_custom_id{
        /* width: 100% !important; */
        object-fit: cover !important;
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
        text-align: center;
    }
    #main_photo_custom_id,#display_photo_custom_id {
        max-width: 650px;
        min-width: 650px;
    }
    .cropper-container{
        display: none;
    }
    .cover_tip_wrap{
        display: none;
    }

</style>

<script>
    var $j = jQuery.noConflict();
    function checkIsProfileCover(value){
        if (!value || value == '0') {
            // hide photo
            $j('.uploaded_photo_custom').hide();
            $j('.upload_photo_button').hide();

            // show video
            $j('.uploaded_video_custom').show();
            $j('.upload_video_button').show();
        }else{
            // hide photo
            $j('.uploaded_photo_custom').show();
            $j('.upload_photo_button').show();

            // show video
            $j('.uploaded_video_custom').hide();
            $j('.upload_video_button').hide();

        }
    }
    window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            checkIsProfileCover($j("input[name='profile_cover']:checked").val())
        }, 3500)
    })

</script>