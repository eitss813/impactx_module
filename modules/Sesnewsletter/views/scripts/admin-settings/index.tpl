<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
 ?>
<?php include APPLICATION_PATH .  '/application/modules/Sesnewsletter/views/scripts/dismiss_message.tpl';?>

<div class='clear'>
  <div class='settings sesbasic_admin_form'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<div class="sesbasic_waiting_msg_box" style="display:none;">
	<div class="sesbasic_waiting_msg_box_cont">
    <?php echo $this->translate("Please wait.. It might take some time to activate plugin."); ?>
    <i></i>
  </div>
</div>
<?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.pluginactivated',0)){ 
 $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
	<script type="application/javascript">
  	sesJqueryObject('.global_form').submit(function(e){
			sesJqueryObject('.sesbasic_waiting_msg_box').show();
		});
  </script>
<?php } ?>
<?php $enabletestmode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.enabletestmode', 0);?>
<script>
  
  window.addEvent('domready',function() {
    <?php if(@$_POST['sesnewsletter_enabletestmode'] == 1) { ?>
      showtestmde('<?php echo @$_POST['sesnewsletter_enabletestmode'];?>');
    <?php } else { ?>
      showtestmde('<?php echo $enabletestmode;?>');
    <?php } ?>
  });
function showtestmde(value) {
  if(value == 1) {
    if('sesnewsletter_testemail-wrapper')
      $('sesnewsletter_testemail-wrapper').style.display = 'block';
  } else { 
    if('sesnewsletter_testemail-wrapper')
      $('sesnewsletter_testemail-wrapper').style.display = 'none';
  }
}
</script>
