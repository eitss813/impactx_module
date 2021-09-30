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
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">
  var dateAction =function(start_date, end_date){
    if($("tag"))
      $("tag").value='';
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_archive')){
				form=$('filter_form_archive');
			}
   form.elements['start_date'].value  = start_date;
   form.elements['end_date'].value= end_date;
    form.submit();
  }
</script>
<form id='filter_form_archive' class='global_form_box' method='get' action='<?php echo $this->url(array('module' => 'sitepage', 'action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
  <input type="hidden" id="start_date" name="start_date"  value=""/>
  <input type="hidden" id="end_date" name="end_date"  value=""/>
</form>
<?php if (count($this->archive_sitepage)): ?>
  <ul class="sitepage_sidebar_list">
    <?php foreach ($this->archive_sitepage as $archive): ?>
      <li>
        <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start'] ?> , <?php echo $archive['date_end'] ?>);' <?php if ($this->start_date == $archive['date_start'])
      echo " class='bold'"; ?>><?php echo $archive['label'] ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>