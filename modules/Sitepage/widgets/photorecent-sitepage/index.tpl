<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
  <?php
  $k=0;
  ?>
<?php if (empty($this->is_ajax)) : ?>
  <div id='photo_image_recent' class="sitepge_photos_list" <?php if ($this->currenttabid): ?>style="display:none"<?php else: ?>style="display:block"<?php endif; ?>>
<?php endif; ?>	
  <?php foreach ($this->paginator as $photo): ?>
    <div class="thumb_photo"> 
      <?php //if (!$this->showLightBox): ?>
<!--        <a href="javascript:void(0)" onclick='ShowPhotoPage("<?php //echo $photo->getHref() ?>")' title="<?php //echo $photo->title; ?>" style="background-image:url(<?php //echo $photo->getPhotoUrl('thumb.normal'); ?>);" class="thumb_img">			
        </a>-->
      <?php //else: ?>
        <?php echo $this->itemBackgroundPhoto($photo, 'thumb.normal', $photo->getTitle(), array(
                'class' => 'thumb_img',
                'tag' => 'a',
                'onclick' => SEA_SITEPAGEALBUM_LIGHTBOX  ? "openSeaocoreLightBox('<?php echo $photo->getHref() . '/type/strip_creation_date' . '/count/'. $this->row_count. '/offset/' . $k. '/page_id/' . $this->sitepage_subject->page_id; ?>');return false;" : '' ,
             )); ?>
        </a>
      <?php //endif; ?>
      <?php if ($this->can_edit): ?>
        <div id='hide_<?php echo $photo->photo_id; ?>' class="photo_hide">
          <a href="javascript:void(0);" title="<?php echo $this->translate('Hide this photo'); ?>" onclick="hidephoto(<?php echo $photo->photo_id; ?>, <?php echo $this->sitepage_subject->page_id; ?>);" ></a>
        </div>
      <?php endif; ?>
    </div>
    <?php $k++;?>
  <?php endforeach; ?>    
  <?php if ($this->count > 0 && $this->can_edit): ?>
    <?php if (count($this->paginator) < $this->limit): ?>
      <div class="thumb_photo">
        <a href='javascript:void(0);' title="<?php echo $this->translate('Reset Photo Strip'); ?>" onclick='opensmoothbox("<?php echo $this->url(array('action' => 'unhide-photo', 'page_id' => $this->sitepage_subject->page_id), 'sitepage_dashboard', true) ?>");return false;'>
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/photoreset.png' />
        </a>	    	
      </div>
      <?php for ($i=1; $i <$this->limit - count($this->paginator); $i++ ): ?>       
        <div class="thumb_photo"> </div>    
      <?php endfor; ?>      
    <?php endif; ?>
  <?php endif; ?>
<?php if (empty($this->is_ajax)) : ?>
  </div> 
<?php endif; ?>

<script type="text/javascript">
  var submit_topageprofile = true;
  function hidephoto( photo_id, page_id) 
  {	
    submit_topageprofile = false;
   
    en4.core.request.send(new Request.HTML({     
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepage/name/photorecent-sitepage', 
      'data' : {
        format : 'html',
        'subject' : 'sitepage_page_' + page_id,
        isajax : 1,
        hide_photo_id : photo_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('photo_image_recent').innerHTML = responseHTML;
      }
    }));
	
    return false;
  }

  function ShowPhotoPage(pageurl) {
    if (submit_topageprofile) {
      window.location = pageurl;
    }
    else {
      submit_topageprofile = true;
    }
  }

  function opensmoothbox(url) {
    Smoothbox.open(url);
  }

</script>