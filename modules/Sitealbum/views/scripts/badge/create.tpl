<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(empty($this->error)):?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/mooRainbow.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>
<div class='sitealbum_badge_right'>
  <iframe scrolling="no" frameborder="0" id="badge_photo_iframe" src="" style="overflow: auto; width: 300px; height: 800;" allowTransparency="true" >
    <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/loader.gif' /> </center>
  </iframe>
</div>
<div class='sitealbum_badge_left'>
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  window.addEvent('domready', function() {
    <?php if($this->type=='album'): ?>
    showDropDown('album');
     previewBadge(1);
    <?php else:?>
     showDropDown('recent');
    <?php endif; ?>
  });
  function previewBadge(option){
    var lodingImage='<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/loader.gif" /></center>';
    var onePhotoHight=96;
    var onePhotoWidth=116;

    var width=escape($("width").value);
    var no_of_image=escape($("no_of_image").value);
    
    var inOneRow=(width/onePhotoWidth); 
    if(onePhotoWidth>width){
      inOneRow=1;
    }else{
      inOneRow=parseInt(inOneRow);
    }
    width=parseInt(width)+8;
    var no_of_row=Math.ceil((parseInt(no_of_image)+1 )/inOneRow);
    var height=parseInt(onePhotoHight*no_of_row)+28;
    var background_color=escape($('background_color').value);
    var border_color=escape($('border_color').value);
    var text_color=escape($('text_color').value);
    var link_color=escape($('link_color').value);    
    var type=escape($("type").value);
    var owner=escape($("owner").value);
    var id=0;
    if(type=='album')
      id=escape($("album").value);
    
    var url_content ="?type="+type+"&id="+id+"&width="+width+"&height="+height+"&owner="+owner+"&no_of_image="+no_of_image+"&background_color="+background_color+"&border_color="+border_color+"&text_color="+text_color+"&link_color="+link_color;
    if(option==1){
      var  srcUrl="<?php echo $this->url(array('action' => 'index'), 'sitealbum_badge', true) ?>"+url_content;
      $('badge_photo_iframe').style.width=width+"px";
      $('badge_photo_iframe').style.height=height+"px";      
      $('badge_photo_iframe').contentWindow.document.body.innerHTML=lodingImage;

      $('badge_photo_iframe').src=srcUrl;
    }else{
      var  srcUrl="<?php echo $this->url(array('action' => 'get-source'), 'sitealbum_badge', true) ?>"+url_content;
      
      Smoothbox.open(srcUrl);
    }
  }

  function showDropDown(option){
    if(option=='album'){
      $('album-wrapper').style.display='block';
    }else{
      $('album-wrapper').style.display='none';
      previewBadge(1);
    }
  }
</script>
<?php else:?>
<div class="tip">
  <span>
    <?php echo $this->translate("You have not added any photos yet."); ?>
      <?php if ($this->canCreate): ?>
        <?php if(Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()):?>
            <?php echo $this->translate('%1$sAdd photos%2$s now!', '<a class="seao_smoothbox" data-SmoothboxSEAOClass="seao_add_photo_lightbox" href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
        <?php else:?>
            <?php echo $this->translate('%1$sAdd photos%2$s now!', '<a href="' . $this->url(array('action' => 'upload'), 'sitealbum_general', true) . '">', '</a>'); ?>
        <?php endif;?>
      <?php endif; ?>
      
  </span>
</div>
<?php endif;?>
<style type="text/css">
/*Create Photo Badge Page*/
.sitealbum_badge_right{
	float:right;
	width:56%;
	margin-left:10px;
	overflow:auto;
}
.sitealbum_badge_left{
	overflow:hidden;
	width:42%;
}
#badge_create div.form-label{
	width:auto;
	margin-bottom:3px;
}
#badge_create div.form-element{
	clear:both;
	margin-bottom:10px;
}
#badge_create > div > div > h3 + p,
#badge_create div > p{
	max-width:350px;
	margin-bottom:3px;
	margin-top:0px;
}
</style>