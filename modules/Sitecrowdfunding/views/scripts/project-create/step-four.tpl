<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.css' ?>" rel="stylesheet">
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/styles/custom.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.js' ?>"></script>


<div class="sitecrowdfunding_project_new_steps">
    <?php echo $this->settingsForm->render($this); ?>

    <?php
        $photo_btn_label = $this->project->photo_id ? 'Re-Upload Photo' : 'Upload Photo';
    echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'project-create' , 'action'=>'upload-photo', 'project_id' => $this->project_id), $this->translate($photo_btn_label), array('class' => 'upload_photo_button button fright icon smoothbox seaocore_icon_add'))
    ?>
    <br/>
    <?php
        $video_btn_label = $this->project->video_id ? 'Re-Upload Video' : 'Upload Video';
    echo $this->htmlLink(array('module'=>'sitecrowdfunding', 'controller'=> 'project-create' , 'action'=>'upload-video', 'project_id' => $this->project_id), $this->translate($video_btn_label), array('class' => 'upload_video_button button fright icon smoothbox seaocore_icon_add'))
    ?>
    <br/>

    <?php if(!empty($this->photoUrl)):?>
    <div class="uploaded_photo_custom align_center" style="height: 500px">

        <div  id="sitecrowdfunding_edit_media_thumb_custom" style="height: 470px;max-width: 650px">
            <h2>
                Uploaded photo
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
    </div>
    <?php endif; ?>
    <div class="uploaded_video_custom align_center">
        <?php if(!empty($this->item)):?>
        <h2>
            Uploaded video
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


    <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
        <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
        <button name="execute" id="execute"  type="button" onclick="checkNextFun()">Next</button>
    </div>
    <div class="common_star_info"> <span>* </span> Means required information</div>

</div>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<style>
    #display_photo_custom_id{
        /* width: 100% !important; */
        object-fit: cover !important;
    }
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
    /*img.thumb_profile {
        max-width: 400px;
        max-height: 600px;
    }*/
    .sitecrowdfunding_video_length{
        text-align: center;
    }
   /* img.thumb_normal {
        max-width: 280px;
        max-height: 320px;
    }*/
    .upload_photo_button,.upload_video_button{
        /*padding-left: 15px;*/
        margin: 10px;
        font-weight: unset;
    }
    @media(max-width:767px){
       /* img.thumb_profile {
            max-width: 200px;
            max-height: 400px;
        }
        img.thumb_normal {
            max-width: 140px;
            max-height: 160px;
        }*/
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

    .thumb_normal_custom{
        max-width: 650px;
        min-width: 650px;
    }

    @media(max-width:767px){
        .cropper-container{
            width:100%
        }
        #sitecrowdfunding_edit_media_thumb_custom{
            width:100%
        }
        #main_photo_custom_id, #display_photo_custom_id {
            max-width: 100%;
            min-width: 100%;
            width: 100%;
        }
        .thumb_normal_custom{
            min-width: 100%;
            max-width: 100%;
        }
        .cropper-crop-box {
            width: 100% !important;
        }
    }
</style>

<script>
    var $j = jQuery.noConflict();

    function checkNextFun(){
        $j('#sitecrowdfunding_project_new_step_five_custom').submit()
    }

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
            setTimeout(function () {
                checkIsProfileCover($j("input[name='profile_cover']:checked").val())
            }, 500)
            save.onclick = function () {
                let canvas = cropper.getCroppedCanvas();
                saveData(cropper.getData().x + ":" + cropper.getData().y + ":" + canvas.width + ":" + canvas.height)
            }
        }else{
            setTimeout(function () {
                checkIsProfileCover($j("input[name='profile_cover']:checked").val())
            }, 500)
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