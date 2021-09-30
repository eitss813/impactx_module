<?php
/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2019-2020 SocialEngineSolutions
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id:createsettings.tpl 2019-08-20 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<div class="settings sesbasic_admin_form">
  <?php echo $this->form->render(); ?>
</div>
<script>
scriptJquery(window).load(function() {
  changeenablecategory();
  changeenabledescriptition();
  changeenablephoto();
});

function changeenablephoto() {
  var values = document.querySelector('input[name="sesblog_cre_photo"]:checked').value;
  if (parseInt(values) == 1) {
    scriptJquery("#sesblog_photo_mandatory-wrapper").show();
  } else {
    scriptJquery("#sesblog_photo_mandatory-wrapper").hide();
  }
}

function changeenablecategory() {
  var values = document.querySelector('input[name="sesblogcre_enb_category"]:checked').value;
  if (parseInt(values) == 1) {
    scriptJquery("#sesblogcre_cat_req-wrapper").show();
  } else {
    scriptJquery("#sesblogcre_cat_req-wrapper").hide();
  }
}

function changeenabledescriptition() {
  var values = document.querySelector('input[name="sesblogcre_enb_des"]:checked').value;
  if (parseInt(values) == 1) {
    scriptJquery("#sesblogcre_des_req-wrapper").show();
  } else {
    scriptJquery("#sesblogcre_des_req-wrapper").hide();
  }
}
</script>
