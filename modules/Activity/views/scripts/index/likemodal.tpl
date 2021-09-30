
<div>
    <h2 class="likes"> <?php echo count($this->users_liked); ?>  Like(s) </h2>
   <!-- <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"  class="close popup_close fright">  </a> -->
    <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"  class="popup_close fright">  </a>
</div>
<?php foreach($this->users_liked as $val): ?>
  <?php $file = Engine_Api::_()->getItemTable('storage_file')->getFile($val->photo_id, 'thumb.icon'); ?>
 <div class="content">
     <a href="<?php echo $val->getHref(); ?>" target="_blank">   <img src="<?php echo $file->map(); ?>"></a>
     <a href="<?php echo $val->getHref(); ?>" style="margin-left: 6px" target="_blank"> <?php echo $val->displayname;  ?> </a>
 </div>
  <br>
<?php endforeach; ?>
<style>
    .content{
        display: flex;
        margin-left: 13px;
        margin-top: 8px;
    }
    .likes{
       /* color: #44AEC1; */
        font-size: 17px;
        font-weight: 700;
        display: flex;
        justify-content: center;
    }
    .close{
        right: 27px;
        position: absolute;
        font-weight: 900;
        top: 10px;
    }
</style>


