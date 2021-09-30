<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.css' ?>" rel="stylesheet">
<script src="<?php echo $this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.js' ?>"></script>

<form id="crop_save_form" method="post" action="<?php
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    echo $view->url(array('action' => 'upload-crop-photo','controller'=> 'project-create','project_id'=> $this->project_id ), "sitecrowdfunding_createspecific", true)
?>">
    <div class="container">
        <h2>Uploaded Photo</h2>
        <div>
            <img id="image" src="<?php echo $this->photoUrl; ?>" alt="Picture">
        </div>
        <p style="display: none">Data: <span id="data"></span></p>
        <br>
        <div>
            <button type="button" id="button_crop">Crop Image</button>
        </div>
        <br>
        <div class="cropped_section_custom">
            <h3>Cropped Image</h3>
            <div id="result"></div>
            <button type="submit" >Save</button>
        </div>
        <input type="hidden" name="base64URLInput" id="base64URLInput" />
        <input type="hidden" name="coordinatesInput" id="coordinatesInput" />
        <input type="hidden" name="photo_id" value="<?php echo $this->project->photo_id; ?>" />
        <input type="hidden" name="project_id" id="" value="<?php echo $this->project_id ?>" />
    </div>
</form>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');?>

<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        $j('.cropped_section_custom').hide();
    });
    window.addEventListener('DOMContentLoaded', function () {
        var image = document.querySelector('#image');
        var minAspectRatio = 0.5;
        var maxAspectRatio = 1.5;
        var button = document.getElementById('button_crop');
        var base64URLInput = document.getElementById('base64URLInput');
        var result = document.getElementById('result');
        var cropper = new Cropper(image, {
            ready: function () {
                var cropper = this.cropper;
                var containerData = cropper.getContainerData();
                var cropBoxData = cropper.getCropBoxData();
                var aspectRatio = cropBoxData.width / cropBoxData.height;
                var newCropBoxWidth;

                if (aspectRatio < minAspectRatio || aspectRatio > maxAspectRatio) {
                    newCropBoxWidth = cropBoxData.height * ((minAspectRatio + maxAspectRatio) / 2);

                    cropper.setCropBoxData({
                        left: (containerData.width - newCropBoxWidth) / 2,
                        width: newCropBoxWidth
                    });
                }
            },

            cropmove: function () {
                var cropper = this.cropper;
                var cropBoxData = cropper.getCropBoxData();
                var aspectRatio = cropBoxData.width / cropBoxData.height;

                if (aspectRatio < minAspectRatio) {
                    cropper.setCropBoxData({
                        width: cropBoxData.height * minAspectRatio
                    });
                } else if (aspectRatio > maxAspectRatio) {
                    cropper.setCropBoxData({
                        width: cropBoxData.height * maxAspectRatio
                    });
                }
            },
        });

        button.onclick = function () {
            result.innerHTML = '';
            let canvas = cropper.getCroppedCanvas();
            result.appendChild(canvas);
            document.getElementById('coordinatesInput').value = cropper.getData().x + ":" + cropper.getData().y + ":" + canvas.width + ":" + canvas.height
            base64URLInput.value = cropper.getCroppedCanvas().toDataURL();
            $j('.cropped_section_custom').show();
        };
    });

</script>
<style>
    .container {
        margin: 20px auto;
        max-width: 640px;
    }
    img {
        max-width: 100%;
    }
</style>