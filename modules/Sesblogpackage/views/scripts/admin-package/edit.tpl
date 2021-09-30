<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: edit.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/pinboardcomment.js');?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<div class="sesbasic_search_result">
	<?php echo $this->htmlLink($this->url(array('module' => 'sesblogpackage', 'controller' => 'package','action'=>'index')), $this->translate("Back to Manage Package"), array('class' => 'buttonlink sesbasic_icon_back')); ?>
</div><br />
<div class="sesbasic_admin_form settings">
  <?php echo $this->form->render($this) ?>
</div>
<script type="application/javascript">
  function showRenewData(value){
    if(value == 1)
      document.getElementById('renew_link_days-wrapper').style.display = 'block';
    else
      document.getElementById('renew_link_days-wrapper').style.display = 'none';
  }
  function checkOneTime(value){
    if(value == 'forever'){
      document.getElementById('is_renew_link-wrapper').style.display = 'block';
      document.getElementById('renew_link_days-wrapper').style.display = 'block';
    }else{
      document.getElementById('is_renew_link-wrapper').style.display = 'none';
      document.getElementById('renew_link_days-wrapper').style.display = 'none';
    }
  }
  document.getElementById("recurrence-select").onclick = function(e){
    var value = this.value;
    checkOneTime(value);
    var value = document.getElementById('is_renew_link').value;
    showRenewData(value);
  };
  window.addEvent('domready',function(){
    var value = document.getElementById('recurrence-select').value;
    checkOneTime(value);
    var value = document.getElementById('is_renew_link').value;
    showRenewData(value);
  });
</script>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    window.location.href = en4.core.baseUrl + 'admin/sesblog/settings/level/id/' + level_id;
    //alert(level_id);
  }
  sesJqueryObject(document).on('change','input[type=radio][name=can_add_jury]',function(){
    if (this.value == 1) {
      sesJqueryObject('#jury_member_count-wrapper').show();
    }else{
      sesJqueryObject('#jury_member_count-wrapper').hide();
    }
  });
  sesJqueryObject(document).on('change','input[type=radio][name=blog_choose_style]',function(){
    if (this.value == 1) {
      sesJqueryObject('#blog_chooselayout-wrapper').show();
      sesJqueryObject('#blog_style_type-wrapper').hide();
    }else{
      sesJqueryObject('#blog_style_type-wrapper').show();
      sesJqueryObject('#blog_chooselayout-wrapper').hide();
    }
  });
  window.addEvent('domready', function() {
    if(sesJqueryObject('#can_add_jury')) {
      var valueStyle = sesJqueryObject('input[name=can_add_jury]:checked').val();
      if(valueStyle == 1) {
        sesJqueryObject('#jury_member_count-wrapper').show();
      }
      else {
        sesJqueryObject('#jury_member_count-wrapper').hide();
      }
    }
   var valueStyle = sesJqueryObject('input[name=blog_choose_style]:checked').val();
    if(valueStyle == 1) {
      sesJqueryObject('#blog_chooselayout-wrapper').show();
      sesJqueryObject('#blog_style_type-wrapper').hide();
    }
    else {
      sesJqueryObject('#blog_style_type-wrapper').show();
      sesJqueryObject('#blog_chooselayout-wrapper').hide();
   }
  });
</script>
