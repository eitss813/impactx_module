<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
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

    <div class="uploaded_photo_custom align_center">
        <?php if(!empty($this->photoUrl)):?>
        <h2>
            Uploaded photo
        </h2>
        <img src="../../../../../../../index.php" alt="" id="lassoImg" class="thumb_profile_edit thumb_profile item_photo_sitecrowdfunding_project ">
        <?php endif; ?>
    </div>
    <div class="uploaded_video_custom align_center">
        <?php if(!empty($this->item)):?>
        <h2>
            Uploaded video
        </h2>
        <div class="sitecrowdfunding_edit_media_thumb">
            <?php
                                    if ($this->item->photo_id)
            echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.normal'), array());
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

<script type="text/javascript">
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
    $j(document).ready(function() {
        checkIsProfileCover($j("input[name='profile_cover']:checked").val())
    });
</script>
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
    img.thumb_profile {
        max-width: 400px;
        max-height: 600px;
    }
    .sitecrowdfunding_video_length{
        text-align: center;
    }
    img.thumb_normal {
        max-width: 280px;
        max-height: 320px;
    }
    .upload_photo_button,.upload_video_button{
        /*padding-left: 15px;*/
        margin: 10px;
        font-weight: unset;
    }
    @media(max-width:767px){
        img.thumb_profile {
            max-width: 200px;
            max-height: 400px;
        }
        img.thumb_normal {
            max-width: 140px;
            max-height: 160px;
        }
    }
</style>
