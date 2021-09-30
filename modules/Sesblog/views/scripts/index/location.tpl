<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: location.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<div class="sesblog_edit_location_popup">
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  $('lat-wrapper').style.display = 'none';
  $('lng-wrapper').style.display = 'none';
  sesJqueryObject('#mapcanvas-label').attr('id','map-canvas-list');
  sesJqueryObject('#map-canvas-list').css('height','200px');
  sesJqueryObject('#ses_location-label').attr('id','ses_location_data_list');
  sesJqueryObject('#ses_location_data_list').html('<?php echo $this->blog->location; ?>');
  sesJqueryObject('#ses_location-wrapper').css('display','none');
  <?php if($this->type == 'blog_location'){ ?>
    sesJqueryObject('#location-wrapper').hide();
    sesJqueryObject('#execute').hide();
    sesJqueryObject('#or_content').hide();
    sesJqueryObject('#location-form').find('div').find('div').find('h3').hide();
    sesJqueryObject('#cancel').replaceWith('<button name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();">'+en4.core.language.translate('Close')+'</button>');
  <?php } ?>
  initializeSesBlogMapList();
  sesJqueryObject( window ).load(function() {
    editSetMarkerOnMapListBlog();
  });
</script>
