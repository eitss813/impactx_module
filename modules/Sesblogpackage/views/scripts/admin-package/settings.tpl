<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: settings.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>

<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<div class='sesbasic-form sesbasic-categories-form'>
  <div>
    <?php if(count($this->subNavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subNavigation)->render();?>
      </div>
    <?php endif; ?>
    <div class='sesbasic-form-cont'>
      <div class='clear'>
        <div class='settings sesbasic_admin_form'>
          <?php echo $this->form->render($this); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="sesbasic_waiting_msg_box" style="display:none;">
	<div class="sesbasic_waiting_msg_box_cont">
    <?php echo $this->translate("Please wait.. It might take some time to activate plugin."); ?>
    <i></i>
  </div>
</div>
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.pluginactivated',0)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js'); ?>
	<script type="application/javascript">
  	sesJqueryObject('.global_form').submit(function(e){
			sesJqueryObject('.sesbasic_waiting_msg_box').show();
		});
  </script>
<?php } ?>
<script type="application/javascript">
  function enable_package(value){
    if(value == 1){
      document.getElementById('sesblogpackage_package_info-wrapper').style.display = 'block';	
      document.getElementById('sesblogpackage_payment_mod_enable-wrapper').style.display = 'block';	
    }else{
      document.getElementById('sesblogpackage_package_info-wrapper').style.display = 'none';	
      document.getElementById('sesblogpackage_payment_mod_enable-wrapper').style.display = 'none';		
    }
  }
  enable_package(document.getElementById('sesblogpackage_enable_package').value);
</script>
