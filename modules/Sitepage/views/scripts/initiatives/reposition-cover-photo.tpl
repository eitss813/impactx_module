<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.js'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.css'); ?>


<div class="global_form_popup">
  <h3 style="text-align: center"><?php echo $this->translate('Reposition Cover Photo'); ?></h3>
  <div style="margin:20px">
    <div class="uploaded_photo_custom align_center" style="height: 500px">
      <div id="sitecrowdfunding_edit_media_thumb_custom" style="height: 470px;max-width: 650px">
        <img id="main_photo_custom_id" src="<?php echo $this->initiative->getLogoUrl(); ?>" alt="" id="lassoImg" class="thumb_profile_edit thumb_profile item_photo_sitecrowdfunding_project ">
     <div class="image_container">
       <img id="display_photo_custom_id" style='height: 300px;width: 200px;object-fit: contain;' src="<?php echo $this->initiative->getLogoUrl('thumb.cover'); ?>" />
     </div>
        <div class="cover_tip_wrap ">
          <div class="cover_tip drag_img_custom">Drag to Reposition Cover Photo</div>
        </div>
      </div>
      <div style="margin-top: 10px;margin-bottom: 10px">
        <button type="button" id="set_position_custom">Reposition</button>
        <button type="button" id="save_position_custom">Save</button>
        <button type="button" id="cancel_position_custom">Cancel</button>
        <button type="button" id="smootbox_cancel_position_custom">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var $j = jQuery.noConflict();

  function loadImageContent(){
    var image = document.querySelector('#main_photo_custom_id');
    var imagedisplay = document.querySelector('#display_photo_custom_id');
    var button = document.getElementById('set_position_custom')
    var save = document.getElementById('save_position_custom')
    var cancel = document.getElementById('cancel_position_custom')
    var smoothBoxCancel = document.getElementById('smootbox_cancel_position_custom');
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
        $j('#smootbox_cancel_position_custom').hide();
        $j('#save_position_custom').show();
        $j('.cropper-container').show();
        $j('.cover_tip_wrap').show();
        $j('#cancel_position_custom').show();
      };
      cancel.onclick = function() {
        $j('#display_photo_custom_id').show();
        $j('#set_position_custom').show();
        $j('#smootbox_cancel_position_custom').show();
        $j('#save_position_custom').hide();
        $j('.cropper-container').hide();
        $j('.cover_tip_wrap').hide();
        $j('#cancel_position_custom').hide();
      };
      smoothBoxCancel.onclick = function() {
        parent.Smoothbox.close();
      };
      save.onclick = function () {
        let canvas = cropper.getCroppedCanvas();
        saveData(cropper.getData().x + ":" + cropper.getData().y + ":" + canvas.width + ":" + canvas.height)
      }
    }
  }

  function saveData(coordinates){
    var request = new Request.JSON({
      url: en4.core.baseUrl + 'sitepage/initiatives/save-cropped-image',
      method: 'POST',
      data: {
        format: 'json',
        coordinates: coordinates,
        initiative_id: '<?php echo $this->initiative_id ?>',
        photo_id: '<?php echo $this->initiative->logo; ?>'
      },
      onRequest: function () {
        console.log('debugging request',)
      },
      onSuccess: function (responseJSON) {
        setTimeout(function() {
          window.location.reload()
        })
      }
    })
    request.send();
  }

  window.addEventListener('DOMContentLoaded', function () {
    // load image content
    loadImageContent();
  });

</script>

<style>
  /* Photo */
  .uploaded_photo_custom{
    padding: 15px;
    text-align: center;
  }
  .align_center{
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
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
  #global_page_sitepage-initiatives-reposition-cover-photo {
    overflow: hidden !important;
  }
  .image_container{
    width: 650px;
    height: 470px;
  }
</style>